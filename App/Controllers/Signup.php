<?php

namespace App\Controllers;

use \Core\View;
use \App\Models\User;
use \App\Flash;

// Signup controller
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
    
                $user->sendActivationEmail();
                Flash::addMessage('registrationCompleted' , 'You have successfully signed up!', Flash::SUCCESS);
                Flash::addMessage('registrationEmail' , 'Please check your email to activate your account.', Flash::SUCCESS);
    
            } else {
                Flash::addMessage('user' , $user, Flash::WARNING);
            }
        } 
        $this->redirect('/signup/index');
    }

    // Activate a new account
    public function activateAction()
    {
        User::activate($this->route_params['token']);

        $this->redirect('/signup/activated');        
    }

    // Show the activation success page
    public function activatedAction()
    {
        View::renderTemplate('Signup/activationSuccess.html');
    }

}
