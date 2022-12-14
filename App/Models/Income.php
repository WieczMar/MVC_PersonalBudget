<?php

namespace App\Models;

use PDO;

class Income extends \Core\Model
{
    public function __construct($data = [])
    {
        foreach ($data as $key => $value) {
                $this->$key = $value;
        };
    }

    public static function getIncomeCategories($userId)
    {
        $db = static::getDB();

        $statement = $db->prepare("SELECT id, name FROM incomes_category_assigned_to_users WHERE user_id = :userId");
        $statement->bindValue(':userId', $userId, PDO::PARAM_INT);
        $statement->execute();

        $incomeCategories = $statement->fetchAll(PDO::FETCH_ASSOC);

        return $incomeCategories;
            
    }

    public static function getIncomes($userId, $startDate, $endDate)
    {
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

    }

    public function addNewIncome($userId)
    {
        $db = static::getDB();

        $statement = $db->prepare("INSERT INTO incomes VALUES (NULL, :userId, :categoryId, :amount, :date, :comment)");
        $statement->bindValue(':userId', $userId, PDO::PARAM_INT);
        $statement->bindValue(':categoryId', $this->categoryId, PDO::PARAM_INT);
        $statement->bindValue(':amount', $this->amount, PDO::PARAM_STR);
        $statement->bindValue(':date', $this->date, PDO::PARAM_STR);
        $statement->bindValue(':comment', $this->comment, PDO::PARAM_STR);
        
        return $statement->execute();
            
    }
}

