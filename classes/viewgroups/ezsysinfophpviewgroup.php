<?php
/**
 * @author G. Giunta
 * @copyright (C) G. Giunta 2014-2022
 * @license Licensed under GNU General Public License v2.0. See file license.txt
 */

class ezSysinfoPHPViewGroup extends ezSysinfoBaseViewGroup implements ezSysinfoViewgroup
{
    static $view_groups = array(
        'php' => array(
            //'functions' => array( 'system_info' ), - we check in the module itself
            'script' => 'genericview.php',
            'default_navigation_part' => 'ezsysinfonavigationpart',
            'name' => 'phpinfo()',
            'description' => 'The standard phpinfo() information page detailing php settings',
            'cluster_mode' => 'clustermasterview.php',
        ),

        'acceleratorplus' => array(
            //'functions' => array( 'system_info' ), - we check in the module itself
            'script' => 'genericview.php',
            'default_navigation_part' => 'ezsysinfonavigationpart',
            'name' => 'OPcache/Zend Acc Plus',
            'description' => 'The control panel for the OPcache / Zend Accelerator Plus opcode cache',
            'disabled' => true,
            'cluster_mode' => 'clustermasterview.php',
        ),

        'apc' => array(
            //'functions' => array( 'system_info' ), - we check in the module itself
            'script' => 'genericview.php',
            'default_navigation_part' => 'ezsysinfonavigationpart',
            'name' => 'APC',
            'description' => 'The control panel for the APC opcode cache',
            'disabled' => true,
            'cluster_mode' => 'clustermasterview.php',
        ),

        'eaccelerator' => array(
            //'functions' => array( 'system_info' ), - we check in the module itself
            'script' => 'genericview.php',
            'default_navigation_part' => 'ezsysinfonavigationpart',
            'name' => 'eAccelerator',
            'description' => 'The control panel for the eAccelerator opcode cache',
            'disabled' => true,
            'cluster_mode' => 'clustermasterview.php',
        ),

        'wincache' => array(
            //'functions' => array( 'system_info' ), - we check in the module itself
            'script' => 'genericview.php',
            'default_navigation_part' => 'ezsysinfonavigationpart',
            'name' => 'WinCache',
            'description' => 'The control panel for the WinCache opcode cache',
            'disabled' => true,
            'cluster_mode' => 'clustermasterview.php',
        ),

        'xcache' => array(
            //'functions' => array( 'system_info' ), - we check in the module itself
            'script' => 'genericview.php',
            'default_navigation_part' => 'ezsysinfonavigationpart',
            'name' => 'XCache',
            'description' => 'The control panel for the XCache opcode cache',
            'disabled' => true,
            'cluster_mode' => 'clustermasterview.php',
        ),

        'mysqli' => array(
            //'functions' => array( 'system_info' ), - we check in the module itself
            'script' => 'genericview.php',
            'default_navigation_part' => 'ezsysinfonavigationpart',
            'name' => 'MySQL',
            'description' => 'A control panel for MySQL connection stats',
            'disabled' => true,
            'cluster_mode' => 'clustermasterview.php',
        ),

        'realpath' => array(
            //'functions' => array( 'system_info' ), - we check in the module itself
            'script' => 'genericview.php',
            'default_navigation_part' => 'ezsysinfonavigationpart',
            'name' => 'Realpath cache',
            'description' => 'Realpath cache information (you probably do not want this to be used at 100%)',
            'disabled' => false,
            'cluster_mode' => 'clustermasterview.php',
        ),
    );

    protected static function initialize()
    {

        // starting with version 4.1, this is available in the Setup|System Info page
        if ( version_compare( '4.1', eZPublishSDK::version() ) <= 0 && !count( ezSysinfoClusterManager::clusterNodes() ) )
        {
            self::$view_groups['php']['hidden'] = true;
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
            self::$view_groups['eaccelerator']['disabled'] = false;
        }
        if ( extension_loaded( 'apc' ) )
        {
            self::$view_groups['apc']['disabled'] = false;
        }
        if ( function_exists( 'accelerator_get_status' ) || function_exists( 'opcache_get_status' ) )
        {
            self::$view_groups['acceleratorplus']['disabled'] = false;
        }
        /*else if ( extension_loaded( 'Zend Performance Suite' ) )
        {
            $operatorValue = 'performancesuite';
        }*/
        if ( extension_loaded( 'xcache' ) )
        {
            self::$view_groups['xcache']['disabled'] = false;
        }
        if ( extension_loaded( 'wincache' ) )
        {
            self::$view_groups['wincache']['disabled'] = false;
        }

        $db = eZDB::instance();
        if ( $db->databaseName() == 'mysql' )
        {
            /// @todo is this the correct way to check?
            if ( function_exists( 'mysqli_get_client_stats' ) )
            {
                self::$view_groups['mysqli']['disabled'] = false;
            }
        }

        // a bit hackish
        if ( count( ezSysinfoClusterManager::clusterNodes() ) && ! ezSysinfoClusterManager::isClusterSlaveRequest() )
        {
            foreach( self::$view_groups as &$viewDefinition )
            {
                if ( @$viewDefinition['cluster_mode'] != '' )
                {
                    $viewDefinition['script'] = $viewDefinition['cluster_mode'];
                }
            }
        }
    }
}
