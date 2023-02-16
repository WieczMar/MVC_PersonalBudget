// Modal select nonstandard Date Range
const dropDownList = document.querySelector('#selectedPeriod');
const periodForm = document.querySelector('#periodForm');

dropDownList.addEventListener('change', function () {
    if (dropDownList.value === "Nonstandard"){
        const myModal = new bootstrap.Modal(document.querySelector('#nonstandardDateRangeModal'),{});
            myModal.show();
    }
    else{
        periodForm.submit();
    }
});

//Get data from tables to draw pieCharts
const incomesTable = document.querySelector('#incomesTable');
const expensesTable = document.querySelector('#expensesTable');

const incomesRowsLength = incomesTable.rows.length;
const expensesRowsLength = expensesTable.rows.length;

let incomeCategories = [];
let incomeAmounts = [];
let expenseCategories = [];
let expenseAmounts = [];

for (let i = 1; i < incomesRowsLength; i++){

    let currentRowCells = incomesTable.rows.item(i).cells;
    incomeCategories.push(currentRowCells.item(0).innerHTML);
    incomeAmounts.push(currentRowCells.item(1).innerHTML);

};

for (let j = 1; j < expensesRowsLength; j++){

	let currentRowCells = expensesTable.rows.item(j).cells;
    expenseCategories.push(currentRowCells.item(0).innerHTML);
    expenseAmounts.push(currentRowCells.item(1).innerHTML);
  
};

// Draw pieCharts
let containerIncomesPieChart = document.querySelector("#incomesPieChart").getContext('2d');
let incomesPieChart = new Chart(containerIncomesPieChart, {
  type: 'pie',
  data: {
    labels: incomeCategories,
    datasets: [{
      data: incomeAmounts,
      backgroundColor: ["#264653", "#0077b6",  "#146428", "#2a9d8f", "#e9c46a", "#f4a261", "#e76f51", "#F7464A", "#9b3228"],
      hoverBackgroundColor: ["#3c6469", "#0f82c3", "#23783c", "#3ca5a0", "#f5cd73", "#ffaf6e", "#f57d5f", "#FF5A5E", "#a54132"]
    }]
  },
  options: {
    responsive: true,
    title:{
        display: true,
        text: "Incomes"
    },
    legend:{
        display: false
    }
  }
});

let containerExpensesPieChart = document.querySelector("#expensesPieChart").getContext('2d');
let expensesPieChart = new Chart(containerExpensesPieChart, {
  type: 'pie',
  data: {
    labels: expenseCategories,
    datasets: [{
      data: expenseAmounts,
      backgroundColor: ["#264653", "#0077b6", "#146428", "#2a9d8f", "#e9c46a", "#f4a261", "#e76f51", "#F7464A", "#9b3228"],
      hoverBackgroundColor: ["#3c6469", "#0f82c3", "#23783c", "#3ca5a0", "#f5cd73", "#ffaf6e", "#f57d5f", "#FF5A5E", "#a54132"]
    }]
  },
  options: {
    responsive: true,
    title:{
        display: true,
        text: "Expenses"
    },
    legend:{
        display: false
    }
  }
});

// Modal category details

// API GET method
const getIncomesForCategory = async (categoryId, startDate, endDate) => {
  return fetch(`http://localhost/api/income-details/${categoryId}?start-date=${startDate}&end-date=${endDate}`)
    .then((response) => response.json());
};

const getExpensesForCategory = async (categoryId, startDate, endDate) => {
  return fetch(`http://localhost/api/expense-details/${categoryId}?start-date=${startDate}&end-date=${endDate}`)
    .then((response) => response.json());
};

const getIncomeCategoryName = async (categoryId) => {
  return fetch(`http://localhost/api/income-category-name/${categoryId}`)
    .then((response) => response.json())
    .then((data) => data[0].name);
};

const getExpenseCategoryName = async (categoryId) => {
  return fetch(`http://localhost/api/expense-category-name/${categoryId}`)
    .then((response) => response.json())
    .then((data) => data[0].name);
};

const renderOnDOM = (categoryName, incomesOrExpensesInCategory) => {
  detailsTableTitle.innerHTML = categoryName;
  let tableBody = document.querySelector("#detailsTable tbody");

  incomesOrExpensesInCategory.forEach(function(incomeOrExpense) {
    let row = tableBody.insertRow();
    let cell0 = row.insertCell(0);
    let cell1 = row.insertCell(1);
    let cell2 = row.insertCell(2);
    cell0.innerHTML = incomeOrExpense.amount;
    cell1.innerHTML = incomeOrExpense.date;
    cell2.innerHTML = incomeOrExpense.comment;
  });
};

const clearDetailsTable = () => {
  let tableBody = document.querySelector("#detailsTable tbody");
  let rows = tableBody.querySelectorAll("tr");

  rows.forEach(function(row) {
    row.remove();
  });
};

const fillDetailsTableWithIncomes = async (categoryId, startDate, endDate) => {
  let incomesInCategory = await getIncomesForCategory(categoryId, startDate, endDate);
  let categoryName = await getIncomeCategoryName(categoryId);
  renderOnDOM(categoryName, incomesInCategory);
};

const fillDetailsTableWithExpenses = async (categoryId, startDate, endDate) => {
  let expensesInCategory = await getExpensesForCategory(categoryId, startDate, endDate);
  let categoryName = await getExpenseCategoryName(categoryId);
  renderOnDOM(categoryName, expensesInCategory);
};

const startDate = document.querySelector('#selectedStartDateValue').innerHTML;
const endDate = document.querySelector('#selectedEndDateValue').innerHTML;
const detailsTableTitle = document.querySelector('#categoryDetailsModalLabel');

// Incomes buttons
const incomeCategoryDetailsButtons = document.querySelectorAll('.incomeCategoryDetailsButton');
incomeCategoryDetailsButtons.forEach(function(incomeCategoryDetailsButton) {
  if (incomeCategoryDetailsButton.getAttribute("value") === "-") {
    incomeCategoryDetailsButton.setAttribute("class", "collapsible");
  } else {
    incomeCategoryDetailsButton.addEventListener("click", function(event) {
      clearDetailsTable();
      const incomeCategoryId = event.target.value;
      fillDetailsTableWithIncomes(incomeCategoryId, startDate, endDate);
    });
  };
});

// Expenses buttons
const expenseCategoryDetailsButtons = document.querySelectorAll('.expenseCategoryDetailsButton');
expenseCategoryDetailsButtons.forEach(function(expenseCategoryDetailsButton) {
  if (expenseCategoryDetailsButton.getAttribute("value") === "-") {
    expenseCategoryDetailsButton.setAttribute("class", "collapsible");
  } else {
    expenseCategoryDetailsButton.addEventListener("click", function(event) {
      clearDetailsTable();
      const expenseCategoryId = event.target.value;
      fillDetailsTableWithExpenses(expenseCategoryId, startDate, endDate);
    });
  };
});