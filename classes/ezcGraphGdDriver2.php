<?php
/**
 * Fix the standard ezc gd driver: it does not support rendering to string via 'php://stdout'
 */
class ezcGraphGdDriver2 extends ezcGraphGdDriver
{
    public function render( $file )
    {
        if ( $file == 'php://stdout' )
        {
            $file = null;
        }
        parent::render( $file );
    }
}

?>
