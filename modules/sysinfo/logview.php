<?php
/**
 * Display a list of messages by parsing an ezdebug log file, including its rotated versions.
 * Now with support for "raw" version as well
 *
 * @author G. Giunta
 * @copyright (C) G. Giunta 2010-2020
 * @license Licensed under GNU General Public License v2.0. See file license.txt
 *
 * @todo add support for user-selected start and end date (offset/limit?)
 * @todo add support for not showing older (rotated) logs
 */

$errormsg = 'File not found';
$data = array();
$logname = '';

// nb: this dir is calculated the same way as ezlog does
$debug = eZDebug::instance();
$logfiles = $debug->logFiles();
foreach( $logfiles as $level => $file )
{
    if ( $file[1] == $Params['logfile'] . '.log' )
    {

        $logfile = $file[0] . $file[1];
        $logname = $Params['logfile'];

        if ( file_exists( $logfile ) )
        {
            $errormsg = '';

            /// @todo add support for if-modified-since, etag headers
            if ( $Params['viewmode'] == 'raw' )
            {
                $mdate = gmdate( 'D, d M Y H:i:s', filemtime( $logfile ) ) . ' GMT';
                header( 'Content-Type: text/plain' );
                header( "Last-Modified: $mdate" );

                /// @todo this can be a DOS. Do not attempt it if filesize is too big for the browser too handle, or use 'tail -1000'...

                for( $i = eZdebug::maxLogrotateFiles(); $i > 0; $i-- )
                {
                    $archivelog = $logfile.".$i";
                    if ( file_exists( $archivelog ) )
                    {
                        readfile( $archivelog );
                    }
                }

                readfile( $logfile );
                $mdate = gmdate( 'D, d M Y H:i:s', filemtime( $logfile ) ) . ' GMT';

                eZExecution::cleanExit();
            }

            // *** parse rotated log files, if found ***
            for( $i = eZdebug::maxLogrotateFiles(); $i > 0; $i-- )
            {
                $archivelog = $logfile.".$i";
                if ( file_exists( $archivelog ) )
                {
                    $data = array_merge( $data, ezLogsGrapher::splitLog( $archivelog ) );
                    //var_dump( $archivelog );
                }
            }

            // *** Parse log file ***
            $data = array_reverse( array_merge( $data, ezLogsGrapher::splitLog( $logfile ) ) );
            $mdate = gmdate( 'D, d M Y H:i:s', filemtime( $logfile ) ) . ' GMT';
            header( "Last-Modified: $mdate" );
        }
        break;
    }
}

if ( $Params['viewmode'] == 'raw' )
{
    // if we're here it's because desired file was not found
    // @todo return a 404 error?
    //       It can be either a valid filename but no log yet, or bad filename...
}

// *** output ***

$tpl->setVariable( 'log', $data );
$tpl->setVariable( 'logfile', $Params['logfile'] );
$tpl->setVariable( 'errormsg', $errormsg );
$tpl->setVariable( 'title', ezSysinfoModule::viewTitle( 'logview' ) . ': ' . $Params['logfile'] ); // washed in tpl for safety

$extra_path = $logname;
