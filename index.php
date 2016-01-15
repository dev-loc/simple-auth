<?php

define('CONTROLLERS_PATH', 'controllers/');
define('VIEWS_PATH', 'views/');
define('LIBS_PATH' , 'libs/');

require_once( LIBS_PATH . 'utils.class.php' );

$aCfg = include 'config.php';

require_once( LIBS_PATH . 'db.class.php' );

// Инициализация объкта для доступа к БД
$oDb = Db::Instance(
    $aCfg['host'], $aCfg['name'], $aCfg['user'], $aCfg['pass'] 
);

require_once( LIBS_PATH . 'user.class.php' );
$oUser = User::Instance();

require_once( LIBS_PATH . 'view.class.php' );
$oView = View::Instance( VIEWS_PATH );
$oView->SetVar( 'sBaseUrl', $aCfg['base_url'] );

// Получаем имя контроллера, которое содержится в $_GET['c'].
$sController = preg_replace(
   '#\W#', '', isset( $_GET['c'] ) ? $_GET['c']: '' 
);

$sController = $sController == '' ? 'userpage': $sController;

// Формируем путь к файлу контроллера
$sContrPath = CONTROLLERS_PATH . $sController . '.php';

if ( is_file( $sContrPath ) )
{
    include( $sContrPath );

    $sControllerClass = ucfirst($sController) . 'Controller';
    $oContr = new $sControllerClass();

    $sAct = isset( $_GET['a'] ) ? $_GET['a']: 'index';

    // TODO: sanitize $sAct

    if ( method_exists( $oContr, $sAction = 'action_' . strtolower($sAct) ) )
    {
        $oContr->$sAction();
    }
    else
    {
        Utils::Error404();
    }
}

