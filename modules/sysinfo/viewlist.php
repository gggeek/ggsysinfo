<?php
/**
 * List all existing views (optionally, in a given module)
 * @author G. Giunta
 * @copyright (C) G. Giunta 2010-2017
 * @license Licensed under GNU General Public License v2.0. See file license.txt
 *
 */

// generic info for all views: module name, extension name, ...
$viewList = array();
$modules = eZModuleLister::getModuleList();
if ( $Params['modulename'] != '' && !array_key_exists( $Params['modulename'], $modules ) )
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
                $extension = '';
                if ( preg_match( '#extension/([^/]+)/modules/#', $path, $matches ) )
                {
                    $extension = $matches[1];
                }
                foreach( $module->attribute( 'views' ) as $viewname => $viewx )
                {
                    // merge empty array to facilitate life of templates
                    $viewx = array_merge( array( 'params' => array(), 'functions' => array(), 'unordered_params' => array(), 'single_post_actions' => array(), 'post_actions' => array(), 'post_action_parameters' => array() ), $viewx );
                    $viewList[$viewname . '_' . $modulename] = $viewx;
                    $viewList[$viewname . '_' . $modulename]['name'] = $viewname;
                    $viewList[$viewname . '_' . $modulename]['module'] = $modulename;
                    $viewList[$viewname . '_' . $modulename]['extension'] = $extension;
                    // merge all post parameters stuff
                    $post_params = array_merge( $viewx['post_actions'], array_keys( $viewx['single_post_actions'] ) );
                    foreach( $viewx['post_action_parameters'] as $key => $params )
                    {
                        $post_params = array_merge( $post_params, array_keys( $params ) );
                    }
                    sort( $post_params );
                    $viewList[$viewname . '_' . $modulename]['post_params'] = array_unique( $post_params );
                }
            }
        }
    }
    ksort( $viewList );
}

$title = 'List of available views';
if ( $Params['modulename'] != '' )
{
    $title .= ' in module "' . $Params['modulename'] . '"';
    $extra_path = $Params['modulename'];
}

$ezgeshi_available = sysInfoTools::ezgeshiAvailable();

$tpl->setVariable( 'title', $title );
$tpl->setVariable( 'viewlist', $viewList );
$tpl->setVariable( 'sdkversion', eZPublishSDK::version() );
$tpl->setVariable( 'ezgeshi_available', $ezgeshi_available );
$tpl->setVariable( 'source_available', sysInfoTools::sourceCodeAvailable( eZPublishSDK::version() ) );
