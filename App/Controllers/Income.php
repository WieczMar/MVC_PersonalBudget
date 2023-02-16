<?php

namespace App\Controllers;

use \Core\View;
use \App\Models\Income as IncomeModel;
use \App\Flash;

//Signin controller
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

}
