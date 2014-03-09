<?php
/**
 * Class used to help managing in a single place all module/view info
 *
 * @author G. Giunta
 * @copyright (C) G. Giunta 2010-2012
 * @license Licensed under GNU General Public License v2.0. See file license.txt
 */

class sysinfoModule{

    static $initialized = false;

    /**
    * Structure used by eZP module view definitions, augmented somewhat by:
    * - title / name ???
    * - description
    * - disabled (this one is calculated by the initialize() function
    * - hidden (for left menu)
    */
    static $view_groups = array(

        'Index' => array(
            'index' => array(
                'script' => 'genericview.php',
                'default_navigation_part' => 'ezsysinfonavigationpart',
                'name' => '',
                'title' => 'System Information' ),
        ),

        'PHP' => array(
            'php' => array(
                //'functions' => array( 'system_info' ), - we check in the module itself
                'script' => 'genericview.php',
                'default_navigation_part' => 'ezsysinfonavigationpart',
                'name' => 'phpinfo()',
                'description' => 'The standard phpinfo() information page detailing php settings' ),

            'acceleratorplus' => array(
                //'functions' => array( 'system_info' ), - we check in the module itself
                'script' => 'genericview.php',
                'default_navigation_part' => 'ezsysinfonavigationpart',
                'name' => 'OPcache/Zend Acc Plus',
                'description' => 'The control panel for the OPcache / Zend Accelerator Plus opcode cache',
                'disabled' => true ),

            'apc' => array(
                //'functions' => array( 'system_info' ), - we check in the module itself
                'script' => 'genericview.php',
                'default_navigation_part' => 'ezsysinfonavigationpart',
                'name' => 'APC',
                'description' => 'The control panel for the APC opcode cache',
                'disabled' => true ),

            'eaccelerator' => array(
                //'functions' => array( 'system_info' ), - we check in the module itself
                'script' => 'genericview.php',
                'default_navigation_part' => 'ezsysinfonavigationpart',
                'name' => 'eAccelerator',
                'description' => 'The control panel for the eAccelerator opcode cache',
                'disabled' => true ),

            'wincache' => array(
                //'functions' => array( 'system_info' ), - we check in the module itself
                'script' => 'genericview.php',
                'default_navigation_part' => 'ezsysinfonavigationpart',
                'name' => 'WinCache',
                'description' => 'The control panel for the WinCache opcode cache',
                'disabled' => true ),

            'xcache' => array(
                //'functions' => array( 'system_info' ), - we check in the module itself
                'script' => 'genericview.php',
                'default_navigation_part' => 'ezsysinfonavigationpart',
                'name' => 'XCache',
                'description' => 'The control panel for the XCache opcode cache',
                'disabled' => true ),

            'mysqli' => array(
                //'functions' => array( 'system_info' ), - we check in the module itself
                'script' => 'genericview.php',
                'default_navigation_part' => 'ezsysinfonavigationpart',
                'name' => 'MySQL',
                'description' => 'A control panel for MySQL connection stats',
                'disabled' => true ),
        ),
        'eZPublish' => array(

            'systemstatus' => array(
                //'functions' => array( 'system_info' ), - we check in the module itself
                'script' => 'genericview.php',
                'default_navigation_part' => 'ezsysinfonavigationpart',
                'params' => array( 'output_format' ),
                'name' => 'System status',
                'description' => 'Executes tests to verify the functioning of various parts of the system (e.g. connection to the database or to the eZFind indexing server)' ),

            'systemcheck' => array(
                //'functions' => array( 'system_info' ), - we check in the module itself
                'script' => 'genericview.php',
                'default_navigation_part' => 'ezsysinfonavigationpart',
                //'params' => array( 'output_format' ),
                'name' => 'System check',
                'description' => 'Executes tests to verify that the environment can properly support eZ Publish (i.e. the tests normally run by the setup wizard)' ),

            'cachestats' => array(
                //'functions' => array( 'system_info' ), - we check in the module itself
                'script' => 'genericview.php',
                'default_navigation_part' => 'ezsysinfonavigationpart',
                'unordered_params' => array( 'view' => 'viewmode' ),
                'name' => 'Cache stats',
                'description' => 'Number of files and total size per every cache type',
                'disabled' => true ),

            'cachesearch' => array(
                //'functions' => array( 'system_info' ), - we check in the module itself
                'script' => 'genericview.php',
                'default_navigation_part' => 'ezsysinfonavigationpart',
                'name' => 'Cache search',
                'description' => 'Allows to search for a given string or regexp in the different cache files',
                'disabled' => true ),

            'storagestats' => array(
                //'functions' => array( 'system_info' ), - we check in the module itself
                'script' => 'genericview.php',
                'default_navigation_part' => 'ezsysinfonavigationpart',
                'unordered_params' => array( 'view' => 'viewmode' ),
                'name' => 'Storage stats',
                'description' => 'Number of files and total size for binary contents',
                'disabled' => true ),

            'storagechurn' => array(
                //'functions' => array( 'system_info' ), - we check in the module itself
                'script' => 'genericview.php',
                'default_navigation_part' => 'ezsysinfonavigationpart',
                'unordered_params' => array( 'view' => 'viewmode' ),
                'name' => 'Storage churn',
                'description' => 'Graph of number of files per minutes written to disk (including caches and binary contents)' ),

            'contentstats' => array(
                //'functions' => array( 'system_info' ), - we check in the module itself
                'script' => 'genericview.php',
                'default_navigation_part' => 'ezsysinfonavigationpart',
                'name' => 'Content stats',
                'description' => 'Number of content objects present, information collections, pending notification events, pending indexation events etc...' ),

            'contentchurn' => array(
                //'functions' => array( 'system_info' ), - we check in the module itself
                'script' => 'genericview.php',
                'default_navigation_part' => 'ezsysinfonavigationpart',
                'name' => 'Content churn',
                'description' => 'Graph of number of objects created per day' ),

            'logstats' => array(
                //'functions' => array( 'system_info' ), - we check in the module itself
                'script' => 'genericview.php',
                'default_navigation_part' => 'ezsysinfonavigationpart',
                'unordered_params' => array( 'view' => 'viewmode' ),
                'name' => 'Log Stats',
                'description' => 'Total size and last modification date of log files' ),

            /*'logsearch' => array(
                //'functions' => array( 'system_info' ), - we check in the module itself
                'script' => 'logsearch.php',
                'default_navigation_part' => 'ezsysinfonavigationpart',
                'name' => 'Log search' ),*/

            'logchurn' => array(
                //'functions' => array( 'system_info' ), - we check in the module itself
                'script' => 'genericview.php',
                'default_navigation_part' => 'ezsysinfonavigationpart',
                'unordered_params' => array( 'view' => 'viewmode' ),
                'name' => 'Log churn',
                'description' => 'Graph of events written per minute in the log files' ),

            'logview' => array(
                //'functions' => array( 'system_info' ), - we check in the module itself
                'script' => 'genericview.php',
                'default_navigation_part' => 'ezsysinfonavigationpart',
                'params' => array( 'logfile' ),
                'unordered_params' => array( 'view' => 'viewmode' ),
                'name' => 'Log view',
                'hidden' => true ),

        ),
        'Development' => array(

/*'extensiondetails'] = array(
   'script' => 'extensiondetails.php',
   'default_navigation_part' => 'ezsysinfonavigationpart',
   'params' => array( 'extensionname' ) );*/

        'modulelist' => array(
            //'functions' => array( 'system_info' ), - we check in the module itself
            'script' => 'genericview.php',
            'default_navigation_part' => 'ezsysinfonavigationpart',
            'params' => array( 'extensionname' ),
            'name' => 'Module list',
            'description' => 'List of all active modules' ),

/*'moduledetails'] = array(
   'script' => 'moduledetails.php',
   'default_navigation_part' => 'ezsysinfonavigationpart',
   'params' => array( 'modulename' ) );*/

        'viewlist' => array(
            //'functions' => array( 'system_info' ), - we check in the module itself
            'script' => 'genericview.php',
            'default_navigation_part' => 'ezsysinfonavigationpart',
            'params' => array( 'modulename' ),
            'name' => 'View list',
            'description' => 'List of all module views' ),

/*'viewdetails'] = array(
   'script' => 'viewdetails.php',
   'default_navigation_part' => 'ezsysinfonavigationpart',
   'params' => array( 'modulename', 'viewname' ) );*/

        'policylist' => array(
            //'functions' => array( 'system_info' ), - we check in the module itself
            'script' => 'genericview.php',
            'default_navigation_part' => 'ezsysinfonavigationpart',
            'params' => array( 'modulename' ),
            'name' => 'Policy functions list',
            'description' => 'List of all module access policies' ),

        'operationlist' => array(
            //'functions' => array( 'system_info' ), - we check in the module itself
            'script' => 'genericview.php',
            'default_navigation_part' => 'ezsysinfonavigationpart',
            'params' => array( 'modulename' ),
            'name' => 'Operations list',
            'description' => 'List of all module operations' ),

        'fetchlist' => array(
            //'functions' => array( 'system_info' ), - we check in the module itself
            'script' => 'genericview.php',
            'default_navigation_part' => 'ezsysinfonavigationpart',
            'params' => array( 'modulename' ),
            'name' => 'Fetch functions list',
            'description' => 'List of all module fetch functions' ),

        'operatorlist' => array(
           //'functions' => array( 'system_info' ), - we check in the module itself
           'script' => 'genericview.php',
           'default_navigation_part' => 'ezsysinfonavigationpart',
           'params' => array(  'extensionname' ),
            'name' => 'Template Operators list',
            'description' => 'List of all template operators' ),
        ),

        'QA' => array(

            'secinfo' => array(
                //'functions' => array( 'system_info' ), - we check in the module itself
                'script' => 'genericview.php',
                'default_navigation_part' => 'ezsysinfonavigationpart',
                'name' => 'Security checks',
                'description' => 'Executes tests to verify the proper configuration of the system for security-related aspects (taken from phpsecinfo)' ),

            'databaseqa' => array(
                //'functions' => array( 'system_info' ), - we check in the module itself
                'script' => 'genericview.php',
                'default_navigation_part' => 'ezsysinfonavigationpart',
                'name' => 'Database problems',
                'description' => 'Checks for common database misconfigurations (character set and storage engine of all tables)',
                'disabled' => true ),

            'inifilesqa' => array(
                //'functions' => array( 'system_info' ), - we check in the module itself
                'script' => 'genericview.php',
                'default_navigation_part' => 'ezsysinfonavigationpart',
                'name' => 'Ini files problems',
                'description' => 'Checks for all ini files found the correct naming conventions' ),

            'inisettingsqa' => array(
                //'functions' => array( 'system_info' ), - we check in the module itself
                'script' => 'genericview.php',
                'default_navigation_part' => 'ezsysinfonavigationpart',
                'name' => 'Ini values problems',
                'description' => 'Checks for all ini files found the correctness of syntax, presence of php opening comment tag and charset declaration' ),

            'phpfilesqa' => array(
                //'functions' => array( 'system_info' ), - we check in the module itself
                'script' => 'genericview.php',
                'unordered_params' => array( 'view' => 'viewmode' ),
                'default_navigation_part' => 'ezsysinfonavigationpart',
                'name' => 'Php files problems',
                'description' => 'Checks for all php files found the presence of opening and closing php tags, syntax validity' ),

            'tplfilesqa' => array(
                //'functions' => array( 'system_info' ), - we check in the module itself
                'script' => 'genericview.php',
                'unordered_params' => array( 'view' => 'viewmode' ),
                'default_navigation_part' => 'ezsysinfonavigationpart',
                'name' => 'Tpl files problems',
                'description' => 'Checks syntax validity for all tpl files found' ),
        ),

        'Reports' => array(

            'classesreport' => array(
                //'functions' => array( 'system_info' ), - we check in the module itself
                'script' => 'genericview.php',
                'default_navigation_part' => 'ezsysinfonavigationpart',
                'params' => array( ),
                'name' => 'Content classes',
                'title' => 'Content classes report',
                'description' => 'Definition of all content classes in a format friendly to backups' ),

            'inireport' => array(
                //'functions' => array( 'system_info' ), - we check in the module itself
                'script' => 'genericview.php',
                'default_navigation_part' => 'ezsysinfonavigationpart',
                'params' => array( 'siteaccess' ),
                'name' => 'Ini settings',
                'title' => 'Ini settings report',
                'description' => 'Definition of all Ini settings in a format friendly to backups' ),

            'policiesreport' => array(
                //'functions' => array( 'system_info' ), - we check in the module itself
                'script' => 'genericview.php',
                'default_navigation_part' => 'ezsysinfonavigationpart',
                'params' => array( ),
                'name' => 'Roles & Policies',
                'title' => 'Roles & Policies report',
                'description' => 'Definition of all Roles & Policies in a format friendly to backups' ),

        )
    );

    protected static function initialize( $force=false )
    {
        if ( self::$initialized && !$force )
        {
            return;
        }

        // starting with version 4.1, this is available in the Setup|System Info page
        if ( version_compare( '4.1', eZPublishSDK::version() ) <= 0 )
        {
             self::$view_groups['PHP']['php']['hidden'] = true;
        }

        $ini = eZINI::instance( 'file.ini' );
        $h = $ini->variable( 'ClusteringSettings', 'FileHandler' );
        if ( in_array( $h, array( 'ezfs', 'eZFSFileHandler', 'eZFS2FileHandler' ) ) )
        {
            self::$view_groups['eZPublish']['cachestats']['disabled'] = false;
            self::$view_groups['eZPublish']['cachesearch']['disabled'] = false;
            self::$view_groups['eZPublish']['storagestats']['disabled'] = false;
        }
        if ( in_array( $h, array( 'eZDFSFileHandler' ) ) )
        {
            self::$view_groups['eZPublish']['cachestats']['disabled'] = false;
            self::$view_groups['eZPublish']['storagestats']['disabled'] = false;
        }
        /*if ( isset( $GLOBALS['_PHPA'] ) )
        {
           self::$view_groups['PHP']['phpaccelerator'];
        }
        else if ( extension_loaded( 'Turck MMCache' ) )
        {
            $operatorValue = 'mmcache';
        }*/
        if ( extension_loaded( 'eAccelerator' ) )
        {
            self::$view_groups['PHP']['eaccelerator']['disabled'] = false;
        }
        if ( extension_loaded( 'apc' ) )
        {
             self::$view_groups['PHP']['apc']['disabled'] = false;
        }
        if ( function_exists( 'accelerator_get_status' ) || function_exists( 'opcache_get_status' ) )
        {
            self::$view_groups['PHP']['acceleratorplus']['disabled'] = false;
        }
        /*else if ( extension_loaded( 'Zend Performance Suite' ) )
        {
            $operatorValue = 'performancesuite';
        }*/
        if ( extension_loaded( 'xcache' ) )
        {
            self::$view_groups['PHP']['xcache']['disabled'] = false;
        }
        if ( extension_loaded( 'wincache' ) )
        {
             self::$view_groups['PHP']['wincache']['disabled'] = false;
        }

        $db = eZDB::instance();
        if ( $db->databaseName() == 'mysql' )
        {
            self::$view_groups['QA']['databaseqa']['disabled'] = false;

            /// @todo is this the correct way to check?
            if ( function_exists( 'mysqli_get_client_stats' ) )
            {
                self::$view_groups['PHP']['mysqli']['disabled'] = false;
            }
        }

        self::$initialized = true;
    }

    static function groupList()
    {
        self::initialize();
        return self::$view_groups;
    }

    static function viewList( $group='' )
    {
        self::initialize();
        $viewlist = array();
        if ( $group == '' )
        {
            foreach( self::$view_groups as $views )
            {
                $viewlist = array_merge( $viewlist, $views );
            }
        }
        else if ( isset( self::$view_groups[$group] ) )
        {
            $viewlist = self::$view_groups[$group];
        }
        return $viewlist;
    }

    // we use name if title is missing
    static function viewTitle( $viewname )
    {
        foreach( self::$view_groups as $views )
        {
            if ( array_key_exists( $viewname, $views ) )
            {
                return isset( $views[$viewname]['title'] ) ? $views[$viewname]['title'] : $views[$viewname]['name'];
            }
        }
        return 'title';
    }

    static function viewName( $viewname )
    {
        foreach( self::$view_groups as $views )
        {
            if ( array_key_exists( $viewname, $views ) )
            {
                return $views[$viewname]['name'];
            }
        }
        return 'title-for-path';
    }

    static function viewDescription( $viewname )
    {
        foreach( self::$view_groups as $views )
        {
            if ( array_key_exists( $viewname, $views ) )
            {
                return isset( $views[$viewname]['description'] ) ? $views[$viewname]['description'] : $views[$viewname];
            }
        }
        return '';
    }

    /// true if view is neither hidden nor disabled
    static function viewActive( $viewname )
    {
        foreach( self::$view_groups as $views )
        {
            if ( array_key_exists( $viewname, $views ) )
            {
                return !@$views[$viewname]['disabled'] && !@$views[$viewname]['hidden'];
            }
        }
        return false;
    }
}
?>