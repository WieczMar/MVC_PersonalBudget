const addPeriodOptionListener = () => {
  const dropDownList = document.querySelector('#selectedPeriod');
  const periodForm = document.querySelector('#periodForm');

  dropDownList.addEventListener('change', () => {
      if (dropDownList.value === "Nonstandard"){
          const myModal = new bootstrap.Modal(document.querySelector('#nonstandardDateRangeModal'),{});
              myModal.show();
      }
      else{
          periodForm.submit();
      }
  });
};


const drawPieCharts = () => {
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
    const currentRowCells = incomesTable.rows.item(i).cells;
      incomeCategories.push(currentRowCells.item(0).innerHTML);
      incomeAmounts.push(currentRowCells.item(1).innerHTML);
  };

  for (let j = 1; j < expensesRowsLength; j++){
    const currentRowCells = expensesTable.rows.item(j).cells;
      expenseCategories.push(currentRowCells.item(0).innerHTML);
      expenseAmounts.push(currentRowCells.item(1).innerHTML);
  };

  // Draw pieCharts
  const containerIncomesPieChart = document.querySelector("#incomesPieChart").getContext('2d');
  const incomesPieChart = new Chart(containerIncomesPieChart, {
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

  const containerExpensesPieChart = document.querySelector("#expensesPieChart").getContext('2d');
  const expensesPieChart = new Chart(containerExpensesPieChart, {
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
};

// API GET methods
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

// API DELETE method
const deleteItem = async (controller, id) => {
  fetch(`http://localhost/api/${controller}-dump/${id}`, {
    method: 'DELETE',
  })
    .then(async (response) => {
      if (!response.ok) {
        const errorMessage = await response.json();
        throw new Error(errorMessage);
      } else {
        return response.json();
      }
    })
    .then((data) => {
      console.log('Success: ', data);
      closeCategoryDetailsModal();
      showConfirmationModal(data);
    })
    .catch((error) => {
      closeCategoryDetailsModal();
      showConfirmationModal(error);
    });
};

// Modal data rendering
const renderOnModal = (key, categoryName, incomesOrExpensesInCategory) => {
  const detailsTableTitle = document.querySelector('#categoryDetailsModalLabel');
  detailsTableTitle.innerHTML = categoryName;
  const tableBody = document.querySelector("#detailsTable tbody");

  incomesOrExpensesInCategory.forEach((incomeOrExpense) => {
    const row = tableBody.insertRow();
    const cell0 = row.insertCell(0);
    const cell1 = row.insertCell(1);
    const cell2 = row.insertCell(2);
    const cell3 = row.insertCell(3);
    cell0.innerHTML = incomeOrExpense.amount;
    cell1.innerHTML = incomeOrExpense.date;
    cell2.innerHTML = incomeOrExpense.comment;
    cell3.innerHTML = insertDeleteIcon(key, incomeOrExpense.id);
  });
};

const insertDeleteIcon = (key, id) => {
  const innerHTML = `
    <div class="d-flex justify-content-center mt-1">
    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-x-square text-danger clickable-icon delete-icon" viewBox="0 0 16 16"
    id="${key}:${id}">
        <path style="pointer-events: none;" d="M14 1a1 1 0 0 1 1 1v12a1 1 0 0 1-1 1H2a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1h12zM2 0a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V2a2 2 0 0 0-2-2H2z"/>
        <path style="pointer-events: none;" d="M4.646 4.646a.5.5 0 0 1 .708 0L8 7.293l2.646-2.647a.5.5 0 0 1 .708.708L8.707 8l2.647 2.646a.5.5 0 0 1-.708.708L8 8.707l-2.646 2.647a.5.5 0 0 1-.708-.708L7.293 8 4.646 5.354a.5.5 0 0 1 0-.708z"/>
      </svg>
  </div>`;
  return innerHTML;
};

const clearDetailsTable = () => {
  const tableBody = document.querySelector("#detailsTable tbody");
  const rows = tableBody.querySelectorAll("tr");
  rows.forEach(function(row) {
    row.remove();
  });
};

const addDeleteIconsListeners = () => {
  const deleteIcons = document.querySelectorAll('.delete-icon');
  deleteIcons.forEach((deleteIcon) => {
    deleteIcon.addEventListener("click", (event) => {
      const itemData = event.target.id.split(":");
      const key = itemData[0];
      const id = itemData[1];
      deleteItem(key, id);
    });
  });
};

const fillDetailsTableWithIncomes = async (categoryId, startDate, endDate) => {
  const incomesInCategory = await getIncomesForCategory(categoryId, startDate, endDate);
  const categoryName = await getIncomeCategoryName(categoryId);
  renderOnModal('income', categoryName, incomesInCategory);
  addDeleteIconsListeners();
};

const fillDetailsTableWithExpenses = async (categoryId, startDate, endDate) => {
  const expensesInCategory = await getExpensesForCategory(categoryId, startDate, endDate);
  const categoryName = await getExpenseCategoryName(categoryId);
  renderOnModal('expense', categoryName, expensesInCategory);
  addDeleteIconsListeners();
};

const addDetailsButtonsListeners = () => {
  const startDate = document.querySelector('#selectedStartDateValue').innerHTML;
  const endDate = document.querySelector('#selectedEndDateValue').innerHTML;

  // Incomes buttons
  const incomeCategoryDetailsButtons = document.querySelectorAll('.incomeCategoryDetailsButton');
  incomeCategoryDetailsButtons.forEach((incomeCategoryDetailsButton) => {
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
  expenseCategoryDetailsButtons.forEach((expenseCategoryDetailsButton) => {
    if (expenseCategoryDetailsButton.getAttribute("value") === "-") {
      expenseCategoryDetailsButton.setAttribute("class", "collapsible");
    } else {
      expenseCategoryDetailsButton.addEventListener("click", (event) => {
        clearDetailsTable();
        const expenseCategoryId = event.target.value;
        fillDetailsTableWithExpenses(expenseCategoryId, startDate, endDate);
      });
    };
  });
};

const showConfirmationModal = (message) => {
  const delitingConfirmationModal = document.querySelector('#delitingConfirmationModal');
  const delitingConfirmationMessageModal = document.querySelector('#delitingConfirmationMessageModal');
  delitingConfirmationMessageModal.innerHTML = message;
  let modal = new bootstrap.Modal(delitingConfirmationModal);
  modal.show();
};

const closeCategoryDetailsModal = () => {
  const categoryDetailsModal = document.querySelector('#categoryDetailsModal');
  let modal = new bootstrap.Modal(categoryDetailsModal);
  modal.hide();
};

const addDelitingConfirmationButtonModalListener = () => {
  const delitingConfirmationButtonModal = document.querySelector('#delitingConfirmationButtonModal');
  delitingConfirmationButtonModal.addEventListener("click", () => {
    location.reload();
  });
};

const getBalance = () => {
  addPeriodOptionListener();
  drawPieCharts();
  addDetailsButtonsListeners();
  addDelitingConfirmationButtonModalListener();
;}

getBalance();