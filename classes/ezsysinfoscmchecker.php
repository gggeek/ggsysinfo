<?php

class eZSysinfoSCMChecker implements ezSysinfoReport
{
    public function getReport()
    {

    }

    /**
     * @return array:
     *         - 'tag'
     *         - 'title'
     *         - 'executingString'
     *         - 'format' byrow, byline or html for now
     */
    public function getDescription()
    {

    }

    /**
     * @return bool
     */
    public static function hasScmInfo()
    {
        return self::getScmDir() != false ? true : false;
    }

    /**
     * @todo add support for SVN
     * @return array each element is an array with information
     */
    public static function getScmInfo()
    {
        $ini = eZINI::instance( 'sysinfo.ini' );
        $type = $ini->variable('SCMSettings', 'RepoType');

        $dirs = self::getScmDir($type);
        if (!$dirs) {
            return array();
        }

        if ( is_string( $dirs ) )
        {
            $dirs = array( $dirs );
        }

        $out = array();
        foreach( $dirs as $name => $dir )
        {
            if ( !is_dir( $dir) )
            {
                eZDebug::writeWarning( "'$dir' is not a directory, can not get SCM info", __METHOD__ );
                continue;
            }

            switch( $type )
            {
                case 'git':
                    $out[$name] = static::getRevisionInfoFromGit($dir);
                    break;
                case 'file':
                    $out[$name] = static::getRevisionInfoFromFiles($dir);
                    break;
                default:
                    eZDebug::writeError( "Unsupported scm type '$type'", __METHOD__ );
            }
        }

        return $out;
    }

    /**
     * @param string $dir
     * @return array
     * @todo add check that 'git' is executable
     */
    protected static function getRevisionInfoFromGit( $dir )
    {
        $revisionInfo = array();
        exec( "cd $dir && git log -1", $revisionInfo, $retcode );

        $statusInfo = array();
        exec( "cd $dir && git status", $statusInfo, $retcode );

        $tagInfo = array();
        exec( "cd $dir && git describe", $tagInfo, $retcode );

        return array(
            'revision_info' => $revisionInfo,
            'status_info' => $statusInfo,
            'tag_info' => $tagInfo
        );
    }

    protected static function getRevisionInfoFromFiles( $dir )
    {
        $revisionInfo = array();
        if ( is_file( "$dir/revision.txt" ) )
        {
            $revisionInfo = file( "$dir/revision.txt", FILE_IGNORE_NEW_LINES );
        }

        $statusInfo = array();
        if ( is_file( "$dir/status.txt" ) )
        {
            $revisionInfo = file( "$dir/status.txt", FILE_IGNORE_NEW_LINES );
        }

        $tagInfo = array();
        if ( is_file( "$dir/tag.txt" ) )
        {
            $revisionInfo = file( "$dir/tag.txt", FILE_IGNORE_NEW_LINES );
        }

        return array(
            'revision_info' => $revisionInfo,
            'status_info' => $statusInfo,
            'tag_info' => $tagInfo
        );
    }

    /**
     * @return false|array|string
     */
    protected static function getScmDir($type)
    {
        $ini = eZINI::instance( 'sysinfo.ini' );
        $dir = $ini->variable( 'SCMSettings', 'RepoDir' );
        if ( $dir !== '' )
        {
            return $dir;
        }

        switch($type)
        {
            case 'git':
                return is_dir( './.git' ) ? './.git' : ( is_dir( '../.git' ) && is_file( '../ezpublish/EzPublishKernel.php' ) ? '../.git' : false );
            case 'file':
            default:
                eZDebug::writeError( "Can not find info from scm type '$type' with an empty directory", __METHOD__ );
                return false;
        }
    }
}
