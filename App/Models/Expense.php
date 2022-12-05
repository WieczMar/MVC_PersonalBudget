<?php

namespace App\Models;

use PDO;

class Expense extends \Core\Model
{
    public function __construct($data = [])
    {
        foreach ($data as $key => $value) {
                $this->$key = $value;
        };
    }

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

    public static function getExpenses($userId, $startDate, $endDate)
    {
        try {

            $db = static::getDB();

            $statement = $db->prepare("SELECT name AS categoryName, SUM(amount) AS categoryAmount FROM expenses, expenses_category_assigned_to_users 
            WHERE expenses.user_id = :userId AND expenses.expense_category_assigned_to_user_id=expenses_category_assigned_to_users.id 
            AND expenses.date_of_expense BETWEEN :startDate AND :endDate GROUP BY categoryName ORDER BY categoryAmount DESC");
            $statement->bindValue(':userId', $userId, PDO::PARAM_INT);
            $statement->bindValue(':startDate', $startDate, PDO::PARAM_STR);
            $statement->bindValue(':endDate', $endDate, PDO::PARAM_STR);
            $statement->execute();

            $expenses = $statement->fetchAll(PDO::FETCH_ASSOC);

            return $expenses;
            
        } catch (PDOException $e) {
            echo $e->getMessage();
        }
    }

    public function addNewExpense($userId) 
    {
        try {

            $db = static::getDB();

            $statement = $db->prepare("INSERT INTO expenses VALUES (NULL, :userId, :categoryId, :paymentMethodId, :amount, :date, :comment)");
            $statement->bindValue(':userId', $userId, PDO::PARAM_INT);
            $statement->bindValue(':categoryId', $this->categoryId, PDO::PARAM_INT);
            $statement->bindValue(':paymentMethodId', $this->paymentMethodId, PDO::PARAM_INT);
            $statement->bindValue(':amount', $this->amount, PDO::PARAM_STR);
            $statement->bindValue(':date', $this->date, PDO::PARAM_STR);
            $statement->bindValue(':comment', $this->comment, PDO::PARAM_STR);
            
            return $statement->execute();
            
        } catch (PDOException $e) {
            echo $e->getMessage();
        }

        return false;
    }

}

