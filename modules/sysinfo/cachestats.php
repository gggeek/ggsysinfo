<?php
/**
 * @author G. Giunta
 * @copyright (C) G. Giunta 2008-2022
 * @license Licensed under GNU General Public License v2.0. See file license.txt
 *
 * @todo add more details, such as dates of first/last files
 * @todo add possibility to zoom in to file list going to cachesearch view
 * @todo add support for db-clustered configs - hard currently, since there is no recursive search in api...
 * @todo in ezdfs mode allow user to only show clustered data
 */

/** @var array $Params */
/** @var eZTemplate $tpl */
/** @var eZINI $ini */

$cacheFilesList = array();

// work around legacy kernel bug with ezplatform 2.5
$siteDir = preg_replace('#/app\.php$#', '', eZSys::siteDir());

$cacheList = eZCache::fetchList();
foreach ( $cacheList as $cacheItem )
{
    if ( $cacheItem['path'] != false && $cacheItem['enabled'] )
    {
        $cacheFilesList[$cacheItem['name']] = array( 'path' => $cacheItem['path'] );

        // take care: this is hardcoded from knowledge of cache structure...
        if ( $cacheItem['path'] == 'var/cache/ini' )
        {
            // work around legacy kernel bug with ezplatform 2.5
            $cachedir = $siteDir . '/' . $cacheItem['path'];
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
if ( class_exists( 'Symfony\Component\HttpKernel\Kernel' ) && (
    is_dir( $ezp5CacheDir = $siteDir . '/../ezpublish/cache' ) || is_dir( $ezp5CacheDir = $siteDir . '/../var/cache' )) )
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
