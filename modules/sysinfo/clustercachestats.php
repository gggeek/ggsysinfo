<?php
/**
 *
 * @author G. Giunta
 * @copyright (C) G. Giunta 2008-2022
 * @license Licensed under GNU General Public License v2.0. See file license.txt
 *
 * @todo add more details, such as dates of first/last files
 * @todo add possibility to zoom in to file list going to cachesearch view
 * @todo add support for db-clustered configs - hard currently, since there is no recursive search in api...
 * @todo in ezdfs mode allow user to only show clustered data
 */

$cacheFilesList = array();

$cacheList = eZCache::fetchList();

$clusterStorageDir = ezSysinfoClusterManager::clusterFileStorageDir();
if ( $clusterStorageDir != '' )
{
    $storageDir = $clusterStorageDir;

    foreach ( $cacheList as $cacheItem )
    {
        if ( $cacheItem['path'] != false && $cacheItem['enabled'] )
        {
            $cacheName = 'DFS://' . $cacheItem['name'];

            // take care: this is hardcoded from knowledge of cache structure...
            if ( $cacheItem['path'] == 'var/cache/ini' )
            {
                //$cachedir = $storageDir . '/' . eZSys::siteDir() . '/' . $cacheItem['path'];
                // no var/cache/ini in dfs nfs storage
                continue;
            }
            else
            {
                $cachedir = $storageDir . '/' . eZSys::cacheDirectory() . '/' . $cacheItem['path'];
            }
            $cacheFilesList[$cacheName] = array( 'path' => $cachedir );
            $count = sysInfoTools::countFilesInDir( $cachedir );
            $cacheFilesList[$cacheName]['count'] = $count;
            if ( $count )
            {
                $cacheFilesList[$cacheName]['size'] = number_format( sysInfoTools::countFilesSizeInDir( $cachedir ) );
            }
            else
            {
                $cacheFilesList[$cacheName]['size'] = "";
            }
        }
    }

}

if ( $Params['viewmode'] == 'json' )
{
    $response_type = $Params['viewmode'];
    $response_data = $cacheFilesList;
    return;
}

$tpl->setVariable( 'filelist', $cacheFilesList );
