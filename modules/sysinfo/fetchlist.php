<?php
/**
 * List all existing fetch functions (optionally, in a given module)
 * @author G. Giunta
 * @copyright (C) G. Giunta 2010-2012
 * @license Licensed under GNU General Public License v2.0. See file license.txt
 *
 */

// rely on system policy instead of creating our own, but allow also PolicyOmitList
$ini = eZINI::instance();
if ( !in_array( 'sysinfo/fetchlist', $ini->variable( 'RoleSettings', 'PolicyOmitList' ) ) )
{
    $user = eZUser::currentUser();
    $access = $user->hasAccessTo( 'setup', 'system_info' );
    if ( $access['accessWord'] != 'yes' )
    {
        $module = $Params['Module'];
        return $module->handleError( eZError::KERNEL_ACCESS_DENIED, 'kernel' );
    }
}

// generic info for all views: module name, extension name, ...
$fetchList = array();
$modules = eZModuleLister::getModuleList();
if ( $Params['modulename'] != '' && !array_key_exists( $Params['modulename'], $modules) )
{
    /// @todo
}
else
{

    $classes = sysInfoTools::autoloadClasses();
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
                $functions = eZFunctionHandler::moduleFunctionInfo( $modulename );
                foreach( $functions->FunctionList as $fetchname => $fetch )
                {
                    // merge empty array to facilitate life of templates
                    $fetch = array_merge( array( 'name' => $fetchname, 'parameters' => array(), ), $fetch );
                    // if fetch is done via class method and file to be included misses, calculate it using autoload
                    if ( isset( $fetch['call_method']['class'] ) && !isset($fetch['call_method']['include_file'] ) )
                    {
                        if ( isset( $classes[$fetch['call_method']['class']] ) )
                        {
                            $fetch['call_method']['include_file'] = $classes[$fetch['call_method']['class']];
                        }
                        else
                        {
                            eZDebug::writeWarning( 'Cannot find in autoloads php file for class ' . $fetch['call_method']['class'], __METHOD__ );
                        }
                    }
                    $fetchList[$fetchname . '_' . $modulename] = $fetch;
                    $fetchList[$fetchname . '_' . $modulename]['module'] = $modulename;
                    $fetchList[$fetchname . '_' . $modulename]['extension'] = $extension;
                }
            }
        }
    }
    ksort( $fetchList );
}

$title = 'List of available fetch functions';
if ( $Params['modulename'] != '' )
{
    $title .= ' in module "' . $Params['modulename'] . '"';
    $extra_path =  $Params['modulename'];
}

$ezgeshi_available = sysInfoTools::ezgeshiAvailable();

$tpl->setVariable( 'title', $title );
$tpl->setVariable( 'fetchlist', $fetchList );
$tpl->setVariable( 'sdkversion', eZPublishSDK::version() );
$tpl->setVariable( 'ezgeshi_available', $ezgeshi_available );

?>
