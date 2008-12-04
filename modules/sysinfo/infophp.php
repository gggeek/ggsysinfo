<?php
/**
 *
 * @author G. Giunta
 * @version $Id$
 * @copyright (C) G. Giunta 2008
 * @license Licensed under GNU General Public License v2.0. See file license.txt
 */

$module = $Params['Module'];

$user = eZUser::currentUser();
if ( !$user->hasAccessTo( 'setup', 'system_info' ) )
{
    return $module->handleError( eZError::KERNEL_ACCESS_DENIED, 'kernel' );
}

ob_start();
phpinfo();
$output = ob_get_contents();
ob_end_clean();
$output = preg_replace( array( '#^.*<body>#s','#</body>.*$#s' ), '', $output );

require_once( "kernel/common/template.php" );
$tpl = templateInit();
$tpl->setVariable( 'title', 'phpinfo()' );
$tpl->setVariable( 'css', 'php.css' );
$tpl->setVariable( 'info', $output );

$Result = array();
$Result['content'] = $tpl->fetch( "design:sysinfo/info.tpl" );

$Result['left_menu'] = 'design:parts/sysinfo/menu.tpl';
$Result['path'] = array( array( 'url' => false,
                                'text' => 'phpinfo()' ) );

?>
