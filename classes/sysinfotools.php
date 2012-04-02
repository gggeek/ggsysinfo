<?php
/**
 *
 * @author G. Giunta
 * @copyright (C) G. Giunta 2008-2012
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
                        $return += self::countFilesInDir( $cachedir . '/' . $file );
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
                return array();
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
			'ezfind' => '0',
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
        $handler = $ini->variable( 'ClusteringSettings', 'FileHandler' );
        if ( $handler == 'ezdb' || $handler == 'eZDBFileHandler' )
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
        else if ( $handler == 'eZDFSFileHandler' )
        {
            // This is even worse: we have no right to know if db connection is ok.
            // So we replicate some code here...
            $dbbackend = eZExtension::getHandlerClass(
                new ezpExtensionOptions(
                array( 'iniFile'     => 'file.ini',
                       'iniSection'  => 'eZDFSClusteringSettings',
                       'iniVariable' => 'DBBackend' ) ) );
            try
            {
                $dbbackend->_connect();
                $status_tests['cluster db'] = 1;
            }
            catch ( exception $e )
            {

            }
        }
        else
        {
            $status_tests['cluster db'] = 'X';
        }

	if ( in_array( 'ezfind', eZExtension::activeExtensions())  ){
		 $ini = eZINI::instance( 'solr.ini' );
		 $ezfinpingurl = $ini->variable( 'SolrBase', 'SearchServerURI' )."/admin/ping";
		 $data = eZHTTPTool::getDataByURL( $ezfinpingurl, false );
           		 $pos2 = stripos($data, '<str name="status">OK</str>');
	if ($pos2 !== false)
             {
                $status_tests['ezfind'] = '1';
             }else{
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

    static function ezgeshiAvailable()
    {
        return false;
        if ( in_array( 'ezsh', eZExtension::activeExtensions() ) )
        {
            $info = eZExtension::extensionInfo( 'ezsh' );
            // since ezp 4.4, we have a lowercase version info
            return ( version_compare( @$info['Version'], '1.3' ) >= 0 || version_compare( @$info['version'], '1.3' ) >= 0 );
        }
        return false;
    }

    /**
    * Return arry of all php classes registered for autoload.
    * Class ezpAutoloader does not help us here...
    */
    static function autoloadClasses()
    {
        if ( !is_array( self::$ezpClasses ) )
        {
            self::$ezpClasses = include 'autoload/ezp_kernel.php';
            if ( file_exists( 'var/autoload/ezp_extension.php' ) )
            {
                self::$ezpClasses = array_merge( self::$ezpClasses, include 'var/autoload/ezp_extension.php' );
            }
            if ( defined( 'EZP_AUTOLOAD_ALLOW_KERNEL_OVERRIDE' ) and EZP_AUTOLOAD_ALLOW_KERNEL_OVERRIDE )
            {
                if ( $ezpKernelOverrideClasses = include 'var/autoload/ezp_override.php' )
                {
                    self::$ezpClasses = array_merge( self::$ezpClasses, $ezpKernelOverrideClasses );
                }
            }
        }
        return self::$ezpClasses;
    }

    /**
    * Returns true for eZP versions which have a known github tag
    * @param string $version
    * @return boolean
    */
    static function sourceCodeAvailable( $version )
    {
        $ini = eZINI::instance( 'sysinfo.ini' );
        $excluded = $ini->variable( 'GeneralSettings', 'MissingSourceVersions' );
        // exclude CP installs, as there is no online tag for those
        $majorversion = explode( '.', $version );
        $majorversion = $majorversion[0];
        return ( !in_array( $version, $excluded ) && $majorversion < 2011 );
    }

    /**
    * Given a (known) tpl operator name, returns its subfolder in the online docs
    * @param string $version
    * @return array or false
    *
    * @todo move list to ini ?
    */
    static function operatorDocFolders( $operator )
    {
        $out = false;
        $folders = array(
            'Arrays' => array( 'append', 'array', 'array_sum', 'begins_with', 'compare', 'contains', 'ends_with', 'explode', 'extract', 'extract_left', 'extract_right', 'hash', 'implode', 'insert', 'merge', 'prepend', 'remove', 'repeat', 'replace', 'reverse', 'unique' ),
            'Data-and-information-extraction' => array( 'currentdate', 'ezhttp', 'ezhttp_hasvariable', 'ezini', 'ezini_hasvariable', 'ezmodule', 'ezpreference', 'ezsys', 'fetch', 'module_params' ),
            'Formatting-and-internationalization' => array( 'd18n', 'datetime', 'i18n', 'l10n', 'si' ),
            'Images' => array( 'image', 'imagefile', 'texttoimage' ),
            'Logical-operations' => array( 'and', 'choose', 'cond', 'eq', 'false', 'first_set', 'ge', 'gt', 'le', 'lt', 'ne', 'not', 'null', 'or', 'true' ),
            'Matemathics' => array( 'abs', 'ceil', 'dec', 'div', 'floor', 'inc', 'max', 'min', 'mod', 'mul', 'rand', 'round', 'sub', 'sum' ),
            'Miscellaneous' => array( 'action_icon', 'attribute', 'classgroup_icon', 'class_icon', 'content_structure_tree', 'ezpackage', 'flag_icon', 'gettime', 'icon_info', 'makedate', 'maketime', 'mimetype_icon', 'month_overview', 'pdf', 'roman', 'topmenu', 'treemenu' ),
            'Strings' => array( 'append', 'autolink', 'begins_with', 'break', 'chr', 'compare', 'concat', 'contains', 'count_chars', 'count_words', 'crc32', 'downcase', 'ends_with', 'explode', 'extract', 'extract_left', 'extract_right', 'indent', 'insert', 'md5', 'nl2br', 'ord', 'pad', 'prepend', 'remove', 'repeat', 'replace', 'reverse', 'rot13', 'shorten', 'simpletags', 'simplify', 'trim', 'upcase', 'upfirst', 'upword', 'wash', 'wordtoimage', 'wrap' ),
            'URLs' => array( 'exturl', 'ezdesign', 'ezimage', 'ezroot', 'ezurl' ),
            'Variable-and-type-handling' => array('count', 'float', 'get_class', 'get_type', 'int', 'is_array', 'is_boolean', 'is_class', 'is_float', 'is_integer', 'is_null', 'is_numeric', 'is_object', 'is_set', 'is_string', 'is_unset' )
        );
        foreach ( $folders as $fname => $folder )
        {
            if ( in_array( $operator, $folder ) )
            {
                $out[] = $fname;
            }
        }
        return $out;
    }

    static $ezpClasses = false;
}
?>
