<?php
/**
 *
 * @author G. Giunta
 * @version $Id$
 * @copyright (C) G. Giunta 2008
 * @license Licensed under GNU General Public License v2.0. See file license.txt
 *
 * @todo add support for offset, to facilitate display of long searches results
 * @todo add confirmation message with nr. of deleted files
 * @todo add support for separate cache searches: templates, template-blocks, etc...
 * @todo add support for clustered configs - hard currently, since there is no recursive search in api...
 * @todo add support for basic (non regexp) search
 */

$module = $Params['Module'];
$http = eZHTTPTool::instance();

$user = eZUser::currentUser();

if ( !$user->hasAccessTo( 'setup', 'system_info' ) )
{
    return $Module->handleError( eZError::KERNEL_ACCESS_DENIED, 'kernel' );
}

$deletedfiles = 0;
$filelist = array();
$searchtext = '';

// delete file(s) if path in POST
if ( $http->hasPostVariable( 'RemoveButton' ) && $http->hasPostVariable( 'deleteFile' ) && is_array( $http->postVariable( 'deleteFile' ) ) )
{
    if ( !$user->hasAccessTo( 'setup', 'managecache' ) )
    {
        return $Module->handleError( eZError::KERNEL_ACCESS_DENIED, 'kernel' );
    }

    $fileHandler = eZClusterFileHandler::instance();
    foreach( $http->postVariable( 'deleteFile' ) as $item)
    {
        $fileHandler->fileDelete( $item );
        //if( unlink($item) )
        //{
            // file has been deleted
            $deletedfiles++;
        //}
    }
}

if ( $http->hasPostVariable( 'SearchText' ) )
{
    $ini = eZINI::instance();
    $cachedir = eZSys::cacheDirectory();
    //$cachedir .= "/" .$ini->variable( 'ContentSettings', 'CacheDir' );
    $searchtext = $http->postVariable( 'SearchText' );
    $filelist = sysInfoTools::searchInFiles( $searchtext, $cachedir );
    //print_r($filelist);
}


require_once( "kernel/common/template.php" );
$tpl = templateInit();
$tpl->setVariable( 'filelist', $filelist );
$tpl->setVariable( 'list_count', count( $filelist ) );
$tpl->setVariable( 'searchtext', $searchtext );
$tpl->setVariable( 'deletedfiles', $deletedfiles );

$Result = array();
$Result['content'] = $tpl->fetch( "design:sysinfo/cachesearch.tpl" );

$Result['left_menu'] = 'design:parts/sysinfo/menu.tpl';
$Result['path'] = array( array( 'url' => false,
                                'text' => ezi18n( 'SysInfo', 'Cache search' ) ) );


?>
