<?php
/**
 * Tests status of eZ Publish install in more detail than ezinfo/isalive
 * For every test, 1 = OK, 0 = KO and X = NA. ? = test not yet implemented
 * NB: some tests are enabled/disabled depending upon config in sysinfo.ini
 *
 * @author G. Giunta
 * @copyright (C) G. Giunta 2008-2014
 * @license Licensed under GNU General Public License v2.0. See file license.txt
 */

// backwards compatibility
if ( !isset( $Params['viewmode'] ) )
{
    if ( isset( $Params['output_format'] ) )
    {
        $Params['viewmode'] = $Params['output_format'];
    }
}

$testsList = sysInfoTools::runTests();
$ezsnmpd_available = false;
if ( in_array( 'ezsnmpd', eZExtension::activeExtensions() ) )
{
    $ezsnmpd_available = true;
}

if ( $Params['viewmode'] == 'plaintext' || $Params['viewmode'] == 'json' )
{
    $response_type = $Params['viewmode'];
    $response_data = $testsList;
    return;
}

$tpl->setVariable( 'testslist', $testsList );
$tpl->setVariable( 'ezsnmpd_available', $ezsnmpd_available );

