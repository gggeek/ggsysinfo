<?php
/**
 * List all existing policy functions (optionally, in a given module)
 * @author G. Giunta
 * @copyright (C) G. Giunta 2010-2014
 * @license Licensed under GNU General Public License v2.0. See file license.txt
 *
 */

// generic info for all views: module name, extension name, ...
$policyList = array();
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
                $extension = '';
                if ( preg_match( '#extension/([^/]+)/modules/#', $path, $matches ) )
                {
                    $extension = $matches[1];
                }
                foreach( $module->attribute( 'available_functions' ) as $policyname => $policy )
                {
                    // merge empty array to facilitate life of templates
                    $policy = array( 'name' => $policyname, 'limitations' => $policy );
                    $policyList[$policyname . '_' . $modulename] = $policy;
                    $policyList[$policyname . '_' . $modulename]['module'] = $modulename;
                    $policyList[$policyname . '_' . $modulename]['extension'] = $extension;
                }
            }
        }
    }
    ksort( $policyList );
}

$title = 'List of available policy functions';
if ( $Params['modulename'] != '' )
{
    $title .= ' in module "' . $Params['modulename'] . '"';
    $extra_path = $Params['modulename'];
}

$tpl->setVariable( 'title', $title );
$tpl->setVariable( 'policylist', $policyList );

?>
