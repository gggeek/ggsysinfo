<?php

/** @var eZTemplate $tpl */

$tpl->setVariable( 'info', eZSysinfoSCMChecker::getScmInfo() );
