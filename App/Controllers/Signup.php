<?php

namespace App\Controllers;

use \Core\View;
use \App\Models\Signup as SignupModel;
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
            $_SESSION['isValidationCorrect']=true; 

        //name validation
            $name = $_POST['name'];	
            self::validateName($name);
    
        //email validation
            $email = $_POST['email']; 
            self::validateEmail($email);
    
        //password validation
            $password = $_POST['password'];
            self::validatePassword($password);
    
        //Google recaptcha validation
            self::validateRecaptcha($_POST['g-recaptcha-response']);

            if ($_SESSION['isValidationCorrect']==true)
            {
                $hashedPassword = password_hash($password, PASSWORD_DEFAULT); // encrypt password (hash), PASSWORD_DEFAULT means use the best method currently known

                SignupModel::addNewUser($name, $hashedPassword, $email);
                $_SESSION['registrationCompleted'] = true;
                //Mail::sendSignupConfirmation($name, $email);
            }
            else{
                // remember POSTed values
                $_SESSION['name'] = $_POST['name'];	
                $_SESSION['email'] = $_POST['email'];	
                $_SESSION['password'] = $_POST['password'];	
            }

            unset($_SESSION['isValidationCorrect']);

        }
        header('Location: /signup/index');
    }

    private static function validateName($name)
    {
        if ((strlen($name)<3) || (strlen($name)>30)) 
        {
            $_SESSION['isValidationCorrect']=false;
            $_SESSION['incorrectName']="Name has to be at least 3 and up to 30 sign length!";
        }
        if (ctype_alnum($name)==false) // check if argument has only alphanumeric signs
        {
            $_SESSION['isValidationCorrect']=false;
            $_SESSION['incorrectName']="Name has to be without distinctive marks";
        }
    }

    private static function validateEmail($email)
    {
        $filteredEmail = filter_var($email, FILTER_SANITIZE_EMAIL); // remove forbidden signs if such exist and leave rest
        if ((filter_var($filteredEmail, FILTER_VALIDATE_EMAIL)==false) || ($filteredEmail!=$email)) 
        {
            $_SESSION['isValidationCorrect']=false;
            $_SESSION['incorrectEmail']="Type correct email address!";
        }
        else{
            //Check if email already exists
            $rowsCount = SignupModel::isEmailAlreadyUsed($email);
            if($rowsCount>0)
            {
                $_SESSION['isValidationCorrect']=false;
                $_SESSION['incorrectEmail']="An account with such email already exists!";
            }
        }
    }

    private static function validatePassword($password)
    {
        if ((strlen($password)<8) || (strlen($password)>30))
        {
            $_SESSION['isValidationCorrect']=false;
            $_SESSION['incorrectPassword']=true;
        }
    }

    private static function validateRecaptcha($postRecaptchaResponse)
    {
        $secretKey = "6Lfa1ZgiAAAAALP1oykI5JFBlOqiv8zT0_GsJiNP";
        $reCaptchaResponse = file_get_contents('https://www.google.com/recaptcha/api/siteverify?secret='.$secretKey.'&response='.$postRecaptchaResponse);
        $reCaptchaResponse = json_decode($reCaptchaResponse);
        if ($reCaptchaResponse->success==false)
        {
            $_SESSION['isValidationCorrect']=false;
            $_SESSION['incorrectReCaptcha']=true;
        }	
    }

}
