<?php
/**
* Tests status of eZ Publish install in mlore detail than ezinfo/isalive
* For every test, 1 = OK, 0 = KO and X = NA. ? = test not yet implemented
* NB: some tests are enabled/disabled depending upon config in sysinfo.ini
*
 * @version $Id$
 * @copyright (C) G. Giunta 2008
 * @license Licensed under GNU General Public License v2.0. See file license.txt
*/

$module = $Params['Module'];
$http = eZHTTPTool::instance();

$user = eZUser::currentUser();
if ( !$user->hasAccessTo( 'setup', 'system_info' ) )
{
    return $Module->handleError( eZError::KERNEL_ACCESS_DENIED, 'kernel' );
}

$testslist = sysInfoTools::runtests();

include_once( 'kernel/common/template.php' );
$tpl = templateInit();
$tpl->setVariable( 'title', 'System status' );
$tpl->setVariable( 'testslist', $testslist );

$Result = array();
$Result['content'] = $tpl->fetch( 'design:sysinfo/systemstatus.tpl' );

$Result['left_menu'] = 'design:parts/sysinfo/menu.tpl';
$Result['path'] = array( array( 'url' => false,
                                'text' => ezi18n( 'SysInfo', 'System status' ) ) );

?>