<?php

namespace App\Controllers;

use \Core\View;
use \App\Models\Income as IncomeModel;
use \App\Flash;

// Signin controller
class Income extends Authenticated
{
    //Show the index page
    public function indexAction()
    {
        $rowsIncomeCategory = IncomeModel::getIncomeCategories($_SESSION['userId']);

        View::renderTemplate('Income/index.html', [
            'rowsIncomeCategory' => $rowsIncomeCategory
        ]); 
    }

    public function addAction()
    {
        if ((!empty($_POST['amount']))&&(!empty($_POST['categoryId']))) 
        {
            $income = new IncomeModel($_POST);

            if($income->addNewIncome($_SESSION['userId'])){

                Flash::addMessage('savingTransactionResult' , 'You have successfully added new income!', Flash::SUCCESS);
            }      

        } else {

            Flash::addMessage('savingTransactionResult' , 'Error! Please fill in all required fields', Flash::WARNING);
        }
        
        $this->redirect('/income/index');
    }     
    
    public function getIncomeDetailsInCategoryForSelectedDatesAction()
    {
        $categoryId = $this->route_params['id'];
        $startDate = $_GET['start-date'];
        $endDate = $_GET['end-date'];
        
        echo json_encode(IncomeModel::getIncomeDetailsInCategoryForSelectedDates($categoryId, $startDate, $endDate), JSON_UNESCAPED_UNICODE);
    }

    public function getIncomeCategoryNameAction()
    {
        $categoryId = $this->route_params['id'];      
        echo json_encode(IncomeModel::getIncomeCategoryName($categoryId), JSON_UNESCAPED_UNICODE);
    }

    public function getIncomeCategoriesAction()
    {   
        $userId = $_SESSION['userId'];
        echo json_encode(IncomeModel::getIncomeCategories($userId), JSON_UNESCAPED_UNICODE);
    }

    public function editIncomeCategoryAction()
    {   
        $request_body = file_get_contents('php://input');
        $data = json_decode($request_body);
        
        if((!empty($data->id))&&(!empty($data->name))){
            http_response_code(200);
            echo json_encode(IncomeModel::editIncomeCategory($data), JSON_UNESCAPED_UNICODE);
        } else {
            http_response_code(400);
            $error = "Id and name values are required and cannot be empty.";
            echo json_encode($error, JSON_UNESCAPED_UNICODE);
        }
    }

    public function addIncomeCategoryAction()
    {   
        $request_body = file_get_contents('php://input');
        $data = json_decode($request_body);
        
        if(empty($data->name)){
            http_response_code(400);
            $error = "Name value cannot be empty.";
            echo json_encode($error);
        } else if (!empty(IncomeModel::getIncomeCategoryIdByName($data->name))) {
            http_response_code(405);
            $error = "Income category with such name already exists!";
            echo json_encode($error, JSON_UNESCAPED_UNICODE);
        } else {
            http_response_code(201);
            echo json_encode(IncomeModel::addIncomeCategory($data), JSON_UNESCAPED_UNICODE);
        }
        
    }

    public function deleteIncomeCategoryAction()
    {   
        $categoryId = $this->route_params['id'];

        if(empty(IncomeModel::getIncomeDetailsInCategory($categoryId))){
            http_response_code(200);
            echo json_encode(IncomeModel::deleteIncomeCategory($categoryId), JSON_UNESCAPED_UNICODE);
        } else {
            http_response_code(405);
            $error = "There are already incomes added to this category. Firstly delete all incomes related to category.";
            echo json_encode($error, JSON_UNESCAPED_UNICODE);
        }
    }
    



}
