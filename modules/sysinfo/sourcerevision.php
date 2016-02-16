<?php

/*$revisionInfo = array();
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
$tpl->setVariable( 'tag_info', $tagInfo );*/

$tpl->setVariable( 'info', eZSysinfoSCMChecker::getScmInfo() );