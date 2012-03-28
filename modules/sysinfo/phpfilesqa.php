<?php
/**
 * @author G. Giunta
 * @copyright (C) G. Giunta 2010-2012
 * @license Licensed under GNU General Public License v2.0. See file license.txt
 */

$warnings = phpChecker::checkFileContents();
$ezgeshi_available = sysInfoTools::ezgeshiAvailable();

$tpl->setVariable( 'warnings', $warnings );
$tpl->setVariable( 'ezgeshi_available', $ezgeshi_available );

?>