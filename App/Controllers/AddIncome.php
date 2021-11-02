<?php

namespace App\Controllers;

use \Core\View;
use \App\Models\Income;
use \App\Flash;


class AddIncome extends Authenticated
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
        View::renderTemplate('AddIncome/execute.html');
    }


    /**
     * choose a category income
     *
     * @return void
     */
    public function chooseIncomeAction()
    {
      $searchTerm = $_POST['searchTerm'] ?? '';
      Income::getDataIncome($searchTerm);
    }


    /**
     * Add a new item
     *
     * @return void
     */

    public function newAction()
    {
      $income = new Income($_POST);

      if($income->addNewIncome())
      {
        Flash::addMessage('Przychód dodano');
        View::renderTemplate('AddIncome/successIncome.html');
      }
      else
      {
        Flash::addMessage('Próba dodania przychodu nie powiodła się');
        $this->redirect('/AddIncome');
      }
    }

}