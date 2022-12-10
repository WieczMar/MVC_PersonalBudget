<?php

namespace App\Controllers;

use \Core\View;
use \App\Models\User;
use \App\Auth;
use \App\Flash;

//Password controller
class Password extends \Core\Controller
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
            
            View::renderTemplate('Password/index.html');
        }
    }

    public function requestReset()
    {
        if(isset($_POST['email']))
        {
            User::sendPasswordReset($_POST['email']);
            Flash::addMessage('isEmailSent' , 'Reset password request has been sent. Check your email inbox.', Flash::INFO);
        }
        $this->redirect('/password/index');

    }
}
