<?php
/**
 * @author G. Giunta
 * @copyright (C) G. Giunta 2014-2022
 * @license Licensed under GNU General Public License v2.0. See file license.txt
 */

class tplChecker
{

    static $originaltpls = array(); // $i => $filename
    static $extensiontpls = array(); // $i => $filename, includes inactive sa and active exts
    static $initialized = false;
    static $tpl = null;

    static function checkFileContents()
    {
        self::initialize();

        $warnings = array();

        // generic syntax validation
        foreach( self::$originaltpls as $file )
        {
            $ok = self::parseTplFile( $file, $warnings );
        }
        foreach( self::$extensiontpls as $file )
        {
            $ok = self::parseTplFile( $file, $warnings );
        }

        return $warnings;
    }

    protected static function initialize( $force=false )
    {
        if ( self::$initialized && !$force )
        {
           return;
        }

        self::$originaltpls = array();
        $knowndirs = array( 'design' );
        foreach ( $knowndirs as $phpdir )
        {
            self::$originaltpls = array_merge( self::$originaltpls, self::scanDirFortpls( $phpdir, true ) );
        }

        self::$extensiontpls = array();
        $extensionsdir = eZExtension::baseDirectory();
        $ini = eZINI::instance( 'design.ini' );
        /// @todo take this from an ini too, to allow user to add more known php files dirs
        foreach ( $ini->variable( 'ExtensionSettings', 'DesignExtensions' ) as $extdir )
        {
            self::$extensiontpls = array_merge( self::$extensiontpls, self::scanDirFortpls( "$extensionsdir/$extdir/design", true ) );
        }

        self::$tpl = sysInfoTools::eZTemplateFactory();
        self::$initialized = true;
    }

    /// Scan a settings dir for php files
    protected static function scanDirFortpls( $dir, $recursive=true )
    {
        $phpfiles = array();
        foreach( @scandir( $dir ) as $phpfile )
        {
            if ( is_file( "$dir/$phpfile" ) && preg_match( '/\.tpl$/', $phpfile ) )
            {
                $phpfiles[] = "$dir/$phpfile";
            }
            elseif ( $recursive && is_dir( "$dir/$phpfile" ) && $phpfile != '.' && $phpfile != '..' )
            {
                $phpfiles = array_merge( $phpfiles, self::scanDirFortpls( "$dir/$phpfile", true ) );
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
    protected static function parseTplFile( $filename, &$warnings )
    {
        $debug = eZDebug::instance();
        $messagecount = count( $debug->DebugStrings );
        $ok = self::$tpl->validateTemplateFile( $filename );
        if ( !$ok )
        {
            if ( count( $debug->DebugStrings ) > $messagecount )
            {
                foreach ( array_slice( $debug->DebugStrings, $messagecount ) as $msg )
                {
                    $line = null;
                    if ( preg_match( '#@ ' . str_replace( '.', '\.', $filename ) . '\:([0-9]+)#', $msg['String'], $matches ) )
                    {
                        $line = $matches[1];
                    }
                    $warnings[] = array( preg_replace( '#parser error @ ' . str_replace( '.', '\.', $filename ) . '(\\\\|\:[0-9]+)\[[0-9]+\]#', '', $msg['String']), $filename, $line, '' );
                }
            }
            else
            {
                $warnings[] = array( 'Template file invalid', $filename, null, '' );
            }

        }
        return true;
    }

}
