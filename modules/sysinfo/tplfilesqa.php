<?php
/**
 * @author G. Giunta
 * @copyright (C) G. Giunta 2014-2022
 * @license Licensed under GNU General Public License v2.0. See file license.txt
 */

/** @var array $Params */
/** @var eZTemplate $tpl */

$warnings = tplChecker::checkFileContents();
$ezgeshi_available = sysInfoTools::ezgeshiAvailable();

if ( $Params['viewmode'] == 'json' )
{
    $response_type = $Params['viewmode'];
    $response_data = $warnings;
    return;
}

$tpl->setVariable( 'warnings', $warnings );
$tpl->setVariable( 'ezgeshi_available', $ezgeshi_available );
