<?php
/**
 *
 * @author G. Giunta
 * @version $Id$
 * @copyright (C) G. Giunta 2010
 * @license Licensed under GNU General Public License v2.0. See file license.txt
 *
 * @todo sort logs by criticity
 */

$module = $Params['Module'];

// rely on system policy instead of creating our own, but allow also PolicyOmitList
$ini = eZINI::instance();
if ( !in_array( 'sysinfo/logstats', $ini->variable( 'RoleSettings', 'PolicyOmitList' ) ) )
{
    $user = eZUser::currentUser();
    $access = $user->hasAccessTo( 'setup', 'system_info' );
    if ( $access['accessWord'] != 'yes' )
    {
        return $module->handleError( eZError::KERNEL_ACCESS_DENIED, 'kernel' );
    }
}

$errormsg = '';
$cachedir = eZSys::cacheDirectory() . '/sysinfo';
$logFilesList = array();

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

        $logFilesList[$logname] = array( 'path' => $logfile, 'count' => $count, 'size' => $size, 'modified' => $modified );
    }
}

require_once( "kernel/common/template.php" );
$tpl = templateInit();
$tpl->setVariable( 'filelist', $logFilesList );
$tpl->setVariable( 'title', 'Log Stats' );

$Result = array();
$Result['content'] = $tpl->fetch( "design:sysinfo/logstats.tpl" ); //var_dump($cacheFilesList);

$Result['left_menu'] = 'design:parts/sysinfo/menu.tpl';
$Result['path'] = array( array( 'url' => false,
                                'text' => ezi18n( 'SysInfo', 'Log stats' ) ) );

?>
