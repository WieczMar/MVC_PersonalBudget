<?php

namespace App\Controllers;

use \Core\View;
use \App\Models\Income as IncomeModel;
//Signin controller
class Income extends Authenticated
{
    //Show the index page
    public function indexAction()
    {
        $rowsIncomeCategory = IncomeModel::getIncomeCategories($_SESSION['userId']);

        View::renderTemplate('Income/index.html', [
            'savingTransactionCompleted' => isset($_SESSION['savingTransactionCompleted']),
            'rowsIncomeCategory' => $rowsIncomeCategory
        ]);
        unset($_SESSION['savingTransactionCompleted']);     
    }

    public function addAction()
    {
        if ((isset($_POST['amount']))&&(!empty($_POST['amount']))) 
        {
            IncomeModel::addNewIncome($_SESSION['userId'], $_POST['category'], $_POST['amount'], $_POST['date'], $_POST['comment']);
            $_SESSION['savingTransactionCompleted'] = true;

        }
        $this->redirect('/income/index');
    }       

}
