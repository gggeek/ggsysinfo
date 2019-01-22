<?php
/**
 * A script that gathers the common parts of all views of the sysinfo module.
 * It supports "cluster-slave-node" mode, where page navigation elements are not displayed
 *
 * @author G. Giunta
 * @copyright (C) G. Giunta 2010-2019
 * @license Licensed under GNU General Public License v2.0. See file license.txt
 *
 * @todo use a 3-level path, with the name of the group as 2nd element ?
 */

/// @var eZModule $module
$module = $Params['Module'];
$view = $module->currentView();

// rely on system policy instead of creating our own, but allow also PolicyOmitList for single views
// (useful f.e. for system status checks from tools which can not authenticate because they are too simple).
// Also allow the clusterhelper view to do its auth and then run this view.
$ini = eZINI::instance();
if ( !in_array( "sysinfo/$view", $ini->variable( 'RoleSettings', 'PolicyOmitList' ) ) && ezSysinfoClusterManager::getAuthStatus() !== true )
{
    $user = eZUser::currentUser();
    $access = $user->hasAccessTo( 'setup', 'system_info' );
    if ( $access['accessWord'] != 'yes' )
    {
        return $module->handleError( eZError::KERNEL_ACCESS_DENIED, 'kernel' );
    }
}

$isClusterSlaveRequest = ezSysinfoClusterManager::isClusterSlaveRequest( $Params );

$tpl = sysInfoTools::eZTemplateFactory();

if ( $isClusterSlaveRequest )
{
    // for requests which 'come from cluster', remove titles
    $tpl->setVariable( 'title', '' );
    $tpl->setVariable( 'description', '' );
    $tpl->setVariable( 'cluster_request', true );
}
else
{
    $tpl->setVariable( 'title', ezSysinfoModule::viewTitle( $view ) );
    $tpl->setVariable( 'description', ezSysinfoModule::viewDescription( $view ) );
    $tpl->setVariable( 'cluster_request', false );
}

// value to these vars can be set by the view code to alter response
$extra_path = '';
$response_type = '';
$response_data = null;

$executionResult = include( "extension/ggsysinfo/modules/sysinfo/$view.php" );

// used by views exiting immediately, such as f.e. on access denied
if ( is_array( $executionResult ) )
{
    return $executionResult;
}

// REST-ish responses allowed but not yet really used...

switch ( $response_type )
{
    case 'json':
        header( 'Content-Type: application/json' );
        echo json_encode( $response_data );
        eZExecution::cleanExit();

    case 'plaintext':
        header( 'Content-Type: text_plain' );
        echo var_export( $response_data );
        eZExecution::cleanExit();

    case '':
        break;

    default:
        eZDebug::writeWarning( "Sysinfo view uses an unsupported response type: $response_type" );
}

// fetch template to render results

$Result = array();

$Result['content'] = $tpl->fetch( "design:sysinfo/$view.tpl" );

// for requests which 'come from cluster', remove pagelayout, as they will be shown inside an iframe

if ( $isClusterSlaveRequest )
{
    /// @todo rewrite links

    // remove pagelayout
    $Result['pagelayout'] = 'design:clusterview_pagelayout.tpl';

    // shall we remove debug info as well?
}
else
{

    // build nav menu & left-hand menu

    $Result['left_menu'] = 'design:parts/sysinfo/menu.tpl';
    $url1stlevel = array( array( 'url' => 'sysinfo/index',
        'text' => sysInfoTools::ezpI18ntr( 'SysInfo', 'System information' ) ) );
    if ( $view == 'index' )
    {
        $url1stlevel[0]['url'] = false;
        $url2ndlevel = array();
    }
    else
    {
        $url2ndlevel = array( array( 'url' => false,
            'text' => sysInfoTools::ezpI18ntr( 'SysInfo', ezSysinfoModule::viewName( $view ) ) ) );
    }
    if ( $extra_path != '' )
    {
        if ( ezSysinfoModule::viewActive( $view )  )
        {
            $url2ndlevel[0]['url'] = "sysinfo/$view";
        }

        $url3rdlevel = array( array( 'url' => false,
            'text' => $extra_path ) );
    }
    else
    {
        $url3rdlevel = array();
    }
    $Result['path'] = array_merge( $url1stlevel, $url2ndlevel, $url3rdlevel );

}
