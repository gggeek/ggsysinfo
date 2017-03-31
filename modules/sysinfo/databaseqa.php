<?php
/**
 * @author G. Giunta
 * @copyright (C) G. Giunta 2012-2017
 * @license Licensed under GNU General Public License v2.0. See file license.txt
 */

$warnings = dbChecker::checkDatabase();

$tpl->setVariable( 'warnings', $warnings );
