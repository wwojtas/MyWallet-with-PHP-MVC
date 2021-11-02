<?php

namespace App\Models;

use PDO;
use App\Auth;

/**
 * Remembered login model
 *
 * PHP version 7.0
 */
class Income extends \Core\Model
{

 public $errors = [];

 public function __construct($data = [])
 {
   foreach ($data as $key => $value) {
     $this->$key = $value;
   };
 }

/**
 * get income category 
 */
  public static function getDataIncome($selectSearchTerm)
 {
   $user = Auth::getUser();
   
   if(!isset($selectSearchTerm))
     {
       $fetchData = static::getCategoryIncomeName();
     }
   else
     {
       $sql = "SELECT * FROM incomes_category_assigned_to_users WHERE name LIKE '%".$selectSearchTerm."%' AND user_id = '$user->id'";
       $fetchData = static::getUsersSelect($sql);
     }

   $data = array();
   foreach ($fetchData as $row) {
     $data[] = array('id'=>$row['id'], 'text'=>$row['name']);
   };
   echo json_encode($data);
 }


 
 public static function getUsersSelect($sql)
 {
   $db = static::getDB();
   $stmt = $db->prepare($sql);
   $stmt->execute();
   return $stmt->fetchAll(PDO::FETCH_ASSOC);
 }



 public function addNewIncome()
 {
    $user = Auth::getUser();
    $this->validate();
      if (empty($this->errors))
      {
        $sql = "INSERT INTO incomes (user_id, income_category_assigned_to_user_id, amount, date_of_income, income_comment)
                VALUES (:user_id, :income_category, :amount, :date_of_income, :income_comment)";

        $db = static::getDB();
        $stmt = $db->prepare($sql);

        $stmt->bindValue(':user_id', $user->id, PDO::PARAM_INT);
        $stmt->bindValue(':income_category', $this->income_category, PDO::PARAM_INT);
        $stmt->bindValue(':amount', $this->amount, PDO::PARAM_STR);
        $stmt->bindValue(':date_of_income', $this->date_of_income, PDO::PARAM_STR);
        $stmt->bindValue(':income_comment', $this->income_comment, PDO::PARAM_STR);

        return $stmt->execute();
      }
      return false;
 }



 public function validate()
  {
    if($this->amount == '')
    {
      $this->errors[] = 'Podaj wartość przychodu';
    }
    if($this->date_of_income == '')
    {
      $this->errors[] = 'Podaj datę';
    }
    if($this->income_category == 0)
    {
      $this->errors[] = 'Wybierz kategorię';
    }
  }


  public static function getCategoryIncomeName()
  {
    $user = Auth::getUser();
    $sql = "SELECT * FROM incomes_category_assigned_to_users WHERE user_id = '$user->id' ORDER BY name";
    return  $fetchData = static::getUsersSelect($sql);
  }

}
