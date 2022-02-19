<?php
/**
 * @author G. Giunta
 * @copyright (C) G. Giunta 2008-2022
 * @license Licensed under GNU General Public License v2.0. See file license.txt
 *
 * @todo add more details, such as dates of first/last files
 * @todo add support for db-clustered configs - hard currently, since there is no recursive search in api...
 * @todo in edfs mode allow user to only show clustered data
 */

/** @var array $Params */
/** @var eZTemplate $tpl */
/** @var eZINI $ini */

$storageFilesList = array();

$storageDir = eZSys::storageDirectory();

$clusterStorageDir = ezSysinfoClusterManager::clusterFileStorageDir();
if ( $clusterStorageDir != '' )
{
    $storageDir = $clusterStorageDir . '/' . $storageDir;
    $files = @scandir( $storageDir );
    foreach( $files as $file )
    {
        if ( $file != '.' && $file != '..' && is_dir( $storageDir . '/' . $file ) )
        {
            $storageFilesList["DFS://$file"] = array(
                'path' => $storageDir . '/' . $file,
                'count' => number_format( sysInfoTools::countFilesInDir( $storageDir . '/' . $file ) ),
                'size' => number_format( sysInfoTools::countFilesSizeInDir( $storageDir . '/' . $file ) ) );
        }
    }
}

if ( $Params['viewmode'] == 'json' )
{
    $response_type = $Params['viewmode'];
    $response_data = $storageFilesList;
    return;
}

$tpl->setVariable( 'filelist', $storageFilesList );
