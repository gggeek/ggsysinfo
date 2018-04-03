<?php
/**
 *
 * @author G. Giunta
 * @copyright (C) G. Giunta 2008-2018
 * @license Licensed under GNU General Public License v2.0. See file license.txt
 */

$extdir =  eZExtension::baseDirectory();

// patch variable used by apc.php to build urls
$self = $_SERVER['PHP_SELF'];
$url = 'sysinfo/apc';
eZURI::transformURI( $url );

$_SERVER['PHP_SELF'] = $url;
ob_start();
include( $extdir . '/ggsysinfo/modules/sysinfo/lib/apc.php' );
$output = ob_get_contents();
ob_end_clean();

$_SERVER['PHP_SELF'] = $self;

// the included file is also used to generate images (links to self)
if ( isset( $_GET['IMG'] ) )
{
    echo $output;
    eZExecution::cleanExit();
}

$output = preg_replace( array( '#^.*<body>#s','#</body>.*$#s' ), '', $output );

$tpl->setVariable( 'css', 'apc.css' );
$tpl->setVariable( 'info', $output );
