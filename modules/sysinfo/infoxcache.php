<?php
/**
 *
 * @author G. Giunta
 * @version $Id$
 * @copyright (C) G. Giunta 2008-2010
 * @license Licensed under GNU General Public License v2.0. See file license.txt
 */

$module = $Params['Module'];

// rely on system policy instead of creating our own, but allow also PolicyOmitList
$ini = eZINI::instance();
if ( !in_array( 'sysinfo/xcache', $ini->variable( 'RoleSettings', 'PolicyOmitList' ) ) )
{
    $user = eZUser::currentUser();
    if ( !$user->hasAccessTo( 'setup', 'system_info' ) )
    {
        return $Module->handleError( eZError::KERNEL_ACCESS_DENIED, 'kernel' );
    }
}

ob_start();
include('extension/ggsysinfo/modules/sysinfo/lib/xcache_admin/xcache.php');
$output = ob_get_contents();
ob_end_clean();
$output = preg_replace(array('#^.*<body>#s','#</body>.*$#s'), '', $output);

require_once( "kernel/common/template.php" );
$tpl = templateInit();
$tpl->setVariable( 'title', 'XCache' );
//$tpl->setVariable( 'css', 'xcache.css' );
$tpl->setVariable( 'info', $output );

$Result = array();
$Result['content'] = $tpl->fetch( "design:sysinfo/info.tpl" );

$Result['left_menu'] = 'design:parts/sysinfo/menu.tpl';
$Result['path'] = array( array( 'url' => false,
                                'text' => 'XCache' ) );

?>
