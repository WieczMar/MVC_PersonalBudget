<?php

namespace App\Models;

use PDO;

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

        if ($user) {
            if (password_verify($password, $user->password)) {
                return $user;
            }
        }

        return false;
    }

    public function saveNewUser()
    {
        $this->validateName();
        $this->validateEmail();
        $this->validatePassword();
        $this->validateRecaptcha();

        if (empty($this->errors)) {
            try {

                $db = static::getDB();

                $hashedPassword = password_hash($this->password, PASSWORD_DEFAULT); // encrypt password (hash), PASSWORD_DEFAULT means use the best method currently known
                
                $statement = $db->prepare("INSERT INTO users VALUES (NULL, :name, :hashedPassword, :email)");
                $statement->bindValue(':name', $this->name, PDO::PARAM_STR);
                $statement->bindValue(':hashedPassword', $hashedPassword, PDO::PARAM_STR);
                $statement->bindValue(':email', $this->email, PDO::PARAM_STR);
                $result = $statement->execute();

                $this->userId = $this->getUserId();
                $this->setUserDefaultBudgetOptions();

                return $result;
                
            } catch (PDOException $e) {
                echo $e->getMessage();
            }
        }

        return false;
    }

    private static function getUserByEmail($email)
    {
        try {

            $db = static::getDB();
            $statement = $db->prepare("SELECT * FROM users WHERE email=:email");
            $statement->bindValue(':email', $email, PDO::PARAM_STR);
            $statement->execute();
            $user = $statement->fetchObject();

            return $user;

        } catch (PDOException $e) {
            echo $e->getMessage();
        }

    }

    private function isEmailAlreadyUsed()
    {
        try {

            $db = static::getDB();

            $statement = $db->prepare("SELECT id FROM users WHERE email=:email");
            $statement->bindValue(':email', $this->email, PDO::PARAM_STR);
            $statement->execute();

            return $statement->fetch() !== false;

        } catch (PDOException $e) {
            echo $e->getMessage();
        }
    }

    private function getUserId()
    {
        try {

            $db = static::getDB();

            $statement = $db->prepare("SELECT id FROM users WHERE email=:email");
            $statement->bindValue(':email', $this->email, PDO::PARAM_STR);
            $statement->execute();

            $user = $statement->fetchObject();

            return $user->id;

        } catch (PDOException $e) {
            echo $e->getMessage();
        }
    }

    private function setUserDefaultBudgetOptions()
    {
        try {

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

        } catch (PDOException $e) {
            echo $e->getMessage();
        }
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
}
