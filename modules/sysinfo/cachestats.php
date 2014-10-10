<?php
/**
 *
 * @author G. Giunta
 * @copyright (C) G. Giunta 2008-2014
 * @license Licensed under GNU General Public License v2.0. See file license.txt
 *
 * @todo add more details, such as dates of first/last files
 * @todo add possibility to zoom in to file list going to cachesearch view
 * @todo add support for db-clustered configs - hard currently, since there is no recursive search in api...
 * @todo in ezdfs mode allow user to only show clustered data
 */

$cacheFilesList = array();

$cacheList = eZCache::fetchList();
foreach ( $cacheList as $cacheItem )
{
    if ( $cacheItem['path'] != false && $cacheItem['enabled'] )
    {
        $cacheFilesList[$cacheItem['name']] = array( 'path' => $cacheItem['path'] );

        // take care: this is hardcoded from knowledge of cache structure...
        if ( $cacheItem['path'] == 'var/cache/ini' )
        {
            $cachedir = eZSys::siteDir() . '/' . $cacheItem['path'];
        }
        else
        {
            $cachedir = eZSys::cacheDirectory() . '/' . $cacheItem['path'];
        }
        $count = sysInfoTools::countFilesInDir( $cachedir );
        $cacheFilesList[$cacheItem['name']]['count'] = $count;
        if ( $count )
        {
            $cacheFilesList[$cacheItem['name']]['size'] = number_format( sysInfoTools::countFilesSizeInDir( $cachedir ) );
        }
        else
        {
            $cacheFilesList[$cacheItem['name']]['size'] = "";
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
