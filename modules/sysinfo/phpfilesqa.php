<?php
/**
 * @author G. Giunta
 * @version $Id: contentstats.php 2570 2008-11-25 11:35:44Z ezsystems $
 * @copyright (C) G. Giunta 2010
 * @license Licensed under GNU General Public License v2.0. See file license.txt
 */

$warnings = phpChecker::checkFileContents();
$ezgeshi_available = false;
if ( in_array( 'ezsh', eZExtension::activeExtensions() ) )
{
    $info = eZExtension::extensionInfo( 'ezsh' );
    $ezgeshi_available = ( version_compare( $info['Version'], '1.3' ) >= 0 );
}

$tpl->setVariable( 'warnings', $warnings );
$tpl->setVariable( 'ezgeshi_available', $ezgeshi_available );

?>