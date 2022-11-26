<?php

namespace App\Controllers;

use \Core\View;
use \App\Models\User;

//Signin controller
class Signin extends \Core\Controller
{
    //Before filter
    protected function before()
    {
        session_start();
    }

    //After filter
    protected function after()
    {
    }

    //Show the index page
    public function indexAction()
    {
        if ((isset($_SESSION['loggedIn']))&&($_SESSION['loggedIn']==true))
        {
            header('Location: http://'.$_SERVER['HTTP_HOST'].'/home/index');
        }
        else{
            
            View::renderTemplate('Signin/index.html', ['loginError' => isset($_SESSION['loginError'])]);
            unset($_SESSION['loginError']);
        }
        
    }

    public function enterAction()
    {
        $email = $_POST['email'];
        $email = htmlentities($email, ENT_QUOTES, "UTF-8");
        $password = $_POST['password'];

        $user = User::getUser($email);
        if($user && password_verify($password, $user->password)){
            $_SESSION['loggedIn'] = true; 
            unset($_SESSION['loginError']);

            $_SESSION['userId'] = $user->id; 
            $_SESSION['username'] = $user->username; 
            header('Location: http://'.$_SERVER['HTTP_HOST'].'/home/index');
        }
        else{
            $_SESSION['loginError'] = true;
            header('Location: http://'.$_SERVER['HTTP_HOST'].'/signin/index');
        }         
    }
}
