<?php

/** @var eZTemplate $tpl */

$tpl->setVariable( 'groups', ezSysinfoModule::groupList() );
