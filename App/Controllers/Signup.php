<?php

namespace App\Controllers;

use \Core\View;
use \App\Models\Signup as SignupModel;
//Signup controller
class Signup extends \Core\Controller
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
        if(!isset($_SESSION['incorrectName'])) {
            $_SESSION['incorrectName'] = false;
        }
        if(!isset($_SESSION['incorrectEmail'])) {
            $_SESSION['incorrectEmail'] = false;
        }
        if(!isset($_SESSION['name'])) {
            $_SESSION['name'] = false;
        }
        if(!isset($_SESSION['email'])) {
            $_SESSION['email'] = false;
        }
        if(!isset($_SESSION['password'])) {
            $_SESSION['password'] = false;
        }

        View::renderTemplate('Signup/index.html', [
            'registrationCompleted' => isset($_SESSION['registrationCompleted']),
            'incorrectName' => $_SESSION['incorrectName'],
            'incorrectNameValue' => $_SESSION['incorrectName'],
            'incorrectEmail' => $_SESSION['incorrectEmail'],
            'incorrectEmailValue' => $_SESSION['incorrectEmail'],
            'incorrectPassword' => isset($_SESSION['incorrectPassword']),
            'incorrectReCaptcha' => isset($_SESSION['incorrectReCaptcha']),
            'namePostValue' => $_SESSION['name'],
            'emailPostValue' => $_SESSION['email'],
            'passwordPostValue' => $_SESSION['password']
        ]);

        unset($_SESSION['registrationCompleted']);
        unset($_SESSION['incorrectName']);
        unset($_SESSION['incorrectEmail']);
        unset($_SESSION['incorrectPassword']);
        unset($_SESSION['incorrectReCaptcha']);
        unset($_SESSION['name']);
        unset($_SESSION['email']);
        unset($_SESSION['password']);
    }

    public function addAction()
    {
        if (isset($_POST['email'])) 
        {
            $isValidationCorrect=true; 

        // name validation
            $name = $_POST['name'];	
            if ((strlen($name)<3) || (strlen($name)>30)) 
            {
                $isValidationCorrect=false;
                $_SESSION['incorrectName']="Name has to be at least 3 and up to 30 sign length!";
            }
            if (ctype_alnum($name)==false) // check if argument has only alphanumeric signs
            {
                $isValidationCorrect=false;
                $_SESSION['incorrectName']="Name has to be without distinctive marks";
            }
    
        //email validation
            $email = $_POST['email']; 
            $filteredEmail = filter_var($email, FILTER_SANITIZE_EMAIL); // remove forbidden signs if such exist and leave rest
            if ((filter_var($filteredEmail, FILTER_VALIDATE_EMAIL)==false) || ($filteredEmail!=$email)) 
            {
                $isValidationCorrect=false;
                $_SESSION['incorrectEmail']="Type correct email address!";
            }
    
        //password validation
            $password = $_POST['password'];
            if ((strlen($password)<8) || (strlen($password)>30))
            {
                $isValidationCorrect=false;
                $_SESSION['incorrectPassword']=true;
            }
    
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT); // encrypt password (hash), PASSWORD_DEFAULT means use the best method currently known
    
        //Google recaptcha validation
            $secretKey = "6Lfa1ZgiAAAAALP1oykI5JFBlOqiv8zT0_GsJiNP";
            $reCaptchaResponse = file_get_contents('https://www.google.com/recaptcha/api/siteverify?secret='.$secretKey.'&response='.$_POST['g-recaptcha-response']);
            $reCaptchaResponse = json_decode($reCaptchaResponse);
            if ($reCaptchaResponse->success==false)
            {
                $isValidationCorrect=false;
                $_SESSION['incorrectReCaptcha']=true;
            }	
    
        //Check if email already exists
            $rowsCount = SignupModel::isEmailAlreadyUsed($email);
            if($rowsCount>0)
            {
                $isValidationCorrect=false;
                $_SESSION['incorrectEmail']="An account with such email already exists!";
            }	

            if ($isValidationCorrect==true)
            {
                SignupModel::addNewUser($name, $hashedPassword, $email);
                $_SESSION['registrationCompleted'] = true;
                //Mail::sendSignupConfirmation($name, $email);
            }

            // remember POSTed values
            $_SESSION['name'] = $_POST['name'];	
            $_SESSION['email'] = $_POST['email'];	
            $_SESSION['password'] = $_POST['password'];	

        }
        header('Location: /signup/index');
    }
}
