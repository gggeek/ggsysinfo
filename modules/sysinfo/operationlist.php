<?php
/**
 * List all existing operations (optionally, in a given module)
 * @author G. Giunta
 * @version $Id: cachestats.php 18 2010-04-17 14:29:21Z gg $
 * @copyright (C) G. Giunta 2010
 * @license Licensed under GNU General Public License v2.0. See file license.txt
 *
 */

$module = $Params['Module'];

// rely on system policy instead of creating our own, but allow also PolicyOmitList
$ini = eZINI::instance();
if ( !in_array( 'sysinfo/operationlist', $ini->variable( 'RoleSettings', 'PolicyOmitList' ) ) )
{
    $user = eZUser::currentUser();
    $access = $user->hasAccessTo( 'setup', 'system_info' );
    if ( $access['accessWord'] != 'yes' )
    {
        return $module->handleError( eZError::KERNEL_ACCESS_DENIED, 'kernel' );
    }
}

// generic info for all views: module name, extension name, ...
$operationList = array();
$modules = eZModuleLister::getModuleList();
if ( $Params['modulename'] != '' && !array_key_exists( $Params['modulename'], $modules) )
{
    /// @todo
}
else
{

    foreach( $modules as $modulename => $path )
    {
        if ( $Params['modulename'] == '' || $Params['modulename'] == $modulename )
        {
            $module = eZModule::exists( $modulename );
            if ( $module instanceof eZModule )
            {
                $moduleOperationInfo = new eZModuleOperationInfo( $modulename );
                /// @todo prevent warning to be generated here
                $moduleOperationInfo->loadDefinition();
                if ( $moduleOperationInfo->isValid() )
                {
                    $extension = '';
                    if ( preg_match( '#extension/([^/]+)/modules/#', $path, $matches ) )
                    {
                        $extension = $matches[1];
                    }
                    foreach( $moduleOperationInfo->OperationList as $op )
                    {

                        $operationList[$op['name'] . '_' . $modulename ] = $op;
                        $operationList[$op['name'] . '_' . $modulename ]['module'] = $modulename;
                        $operationList[$op['name'] . '_' . $modulename ]['extension'] = $extension;
                    }
                }

/*                foreach( $module->attribute( 'views' ) as $viewname => $view )
                {
                    // merge empty array to facilitate life of templates
                    $view = array_merge( array( 'params' => array(), 'functions' => array(), 'unordered_params' => array(), 'single_post_actions' => array(), 'post_actions' => array(), 'post_action_parameters' => array() ), $view );
                    $operationList[$viewname . '_' . $modulename] = $view;
                    $operationList[$viewname . '_' . $modulename]['name'] = $viewname;
                    $operationList[$viewname . '_' . $modulename]['module'] = $modulename;
                    $operationList[$viewname . '_' . $modulename]['extension'] = $extension;
                    // merge all post parameters stuff
                    $post_params = array_merge( $view['post_actions'], array_keys( $view['single_post_actions'] ) );
                    foreach( $view['post_action_parameters'] as $key => $params )
                    {
                        $post_params = array_merge( $post_params, array_keys( $params ) );
                    }
                    sort( $post_params );
                    $operationList[$viewname . '_' . $modulename]['post_params'] = array_unique( $post_params );
                }*/
            }
        }
    }
    ksort( $operationList );
}

$title = 'List of available operations';
if ( $Params['modulename'] != '' )
{
    $title .= ' in module "' . $Params['modulename'] . '"';
}

$ezgeshi_available = false;
if ( in_array( 'ezsh', eZExtension::activeExtensions() ) )
{
    $info = eZExtension::extensionInfo( 'ezsh' );
    $ezgeshi_available = ( version_compare( $info['Version'], '1.3' ) >= 0 );
}
require_once( "kernel/common/template.php" );
$tpl = templateInit();
$tpl->setVariable( 'title', $title );
$tpl->setVariable( 'operationlist', $operationList );
$tpl->setVariable( 'sdkversion', eZPublishSDK::version() );
$tpl->setVariable( 'ezgeshi_available', $ezgeshi_available );

$Result = array();
$Result['content'] = $tpl->fetch( "design:sysinfo/operationlist.tpl" ); //var_dump($cacheFilesList);

$Result['left_menu'] = 'design:parts/sysinfo/menu.tpl';
$Result['path'] = array( array( 'url' => false,
                                'text' => ezi18n( 'SysInfo', 'Operations' ) ) );
if ( $Params['modulename'] != '' )
{
    $Result['path'][0]['url'] = '/sysinfo/operationlist';
    $Result['path'][] = array( 'url' => false,
                               'text' => $Params['modulename'] );
}

$Result['groups'] = sysinfoModule::$view_groups;
?>
