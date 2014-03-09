<?php
/**
 *
 * @author G. Giunta
 * @copyright (C) G. Giunta 2010-2014
 * @license Licensed under GNU General Public License v2.0. See file license.txt
 *
 * @todo sort logs by criticity
 */

$errormsg = '';
$cachedir = eZSys::cacheDirectory() . '/sysinfo';
$logFilesList = array();
$extraLogFilesList = array();


// nb: this dir is calculated the same way as ezlog does
$debug = eZDebug::instance();
$logfiles = $debug->logFiles();
foreach( $logfiles as $level => $file )
{
    $logfile = $file[0] . $file[1];
    $logname = str_replace( '.log', '', $file[1] );

    if ( file_exists( $logfile ) )
    {
        $count = 1;
        $size = filesize( $logfile );
        $modified = filemtime( $logfile );

        // *** parse rotated log files, if found ***
        $data = array();
        for( $i = eZdebug::maxLogrotateFiles(); $i > 0; $i-- )
        {
            $archivelog = $logfile.".$i";
            if ( file_exists( $archivelog ) )
            {
                $data = ezLogsGrapher::asum( $data, ezLogsGrapher::parseLog( $archivelog ) );
                $size += filesize( $archivelog );
                $count++;
            }
        }

        $logFilesList[$logname] = array( 'path' => $logfile, 'count' => $count, 'size' => $size, 'modified' => $modified, 'link' => true );
    }
}

foreach( scandir( 'var/log' ) as $log )
{
    $logfile = "var/log/$log";
    if ( is_file( $logfile ) && substr( $log, -4 ) == '.log' && !in_array( $log, array( 'error.log', 'warning.log', 'debug.log', 'notice.log', 'strict.log' ) ) )
    {
        $logFilesList[$log] = array( 'path' => $logfile, 'count' => '[1]', 'size' => filesize( $logfile ), 'modified' => filemtime( $logfile ) );
    }
}
$logdir = eZSys::varDirectory() . '/' . $ini->variable( 'FileSettings', 'LogDir' );
foreach( scandir( $logdir ) as $log )
{
    $logfile = "$logdir/$log";
    if ( is_file( $logfile ) && substr( $log, -4 ) == '.log' )
    {
        $logFilesList[$log] = array( 'path' => $logfile, 'count' => '[1]', 'size' => filesize( $logfile ), 'modified' => filemtime( $logfile ) );
    }
}

if ( $Params['viewmode'] == 'json' )
{
    header( 'Content-Type: application/json' );
    echo json_encode( $logFilesList );
    eZExecution::cleanExit();
}

$tpl = sysInfoTools::eZTemplateFactory();
$tpl->setVariable( 'filelist', $logFilesList );

?>
