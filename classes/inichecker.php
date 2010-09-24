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

    /// Scan a settings dir for ini files; Works for settings/ as well as for extension/xxx/settings
    static function scanDirForInis( $dir, $dofiles=true, $dodirs=true )
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

    // return an array of blocks, with values inside
    static function parseIniFile( $filename )
    {
        foreach ( file( $filename ) as $line )
        {
            /// @todo...
        }
    }

    /// @bug: does not check for extensions activated by sa
    /// @bug: does not work with symlinks for used folders?
    static function runChecks()
    {
        $ini = eZINI::instance( 'site.ini' );

        $settingsdir = 'settings';
        $extensionsdir = eZExtension::baseDirectory();
        $extensionsdirs = array(); // $name => $dir
        $activeextensions = $ini->variable( 'ExtensionSettings', 'ActiveExtensions' ); // $i => $name
        $activesiteaccesses = $ini->variable( 'SiteAccessSettings', 'AvailableSiteAccessList' ); // $i => $name
        $originalinis = array(); // $i => $name
        $extensioninis = array(); // $i => $filename, includes inactive sa and active exts
        $siteaccesinis = array(); // $i => $filename, includes inactive sa
        $overrideinis = array(); // $i => $filename

        foreach ( scandir( $settingsdir ) as $inifile )
        {
            if ( is_file( "$settingsdir/$inifile" ) && preg_match( '#\.ini$#', $inifile ) )
            {
                $originalinis[] = $inifile;
            }
        }
        foreach ( @scandir( "$settingsdir/override" ) as $inifile )
        {
            if ( is_file( "$settingsdir/override/$inifile" ) && preg_match( '/\.ini(\.append)?(\.php)?$/', $inifile ) )
            {
                $overrideinis[] = "$settingsdir/override/$inifile";
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
            $extensioninis = array_merge( $extensioninis, self::scanDirForInis( $extensionsdirs[$extname] ) );
        }
        $userinis = array_merge( $overrideinis, $siteaccesinis, $extensioninis );

//var_export( $originalinis );
//var_export( $extensioninis );
//var_export( $siteaccesinis );
//var_export( $overrideinis );

        // checks:
        $warnings = array();

        // look for files named .ini.append, .ini.php
        foreach( $userinis as $file )
        {
            if ( preg_match( '/\.ini(\.append|\.php)$/', $file ) )
            {
                $warnings[] = "File $file has deprecated filename extension";
            }
        }

        // look for extension/xxx/siteaccess/yyy/zzz files, with yyy siteaccess not existing
        foreach( $userinis as $file )
        {
            if ( preg_match( '#/siteaccess/([^/]+)/.+#', $file, $matches ) )
            {
                if ( $matches[1] != 'setup' && $matches[1] != 'admin' && !in_array( $matches[1], $activesiteaccesses ) )
                {
                    $warnings[] = "File $file is for an inactive siteaccess {$matches[1]}";
                }
            }
        }

        // .ini files in extensions that have same name as std files, no .ini (master) file for new ones
        $newinis = array();
        $changedinis = array();
        foreach( $userinis as $file )
        {
            $ini = preg_replace( '/\.append$/', '', preg_replace( '/\.php/', '', basename( $file ) ) );
            if ( in_array( $ini, $originalinis ) )
            {
                $changedinis[$ini][] = $file;
            }
            else
            {
                $newinis[$ini][] = $file;
            }
        }
//var_export( $changedinis );
//var_export( $newinis );
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

        // unexpected params in (changed) std files

        // values present in both sa and override

        // bad content: whitespaces

        // bad content: params in changed files with different Case than std files params

        // values that will be changed with the new (4.4) precedence rules

        return $warnings;
    }

}
?>