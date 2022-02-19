<?php

/** @var array $Params */
/** @var eZTemplate $tpl */

$currentSiteAccess = $Params['siteaccess'];

if ( $currentSiteAccess == '' )
{
    $currentSiteAccess = $GLOBALS['eZCurrentAccess']['name'];
}

$report = new ezIniReport( $currentSiteAccess );
$iniFiles = $report->getSettings();

$tpl->setVariable( 'ini_files', $iniFiles );
$tpl->setVariable( 'current_siteaccess', $currentSiteAccess );
