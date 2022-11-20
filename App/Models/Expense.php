<?php

namespace App\Models;

use PDO;

class Expense extends \Core\Model
{
    public static function getExpenseCategories($userId)
    {
        try {

            $db = static::getDB();

            $statement = $db->prepare("SELECT id, name FROM expenses_category_assigned_to_users WHERE user_id = :userId");
            $statement->bindValue(':userId', $userId, PDO::PARAM_INT);
            $statement->execute();

            $expenseCategories = $statement->fetchAll(PDO::FETCH_ASSOC);

            return $expenseCategories;
            
        } catch (PDOException $e) {
            echo $e->getMessage();
        }
    }

    public static function getPaymentMethods($userId)
    {
        try {

            $db = static::getDB();

            $statement = $db->prepare("SELECT id, name FROM payment_methods_assigned_to_users WHERE user_id = :userId");
            $statement->bindValue(':userId', $userId, PDO::PARAM_INT);
            $statement->execute();

            $paymentMethods = $statement->fetchAll(PDO::FETCH_ASSOC);

            return $paymentMethods;
            
        } catch (PDOException $e) {
            echo $e->getMessage();
        }
    }

    public static function addNewExpense($userId, $categoryId, $paymentMethodId, $amount, $date, $comment)
    {
        try {

            $db = static::getDB();

            $statement = $db->prepare("INSERT INTO expenses VALUES (NULL, :userId, :categoryId, :paymentMethodId, :amount, :date, :comment)");
            $statement->bindValue(':userId', $userId, PDO::PARAM_INT);
            $statement->bindValue(':categoryId', $categoryId, PDO::PARAM_INT);
            $statement->bindValue(':paymentMethodId', $paymentMethodId, PDO::PARAM_INT);
            $statement->bindValue(':amount', $amount, PDO::PARAM_STR);
            $statement->bindValue(':date', $date, PDO::PARAM_STR);
            $statement->bindValue(':comment', $comment, PDO::PARAM_STR);
            $statement->execute();
            
        } catch (PDOException $e) {
            echo $e->getMessage();
        }
    }
}

