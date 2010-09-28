<?php
/**
 * @author G. Giunta
 * @version $Id: contentstats.php 2570 2008-11-25 11:35:44Z ezsystems $
 * @copyright (C) G. Giunta 2010
 * @license Licensed under GNU General Public License v2.0. See file license.txt
 */



/**
 * iniChecker
 *
 */
class iniChecker
{

    static $originalinis = array(); // $i => $name
    static $extensioninis = array(); // $i => $filename, includes inactive sa and active exts
    static $siteaccesinis = array(); // $i => $filename, includes inactive sa
    static $overrideinis = array(); // $i => $filename
    static $userinis = array();
    static $initialized = false;

    /// @bug: does not check for extensions activated by sa
    /// @bug: does not work with symlinks for used folders?
    static function checkFileNames()
    {
        self::initialize();

        $ini = eZINI::instance( 'site.ini' );
        $activesiteaccesses = $ini->variable( 'SiteAccessSettings', 'AvailableSiteAccessList' ); // $i => $name

        // checks:
        $warnings = array();

        // look for files named .ini.append, .ini.php
        foreach( self::$userinis as $file )
        {
            if ( preg_match( '/\.ini(\.append|\.php)$/', $file ) )
            {
                $warnings[] = "File $file has deprecated filename extension";
            }
        }

        // look for extension/xxx/siteaccess/yyy/zzz files, with yyy siteaccess not existing
        foreach( self::$userinis as $file )
        {
            if ( preg_match( '#/siteaccess/([^/]+)/.+#', $file, $matches ) )
            {
                if ( $matches[1] != 'setup' && $matches[1] != 'admin' && !in_array( $matches[1], $activesiteaccesses ) )
                {
                    $warnings[] = "File $file is for an inactive siteaccess {$matches[1]}";
                }
            }
        }

        // starting with version 4.4, it is not necessary to have exactly one .ini
        // file (and optionally many .ini.append.php)
        if ( version_compare( '4.4', eZPublishSDK::version() ) == -1 )
        {

            // .ini files in extensions that have same name as std files, no .ini (master) file for new ones
            $newinis = array();
            $changedinis = array();
            foreach( self::$userinis as $file )
            {
                $ini = preg_replace( '/\.append$/', '', preg_replace( '/\.php/', '', basename( $file ) ) );
                if ( in_array( $ini, self::$originalinis ) )
                {
                    $changedinis[$ini][] = $file;
                }
                else
                {
                    $newinis[$ini][] = $file;
                }
            }

            foreach( $changedinis as $ini )
            {
                foreach( $ini as $file )
                {
                    if ( preg_match( '#\.ini$#', $file ) )
                    {
                        $warnings[] = "File $file should be renamed to .ini.append.php";
                    }
                }
            }
            foreach( $newinis as $ininame => $inis )
            {
                $orig = 0;
                foreach( $inis as $file )
                {
                    if ( preg_match( '#\.ini$#', $file ) )
                    {
                        $orig++;
                    }
                }
                if ( $orig != 1 )
                {
                    if ( $orig == 0 )
                    {
                        $warnings[] = "There should be one $ininame file with a .ini extension. Found: " . implode( $inis, ', ' );
                    }
                    else
                    {
                        $warnings[] = "There should be only one $ininame file with a .ini extension. Found: " . implode( $inis, ', ' );
                    }
                }
            }
        }

        return $warnings;
    }

    static function checkFileContents()
    {
        self::initialize();

        $warnings = array();

        // generic syntax validation
        foreach( self::$originalinis as $file )
        {
            $values[$file] = self::parseIniFile( "settings/$file", $warnings );
        }
        foreach( self::$extensioninis as $file )
        {
            $extvalues[$file] = self::parseIniFile( $file, $warnings, true );
        }
        foreach( self::$siteaccesinis as $file )
        {
            $savalues[$file] = self::parseIniFile( $file, $warnings, true );
        }
        foreach( self::$overrideinis as $file )
        {
            $overvalues[$file] = self::parseIniFile( $file, $warnings, true );
        }

        // unexpected params in (changed) std files

        // values present in both sa and override

        // bad content: whitespaces

        // bad content: params in changed files with different Case than std files params

        // values that will be changed with the new (4.4) precedence rules

        return $warnings;
    }

    protected static function initialize( $force=false )
    {
        if ( self::$initialized && !$force )
        {
           return;
        }

        $settingsdir = 'settings';
        $extensionsdir = eZExtension::baseDirectory();
        $extensionsdirs = array(); // $name => $dir
        $ini = eZINI::instance( 'site.ini' );
        $activeextensions = $ini->variable( 'ExtensionSettings', 'ActiveExtensions' ); // $i => $name

        foreach ( scandir( $settingsdir ) as $inifile )
        {
            if ( is_file( "$settingsdir/$inifile" ) && preg_match( '#\.ini$#', $inifile ) )
            {
                self::$originalinis[] = $inifile;
            }
        }
        foreach ( @scandir( "$settingsdir/override" ) as $inifile )
        {
            if ( is_file( "$settingsdir/override/$inifile" ) && preg_match( '/\.ini(\.append)?(\.php)?$/', $inifile ) )
            {
                self::$overrideinis[] = "$settingsdir/override/$inifile";
            }
        }
        $siteaccesinis = self::scanDirForInis( $settingsdir, false, true );
        foreach ( scandir( $extensionsdir ) as $extdir )
        {
            if ( is_dir( "$extensionsdir/$extdir/settings" ) && $extdir != '.' && $extdir != '..' )
            {
                $extensionsdirs[$extdir] = "$extensionsdir/$extdir/settings";
            }
        }
        foreach( $activeextensions as $extname )
        {
            self::$extensioninis = array_merge( self::$extensioninis, self::scanDirForInis( $extensionsdirs[$extname] ) );
        }
        self::$userinis = array_merge( self::$overrideinis, self::$siteaccesinis, self::$extensioninis );
    }

    /// Scan a settings dir for ini files; Works for settings/ as well as for extension/xxx/settings
    protected static function scanDirForInis( $dir, $dofiles=true, $dodirs=true )
    {
        $inifiles = array();
        foreach( @scandir( $dir ) as $extfile )
        {
            /// @todo check ez code for exact list of extensions used
            // .ini, .ini.php, .ini.append, .ini.append.php
            if ( is_file( "$dir/$extfile" ) && $dofiles && preg_match( '/\.ini(\.append)?(\.php)?$/', $extfile ) )
            {
                $inifiles[] = "$dir/$extfile";
            }
            elseif ( $extfile == 'siteaccess' && $dodirs )
            {
                foreach( scandir( "$dir/$extfile" ) as $extdir )
                {
                    if( is_dir( "$dir/$extfile/$extdir" ) && $extdir != '..' && $extdir != '.' )
                    {
                        $inifiles = array_merge( $inifiles, self::scanDirForInis( "$dir/$extfile/$extdir", true, false ) );
                    }
                }
            }
        }
        return $inifiles;
    }

    /**
    * Return an array of blocks, with values inside, and stores the warnings
    * generated while parsing the file.
    * Regular expressions taken from ezgeshi extension
    *
    * @todo be more stringent with php opening/closing tag lines
    * @todo make sure there is a php comment at the start of a php file!
    * @todo allow php-style comments: // and /* * / ?
    */
    protected static function parseIniFile( $filename, &$warnings, $isphp=false )
    {
        $groups = array();
        $currentrgroup = '';
        $isincomments = false;
        foreach ( file( $filename, FILE_IGNORE_NEW_LINES ) as $i => $line )
        {
            $i++;

            // empty line
            if ( trim( $line ) == '' )
            {
                continue;
            }

            // windows CRLF eol marker does not gbet stripped properly all of the time by php!
            $line = preg_replace( '/\r$/', '', $line);

            // comment
            if ( preg_match( '/^#/', $line ) )
            {
                continue;
            }
            // bad ini block: whitespace before it...
            if ( preg_match( '/^[ \t]+\[[^\]]+\][ \t]*/', $line ) )
            {
                $warnings[] = array( "Bad block: whitespace before opening bracket", $filename, $i, $line );
                continue;
            }
            // bad line: space before setting name
            if ( preg_match( '/^[ \t]+[\w_*@-]/', $line ) )
            {
                $warnings[] = array( "Bad parameter: whitespace before parameter name", $filename, $i, $line );
                continue;
            }
            // bad line: space after setting name
            if ( preg_match( '/^[\w_*@-]*[\w_*@-][ \t]+/', $line ) )
            {
                $warnings[] = array( "Bad parameter: whitespace after parameter", $filename, $i, $line );
                continue;
            }
            // bad line: space after array key
            if ( preg_match( '/^[\w_*@-]+\[[^\]]*\][ \t]+/', $line ) )
            {
                $warnings[] = array( "Bad array parameter: whitespace after array key", $filename, $i, $line );
                continue;
            }
            // most likely bad line: space after = sign
            if ( preg_match( '/^[^#=]+=[ \t]+.?/', $line ) )
            {
                $warnings[] = array( "Bad parameter: whitespace after equal sign", $filename, $i, $line );
                continue;
            }
            // most likely bad line: space after setting value
            if ( preg_match( '/^[^#=]+=[^ \t\n]+[ \t]+$/', $line ) )
            {
                $warnings[] = array( "Bad parameter: whitespace after value", $filename, $i, $line );
                continue;
            }
             // most likely bad line: space at beginning or end of array key
            if ( preg_match( '/^[^\[#]\[[ \t]+[^\]]*\]|\[[^\]]*[ \t]+\]/', $line ) )
            {
                $warnings[] = array( "Bad array parameter: whitespace at beginning or end of array key", $filename, $i, $line );
                continue;
            }
            // ini block: no whitespace allowed before it on the line, only whitespace allowed afterwards
            if ( preg_match( '/^\[([^\]]+)\][ \t]*/', $line, $matches ) )
            {
                $currentgroup = $matches[1];
                continue;
            }
            // array reset
            if ( preg_match( '/^([\w_*@-]+)\[\]$/', $line, $matches ) )
            {
                $groups[$currentgroup][$matches[1]] = array();
                continue;
            }
            // array value
            if ( preg_match( '/^([^#\]]+)\[([^ \]]+[^\]]*[^ \]]+|[^ \]]*)\]=(.*)/', $line, $matches ) )
            {
                if ( $matches[2] == '' )
                {
                    $groups[$currentgroup][$matches[1]][] = $matches[3];
                }
                else
                {
                    $groups[$currentgroup][$matches[1]][$matches[2]] = $matches[3];
                }
                continue;
            }
            // config line
            if ( preg_match( '/^([\w_*@-]+(?:\[[^\]]*\])?)=(.*)/', $line, $matches ) )
            {
                $groups[$currentgroup][$matches[1]] = $matches[2];
                continue;
            }
            if ( !$isphp || ( !preg_match( '/^<\?php/', $line ) && !preg_match( '/\?>$/', $line ) && !preg_match( '#^/\*$#', $line ) && !preg_match( '#^\*/$#', $line ) ) )
            {
                $warnings[] = array( "Bad line: neither a value, nor a block or comment", $filename, $i, $line );
            }
        }
        return $groups;
    }

}
?>