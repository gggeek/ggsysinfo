<?php
/**
 *
 * @author G. Giunta
 * @copyright (C) G. Giunta 2012-2014
 * @license Licensed under GNU General Public License v2.0. See file license.txt
 *
 * @todo add more classes of content that have no stats in main admin interface
 * @todo add support for webshop, ezsurvey, ezflow, eznewsletter contents
 */

class contentStatsGatherer implements ezSysinfoReport
{

    public function getReport()
    {
        return self::gather();
    }

    public function getDescription()
    {
        return array(
            'tag' => 'contentstats',
            'title' => 'Content stats',
            'executingString' => 'Gathering content stats...',
            'format' => 'byrow'
        );
    }

    static function gather()
    {
        $contentTypes = array(
            'Objects (including users)' => array( 'table' => 'ezcontentobject' ),
            'Users' => array( 'table' => 'ezuser' ),
            'Nodes' => array( 'table' => 'ezcontentobject_tree' ),
            'Content Classes' => array( 'table' => 'ezcontentclass' ),
            'Information Collections' => array( 'table' => 'ezinfocollection' ),
            'Pending notification events' => array( 'table' => 'eznotificationevent', 'wherecondition' => 'status = 0' ),
            'Objects pending indexation' => array( 'table' => 'ezpending_actions', 'wherecondition' => "action = 'index_object'" ),
            'Binary files (content)' => array( 'table' => 'ezbinaryfile' ),
            'Image files (content)' => array( 'table' => 'ezimagefile' ),
            'Media files (content)' => array( 'table' => 'ezmedia' ),
            'Maximum children per node' => array( 'sql' => 'SELECT MAX(tot) AS NUM FROM ( SELECT count(*) AS tot FROM ezcontentobject_tree GROUP BY parent_node_id ) nodes' ),
            'Maximum nodes per object' => array( 'sql' => 'SELECT MAX(tot) AS NUM FROM ( SELECT count(*) AS tot FROM ezcontentobject_tree GROUP BY contentobject_id ) nodes' ),
            'Maximum incoming relations to an object' => array( 'sql' => 'SELECT MAX(tot) AS NUM FROM ( SELECT count(*) AS tot FROM ezcontentobject_link GROUP BY to_contentobject_id ) links' ),
            'Maximum outgoing relations from an object' => array( 'sql' => 'SELECT MAX(tot) AS NUM FROM ( SELECT count(*) AS tot FROM ezcontentobject_link GROUP BY from_contentobject_id ) links' ),
        );

        $db = eZDB::instance();
        $contentList = array();
        foreach( $contentTypes as $key => $desc )
        {
            if ( isset( $desc['table'] ) )
            {
                $sql = 'SELECT COUNT(*) AS NUM FROM ' .  $desc['table'];
                if ( @$desc['wherecondition'] )
                {
                    $sql .= ' WHERE ' . $desc['wherecondition'];
                }
            }
            else
            {
                $sql = $desc['sql'];
            }
            /*if ( isset($desc['groupby']) )
               {
               $sql. = '';
               }*/
            $count = $db->arrayQuery( $sql );
            $contentList[$key] = $count[0]['NUM'];
        }

        if ( in_array( 'ezfind', eZExtension::activeExtensions() ) )
        {
            $ini = eZINI::instance( 'solr.ini' );
            $ezfindpingurl = $ini->variable( 'SolrBase', 'SearchServerURI' )."/admin/stats.jsp";
            $data = eZHTTPTool::getDataByURL( $ezfindpingurl, false );
            //var_dump( $data );
            if ( preg_match( '#<stat +name="numDocs" ?>([^<]+)</stat>#', $data, $matches ) )
            {
                $contentList['Documents in SOLR'] = trim( $matches[1] );
            }
            else
            {
                $contentList['Documents in SOLR'] = 'Unknown';
            }
        }

        return $contentList;
    }
}

?>
