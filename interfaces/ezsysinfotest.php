<?php

/**
 * To be implemented by classes which can be hooked up to the "System Status" page to execute 'status checks'
 */
interface ezSysinfoTest
{
    /**
     * @return array @see ezsysinfotools for format
     */
    public static function runTests();
}

