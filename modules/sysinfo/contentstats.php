<?php
/**
 *
 * @author G. Giunta
 * @copyright (C) G. Giunta 2008-2020
 * @license Licensed under GNU General Public License v2.0. See file license.txt
 */

$tpl->setVariable( 'contentlist', contentStatsGatherer::gather() );
