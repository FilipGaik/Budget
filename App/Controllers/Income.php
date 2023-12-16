<?php

namespace App\Controllers;

use PDO;
use \Core\View;
use \App\Models\Incomes;
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
  View::renderTemplate('Income/show.html', ['user' => $this->user, 'income' => Incomes::getIncomesCategories($_SESSION['user_id']), 'today' => date("Y-m-d")]);
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

      if (Incomes::addIncome($_SESSION['user_id'], $_POST['category'], $_POST['number'], $_POST['date'], $_POST['comment'])) {

        Flash::addMessage('Income added');

      } else {

        Flash::addMessage('Income was not added, please try again', Flash::WARNING);

      }

    }

    $this->redirect('/income/show');
  }
}
