<?php

namespace App\Controllers;

use \Core\View;

//Signout controller
class Signout extends \Core\Controller
{
    //Before filter
    protected function before()
    {}

    //After filter
    protected function after()
    {}

    //Show the index page
    public function indexAction()
    {   
        //Unset all of the session variables
        session_unset(); 

        //Delete the session cookie
        if (ini_get('session.use_cookies')) {
            $params = session_get_cookie_params();

            setcookie(
                session_name(),
                '',
                time() - 42000,
                $params['path'],
                $params['domain'],
                $params['secure'],
                $params['httponly']
            );
        }

        //Finally destroy the session
        session_destroy();

        $this->redirect('/home/index');
    }
}
