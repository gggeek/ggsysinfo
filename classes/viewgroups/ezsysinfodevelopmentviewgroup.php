<?php
/**
 * @author G. Giunta
 * @copyright (C) G. Giunta 2014
 * @license Licensed under GNU General Public License v2.0. See file license.txt
 */

class ezSysinfoDevelopmentViewGroup extends ezSysinfoBaseViewGroup implements ezSysinfoViewgroup
{
    static $view_groups = array(

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

        'eventtypelist' => array(
            //'functions' => array( 'system_info' ), - we check in the module itself
            'script' => 'genericview.php',
            'default_navigation_part' => 'ezsysinfonavigationpart',
            'params' => array(  'extensionname' ),
            'name' => 'Workflow Event Types list',
            'description' => 'List of all workflow event types and workflows using them' ),
    );
}