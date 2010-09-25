<?php
/**
 *
 * @author G. Giunta
 * @version $Id$
 * @copyright (C) G. Giunta 2008-2010
 * @license Licensed under GNU General Public License v2.0. See file license.txt
 *
 * @todo add more details, such as dates of first/last files
 * @todo add possibility to zoom in to file list going to cachesearch view
 * @todo add support for clustered configs - hard currently, since there is no recursive search in api...
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
            $cacheFilesList[$cacheItem['name']]['count'] = sysInfoTools::countFilesInDir( eZSys::siteDir() . '/' . $cacheItem['path'] );
            $cacheFilesList[$cacheItem['name']]['size'] = number_format( sysInfoTools::countFilesSizeInDir( eZSys::siteDir() . '/' . $cacheItem['path'] ) );
        }
        else
        {
            $cacheFilesList[$cacheItem['name']]['count'] = sysInfoTools::countFilesInDir( eZSys::cacheDirectory() . '/' . $cacheItem['path'] );
            $cacheFilesList[$cacheItem['name']]['size'] = number_format( sysInfoTools::countFilesSizeInDir( eZSys::cacheDirectory() . '/' . $cacheItem['path'] ) );
        }
    }
}

$tpl->setVariable( 'filelist', $cacheFilesList );

?>
