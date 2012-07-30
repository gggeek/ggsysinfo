<?php
/**
 * @author G. Giunta
 * @copyright (C) G. Giunta 2012
 * @license Licensed under GNU General Public License v2.0. See file license.txt
 */
$mysqlnd_available = false;
if ( function_exists( 'mysqli_get_client_stats' ) )
{
    $stats = mysqli_get_client_stats();
    $mysqlnd_available = true;
}

$tpl->setVariable( 'stats', $stats );
$tpl->setVariable( 'mysqlnd_available', $mysqlnd_available );
$tpl->setVariable( 'important_stats', array( 'slow_queries', 'connect_failure' ) );

?>