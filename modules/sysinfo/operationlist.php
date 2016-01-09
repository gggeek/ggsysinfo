<?php
/**
 * List all existing operations (optionally, in a given module)
 * @author G. Giunta
 * @copyright (C) G. Giunta 2010-2016
 * @license Licensed under GNU General Public License v2.0. See file license.txt
 *
 */

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
    $extra_path = $Params['modulename'];
}

$ezgeshi_available = sysInfoTools::ezgeshiAvailable();

$tpl->setVariable( 'title', $title );
$tpl->setVariable( 'operationlist', $operationList );
$tpl->setVariable( 'sdkversion', eZPublishSDK::version() );
$tpl->setVariable( 'ezgeshi_available', $ezgeshi_available );
$tpl->setVariable( 'source_available', sysInfoTools::sourceCodeAvailable( eZPublishSDK::version() ) );

?>
