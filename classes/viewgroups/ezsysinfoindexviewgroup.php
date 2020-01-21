<?php
/**
 * @author G. Giunta
 * @copyright (C) G. Giunta 2014-2020
 * @license Licensed under GNU General Public License v2.0. See file license.txt
 */

class ezSysinfoIndexViewGroup extends ezSysinfoBaseViewGroup implements ezSysinfoViewgroup
{
    static $view_groups = array(
        'index' => array(
            'script' => 'genericview.php',
            'default_navigation_part' => 'ezsysinfonavigationpart',
            'name' => '',
            'title' => 'System Information' ),
    );
}