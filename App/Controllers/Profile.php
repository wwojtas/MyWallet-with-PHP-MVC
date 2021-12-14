<?php

namespace App\Controllers;

use \Core\View;
use \App\Auth;
use \App\Flash;
use \App\Models\Income;
use \App\Models\Expense;
use \App\Models\User;
use \App\Models\RememberedLogin;


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
      $paymentMethods = Expense::getPaymentMethodName();
      
      View::renderTemplate('Settings/show.html', [
        'incomeCategories' =>  $incomeCategories,
        'expenseCategories' => $expenseCategories,
        'paymentMethods' => $paymentMethods,
        'user' => $user,

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
      }
      else
      {
        Flash::addMessage('Podana kategoria przychodu już istnieje. Podaj inną', Flash::WARNING);
        $this->redirect('/profile');
      }
    }

    public function deleteCategoryIncomeAction()
    {
      $deletedIncomeCategoryId = $_POST['deletedIncomeCategoryId'];
      $deletedIncomeCategoryUserId = $_POST['deletedIncomeCategoryUserId'];

      $otherIncomeCategoryId = Income::getOtherIncomeCategoryId($deletedIncomeCategoryUserId);
      Income::changeAssignIncomeNumberInIncomes($deletedIncomeCategoryId, $otherIncomeCategoryId);
      Income::deleteIncomeCategory($deletedIncomeCategoryId);

      View::renderTemplate('Settings/successDeleteCategory.html');
    }



    public function addNewIncomeCategoryAction()
    {
        $newIncomeCategory = $_POST['newIncomeCategory'];
        Income::putNewIncomeCategoryIntoBase($newIncomeCategory);
        View::renderTemplate('Settings/successNewCategory.html');
    }


    public function editCategoryExpenseAction() 
    {
      $newExpenseName = strtolower($_POST['newExpenseName']);
      $newExpenseName = ucfirst($newExpenseName);

      $editedExpenseId = $_POST['editedExpenseId'];
      $limitValue = $_POST['limitValue'];

      if(isset($_POST['checkbox'])) 
      {
        $checkbox = $_POST['checkbox'];
      }
      else if (!isset($_POST['newExpenseName']) && !isset($_POST['checkbox']) && ( is_numeric(isset($_POST['limitValue'])))) 
      {
        View::renderTemplate('Settings/errorPage.html');
        return;
      } 
      else 
      {
        View::renderTemplate('Settings/errorPage.html');
        return;
      }

      if(Expense::checkCategoryExpenseName($newExpenseName))
      {
        if(isset($_POST['checkbox'])) Expense::editExpense($newExpenseName, $editedExpenseId, $limitValue, $checkbox);
        else Expense::editExpense($newExpenseName, $editedExpenseId, $limitValue, $checkbox = 0);
        View::renderTemplate('Settings/successEditExpense.html');
      }
      else if (!Expense::checkCategoryExpenseName($newExpenseName)) 
      {
        Flash::addMessage('Podana kategoria wydatku już istnieje. Podaj inną', Flash::WARNING);
        $this->redirect('/profile');
      } 
      else
      {
        Flash::addMessage('Nieprawidłowa edycja', Flash::WARNING);
        $this->redirect('/profile');
      } 

    }

    public function deleteCategoryExpenseAction()
    {
      $deletedExpenseCategoryId = $_POST['deletedExpenseCategoryId'];
      $deletedExpenseCategoryUserId = $_POST['deletedExpenseCategoryUserId'];

      $otherExpenseCategoryId = Expense::getOtherExpenseCategoryId($deletedExpenseCategoryUserId);

      Expense::changeAssignExpenseNumberInExpenses($deletedExpenseCategoryId, $otherExpenseCategoryId);

      Expense::deleteExpenseCategory($deletedExpenseCategoryId);
      View::renderTemplate('Settings/successDeleteCategory.html');

    }

    public function addNewExpenseCategoryAction()
    {
      $newExpenseCategory = $_POST['newExpenseCategory'];

      Expense::putNewExpenseCategoryIntoBase($newExpenseCategory);
      View::renderTemplate('Settings/successNewCategory.html');
    }

    public function editCategoryPaymentAction() 
    {
      $newPaymentName = strtolower($_POST['newPaymentName']);
      $newPaymentName = ucfirst($newPaymentName);

      $editedPaymentId = $_POST['editedPaymentId'];

      if(Expense::checkPaymentMethodName($newPaymentName))
      {
        Expense::editPaymentMethod($newPaymentName, $editedPaymentId);

        View::renderTemplate('Settings/successEditPayment.html');
      }
      else
      {
        Flash::addMessage('Podana kategoria płatności już istnieje. Podaj inną', Flash::WARNING);
        $this->redirect('/profile');
      }
    }

    

    public function deletePaymentCategoryAction()
    {
      $deletedPaymentId = $_POST['deletedPaymentId'];
      $deletedPaymentUserId = $_POST['deletedPaymentUserId'];
      
      $otherPaymentCategoryId = Expense::getOtherPaymentCategoryId($deletedPaymentUserId);
      Expense::changePaymentCategoryInExpenses($deletedPaymentId, $otherPaymentCategoryId);
      Expense::deletePaymentCategoryInPaymentMethodsUsers($deletedPaymentId);

      View::renderTemplate('Settings/successDeletePaymentCategory.html');

    }

    public function addNewPaymentCategoryAction()
    {
      $newPaymentCategory = $_POST['newPaymentCategory'];

      Expense::putNewPaymentCategoryIntoBase($newPaymentCategory);
      View::renderTemplate('Settings/successNewCategory.html');

    }

    public function editUserNameAction()
    {
      $user = Auth::getUser();
      $editedUserId =  $user->id;

      if(isset($_POST['newUserName'])){

        $newUserName = $_POST['newUserName'];

        if(User::editNameUser($newUserName, $editedUserId)) 
        {
          View::renderTemplate('Settings/successNewUserName.html');
        }
      }
      else
      {
        View::renderTemplate('Settings/errorPage.html');
      }
    }

    public function editUserEmailAction()
    {
      $changeUserEmail = new User($_POST);

      if($changeUserEmail->editEmailUser($_SESSION['user_id'])) 
      {
        $changeUserEmail->sendActivationEmail();
        Auth::logout();
        View::renderTemplate('Settings/successEditEmail.html');
      } else {
        View::renderTemplate('Settings/errorPage.html');
      }

    }

    public function deleteAllUserBalanceSheetAction()
    {
      $deletedUserId = $_POST['deletedUserId'];
      if(Expense::deleteAllUserExpenses($deletedUserId) && Income::deleteAllUserIncomes($deletedUserId))
      View::renderTemplate('Settings/successDeleteBalance.html');
    }

    public function removeUserAccountAction()
    {
      $deletedUserId = $_POST['deletedUserId'];

      Expense::deleteAllUserExpenses($deletedUserId);
      Expense::deleteAllUserExpensesCategory($deletedUserId);
      Expense::deleteAllUserPaymentMethods($deletedUserId);

      Income::deleteAllUserIncomes($deletedUserId);
      Income::deleteAllUserIncomesCategory($deletedUserId);

      RememberedLogin::deleteAllUserTokenHash($deletedUserId);

      User::deleteUserAccountFromUsers($deletedUserId);

      View::renderTemplate('Settings/removeUserAccount.html');
      
    }














}
