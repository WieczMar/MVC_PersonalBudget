<?php

namespace App\Controllers;

use \Core\View;
use \App\Models\User;
use \App\Flash;

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
        View::renderTemplate('Signup/index.html');
    }

    public function addAction()
    {
        if(isset($_POST['email']))
        {
            $user = new User($_POST);
    
            if ($user->saveNewUser()) {
    
                //$user->sendSignupConfirmation();
                Flash::addMessage('registrationCompleted' , 'You have successfully signed up!', Flash::SUCCESS);
    
            } else {
                Flash::addMessage('user' , $user, Flash::WARNING);
            }
        } 
        $this->redirect('/signup/index');
    }

}
