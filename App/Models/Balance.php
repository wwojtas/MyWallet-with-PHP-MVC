<?php

namespace App\Models;

use PDO;
use App\Auth;

class Balance extends \Core\Model
{
	/**
     * Class contructor
     * 
     * @param array $data Initial property values
     * 
     * @return void
     */
    public function __construct($data = []) {
        foreach ($data as $key => $value) {
            $this -> $key = $value;
        }
    }

	public static function getIncomesData($balance)
	{
		$user = Auth::getUser();

		if($balance  == 'current_month') {
			$start_date = date("Y-m-01");
			$end_date = date("Y-m-d");

        } else if ($balance  == 'previous_month') {
            $start_date =  date('Y-m-d', strtotime(date('Y-m-01').' -1 MONTH'));
			$end_date = date('Y-m-d', strtotime("LAST DAY OF PREVIOUS MONTH"));

        } else if ($balance  == 'current_year') {
			$start_date = date("Y-01-01");
			$end_date = date("Y-m-d");

        } else if ($balance  == 'selected_period') {
			$start_date = $_SESSION['start_date'];
			$end_date = $_SESSION['end_date'];
		}

		$sql = "SELECT icatu.name, incomes.amount, incomes.date_of_income 
				FROM users 
				INNER JOIN incomes ON users.id = incomes.user_id 
				INNER JOIN incomes_category_assigned_to_users AS icatu ON incomes.income_category_assigned_to_user_id = icatu.id 
				WHERE users.id = :user_id AND incomes.date_of_income >= :startDate AND incomes.date_of_income <= :endDate
				ORDER BY incomes.date_of_income DESC";

		$db = static::getDB();
		$stmt = $db->prepare($sql);

		$stmt -> bindValue(':user_id', $user->id, PDO::PARAM_INT);
		$stmt -> bindValue(':startDate', $start_date, PDO::PARAM_STR);
		$stmt -> bindValue(':endDate', $end_date, PDO::PARAM_STR);
		$stmt->execute();

		return $stmt->fetchAll();
	}


	public static function getExpensesData($balance)
	{
		$user = Auth::getUser();

		if($balance  == 'current_month') {
			$start_date = date("Y-m-01");
			$end_date = date("Y-m-d");

        } else if ($balance  == 'previous_month') {
            $start_date =  date('Y-m-d', strtotime(date('Y-m-01').' -1 MONTH'));
			$end_date = date('Y-m-d', strtotime("LAST DAY OF PREVIOUS MONTH"));

        } else if ($balance  == 'current_year') {
			$start_date = date("Y-01-01");
			$end_date = date("Y-m-d");

        } else if ($balance  == 'selected_period') {
			$start_date = $_SESSION['start_date'];
			$end_date = $_SESSION['end_date'];
		}

		$sql = 	"SELECT ecatu.name, expenses.amount, expenses.date_of_expense 
				FROM users 
				INNER JOIN expenses ON users.id = expenses.user_id 
				INNER JOIN expenses_category_assigned_to_users AS ecatu ON expenses.expense_category_assigned_to_user_id = ecatu.id 
				WHERE users.id = :user_id AND expenses.date_of_expense >= :startDate AND  expenses.date_of_expense <= :endDate
				ORDER BY expenses.date_of_expense DESC";

		$db = static::getDB();
		$stmt = $db->prepare($sql);
		$stmt -> bindValue(':user_id', $user->id, PDO::PARAM_INT);
		$stmt -> bindValue(':startDate', $start_date, PDO::PARAM_STR);
		$stmt -> bindValue(':endDate', $end_date, PDO::PARAM_STR);
		$stmt->execute();
	
		return $stmt->fetchAll();
	}

	





}