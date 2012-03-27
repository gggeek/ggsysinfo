<?php
/**
 *
 * @author G. Giunta
 * @copyright (C) G. Giunta 2008-2012
 * @license Licensed under GNU General Public License v2.0. See file license.txt
 *
 * @todo add more details, such as dates of first/last files
 * @todo add support for clustered configs - hard currently, since there is no recursive search in api...
 */

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

$tpl->setVariable( 'filelist', $cacheFilesList );

?>
