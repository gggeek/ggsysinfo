<?php
/**
 * Class that scans all active module definitions
 * Copied here from ezwebservicesapi and renamed to avoid clashes
 *
 * @author G. Giunta
 * @copyright (C) G. Giunta 2010-2019
 * @license Licensed under GNU General Public License v2.0. See file license.txt
 */

class eZModuleLister
{

    /**
    * Finds all available modules in the system
    * @return array $modulename => $path
    */
    static function getModuleList()
    {
        $out = array();
        foreach ( eZModule::globalPathList() as $path )
        {
            foreach ( scandir( $path ) as $subpath )
            {
                if ( $subpath != '.' && $subpath != '..' && is_dir( $path . '/' . $subpath ) && file_exists( $path . '/' . $subpath . '/module.php' ) )
                {
                    $out[$subpath] = $path . '/' . $subpath . '/module.php';
                }
            }
        }
        return $out;
    }

    /**
    * @return array
    */
    /*static function analyzeModule( $path )
    {

    }*/
}
