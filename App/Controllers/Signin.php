<?php

namespace App\Controllers;

use \Core\View;
use \App\Models\User;

//Signin controller
class Signin extends \Core\Controller
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
        if ((isset($_SESSION['loggedIn']))&&($_SESSION['loggedIn']==true))
        {
            $this->redirect('/home/index');
            exit();
        }
        else{ 

            if(!isset($_SESSION['email'])) $_SESSION['email'] = "";
            
            View::renderTemplate('Signin/index.html', [
                'loginError' => isset($_SESSION['loginError']),
                'email' => $_SESSION['email']
            ]);
            unset($_SESSION['loginError']);
            unset($_SESSION['email']);
        }
    }

    public function enterAction()
    {
        if(isset($_POST['email']))
        {
            $user = User::authenticate($_POST['email'], $_POST['password']);

             if ($user) 
             {
                session_regenerate_id(true); // generate new session id and prevent session fixation attack

                $_SESSION['loggedIn'] = true; 
                unset($_SESSION['loginError']);
                $_SESSION['userId'] = $user->id; 
                $_SESSION['username'] = $user->username; 
                $this->redirect('/home/index');
                exit();

            } else {
                $_SESSION['email'] = $_POST['email'];
                $_SESSION['loginError'] = true;
                $this->redirect('/signin/index');
            }   
        } else {

            $this->redirect('/signin/index');
        }
    }
}
