<?php

namespace App\Controllers;

use \Core\View;
use \App\Models\Expense as ExpenseModel;
use \App\Flash;

// Expense controller
class Expense extends Authenticated
{
    //Show the index page
    public function indexAction()
    {
        $rowsExpenseCategory = ExpenseModel::getExpenseCategories($_SESSION['userId']);
        $rowsPaymentMethod = ExpenseModel::getPaymentMethods($_SESSION['userId']);

        View::renderTemplate('Expense/index.html', [
            'rowsExpenseCategory' => $rowsExpenseCategory,
            'rowsPaymentMethod' => $rowsPaymentMethod
        ]);
    }

    public function addAction()
    {
        if ((!empty($_POST['amount']))&&(!empty($_POST['paymentMethodId']))&&(!empty($_POST['categoryId']))) 
        {
            $expense = new ExpenseModel($_POST);

            if($expense->addNewExpense($_SESSION['userId'])){

                Flash::addMessage('savingTransactionResult' , 'You have successfully added new expense!', Flash::SUCCESS);
            }

        } else {

            Flash::addMessage('savingTransactionResult' , 'Error! Please fill in all required fields', Flash::WARNING);
        }
        $this->redirect('/expense/index');
    }      
    
    public function getLimitOfExpensesInCategoryAction()
    {
        $categoryId = $this->route_params['id'];

        echo json_encode(ExpenseModel::getLimitOfExpensesInCategory($categoryId), JSON_UNESCAPED_UNICODE);
    }
    
    public function getSumOfExpensesInCategoryForSelectedMonthAction()
    {
        $categoryId = $this->route_params['id'];
        $date = $_GET['date'];

        echo json_encode(ExpenseModel::getSumOfExpensesInCategoryForSelectedMonth($categoryId, $date), JSON_UNESCAPED_UNICODE);

    }

    public function getExpenseDetailsInCategoryForSelectedDatesAction()
    {
        $categoryId = $this->route_params['id'];
        $startDate = $_GET['start-date'];
        $endDate = $_GET['end-date'];
        
        echo json_encode(ExpenseModel::getExpenseDetailsInCategoryForSelectedDates($categoryId, $startDate, $endDate), JSON_UNESCAPED_UNICODE);
    }

    public function getExpenseCategoryNameAction()
    {
        $categoryId = $this->route_params['id'];      
        echo json_encode(ExpenseModel::getExpenseCategoryName($categoryId), JSON_UNESCAPED_UNICODE);
    }
    
    public function getExpenseCategoriesAction()
    {
        $userId = $_SESSION['userId'];      
        echo json_encode(ExpenseModel::getExpenseCategories($userId), JSON_UNESCAPED_UNICODE);
    }

    public function getPaymentMethodsAction()
    {
        $userId = $_SESSION['userId'];     
        echo json_encode(ExpenseModel::getPaymentMethods($userId), JSON_UNESCAPED_UNICODE);
    }

    public function editExpenseCategoryAction()
    {   
        $userId = $_SESSION['userId'];

        $request_body = file_get_contents('php://input');
        $data = json_decode($request_body);

        if((!empty($data->id))&&(!empty($data->name))){
            http_response_code(200);
            echo json_encode(ExpenseModel::editExpenseCategory($data), JSON_UNESCAPED_UNICODE);
        } else {
            http_response_code(400);
            $error = "Id and name values are required and cannot be empty.";
            echo json_encode($error, JSON_UNESCAPED_UNICODE);
        }
    }

    public function editPaymentMethodAction()
    {   
        $request_body = file_get_contents('php://input');
        $data = json_decode($request_body);

        if((!empty($data->id))&&(!empty($data->name))){
            http_response_code(200);
            echo json_encode(ExpenseModel::editPaymentMethod($data), JSON_UNESCAPED_UNICODE);
        } else {
            http_response_code(400);
            $error = "Id and name values are required and cannot be empty.";
            echo json_encode($error, JSON_UNESCAPED_UNICODE);
        }
    }

    public function addExpenseCategoryAction()
    {   
        $request_body = file_get_contents('php://input');
        $data = json_decode($request_body);

        if(empty($data->name)){
            http_response_code(400);
            $error = "Name value cannot be empty.";
            echo json_encode($error);
        } else if (!empty(ExpenseModel::getExpenseCategoryIdByName($data->name))) {
            http_response_code(405);
            $error = "Expense category with such name already exists!";
            echo json_encode($error, JSON_UNESCAPED_UNICODE);
        } else {
            http_response_code(201);
            echo json_encode(ExpenseModel::addExpenseCategory($data), JSON_UNESCAPED_UNICODE);
        }
    }

    public function addPaymentMethodAction()
    {   
        $request_body = file_get_contents('php://input');
        $data = json_decode($request_body);

        if(empty($data->name)){
            http_response_code(400);
            $error = "Name value cannot be empty.";
            echo json_encode($error);
        } else if (!empty(ExpenseModel::getPaymentMethodIdByName($data->name))) {
            http_response_code(405);
            $error = "Payment method with such name already exists!";
            echo json_encode($error, JSON_UNESCAPED_UNICODE);
        } else {
            http_response_code(201);
            echo json_encode(ExpenseModel::addPaymentMethod($data), JSON_UNESCAPED_UNICODE);
        }
    }

    public function deleteExpenseCategoryAction()
    {   
        $categoryId = $this->route_params['id'];
    
        if(empty(ExpenseModel::getExpenseDetailsInCategory($categoryId))){
            http_response_code(200);
            echo json_encode(ExpenseModel::deleteExpenseCategory($categoryId), JSON_UNESCAPED_UNICODE);
        } else {
            http_response_code(405);
            $error = "There are already expenses added to this category. Firstly delete all expenses related to this category.";
            echo json_encode($error, JSON_UNESCAPED_UNICODE);
        }
    }

    public function deletePaymentMethodAction()
    {   
        $paymentMethodId = $this->route_params['id'];
    
        if(empty(ExpenseModel::getExpenseDetailsWithPaymentMethod($paymentMethodId))){
            http_response_code(200);
            echo json_encode(ExpenseModel::deletePaymentMethod($paymentMethodId), JSON_UNESCAPED_UNICODE);
        } else {
            http_response_code(405);
            $error = "There are already expenses added with such payment method. Firstly delete all expenses with this payment method.";
            echo json_encode($error, JSON_UNESCAPED_UNICODE);
        }
    }

    public function deleteExpenseAction()
    {   
        $expenseId = $this->route_params['id'];

        if(ExpenseModel::deleteExpense($expenseId)){
            http_response_code(200);
            echo json_encode('Expense has been deleted.', JSON_UNESCAPED_UNICODE);
        } else {
            http_response_code(400);
            $error = "Expense with such id does not exist.";
            echo json_encode($error, JSON_UNESCAPED_UNICODE);
        }
    }

}
