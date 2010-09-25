<?php
/**
 * Class used to help managing in a single place all module/view info
 *
 * @author G. Giunta
 * @version $Id: contentstats.php 2570 2008-11-25 11:35:44Z ezsystems $
 * @copyright (C) G. Giunta 2010
 * @license Licensed under GNU General Public License v2.0. See file license.txt
 *
 * @todo add an enabled/disabled (calculated) status for thos views that need it
 * @todo add view title here, as well as view name for path
 */

class sysinfoModule{

    static $view_groups = array(

        'Index' => array(
            'index' => array(
                "script" => "genericview.php",
                "default_navigation_part" => 'ezsysinfonavigationpart',
                "params" => array( ),
                'name' => '',
                'title' => 'System Information' ),
        ),

        'PHP' => array(
            'php' => array(
                //'functions' => array( 'system_info' ), - we check in the module itself
                "script" => "genericview.php",
                "default_navigation_part" => 'ezsysinfonavigationpart',
                "params" => array( ),
                'name' => 'phpinfo()',
                'description' => 'The standard phpinfo() information page detailing php settings' ),

            'apc' => array(
                //'functions' => array( 'system_info' ), - we check in the module itself
                "script" => "genericview.php",
                "default_navigation_part" => 'ezsysinfonavigationpart',
                "params" => array( ),
                'name' => 'APC',
                'description' => 'The control panel for the APC opcode cache' ),

            'eaccelerator' => array(
                //'functions' => array( 'system_info' ), - we check in the module itself
                "script" => "genericview.php",
                "default_navigation_part" => 'ezsysinfonavigationpart',
                "params" => array( ),
                'name' => 'eAccelerator',
                'description' => 'The control panel for the eAccelerator opcode cache' ),

            'xcache' => array(
                //'functions' => array( 'system_info' ), - we check in the module itself
                "script" => "genericview.php",
                "default_navigation_part" => 'ezsysinfonavigationpart',
                "params" => array( ),
                'name' => 'XCache',
                'description' => 'The control panel for the XCache opcode cache' ),

            'wincache' => array(
                //'functions' => array( 'system_info' ), - we check in the module itself
                "script" => "genericview.php",
                "default_navigation_part" => 'ezsysinfonavigationpart',
                "params" => array( ),
                'name' => 'WinCache',
                'description' => 'The control panel for the WinCache opcode cache' ),
        ),
        'eZPublish' => array(

            'systemstatus' => array(
                //'functions' => array( 'system_info' ), - we check in the module itself
                "script" => "systemstatus.php",
                "default_navigation_part" => 'ezsysinfonavigationpart',
                "params" => array( 'output_format' ),
                'name' => 'System status' ),

            'cachestats' => array(
                //'functions' => array( 'system_info' ), - we check in the module itself
                "script" => "genericview.php",
                "default_navigation_part" => 'ezsysinfonavigationpart',
                "params" => array( ),
                'name' => 'Cache stats' ),

            'cachesearch' => array(
                //'functions' => array( 'system_info' ), - we check in the module itself
                "script" => "cachesearch.php",
                "default_navigation_part" => 'ezsysinfonavigationpart',
                "params" => array( ),
                'name' => 'Cache search' ),


            'storagestats' => array(
                //'functions' => array( 'system_info' ), - we check in the module itself
                "script" => "genericview.php",
                "default_navigation_part" => 'ezsysinfonavigationpart',
                "params" => array( ),
                'name' => 'Storage stats' ),

            'storagechurn' => array(
                //'functions' => array( 'system_info' ), - we check in the module itself
                "script" => "genericview.php",
                "default_navigation_part" => 'ezsysinfonavigationpart',
                "params" => array( ),
                "unordered_params" => array(),
                'name' => 'Storage churn' ),

            'contentstats' => array(
                //'functions' => array( 'system_info' ), - we check in the module itself
                "script" => "genericview.php",
                "default_navigation_part" => 'ezsysinfonavigationpart',
                "params" => array( ),
                'name' => 'Content stats' ),

            'logstats' => array(
                //'functions' => array( 'system_info' ), - we check in the module itself
                "script" => "genericview.php",
                "default_navigation_part" => 'ezsysinfonavigationpart',
                "params" => array( ),
                'name' => 'Log Stats' ),

            'logsearch' => array(
                //'functions' => array( 'system_info' ), - we check in the module itself
                "script" => "logsearch.php",
                "default_navigation_part" => 'ezsysinfonavigationpart',
                "params" => array( ),
                'name' => 'Log search' ),

            'logchurn' => array(
                //'functions' => array( 'system_info' ), - we check in the module itself
                "script" => "genericview.php",
                "default_navigation_part" => 'ezsysinfonavigationpart',
                "params" => array( ),
                "unordered_params" => array(),
                'name' => 'Log churn' ),

            'logview' => array(
                //'functions' => array( 'system_info' ), - we check in the module itself
                "script" => "logview.php",
                "default_navigation_part" => 'ezsysinfonavigationpart',
                "params" => array( 'logfile' ),
                "unordered_params" => array(),
                'name' => 'Log view' ),

        ),
        'Development' => array(

/*'extensiondetails'] = array(
   "script" => "extensiondetails.php",
   "default_navigation_part" => 'ezsysinfonavigationpart',
   "params" => array( 'extensionname' ) );*/

        'modulelist' => array(
            //'functions' => array( 'system_info' ), - we check in the module itself
            "script" => "modulelist.php",
            "default_navigation_part" => 'ezsysinfonavigationpart',
            "params" => array( 'extensionname' ),
            'name' => 'Module list' ),

/*'moduledetails'] = array(
   "script" => "moduledetails.php",
   "default_navigation_part" => 'ezsysinfonavigationpart',
   "params" => array( 'modulename' ) );*/

        'viewlist' => array(
            //'functions' => array( 'system_info' ), - we check in the module itself
            "script" => "viewlist.php",
            "default_navigation_part" => 'ezsysinfonavigationpart',
            "params" => array( 'modulename' ),
            'name' => 'View list' ),

/*'viewdetails'] = array(
   "script" => "viewdetails.php",
   "default_navigation_part" => 'ezsysinfonavigationpart',
   "params" => array( 'modulename', 'viewname' ) );*/

        'policylist' => array(
            //'functions' => array( 'system_info' ), - we check in the module itself
            "script" => "policylist.php",
            "default_navigation_part" => 'ezsysinfonavigationpart',
            "params" => array( 'modulename' ),
            'name' => 'Policy functions list' ),

        'fetchlist' => array(
            //'functions' => array( 'system_info' ), - we check in the module itself
            "script" => "fetchlist.php",
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
                "params" => array( ),
                'name' => 'Ini files problems' ),

            /*'inisettingsqa' => array(
                //'functions' => array( 'system_info' ), - we check in the module itself
                "script" => "inisettingsqa.php",
                "default_navigation_part" => 'ezsysinfonavigationpart',
                "params" => array( ),
                'name' => '' )*/

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

    static function groupList()
    {
        return array_keys( self::$view_groups );
    }

    static function viewList( $group='' )
    {
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
}
?>