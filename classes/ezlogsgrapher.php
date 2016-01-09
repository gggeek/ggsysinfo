<?php
/**
 * @author G. Giunta
 * @copyright (C) G. Giunta 2008-2016
 * @license Licensed under GNU General Public License v2.0. See file license.txt
 */

/// @todo see if using a static method instead of plain func works
function calcChurnLabel( $pos, $step )
{
    $locale = eZLocale::instance();
    /// todo: look at time span, if too big use date, if small use time (both will not fit, unless we change axis type)
    // $out = $locale->formatShortDate( $pos );
    $out = $locale->formatShortTime( $pos );
    return $out;
}

class ezLogsGrapher
{

    static $lastError = '';

    /**
     * Parses an eZ log file, returns a (nested) array with one value per log message
     * @return array
     */
    static function splitLog( $logfile, $exclude_regexp='#\] Timing Point: #' )
    {
        $file =  file( $logfile );
        $data = array();
        $content = '';
        $time = 0;
        $ip = '';
        $date = '';
        $label = '';
        foreach ( $file as $line )
        {
            if ( preg_match( '/^\[ ([A-Za-z0-9: ]+) \] \[([0-9.]*)\] (.*)/', $line, $matches ) )
            {
                if ( $time > 0 )
                {
                    $data[] = array( 'date' => $date, 'message' => $content, 'label' => $label, 'source' => $ip, 'timestamp' => $time );
                }
                $time = 0;
                $content = '';
                if ( !preg_match( $exclude_regexp, $line ) )
                {
                    $date = $matches[1];
                    /// @todo test if $time > 0 else log error
                    $time = strtotime( $date );
                    $ip = $matches[2];
                    $label = $matches[3];
                }
            }
            else
            {
                $content .= "\n$line";
            }
        }
        if ( $time > 0 )
        {
            $data[] = array( 'date' => $date, 'message' => $content, 'label' => $label, 'source' => $ip, 'timestamp' => $time );
        }
        return $data;
    }

    /**
    * Returns an array where indexes are timestamps, and values are the number of log events found
    * @param $scale the time interval used to average (default: 1 minute)
    * @return array
    */
    static function parseLog( $logfile, $scale=60, $exclude_regexp='#\] Timing Point: #' )
    {
        if ( !file_exists( $logfile ) )
        {
            return array();
        }
        $file =  file( $logfile );
        $data = array();
        foreach ( $file as $line )
        {
            if ( strlen( $line ) > 22 && substr( $line, 0, 2 ) == '[ ' && substr( $line, 22, 2) == ' ]' )
            {
                if ( !preg_match( $exclude_regexp, $line ) )
                {
                    $time = strtotime( substr( $line, 2, 20 ) );
                    if ( $time > 0 )
                    {

                        $time = $time - ( $time % $scale );
                        if( !isset( $data[$time] ) )
                        {
                            $data[$time] = 1;
                        }
                        else
                        {
                            $data[$time]++;
                        }
                    }
                }
            }
        }
        return $data;
    }

    /**
     * create graph via ezc/gd2
     * @todo verify availability of gd2?
     * @todo improve layout: col. width, x axis labels, etc...
     * @todo if zetacomponent graph is not there, create an error image using gd
     */
    static function graph( $data, $dataname, $scale = 60 )
    {
        $content = false;
        self::$lastError = '';

        $times = array_keys( $data );
        $min = $times[0];
        $max = end( $times );

        if ( !class_exists( 'ezcGraphBarChart' ) )
        {
            $errormsg = "Error while rendering graph: missing Zetacomponents Graph library";
            self::$lastError = $errormsg;
            eZDebug::writeError( $errormsg );
            return false;
        }

        $graph = new ezcGraphBarChart();
        $locale = eZLocale::instance();
        $graph->title = "From " . $locale->formatShortDateTime( $min ) . " to " . $locale->formatShortDateTime( $max );
        //$graph->xAxis->label = "From " . $locale->formatShortDateTime( $min ) . " to " . $locale->formatShortDateTime( $max );
        $graph->options->font->maxFontSize = 10;
        $graph->palette = new ezcGraphPaletteEzBlue();
        $graph->yAxis->label = $dataname;
        $graph->yAxis->min = 0;
        $graph->legend = false;
        // width of bar charts is not calculated correctly by DateAxis
        //$graph->xAxis = new ezcGraphChartElementDateAxis();
        //$graph->xAxis->interval = $scale;
        $graph->xAxis = new ezcGraphChartElementNumericAxis();
        $graph->xAxis->min = $min - ( $scale / 2 );
        $graph->xAxis->max = $max + ( $scale / 2 );
        $graph->xAxis->labelCallback = 'calcChurnLabel';
        $graph->data[$dataname] = new ezcGraphArrayDataSet( $data );
        $graph->driver = new ezcGraphGdDriver2();
        $graph->driver->options->imageFormat = IMG_JPEG;
        // pick a font that is delivered along with ezp
        $graph->options->font = 'design/standard/fonts/arial.ttf';

        try
        {
            $ok = ob_start();
            $graph->render( 600, 400, 'php://stdout' );
            $content = ob_get_clean();
            //$clusterfile->fileStoreContents( $cachefile, $content );
        } catch( exception $e )
        {
            $errormsg = "Error while rendering graph: " . $e->getMessage();
            self::$lastError = $errormsg;
            eZDebug::writeError( $errormsg );
        }
        return $content;
    }

    static function asum( $a1, $a2 )
    {
        foreach ( $a2 as $key => $val )
        {
            if ( isset( $a1[$key] ) )
            {
                $a1[$key] = $a1[$key] + $val;
            }
            else
            {
                $a1[$key] = $val;
            }
        }
        return $a1;
    }

    static function lastError()
    {
        return self::$lastError;
    }
}
?>
