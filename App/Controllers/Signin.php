<?php

namespace App\Controllers;

use \Core\View;
use \App\Models\Signin as SigninModel;
//Signin controller
class Signin extends \Core\Controller
{

    /**
     * Before filter
     *
     * @return void
     */
    protected function before()
    {
        session_start();
    }

    /**
     * After filter
     *
     * @return void
     */
    protected function after()
    {
        //echo " (after)";
    }

    /**
     * Show the index page
     *
     * @return void
     */
    public function indexAction()
    {
        if ((isset($_SESSION['loggedIn']))&&($_SESSION['loggedIn']==true))
        {
            header('Location: /home/index');
            exit(); 
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

        $user = SigninModel::getUser($email);
        if($user && password_verify($password, $user->password)){
            $_SESSION['loggedIn'] = true; 
            unset($_SESSION['loginError']);

            $_SESSION['userId'] = $user->id; 
            $_SESSION['username'] = $user->username; 
            header('Location: /home/index');
        }
        else{
            $_SESSION['loginError'] = true;
            header('Location: /signin/index');
        }         
    }
}
