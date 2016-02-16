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
     * @return array
     */
    public static function getScmInfo()
    {
        $dir = self::getScmDir();
        if (!$dir) {
            return array();
        }

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

    protected static function getScmDir()
    {
        return is_dir('./.git') ? './.git' : (is_dir('../.git') && is_file('../ezpublish/EzPublishKernel.php') ? '../.git' : false);
    }
}
