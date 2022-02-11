<?php
/**
 * List all existing modules (optionally, in a given extension)
 * @author G. Giunta
 * @copyright (C) G. Giunta 2010-2022
 * @license Licensed under GNU General Public License v2.0. See file license.txt
 *
 */

// generic info for all modules: number of views, fetch functions, policy functions, name of extension
$moduleList = array();

/// @todo spit an error msg if given extenion name is not an active ext

$modules = eZModuleLister::getModuleList();
foreach( $modules as $modulename => $path )
{
    $module = eZModule::exists( $modulename );
    if ( $module instanceof eZModule )
    {
        $extension = '';
        if ( preg_match( '#extension/([^/]+)/modules/#', $path, $matches ) )
        {
            $extension = $matches[1];
        }
        if ( $Params['extensionname'] == '' || $Params['extensionname'] == $extension )
        {
            /// @todo this generates a warning in debug logs if there are no functions; how to avoid it?
            ///       The only way seems to pre_test for file existence, looking only
            ///       at the same dir as where the module is
            $functions = eZFunctionHandler::moduleFunctionInfo( $modulename );
            /// @todo prevent warning to be generated here, too
            $moduleOperationInfo = new eZModuleOperationInfo( $modulename );
            $moduleOperationInfo->loadDefinition();
            $moduleList[$modulename] = array(
                'views' => count( $module->attribute( 'views' ) ),
                'fetch_functions' => count( $functions->FunctionList ),
                'policy_functions' => count( $module->attribute( 'available_functions' ) ),
                'operations' => count( $moduleOperationInfo->OperationList ),
                'extension' => $extension
            );
        }
    }
}
ksort( $moduleList );

$title = 'List of available modules';
if ( $Params['extensionname'] != '' )
{
    $title .= ' in extension "' . $Params['extensionname'] . '"';
    $extra_path = $Params['extensionname'];
}

$ezgeshi_available = sysInfoTools::ezgeshiAvailable();

$tpl->setVariable( 'title', $title );
$tpl->setVariable( 'modulelist', $moduleList );
$tpl->setVariable( 'sdkversion', eZPublishSDK::version() );
$tpl->setVariable( 'ezgeshi_available', $ezgeshi_available );
$tpl->setVariable( 'source_available', sysInfoTools::sourceCodeAvailable( eZPublishSDK::version() ) );
