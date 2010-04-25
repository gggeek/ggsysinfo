<?php
/**
 * List all existing policy functions (optionally, in a given module)
 * @author G. Giunta
 * @version $Id: cachestats.php 18 2010-04-17 14:29:21Z gg $
 * @copyright (C) G. Giunta 2010
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
}

require_once( "kernel/common/template.php" );
$tpl = templateInit();
$tpl->setVariable( 'title', $title );
$tpl->setVariable( 'policylist', $policyList );

$Result = array();
$Result['content'] = $tpl->fetch( "design:sysinfo/policylist.tpl" ); //var_dump($cacheFilesList);

$Result['left_menu'] = 'design:parts/sysinfo/menu.tpl';
$Result['path'] = array( array( 'url' => false,
                                'text' => ezi18n( 'SysInfo', 'Policy Functions' ) ) );
if ( $Params['modulename'] != '' )
{
    $Result['path'][0]['url'] = '/sysinfo/policylist';
    $Result['path'][] = array( 'url' => false,
                               'text' => $Params['modulename'] );
}
?>
