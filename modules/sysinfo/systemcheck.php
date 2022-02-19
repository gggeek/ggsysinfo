<?php
/**
 * @author G. Giunta
 * @copyright (C) G. Giunta 2014-2022
 * @license Licensed under GNU General Public License v2.0. See file license.txt
 */

/** @var eZTemplate $tpl */

$warnings = systemChecker::checkSetupRequirements();

$tpl->setVariable( 'warnings', $warnings );
