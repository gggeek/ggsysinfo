<?php
/**
 * @author G. Giunta
 * @copyright (C) G. Giunta 2010-2014
 * @license Licensed under GNU General Public License v2.0. See file license.txt
 */

/**
 * iniChecker
 *
 */
class phpChecker
{

    static $originalphps = array(); // $i => $filename
    static $extensionphps = array(); // $i => $filename, includes inactive sa and active exts
    static $initialized = false;
    static $php = null;

    static function checkFileContents()
    {
        self::initialize();

        $warnings = array();

        // generic syntax validation
        foreach( self::$originalphps as $file )
        {
            $ok = self::parsePhpFile( $file, $warnings );
        }
        foreach( self::$extensionphps as $file )
        {
            $ok = self::parsePhpFile( $file, $warnings );
        }

        return $warnings;
    }

    protected static function initialize( $force=false )
    {
        if ( self::$initialized && !$force )
        {
           return;
        }

        self::$originalphps = array();
        $knowndirs = array( 'autoload', 'bin', 'cronjobs', 'kernel', 'lib', 'update' );
        foreach ( $knowndirs as $phpdir )
        {
            self::$originalphps = array_merge( self::$originalphps, self::scanDirForphps( $phpdir, true ) );
        }

        self::$extensionphps = array();
        $extensionsdir = eZExtension::baseDirectory();
        $ini = eZINI::instance( 'site.ini' );
        /// @todo take this from an ini too, to allow user to add more known php files dirs
        foreach ( $ini->variable( 'ExtensionSettings', 'ActiveExtensions' ) as $extdir )
        {
            self::$extensionphps = array_merge( self::$extensionphps, self::scanDirForphps( "$extensionsdir/$extdir", true ) );
        }

        $php = 'php';
        exec( $php . ' -v', $output );
        if ( count( $output ) && strpos( $output[0], 'PHP' ) !== false )
        {
            self::$php = $php;
        }
        self::$initialized = true;
    }

    /// Scan a settings dir for php files
    protected static function scanDirForphps( $dir, $recursive=true )
    {
        $phpfiles = array();
        foreach( @scandir( $dir ) as $phpfile )
        {
            if ( is_file( "$dir/$phpfile" ) && preg_match( '/\.php$/', $phpfile ) )
            {
                $phpfiles[] = "$dir/$phpfile";
            }
            elseif ( $recursive && is_dir( "$dir/$phpfile" ) && $phpfile != '.' && $phpfile != '..' && $phpfile != 'settings' )
            {
                $phpfiles = array_merge( $phpfiles, self::scanDirForphps( "$dir/$phpfile", true ) );
            }
        }
        return $phpfiles;
    }

    /**
    * Return ... and stores the warnings generated while parsing the file.
    *
    * @todo parse for validity
    * @todo we are not checking if a php closing tag is followed by whitespace only lines
    */
    protected static function parsePhpFile( $filename, &$warnings, $checksyntax=true )
    {
        $lines = file( $filename, FILE_IGNORE_NEW_LINES );

        // known php files to include html content
        /// @todo take this from an ini, to allow user to add more known files to be skipped
        if ( preg_match( '#^extension/ggsysinfo/modules/sysinfo/lib#' , $filename ) )
        {
            return true;
        }

        $linecount = count( $lines );
        foreach ( $lines as $i => $line )
        {
            $i++;

            // empty line
            if ( trim( $line ) == '' )
            {
                continue;
            }

            // windows CRLF eol marker does not gbet stripped properly all of the time by php!
            $line = preg_replace( '/\r$/', '', $line);

            // check: every php file should start with a long php opening tag
            // NB: we consider files with the BOM errors, too!
            if ( $i == 1 && !preg_match( '/^<\?/', $line ) && !preg_match( '%^#!/usr/bin/env php%', $line ) )
            {
                $warnings[] = array( "Spurious content: it should start with a php opening tag", $filename, $i, $line );
            }
            else if ( $i == 1 && preg_match( '/^<\?/', $line ) && !preg_match( '/<\?php/', $line ) )
            {
                $warnings[] = array( "Bad php opening tag: it should be the long version", $filename, $i, $line );
            }
            /* // check: every php file should end with a php closing tag
            if ( $i == $linecount && !preg_match( '/\?>$/', $line ) )
            {
                $warnings[] = array( "Spurious content: it should end with a php closing tag", $filename, $i, $line );
            }*/
        }

        if ( $checksyntax && self::$php )
        {
            exec( escapeshellcmd( self::$php ) . " -l " . escapeshellarg( $filename ), $output );
            $output = implode( "\n", $output );
            if ( strpos( $output, 'No syntax errors detected' ) !== 0 )
            {
                $warnings[] = array( "Syntax error: $output", $filename, null, null );
            }
        }

        return true; /// @todo
    }

}
?>