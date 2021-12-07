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


  public static function checkCategoryExpenseName($newExpenseName)
  {
      $expenseCategories = static::getCategoryExpenseName();
      foreach($expenseCategories as $row)
      {
        if($newExpenseName == $row['name']) return false;
        else return true;
      }
  }

  public static function editExpense($newExpenseName, $editedExpenseId, $limitValue, $checkbox)
  {

    if($newExpenseName == "" && $checkbox != 0)
    {
      $sql = 'UPDATE expenses_category_assigned_to_users
              SET expense_limit	= :expense_limit
              WHERE id = :editedExpenseId';
    }
    else if($limitValue == "" && $checkbox == 0)
    {
      $sql = 'UPDATE expenses_category_assigned_to_users
              SET name = :name
              WHERE id = :editedExpenseId';
    }
    else
    {
      $sql = 'UPDATE expenses_category_assigned_to_users
              SET name = :name, expense_limit	= :expense_limit
              WHERE id = :editedExpenseId';
    }
  
    $db = static::getDB();
    $stmt = $db->prepare($sql);
    
    $stmt->bindValue(':editedExpenseId', $editedExpenseId, PDO::PARAM_INT);
    if($newExpenseName != "") $stmt->bindValue(':name', $newExpenseName, PDO::PARAM_STR);
    if($limitValue != "") $stmt->bindValue(':expense_limit', $limitValue, PDO::PARAM_INT);
    return $stmt->execute();
   
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





}
