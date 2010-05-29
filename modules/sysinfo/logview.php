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

$module = $Params['Module'];

// rely on system policy instead of creating our own, but allow also PolicyOmitList
$ini = eZINI::instance();
if ( !in_array( 'sysinfo/logview', $ini->variable( 'RoleSettings', 'PolicyOmitList' ) ) )
{
    $user = eZUser::currentUser();
    $access = $user->hasAccessTo( 'setup', 'system_info' );
    if ( $access['accessWord'] != 'yes' )
    {
        return $module->handleError( eZError::KERNEL_ACCESS_DENIED, 'kernel' );
    }
}

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

require_once( "kernel/common/template.php" );
$tpl = templateInit();
$tpl->setVariable( 'title', $Params['logfile'] ); // washed in tpl for safety
$tpl->setVariable( 'log', $data );
$tpl->setVariable( 'errormsg', $errormsg );

$Result = array();
$Result['content'] = $tpl->fetch( "design:sysinfo/logview.tpl" ); //var_dump($cacheFilesList);

$Result['left_menu'] = 'design:parts/sysinfo/menu.tpl';
$Result['path'] = array( array( 'url' => false,
                                'text' => ezi18n( 'SysInfo', 'Log view' ) ),
                         array( 'url' => false,
                                'text' => $logname ) );
?>