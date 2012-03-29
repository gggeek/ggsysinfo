<?php
/**
 * List all existing tpl ops (optionally, in a given extension)
 *
 * @author G. Giunta
 * @copyright (C) G. Giunta 2012
 * @license Licensed under GNU General Public License v2.0. See file license.txt
 *
 */

// generic info for all views: module name, extension name, ...
$operatorList = array();
$extensions = eZModuleLister::getModuleList(); // ...
if ( $Params['extensionname'] != '' && !array_key_exists( $Params['extensionname'], $extensions ) )
{
    /// @todo
}
else
{
    $classes = sysInfoTools::autoloadClasses();

    require_once( "kernel/common/template.php" );
    $tpl = templateInit();
    foreach( $tpl->Operators as $opName => $operatorDef )
    {
        if (!isset($operatorDef['class']))
        {
            // cheating
            $operatorDef['script'] = 'kernel/common/eztemplateautoload.php';
            $operatorDef['class'] = '';
        }
        else
        {
            // load real path from class name
            $operatorDef['script'] = $classes[$operatorDef['class']];
        }

        if ( $Params['extensionname'] == '' || strpos( $operatorDef['script'], 'extension/' . $Params['extensionname'] . '/' ) === 0 )
        {
            $extension = '';
            if ( preg_match( '#^extension/([^/]+)/#', $operatorDef['script'], $matches ) )
            {
                $extension = $matches[1];
            }

            $operatorList[$opName] = array(
                'script' => $operatorDef['script'],
                'class' => $operatorDef['class'],
                'extension' => $extension,
                'params' => $tpl->operatorParameterList( $opName )
            );
        }
    }
    ksort( $operatorList );
}

$title = 'List of available template operators';
if ( $Params['extensionname'] != '' )
{
    $title .= ' in extension "' . $Params['extensionname'] . '"';
    $extra_path = $Params['extensionname'];
}

$ezgeshi_available = sysInfoTools::ezgeshiAvailable();

$tpl->setVariable( 'title', $title );
$tpl->setVariable( 'operatorlist', $operatorList );
$tpl->setVariable( 'sdkversion', eZPublishSDK::version() );
$tpl->setVariable( 'ezgeshi_available', $ezgeshi_available );

?>
