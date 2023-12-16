<?php

namespace App\Models;

use PDO;
use Core\View;

/**
 * Incomes model
 * 
 * PHP version 7.0
 */
class Incomes extends \Core\Model {
  /**
   * Get incomes categories assigned to user
   * 
   * @return mixed Incomes object if found, false otherwise
   */
  public static function getIncomesCategories($user_id) {
    $sql = 'SELECT * FROM incomes_category_assigned_to_users WHERE user_id = :id';
    
    $db = static::getDB();
    
    $stmt = $db->prepare($sql);
    $stmt->bindValue(':id', $user_id, PDO::PARAM_INT);
    
    $stmt->execute();
    
    return $stmt->fetchAll();
  }

  /**
   * Add income to incomes table
   * 
   * @return boolean True if the income was added, false otherwise
   */
  public static function addIncome($user_id, $income_category_assigned_to_user_id, $amount, $date_of_income, $income_comment) {
    $sql = 'INSERT INTO incomes (user_id, income_category_assigned_to_user_id, amount, date_of_income, income_comment)
            VALUES (:user_id, :income_category_assigned_to_user_id, :amount, :date_of_income, :income_comment);';

    $db = static::getDB();
        
    $stmt = $db->prepare($sql);
    $stmt->bindValue(':user_id', $user_id, PDO::PARAM_INT);
    $stmt->bindValue(':income_category_assigned_to_user_id', $income_category_assigned_to_user_id, PDO::PARAM_INT);
    $stmt->bindValue(':amount', $amount, PDO::PARAM_STR);
    $stmt->bindValue(':date_of_income', $date_of_income, PDO::PARAM_STR);
    if ($income_comment) {

      $stmt->bindValue(':income_comment', $income_comment, PDO::PARAM_STR);

    } else {
      
      $stmt->bindValue(':income_comment', '', PDO::PARAM_STR);
    }

    return $stmt->execute();
  }
}