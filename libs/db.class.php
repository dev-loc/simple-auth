<?php

/**
 * Класс для работы с базой данных
 */

class Db
{
    private static $oInstance = NULL;
    private $hConn = NULL;

    public function __construct( $sHost, $sName, $sUser, $sPass )
    {
        $this->hConn = mysql_connect( $sHost, $sUser, $sPass );

        if ( !$this->hConn )
        {
           echo mysql_errno() .':'. mysql_error() .' ( line'. __LINE__ .')';
           exit;
        }
        else
        {
           $bConn = mysql_select_db( $sName, $this->hConn );

           if ( !$bConn )
           {
              echo mysql_errno() .':'. mysql_error() .' ( line'. __LINE__ .')';
              exit;
           }
        }

        mysql_query( "SET NAMES 'utf8'" );
    }

    /**
     * Получаем экземпляр класса
     */
    public static function Instance( $sHost = '', $sName = '', $sUser = '', $sPass = '' )
    {
        if ( !self::$oInstance )
        {
            self::$oInstance = new Db( $sHost, $sName, $sUser, $sPass );
        }

        return self::$oInstance;
    }

    public function Query( $sQuery )
    {
        return mysql_query( $sQuery, $this->hConn );
    }

    public function GetAll( $sQuery )
    {
        $aRes = array();

        if ( $hRes = $this->Query( $sQuery ) )
        {
            while( $aRow = mysql_fetch_assoc($hRes) )
            {
                $aRes[] = $aRow;
            }
        }

        return $aRes;
    }

    public function Safe( $sStr )
    {
        return mysql_real_escape_string( $sStr );
    }
}
