<?php
/**
 * @author G. Giunta
 * @copyright (C) G. Giunta 2014-2016
 * @license Licensed under GNU General Public License v2.0. See file license.txt
 */

class ezSysinfoReportsViewGroup extends ezSysinfoBaseViewGroup implements ezSysinfoViewgroup
{
    static $view_groups = array(
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

        'workflowsreport' => array(
            //'functions' => array( 'system_info' ), - we check in the module itself
            'script' => 'genericview.php',
            'default_navigation_part' => 'ezsysinfonavigationpart',
            'params' => array( ),
            'name' => 'Workflows & Triggers',
            'title' => 'Workflows & Triggers report',
            'description' => 'Definition of all Workflows & Triggers in a format friendly to backups' ),
    );
}