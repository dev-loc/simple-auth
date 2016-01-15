<?php

/**
 * Контроллер Страницы пользователя
 */

class UserpageController
{
    // TODO: Вынести в родительский класс
    private $oUser = NULL;
    private $oView = NULL;

    public function __construct()
    {
        // TODO: Вынести в родительский класс
        $this->oUser = User::Instance();
        $this->oView = View::Instance();

        if ( !$this->oUser->IsLoggedIn() )
        {
            Utils::Redirect('?c=auth&a=login');
        }
    }

    /**
     * Главная страница
     */
    public function action_index()
    {
        $sCont = $this->oView->Render(
            'user_page', array( 'sUserName' => $this->oUser->GetUserName() )
        );

        echo $this->oView->Render(
            'wrapper', 
             array( 'sTitle' => 'Страница пользователя :: Главная', 'sContent' => $sCont )
        );
    }

    /**
     * Изменение пароля
     */
    public function action_passwd()
    {
        $sError = '';

        if ( !empty($_POST) )
        {
            $sPassw1 = @$_POST['passw1'];

            // Проверяем корректность введенных данных
            $sError = $this->oUser->Validate( 
                @$_POST['old_passw'], $sPassw1, @$_POST['passw2']
            );

            if ( $sError == '' )
            {
                $this->oUser->SetPassword( $sPassw1 );

                Utils::RedirectMsg('Пароль был изменен', '');
            }
        }

        $sCont = $this->oView->Render(
            'passwd', array( 'error' => $sError )
        );

        echo $this->oView->Render(
            'wrapper', 
             array( 'sTitle' => 'Страница пользователя :: Поменять пароль', 'sContent' => $sCont )
        );
    }
}