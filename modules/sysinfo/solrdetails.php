<?php
/**
 * User: gaetano.giunta
 * Date: 30/05/14
 * Time: 12.02
 */

$contentList = array();

if ( in_array( 'ezfind', eZExtension::activeExtensions() ) )
{
    $ini = eZINI::instance( 'solr.ini' );

    // OVUM additions - query both solr servers besides the vIP
    foreach( array( 'SolrIPMaster', 'SolrIPSlave' ) as $hostname )
    {
        if ( $ini->hasVariable( 'OvumSearch', $hostname ) && $ini->variable( 'OvumSearch', $hostname ) != '' )
        {
            $ezfindpingurl = 'http://' . $ini->variable( 'OvumSearch', $hostname ) . ":8983/solr/admin/stats.jsp";
            $data = eZHTTPTool::getDataByURL( $ezfindpingurl, false );
            //var_dump( $data );
            if ( preg_match( '#<stat +name="numDocs" ?>([^<]+)</stat>#', $data, $matches ) )
            {
                $contentList['Documents in SOLR ' . $hostname] = trim( $matches[1] );
            }
            else
            {
                $contentList['Documents in SOLR ' . $hostname] = 'Unknown';
            }

            $ezfindreplicationurl = 'http://' . $ini->variable( 'OvumSearch', $hostname ) . ":8983/solr/admin/replication/index.jsp";
            $data = eZHTTPTool::getDataByURL( $ezfindreplicationurl, false );
            //var_dump( $data );
            if ( preg_match( '#<h1>([^<]+)</h1>#', $data, $matches ) )
            {
                $contentList['Role ' . $hostname] = trim( str_replace( 'Solr replication (ezfind)', '', $matches[1] ) );
            }
            else
            {
                $contentList['Role ' . $hostname] = 'Unknown';
            }
            if ( preg_match( '#Index (Version:[^<]+)</td>#', $data, $matches ) )
            {
                $contentList['Index status ' . $hostname] = trim($matches[1] );
            }
            else
            {
                $contentList['Index status ' . $hostname] = 'Unknown';
            }
        }
    }
}

$tpl->setVariable( 'data', $contentList );
