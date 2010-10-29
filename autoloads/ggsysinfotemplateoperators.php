<?php
/**
* @todo the sysinfomoduleviews should be moved to a a fetch func, maybe renamed...
*/
class ggSysinfoTemplateOperators
{

    static $operators = array(
        'installedphpcache' => array(),
        'sysinfomoduleviews' => array(),
    );

    /**
     Return an array with the template operator name.
    */
    public function operatorList()
    {
        return array_keys( self::$operators );
    }

    /**
     @return true to tell the template engine that the parameter list exists per operator type,
             this is needed for operator classes that have multiple operators.
    */
    public function namedParameterPerOperator()
    {
        return true;
    }

    /**
     @See eZTemplateOperator::namedParameterList
    */
    public function namedParameterList()
    {

        return self::$operators;
    }

    /**
    */
    public function modify( $tpl, $operatorName, $operatorParameters, $rootNamespace, $currentNamespace, &$operatorValue, $namedParameters )
    {

        switch ( $operatorName )
        {
            case 'installedphpcache':
            {
                if ( isset( $GLOBALS['_PHPA'] ) )
                {
                    $operatorValue = 'phpaccelerator';
                }
                else if ( extension_loaded( "Turck MMCache" ) )
                {
                    $operatorValue = 'mmcache';
                }
                else if ( extension_loaded( "eAccelerator" ) )
                {
                    $operatorValue = 'eaccelerator';
                }
                else if ( extension_loaded( "apc" ) )
                {
                    $operatorValue = 'apc';
                }
                else if ( extension_loaded( "Zend Performance Suite" ) )
                {
                    $operatorValue = 'performancesuite';
                }
                else if ( extension_loaded( "xcache" ) )
                {
                    $operatorValue = 'xcache';
                }
                else if ( extension_loaded( "wincache" ) )
                {
                    $operatorValue = 'wincache';
                }
                else
                {
                        $operatorValue = '';
                }
                break;
            }
            case 'sysinfomoduleviews':
                {
                    $operatorValue = sysinfoModule::groupList();
                }
                break;

        }
    }

}

?>
