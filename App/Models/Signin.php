<?php

namespace App\Models;

use PDO;

class Signin extends \Core\Model
{

    /**
     * Get all the posts as an associative array
     *
     * @return array
     */
    public static function getUser($email)
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
}
