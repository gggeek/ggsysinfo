<?php
/**
 * @author G. Giunta
 * @copyright (C) G. Giunta 2014-2019
 * @license Licensed under GNU General Public License v2.0. See file license.txt
 */

class ezContentClassesReport implements ezSysinfoReport
{

    public function getReport()
    {
        // quick and dirty: use same data as for the web
        $tpl = sysInfoTools::eZTemplateFactory();
        $tpl->setVariable( 'title', 'Content Classes Report' );
        $htmlReport = $tpl->fetch( "design:sysinfo/classesreport.tpl" );
        return $htmlReport;
    }

    public function getDescription()
    {
        return array(
            'tag' => 'contentclasses',
            'title' => 'Content Classes Report',
            'executingString' => 'Gathering content classes definition...',
            'format' => 'html'
        );
    }
}