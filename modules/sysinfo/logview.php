<?php
/**
 * Dsiplay a table of messages by parsing a log file
 *
 * @author G. Giunta
 * @version $Id: storagechurn.php 43 2010-05-09 23:13:22Z gg $
 * @copyright (C) G. Giunta 2010
 * @license Licensed under GNU General Public License v2.0. See file license.txt
 *
 * @todo add support for user-selected start and end date (offset/limit?)
 * @todo add support for not showing older (rotated) logs
 */

$errormsg = 'File not found';
$data = array();
$cachedir = eZSys::cacheDirectory() . '/sysinfo';
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

            // *** parse rotated log files, if found ***
            for( $i = eZdebug::maxLogrotateFiles(); $i > 0; $i-- )
            {
                $archivelog = $logfile.".$i";
                if ( file_exists( $archivelog ) )
                {
                    $data = array_merge( $data, ezLogsGrapher::splitLog( $archivelog ) );
                }
            }

            // *** Parse log file ***
            $data = array_reverse( array_merge( $data, ezLogsGrapher::splitLog( $logfile ) ) );

            //var_dump( $data );
        }
        break;
    }
}

// *** output ***

$tpl->setVariable( 'log', $data );
$tpl->setVariable( 'errormsg', $errormsg );
$tpl->setVariable( 'title', sysinfoModule::viewTitle( 'logview' ) . ': ' . $Params['logfile'] ); // washed in tpl for safety

$extra_path = $logname;

?>