<?php
/**
 *
 * @author G. Giunta
 * @copyright (C) G. Giunta 2014
 * @license Licensed under GNU General Public License v2.0. See file license.txt
 */

class sysinfoTests implements ezSysinfoTest
{
    /**
     * @todo set up soap and webdav tests
     */
    public static function runTests()
    {

        $status_tests = array(
            'db' => '0',
            'cluster db' => '0',
            'ldap server' => '0',
            'web access' => '0',
            'ezfind' => '0',
            'mail' => '0',
            //'ez soap' => '?',
            //'ez webdav' => '?'
        );

        $db = eZDB::instance();
        if ( $db->isConnected() === true )
        {
            $status_tests['db'] = '1';
        }

        $clusterDBHandler = ezSysinfoClusterManager::clusterDBHandler();
        if ( $clusterDBHandler instanceof eZDBFileHandler )
        {
            // warning - we dig into the private parts of the cluster file handler,
            // as no real API are provided for it (yet)
            if ( is_resource( $clusterDBHandler->backend->db ) )
                $status_tests['cluster db'] = '1';
        }
        else if ( is_object( $clusterDBHandler ) )
        {
            // This is even worse: we have no right to know if db connection is ok.
            // So we replicate some code here...
            try
            {
                $clusterDBHandler->_connect();
                $status_tests['cluster db'] = '1';
            }
            catch ( exception $e )
            {

            }
        }
        else
        {
            $status_tests['cluster db'] = 'X';
        }

        if ( in_array( 'ezfind', eZExtension::activeExtensions() ) )
        {
            $ini = eZINI::instance( 'solr.ini' );
            $ezfinpingurl = $ini->variable( 'SolrBase', 'SearchServerURI' )."/admin/ping";
            $data = eZHTTPTool::getDataByURL( $ezfinpingurl, false );
            $pos2 = stripos($data, '<str name="status">OK</str>');
            if ( $pos2 !== false )
            {
                $status_tests['ezfind'] = '1';
            }
            else
            {
                $status_tests['ezfind'] = '0';
            }
        }
        else
        {
            $status_tests['ezfind'] = 'X';
        }

        $ini = eZINI::instance( 'ldap.ini' );
        if ( $ini->variable( 'LDAPSettings', 'LDAPEnabled' ) == 'true' && $ini->variable( 'LDAPSettings', 'LDAPServer' ) != '' )
        {
            if ( function_exists( 'ldap_connect' ) )
            {
                // code copied over ezldapuser class...

                $LDAPVersion = $ini->variable( 'LDAPSettings', 'LDAPVersion' );
                $LDAPServer = $ini->variable( 'LDAPSettings', 'LDAPServer' );
                $LDAPPort = $ini->variable( 'LDAPSettings', 'LDAPPort' );
                $LDAPBindUser = $ini->variable( 'LDAPSettings', 'LDAPBindUser' );
                $LDAPBindPassword = $ini->variable( 'LDAPSettings', 'LDAPBindPassword' );

                $ds = ldap_connect( $LDAPServer, $LDAPPort );

                if ( $ds )
                {
                    ldap_set_option( $ds, LDAP_OPT_PROTOCOL_VERSION, $LDAPVersion );
                    if ( $LDAPBindUser == '' )
                    {
                        $r = ldap_bind( $ds );
                    }
                    else
                    {
                        $r = ldap_bind( $ds, $LDAPBindUser, $LDAPBindPassword );
                    }
                    if ( $r )
                    {
                        $status_tests['ldap server'] = '1';
                    }
                }
            }
        }
        else
        {
            $status_tests['ldap server'] = 'X';
        }

        $ini = eZINI::instance( 'sysinfo.ini' );
        $websites = $ini->variable( 'SystemStatus', 'WebBeacons' );
        if ( is_string( $websites ) )
            $websites = array( $websites );
        foreach ( $websites as $key => $site )
        {
            if ( trim( $site ) == '' )
            {
                unset( $websites[$key] );
            }
        }
        if ( count( $websites ) )
        {
            foreach ( $websites as $site )
            {
                // current eZ code is broken if no curl is installed, as it does not check for 404 or such.
                // besides, it does not even support proxies...
                if ( extension_loaded( 'curl' ) )
                {
                    if ( eZHTTPTool::getDataByURL( $site, true ) )
                    {
                        $status_tests['web access'] = '1';
                        break;
                    }
                }
                else
                {
                    $data = eZHTTPTool::getDataByURL( $site, false );
                    if ( $data !== false && sysInfoTools::isHTTP200( $data) )
                    {
                        $status_tests['web access'] = '1';
                        break;
                    }
                }
            }
        }
        else
        {
            $status_tests['web access'] = 'X';
        }

        $ini = eZINI::instance( 'sysinfo.ini' );
        $recipient = $ini->variable( 'SystemStatus', 'MailReceiver' );
        $mail = new eZMail();
        if ( trim( $recipient ) != '' && $mail->validate( $recipient ) )
        {
            $mail->setReceiver( $recipient );
            $ini = eZINI::instance();
            $sender = $ini->variable( 'MailSettings', 'EmailSender' );
            $mail->setSender($sender);
            $mail->setSubject( "Test email" );
            $mail->setBody( "This email was automatically sent while testing eZ Publish connectivity to the mail server. Please do not reply." );
            $mailResult = eZMailTransport::send( $mail );
            if ( $mailResult )
            {
                $status_tests['mail'] = '1';
            }
        }
        else
        {
            $status_tests['mail'] = 'X';
        }

        /*
        $ini = eZINI::instance( 'soap.ini' );
        if ( $ini->variable( 'GeneralSettings', 'EnableSOAP' ) == 'true' )
        {
            /// @todo...
        }
        else
        {
            $status_tests['ez soap'] = 'X';
        }

        $ini = eZINI::instance( 'webdav.ini' );
        if ( $ini->variable( 'GeneralSettings', 'EnableWebDAV' ) == 'true' )
        {
            /// @todo...
        }
        else
        {
            $status_tests['ez webdav'] = 'X';
        }
        */

        return $status_tests;
    }

}