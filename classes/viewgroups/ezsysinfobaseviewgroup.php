<?php
/**
 * @author G. Giunta
 * @copyright (C) G. Giunta 2014-2018
 * @license Licensed under GNU General Public License v2.0. See file license.txt
 */

class ezSysinfoBaseViewGroup
{
    static $view_groups = array();

    public static function groupList()
    {
        static::initialize();
        return static::$view_groups;
    }

    protected static function initialize()
    {
    }
}