<?php
/**
 *
 * @author G. Giunta
 * @version $Id$
 * @copyright (C) G. Giunta 2008-2010
 * @license Licensed under GNU General Public License v2.0. See file license.txt
 */

ob_start();
include('extension/ggsysinfo/modules/sysinfo/lib/apc.php');
$output = ob_get_contents();
ob_end_clean();
$output = preg_replace(array('#^.*<body>#s','#</body>.*$#s'), '', $output);

$tpl->setVariable( 'css', 'apc.css' );
$tpl->setVariable( 'info', $output );

?>
