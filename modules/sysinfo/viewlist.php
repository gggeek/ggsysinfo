<?php
/**
 * List all existing views (optionally, in a given module)
 * @author G. Giunta
 * @version $Id: cachestats.php 18 2010-04-17 14:29:21Z gg $
 * @copyright (C) G. Giunta 2010
 * @license Licensed under GNU General Public License v2.0. See file license.txt
 *
 */

// generic info for all views: module name, extension name, ...
$viewList = array();
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
                foreach( $module->attribute( 'views' ) as $viewname => $view )
                {
                    // merge empty array to facilitate life of templates
                    $view = array_merge( array( 'params' => array(), 'functions' => array(), 'unordered_params' => array() ), $view );
                    $viewList[$viewname . '_' . $modulename] = $view;
                    $viewList[$viewname . '_' . $modulename]['name'] = $viewname;
                    $viewList[$viewname . '_' . $modulename]['module'] = $modulename;
                    $viewList[$viewname . '_' . $modulename]['extension'] = $extension;
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
}

require_once( "kernel/common/template.php" );
$tpl = templateInit();
$tpl->setVariable( 'title', $title );
$tpl->setVariable( 'viewlist', $viewList );

$Result = array();
$Result['content'] = $tpl->fetch( "design:sysinfo/viewlist.tpl" ); //var_dump($cacheFilesList);

$Result['left_menu'] = 'design:parts/sysinfo/menu.tpl';
$Result['path'] = array( array( 'url' => false,
                                'text' => ezi18n( 'SysInfo', 'Views' ) ) );
if ( $Params['modulename'] != '' )
{
    $Result['path'][0]['url'] = '/sysinfo/viewlist';
    $Result['path'][] = array( 'url' => false,
                               'text' => $Params['modulename'] );
}
?>
