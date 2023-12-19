const getLimitForCategory = async (category) => {
  try {
    const res = await fetch(`${url}/api/limit/${category}`);

    return await res.json();
  } catch(err) {
    alert(`ERROR: ${err}`);
  }
}

const getExpensesCatAmountAndDate = async () => {
  try {
    const res = await fetch(`${url}/api/getExpensesCatAmountAndDate`);

    return await res.json();
  } catch(err) {
    alert(`ERROR: ${err}`);
  }
}

const amountInput = document.getElementById("amountInput");
const dateInput = document.getElementById("dateInput");
const categorySelect = document.getElementById("category");
const limitSet = document.getElementById("limitSet");
const spent = document.getElementById("spent");
const left = document.getElementById("left");

const changeLimitSetInnerHTML = async () => {
  let nameOfSelectedCategory = categorySelect.options[categorySelect.options.selectedIndex].innerText;
  let limitValue = await getLimitForCategory(nameOfSelectedCategory);

  if(limitValue <= 0) {
    limitSet.innerHTML = `Monthly limit for <b>${nameOfSelectedCategory}</b> category is <b>not</b> set.`;
  } else {
    limitSet.innerHTML = `Monthly limit for <b>${nameOfSelectedCategory}</b> category is set to <b id="limitSetAmount">${limitValue}</b>.`;
  }

  changeLeftInnerHTML();
}

const changeSpentInnerHTML = async () => {
  let nameOfSelectedCategory = categorySelect.options[categorySelect.options.selectedIndex].innerText;
  let expensesCatAmountAndDate = await getExpensesCatAmountAndDate();
  let yearAndMonth = (dateInput.value == '') ? '' : dateInput.value.slice(0, 7);

  if(yearAndMonth == '') {
    spent.innerHTML = "Enter a date.";
  } else {
    let spentAmount = 0;

    for(let i = 0; i < expensesCatAmountAndDate.length; i++) {
      if(expensesCatAmountAndDate[i]["date_of_expense"].slice(0, 7) == yearAndMonth && expensesCatAmountAndDate[i]["expense_category_assigned_to_user_id"] == category.value) {
        spentAmount += parseFloat(expensesCatAmountAndDate[i]["amount"]);
      }
    }

    spentAmount = Math.round(spentAmount * 100) / 100;

    if(spentAmount == 0) {
      spent.innerHTML = `You have <b>no</b> expenses in selected month for <b>${nameOfSelectedCategory}</b> category.`;
    } else {
      spent.innerHTML = `You have spent <b id="spentAmount">${spentAmount}</b> in selected month for <b>${nameOfSelectedCategory}</b> category.`;
    }
  }

  changeLeftInnerHTML();
}

const changeLeftInnerHTML = () => {
  let limitSetAmount = document.getElementById("limitSetAmount");

  if(limitSetAmount == null && dateInput.value == '') {
    left.innerHTML = "Please select a category with limit assigned and enter a date.";
  } else if(dateInput.value == '') {
    left.innerHTML = "Enter a date.";
  } else if(limitSetAmount == null) {
    left.innerHTML = "Please select a category with limit assigned.";
  } else {
    let spentAmount = document.getElementById("spentAmount");
    let balance = (spentAmount == null) ? limitSetAmount.innerText - amountInput.value : limitSetAmount.innerText - spentAmount.innerText - amountInput.value;

    balance = Math.round(balance * 100) / 100;

    if(balance > 0) {
      left.innerHTML = `You still have <b style="color: green;">${balance}</b> to spend to reach the limit.`;
    } else {
      left.innerHTML = `Unfortunately you have spent <b style="color: red;">${balance * (- 1)}</b> over the limit.`;
    }
  }
}

changeLimitSetInnerHTML();
changeSpentInnerHTML();

categorySelect.addEventListener("change", async () => {
  changeLimitSetInnerHTML();
  changeSpentInnerHTML();
});

dateInput.addEventListener("change", async () => {
  changeLimitSetInnerHTML();
  changeSpentInnerHTML();
});

amountInput.addEventListener("input", () => {
  changeLeftInnerHTML();
});
