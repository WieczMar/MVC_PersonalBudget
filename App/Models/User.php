<?php

namespace App\Models;

use PDO;
use \App\Flash;
use \App\Token;
use \App\Mail;
use \Core\View;

class User extends \Core\Model
{
    public $errors = [];

    public function __construct($data = [])
    {
        foreach ($data as $key => $value) {
                $key = preg_replace('/-/', '_', $key); // change dash to underscore in variable names
                $this->$key = $value;

        };
    }

    public static function authenticate($email, $password)
    {
        $user = self::getUserByEmail($email);

        if ($user && $user->is_active) {
            if (password_verify($password, $user->password)) {
                return $user;
            }
        }

        return false;
    }

    public static function getByID($userId)
    {
        $sql = 'SELECT * FROM users WHERE id = :id';

        $db = static::getDB();
        $statement = $db->prepare($sql);
        $statement->bindValue(':id', $userId, PDO::PARAM_INT);

        $statement->setFetchMode(PDO::FETCH_CLASS, get_called_class());

        $statement->execute();

        return $statement->fetch();
    }

    public function saveNewUser()
    {
        $this->validateName();
        $this->validateEmail();
        $this->validatePassword();
        $this->validateRecaptcha();

        if (empty($this->errors)) {

                $hashedPassword = password_hash($this->password, PASSWORD_DEFAULT); // encrypt password (hash), PASSWORD_DEFAULT means use the best method currently known
                
                $token = new Token();
                $hashed_token = $token->getHash();
                $this->activation_token = $token->getValue();

                $db = static::getDB();
                $statement = $db->prepare("INSERT INTO users (username, password, email, activation) 
                                            VALUES (:name, :hashedPassword, :email, :hashedActivation)");
                $statement->bindValue(':name', $this->name, PDO::PARAM_STR);
                $statement->bindValue(':hashedPassword', $hashedPassword, PDO::PARAM_STR);
                $statement->bindValue(':email', $this->email, PDO::PARAM_STR);
                $statement->bindValue(':hashedActivation', $hashed_token, PDO::PARAM_STR);
                $result = $statement->execute();

                $this->userId = $this->getUserId();
                $this->setUserDefaultBudgetOptions();

                return $result;
                
        }

        return false;
    }

    private static function getUserByEmail($email)
    {
        $db = static::getDB();
        $statement = $db->prepare("SELECT * FROM users WHERE email=:email");
        $statement->bindValue(':email', $email, PDO::PARAM_STR);

        $statement->setFetchMode(PDO::FETCH_CLASS, get_called_class());

        $statement->execute();

        return $statement->fetch();

    }

    private function isEmailAlreadyUsed()
    {
        $db = static::getDB();

        $statement = $db->prepare("SELECT id FROM users WHERE email=:email");
        $statement->bindValue(':email', $this->email, PDO::PARAM_STR);
        $statement->execute();

        return $statement->fetch() !== false;

    }

    private function getUserId()
    {
        $db = static::getDB();

        $statement = $db->prepare("SELECT id FROM users WHERE email=:email");
        $statement->bindValue(':email', $this->email, PDO::PARAM_STR);
        $statement->execute();

        $user = $statement->fetchObject();

        return $user->id;
    }

    private function setUserDefaultBudgetOptions()
    {
        $db = static::getDB();

        $statement = $db->prepare("INSERT INTO incomes_category_assigned_to_users (user_id, name) SELECT :userId, name FROM incomes_category_default");
        $statement->bindValue(':userId', $this->userId, PDO::PARAM_INT);
        $statement->execute();

        $statement = $db->prepare("INSERT INTO expenses_category_assigned_to_users (user_id, name) SELECT :userId, name FROM expenses_category_default");
        $statement->bindValue(':userId', $this->userId, PDO::PARAM_INT);
        $statement->execute();

        $statement = $db->prepare("INSERT INTO payment_methods_assigned_to_users (user_id, name) SELECT :userId, name FROM payment_methods_default");
        $statement->bindValue(':userId', $this->userId, PDO::PARAM_INT);
        $statement->execute();
    }

    private function validateName()
    {
        if ((strlen($this->name) < 3) || (strlen($this->name) > 30)) 
        {
            $this->errors['name']="Name has to be at least 3 and up to 30 sign length!";
            
        }
        else if (ctype_alnum($this->name) == false) // check if argument has only alphanumeric signs
        {
            $this->errors['name'] = "Name has to be without distinctive marks";
        }
    }

    private function validateEmail()
    {
        $filteredEmail = filter_var($this->email, FILTER_SANITIZE_EMAIL); // remove forbidden signs if such exist and leave rest
        if ((filter_var($filteredEmail, FILTER_VALIDATE_EMAIL)==false) || ($filteredEmail!=$this->email)) 
        {
            $this->errors['email'] = "Type correct email address!";
        }
        else if($this->isEmailAlreadyUsed())
        {
            $this->errors['email'] = "An account with such email already exists!";
            
        }
    }

    private function validatePassword()
    {
        if ((strlen($this->password)<8) || (strlen($this->password)>30))
        {
            $this->errors['password'] = 'Password needs to have at least 6 and maximum 30 characters';
        }
        else if (preg_match('/.*[a-z]+.*/i', $this->password) == 0) {
            $this->errors['password'] = 'Password needs to have at least one letter';
        }
        else if (preg_match('/.*\d+.*/i', $this->password) == 0) {
            $this->errors['password'] = 'Password needs to have at least one number';
        }
    }

    private function validateRecaptcha()
    {
        $secretKey = "6Lfa1ZgiAAAAALP1oykI5JFBlOqiv8zT0_GsJiNP";
        $reCaptchaResponse = file_get_contents('https://www.google.com/recaptcha/api/siteverify?secret='.$secretKey.'&response='.$this->g_recaptcha_response);
        $reCaptchaResponse = json_decode($reCaptchaResponse);
        if ($reCaptchaResponse->success==false)
        {
            $this->errors['recaptcha'] = 'reCaptcha Error!';
        }	
    }

    // Remember the login by inserting a new unique token into the remembered_logins table for this user record
    public function rememberLogin()
    {
        $token = new Token();
        $token_hash = $token->getHash();
        $this->rememberToken = $token->getValue();

        $this->expiryTimestamp = time() + 60 * 60 * 24 * 30;  // 30 days from now

        $sql = 'INSERT INTO remembered_logins (token_hash, user_id, expires_at)
                VALUES (:token_hash, :user_id, :expires_at)';

        $db = static::getDB();
        $statement = $db->prepare($sql);

        $statement->bindValue(':token_hash', $token_hash, PDO::PARAM_STR);
        $statement->bindValue(':user_id', $this->id, PDO::PARAM_INT);
        $statement->bindValue(':expires_at', date('Y-m-d H:i:s', $this->expiryTimestamp), PDO::PARAM_STR);

        return $statement->execute();
    }

    
    // Send password reset instructions to the user specified
    public static function sendPasswordReset($email)
    {
        $user = static::getUserByEmail($email);

        if ($user) {

            if ($user->startPasswordReset()) {

                $user->sendPasswordResetEmail();

            }
        }
    }

    // Start the password reset process by generating a new token and expiry
    protected function startPasswordReset()
    {
        $token = new Token();
        $hashed_token = $token->getHash();
        $this->password_reset_token = $token->getValue();

        $expiry_timestamp = time() + 60 * 60 * 2;  // 1 hour from now

        $sql = 'UPDATE users
                SET password_reset = :token_hash,
                    password_reset_expires_at = :expires_at
                WHERE id = :id';

        $db = static::getDB();
        $statement = $db->prepare($sql);

        $statement->bindValue(':token_hash', $hashed_token, PDO::PARAM_STR);
        $statement->bindValue(':expires_at', date('Y-m-d H:i:s', $expiry_timestamp), PDO::PARAM_STR);
        $statement->bindValue(':id', $this->id, PDO::PARAM_INT);

        return $statement->execute();
    }

    // Send password reset instructions in an email to the user
    protected function sendPasswordResetEmail()
    {
        $url = 'http://' . $_SERVER['HTTP_HOST'] . '/password/reset/' . $this->password_reset_token;

        $htmlMessage = View::getTemplate('Password/resetPasswordEmail.html', [
            'name' => $this->name, 
            'url' => $url]);

        Mail::send($this->email, 'Personal Budget - Reset Password Request !', $htmlMessage);
    }

    //  Find a user model by password reset token and expiry
    public static function findByPasswordReset($token)
    {
        $token = new Token($token);
        $hashed_token = $token->getHash();

        $sql = 'SELECT * FROM users
                WHERE password_reset = :token_hash';

        $db = static::getDB();
        $statement = $db->prepare($sql);

        $statement->bindValue(':token_hash', $hashed_token, PDO::PARAM_STR);

        $statement->setFetchMode(PDO::FETCH_CLASS, get_called_class());

        $statement->execute();

        $user = $statement->fetch();

        if ($user) {

            // Check password reset token hasn't expired
            if (strtotime($user->password_reset_expires_at) > time()) {

                return $user;
            }
        }
    }

    //  Reset the password
    public function resetPassword($password)
    {
        $this->password = $password;

        $this->validatePassword();

        if (empty($this->errors)) {

            $hashedPassword = password_hash($this->password, PASSWORD_DEFAULT);

            $sql = 'UPDATE users
                    SET password = :hashedPassword,
                        password_reset = NULL,
                        password_reset_expires_at = NULL
                    WHERE id = :id';

            $db = static::getDB();
            $statement = $db->prepare($sql);

            $statement->bindValue(':id', $this->id, PDO::PARAM_INT);
            $statement->bindValue(':hashedPassword', $hashedPassword, PDO::PARAM_STR);

            return $statement->execute();
        }

        return false;
    }

    // Send an email to the user containing the activation link
    public function sendActivationEmail()
    {
        $url = 'http://' . $_SERVER['HTTP_HOST'] . '/signup/activate/' . $this->activation_token;

        $htmlMessage = View::getTemplate('Signup/activationEmail.html', [
            'name' => $this->name, 
            'url' => $url]);

        Mail::send($this->email, 'Personal Budget - account activation', $htmlMessage);
    }

    // Activate the user account with the specified activation token
    public static function activate($value)
    {
        $token = new Token($value);
        $hashed_token = $token->getHash();

        $sql = 'UPDATE users
                SET is_active = 1,
                    activation = null
                WHERE activation = :hashed_token';

        $db = static::getDB();
        $statement = $db->prepare($sql);

        $statement->bindValue(':hashed_token', $hashed_token, PDO::PARAM_STR);

        $statement->execute();                
    }
    
}
