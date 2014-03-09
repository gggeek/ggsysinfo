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

$format = $Params['output_format'];

$testslist = sysInfoTools::runtests();
$ezsnmpd_available = false;
if ( in_array( 'ezsnmpd', eZExtension::activeExtensions() ) )
{
    $ezsnmpd_available = true;
}

if ( $format == 'plaintext' )
{
    var_export( $testslist );
    eZExecution::cleanExit();
}
else
{
    $tpl->setVariable( 'testslist', $testslist );
    $tpl->setVariable( 'ezsnmpd_available', $ezsnmpd_available );
}
?>