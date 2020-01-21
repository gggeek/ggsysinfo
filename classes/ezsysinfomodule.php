<?php
/**
 * Class used to help managing in a single place all module/view info
 *
 * @author G. Giunta
 * @copyright (C) G. Giunta 2010-2020
 * @license Licensed under GNU General Public License v2.0. See file license.txt
 */

class ezSysinfoModule
{

    static $initialized = false;

    /**
    * Structure used by eZP module view definitions, augmented somewhat by:
    * - title / name ???
    * - description
    * - disabled (this one is calculated by the initialize() function
    * - hidden (for left menu)
    */
    static $view_groups = array();

    protected static function initialize( $force=false )
    {
        if ( self::$initialized && !$force )
        {
            return;
        }

        $ini = eZINI::instance( 'sysinfo.ini' );
        foreach( $ini->variable( 'ModuleSettings', 'GroupsList' ) as $groupName => $class )
        {
            /// @todo check that interface is implemented
            self::$view_groups[$groupName] = call_user_func( array( $class, 'groupList' ) );
        }

        self::$initialized = true;
    }

    static function groupList()
    {
        self::initialize();
        return self::$view_groups;
    }

    static function viewList( $group='' )
    {
        self::initialize();
        $viewlist = array();
        if ( $group == '' )
        {
            foreach( self::$view_groups as $views )
            {
                $viewlist = array_merge( $viewlist, $views );
            }
        }
        else if ( isset( self::$view_groups[$group] ) )
        {
            $viewlist = self::$view_groups[$group];
        }
        return $viewlist;
    }

    // we use name if title is missing
    static function viewTitle( $viewName )
    {
        foreach( self::$view_groups as $views )
        {
            if ( array_key_exists( $viewName, $views ) )
            {
                return isset( $views[$viewName]['title'] ) ? $views[$viewName]['title'] : $views[$viewName]['name'];
            }
        }
        return 'title';
    }

    static function viewName( $viewName )
    {
        foreach( self::$view_groups as $views )
        {
            if ( array_key_exists( $viewName, $views ) )
            {
                return $views[$viewName]['name'];
            }
        }
        return 'title-for-path';
    }

    static function viewDescription( $viewName )
    {
        foreach( self::$view_groups as $views )
        {
            if ( array_key_exists( $viewName, $views ) )
            {
                return isset( $views[$viewName]['description'] ) ? $views[$viewName]['description'] : $views[$viewName];
            }
        }
        return '';
    }

    /// true if view is neither hidden nor disabled
    static function viewActive( $viewName )
    {
        foreach( self::$view_groups as $views )
        {
            if ( array_key_exists( $viewName, $views ) )
            {
                return !@$views[$viewName]['disabled'] && !@$views[$viewName]['hidden'];
            }
        }
        return false;
    }

}
