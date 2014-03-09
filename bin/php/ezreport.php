<?php
/**
 * A CLI script which gathers all sorts of system-info data, useful fr auditing sites.
 * Should be executable even whithout activating the extension:
 * - not using any template or custom ini
 * - using a custom autoloader
 *
 * @author G. Giunta
 * @copyright (C) G. Giunta 2012-2014
 * @license Licensed under GNU General Public License v2.0. See file license.txt
 *
 * @todo add options to let users to select which tests to run (ALL by default)
 * @todo add option for output format: CSV, XML, ...
 */

require 'autoload.php';

// Inject our own autoloader after the std one, as this script is supposed to be
// executable even when extension has not been activated
spl_autoload_register( array( 'autoloadHelper', 'autoload' ) );

$cli = eZCLI::instance();

$script = eZScript::instance( array( 'description' => ( "Generate System Information Report" ),
    'use-session' => false,
    'use-modules' => true,
    'use-extensions' => true ) );
$script->startup();
$options = $script->getOptions(
    '',
    '',
    array( ) );
$script->initialize();

//$updates = $options['updates'] ? $options['updates'] : false;
/*
   $amount = $options['amount'] ? $options['amount'] : false;

   if ( $siteAccess )
   {
   $cli = eZCLI::instance();
   if ( in_array( $siteAccess, eZINI::instance()->variable( 'SiteAccessSettings', 'AvailableSiteAccessList' ) ) )
   {
   $cli->output( "Using siteaccess $siteAccess" );
   }
   else
   {
   $cli->notice( "Siteaccess $siteAccess does not exist, using default siteaccess" );
   }
   }
*/

// Report structure: [ { title: "xxx", data: [] } ]
$report = array();

$cli->output( 'Executing system status checks...' );
$data = sysInfoTools::runtests();
$report[] = array(
    'title' => 'System status checks (1=OK, 0=KO, X=NA)',
    'data' => $data,
    'byrow' => true
);

$cli->output( 'Executing setup wizards checks...' );
$data = systemChecker::checkSetupRequirements();
$report[] = array(
    'title' => 'Setup wizard checks',
    'data' => array_combine( array_keys( $data ), array_fill( 0, count( $data ), 'warning' ) ),
    'byrow' => true
);

$cli->output( 'Gathering content stats...' );
$report[] = array(
    'title' => 'Content stats',
    'data' => contentStatsGatherer::gather(),
    'byrow' => true
);

$cli->output( 'Done!' );
$cli->output();

$cli->output( reportGenerator::getCSV( $report ) );

$script->shutdown();

/**
* manages autoloading for classes contained within this extension
*/
class autoloadHelper
{
    protected static $ezpClasses = null;

    public static function autoload( $className )
    {
        if ( !is_array( self::$ezpClasses ) )
        {
            self::initializeAutoload();
        }
        if ( isset( self::$ezpClasses[$className] ) )
        {
            require( self::$ezpClasses[$className] );
        }
    }

    protected static function initializeAutoload()
    {
        $autoloadOptions = new ezpAutoloadGeneratorOptions();

        $autoloadOptions->basePath = 'extension/ggsysinfo';

        $autoloadOptions->searchKernelFiles = false;
        $autoloadOptions->searchKernelOverride = false;
        $autoloadOptions->searchExtensionFiles = true;
        $autoloadOptions->searchTestFiles = false;
        $autoloadOptions->writeFiles = false;
        $autoloadOptions->displayProgress = false;

        $autoloadGenerator = new eZAutoloadGenerator( $autoloadOptions );
        // We have to jump through hoops to get eZAutoloadGenerator give us back an array
        $autoloadGenerator->setOutputCallback( array( 'autoloadHelper', 'autoloadCallback' ) );

        try
        {
            $autoloadGenerator->buildAutoloadArrays();
            $autoloadGenerator->printAutoloadArray();
        }
        catch ( Exception $e )
        {
            echo $e->getMessage() . "\n";
        }
    }

    /**
    * Used as callback for eZAutoloadGenerator
    */
    public static function autoloadCallback( $php, $label )
    {
        // callback is called many times with info messages, only use the good one
        if ( strpos( $php, '<?php' ) !== 0 )
        {
            return;
        }
        $php = str_replace( array( '<?php', '?>', ), '', $php );
        self::$ezpClasses = eval( $php );
        // fix path to be proper relative to eZ root
        foreach ( self::$ezpClasses as $key => $val )
        {
            self::$ezpClasses[$key] = 'extension/ggsysinfo/' . $val;
        }
    }
}




$db = eZDB::instance();

//general limitations
//$maxDayLimit = 100000;
$maxDayLimit = 40000;
$minDate = "2005-01-01";
$minDateTS = strtotime( $minDate );
$offset = 0;
$limit = 100000;
$scope = array( 'binaryfile', 'image', 'mediafile' );

//get count of updates per day
$cli->output();
$cli->output( 'Calculating rate of updates on content objects...' );
$max = $db->arrayQuery( "select max(modified) as max from ezcontentobject_version" );
$min = $db->arrayQuery( "select min(modified) as min from ezcontentobject_version where modified>$minDateTS" );
$startDay = date( "Y-m-d", $min[0]['min'] );
$endDay   = date( "Y-m-d", $max[0]['max'] );
$cli->output( "First day of modification: $startDay" );
$cli->output( "Last day of modification:  $endDay" );

$updates = array();
$currentDay = $startDay;
while( $currentDay != $endDay )
{
    //$cli->output( $currentDay );
    //echo date( "n", strtotime( $currentDay ) );
    $currentDayBeginTS = mktime( "00", "00", "00", date( "n", strtotime( $currentDay ) ), date( "j", strtotime( $currentDay ) ), date( "y", strtotime( $currentDay ) ) );
    //$cli->output( date( "Y-m-d h:i:s a", $currentDayBeginTS ) );

    $currentDayEndTS = mktime( "23", "59", "59", date( "n", strtotime( $currentDay ) ), date( "j", strtotime( $currentDay ) ), date( "y", strtotime( $currentDay ) ) );
    //$cli->output( date( "Y-m-d h:i:s a", $currentDayEndTS ) );

    $result = $db->arrayQuery( "select count(*) as count from ezcontentobject_version where modified>$currentDayBeginTS and modified<$currentDayEndTS;" );
    $count = $result[0]['count'];
    if( ( $count != 0 ) && ( $count < $maxDayLimit ) )
    {
        $cli->output( date( "Y-m-d", $currentDayBeginTS ) . " " . $count  . " updates" );
        $updates[]=$count;
    }

    $currentDay = date( "Y-m-d", strtotime( date( "Y-m-d", $currentDayBeginTS ) . " +1 day" ) );
    //$cli->output( $currentDay );
}

$average = array_sum( $updates )/count($updates);

$cli->output();
$cli->output( "Max updates/day:     ".max( $updates ) );
$cli->output( "Average updates/day: ".$average );

//get languages
$cli->output();
$cli->output( 'Calculating count of used languages...' );
$availableTranslations = eZContentLanguage::fetchList();
$cli->output( "Total amount of languages: " . count( $availableTranslations ) );

//get siteaccesses
$cli->output();
$cli->output( 'Calculating count of used siteaccesses...' );
$ini = eZINI::instance();
$siteAccessList = $ini->variable( 'SiteAccessSettings', 'AvailableSiteAccessList' );
$cli->output( "Total amount of siteaccesses: " . count( $siteAccessList ) );

//get storage and cache files
$cli->output();
$cli->output( 'Calculating count of binary and cache files...' );
$fileHandler = eZClusterFileHandler::instance();
if( $fileHandler instanceof eZDFSFileHandler )
{
    //initialize DFS DB connection
    $ini = eZINI::instance( 'file.ini' );
    $dbParameters = array();
    $dbParameters['server']   = $ini->variable( 'eZDFSClusteringSettings' , 'DBHost' );
    $dbParameters['port']     = $ini->variable( 'eZDFSClusteringSettings' , 'DBPort' );
    $dbParameters['user']     = $ini->variable( 'eZDFSClusteringSettings' , 'DBUser' );
    $dbParameters['password'] = $ini->variable( 'eZDFSClusteringSettings' , 'DBPassword' );
    $dbParameters['database'] = $ini->variable( 'eZDFSClusteringSettings' , 'DBName' );
    $dbParameters['socket']   = $ini->variable( 'eZDFSClusteringSettings' , 'DBSocket' );
    //$dbParameters['charset']  = $ini->variable( 'eZDFSClusteringSettings' , 'DBCharset' );
    $db = eZDB::instance( false, $dbParameters, true );

    //get DFS binary files
    $sql =  "SELECT count(*) AS count FROM ezdfsfile WHERE scope IN ('".implode("', '", $scope )."')";
    $dbResult = $db->arrayQuery( $sql );
    $count = $dbResult[0]['count'];

    $cli->output();
    $cli->output( "Total amount of binary files on DFS: $count" );
    //calculate size in subfunction and offset
    $size = formatBytes( getTotalSizeOnDFS( $db, $limit, $count, $scope ) );
    $cli->output( "Total size of binary files on DFS: $size" );

    //get DFS cache files
    $sql =  "SELECT count(*) AS count FROM ezdfsfile WHERE scope NOT IN ('".implode("', '", $scope )."')";
    $dbResult = $db->arrayQuery( $sql );
    $count = $dbResult[0]['count'];

    $cli->output();
    $cli->output( "Total amount of cache files on DFS: $count" );
    $size = formatBytes( getTotalSizeOnDFS( $db, $limit, $count, $scope ) );
    $cli->output( "Total size of cache files on DFS: $size" );
}
else
{
    $storageDir = eZSys::storageDirectory();
    $cacheDir   = eZSys::cacheDirectory();

    //get local binary files
    $output1 = array();
    $output2 = array();
    exec( "find $storageDir", $output1 );
    exec( "du -hs $storageDir", $output2 );
    $cli->output();
    $cli->output( "Total amount of local binary files: " . count( $output1 ) );
    $cli->output( "Total size of local binary files: " . $output2[0] );

    //get local cache files
    $output = array();
    $output2 = array();
    exec( "find $cacheDir", $output1 );
    exec( "du -hs $cacheDir", $output2 );
    $cli->output();
    $cli->output( "Total amount of local cache files: " . count( $output1 ) );
    $cli->output( "Total size of local cache files: " . $output2[0] );
}

function getTotalSizeOnDFS( $db, $limit, $count, $scope )
{
    global $cli;
    $totalSize = 0;
    for( $offset = 0; $offset<=$count; $offset = $offset+$limit )
    {
        $sql =  "SELECT SUM(size) AS size FROM ( SELECT size FROM ezdfsfile WHERE scope IN ('".implode("', '", $scope )."') LIMIT $limit OFFSET $offset ) q";
        $result = $db->arrayQuery( $sql );
        $size = $result[0]['size'];
        $totalSize = $totalSize+$size;
        //display progress status
        if( $offset > 0 )
        {
            $percent = number_format( ( $offset * 100.0 ) / ( $count ), 2 );
            $cli->output( " " . $percent . "% " );
        }
    }
    return $totalSize;
}

?>