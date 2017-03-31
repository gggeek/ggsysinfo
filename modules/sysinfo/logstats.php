<?php
/**
 *
 * @author G. Giunta
 * @copyright (C) G. Giunta 2010-2017
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
$logFiles = $debug->logFiles();
foreach( $logFiles as $level => $file )
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

        $logFilesList[$logname] = array( 'path' => $logfile, 'count' => $count, 'size' => $size,
            'modified' => $modified, 'link' => 'sysinfo/logview/' . $logname );
    }
}

foreach( scandir( 'var/log' ) as $log )
{
    $logfile = "var/log/$log";
    if ( is_file( $logfile ) && substr( $log, -4 ) == '.log' && !in_array( $log, array( 'error.log', 'warning.log', 'debug.log', 'notice.log', 'strict.log' ) ) )
    {
        $logFilesList[$log] = array( 'path' => $logfile, 'count' => '[1]', 'size' => filesize( $logfile ),
            'modified' => filemtime( $logfile ), 'link' => 'sysinfo/customlogview/' . str_replace( array( '/', '\\' ), ':', $logfile ) );
    }
}
$logDir = eZSys::varDirectory() . '/' . $ini->variable( 'FileSettings', 'LogDir' );
foreach( scandir( $logDir ) as $log )
{
    $logfile = "$logDir/$log";
    if ( is_file( $logfile ) && substr( $log, -4 ) == '.log' )
    {
        $logFilesList[$log] = array( 'path' => $logfile, 'count' => '[1]', 'size' => filesize( $logfile ),
            'modified' => filemtime( $logfile ), 'link' => 'sysinfo/customlogview/' . str_replace( array( '/', '\\' ), ':', $logfile ) );
    }
}

// q: are we 100% sure that the eZ5 logs are always at that location?
if ( class_exists( 'Symfony\Component\HttpKernel\Kernel' ) && is_dir( $ezp5CacheDir = eZSys::siteDir() . '/../ezpublish/cache' ) )
{
    $logDir = eZSys::siteDir() . '/../ezpublish/logs';
    foreach( scandir( $logDir ) as $log )
    {
        $logfile = "$logDir/$log";
        if ( is_file( $logfile ) && substr( $log, -4 ) == '.log' )
        {
            $logFilesList[$log] = array( 'path' => "Symfony/$log", 'count' => '[1]', 'size' => filesize( $logfile ),
                'modified' => filemtime( $logfile ), 'link' => 'sysinfo/customlogview/' . 'symfony:'. $log );
        }
    }
}

// windows friendly
foreach( $logFilesList as &$desc )
{
    $desc['path'] = str_replace( '\\', '/', $desc['path'] );
}

if ( $Params['viewmode'] == 'json' )
{
    $response_type = $Params['viewmode'];
    $response_data = $logFilesList;
    return;
}

$tpl->setVariable( 'filelist', $logFilesList );
