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
        $db = static::getDB();

        $statement = $db->prepare("SELECT id, name, monthly_limit FROM expenses_category_assigned_to_users WHERE user_id = :userId");
        $statement->bindValue(':userId', $userId, PDO::PARAM_INT);
        $statement->execute();

        $expenseCategories = $statement->fetchAll(PDO::FETCH_ASSOC);

        return $expenseCategories;

    }

    public static function getPaymentMethods($userId)
    {
        $db = static::getDB();

        $statement = $db->prepare("SELECT id, name FROM payment_methods_assigned_to_users WHERE user_id = :userId");
        $statement->bindValue(':userId', $userId, PDO::PARAM_INT);
        $statement->execute();

        $paymentMethods = $statement->fetchAll(PDO::FETCH_ASSOC);

        return $paymentMethods;

    }

    public static function getExpenses($userId, $startDate, $endDate)
    {
        $db = static::getDB();

        $statement = $db->prepare("SELECT expenses_category_assigned_to_users.id AS categoryId, name AS categoryName, SUM(amount) AS categoryAmount FROM expenses, expenses_category_assigned_to_users 
        WHERE expenses.user_id = :userId AND expenses.expense_category_assigned_to_user_id=expenses_category_assigned_to_users.id 
        AND expenses.date_of_expense BETWEEN :startDate AND :endDate GROUP BY categoryName, expenses_category_assigned_to_users.id ORDER BY categoryAmount DESC");
        $statement->bindValue(':userId', $userId, PDO::PARAM_INT);
        $statement->bindValue(':startDate', $startDate, PDO::PARAM_STR);
        $statement->bindValue(':endDate', $endDate, PDO::PARAM_STR);
        $statement->execute();

        $expenses = $statement->fetchAll(PDO::FETCH_ASSOC);

        return $expenses;
            
    }

    public function addNewExpense($userId) 
    {
        $db = static::getDB();

        $statement = $db->prepare("INSERT INTO expenses VALUES (NULL, :userId, :categoryId, :paymentMethodId, :amount, :date, :comment)");
        $statement->bindValue(':userId', $userId, PDO::PARAM_INT);
        $statement->bindValue(':categoryId', $this->categoryId, PDO::PARAM_INT);
        $statement->bindValue(':paymentMethodId', $this->paymentMethodId, PDO::PARAM_INT);
        $statement->bindValue(':amount', $this->amount, PDO::PARAM_STR);
        $statement->bindValue(':date', $this->date, PDO::PARAM_STR);
        $statement->bindValue(':comment', $this->comment, PDO::PARAM_STR);
        
        return $statement->execute();
            
    }

    public static function getLimitOfExpensesInCategory($categoryId)
    {
        $userId = $_SESSION['userId'];

        $db = static::getDB();

        $statement = $db->prepare("SELECT monthly_limit FROM expenses_category_assigned_to_users WHERE user_id = :userId AND id = :categoryId");
        $statement->bindValue(':userId', $userId, PDO::PARAM_INT);
        $statement->bindValue(':categoryId', $categoryId, PDO::PARAM_INT);
        $statement->execute();

        return $statement->fetchAll(PDO::FETCH_ASSOC);

    }

    public static function getSumOfExpensesInCategoryForSelectedMonth($categoryId, $date)
    {
        $userId = $_SESSION['userId'];
        $month = date("m", strtotime($date));
        $year = date("Y", strtotime($date));
        $startDate = $year.'-'.$month.'-01';
        $endDate = $year.'-'.$month.'-'.cal_days_in_month(CAL_GREGORIAN,$month,$year);

        $db = static::getDB();

        $statement = $db->prepare("SELECT SUM(amount) AS categoryAmount FROM expenses 
        WHERE expenses.user_id = :userId AND expenses.expense_category_assigned_to_user_id = :categoryId
        AND expenses.date_of_expense BETWEEN :startDate AND :endDate");
        $statement->bindValue(':userId', $userId, PDO::PARAM_INT);
        $statement->bindValue(':categoryId', $categoryId, PDO::PARAM_INT);
        $statement->bindValue(':startDate', $startDate, PDO::PARAM_STR);
        $statement->bindValue(':endDate', $endDate, PDO::PARAM_STR);
        $statement->execute();

        return $statement->fetchAll(PDO::FETCH_ASSOC);

    }

    public static function getExpenseDetailsInCategoryForSelectedDates($categoryId, $startDate, $endDate)
    {
        $userId = $_SESSION['userId'];

        $db = static::getDB();

        $statement = $db->prepare("SELECT id, amount, date_of_expense AS date, expense_comment AS comment FROM expenses 
        WHERE user_id = :userId AND expense_category_assigned_to_user_id = :categoryId
        AND date_of_expense BETWEEN :startDate AND :endDate ORDER BY amount DESC");
        $statement->bindValue(':userId', $userId, PDO::PARAM_INT);
        $statement->bindValue(':categoryId', $categoryId, PDO::PARAM_INT);
        $statement->bindValue(':startDate', $startDate, PDO::PARAM_STR);
        $statement->bindValue(':endDate', $endDate, PDO::PARAM_STR);
        $statement->execute();

        return $statement->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function getExpenseCategoryName($categoryId)
    {
        $userId = $_SESSION['userId'];

        $db = static::getDB();

        $statement = $db->prepare("SELECT name FROM expenses_category_assigned_to_users 
        WHERE user_id = :userId AND id = :categoryId");
        $statement->bindValue(':userId', $userId, PDO::PARAM_INT);
        $statement->bindValue(':categoryId', $categoryId, PDO::PARAM_INT);
        $statement->execute();

        return $statement->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function editExpenseCategory($data)
    {
        $userId = $_SESSION['userId'];
        $categoryId = $data->id;
        $name = $data->name;
        if(isset($data->monthlyLimit)) {
            $montlyLImit = $data->monthlyLimit;
        } else {
            $montlyLImit = null;
        }

        $db = static::getDB();

        $statement = $db->prepare("UPDATE expenses_category_assigned_to_users SET name = :name, monthly_limit = :monthlyLimit
        WHERE user_id = :userId AND id = :categoryId");
        $statement->bindValue(':userId', $userId, PDO::PARAM_INT);
        $statement->bindValue(':categoryId', $categoryId, PDO::PARAM_INT);
        $statement->bindValue(':name', $name, PDO::PARAM_STR);
        $statement->bindValue(':monthlyLimit', $montlyLImit, PDO::PARAM_INT);

        return $statement->execute();
    }

    public static function editPaymentMethod($data)
    {
        $userId = $_SESSION['userId'];
        $paymentMethodId = $data->id;
        $name = $data->name;

        $db = static::getDB();

        $statement = $db->prepare("UPDATE payment_methods_assigned_to_users SET name = :name
        WHERE user_id = :userId AND id = :paymentMethodId");
        $statement->bindValue(':userId', $userId, PDO::PARAM_INT);
        $statement->bindValue(':paymentMethodId', $paymentMethodId, PDO::PARAM_INT);
        $statement->bindValue(':name', $name, PDO::PARAM_STR);

        return $statement->execute();
    }

    public static function addExpenseCategory($data)
    {
        $userId = $_SESSION['userId'];
        $name = $data->name;
        if(isset($data->monthlyLimit)) {
            $montlyLImit = $data->monthlyLimit;
        } else {
            $montlyLImit = null;
        }

        $db = static::getDB();

        $statement = $db->prepare("INSERT INTO expenses_category_assigned_to_users VALUES (NULL, :userId, :name, :monthlyLimit)");
        $statement->bindValue(':userId', $userId, PDO::PARAM_INT);
        $statement->bindValue(':name', $name, PDO::PARAM_STR);
        $statement->bindValue(':monthlyLimit', $montlyLImit, PDO::PARAM_INT);
        
        return $statement->execute();
    }

    public static function getExpenseCategoryIdByName($name)
    {
        $userId = $_SESSION['userId'];

        $db = static::getDB();

        $statement = $db->prepare("SELECT id FROM expenses_category_assigned_to_users
        WHERE user_id = :userId AND name = :name");
        $statement->bindValue(':userId', $userId, PDO::PARAM_INT);
        $statement->bindValue(':name', $name, PDO::PARAM_STR);
        $statement->execute();

        return $statement->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public static function addPaymentMethod($data)
    {
        $userId = $_SESSION['userId'];
        $name = $data->name;

        $db = static::getDB();

        $statement = $db->prepare("INSERT INTO payment_methods_assigned_to_users VALUES (NULL, :userId, :name)");
        $statement->bindValue(':userId', $userId, PDO::PARAM_INT);
        $statement->bindValue(':name', $name, PDO::PARAM_STR);
        
        return $statement->execute();
    }

    
    public static function getPaymentMethodIdByName($name)
    {
        $userId = $_SESSION['userId'];

        $db = static::getDB();

        $statement = $db->prepare("SELECT id FROM payment_methods_assigned_to_users WHERE user_id = :userId AND name = :name");
        $statement->bindValue(':userId', $userId, PDO::PARAM_INT);
        $statement->bindValue(':name', $name, PDO::PARAM_STR);
        $statement->execute();

        return $statement->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function deleteExpenseCategory($categoryId)
    {
        $userId = $_SESSION['userId'];

        $db = static::getDB();

        $statement = $db->prepare("DELETE FROM expenses_category_assigned_to_users WHERE user_id = :userId AND id = :categoryId");
        $statement->bindValue(':userId', $userId, PDO::PARAM_INT);
        $statement->bindValue(':categoryId', $categoryId, PDO::PARAM_INT);

        return $statement->execute();
    }

    public static function getExpenseDetailsInCategory($categoryId)
    {
        $userId = $_SESSION['userId'];

        $db = static::getDB();

        $statement = $db->prepare("SELECT amount, date_of_expense AS date, expense_comment AS comment FROM expenses 
        WHERE user_id = :userId AND expense_category_assigned_to_user_id = :categoryId ORDER BY amount DESC");
        $statement->bindValue(':userId', $userId, PDO::PARAM_INT);
        $statement->bindValue(':categoryId', $categoryId, PDO::PARAM_INT);
        $statement->execute();

        return $statement->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function deletePaymentMethod($paymentMethodId)
    {
        $userId = $_SESSION['userId'];

        $db = static::getDB();

        $statement = $db->prepare("DELETE FROM payment_methods_assigned_to_users WHERE user_id = :userId AND id = :paymentMethodId");
        $statement->bindValue(':userId', $userId, PDO::PARAM_INT);
        $statement->bindValue(':paymentMethodId', $paymentMethodId, PDO::PARAM_INT);

        return $statement->execute();
    }

    public static function getExpenseDetailsWithPaymentMethod($paymentMethodId)
    {
        $userId = $_SESSION['userId'];

        $db = static::getDB();

        $statement = $db->prepare("SELECT amount, date_of_expense AS date, expense_comment AS comment FROM expenses 
        WHERE user_id = :userId AND payment_method_assigned_to_user_id = :paymentMethodId ORDER BY amount DESC");
        $statement->bindValue(':userId', $userId, PDO::PARAM_INT);
        $statement->bindValue(':paymentMethodId', $paymentMethodId, PDO::PARAM_INT);
        $statement->execute();

        return $statement->fetchAll(PDO::FETCH_ASSOC);
    }
    



}

