<?php

/**
 * Контроллер авторизации
 */

class AuthController
{
    // TODO: Вынести в родительский класс
    private $oUser = NULL;
    private $oView = NULL;

    public function __construct()
    {
        // TODO: Вынести в родительский класс
        $this->oUser = User::Instance();
        $this->oView = View::Instance();
    }

    public function action_index()
    {
        $this->action_login();
    }

    public function action_login()
    {
        if ( !$this->oUser->IsLoggedIn() )
        {
            if ( empty($_POST) )
            {
                $sCont = $this->oView->Render( 
                    'login', array( 'error' => '', 'sLogin' => '' )
                );
            }
            else
            {
                if ( ( $nWait = $this->oUser->GetWaitTime() ) > 0 )
                {
                    $sError = "Попробуйте еще раз через {$nWait} секунд";
                }
                elseif ( $this->oUser->LogIn( @$_POST['login'], @$_POST['passw'] ) )
                {
                    Utils::RedirectMsg('Вы успешно авторизовались', '?c=userpage');
                }
                else
                {
                    $sError = 'Неверные данные';

                    if ( ( $nWait = $this->oUser->GetWaitTime() ) > 0 )
                    {
                        $sError = "Попробуйте еще раз через {$nWait} секунд";
                    }
                }

                $sCont = $this->oView->Render( 
                    'login', array( 'error' => $sError, 'sLogin' => @$_POST['login'] )
                );
            }

            echo $this->oView->Render(
                'wrapper', 
                 array( 'sTitle' => 'Авторизация', 'sContent' => $sCont )
            );
        }
        else
        {
            Utils::Redirect('?c=userpage');
        }
    }

    public function action_logout()
    {
        $this->oUser->LogOut();

        Utils::RedirectMsg('Вы успешно вышли', '?c=auth&a=login');
    }
}
