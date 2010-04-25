<?php
/**
 * Class that scans all active module definitions
 * Copied here from ezwebservicesapi and renamed to avoid clashes
 *
 * @author G. Giunta
 * @version $Id: ggezwebservicesclient.php 102 2009-09-02 09:03:34Z gg $
 * @copyright (C) G. Giunta 2010
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

?>