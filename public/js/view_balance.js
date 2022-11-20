//Modal select nonstandard Date Range
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

}

for (let j = 1; j < expensesRowsLength; j++){

	let currentRowCells = expensesTable.rows.item(j).cells;
    expenseCategories.push(currentRowCells.item(0).innerHTML);
    expenseAmounts.push(currentRowCells.item(1).innerHTML);
  
}

//draw pieCharts
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