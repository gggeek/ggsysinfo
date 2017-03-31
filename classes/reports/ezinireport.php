<?php
/**
 * @author G. Giunta
 * @copyright (C) G. Giunta 2014-2017
 * @license Licensed under GNU General Public License v2.0. See file license.txt
 */

class ezIniReport implements ezSysinfoReport
{

    protected $currentSiteAccess;

    /**
     * @param string $currentSiteAccess when null is passed, current siteaccess is used
     */
    public function __construct( $currentSiteAccess='' )
    {
        if ( $currentSiteAccess == '' )
        {
            $currentSiteAccess = $GLOBALS['eZCurrentAccess']['name'];
        }
        $this->currentSiteAccess = $currentSiteAccess;
    }

    public function getReport()
    {
        $report = array();
        foreach( $this->getSettings() as $fileName => $blocks )
        {
            $report[] = $fileName;
            foreach( $blocks as $blockName => $settings )
            {
                $report[] = array( $fileName, $blockName );
                foreach( $settings as $name => $value )
                {
                    if ( is_array( $value ) )
                    {
                        foreach( $value as $key => $val )
                        {
                            $report[] = array( $fileName, $blockName, $name, $key, $val );
                        }
                    }
                    else
                    {
                        $report[] = array( $fileName, $blockName, $name, $value );
                    }
                }
            }
        }
        return $report;
    }

    public function getDescription()
    {
        return array(
            'tag' => 'inisettings',
            'title' => 'Ini Settings Report',
            'executingString' => 'Gathering settings...',
            'format' => 'byline'
        );
    }

    public function getSettings()
    {
        // Copied from settings/view

        $rootDir = 'settings';
        $iniFiles = eZDir::recursiveFindRelative( $rootDir, '', '.ini' );

        // find all .ini files in active extensions
        // Note: is this the same algorithm used by ezini? mmm...
        foreach ( eZINI::globalOverrideDirs() as $iniDataSet )
        {
            $iniPath = $iniDataSet[1] ? $iniDataSet[0] : 'settings/' . $iniDataSet[0];
            $iniFiles = array_merge( $iniFiles, eZDir::recursiveFindRelative( $iniPath, '', '.ini' ) );
            $iniFiles = array_merge( $iniFiles, eZDir::recursiveFindRelative( $iniPath, '', '.ini.append.php' ) );
        }

        // extract all .ini files without path
        $iniFiles = preg_replace('%.*/%', '', $iniFiles );
        // remove *.ini[.append.php] from file name
        $iniFiles = preg_replace('%\.ini.*%', '.ini', $iniFiles );
        $iniFiles = array_unique( $iniFiles );
        sort( $iniFiles );

        $siteIni = null;
        foreach( $iniFiles as $key => $ini )
        {
            if ( $this->currentSiteAccess != '' && $GLOBALS['eZCurrentAccess']['name'] !== $this->currentSiteAccess )
            {
                // create a site ini instance using $useLocalOverrides
                if ( $siteIni === null )
                {
                    $siteIni = eZSiteAccess::getIni( $this->currentSiteAccess, 'site.ini' );
                }

                // load settings file with $useLocalOverrides = true
                $iniFile = new eZINI(
                /*$fileName =*/ $ini,
                    /*$rootDir =*/ 'settings',
                    /*$useTextCodec =*/ null,
                    /*$useCache =*/ false,
                    /*$useLocalOverrides =*/ true,
                    /*$directAccess =*/ false,
                    /*$addArrayDefinition =*/ false,
                    /*$load =*/ false );
                $iniFile->setOverrideDirs( $siteIni->overrideDirs( false ) );
                $iniFile->load();
            }
            else
            {
                $iniFile = new eZINI( $ini );
            }
            $iniFiles[$ini] = $iniFile->groups();
            unset( $iniFiles[$key] );
        }

        return $iniFiles;
    }
}