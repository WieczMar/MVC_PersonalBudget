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

    // Send email with reset link with token
    public function requestReset()
    {
        if(isset($_POST['email']))
        {
            User::sendPasswordReset($_POST['email']);
            Flash::addMessage('isEmailSent' , 'Reset password request has been sent. Check your email inbox.', Flash::INFO);
        }
        $this->redirect('/password/index');

    }

    // Show the reset password form
    public function resetAction()
    {
        $token = $this->route_params['token'];

        $user = $this->getUserOrExit($token);

        View::renderTemplate('Password/resetPassword.html', [
            'token' => $token
        ]);
    }

    // Reset the user's password
    public function resetPasswordAction()
    {
        $token = $_POST['token'];

        $user = $this->getUserOrExit($token);

        if ($user->resetPassword($_POST['password'])) {

            View::renderTemplate('Password/resetSuccess.html');

        } else {
            
            Flash::addMessage('user' , $user, Flash::WARNING);
            View::renderTemplate('Password/resetPassword.html', [
                'token' => $token
            ]);
        }
    }

    // Find the user model associated with the password reset token, or end the request with a message
    protected function getUserOrExit($token)
    {
        $user = User::findByPasswordReset($token);

        if ($user) {

            return $user;

        } else {

            View::renderTemplate('Password/tokenExpired.html');
            exit;

        }
    }
}
