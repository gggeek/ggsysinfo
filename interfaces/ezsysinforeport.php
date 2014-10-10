<?php

/**
 * To be implemented by all classes which implement a "report" - for the moment reports are displayed by a CLI script
 */
interface ezSysinfoReport
{
    /**
     * @return array
     *
     * @todo describe the format
     */
    public function getReport();

    /**
     * @return array:
     *         - 'tag'
     *         - 'title'
     *         - 'executingString'
     *         - 'format' byrow, byline or html for now
     */
    public function getDescription();

}

