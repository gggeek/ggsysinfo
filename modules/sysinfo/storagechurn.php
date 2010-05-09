<?php
/**
 * Create a graph of files-per-minute by analyzing storage.log
 *
 * @author G. Giunta
 * @version $Id$
 * @copyright (C) G. Giunta 2008-2010
 * @license Licensed under GNU General Public License v2.0. See file license.txt
 *
 * @todo verify availability of gd2?
 * @todo add support for user-selected start and end date
 * @todo support coarser intervals than 60 secs
 * @todo improve layout: graph padding, x axis labels, etc...
 */

function calcChurnLabel( $pos, $step )
{
    $locale = eZLocale::instance();
    /// todo: look at time span, if too big use date, if small use time (both will not fit, unless we change axis type)
    // $out = $locale->formatShortDate( $pos );
    $out = $locale->formatShortTime( $pos );
    return $out;
}

$module = $Params['Module'];

// rely on system policy instead of creating our own, but allow also PolicyOmitList
$ini = eZINI::instance();
if ( !in_array( 'sysinfo/storagechurn', $ini->variable( 'RoleSettings', 'PolicyOmitList' ) ) )
{
    $user = eZUser::currentUser();
    $access = $user->hasAccessTo( 'setup', 'system_info' );
    if ( $access['accessWord'] != 'yes' )
    {
        return $module->handleError( eZError::KERNEL_ACCESS_DENIED, 'kernel' );
    }
}

// nb: this dir is calculated the same way as ezlog does
$logfile = eZSys::varDirectory() . '/' . $ini->variable( 'FileSettings', 'LogDir' ) . '/storage.log';
$cachedir = eZSys::cacheDirectory() . '/sysinfo';
$cachefile = $cachedir . '/storagechurn.jpg';

// *** Check if cached image file exists and is younger than storage log
$cachefound = false;
$clusterfile = eZClusterFileHandler::instance( $cachefile );
if ( $clusterfile->exists() )
{
    $logdate = filemtime( $logfile );
    $cachedate = $clusterfile->mtime();
    if ( $cachedate >= $logdate )
    {
        $cachefound = true;
        $clusterfile->fetch();
    }
}

if ( !$cachefound )
{
    // *** Parse storage.log file ***

    $scale = 60;
    $scalenames = array( 60 => 'minute', 60*60 => 'hour', 60*60*24 => 'day' );

    $ini = eZINI::instance();
    $file =  file( $logfile );
    $data = array();
    foreach ( $file as $line )
    {
        $time = strtotime( substr( $line, 2, 20 ) );
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
    $times = array_keys( $data );
    $min = $times[0];
    $max = end( $times );
    /*
       for ( $i = $min; $i <= $max; $i+= $scale )
       {
       $nodata[$i] = 0;
       }
       $data = array_merge( $nodata, $data );
    */

    // *** build graph ***

    $graphname = ezi18n( 'SysInfo', 'Files per '.$scalenames[$scale] );

    $graph = new ezcGraphBarChart();
    $locale = eZLocale::instance();
    $graph->title = "From " . $locale->formatShortDateTime( $min ) . " to " . $locale->formatShortDateTime( $max );
    $graph->xAxis->label = "From " . $locale->formatShortDateTime( $min ) . " to " . $locale->formatShortDateTime( $max );
    $graph->options->font->maxFontSize = 10;
    $graph->palette = new ezcGraphPaletteEzBlue();
    $graph->yAxis->label = $graphname;
    $graph->legend = false;
    // width of bar charts is not calculated correctly by DateAxis
    //$graph->xAxis = new ezcGraphChartElementDateAxis();
    //$graph->xAxis->interval = $scale;
    $graph->xAxis = new ezcGraphChartElementNumericAxis();
    $graph->xAxis->min = $min;
    $graph->xAxis->max = $max;
    $graph->xAxis->labelCallback = 'calcChurnLabel';
    $graph->data[$graphname] = new ezcGraphArrayDataSet( $data );
    $graph->driver = new ezcGraphGdDriver();
    $graph->driver->options->imageFormat = IMG_JPEG;
    // pick a font that is delivered along with ezp
    $graph->options->font = 'design/standard/fonts/arial.ttf';

    $outputdir = eZSys::rootDir() . '/' . $cachedir;
    $outputfile = eZSys::rootDir() . '/' . $cachefile;
    if ( !is_dir( $outputdir ) )
    {
        mkdir( $outputdir );
    }
    else
    {
        if ( is_file( $outputfile ) )
        {
            unlink( $outputfile );
        }
    }
    try
    {
        $errormsg = "";
        $graph->render( 600, 400, $outputfile );
        $clusterfile->fileStore( $cachefile );
    } catch( exception $e )
    {
        $errormsg = "Error while rendering graph: " . $e->getMessage();
        eZDebug::writeError( $errormsg );
    }
}

// *** output ***

require_once( "kernel/common/template.php" );
$tpl = templateInit();
$tpl->setVariable( 'title', 'Storage churn' );
$tpl->setVariable( 'graphsource', $cachefile );
$tpl->setVariable( 'errormsg', $errormsg );

$Result = array();
$Result['content'] = $tpl->fetch( "design:sysinfo/storagechurn.tpl" ); //var_dump($cacheFilesList);

$Result['left_menu'] = 'design:parts/sysinfo/menu.tpl';
$Result['path'] = array( array( 'url' => false,
                                'text' => ezi18n( 'SysInfo', 'Storage churn' ) ) );
?>