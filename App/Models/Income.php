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

        $statement = $db->prepare("SELECT incomes_category_assigned_to_users.id AS categoryId, name AS categoryName, SUM(amount) AS categoryAmount FROM incomes, incomes_category_assigned_to_users 
        WHERE incomes.user_id = :userId AND incomes.income_category_assigned_to_user_id = incomes_category_assigned_to_users.id 
        AND incomes.date_of_income BETWEEN :startDate AND :endDate GROUP BY categoryName, incomes_category_assigned_to_users.id ORDER BY categoryAmount DESC");
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

    public static function getIncomeDetailsInCategoryForSelectedDates($categoryId, $startDate, $endDate)
    {
        $userId = $_SESSION['userId'];

        $db = static::getDB();

        $statement = $db->prepare("SELECT id, amount, date_of_income AS date, income_comment AS comment FROM incomes 
        WHERE user_id = :userId AND income_category_assigned_to_user_id = :categoryId
        AND date_of_income BETWEEN :startDate AND :endDate ORDER BY amount DESC");
        $statement->bindValue(':userId', $userId, PDO::PARAM_INT);
        $statement->bindValue(':categoryId', $categoryId, PDO::PARAM_INT);
        $statement->bindValue(':startDate', $startDate, PDO::PARAM_STR);
        $statement->bindValue(':endDate', $endDate, PDO::PARAM_STR);
        $statement->execute();

        return $statement->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function getIncomeCategoryName($categoryId)
    {
        $userId = $_SESSION['userId'];

        $db = static::getDB();

        $statement = $db->prepare("SELECT name FROM incomes_category_assigned_to_users 
        WHERE user_id = :userId AND id = :categoryId");
        $statement->bindValue(':userId', $userId, PDO::PARAM_INT);
        $statement->bindValue(':categoryId', $categoryId, PDO::PARAM_INT);
        $statement->execute();

        return $statement->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function editIncomeCategory($data)
    {
        $userId = $_SESSION['userId'];
        $categoryId = $data->id;
        $name = $data->name;

        $db = static::getDB();

        $statement = $db->prepare("UPDATE incomes_category_assigned_to_users SET name = :name 
        WHERE user_id = :userId AND id = :categoryId");
        $statement->bindValue(':userId', $userId, PDO::PARAM_INT);
        $statement->bindValue(':categoryId', $categoryId, PDO::PARAM_INT);
        $statement->bindValue(':name', $name, PDO::PARAM_STR);

        return $statement->execute();
    }

    public static function getIncomeCategoryIdByName($name)
    {
        $userId = $_SESSION['userId'];

        $db = static::getDB();

        $statement = $db->prepare("SELECT id FROM incomes_category_assigned_to_users 
        WHERE user_id = :userId AND name = :name");
        $statement->bindValue(':userId', $userId, PDO::PARAM_INT);
        $statement->bindValue(':name', $name, PDO::PARAM_STR);
        $statement->execute();

        return $statement->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function addIncomeCategory($data)
    {
        $userId = $_SESSION['userId'];
        $name = $data->name;

        $db = static::getDB();

        $statement = $db->prepare("INSERT INTO incomes_category_assigned_to_users VALUES (NULL, :userId, :name)");
        $statement->bindValue(':userId', $userId, PDO::PARAM_INT);
        $statement->bindValue(':name', $name, PDO::PARAM_STR);
        
        return $statement->execute();
    }

    public static function getIncomeDetailsInCategory($categoryId)
    {
        $userId = $_SESSION['userId'];

        $db = static::getDB();

        $statement = $db->prepare("SELECT amount, date_of_income AS date, income_comment AS comment FROM incomes 
        WHERE user_id = :userId AND income_category_assigned_to_user_id = :categoryId ORDER BY amount DESC");
        $statement->bindValue(':userId', $userId, PDO::PARAM_INT);
        $statement->bindValue(':categoryId', $categoryId, PDO::PARAM_INT);
        $statement->execute();

        return $statement->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function deleteIncomeCategory($categoryId)
    {
        $userId = $_SESSION['userId'];

        $db = static::getDB();

        $statement = $db->prepare("DELETE FROM incomes_category_assigned_to_users WHERE user_id = :userId AND id = :categoryId");
        $statement->bindValue(':userId', $userId, PDO::PARAM_INT);
        $statement->bindValue(':categoryId', $categoryId, PDO::PARAM_INT);

        return $statement->execute();
    }

    

    

}

