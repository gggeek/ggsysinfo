<?php
/**
 * @author G. Giunta
 * @copyright (C) G. Giunta 2014-2016
 * @license Licensed under GNU General Public License v2.0. See file license.txt
 */

class ezSysinfoeZPublishViewGroup extends ezSysinfoBaseViewGroup implements ezSysinfoViewgroup
{
    static $view_groups = array(
        'systemstatus' => array(
            //'functions' => array( 'system_info' ), - we check in the module itself
            'script' => 'genericview.php',
            'default_navigation_part' => 'ezsysinfonavigationpart',
            /// @deprecated
            'params' => array( 'output_format' ),
            'unordered_params' => array( 'view' => 'viewmode' ),
            'name' => 'System status',
            'description' => 'Executes tests to verify the functioning of various parts of the system (e.g. connection to the database or to the eZFind indexing server)',
            'cluster_mode' => 'clustermasterview.php',
        ),

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
            'disabled' => true,
            'cluster_mode' => 'clustermasterview.php',
        ),

        'clustercachestats' => array(
            //'functions' => array( 'system_info' ), - we check in the module itself
            'script' => 'genericview.php',
            'default_navigation_part' => 'ezsysinfonavigationpart',
            'unordered_params' => array( 'view' => 'viewmode' ),
            'name' => 'Clustered Cache stats',
            'description' => 'Number of files and total size per every cache type - shared cluster storage',
            'disabled' => true
        ),

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
            'disabled' => true,
            'cluster_mode' => 'clustermasterview.php',
        ),

        'clusterstoragestats' => array(
            //'functions' => array( 'system_info' ), - we check in the module itself
            'script' => 'genericview.php',
            'default_navigation_part' => 'ezsysinfonavigationpart',
            'unordered_params' => array( 'view' => 'viewmode' ),
            'name' => 'Clustered Storage stats',
            'description' => 'Number of files and total size for binary contents - shared cluster storage',
            'disabled' => true
        ),

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
            'description' => 'Number of content objects, information collections, pending notification events, pending indexation events etc...  present in the database' ),

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
            'description' => 'Total size and last modification date of log files',
            'cluster_mode' => 'clustermasterview.php',
        ),

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
            'hidden' => true,
            'cluster_mode' => 'clustermasterview.php'
        ),

        'customlogview' => array(
            //'functions' => array( 'system_info' ), - we check in the module itself
            'script' => 'genericview.php',
            'default_navigation_part' => 'ezsysinfonavigationpart',
            'params' => array( 'logfile' ),
            'unordered_params' => array(),
            'name' => 'Custom Log view',
            'hidden' => true ),

        'clusterslave' => array(
            //'functions' => array( 'system_info' ), - we check in the module itself
            'script' => 'clusterslaveview.php',
            'default_navigation_part' => 'ezsysinfonavigationpart',
            'params' => array(),
            'unordered_params' => array( 'authtoken' => 'authToken' ),
            'hidden' => true ),

    );

    protected static function initialize()
    {
        $ini = eZINI::instance( 'file.ini' );
        $h = $ini->variable( 'ClusteringSettings', 'FileHandler' );
        if ( in_array( $h, array( 'ezfs', 'eZFSFileHandler', 'eZFS2FileHandler' ) ) )
        {
            self::$view_groups['cachestats']['disabled'] = false;
            self::$view_groups['cachesearch']['disabled'] = false;
            self::$view_groups['storagestats']['disabled'] = false;
        }
        if ( in_array( $h, array( 'eZDFSFileHandler' ) ) )
        {
            self::$view_groups['cachestats']['disabled'] = false;
            self::$view_groups['storagestats']['disabled'] = false;
            self::$view_groups['clustercachestats']['disabled'] = false;
            self::$view_groups['clusterstoragestats']['disabled'] = false;

            self::$view_groups['cachestats']['name'] = 'Local Cache Stats';
            self::$view_groups['storagestats']['name'] ='Local Storage Stats';
        }
        else
        {
            self::$view_groups['clustercachestats']['hidden'] = true;
            self::$view_groups['clusterstoragestats']['hidden'] = true;
        }
        if ( eZSysinfoSCMChecker::hasScmInfo()) {
            self::$view_groups['sourcerevision'] = array(
                'script' => 'genericview.php',
                'default_navigation_part' => 'ezsysinfonavigationpart',
                //'unordered_params' => array( 'view' => 'viewmode' ),
                'name' => 'SCM Info',
                'description' => 'Information about the Source Control System current Revision'
            );
        }

        // a bit hackish...
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