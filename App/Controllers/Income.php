<?php

namespace App\Controllers;

use PDO;
use \Core\View;
use \App\Models\User;
use \App\Auth;
use \App\Flash;

/**
 * Income controller
 * 
 * PHP version 7.0
 */
class Income extends Authenticated {
  /**
   * Before filter - called before each action method
   * 
   * @return void
   */
  protected function before() {
    parent::before();
    
    $this->user = Auth::getUser();
  }

  /**
   * Show the income page
   * 
   * @return void
   */
  public function showAction() {
  View::renderTemplate('Income/show.html', ['user' => $this->user, 'income' => $this->getIncomesCategories(), 'today' => date("Y-m-d")]);
  }

  /**
   * Get incomes categories assigned to user
   * 
   * @return mixed Incomes object if found, false otherwise
   */
  public function getIncomesCategories() {
    $sql = 'SELECT * FROM incomes_category_assigned_to_users WHERE user_id = :id';
    
    $db = User::getDB();
    
    $stmt = $db->prepare($sql);
    $stmt->bindValue(':id', $_SESSION['user_id'], PDO::PARAM_INT);
    
    $stmt->execute();
    
    return $stmt->fetchAll();
  }

  /**
   * Add income
   * 
   * @return void
   */
  public function addAction() {
    if($_POST['number'] == NULL || $_POST['number'] <= 0) {

      Flash::addMessage('Income was not added, please try again', Flash::WARNING);

    } else {

      if ($this->add()) {

        Flash::addMessage('Income added');

      } else {

        Flash::addMessage('Income was not added, please try again', Flash::WARNING);

      }

    }

    $this->redirect('/income/show');
  }
  
  /**
   * Add income to incomes table
   * 
   * @return boolean True if the income was added, false otherwise
   */
  private function add() {
    $sql = 'INSERT INTO incomes (user_id, income_category_assigned_to_user_id, amount, date_of_income, income_comment)
            VALUES (:user_id, :income_category_assigned_to_user_id, :amount, :date_of_income, :income_comment);';

    $db = User::getDB();
        
    $stmt = $db->prepare($sql);
    $stmt->bindValue(':user_id', $_SESSION['user_id'], PDO::PARAM_INT);
    $stmt->bindValue(':income_category_assigned_to_user_id', $_POST['category'], PDO::PARAM_INT);
    $stmt->bindValue(':amount', $_POST['number'], PDO::PARAM_STR);
    $stmt->bindValue(':date_of_income', $_POST['date'], PDO::PARAM_STR);
    if ($_POST['comment']) {
      $stmt->bindValue(':income_comment', $_POST['comment'], PDO::PARAM_STR);
    } else {
      $stmt->bindValue(':income_comment', '', PDO::PARAM_STR);
    }

    return $stmt->execute();
  }
}