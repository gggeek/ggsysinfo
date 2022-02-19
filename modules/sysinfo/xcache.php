<?php
/**
 * @author G. Giunta
 * @copyright (C) G. Giunta 2008-2022
 * @license Licensed under GNU General Public License v2.0. See file license.txt
 */

/** @var eZTemplate $tpl */

$extdir =  eZExtension::baseDirectory();
ob_start();
include( $extdir . '/ggsysinfo/modules/sysinfo/lib/xcache_admin/xcache.php' );
$output = ob_get_contents();
ob_end_clean();
$output = preg_replace( array( '#^.*<body>#s','#</body>.*$#s' ), '', $output );

$tpl->setVariable( 'info', $output );
