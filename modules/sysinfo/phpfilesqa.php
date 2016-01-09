<?php
/**
 * @author G. Giunta
 * @copyright (C) G. Giunta 2010-2016
 * @license Licensed under GNU General Public License v2.0. See file license.txt
 */

$warnings = phpChecker::checkFileContents();
$ezgeshi_available = sysInfoTools::ezgeshiAvailable();

if ( $Params['viewmode'] == 'json' )
{
    $response_type = $Params['viewmode'];
    $response_data = $warnings;
    return;
}

$tpl->setVariable( 'warnings', $warnings );
$tpl->setVariable( 'ezgeshi_available', $ezgeshi_available );
