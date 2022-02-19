<?php

/** @var eZTemplate $tpl */

$tpl->setVariable( 'roles', ezPoliciesReport::getRoles() );
