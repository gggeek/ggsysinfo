<?php
/**
 * @author G. Giunta
 * @copyright (C) G. Giunta 2013-2022
 * @license Licensed under GNU General Public License v2.0. See file license.txt
 */

/** @var array $Params */
/** @var eZTemplate $tpl */
/** @var eZINI $ini */
/** @var string $hostName */

$extdir =  eZExtension::baseDirectory();
ob_start();
include( $extdir . '/ggsysinfo/modules/sysinfo/lib/ocp.php' );
$output = ob_get_contents();
ob_end_clean();
$output = preg_replace( array( '#^.*<body>#s','#</body>.*$#s' ), '', $output );

$tpl->setVariable( 'css', 'acceleratorplus.css' );
$tpl->setVariable( 'info', $output );
