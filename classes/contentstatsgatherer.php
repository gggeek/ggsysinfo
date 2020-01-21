<?php
/**
 *
 * @author G. Giunta
 * @copyright (C) G. Giunta 2012-2020
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
            'Object versions (including users)' => array( 'table' => 'ezcontentobject_version' ),
            'Content Classes' => array( 'table' => 'ezcontentclass' ),
            'Information Collections' => array( 'table' => 'ezinfocollection' ),
            'Pending notification events' => array( 'table' => 'eznotificationevent', 'wherecondition' => 'status = 0' ),
            'Objects pending indexation' => array( 'table' => 'ezpending_actions', 'wherecondition' => "action = 'index_object'" ),
            'Objects pending subtree indexation (ezfind)' => array( 'table' => 'ezpending_actions', 'wherecondition' => "action = 'index_subtree'" ),
            'Binary files (content)' => array( 'table' => 'ezbinaryfile' ),
            'Image files (content)' => array( 'table' => 'ezimagefile' ),
            'Media files (content)' => array( 'table' => 'ezmedia' ),
            'Maximum versions per object' => array( 'sql' => 'SELECT MAX(tot) AS NUM FROM ( SELECT count(*) AS tot FROM ezcontentobject_version GROUP BY contentobject_id ) versions' ),
            'Maximum children per node' => array( 'sql' => 'SELECT MAX(tot) AS NUM FROM ( SELECT count(*) AS tot FROM ezcontentobject_tree GROUP BY parent_node_id ) nodes' ),
            'Maximum nodes per object' => array( 'sql' => 'SELECT MAX(tot) AS NUM FROM ( SELECT count(*) AS tot FROM ezcontentobject_tree GROUP BY contentobject_id ) nodes' ),
            'Maximum incoming relations to an object' => array( 'sql' => 'SELECT MAX(tot) AS NUM FROM ( SELECT count(*) AS tot FROM ezcontentobject_link GROUP BY to_contentobject_id ) links', 'nvl' => 0 ),
            'Maximum outgoing relations from an object' => array( 'sql' => 'SELECT MAX(tot) AS NUM FROM ( SELECT count(*) AS tot FROM ezcontentobject_link GROUP BY from_contentobject_id ) links', 'nvl' => 0 ),
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
            $count = $db->arrayQuery( $sql );
            $contentList[$key] = ( $count[0]['NUM'] === null ) ? $desc['nvl'] : $count[0]['NUM'];
        }

        if ( in_array( 'ezfind', eZExtension::activeExtensions() ) )
        {
            $ini = eZINI::instance( 'solr.ini' );
            // remove the last part of the url, corresponding to the core name
            $ezfindSatsUrl = substr(
                $ini->variable( 'SolrBase', 'SearchServerURI' ),
                0,
                strrpos( rtrim( $ini->variable( 'SolrBase', 'SearchServerURI' ), '/' ), '/' )
            ) . "/admin/cores";
            /// @todo it would be nice to be able to use a specific timeout here, in case of firewalls that block the connection...
            $data = eZHTTPTool::getDataByURL( $ezfindSatsUrl, false );
            /// @todo add support for multi-core setups
            if ( preg_match( '#<int +name="numDocs" ?>([^<]+)</int>#', $data, $matches ) )
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
