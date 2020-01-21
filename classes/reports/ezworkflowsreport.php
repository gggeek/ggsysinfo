<?php
/**
 * @author G. Giunta
 * @copyright (C) G. Giunta 2014-2020
 * @license Licensed under GNU General Public License v2.0. See file license.txt
 */

class ezWorkflowsReport implements ezSysinfoReport
{

    public function getReport()
    {
        // quick and dirty: use same data as for the web
        $tpl = sysInfoTools::eZTemplateFactory();

        $tpl->setVariable( 'title', 'Workflows Report' );

        $workflows = eZWorkflow::fetchList( );
        $triggers = eZTrigger::fetchList();
        $tpl->setVariable( 'workflows', $workflows );
        $tpl->setVariable( 'triggers', $triggers );

        $htmlReport = $tpl->fetch( "design:sysinfo/workflowsreport.tpl" );
        return $htmlReport;
    }

    public function getDescription()
    {
        return array(
            'tag' => 'workflows',
            'title' => 'Workflows Report',
            'executingString' => 'Gathering workflows definition...',
            'format' => 'html'
        );
    }
}