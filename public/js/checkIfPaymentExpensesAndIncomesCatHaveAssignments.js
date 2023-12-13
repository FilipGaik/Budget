// check if payment method has expenses assigned
let paymentDeleteButtons = document.getElementsByClassName("paymentDeleteBtn");

for(let i = 0; i < paymentDeleteButtons.length; i++) {
  paymentDeleteButtons[i].addEventListener("click", async e => {
    e.preventDefault();
    try {
      const res = await fetch(`${url}/api/getExpenses`);
      const data = await res.json();
      
      let paymentMethodHasExpensesAssigned = false;
      let paymentId = paymentDeleteButtons[i].id.slice(13);

      for(let j = 0; j < data.length; j++) {
        if(data[j]["payment_method_assigned_to_user_id"] == paymentId) {
          paymentMethodHasExpensesAssigned = true;
          break;
        }
      }
      
      if(!paymentMethodHasExpensesAssigned) {
        document.getElementById(`payment-h4-${paymentId}`).innerText = "";
        document.getElementById(`payment-p-${paymentId}`).innerText = "";
      }
    } catch(err) {
      alert(`ERROR: ${err}`);
    }
  });
}

// check if expense category has expenses assigned
let expenseCategoryDeleteButtons = document.getElementsByClassName("expenseCategoryDeleteBtn");

for(let i = 0; i < expenseCategoryDeleteButtons.length; i++) {
  expenseCategoryDeleteButtons[i].addEventListener("click", async e => {
    e.preventDefault();
    try {
      const res = await fetch(`${url}/api/getExpenses`);
      const data = await res.json();
      
      let expenseCategoryHasExpensesAssigned = false;
      let expenseCategoryId = expenseCategoryDeleteButtons[i].id.slice(21);

      for(let j = 0; j < data.length; j++) {
        if(data[j]["expense_category_assigned_to_user_id"] == expenseCategoryId) {
          expenseCategoryHasExpensesAssigned = true;
          break;
        }
      }
      
      if(!expenseCategoryHasExpensesAssigned) {
        document.getElementById(`expenseCat-h4-${expenseCategoryId}`).innerText = "";
        document.getElementById(`expenseCat-p-${expenseCategoryId}`).innerText = "";
      }
    } catch(err) {
      alert(`ERROR: ${err}`);
    }
  });
}

// check if income category has incomes assigned
let incomeCategoryDeleteButtons = document.getElementsByClassName("incomeCategoryDeleteBtn");

for(let i = 0; i < incomeCategoryDeleteButtons.length; i++) {
  incomeCategoryDeleteButtons[i].addEventListener("click", async e => {
    e.preventDefault();
    try {
      const res = await fetch(`${url}/api/getIncomes`);
      const data = await res.json();
      
      let incomeCategoryHasIncomesAssigned = false;
      let incomeCategoryId = incomeCategoryDeleteButtons[i].id.slice(20);

      for(let j = 0; j < data.length; j++) {
        if(data[j]["income_category_assigned_to_user_id"] == incomeCategoryId) {
          incomeCategoryHasIncomesAssigned = true;
          break;
        }
      }
      
      if(!incomeCategoryHasIncomesAssigned) {
        document.getElementById(`incomeCat-h4-${incomeCategoryId}`).innerText = "";
        document.getElementById(`incomeCat-p-${incomeCategoryId}`).innerText = "";
      }
    } catch(err) {
      alert(`ERROR: ${err}`);
    }
  });
}