<?php
/**
 * Display a log file
 *
 * @author G. Giunta
 * @copyright (C) G. Giunta 2014-2022
 * @license Licensed under GNU General Public License v2.0. See file license.txt
 *
 * @todo add support for if-modified-since, etag headers
 */

/** @var array $Params */
/** @var eZTemplate $tpl */
/** @var eZINI $ini */

$module = $Params['Module'];

$desiredLog = str_replace( ':', '/', $Params['logfile'] );

$logName = '';
$isDebugLog = false;

$desiredLogPath = dirname( $desiredLog );
if( $desiredLogPath != 'var/log'
    && $desiredLogPath != eZSys::varDirectory() . '/' . $ini->variable( 'FileSettings', 'LogDir' )
    && $desiredLogPath != 'symfony' )
{
    return $module->handleError( eZError::KERNEL_ACCESS_DENIED, 'kernel' );
}

// work around legacy kernel bug with ezplatform 2.5
$siteDir = preg_replace('#/app\.php/?$#', '', eZSys::siteDir());
if ( $desiredLogPath == 'symfony' )
{
    $ezp5LogDir = is_dir( $siteDir . '/../ezpublish/logs' ) ? $siteDir . '/../ezpublish/logs/' : eZSys::siteDir() . '/../var/logs/';
    $logfile = $ezp5LogDir . basename( $desiredLog );
}
else
{
    $logfile = $siteDir . '/' . $desiredLog;
}

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
