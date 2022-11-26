<?php

namespace App\Controllers;

use \Core\View;
use \App\Models\User;
//Signup controller
class Signup extends \Core\Controller
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
        if(!isset($_SESSION['user'])) $_SESSION['user'] = "";

        View::renderTemplate('Signup/index.html', [
            'registrationCompleted' => isset($_SESSION['registrationCompleted']),
            'user' => $_SESSION['user']
        ]);
        unset($_SESSION['registrationCompleted']);
        unset($_SESSION['user']);
    }

    public function addAction()
    {
        if(isset($_POST['email']))
        {
            $user = new User($_POST);
    
            if ($user->saveNewUser()) {
    
                //Mail::sendSignupConfirmation($this->$name, $this->$email);
                $_SESSION['registrationCompleted'] = true;
                $this->redirect('/signup/index');
                exit();
    
            } else {
                $_SESSION['user'] = $user;
                $this->redirect('/signup/index');
            }
        } else{
            $this->redirect('/signup/index');
        }
    }

}
