<?php

namespace App\Controllers;

use \Core\View;
use \App\Models\User;
//Signup controller
class Signup extends \Core\Controller
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
        View::renderTemplate('Signup/index.html', [
            'registrationCompleted' => isset($_SESSION['registrationCompleted'])
        ]);
        unset($_SESSION['registrationCompleted']);
    }

    public function addAction()
    {
        if(isset($_POST['email']))
        {
            $user = new User($_POST);
    
            if ($user->saveNewUser()) {
    
                //Mail::sendSignupConfirmation($this->$name, $this->$email);
                $_SESSION['registrationCompleted'] = true;
                header('Location: http://'.$_SERVER['HTTP_HOST'] .'/signup/index');
                exit();
    
            } else {
                View::renderTemplate('Signup/index.html', [
                    'user' => $user
                ]);
            }
        } else{
            header('Location: http://'.$_SERVER['HTTP_HOST'] .'/signup/index');
        }
    }

}
