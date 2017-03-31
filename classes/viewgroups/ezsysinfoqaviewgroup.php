<?php
/**
 * @author G. Giunta
 * @copyright (C) G. Giunta 2014-2017
 * @license Licensed under GNU General Public License v2.0. See file license.txt
 */

class ezSysinfoQAViewGroup extends ezSysinfoBaseViewGroup implements ezSysinfoViewgroup
{
    static $view_groups = array(

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
            'description' => 'Checks syntax validity for all tpl files found' )
    );

    protected static function initialize()
    {
        $db = eZDB::instance();
        if ( $db->databaseName() == 'mysql' )
        {
            self::$view_groups['databaseqa']['disabled'] = false;
        }
    }
}