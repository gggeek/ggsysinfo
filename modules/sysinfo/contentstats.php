<?php
/**
 *
 * @author G. Giunta
 * @version $Id$
 * @copyright (C) G. Giunta 2008-2010
 * @license Licensed under GNU General Public License v2.0. See file license.txt
 *
 * @todo add more classes of content that have no stats in main admin interface
 * @todo add support for ezsurvey, ezflow, eznewsletter contents
 */

$contentTypes = array(
    'Objects (including users)' => array( 'table' => 'ezcontentobject' ),
    'Users' => array( 'table' => 'ezuser' ),
    'Content Classes' => array( 'table' => 'ezcontentclass' ),
    'Information Collections' => array( 'table' => 'ezinfocollection' ),
    'Pending notifications' => array( 'table' => 'eznotificationevent', 'wherecondition' => 'status = 0' ),
    'Objects pending indexation' => array( 'table' => 'ezpending_actions', 'wherecondition' => "action = 'index_object'" ),
);

$db = eZDB::instance();
$contentList = array();
foreach( $contentTypes as $key => $desc )
{
    $sql = 'SELECT COUNT(*) AS NUM FROM ' .  $desc['table'];
    if ( @$desc['wherecondition'] )
    {
        $sql .= ' WHERE ' . $desc['wherecondition'];
    }
    /*if ( isset($desc['groupby']) )
    {
        $sql. = '';
    }*/
    $count = $db->arrayQuery( $sql );
    $contentList[$key] = $count[0]['NUM'];
}

$tpl->setVariable( 'contentlist', $contentList );

?>
