<?php
/**
 * @author G. Giunta
 * @copyright (C) G. Giunta 2014-2019
 * @license Licensed under GNU General Public License v2.0. See file license.txt
 */

/**
 * An helper class, used to make this extension work even when not activated
 */
class ezInactiveExtensionLoader
{
    const TYPE_SCALAR = 0;
    const TYPE_ARRAY = 0;

    /**
     * Tries to load an ini value, including the settings from this extension even when it is not active.
     * Note: when not active, the settings of this extensions are loaded with lower precedence compared to same settings
     * from other extensions.
     *
     * @param string $fileName
     * @param string $blockName
     * @param string $varName
     * @param int $type
     * @return array|mixed|null
     */
    static function getIniValue( $fileName, $blockName, $varName, $type = self::TYPE_SCALAR )
    {
        if ( $type == self::TYPE_SCALAR )
        {
            $value = null;
        }
        else
        {
            $value = array();
        }

        $ini = eZINI::instance( $fileName );
        if ( in_array( 'ggsysinfo', eZExtension::activeExtensions() ) )
        {
            return $ini->hasVariable( $blockName, $varName ) ? $ini->variable( $blockName, $varName ) : $value;
        }
        else
        {
            // load still what possible values are added from other extensions
            $value = $ini->hasVariable( $blockName, $varName ) ? $ini->variable( $blockName, $varName ) : $value;

            $ini = eZINI::fetchFromFile( __DIR__ . '/../settings/sysinfo.ini' );
            if ( $type == self::TYPE_SCALAR )
            {
                return array_merge( $value, $ini->variable( $blockName, $varName ) );
            }
            else
            {
                if ( $value !== null )
                {
                    return $value;
                }
                return  $ini->variable( $blockName, $varName );
            }
        }
    }
}