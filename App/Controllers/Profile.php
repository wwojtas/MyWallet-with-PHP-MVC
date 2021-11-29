<?php

namespace App\Controllers;

use \Core\View;
use \App\Auth;
use \App\Flash;
use \App\Models\Income;
use \App\Models\Expense;


/**
 * Profile controller
 *
 * PHP version 7.0
 */
class Profile extends Authenticated
{

    /**
     * Before filter - called before each action method
     *
     * @return void
     */
    protected function before()
    {
        parent::before();

        $this->user = Auth::getUser();
    }

    /**
     * Show the profile
     *
     * @return void
     */
    public function showAction()
    {
        View::renderTemplate('Profile/show.html', [
            'user' => $this->user
        ]);
    }

    /**
     * Show the form for editing the profile
     *
     * @return void
     */
    public function editAction()
    {
        View::renderTemplate('Profile/edit.html', [
            'user' => $this->user
        ]);
    }

    /**
     * Update the profile
     *
     * @return void
     */
    public function updateAction()
    {
        if ($this->user->updateProfile($_POST)) {

            Flash::addMessage('Changes saved');

            $this->redirect('/profile/show');

        } else {

            View::renderTemplate('Profile/edit.html', [
                'user' => $this->user
            ]);

        }
    }

    public function showSettingsAction()
    {
      $user = Auth::getUser();
      $incomeCategories = Income::getCategoryIncomeName();
      $expenseCategories = Expense::getCategoryExpenseName();
    //   $paymentMethods = Expense::getPaymentMethods();
      
      View::renderTemplate('Settings/show.html', [
        'incomeCategories' =>  $incomeCategories,
        'expenseCategories' => $expenseCategories,
        // 'paymentMethods' => $paymentMethods,
        'user' => $user

      ]);
    }

    public function editCategoryIncomeAction() 
    {
      $newIncomeName = strtolower($_POST['newIncomeName']);
      $newIncomeName = ucfirst($newIncomeName);

      $editedIncomeId = $_POST['editedIncomeId'];

      if(Income::checkCategoryIncomeName($newIncomeName))
      {
        Income::editIncome($newIncomeName, $editedIncomeId);
        View::renderTemplate('Settings/successEditIncome.html');
      } else {
        Flash::addMessage('Podana kategoria przychodu już istnieje. Podaj inną', Flash::WARNING);
        $this->redirect('/profile');
      }
    }

    public function deleteCategoryIncomeAction()
    {
        $deletedIncomeId = $_POST['deletedIncomeId'];
        Income::deleteIncomeCategory($deletedIncomeId);
        View::renderTemplate('Settings/successDeleteCategoryIncome.html');
    }

    public function addNewIncomeCategory()
    {
        $newIncomeCategory = $_POST['newIncomeCategory'];
        Income::putNewIncomeCategoryIntoBase($newIncomeCategory);
        View::renderTemplate('Settings/successNewCategory.html');
    }


    public function editCategoryExpenseAction() 
    {
      $newExpenseName = strtolower($_POST['newExpenseName']);
      $newExpenseName = ucfirst($newExpenseName);

      $editedIncomeId = $_POST['editedExpenseId'];
      $limitValue = $_POST['limitValue'];

      if(Expense::checkCategoryExpenseName($newExpenseName))
      {
        Expense::editExpense($newExpenseName, $editedExpenseId, $limitValue);
        View::renderTemplate('Settings/successEditExpense.html');
      }
      else
      {
        Flash::addMessage('Podana kategoria wydatku już istnieje. Podaj inną', Flash::WARNING);
        $this->redirect('/profile');
      }

    }






}
