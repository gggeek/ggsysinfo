<?php

// Copied from settings/view
$rootDir = 'settings';
$iniFiles = eZDir::recursiveFindRelative( $rootDir, '', '.ini' );

// find all .ini files in active extensions
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

foreach( $iniFiles as $key => $ini )
{
    $iniFile = new eZINI( $ini );
    $iniFiles[$ini] = $iniFile->groups();
    unset( $iniFiles[$key] );
}
$tpl->setVariable( 'ini_files', $iniFiles );

?>