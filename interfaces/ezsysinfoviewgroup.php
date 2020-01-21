<?php
/**
 * @author G. Giunta
 * @copyright (C) G. Giunta 2014-2020
 * @license Licensed under GNU General Public License v2.0. See file license.txt
 */

/**
 * To be implemented by all classes which return a list of views available for this extension.
 */
interface ezSysinfoViewgroup
{
    /**
     * @return array @see ezSysinfoModule for the format. In short: the format used by default by module.php.
     *
     * If the script used to execute the view is genericview.php, some extra keys such as description are used;
     * if 'viewmode' is a view parameter, then when this is set to json the view should return an array with data
     */
    static function groupList();
}