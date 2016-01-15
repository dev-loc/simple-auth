<?php

/**
 * Класс для работы с шаблонами
 */

class View
{
    private $sViewsPath = '';

    private static $oInstance = NULL;

    private $aVars = array();

    public function __construct( $sPath )
    {
        $this->sViewsPath = $sPath;
    }

    public static function Instance( $sPath = '' )	
    {
        if ( !self::$oInstance )
        {
            self::$oInstance = new View( $sPath );
        }

        return self::$oInstance;
    }

    public function SetVar( $sName, $mVal )
    {
        $this->aVars[ $sName ] = $mVal;
    }

    public function Render( $sView, $aVars = array() )
    {
        extract( $this->aVars + $aVars );

        ob_start();
        include( $this->sViewsPath . $sView . '.php' );
        $sCont = ob_get_contents();
        ob_end_clean();

        return $sCont;
    }
}
