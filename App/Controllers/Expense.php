<?php

namespace App\Controllers;

use PDO;
use \Core\View;
use \App\Models\User;
use \App\Auth;
use \App\Flash;

/**
 * Expense controller
 * 
 * PHP version 7.0
 */
class Expense extends Authenticated {
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
   * Show the expense page
   * 
   * @return void
   */
  public function showAction() {
    View::renderTemplate('Expense/show.html', ['user' => $this->user, 'expense' => $this->getExpensesCategories(), 'payment' => $this->getPaymentMethods(), 'today' => date("Y-m-d")]);
  }

  /**
   * Get expenses categories assigned to user
   * 
   * @return mixed Expenses object if found, false otherwise
   */
  public function getExpensesCategories() {
    $sql = 'SELECT * FROM expenses_category_assigned_to_users WHERE user_id = :id';
    
    $db = User::getDB();
    
    $stmt = $db->prepare($sql);
    $stmt->bindValue(':id', $_SESSION['user_id'], PDO::PARAM_INT);
    
    $stmt->execute();
    
    return $stmt->fetchAll();
  }

  /**
   * Get payment methods assigned to user
   * 
   * @return mixed Payment object if found, false otherwise
   */
  public function getPaymentMethods() {
    $sql = 'SELECT * FROM payment_methods_assigned_to_users WHERE user_id = :id';
    
    $db = User::getDB();
    
    $stmt = $db->prepare($sql);
    $stmt->bindValue(':id', $_SESSION['user_id'], PDO::PARAM_INT);
    
    $stmt->execute();
    
    return $stmt->fetchAll();
  }

  /**
   * Add expense
   * 
   * @return void
   */
  public function addAction() {
    if($_POST['number'] == NULL || $_POST['number'] <= 0) {

      Flash::addMessage('Expense was not added, please try again', Flash::WARNING);

    } else {

      if ($this->add()) {

        Flash::addMessage('Expense added');

      } else {

        Flash::addMessage('Expense was not added, please try again', Flash::WARNING);

      }

    }

    $this->redirect('/expense/show');
  }
  
  /**
   * Add expense to expense table
   * 
   * @return boolean True if the expense was added, false otherwise
   */
  private function add() {
    $sql = 'INSERT INTO expenses (user_id, expense_category_assigned_to_user_id, payment_method_assigned_to_user_id, amount, date_of_expense, expense_comment)
            VALUES (:user_id, :expense_category_assigned_to_user_id, :payment_method_assigned_to_user_id, :amount, :date_of_expense, :expense_comment);';

    $db = User::getDB();
        
    $stmt = $db->prepare($sql);
    $stmt->bindValue(':user_id', $_SESSION['user_id'], PDO::PARAM_INT);
    $stmt->bindValue(':expense_category_assigned_to_user_id', $_POST['category'], PDO::PARAM_INT);
    $stmt->bindValue(':payment_method_assigned_to_user_id', $_POST['payment'], PDO::PARAM_INT);
    $stmt->bindValue(':amount', $_POST['number'], PDO::PARAM_STR);
    $stmt->bindValue(':date_of_expense', $_POST['date'], PDO::PARAM_STR);
    if ($_POST['comment']) {
      $stmt->bindValue(':expense_comment', $_POST['comment'], PDO::PARAM_STR);
    } else {
      $stmt->bindValue(':expense_comment', '', PDO::PARAM_STR);
    }

    return $stmt->execute();
  }

  /**
   * Get limit from expenses_category_assigned_to_users table for user and category
   * 
   * @return array Limit
   */
  private function getLimit($user_id, $category) {
    $sql = 'SELECT `limit` FROM expenses_category_assigned_to_users WHERE user_id = :id AND name = :category';
    
    $db = User::getDB();
    
    $stmt = $db->prepare($sql);
    $stmt->bindValue(':id', $user_id, PDO::PARAM_INT);
    $stmt->bindValue(':category', $category, PDO::PARAM_STR);
    
    $stmt->execute();
    
    return $stmt->fetchAll();
  }

  /**
   * Get limit
   * 
   * @return float limit
   */
  public function limitAction() {
    $user_id = $this->user->id;
    $category = $this->route_params['category'];

    echo json_encode((float)$this->getLimit($user_id, $category)[0]['limit'], JSON_UNESCAPED_UNICODE);
  }

  /**
   * Get expense id, category name, amount and date
   * 
   * @return array
   */
  public function getExpensesCatAmountAndDateAction() {
    $sql = 'SELECT id, expense_category_assigned_to_user_id, amount, date_of_expense
            FROM expenses
            WHERE user_id = :id;';
    
    $db = User::getDB();
    
    $stmt = $db->prepare($sql);
    $stmt->bindValue(':id', $_SESSION['user_id'], PDO::PARAM_INT);
    
    $stmt->execute();
    
    $arr = $stmt->fetchAll();

    echo json_encode($arr, JSON_UNESCAPED_UNICODE);
  }
}