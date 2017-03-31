<?php

$workflows = eZWorkflow::fetchList( );
$triggers = eZTrigger::fetchList();

$tpl->setVariable( 'workflows', $workflows );
$tpl->setVariable( 'triggers', $triggers );
