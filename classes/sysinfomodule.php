<?php
/**
 * Class used to help managing in a single place all module/view info
 *
 * @author G. Giunta
 * @version $Id: contentstats.php 2570 2008-11-25 11:35:44Z ezsystems $
 * @copyright (C) G. Giunta 2010
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
                "script" => "genericview.php",
                "default_navigation_part" => 'ezsysinfonavigationpart',
                'name' => '',
                'title' => 'System Information' ),
        ),

        'PHP' => array(
            'php' => array(
                //'functions' => array( 'system_info' ), - we check in the module itself
                "script" => "genericview.php",
                "default_navigation_part" => 'ezsysinfonavigationpart',
                'name' => 'phpinfo()',
                'description' => 'The standard phpinfo() information page detailing php settings' ),

            'apc' => array(
                //'functions' => array( 'system_info' ), - we check in the module itself
                "script" => "genericview.php",
                "default_navigation_part" => 'ezsysinfonavigationpart',
                'name' => 'APC',
                'description' => 'The control panel for the APC opcode cache',
                'disabled' => true ),

            'eaccelerator' => array(
                //'functions' => array( 'system_info' ), - we check in the module itself
                "script" => "genericview.php",
                "default_navigation_part" => 'ezsysinfonavigationpart',
                'name' => 'eAccelerator',
                'description' => 'The control panel for the eAccelerator opcode cache',
                'disabled' => true ),

            'xcache' => array(
                //'functions' => array( 'system_info' ), - we check in the module itself
                "script" => "genericview.php",
                "default_navigation_part" => 'ezsysinfonavigationpart',
                'name' => 'XCache',
                'description' => 'The control panel for the XCache opcode cache',
                'disabled' => true ),

            'wincache' => array(
                //'functions' => array( 'system_info' ), - we check in the module itself
                "script" => "genericview.php",
                "default_navigation_part" => 'ezsysinfonavigationpart',
                'name' => 'WinCache',
                'description' => 'The control panel for the WinCache opcode cache',
                'disabled' => true ),
        ),
        'eZPublish' => array(

            'systemstatus' => array(
                //'functions' => array( 'system_info' ), - we check in the module itself
                "script" => "genericview.php",
                "default_navigation_part" => 'ezsysinfonavigationpart',
                "params" => array( 'output_format' ),
                'name' => 'System status' ),

            'cachestats' => array(
                //'functions' => array( 'system_info' ), - we check in the module itself
                "script" => "genericview.php",
                "default_navigation_part" => 'ezsysinfonavigationpart',
                'name' => 'Cache stats',
                'disabled' => true ),

            'cachesearch' => array(
                //'functions' => array( 'system_info' ), - we check in the module itself
                "script" => "genericview.php",
                "default_navigation_part" => 'ezsysinfonavigationpart',
                'name' => 'Cache search',
                'disabled' => true ),


            'storagestats' => array(
                //'functions' => array( 'system_info' ), - we check in the module itself
                "script" => "genericview.php",
                "default_navigation_part" => 'ezsysinfonavigationpart',
                'name' => 'Storage stats',
                'disabled' => true ),

            'storagechurn' => array(
                //'functions' => array( 'system_info' ), - we check in the module itself
                "script" => "genericview.php",
                "default_navigation_part" => 'ezsysinfonavigationpart',
                'name' => 'Storage churn' ),

            'contentstats' => array(
                //'functions' => array( 'system_info' ), - we check in the module itself
                "script" => "genericview.php",
                "default_navigation_part" => 'ezsysinfonavigationpart',
                'name' => 'Content stats' ),

            'logstats' => array(
                //'functions' => array( 'system_info' ), - we check in the module itself
                "script" => "genericview.php",
                "default_navigation_part" => 'ezsysinfonavigationpart',
                'name' => 'Log Stats' ),

            /*'logsearch' => array(
                //'functions' => array( 'system_info' ), - we check in the module itself
                "script" => "logsearch.php",
                "default_navigation_part" => 'ezsysinfonavigationpart',
                'name' => 'Log search' ),*/

            'logchurn' => array(
                //'functions' => array( 'system_info' ), - we check in the module itself
                "script" => "genericview.php",
                "default_navigation_part" => 'ezsysinfonavigationpart',
                "unordered_params" => array(),
                'name' => 'Log churn' ),

            'logview' => array(
                //'functions' => array( 'system_info' ), - we check in the module itself
                "script" => "genericview.php",
                "default_navigation_part" => 'ezsysinfonavigationpart',
                "params" => array( 'logfile' ),
                'name' => 'Log view',
                'hidden' => true ),

        ),
        'Development' => array(

/*'extensiondetails'] = array(
   "script" => "extensiondetails.php",
   "default_navigation_part" => 'ezsysinfonavigationpart',
   "params" => array( 'extensionname' ) );*/

        'modulelist' => array(
            //'functions' => array( 'system_info' ), - we check in the module itself
            "script" => "genericview.php",
            "default_navigation_part" => 'ezsysinfonavigationpart',
            "params" => array( 'extensionname' ),
            'name' => 'Module list' ),

/*'moduledetails'] = array(
   "script" => "moduledetails.php",
   "default_navigation_part" => 'ezsysinfonavigationpart',
   "params" => array( 'modulename' ) );*/

        'viewlist' => array(
            //'functions' => array( 'system_info' ), - we check in the module itself
            "script" => "genericview.php",
            "default_navigation_part" => 'ezsysinfonavigationpart',
            "params" => array( 'modulename' ),
            'name' => 'View list' ),

/*'viewdetails'] = array(
   "script" => "viewdetails.php",
   "default_navigation_part" => 'ezsysinfonavigationpart',
   "params" => array( 'modulename', 'viewname' ) );*/

        'policylist' => array(
            //'functions' => array( 'system_info' ), - we check in the module itself
            "script" => "genericview.php",
            "default_navigation_part" => 'ezsysinfonavigationpart',
            "params" => array( 'modulename' ),
            'name' => 'Policy functions list' ),

        'operationlist' => array(
            //'functions' => array( 'system_info' ), - we check in the module itself
            "script" => "genericview.php",
            "default_navigation_part" => 'ezsysinfonavigationpart',
            "params" => array( 'modulename' ),
            'name' => 'Operations list' ),

        'fetchlist' => array(
            //'functions' => array( 'system_info' ), - we check in the module itself
            "script" => "genericview.php",
            "default_navigation_part" => 'ezsysinfonavigationpart',
            "params" => array( 'modulename' ),
            'name' => 'Fetch functions list' ),

/*'operatorlist'] = array(
   //'functions' => array( 'system_info' ), - we check in the module itself
   "script" => "operatorlist.php",
   "default_navigation_part" => 'ezsysinfonavigationpart',
   "params" => array( ) );*/

        ),
        'QA' => array(

            'inifilesqa' => array(
                //'functions' => array( 'system_info' ), - we check in the module itself
                "script" => "genericview.php",
                "default_navigation_part" => 'ezsysinfonavigationpart',
                'name' => 'Ini files problems' ),

            'inisettingsqa' => array(
                //'functions' => array( 'system_info' ), - we check in the module itself
                "script" => "genericview.php",
                "default_navigation_part" => 'ezsysinfonavigationpart',
                'name' => 'Ini values problems' ),

            'phpfilesqa' => array(
                //'functions' => array( 'system_info' ), - we check in the module itself
                "script" => "genericview.php",
                "default_navigation_part" => 'ezsysinfonavigationpart',
                'name' => 'Php files problems' ),

        ),
        'Reports' => array(

            'classesreport' => array(
                //'functions' => array( 'system_info' ), - we check in the module itself
                "script" => "genericview.php",
                "default_navigation_part" => 'ezsysinfonavigationpart',
                "params" => array( ),
                'name' => 'Content classes',
                'title' => 'Content classes report' )

        )
    );

    protected static function initialize( $force=false )
    {
        if ( self::$initialized && !$force )
        {
            return;
        }

        $ini = eZINI::instance( 'file.ini' );
        $h = $ini->variable( 'ClusteringSettings', 'FileHandler' );
        if ( in_array( $h, array( 'ezfs', 'eZFSFileHandler', 'eZFS2FileHandler' ) ) )
        {
            self::$view_groups['eZPublish']['cachestats']['disabled'] = false;
            self::$view_groups['eZPublish']['cachesearch']['disabled'] = false;
            self::$view_groups['eZPublish']['storagestats']['disabled'] = false;
        }
        /*if ( isset( $GLOBALS['_PHPA'] ) )
        {
           self::$view_groups['PHP']['phpaccelerator'];
        }
        else if ( extension_loaded( "Turck MMCache" ) )
        {
            $operatorValue = 'mmcache';
        }*/
        else if ( extension_loaded( "eAccelerator" ) )
        {
            self::$view_groups['PHP']['eaccelerator']['disabled'] = false;
        }
        else if ( extension_loaded( "apc" ) )
        {
             self::$view_groups['PHP']['apc']['disabled'] = false;
        }
        /*else if ( extension_loaded( "Zend Performance Suite" ) )
        {
            $operatorValue = 'performancesuite';
        }*/
        else if ( extension_loaded( "xcache" ) )
        {
            self::$view_groups['PHP']['xcache']['disabled'] = false;
        }
        else if ( extension_loaded( "wincache" ) )
        {
             self::$view_groups['PHP']['wincache']['disabled'] = false;
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

    /// @todo use name if title is missing
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