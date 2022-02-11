<?php
/**
 * A CLI script which gathers all sorts of system-info data, useful for auditing sites.
 * Should be executable even without activating the extension.
 * This means that code used for generating reports
 * - should use a custom loader for all ini settings coming from this extension itself
 * - should not use any templates coming from this extension itself (no custom lodaer is provided for now)
 * also this script uses a custom autoloader for php classes
 *
 * @author G. Giunta
 * @copyright (C) G. Giunta 2012-2022
 * @license Licensed under GNU General Public License v2.0. See file license.txt
 *
 * @todo add options to let users to select which tests to run when running status report (ALL by default)
 * @todo add option for output format: CSV, JSON, XML, ...
 */

require 'autoload.php';

// Inject our own autoloader after the std one, as this script is supposed to be
// executable even when extension has not been activated
spl_autoload_register( array( 'autoloadHelper', 'autoload' ) );

$cli = eZCLI::instance();

$script = eZScript::instance( array(
    'description' => 'Generate System Information Report',
    'use-session' => false,
    'use-modules' => true,
    'use-extensions' => true ) );
$script->startup();
$options = $script->getOptions(
    '[reports:]', //'[reports-extensions:]',
    '[action]',
    array(
        'reports' => 'List of reports to generate (separate multiple with commas)',
        'reports-extensions' => 'List of extensions containing reports (defaults to ggsysinfo)',
        'action' => "'list' available reports or 'generate' (default)"
    ) );
$script->initialize();

if( $options['reports'] == '' )
{
    $toGenerate = null;
}
else
{
    $toGenerate = explode( ',', str_replace( ';', ',', $options['reports'] ) );
}

if( count( $options['arguments'] ) == 0 || !in_array( $options['arguments'][0], array( 'list', 'generate' ) ) )
{
    $action = 'list';
}
else
{
    $action = $options['arguments'][0];
}

//$reportsExtensions = explode( ',', $options['reports-extensions'] );
$reportsExtensions = array();
$reportsExtensions[] = 'ggsysinfo';

switch( $action )
{
    case 'list':
        $cli->output( 'Available reports:' );
        // get All checkers
        $reports = getReports( $reportsExtensions );
        foreach( $reports as $report )
        {
            $description = $report->getDescription();
            $cli->output( '  ' . $description['tag'] . ': ' . $description['title'] );
        }
        $cli->output( "Run: php extension/ggsysinfo/bin/php/report.php generate --reports=<name,name,...> to generate reports" );
        break;

    case 'generate':
    default:
        // Report structure: [ { title: "xxx", data: [] } ]
        $report = array();

        // get filtered list of checkers
        $reportGenerators = getReports( $reportsExtensions, $toGenerate );
        foreach( $reportGenerators as $reportGenerator )
        {
            $description = $reportGenerator->getDescription();

            $cli->output( $description['executingString'] );
            $data = $reportGenerator->getReport();

            $report[] = array(
                'title' => $description['title' ],
                'data' => $data,
                'format' =>  $description['format'],
            );
        }

        $cli->output( 'Done!' );
        if ( count( $reportGenerators ) < count( $toGenerate ) )
        {
            $missing = array_diff( $toGenerate, array_keys( $reportGenerators ) );
            $cli->output( 'NB: the following reports are not available: ' . implode( ', ', $missing ) );
        }
        $cli->output();

        $reportFormatter = new reportGenerator();
        $cli->output( $reportFormatter->getCSV( $report ) );
}

$script->shutdown();

/// @todo refactor put in a class
function getReports( $reportsExtensions, $toGenerate = null )
{
    $available = array();
    $declared = ezInactiveExtensionLoader::getIniValue( 'sysinfo.ini', 'ReportsSettings', 'ReportGenerators', ezInactiveExtensionLoader::TYPE_ARRAY );
    foreach( $declared as $class )
    {
        // wouldn't we like a DIC here?
        $generator = new $class();
        $desc = $generator->getDescription();
        $available[$desc['tag']] = $generator;
    }

    if( is_array( $toGenerate ) )
    {
        return array_intersect_key( $available, array_flip( $toGenerate ) );
    }
    return $available;
}

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
