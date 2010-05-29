<?php
/**
 *
 * @author G. Giunta
 * @version $Id: infoxcache.php 41 2010-05-09 14:26:35Z gg $
 * @copyright (C) G. Giunta 2008-2010
 * @license Licensed under GNU General Public License v2.0. See file license.txt
 */

$module = $Params['Module'];

// rely on system policy instead of creating our own, but allow also PolicyOmitList
$ini = eZINI::instance();
if ( !in_array( 'sysinfo/wincache', $ini->variable( 'RoleSettings', 'PolicyOmitList' ) ) )
{
    $user = eZUser::currentUser();
    $access = $user->hasAccessTo( 'setup', 'system_info' );
    if ( $access['accessWord'] != 'yes' )
    {
        return $module->handleError( eZError::KERNEL_ACCESS_DENIED, 'kernel' );
    }
}

ob_start();
include('extension/ggsysinfo/modules/sysinfo/lib/wincache.php');
$output = ob_get_contents();
ob_end_clean();
$output = preg_replace(array('#^.*<body>#s','#</body>.*$#s'), '', $output);

require_once( "kernel/common/template.php" );
$tpl = templateInit();
$tpl->setVariable( 'title', 'WinCache' );
//$tpl->setVariable( 'css', 'xcache.css' );
$tpl->setVariable( 'info', $output );

$Result = array();
$Result['content'] = $tpl->fetch( "design:sysinfo/info.tpl" );

$Result['left_menu'] = 'design:parts/sysinfo/menu.tpl';
$Result['path'] = array( array( 'url' => false,
                                'text' => 'WinCache' ) );

?>
