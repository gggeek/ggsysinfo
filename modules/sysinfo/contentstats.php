<?php
/**
 *
 * @author G. Giunta
 * @version $Id$
 * @copyright (C) G. Giunta 2008
 * @license Licensed under GNU General Public License v2.0. See file license.txt
 *
 * @todo add more classes of content that have no stats in main admin interface
 * @todo add support for ezsurvey, ezflow, eznewsletter contents
 */

$module = $Params['Module'];
$http = eZHTTPTool::instance();

$contentTypes = array(
    'Objects (including users)' => array( 'table' => 'ezcontentobject' ),
    'Users' => array( 'table' => 'ezuser' ),
    'Content Classes' => array( 'table' => 'ezcontentclass' ),
    'Information Collections' => array( 'table' => 'ezinfocollection' ),
);

$db = eZDB::instance();
$contentList = array();
foreach( $contentTypes as $key => $desc )
{
    $sql = 'SELECT COUNT(*) AS NUM FROM ' .  $desc['table'];
    /*if ( isset($desc['groupby']) )
    {
        $sql. = '';
    }*/
    $count = $db->arrayQuery( $sql );
    $contentList[$key] = $count[0]['NUM'];
}

require_once( "kernel/common/template.php" );
$tpl = templateInit();
$tpl->setVariable( 'title', 'Content stats' );
$tpl->setVariable( 'contentlist', $contentList );

$Result = array();
$Result['content'] = $tpl->fetch( "design:sysinfo/contentstats.tpl" ); //var_dump($cacheFilesList);

$Result['left_menu'] = 'design:parts/sysinfo/menu.tpl';
$Result['path'] = array( array( 'url' => false,
                                'text' => ezi18n( 'SysInfo', 'Content stats' ) ) );

?>
