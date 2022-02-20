<?php
/**
 * Create a graph of files-per-minute by analyzing storage.log
 *
 * @author G. Giunta
 * @copyright (C) G. Giunta 2008-2022
 * @license Licensed under GNU General Public License v2.0. See file license.txt
 *
 * @todo add support for user-selected start and end date
 * @todo support coarser intervals than 60 secs
 */

/** @var array $Params */
/** @var eZTemplate $tpl */
/** @var eZINI $ini */
/** @var string $hostName */

$errormsg = "";
// nb: this dir is calculated the same way as ezlog does
$logfile = eZSys::varDirectory() . '/' . $ini->variable( 'FileSettings', 'LogDir' ) . '/storage.log';
// but storage log also is in var/log (created I think before siteaccess settings are loaded)
$logfile2 = 'var/log/storage.log';

$ini = eZINI::instance( 'sysinfo.ini' );
$graphRange =  $ini->variable( 'GraphSettings', 'MaxTimespan' );
if ( $graphRange > 0 )
{
    $minDate = time() - $graphRange;
}
else
{
    $minDate = null;
}
/// @todo allow this to be set from ini. Either that, or pick it based on the timespan
$scale = 60;

if ( $Params['viewmode'] == 'json' )
{
    if ( !is_file( $logfile ) && !is_file( $logfile2 ) )
    {
        /// @todo return a 404 error?
    }

    $data = ezLogsGrapher::asum( ezLogsGrapher::parseLog( $logfile, $scale, true, $minDate ), ezLogsGrapher::parseLog( $logfile2, $scale, true, $minDate ) );
    ksort( $data );

    $mtime = @filemtime( $logfile );
    $mtime2 = @filemtime( $logfile2 );
    $mdate = gmdate( 'D, d M Y H:i:s', ( $mtime > $mtime2 ? $mtime : $mtime2 ) ) . ' GMT';

    header( 'Content-Type: application/json' );
    header( "Last-Modified: $mdate" );
    echo json_encode( $data );
    eZExecution::cleanExit();
}

$cachedir = eZSys::cacheDirectory() . '/sysinfo';
// We add the hostname, so that, if there are many eZ hosts, they don't overwrite each other's graphs
$cachefile = $cachedir . '/storagechurn_' . md5( $hostName ). '.jpg';

// *** Check if cached image file exists and is younger than storage log
$cachefound = false;
$clusterfile = eZClusterFileHandler::instance( $cachefile );
if ( $clusterfile->exists() )
{
    $logdate = $logdate2 = 0;
    if ( file_exists( $logfile ) )
    {
        $logdate = filemtime( $logfile );
    }
    if ( file_exists( $logfile2 ) )
    {
        $logdate2 = filemtime( $logfile2 );
    }
    $cachedate = $clusterfile->mtime();
    if ( $cachedate >= $logdate && $cachedate >= $logdate2 )
    {
        $cachefound = true;
        $clusterfile->fetch();
    }
}

if ( !$cachefound )
{
    // *** Parse storage.log files ***
    $data = ezLogsGrapher::asum( ezLogsGrapher::parseLog( $logfile, $scale, true, $minDate ), ezLogsGrapher::parseLog( $logfile2, $scale, true, $minDate ) );

    if ( count( $data ) ) {
        ksort($data);
        $scalenames = array( 60 => 'minute', 60*60 => 'hour', 60*60*24 => 'day' );

        // *** build graph and store it ***
        $graphname = sysInfoTools::ezpI18ntr('SysInfo', 'files per ' . $scalenames[$scale]);
        $graph = ezLogsGrapher::graph($data, $graphname, $scale);
        if ($graph != false) {
            $clusterfile->fileStoreContents($cachefile, $graph);
        } else {
            $errormsg = ezLogsGrapher::lastError();
        }
    } else {
        $errormsg = "No data found" . ( $minDate > 0 ? ' in the specified time range' : '' );
    }
}

// *** output ***

$tpl->setVariable( 'graphsource', $cachefile );
$tpl->setVariable( 'errormsg', $errormsg );
