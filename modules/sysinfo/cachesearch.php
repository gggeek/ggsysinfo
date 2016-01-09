<?php
/**
 *
 * @author G. Giunta
 * @copyright (C) G. Giunta 2008-2016
 * @license Licensed under GNU General Public License v2.0. See file license.txt
 *
 * @todo add support for offset, to facilitate display of long searches results
 * @todo add confirmation message with nr. of deleted files
 * @todo add support for clustered configs - hard currently, since there is no recursive search in api...
 */

$module = $Params['Module'];
$http = eZHTTPTool::instance();

$deletedfiles = 0;
$filelist = array();
$searchtext = '';
$cacheDirsList = array();
$cacheDirsList2 = array();
$is_regexp = false;

// delete file(s) if path in POST
if ( $http->hasPostVariable( 'RemoveButton' ) && $http->hasPostVariable( 'deleteFile' ) && is_array( $http->postVariable( 'deleteFile' ) ) )
{
    if ( !$user->hasAccessTo( 'setup', 'managecache' ) )
    {
        return $module->handleError( eZError::KERNEL_ACCESS_DENIED, 'kernel' );
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

$cacheList = eZCache::fetchList();
foreach ( $cacheList as $cacheItem )
{
    if ( $cacheItem['path'] != false && $cacheItem['enabled'] )
    {
        $cacheDirsList[$cacheItem['id']] = $cacheItem['path'];
        $cacheDirsList2[$cacheItem['id']] = false;
    }
}

if ( $http->hasPostVariable( 'SearchText' ) && $http->hasPostVariable( 'SearchCaches' ) )
{
    $ini = eZINI::instance();
    $cachedir = eZSys::cacheDirectory();
    //$cachedir .= "/" .$ini->variable( 'ContentSettings', 'CacheDir' );
    $searchtext = $http->postVariable( 'SearchText' );
    $is_regexp = $http->hasPostVariable( 'SearchType' ) && ( $http->postVariable( 'SearchType' ) == 'Regexp' );
    foreach( $http->postVariable( 'SearchCaches' ) as $cache )
    {
        if ( array_key_exists( $cache, $cacheDirsList ) )
        {
            // take care: this is hardcoded from knowledge of cache structure...
            if ( $cacheDirsList[$cache] == 'var/cache/ini' )
            {
                $filelist = array_merge( $filelist, sysInfoTools::searchInFiles( $searchtext, eZSys::siteDir() . '/' . $cacheDirsList[$cache], $is_regexp ) );
            }
            else
            {
                $filelist = array_merge( $filelist, sysInfoTools::searchInFiles( $searchtext, $cachedir . '/' . $cacheDirsList[$cache], $is_regexp ) );
            }
            $cacheDirsList2[$cache] = true;
        }
    }
    //print_r($filelist);
}

$tpl->setVariable( 'filelist', $filelist );
$tpl->setVariable( 'list_count', count( $filelist ) );
$tpl->setVariable( 'searchtext', $searchtext );
$tpl->setVariable( 'cachelist', $cacheDirsList2 );
$tpl->setVariable( 'deletedfiles', $deletedfiles );
$tpl->setVariable( 'is_regexp', $is_regexp );

?>
