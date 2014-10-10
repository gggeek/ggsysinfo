<?php
/**
 * Create a graph of messages-per-minute by analyzing error logs
 *
 * @author G. Giunta
 * @copyright (C) G. Giunta 2008-2014
 * @license Licensed under GNU General Public License v2.0. See file license.txt
 *
 * @todo add support for user-selected start and end date
 * @todo support coarser intervals than 60 secs
 * @todo add support for not showing older (rotated) logs
 * @todo sort logs by criticity
 * @todo use a line graph and coalesce logs together
 */

$errormsg = '';
$cachedir = eZSys::cacheDirectory() . '/sysinfo';
$scale = 60;
$scalenames = array( 60 => 'minute', 60*60 => 'hour', 60*60*24 => 'day' );
$cachefiles = array();

// nb: this dir is calculated the same way as ezlog does
$debug = eZDebug::instance();
$logfiles = $debug->logFiles();
foreach( $logfiles as $level => $file )
{
    $logfile = $file[0] . $file[1];
    $logname = str_replace( '.log', '', $file[1] );
    $cachefiles[$logname] = false;

    if ( file_exists( $logfile ) )
    {
        $cachefile = $cachedir . '/' . $file[1] . '.jpg';

        // *** Check if cached image file exists and is younger than storage log
        $cachefound = false;
        if ( $Params['viewmode'] != 'json' )
        {
            $clusterfile = eZClusterFileHandler::instance( $cachefile );
            if ( $clusterfile->exists() )
            {
                $logdate = filemtime( $logfile );
                $cachedate = $clusterfile->mtime();
                if ( $cachedate >= $logdate )
                {
                    $cachefound = true;
                    $clusterfile->fetch();
                    $cachefiles[$logname] = $cachefile;
                }
            }
        }

        if ( !$cachefound )
        {

            // *** parse rotated log files, if found ***
            $data = array();
            for( $i = eZdebug::maxLogrotateFiles(); $i > 0; $i-- )
            {
                $archivelog = $logfile.".$i";
                if ( file_exists( $archivelog ) )
                {
                    $data = ezLogsGrapher::asum( $data, ezLogsGrapher::parseLog( $archivelog, $scale ) );
                }
            }

            // *** Parse log file ***
            $data = ezLogsGrapher::asum( $data, ezLogsGrapher::parseLog( $logfile, $scale ) );

            if ( $Params['viewmode'] == 'json' )
            {
                $cachefiles[$logname] = $data;
                continue;
            }

            // *** build graph and store it ***
            if ( count( $data ) )
            {
                // work around a bug in ezc with a single col not being drawn
                /*reset( $data );
                $date = key( $data );
                if ( count( $data ) == 1 )
                {
                    $data[$date + $scale] = 0;
                }*/
                /*$last = end( array_keys( $data ) );
                $data[$last + $scale] = 0;*/

                $graphname = sysInfoTools::ezpI18ntr( 'SysInfo', 'messages per '.$scalenames[$scale] );
                $graph = ezLogsGrapher::graph( $data, $graphname, $scale );
                if ( $graph != false )
                {
                    $clusterfile->fileStoreContents( $cachefile, $graph );
                    $cachefiles[$logname] = $cachefile;
                }
                else
                {
                    $errormsg = ezLogsGrapher::lastError();
                }
            }
            else
            {
                eZDebug::writedebug( "No valid date labels in log $logfile", __METHOD__ );
            }
        }
    }
}

if ( $Params['viewmode'] == 'json' )
{
    $response_type = $Params['viewmode'];
    $response_data = $cachefiles;
    return;
}

// *** output ***

$tpl->setVariable( 'graphsources', $cachefiles );
$tpl->setVariable( 'errormsg', $errormsg );

$Result = array();
$Result['content'] = $tpl->fetch( "design:sysinfo/logchurn.tpl" ); //var_dump($cacheFilesList);

$Result['left_menu'] = 'design:parts/sysinfo/menu.tpl';
$Result['path'] = array( array( 'url' => false,
                                'text' => sysInfoTools::ezpI18ntr( 'SysInfo', 'Log churn' ) ) );
