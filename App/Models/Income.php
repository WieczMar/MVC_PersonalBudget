<?php

namespace App\Models;

use PDO;

class Income extends \Core\Model
{
    public static function getIncomeCategories($userId)
    {
        try {

            $db = static::getDB();

            $statement = $db->prepare("SELECT id, name FROM incomes_category_assigned_to_users WHERE user_id = :userId");
            $statement->bindValue(':userId', $userId, PDO::PARAM_INT);
            $statement->execute();

            $incomeCategories = $statement->fetchAll(PDO::FETCH_ASSOC);

            return $incomeCategories;
            
        } catch (PDOException $e) {
            echo $e->getMessage();
        }
    }

    public static function addNewIncome($userId, $categoryId, $amount, $date, $comment)
    {
        try {

            $db = static::getDB();

            $statement = $db->prepare("INSERT INTO incomes VALUES (NULL, :userId, :categoryId, :amount, :date, :comment)");
            $statement->bindValue(':userId', $userId, PDO::PARAM_INT);
            $statement->bindValue(':categoryId', $categoryId, PDO::PARAM_INT);
            $statement->bindValue(':amount', $amount, PDO::PARAM_STR);
            $statement->bindValue(':date', $date, PDO::PARAM_STR);
            $statement->bindValue(':comment', $comment, PDO::PARAM_STR);
            $statement->execute();
            
        } catch (PDOException $e) {
            echo $e->getMessage();
        }
    }
}

