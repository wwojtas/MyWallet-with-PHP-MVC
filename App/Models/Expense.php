<?php

namespace App\Models;

use PDO;
use App\Auth;

/**
 * Remembered login model
 *
 * PHP version 7.0
 */
class Expense extends \Core\Model
{
 public $errors = [];


 public function __construct($data = [])
 {
   foreach ($data as $key => $value) {
     $this->$key = $value;
   };
 }


 public function addNewExpense()
 {
    $user = Auth::getUser();
    $this->validate();

    if (empty($this->errors))
    {
      $sql = 'INSERT INTO expenses (user_id, expense_category_assigned_to_user_id, payment_method_assigned_to_user_id, amount, date_of_expense, expense_comment)
              VALUES (:user_id, :expense_category_assigned_to_user_id, :payment_method_assigned_to_user_id, :amount, :date_of_expense, :expense_comment)';

      $db = static::getDB();
      $stmt = $db->prepare($sql);

      $stmt->bindValue(':user_id', $user->id, PDO::PARAM_INT);
      $stmt->bindValue(':expense_category_assigned_to_user_id', $this->expense_category, PDO::PARAM_INT);
      $stmt->bindValue(':payment_method_assigned_to_user_id', $this->payment_method, PDO::PARAM_INT);
      $stmt->bindValue(':amount', $this->amount, PDO::PARAM_STR);
      $stmt->bindValue(':date_of_expense', $this->date_of_expense, PDO::PARAM_STR);
      $stmt->bindValue(':expense_comment', $this->expense_comment, PDO::PARAM_STR);

      return $stmt->execute();
    }
    return false;
  }


 public function validate()
  {
    if($this->amount == '')
    {
      $this->errors[] = 'Podaj wartośc wydatku';
    }
    if($this->date_of_expense == '')
    {
      $this->errors[] = 'Podaj datę';
    }
    if($this->expense_category == 0)
    {
      $this->errors[] = 'Wybierz kategorię wydatku';
    }
    if($this->payment_method == 0)
    {
      $this->errors[] = 'Wybierz sposób płatności';
    }
  }

  public static function getDataExpense($selectSearchTerm)
  {
    $user = Auth::getUser();
    if(!isset($selectSearchTerm))
      {
        $fetchData = static::getCategoryExpenseName();
      }
    else
      {
        $sql = "SELECT * FROM expenses_category_assigned_to_users WHERE name LIKE '%".$selectSearchTerm."%' AND user_id = '$user->id'";
        $fetchData = static::getDataSelect($sql);
      }
 
    $data = array();
    foreach ($fetchData as $row) {
        $data[] = array('id'=>$row['id'], 'text'=>$row['name']);
    };
    echo json_encode($data);
  }


 public static function getDataPayment($selectSearchTerm)
  {
    $user = Auth::getUser();
    if(!isset($selectSearchTerm))
      {
        $fetchData = static::getPaymentMethodName();
      }
    else
      {
        $sql = "SELECT * FROM payment_methods_assigned_to_users WHERE name LIKE '%".$selectSearchTerm."%' AND user_id = '$user->id'";
        $fetchData = static::getDataSelect($sql);
      }

    $data = array();
      foreach ($fetchData as $row) {
          $data[] = array('id'=>$row['id'], 'text'=>$row['name']);
      };
    echo json_encode($data);
  }
  
  public static function getDataSelect($sql)
  {
    $db = static::getDB();
    $stmt = $db->prepare($sql);
    $stmt->execute();
    
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
  }


  public static function getCategoryExpenseName()
  {
    $user = Auth::getUser();
    $sql = "SELECT * FROM expenses_category_assigned_to_users WHERE user_id = '$user->id'";

    return  $fetchData = static::getDataSelect($sql);
  }


  public static function getPaymentMethodName()
  {
    $user = Auth::getUser();
    $sql = "SELECT * FROM payment_methods_assigned_to_users WHERE user_id = '$user->id'";

    return  $fetchData = static::getDataSelect($sql);
  }

  public static function checkPaymentMethodName($newPaymentName)
  {
      $paymentMethod = static::getPaymentMethodName();
      foreach($paymentMethod as $onePayment)
      {
        if($newPaymentName == $onePayment['name']) return false;
        else return true;
      }
  }


  public static function checkCategoryExpenseName($newExpenseName)
  {
      $expenseCategories = static::getCategoryExpenseName();
      foreach($expenseCategories as $oneExpense)
      {
        if($newExpenseName == $oneExpense['name']) return false;
        else return true;
      }
  }

  public static function editExpense($newExpenseName, $editedExpenseId, $limitValue, $checkbox)
  {

    if($newExpenseName != "" && $checkbox = 0 )
    {
      $sql = 'UPDATE expenses_category_assigned_to_users
              SET name = :name
              WHERE id = :editedExpenseId';

      $db = static::getDB();
      $stmt = $db->prepare($sql);

      $stmt->bindValue(':editedExpenseId', $editedExpenseId, PDO::PARAM_INT);
      $stmt->bindValue(':name', $newExpenseName, PDO::PARAM_STR);
      return $stmt->execute();

    }
    else if($newExpenseName == "" && $checkbox != 0 && $limitValue != "")
    {
      $sql = 'UPDATE expenses_category_assigned_to_users
              SET  expense_limit	= :expense_limit
              WHERE id = :editedExpenseId';

      $db = static::getDB();
      $stmt = $db->prepare($sql);

      $stmt->bindValue(':editedExpenseId', $editedExpenseId, PDO::PARAM_INT);
      $stmt->bindValue(':expense_limit', $limitValue, PDO::PARAM_INT);
      return $stmt->execute();

    }
    else
    {
      $sql = 'UPDATE expenses_category_assigned_to_users
              SET name = :name, expense_limit	= :expense_limit
              WHERE id = :editedExpenseId';

      $db = static::getDB();
      $stmt = $db->prepare($sql);

      $stmt->bindValue(':editedExpenseId', $editedExpenseId, PDO::PARAM_INT);
      $stmt->bindValue(':name', $newExpenseName, PDO::PARAM_STR);
      $stmt->bindValue(':expense_limit', $limitValue, PDO::PARAM_INT);
      return $stmt->execute();
    }
  }


  public static function deleteExpenseCategory($deletedExpenseId)
  {
    $sql = 'DELETE
            FROM expenses_category_assigned_to_users
            WHERE id = :removeExpenseId';

    $db = static::getDB();
    $stmt = $db->prepare($sql);
    $stmt->bindValue(':removeExpenseId', $deletedExpenseId, PDO::PARAM_INT);

    return $stmt->execute();
  }

  public static function getOtherExpenseCategoryId($deletedExpenseCategoryUserId)
  {
    $sql = 'SELECT id
            FROM expenses_category_assigned_to_users
            WHERE user_id = :chosenUserId
            AND name = :name';


    $db = static::getDB();
    $stmt = $db->prepare($sql);
    $stmt->bindValue(':chosenUserId', $deletedExpenseCategoryUserId, PDO::PARAM_INT);
    $stmt->bindValue(':name', 'Inne', PDO::PARAM_STR);

    $stmt->execute();
    $expenseCategoryId = $stmt->fetchAll(PDO::FETCH_ASSOC);

    foreach($expenseCategoryId as $oneExpense) return $oneExpense["id"];

  }


  public static function changeAssignExpenseNumberInExpenses($deletedExpenseCategoryId, $otherExpenseCategoryId)
  {
    $sql = 'UPDATE expenses
            SET  expense_category_assigned_to_user_id = :otherExpenseCategoryId
            WHERE expense_category_assigned_to_user_id = :deletedExpenseCategoryId';
    
    $db = static::getDB();
    $stmt = $db->prepare($sql);
    $stmt->bindValue(':otherExpenseCategoryId', $otherExpenseCategoryId, PDO::PARAM_INT);
    $stmt->bindValue(':deletedExpenseCategoryId', $deletedExpenseCategoryId, PDO::PARAM_INT);
    
    return $stmt->execute();

  }




  public static function putNewExpenseCategoryIntoBase($newExpenseCategory)
  {
    $user = Auth::getUser();
    $sql = 'INSERT INTO expenses_category_assigned_to_users(user_id, name)
            VALUES (:user_id, :newExpenseCategory)';

    $db = static::getDB();
    $stmt = $db->prepare($sql);
    $stmt->bindValue(':user_id', $user->id, PDO::PARAM_INT);
    $stmt->bindValue(':newExpenseCategory', $newExpenseCategory, PDO::PARAM_STR);

    return $stmt->execute();
  }
 



  public static function deleteAllUserExpenses($deletedUserId)
  {
    $sql = 'DELETE
            FROM expenses
            WHERE user_id = :removingExpenseId';

    $db = static::getDB();
    $stmt = $db->prepare($sql);
    $stmt->bindValue(':removingExpenseId', $deletedUserId, PDO::PARAM_INT);

    return $stmt->execute();

  }


  public static function deleteAllUserExpensesCategory($deletedUserId)
  {
    $sql = 'DELETE
            FROM expenses_category_assigned_to_users
            WHERE user_id = :deletedUserAccountId';
    
    $db = static::getDB();
    $stmt = $db->prepare($sql);
    $stmt->bindValue(':deletedUserAccountId', $deletedUserId, PDO::PARAM_INT);

    return $stmt->execute();
  }



  public static function putNewPaymentCategoryIntoBase($newPaymentCategory)
  {
    $user = Auth::getUser();

    $sql = 'INSERT INTO payment_methods_assigned_to_users(user_id, name)
            VALUES (:user_id, :newPaymentCategory)';

    $db = static::getDB();
    $stmt = $db->prepare($sql);
    $stmt->bindValue(':user_id', $user->id, PDO::PARAM_INT);
    $stmt->bindValue(':newPaymentCategory', $newPaymentCategory, PDO::PARAM_STR);

    return $stmt->execute();
  }


  public static function deleteAllUserPaymentMethods($deletedUserId)
  {
    $sql = 'DELETE
            FROM payment_methods_assigned_to_users
            WHERE user_id = :deletedUserAccountId';
    
    $db = static::getDB();
    $stmt = $db->prepare($sql);
    $stmt->bindValue(':deletedUserAccountId', $deletedUserId, PDO::PARAM_INT);

    return $stmt->execute();

  }

 public static function editPaymentMethod($newPaymentName, $editedPaymentId)
 {
   $sql = 'UPDATE payment_methods_assigned_to_users
           SET name = :newPaymentName
           WHERE id = :editedPaymentId';


    $db = static::getDB();
    $stmt = $db->prepare($sql);
    $stmt->bindValue(':editedPaymentId', $editedPaymentId, PDO::PARAM_INT);
    $stmt->bindValue(':newPaymentName', $newPaymentName, PDO::PARAM_STR);

    return $stmt->execute();
 }

  public static function getOtherPaymentCategoryId($deletedPaymentUserId)
  {
    $sql = 'SELECT id
            FROM payment_methods_assigned_to_users
            WHERE user_id = :paymentUserId
            AND name = :name';

    $db = static::getDB();
    $stmt = $db->prepare($sql);
    $stmt->bindValue(':paymentUserId', $deletedPaymentUserId, PDO::PARAM_INT);
    $stmt->bindValue(':name', 'Inne', PDO::PARAM_STR);
    $stmt->execute();

    $paymentMethodId = $stmt->fetchAll(PDO::FETCH_ASSOC);

    foreach($paymentMethodId as $onePayment) return $onePayment["id"];
  }

  public static function changePaymentCategoryInExpenses($deletedPaymentId, $otherPaymentCategoryId)
  {
    $sql = 'UPDATE expenses
            SET payment_method_assigned_to_user_id = :otherPaymenId
            WHERE payment_method_assigned_to_user_id = :removedPaymentId';

    $db = static::getDB();
    $stmt = $db->prepare($sql);
    $stmt->bindValue(':otherPaymenId', $otherPaymentCategoryId, PDO::PARAM_INT);
    $stmt->bindValue(':removedPaymentId', $deletedPaymentId, PDO::PARAM_INT);

    return $stmt->execute();
  }


  public static function deletePaymentCategoryInPaymentMethodsUsers($deletedPaymentId)
  {
    $user = Auth::getUser();
    $sql = 'DELETE
            FROM payment_methods_assigned_to_users
            WHERE id = :deletedPaymentCategoryId
            AND user_id = :paymentUserId';

    $db = static::getDB();
    $stmt = $db->prepare($sql);
    $stmt->bindValue(':paymentUserId', $user->id, PDO::PARAM_INT);
    $stmt->bindValue(':deletedPaymentCategoryId', $deletedPaymentId, PDO::PARAM_INT);

    return $stmt->execute();

  }


  public static function getSumExpenseCatagory()
	{
		$user = Auth::getUser();
		$start_date = date("Y-m-01");
		$end_date = date("Y-m-d");

  $sql = 'SELECT ecatu.name, ecatu.expense_limit, COALESCE(SUM(expenses.amount),0) AS sumCategory
          FROM expenses_category_assigned_to_users AS ecatu
          LEFT JOIN expenses
            ON  ecatu.id = expenses.expense_category_assigned_to_user_id
          AND ecatu.expense_limit = 0 OR ecatu.expense_limit != 0
          AND ( expenses.date_of_expense >= :startDate AND  expenses.date_of_expense <= :endDate)
          WHERE expenses.user_id = :user_id
          GROUP BY ecatu.name';

		$db = static::getDB();
		$stmt = $db->prepare($sql);
		$stmt -> bindValue(':user_id', $user->id, PDO::PARAM_INT);
		$stmt -> bindValue(':startDate', $start_date, PDO::PARAM_STR);
		$stmt -> bindValue(':endDate', $end_date, PDO::PARAM_STR);
		$stmt->execute();

		return ($stmt->fetchAll());

	}



}
