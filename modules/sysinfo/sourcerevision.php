<?php
/**
 * User: gaetano.giunta
 * Date: 24/04/14
 * Time: 17.33
 */

$revisionInfo = array();
$retcode = 0;
exec( "cd .. && git log -1", $revisionInfo, $retcode );

$statusInfo = array();
$retcode = 0;
exec( "cd .. && git status", $statusInfo, $retcode );

$tagInfo = array();
$retcode = 0;
exec( "cd .. && git describe", $tagInfo, $retcode );

$tpl->setVariable( 'revision_info', $revisionInfo );
$tpl->setVariable( 'status_info', $statusInfo );
$tpl->setVariable( 'tag_info', $tagInfo );