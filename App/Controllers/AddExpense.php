<?php

namespace App\Controllers;

use \Core\View;
use \App\Models\Expense;
use \App\Flash; 

/**
 * Items controller (example)
 *
 * PHP version 7.0
 */

class AddExpense extends Authenticated
{

    /**
     * Items index
     *
     * @return void
     */
     public $errors = [];
     
     public function __construct($data = [])
     {
         foreach ($data as $key => $value) {
             $this->$key = $value;
         };
     }


    public function executeAction()
    {
        View::renderTemplate('AddExpense/execute.html');
    }

    /**
     * Add a new item
     *
     * @return void
     */
    public function newAction()
    {
      $expense = new Expense($_POST);

      if($expense->addNewExpense())
      {
        Flash::addMessage('Wydatek dodano');
        View::renderTemplate('AddExpense/successExpense.html');
      }
      else
      {
        Flash::addMessage('Próba dodania wydatku nie powiodła się');
        $this->redirect('/AddExpense');
      }
    }

    /**
     * choose a category expense
     *
     * @return void
     */
    public function chooseExpenseCategoryAction()
    {
        $searchTerm = $_POST['searchTerm'] ?? '';
        Expense::getDataExpense($searchTerm);
    }


    public function choosePaymentMethodAction()
    {
        $searchTerm = $_POST['searchTerm'] ?? '';
        Expense::getDataPayment($searchTerm);
    }

    public function getSumExpenseCatagoryAction()
   {

      $sumExpenseCategory = Expense::getSumExpenseCatagory();

      header('Content-Type: application/json');
      echo json_encode($sumExpenseCategory);
   }


}
