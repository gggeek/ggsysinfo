<?php
/**
 * Display a log file
 *
 * @author G. Giunta
 * @copyright (C) G. Giunta 2014
 * @license Licensed under GNU General Public License v2.0. See file license.txt
 *
 * @todo add support for if-modified-since, etag headers
 */

$module = $Params['Module'];

$desiredLog = str_replace( ':', '/', $Params['logfile'] );

$logName = '';
$isDebugLog = false;


$desiredLogPath = dirname( $desiredLog   );
if( $desiredLogPath != 'var/log'  && $desiredLogPath != eZSys::varDirectory() . '/' . $ini->variable( 'FileSettings', 'LogDir' ) )
{
    return $module->handleError( eZError::KERNEL_ACCESS_DENIED, 'kernel' );
}

$logfile = eZSys::siteDir() . '/' . $desiredLog;
if ( !file_exists( $logfile ) )
{
    return $module->handleError( eZError::KERNEL_NOT_FOUND, 'kernel' );
}
else
{
    $mdate = gmdate( 'D, d M Y H:i:s', filemtime( $logfile ) ) . ' GMT';
    header( 'Content-Type: text/plain' );
    header( "Last-Modified: $mdate" );
    readfile( $logfile );
    eZExecution::cleanExit();
}
