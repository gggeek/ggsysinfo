<?php

// an improved setup/info module
$Module = array( 'name' => 'System Information',
                 'variable_params' => false );

$ViewList = array();

$ViewList['php'] = array(
    //'functions' => array( 'system_info' ), - we check in the module itself
    "script" => "infophp.php",
    "default_navigation_part" => 'ezsysinfonavigationpart',
    "params" => array( ) );

$ViewList['apc'] = array(
    //'functions' => array( 'system_info' ), - we check in the module itself
    "script" => "infoapc.php",
    "default_navigation_part" => 'ezsysinfonavigationpart',
    "params" => array( ) );

$ViewList['xcache'] = array(
    //'functions' => array( 'system_info' ), - we check in the module itself
    "script" => "infoxcache.php",
    "default_navigation_part" => 'ezsysinfonavigationpart',
    "params" => array( ) );

$ViewList['wincache'] = array(
    //'functions' => array( 'system_info' ), - we check in the module itself
    "script" => "infowincache.php",
    "default_navigation_part" => 'ezsysinfonavigationpart',
    "params" => array( ) );

$ViewList['eaccelerator'] = array(
    //'functions' => array( 'system_info' ), - we check in the module itself
    "script" => "infoeaccelerator.php",
    "default_navigation_part" => 'ezsysinfonavigationpart',
    "params" => array( ) );

$ViewList['cachestats'] = array(
    //'functions' => array( 'system_info' ), - we check in the module itself
    "script" => "cachestats.php",
    "default_navigation_part" => 'ezsysinfonavigationpart',
    "params" => array( ) );

$ViewList['cachesearch'] = array(
    //'functions' => array( 'system_info' ), - we check in the module itself
    "script" => "cachesearch.php",
    "default_navigation_part" => 'ezsysinfonavigationpart',
    "params" => array( ) );

$ViewList['logstats'] = array(
    //'functions' => array( 'system_info' ), - we check in the module itself
    "script" => "logstats.php",
    "default_navigation_part" => 'ezsysinfonavigationpart',
    "params" => array( ) );

$ViewList['logsearch'] = array(
    //'functions' => array( 'system_info' ), - we check in the module itself
    "script" => "logsearch.php",
    "default_navigation_part" => 'ezsysinfonavigationpart',
    "params" => array( ) );

$ViewList['logchurn'] = array(
    //'functions' => array( 'system_info' ), - we check in the module itself
    "script" => "logchurn.php",
    "default_navigation_part" => 'ezsysinfonavigationpart',
    "params" => array( ),
    "unordered_params" => array() );

$ViewList['logview'] = array(
    //'functions' => array( 'system_info' ), - we check in the module itself
    "script" => "logview.php",
    "default_navigation_part" => 'ezsysinfonavigationpart',
    "params" => array( 'logfile' ),
    "unordered_params" => array() );

$ViewList['storagestats'] = array(
    //'functions' => array( 'system_info' ), - we check in the module itself
    "script" => "storagestats.php",
    "default_navigation_part" => 'ezsysinfonavigationpart',
    "params" => array( ) );

$ViewList['storagechurn'] = array(
    //'functions' => array( 'system_info' ), - we check in the module itself
    "script" => "storagechurn.php",
    "default_navigation_part" => 'ezsysinfonavigationpart',
    "params" => array( ),
    "unordered_params" => array() );

$ViewList['contentstats'] = array(
    //'functions' => array( 'system_info' ), - we check in the module itself
    "script" => "contentstats.php",
    "default_navigation_part" => 'ezsysinfonavigationpart',
    "params" => array( ) );

$ViewList['systemstatus'] = array(
    //'functions' => array( 'system_info' ), - we check in the module itself
    "script" => "systemstatus.php",
    "default_navigation_part" => 'ezsysinfonavigationpart',
    "params" => array( 'output_format' ) );

/*$ViewList['extensiondetails'] = array(
    "script" => "extensiondetails.php",
    "default_navigation_part" => 'ezsysinfonavigationpart',
    "params" => array( 'extensionname' ) );*/

$ViewList['modulelist'] = array(
    //'functions' => array( 'system_info' ), - we check in the module itself
    "script" => "modulelist.php",
    "default_navigation_part" => 'ezsysinfonavigationpart',
    "params" => array( 'extensionname' ) );

/*$ViewList['moduledetails'] = array(
    "script" => "moduledetails.php",
    "default_navigation_part" => 'ezsysinfonavigationpart',
    "params" => array( 'modulename' ) );*/

$ViewList['viewlist'] = array(
    //'functions' => array( 'system_info' ), - we check in the module itself
    "script" => "viewlist.php",
    "default_navigation_part" => 'ezsysinfonavigationpart',
    "params" => array( 'modulename' ) );

/*$ViewList['viewdetails'] = array(
    "script" => "viewdetails.php",
    "default_navigation_part" => 'ezsysinfonavigationpart',
    "params" => array( 'modulename', 'viewname' ) );*/

$ViewList['policylist'] = array(
    //'functions' => array( 'system_info' ), - we check in the module itself
    "script" => "policylist.php",
    "default_navigation_part" => 'ezsysinfonavigationpart',
    "params" => array( 'modulename' ) );

$ViewList['fetchlist'] = array(
    //'functions' => array( 'system_info' ), - we check in the module itself
    "script" => "fetchlist.php",
    "default_navigation_part" => 'ezsysinfonavigationpart',
    "params" => array( 'modulename' ) );

/*$ViewList['operatorlist'] = array(
    //'functions' => array( 'system_info' ), - we check in the module itself
    "script" => "operatorlist.php",
    "default_navigation_part" => 'ezsysinfonavigationpart',
    "params" => array( ) );*/

$ViewList['classesreport'] = array(
    //'functions' => array( 'system_info' ), - we check in the module itself
    "script" => "classesreport.php",
    "default_navigation_part" => 'ezsysinfonavigationpart',
    "params" => array( ) );

$FunctionList = array();

?>