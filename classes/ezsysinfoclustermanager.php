<?php
/**
 * @author G. Giunta
 * @copyright (C) G. Giunta 2014-2018
 * @license Licensed under GNU General Public License v2.0. See file license.txt
 */

/**
 * Does not 'manage' a cluster, but allows the extension to work in clustered configs
 */
class ezSysinfoClusterManager
{
    static $authStatus = false;

    /**
     * Returns a connection to the cluster db handler - which might be implemented using different php classes...
     * Null if not clustered
     *
     * @return eZClusterFileHandlerInterface|...
     */
    static public function clusterDBHandler()
    {
        $ini = eZINI::instance( 'file.ini' );
        $handler = $ini->variable( 'ClusteringSettings', 'FileHandler' );
        if ( $handler == 'ezdb' || $handler == 'eZDBFileHandler' )
        {
            return eZClusterFileHandler::instance();
        }
        else if ( $handler == 'eZDFSFileHandler' )
        {
            // This is even worse: we have no right to know if db connection is ok.
            // So we replicate some code here...
            return eZExtension::getHandlerClass(
                new ezpExtensionOptions(
                    array( 'iniFile'     => 'file.ini',
                        'iniSection'  => 'eZDFSClusteringSettings',
                        'iniVariable' => 'DBBackend' ) ) );
        }

        return null;
    }

    /**
     * Returns the local path (mounted) of the shared cluster storage, or null.
     * Note that for ezfs and ezfs2 solutions, there is no separate shared cluster storage
     *
     * @return string|null
     */
    static public function clusterFileStorageDir()
    {
        $ini = eZINI::instance( 'file.ini' );
        $handler = $ini->variable( 'ClusteringSettings', 'FileHandler' );
        if ( $handler == 'eZDFSFileHandler' )
        {
            return $ini->variable( 'eZDFSClusteringSettings', 'MountPointPath' );
        }

        return null;
    }

    static public function clusterDataRetrievalMode()
    {
        $ini = eZINI::instance( 'sysinfo.ini' );
        switch( $ini->variable( 'ClusterSettings', 'DataRetrieval' ) )
        {
            case 'iframe':
                return self::RETRIEVAL_MODE_IFRAME;
            default:
                return self::RETRIEVAL_MODE_BACKEND;
        }
    }

    /**
     * The list of nodes in the cluster, as per configuration.
     * Empty node names are pruned
     *
     * @return array
     */
    static public function clusterNodes()
    {
        $ini = eZINI::instance( 'sysinfo.ini' );
        $urls =  $ini->variable( 'ClusterSettings', 'ClusterNodes' );
        foreach( $urls as $name => &$url )
        {
            if ( $url == '' )
            {
                unset( $urls[$name] );
            }
        }
        return array_keys( $urls );
    }

    /**
     * Returns a list of urls, corresponding to the current local url for each cluster node.
     * Used to build the requests to be sent to the cluster-master and cluster-slave views - it makes little sense
     * without proper team play with clustermasterview.php.
     *
     * Assumes that there is no query string or in-page anchor in the url
     *
     * @param string $mode 'proxy' or 'slave'
     * @param string $localUrl
     * @param array $extraParams
     * @param array $queryStringParams
     * @return array
     *
     * @todo use parse_url() to make this work even for query strings and anchors
     */
    static public function clusterDataRetrievalUrls( $mode, $localUrl, $extraParams = array(), $queryStringParams=array() )
    {
        if ( $mode != 'proxy' )
        {
            $localUrl = str_replace( '/sysinfo/', '/sysinfo/clusterslave/', $localUrl );
        }

        $localUrl = rtrim( $localUrl, '/' );
        foreach( $extraParams as $name => $value )
        {
            $localUrl .= "/($name)/" . urlencode( $value );
        }

        $ini = eZINI::instance( 'sysinfo.ini' );
        $urls =  $ini->variable( 'ClusterSettings', 'ClusterNodes' );

        $qs = array();
        foreach( $queryStringParams as $key => $val )
        {
            $qs[] = $key . '=' . urlencode( $val );
        }
        if ( count( $qs ) )
        {
            $qs = '?' . implode ( '&', $qs );
        }
        else
        {
            $qs = '';
        }

        foreach( $urls as $name => &$url )
        {
            if ( $url == '' )
            {
                unset( $urls[$name] );
            }
            else
            {
                if ( $mode == 'proxy' )
                {
                    $url = $localUrl . '/(targetnode)/' . $name . $qs ;
                }
                else
                {
                    $url = rtrim( $url, '/' ) . '/' . ltrim( $localUrl, '/' ) . $qs . ( $qs == '' ? '?' : '&' ) . 'requestor=cluster';
                }
            }
        }

        return $urls;
    }

    /**
     * Starts the http call to retrieve content from cluster nodes, setting appropriate configs
     *
     * @param string $url
     * @return stream resource
     */
    static public function clusterDataFopen( $url )
    {
        $opts = array();

        $ini = eZINI::instance( 'sysinfo.ini' );
        if ( $ini->hasVariable( 'ClusterSettings', 'CustomHeaders' ) && count ( $headers = $ini->variable( 'ClusterSettings', 'CustomHeaders' ) ) )
        {
            foreach( $headers as $header => &$value )
            {
                $value = "$header: $value";
            }
            // is this correct or an array would do?
            $opts['http']['header'] = implode( "\n", $headers );
        }

        $context = stream_context_create( $opts );

        return fopen( $url, 'r', null, $context );
    }

    /**
     * Tells whether the current view should display results in cluster-slave mode
     *
     * We try not to force every view to declare that it has support for the 'requestor' param
     *
     * @return bool
     */
    static public function isClusterSlaveRequest()
    {
        return isset( $_GET['requestor'] ) && $_GET['requestor'] == 'cluster';
    }

    /**
     * Returns the name of the node to which the current request should be proxied
     *
     * @param array $Params view parameters
     * @return null|string
     */
    static public function clusterProxyRequestTarget( $Params )
    {
        if ( isset( $Params['targetnode'] ) )
        {
            return $Params['targetnode'];
        }
        else if ( isset( $Params["UserParameters"]['targetnode'] ) )
        {
            return $Params["UserParameters"]['targetnode'];
        }
        return null;
    }

    /**
     * A helper to allow views communicate auth status between each other when doing internal redirects
     * @param bool $status
     */
    static public function setAuthStatus( $status )
    {
        self::$authStatus = $status;
    }

    static public function getAuthStatus()
    {
        return self::$authStatus;
    }

    /**
     * Auth tokens we store in cache, as it is clustered and does not need an extra dtatabase table
     *
     * @param string $viewName
     * @return string
     */
    static public function generateAuthToken( $viewName )
    {
        $ini = eZINI::instance( 'sysinfo.ini' );
        $cacheDir = eZSys::cacheDirectory() . '/sysinfo/authtokens';

        $seed = md5( getmypid() . microtime() );
        $secret = md5( $viewName . ' ' . $seed . ' ' . $ini->variable( 'ClusterSettings', 'TokenSecret' ) );

        $cacheFile = $cacheDir . '/' . $seed;
        $clusterFile = eZClusterFileHandler::instance( $cacheFile );
        $clusterFile->fileStoreContents( $cacheFile, time() . ' ' . $secret );

        return $seed;
    }

    /**
     * @param $viewName
     * @param $seed
     * @return bool
     */
    static public function verifyAuthToken( $viewName, $seed )
    {
        $ini = eZINI::instance( 'sysinfo.ini' );
        $cacheDir = eZSys::cacheDirectory() . '/sysinfo/authtokens';

        $cacheFile = $cacheDir . '/' . $seed;
        $clusterFile = eZClusterFileHandler::instance( $cacheFile );
        if ( $clusterFile->exists() )
        {
            $contents = $clusterFile->fetchContents();
            list( $time, $secret ) = explode( ' ', $contents, 2 );
            if (
                ( ( time() - $time ) <= $ini->variable( 'ClusterSettings', 'TokenTTL' ) ) &&
                $secret == md5( $viewName . ' ' . $seed . ' ' . $ini->variable( 'ClusterSettings', 'TokenSecret' ) )
            )
            {
                return true;
            }
        }
        return false;
    }
}