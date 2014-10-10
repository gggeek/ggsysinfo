<?php
/**
 * A helper class for generating reports
 *
 * @author G. Giunta
 * @copyright (C) G. Giunta 2012-2014
 * @license Licensed under GNU General Public License v2.0. See file license.txt
 */

class reportGenerator
{
    protected static $ezpClasses = null;

    public function getCSV( $report )
    {
        $out = '';
        foreach ( $report as $test )
        {
            $out .= "----------\n";
            $out .= $test['title'] . "\n";
            $out .= "----------\n";

            if ( @$test['byrow'] )
            {
                foreach( $test['data'] as $key => $val )
                {
                    $out .= "$key, $val\n";
                }
            }
            else
            {
                foreach( $test['data'] as $row )
                {
                    {
                        $out .= implode( ',', $row ) . "\n";
                    }
                }
            }
        }
        return $out;
    }

    /*function writePlaintext( $report )
    {

    }*/

    public function formatBytes( $size, $precision = 2 )
    {
        $base = log( $size ) / log( 1024 );
        $suffixes = array( 'Bytes', 'kB', 'MB', 'GB', 'TB' );

        return round( pow( 1024, $base - floor( $base ) ), $precision ) . $suffixes[floor( $base )];
    }
}

?>