<?php

namespace App\Models;

use PDO;

class Signup extends \Core\Model
{
    public static function addNewUser($name, $hashedPassword, $email)
    {
        try {

            $db = static::getDB();

            $statement = $db->prepare("INSERT INTO users VALUES (NULL, :name, :hashedPassword, :email)");
            $statement->bindValue(':name', $name, PDO::PARAM_STR);
            $statement->bindValue(':hashedPassword', $hashedPassword, PDO::PARAM_STR);
            $statement->bindValue(':email', $email, PDO::PARAM_STR);
            $statement->execute();

            $userId = self::getUserId($email);
            self::setUserDefaultBudgetOptions($userId);
            
        } catch (PDOException $e) {
            echo $e->getMessage();
        }
    }

    public static function isEmailAlreadyUsed($email)
    {
        try {

            $db = static::getDB();

            $statement = $db->prepare("SELECT id FROM users WHERE email=:email");
            $statement->bindValue(':email', $email, PDO::PARAM_STR);
            $statement->execute();

            $rowsCount = $statement->fetchColumn(); 

            return $rowsCount;

        } catch (PDOException $e) {
            echo $e->getMessage();
        }
    }

    private static function getUserId($email)
    {
        try {

            $db = static::getDB();

            $statement = $db->prepare("SELECT id FROM users WHERE email=:email");
            $statement->bindValue(':email', $email, PDO::PARAM_STR);
            $statement->execute();

            $user = $statement->fetchObject();

            return $user->id;

        } catch (PDOException $e) {
            echo $e->getMessage();
        }
    }

    private static function setUserDefaultBudgetOptions($userId)
    {
        try {

            $db = static::getDB();

            $statement = $db->prepare("INSERT INTO incomes_category_assigned_to_users (user_id, name) SELECT :userId, name FROM incomes_category_default");
            $statement->bindValue(':userId', $userId, PDO::PARAM_INT);
            $statement->execute();

            $statement = $db->prepare("INSERT INTO expenses_category_assigned_to_users (user_id, name) SELECT :userId, name FROM expenses_category_default");
            $statement->bindValue(':userId', $userId, PDO::PARAM_INT);
            $statement->execute();

            $statement = $db->prepare("INSERT INTO payment_methods_assigned_to_users (user_id, name) SELECT :userId, name FROM payment_methods_default");
            $statement->bindValue(':userId', $userId, PDO::PARAM_INT);
            $statement->execute();

        } catch (PDOException $e) {
            echo $e->getMessage();
        }
    }
}
