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
        if ((isset($_POST['amount']))&&(!empty($_POST['amount']))) 
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

}
