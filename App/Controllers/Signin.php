<?php

namespace App\Controllers;

use \Core\View;
use \App\Models\User;
use \App\Auth;
use \App\Flash;

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
        if (isset($_SESSION['userId']))
        {
            $this->redirect('/home/index');
            exit();
        }
        else{ 

            if(!isset($_SESSION['email'])) $_SESSION['email'] = "";
            
            View::renderTemplate('Signin/index.html', [
                'email' => $_SESSION['email'],
                'rememberMe' => isset($_SESSION['rememberMe'])
            ]);
            unset($_SESSION['email']);
            unset($_SESSION['rememberMe']);
        }
    }

    //Log in a user
    public function enterAction()
    {
        if(isset($_POST['email']))
        {
            $user = User::authenticate($_POST['email'], $_POST['password']);
            $rememberMe = isset($_POST['rememberMe']);

            if ($user) 
            {
                Auth::login($user, $rememberMe);

                $this->redirect(Auth::getReturnToPage());
                exit();

            } else {
                $_SESSION['email'] = $_POST['email'];
                if($rememberMe) $_SESSION['rememberMe'] = true;
            }   
        } 
        Flash::addMessage('loginError' , 'Wrong email or password!', Flash::WARNING);
        $this->redirect('/signin/index');

    }
}
