<?php
/**
 * A helper class for generating reports
 *
 * @author G. Giunta
 * @copyright (C) G. Giunta 2012-2019
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

            switch( @$test['format'] )
            {
                /// @todo convert html-with-tables to plaintext
                case 'html':
                    $out .= $test['data'] . "\n";
                    break;

                case 'byrow':
                    foreach( $test['data'] as $key => $val )
                    {
                        $out .= "$key, $val\n";
                    }
                    break;

                case 'byline':
                default:
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
