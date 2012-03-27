<?php
/**
 * @author G. Giunta
 * @copyright (C) G. Giunta 2010-2012
 * @license Licensed under GNU General Public License v2.0. See file license.txt
 */

$warnings = iniChecker::checkFileNames();
$ezgeshi_available = false;
if ( in_array( 'ezsh', eZExtension::activeExtensions() ) )
{
    $info = eZExtension::extensionInfo( 'ezsh' );
    $ezgeshi_available = ( version_compare( $info['Version'], '1.3' ) >= 0 );
}

$tpl->setVariable( 'warnings', $warnings );
$tpl->setVariable( 'ezgeshi_available', $ezgeshi_available );

?>