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
     * @todo add check that 'git' is executable
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
        $dirs = self::getScmDir();
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

            $revisionInfo = array();
            exec( "cd $dir && git log -1", $revisionInfo, $retcode );

            $statusInfo = array();
            exec( "cd $dir && git status", $statusInfo, $retcode );

            $tagInfo = array();
            exec( "cd $dir && git describe", $tagInfo, $retcode );

            $out[$name] = array(
                'revision_info' => $revisionInfo,
                'status_info' => $statusInfo,
                'tag_info' => $tagInfo
            );
        }

        return $out;
    }

    /**
     * @return false|array|string
     */
    protected static function getScmDir()
    {
        $ini = eZINI::instance( 'sysinfo.ini' );
        $dir = $ini->variable('SCMSettings', 'RepoDir');
        if ($dir !== '')
        {
            return $dir;
        }

        return is_dir( './.git' ) ? './.git' : ( is_dir( '../.git' ) && is_file( '../ezpublish/EzPublishKernel.php' ) ? '../.git' : false );
    }
}
