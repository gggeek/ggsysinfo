<?php
/**
 *
 * @author G. Giunta
 * @version $Id$
 * @copyright (C) G. Giunta 2008
 * @license Licensed under GNU General Public License v2.0. See file license.txt
 *
 * @todo add support for clustered configs
 */

class sysInfoTools
{

    static function countFilesInDir( $cachedir )
    {
        if (  eZSys::osType() != 'win32' )
        {
            /// @todo use a command that escapes newlines in file names?
            exec( "find \"$cachedir\" -type f | wc -l", $result );
            /// @todo test properly to see if we have received a number or an error...
            return $result[0];
        }
        else
        {
            $return = 0;
            $files = @scandir( $cachedir );
            if ( $files === false )
                return false;
            foreach( $files as $file )
            {
                if ( $file != '.' && $file != '..' )
                {
                    if ( is_dir( $cachedir . '/' . $file ) )
                    {
                        $return += sysInfoTools::countFilesInDir( $cachedir . '/' . $file );
                    }
                    else
                    {
                        $return++;
                    }
                }
            }
            return $return;
        }
    }

    static function countFilesSizeInDir( $cachedir )
    {
        if (  eZSys::osType() != 'win32' )
        {
            /// @todo verify that we got no error?
            exec( "du -b -c -s \"$cachedir\"", $result );
            $result = @explode( ' ', $result[1] );
            return $result[0];
        }
        else
        {
            $return = 0;
            $files = @scandir( $cachedir );
            if ( $files === false )
                return false;
            foreach( $files as $file )
            {
                if ( $file != '.' && $file != '..' )
                {
                    if ( is_dir( $cachedir . '/' . $file ) )
                    {
                        $return += sysInfoTools::countFilesSizeInDir( $cachedir . '/' . $file );
                    }
                    else
                    {
                        $return += filesize( $cachedir . '/' . $file );
                    }
                }
            }
            return $return;
        }
    }

    /**
     * Search text in files using regexps (recursively through folders).
     * Assumes egrep is available if not on windows
     */
    static function searchInFiles( $searchtext, $cachedir, $is_regexp = true )
    {
        //$fileHandler = eZClusterFileHandler::instance();
        $result = array();

        if (  eZSys::osType() != 'win32' )
        {
            if ( $is_regexp )
            {
                exec( 'egrep -s -R -l "' . str_replace( '"', '\"', $searchtext ) . "\" \"$cachedir\"", $result );
            }
            else
            {
                exec( 'fgrep -s -R -l "' . str_replace( '"', '\"', $searchtext ) . "\" \"$cachedir\"", $result );
            }
        }
        else
        {
            $files = @scandir( $cachedir );
            if ( $files === false )
                return false;
            foreach( $files as $file )
            {
                if ( $file != '.' && $file != '..' )
                {
                    if ( is_dir( $cachedir . '/' . $file ) )
                    {
                        $result = array_merge( $result, sysInfoTools::searchInFiles( $searchtext, $cachedir . '/' . $file, $is_regexp ) );
                    }
                    else
                    {
                        $txt = @file_get_contents( $cachedir. '/'. $file );
                        /// @todo escape properly #
                        if ( $is_regexp )
                        {
                            if( preg_match( "#". $searchtext . "#", $txt ) )
                            {
                                $result[] = $cachedir. '/'. $file;
                            }
                        }
                        else
                        {
                            if( strpos( $txt, $searchtext ) !== false )
                            {
                                $result[] = $cachedir. '/'. $file;
                            }
                        }
                        $txt = false; // free memory asap
                    }
                }
            }
        }

        return $result;
    }

    /**
    * @todo set up soap and webdav tests
    */
    static function runtests()
    {

        $status_tests = array(
            'db' => '0',
            'cluster db' => '0',
            'ldap server' => '0',
            'web access' => '0',
            'mail' => '0',
            //'ez soap' => '?',
            //'ez webdav' => '?'
            );

        $db = eZDB::instance();
        if ( $db->isConnected() === true )
        {
            $status_tests['db'] = 1;
        }

        $ini = eZINI::instance( 'file.ini' );
        if ( $ini->variable( 'ClusteringSettings', 'FileHandler' ) == 'ezdb' )
        {
            // @todo...
            $dbFileHandler = eZClusterFileHandler::instance();
            if ( $dbFileHandler instanceof eZDBFileHandler )
            {
                // warning - we dig into the private parts of the cluster file handler,
                // as no real API are provided for it (yet)
                if ( is_resource( $dbFileHandler->backend->db ) )
                    $status_tests['cluster db'] = 1;
            }
        }
        else
        {
            $status_tests['cluster db'] = 'X';
        }

        $ini = eZINI::instance( 'ldap.ini' );
        if ( $ini->variable( 'LDAPSettings', 'LDAPEnabled' ) == 'true' && $ini->variable( 'LDAPSettings', 'LDAPServer' ) != '' )
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
                    $status_tests['ldap server'] = 1;
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
                        $status_tests['web access'] = 1;
                        break;
                    }
                }
                else
                {
                    $data = eZHTTPTool::getDataByURL( $site, false );
                    if ( $data !== false && sysInfoTools::isHTTP200( $data) )
                    {
                        $status_tests['web access'] = 1;
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
            $mail->setBody( "Thsi email was automatically sent while testing eZ Publish connectivity to the mail server. Please do not reply." );
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

    static function isHTTP200( $data )
    {
        // Support "web-proxy-tunelling" connections for https through proxies
        if( preg_match( '/^HTTP\/1\.[0-1] 200 Connection established/', $data ) )
        {
            // Look for CR/LF or simple LF as line separator,
            // (even though it is not valid http)
            $pos = strpos( $data, "\r\n\r\n" );
            if( $pos || is_int( $pos ) )
            {
                $bd = $pos+4;
            }
            else
            {
                $pos = strpos( $data, "\n\n" );
                if( $pos || is_int( $pos ) )
                {
                    $bd = $pos+2;
                }
                else
                {
                    // No separation between response headers and body: fault?
                    $bd = 0;
                }
            }
            if ( $bd )
            {
                // this filters out all http headers from proxy.
                // maybe we could take them into account, too?
                $data = substr( $data, $bd );
            }
            else
            {
                continue; // next web server
            }
        }
        // Strip HTTP 1.1 100 Continue header if present
        while( preg_match( '/^HTTP\/1\.1 1[0-9]{2} /', $data) )
        {
            $pos = strpos( $data, 'HTTP', 12 );
            if( !$pos && !is_int( $pos ) ) // works fine in php 3, 4 and 5
            {
                // server sent a Continue header without any (valid) content following...
                break;
            }
            $data = substr( $data, $pos );
        }
        if ( preg_match( '/^HTTP\/[0-9.]+ 200 /', $data ) )
        {
            return true;
        }

        return false;
    }

}
?>
