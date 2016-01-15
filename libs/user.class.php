<?php

/**
 * Класс для работы с пользователем
 */

class User
{
    private static $oInstance = NULL;

    private $oDb   = NULL; // Экземпляр класса DB

    // Время жизни сессии в секундах
    private $nTTL  = 3600; 

    // Интервал времени в течение которого считаются неудачные 
    // попытки входа, в секундах.
    // Т.е. если в течение $nFailRangeTime секунд $nFailTimes раз были введены
    // неправильные логин/пароль, то вход блокируется на $nBlockTime секунд
    private $nFailRangeTime = 60; 

    // Время блокирования при неудачных попытках входа, в секундах
    private $nBlockTime = 300;

    // Количество неудачных попыток логина
    private $nFailTimes = 3;

    // Оставшееся время блокировки
    private $nWait = 0;

    // Данные активного пользователя
    private $aUser = NULL; 

    public function __construct()
    {
        $this->oDb = Db::Instance();
    }

    /**
     * Получаем экземпляр класса
     */
    public static function Instance()
    {
        if ( !self::$oInstance )
        {
            self::$oInstance = new User();
        }

        return self::$oInstance;
    }

    /**
     * Пытаемся залогиниться, если возможно. 
     * Неудачные попытки сохраняем в таблице failed_logins.
     */
    public function LogIn( $sLogin, $sPassw )
    {
        $bResult = FALSE;

        if ( $this->IsLoginAvailable() )
        {
            if ( !( $bResult = $this->_DoLogIn( $sLogin, $sPassw ) ) )
            {
                 $this->oDb->Query(
                    "INSERT INTO failed_logins SET
                        ip_addr    = '". $_SERVER['REMOTE_ADDR'] ."',
                        login_time = " . time()
                 );
            }
        }

        return $bResult;
    }

    /**
     * Cоздаем сессию, если логин/пароль корректны.
     */
    private function _DoLogIn( $sLogin, $sPassw )
    {
        $this->aUser = NULL;

        $aUser = $this->oDb->GetAll(
           "SELECT * 
            FROM users 
            WHERE login = '". $this->oDb->Safe($sLogin) ."' AND 
                  password = '". md5($sPassw) ."'"
        );

        if ( $aUser )
        {
             $this->aUser = array_shift($aUser);

             $sSessId = md5( $sLogin . mt_rand() . time() );

             $this->oDb->Query(
                "REPLACE INTO sessions SET
                    id         = '{$sSessId}',
                    user_id    = '". $this->oDb->Safe( $this->aUser['id'] ) ."',
                    login_time = " . time()
             );

             setcookie( 'sess_id', $sSessId, 0, '/' );

             // Пользователь залогинился, значит предыдущие ошибки логина/пароля неактуальны
             $this->ClearFailedLogins( $_SERVER['REMOTE_ADDR'] );

             $bResult = TRUE;
        }

        return !empty( $this->aUser );
    }

    /**
     * Удаляем записи о неудачных попытках логина для указанного IP
     */
    private function ClearFailedLogins( $sIpAddr )
    {
        $this->oDb->Query(
           "DELETE FROM failed_logins 
            WHERE ip_addr = '". $this->oDb->Safe( $sIpAddr ) ."'"
        );
    }

    /**
     * Проверяем, разрешено ли пользователю логиниться
     */
    private function IsLoginAvailable()
    {
        // Оставшееся время блокировки
        $this->nWait = 0;
//$this->nFailRangeTime = 300;

        $aFails = $this->oDb->GetAll(
           "SELECT login_time
            FROM failed_logins
            WHERE ip_addr = '". $this->oDb->Safe( $_SERVER['REMOTE_ADDR'] ) ."' AND
                  login_time > " . ( time() - $this->nFailRangeTime ) . "
            ORDER BY login_time DESC
            LIMIT " . (int) $this->nFailTimes
        );

        if ( count($aFails) >= $this->nFailTimes )
        {
            $aFail = array_shift($aFails);

            $this->nWait = $aFail['login_time'] + $this->nBlockTime - time();
        }

        return empty( $this->nWait );
    }

    /**
     * Получаем время оставшейся блокировки пользователя в секундах
     */
    public function GetWaitTime()
    {
        $this->IsLoginAvailable();

        return $this->nWait;
    }

    public function LogOut()
    {
        if ( $sId = @$_COOKIE['sess_id'] )
        {
            $this->oDb->Query(
               "DELETE FROM sessions 
                WHERE id = '" . $this->oDb->Safe($sId) . "'"
            );
        }

        setcookie( 'sess_id', FALSE );
    }

    /**
     * Получаем информацию о текущем залогиненном пользователе
     */
    public function GetLoggedUser()
    {
        if ( empty($this->aUser) )
        {
            $aUser = $this->oDb->GetAll(
               "SELECT u.* 
                FROM sessions s
                     LEFT JOIN users u ON s.user_id = u.id
                WHERE s.id = '". $this->oDb->Safe( @$_COOKIE['sess_id'] ) ."' AND
                      login_time > " . ( time() - $this->nTTL )
            );

            $this->aUser = !empty($aUser) ? array_shift( $aUser ): NULL;
        }

        return $this->aUser;
    }

    /**
     * Shortcut: Пользователь залогинен? 
     */
    public function IsLoggedIn()
    {
        $mUsr = $this->GetLoggedUser();

        return isset($mUsr['id']);
    }

    /**
     * Shortcut: Получаем имя пользователя 
     */
    public function GetUserName()
    {
        $mUsr = $this->GetLoggedUser();

        return isset($mUsr['login']) ? $mUsr['login']: NULL;
    }

    /**
     * Валидация пароля
     */
    public function Validate( $sOldPassw, $sPassw1, $sPassw2 )
    {
        $sError = '';

        $aUser = $this->GetLoggedUser();

        if ( $aUser['password'] != md5($sOldPassw) )
        {
            $sError = 'Старый пароль некорректен';
        }
        elseif ( $sPassw1 == '' )
        {
            $sError = 'Укажите новый пароль';
        }
        elseif ( mb_strlen( $sPassw1 ) < 8 )
        {
            $sError = 'Длина пароля должна быть не менее 8 символов';
        }
        elseif (
            !( preg_match( "#[a-zа-яё]+#u", $sPassw1 ) &&
               preg_match( "#[A-ZА-ЯЁ]+#u", $sPassw1 ) )
        )
        {
            $sError = 'Пароль должен содержать маленькие и большие буквы';
        }
        elseif ( $sPassw1 != $sPassw2 )
        {
            $sError = '"Новый пароль" и "Повторить пароль" должны быть одинаковыми';
        }

        return $sError;
    }

    /**
     * Устанавливаем пароль 
     */
    public function SetPassword( $sPassw )
    {
        $mUsr = $this->GetLoggedUser();

        if ( !empty( $mUsr['id'] ) )
        {
            $this->oDb->Query(
               "UPDATE users SET
                   password = '" . md5($sPassw) . "'
                WHERE id = " . $this->oDb->Safe( $mUsr['id'] )
            );
        }
    }
    
}
