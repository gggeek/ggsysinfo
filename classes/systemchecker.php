<?php
/**
 * @author G. Giunta
 * @copyright (C) G. Giunta 2014-2020
 * @license Licensed under GNU General Public License v2.0. See file license.txt
 */

// relics...
require_once( 'kernel/setup/ezsetuptests.php' );
require_once( 'kernel/setup/ezsetupcommon.php' );

class systemChecker implements ezSysinfoReport
{

    public function getReport()
    {
        $data = self::checkSetupRequirements();
        return array_combine( array_keys( $data ), array_fill( 0, count( $data ), 'warning' ) );
    }

    public function getDescription()
    {
        return array(
            'tag' => 'setupwizardchecks',
            'title' => 'Setup wizard checks',
            'executingString' => 'Executing setup wizards checks...',
            'format' => 'byrow'
        );
    }

    /**
    * Executes checks for system requirements taken from setup wizard
    * @see eZStepSystemCheck::init
    */
    static function checkSetupRequirements()
    {
        $criticalTests = eZSetupCriticalTests();
        $optionalTests = eZSetupOptionalTests();
        //$testTable = eZSetupTestTable();

        // run all tests
        $list = null;
        $runResult = eZSetupRunTests( $criticalTests, 'eZSetup:init:system_check', $list );
        $optionalRunResult = eZSetupRunTests( $optionalTests, 'eZSetup:init:system_check', $list );

        // extract failed ones
        $warnings = array();
        foreach( array( $runResult['results'], $optionalRunResult['results'] ) as $tests )
        {
            foreach( $tests as $test )
            {
                if ( $test[0] != 1 )
                {
                    $warnings[$test[1]] = $test[2];
                }
            }
        }

        // remove failures we don't care about
        foreach( $warnings as $testName => $error )
        {
            switch( $testName )
            {
                // this test is useless: if we get here, at least 1 db connection is working (nb: is it really true? to be checked for anon uer...)
                case 'database_all_extensions':
                // this test should (imho) just be removed from the setup wizard
                case 'accept_path_info':
                    unset( $warnings[$testName] );
                    break;
                default:
                    break;
            }
        }

        return $warnings;
    }

}
