<?php

namespace App\Controllers;

use \Core\View;
use \App\Flash;
use \App\Models\Balance;


class BalanceSheet extends Authenticated
{

   public function showAction() {
      
      $balance = new Balance($_POST);

      $balance  = $this -> route_params['period'];

      if($balance  == 'selected_period')
      {
         $_SESSION['start_date'] = $_POST["start_date"];
         $_SESSION['end_date'] = $_POST["end_date"];
      }

      $incomesData = Balance::getIncomesData($balance);
      $expensesData = Balance::getExpensesData($balance);
      $incomesSum = static::sumAll($incomesData);
      $incomesSum = number_format($incomesSum, 2, '.', '');
      $expensesSum = static::sumAll($expensesData);
      $expensesSum = number_format($expensesSum, 2, '.', '');
      $balanceSum = $incomesSum - $expensesSum;
      $balanceSum =  number_format($balanceSum, 2, '.', '');

      View::renderTemplate('Balance/balance-sheet.html', [

         'incomesSum' => $incomesSum,
         'expensesSum' => $expensesSum,
         'balanceSum' => $balanceSum,
         'incomesData' => $incomesData,
         'expensesData' => $expensesData         
      ]);

  }

  public function selectedPeriodAction() {
      View::renderTemplate('Balance/selected-period.html');
   }

  public static function sumAll($array) {
      $sum = 0;
      foreach($array as $amount ) {
         $sum += $amount['amount'];
      }
      return $sum;
   }

   public function getSumExpenseCatagoryAction()
   {

      $sumExpenseCategory = Balance::getSumExpenseCatagory();

      header('Content-Type: application/json');
      echo json_encode($sumExpenseCategory);

   }


}
