<?php

$currentSiteAccess = $Params['siteaccess'];

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
    if ( $currentSiteAccess != '' && $GLOBALS['eZCurrentAccess']['name'] !== $currentSiteAccess )
    {
        // create a site ini instance using $useLocalOverrides
        if ( $siteIni === null )
        {
            $siteIni = eZSiteAccess::getIni( $currentSiteAccess, 'site.ini' );
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

if ( $currentSiteAccess == '' )
{
    $currentSiteAccess = $GLOBALS['eZCurrentAccess']['name'];
}
$tpl->setVariable( 'ini_files', $iniFiles );
$tpl->setVariable( 'current_siteaccess', $currentSiteAccess );

?>