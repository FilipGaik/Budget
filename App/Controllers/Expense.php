<?php

namespace App\Controllers;

use PDO;
use \Core\View;
use \App\Models\Expenses;
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
    View::renderTemplate('Expense/show.html', ['user' => $this->user, 'expense' => Expenses::getExpensesCategories($_SESSION['user_id']), 'payment' => Expenses::getPaymentMethods($_SESSION['user_id']), 'today' => date("Y-m-d")]);
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

      if (Expenses::addExpense($_SESSION['user_id'], $_POST['category'], $_POST['payment'], $_POST['number'], $_POST['date'], $_POST['comment'])) {

        Flash::addMessage('Expense added');

      } else {

        Flash::addMessage('Expense was not added, please try again', Flash::WARNING);

      }
    }

    $this->redirect('/expense/show');
  }
  
  /**
   * Get limit
   * 
   * @return float limit
   */
  public function limitAction() {
    $user_id = $this->user->id;
    $category = $this->route_params['category'];

    echo json_encode((float)Expenses::getLimit($user_id, $category)[0]['limit'], JSON_UNESCAPED_UNICODE);
  }

  /**
   * Get expense id, category name, amount and date
   * 
   * @return array
   */
  public function getExpensesCatAmountAndDateAction() {
    echo json_encode(Expenses::getExpensesCatAmountAndDate($_SESSION['user_id']), JSON_UNESCAPED_UNICODE);
  }
}
