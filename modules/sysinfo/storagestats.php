<?php
/**
 *
 * @author G. Giunta
 * @version $Id$
 * @copyright (C) G. Giunta 2008-2010
 * @license Licensed under GNU General Public License v2.0. See file license.txt
 *
 * @todo add more details, such as dates of first/last files
 * @todo add support for clustered configs - hard currently, since there is no recursive search in api...
 */

$module = $Params['Module'];

// rely on system policy instead of creating our own, but allow also PolicyOmitList
$ini = eZINI::instance();
if ( !in_array( 'sysinfo/storagestats', $ini->variable( 'RoleSettings', 'PolicyOmitList' ) ) )
{
    $user = eZUser::currentUser();
    $access = $user->hasAccessTo( 'setup', 'system_info' );
    if ( $access['accessWord'] != 'yes' )
    {
        return $module->handleError( eZError::KERNEL_ACCESS_DENIED, 'kernel' );
    }
}

$storagedir = eZSys::storageDirectory();
$files = @scandir( eZSys::storageDirectory() );
foreach( $files as $file )
{
    if ( $file != '.' && $file != '..' && is_dir( $storagedir . '/' . $file ) )
    {
        $cacheFilesList[$file] = array(
            'path' => $storagedir . '/' . $file,
            'count' => number_format( sysInfoTools::countFilesInDir( $storagedir . '/' . $file ) ),
            'size' => number_format( sysInfoTools::countFilesSizeInDir( $storagedir . '/' . $file ) ) );
    }
}

require_once( "kernel/common/template.php" );
$tpl = templateInit();
$tpl->setVariable( 'title', 'Storage stats' );
$tpl->setVariable( 'filelist', $cacheFilesList );

$Result = array();
$Result['content'] = $tpl->fetch( "design:sysinfo/cachestats.tpl" ); //var_dump($cacheFilesList);

$Result['left_menu'] = 'design:parts/sysinfo/menu.tpl';
$Result['path'] = array( array( 'url' => false,
                                'text' => ezi18n( 'SysInfo', 'Storage stats' ) ) );

?>
