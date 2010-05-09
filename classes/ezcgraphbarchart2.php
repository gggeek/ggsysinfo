<?php

/// work around bug #016798: we need to pass null for in-memory rendering with gd, not 'php://stdout'
class ezcGraphBarChart2 extends ezcGraphBarChart
{

    public function render( $width, $height, $file = null )
    {
        $this->renderElements( $width, $height );

        //if ( !empty( $file ) )
        //{
            $this->renderer->render( $file );
        //}

        $this->renderedFile = $file;
    }
}
?>
