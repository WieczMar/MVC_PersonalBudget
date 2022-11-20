<?php

namespace App\Models;

use PDO;

class Balance extends \Core\Model
{
    public static function getIncomes($userId, $startDate, $endDate)
    {
        try {

            $db = static::getDB();

            $statement = $db->prepare("SELECT name AS categoryName, SUM(amount) AS categoryAmount FROM incomes, incomes_category_assigned_to_users 
            WHERE incomes.user_id = :userId AND incomes.income_category_assigned_to_user_id = incomes_category_assigned_to_users.id 
            AND incomes.date_of_income BETWEEN :startDate AND :endDate GROUP BY categoryName ORDER BY categoryAmount DESC");
            $statement->bindValue(':userId', $userId, PDO::PARAM_INT);
            $statement->bindValue(':startDate', $startDate, PDO::PARAM_STR);
            $statement->bindValue(':endDate', $endDate, PDO::PARAM_STR);
            $statement->execute();

            $incomes = $statement->fetchAll(PDO::FETCH_ASSOC);

            return $incomes;
            
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

}
