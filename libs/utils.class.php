<?php

/**
 * Инструменты
 */

class Utils
{
    static public function Redirect( $sUrl )
    {
        header( 'Location: ' . $sUrl );
        exit;
    }

    static public function RedirectMsg( $sMsg, $sUrl )
    {
        echo View::Instance()->Render(
            'wrapper_redirect', 
             array(
                 'sTitle'       => 'Перенаправление...',
                 'sRedirectUrl' => $sUrl,
                 'sMsg'         => $sMsg,
             )
        );

        exit;
    }

    static public function Error404()
    {
        header('HTTP/1.0 404 Not Found');
        exit;
    }
}

