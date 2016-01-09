<?php
/**
 * Not unlike genericview.php, but it is just a "container" view, where real data is gathered from all cluster nodes,
 * and displayed via Iframes.
 *
 * Mode of operation: iframe contents are served from this same view, which acts as a proxy, requesting the data from other nodes
 *
 * @author G. Giunta
 * @copyright (C) G. Giunta 2014-2016
 * @license Licensed under GNU General Public License v2.0. See file license.txt
 *
 * @todo use a 3-level path, with the name of the group as 2nd element ?
 */

/// @var eZModule $module
$module = $Params['Module'];
$view = $module->currentView();

// rely on system policy instead of creating our own, but allow also PolicyOmitList for single views
// (useful f.e. for system status checks from tools which can not authenticate because they are too simple).
$ini = eZINI::instance();
if ( !in_array( "sysinfo/$view", $ini->variable( 'RoleSettings', 'PolicyOmitList' ) ) )
{
    $user = eZUser::currentUser();
    $access = $user->hasAccessTo( 'setup', 'system_info' );
    if ( $access['accessWord'] != 'yes' )
    {
        return $module->handleError( eZError::KERNEL_ACCESS_DENIED, 'kernel' );
    }
}

// check if we are acting as proxy

$clusterProxyRequestTarget = ezSysinfoClusterManager::clusterProxyRequestTarget( $Params );
if ( $clusterProxyRequestTarget != '' )
{
    // this view is a mere proxy - it retrieves data from the real backend node and gives it back

    // generate (and store) auth token for the view we are going to query
    $token = ezSysinfoClusterManager::generateAuthToken( $view );

    $nodeUrls = ezSysinfoClusterManager::clusterDataRetrievalUrls(
        'slave',
        $module->currentRedirectionURI(),
        array( 'authtoken' => $token ),
        $_GET
    );

    if ( !isset( $nodeUrls[$clusterProxyRequestTarget] ) )
    {
        eZDebug::writeWarning( "Node $clusterProxyRequestTarget not in cluster" );
        return $module->handleError( eZError::KERNEL_NOT_FOUND, 'kernel' );
    }

    $url = $nodeUrls[$clusterProxyRequestTarget];
    if ( $fp = ezSysinfoClusterManager::clusterDataFopen( $url ) )
    {
        ob_start();
        fpassthru( $fp );
        $buffer = ob_get_contents();
        //$size = ob_get_length();
        ob_end_clean();

        $info = stream_get_meta_data( $fp );

        // use the stream metadata ['wrapper_data'] to re-inject content-type
        $contentType = '';
        $meta = stream_get_meta_data( $fp );
        foreach( $meta['wrapper_data'] as $header )
        {
            if ( strpos( $header, 'Content-Type: ' ) === 0 )
            {
                $contentType = trim( substr( $header, 14 ) );
                header( $header );
            }
        }
        fclose( $fp );

        if ( strpos( $contentType, 'text/html' ) === 0 )
        {
            $urlParams = '/(targetnode)/' . $clusterProxyRequestTarget;
            // replace all links except those to css/js/gif
            $buffer = preg_replace( '# (href|src)="([^?"]+)(\?[^"]+)?"#' , ' ${1}="${2}' . $urlParams . '${3}"', $buffer );
            $buffer = preg_replace( '# (href|src)="([^?"]+)\.(css|js|gif)' . preg_quote( $urlParams, '#' ) . '#' , ' ${1}="${2}.${3}', $buffer );
        }

        echo $buffer;

        /*eZClusterURLFilter::setUrlParameters( '/(targetnode)/' . $clusterProxyRequestTarget );
        stream_filter_register( 'urlFilter', 'eZClusterURLFilter' );
        stream_filter_append( $fp, 'urlFilter', STREAM_FILTER_READ );
        fpassthru( $fp );*/


    }
    else
    {
        eZDebug::writeWarning( "Could not connect to $url" );
    }

    eZExecution::cleanExit();
}

//$isClusterMasterRequest = count( ezSysinfoClusterManager::clusterNodes() ) && ezSysinfoModule::viewClusterMode( $view ) == 'split';

$tpl = sysInfoTools::eZTemplateFactory();

$tpl->setVariable( 'title', ezSysinfoModule::viewTitle( $view ) );
$tpl->setVariable( 'description', ezSysinfoModule::viewDescription( $view ) );
$tpl->setVariable( 'cluster_nodes', ezSysinfoClusterManager::clusterDataRetrievalUrls( 'proxy', $module->currentRedirectionURI() ) );

// note that we do not execute any php sub-view

// value to these vars can be set by the view code to alter response
$extra_path = '';

// fetch template to render results

$Result = array();

$Result['content'] = $tpl->fetch( "design:sysinfo/clustermasterview.tpl" );

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
