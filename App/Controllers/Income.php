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
            IncomeModel::addNewIncome($_SESSION['userId'], $_POST['category'], $_POST['amount'], $_POST['date'], $_POST['comment']);
            Flash::addMessage('savingTransactionResult' , 'You have successfully added new income!', Flash::SUCCESS);

        } else {

            Flash::addMessage('savingTransactionResult' , 'Error! Please fill in all required fields', Flash::WARNING);
        }
        $this->redirect('/income/index');
    }       

}
