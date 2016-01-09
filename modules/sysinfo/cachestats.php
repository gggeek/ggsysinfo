<?php
/**
 *
 * @author G. Giunta
 * @copyright (C) G. Giunta 2008-2016
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

// q: are we 100% sure that the eZ5 cache is always at that location?
if ( class_exists( 'Symfony\Component\HttpKernel\Kernel' ) && is_dir( $ezp5CacheDir = eZSys::siteDir() . '/../ezpublish/cache' ) )
{
    foreach( glob( $ezp5CacheDir . '/*' , GLOB_ONLYDIR ) as $envDir )
    {
        $env = basename( $envDir );
        foreach( glob( $envDir . '/*' , GLOB_ONLYDIR ) as $cacheDir )
        {
            $cache = basename( $cacheDir );
            $cacheName = "Symfony/$env/$cache";
            $count = sysInfoTools::countFilesInDir( $cacheDir );
            $cacheFilesList[$cacheName] = array(
                'path' => "ezpublish/cache/$env/$cache",
                'size' => ( $count ? number_format( sysInfoTools::countFilesSizeInDir( $cacheDir ) ) : "" ),
                'count' => $count
            );
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
