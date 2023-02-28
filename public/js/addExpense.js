const categoryDropDownList = document.querySelector('#categoryId');
const dateInput = document.querySelector('#date');
const amountInput = document.querySelector('#amount');
const monthlyLimitBanner = document.querySelector('#monthlyLimitBanner');
const alreadySpentInMonthBanner = document.querySelector('#alreadySpentInMonthBanner');
const collapsibleBanner = document.querySelector('#collapsibleBanner');
const collapsibleLimitWarning = document.querySelector('#collapsibleLimitWarning');
const collapsibleLimitInfo = document.querySelector('#collapsibleLimitInfo');
const predictedSumOfExpensesWarningLabel = document.querySelector('#predictedSumOfExpensesWarningLabel');
const predictedSumOfExpensesLabel = document.querySelector('#predictedSumOfExpensesLabel');

// Set today date by default in date input
const setCurrentDate = () => {
    Date.prototype.toDateInputValue = (function() {
        const local = new Date(this);
        local.setMinutes(this.getMinutes() - this.getTimezoneOffset());
        return local.toJSON().slice(0,10);
    });
    document.querySelector('#date').value = new Date().toDateInputValue(); 
};

// API GET method
const getMonthlyLimitForCategory = async (id) => {
    return fetch(`localhost/api/expense-limit/${id}`)
        .then((response) => response.json())
        .then((data) => data[0].monthly_limit);
};

// API GET method
const getSumOfExpensesInMonthForCategory = async (id, date) => {
    return fetch(`localhost/api/expense-sum/${id}?date=${date}`)
        .then((response) => response.json())
        .then((data) => data[0].categoryAmount);
};

const hideElement = (element) => {
    if (!element.classList.contains("collapsible")) element.classList.toggle("collapsible"); 
};

const showElement = (element) => {
    if (element.classList.contains("collapsible")) element.classList.toggle("collapsible"); 
};

// Render results on view
const renderOnDOM = (monthlyLimitForCategory, sumOfExpensesInMonthForCategory, extraAmount) => {
    const predictedSumOfExpenses = parseInt(sumOfExpensesInMonthForCategory) + parseInt(extraAmount);

    if (monthlyLimitForCategory === null) {
        hideElement(collapsibleBanner);
    }
    else {
        monthlyLimitBanner.innerHTML = monthlyLimitForCategory + " zł";
        showElement(collapsibleBanner);
        alreadySpentInMonthBanner.innerHTML = Math.round(sumOfExpensesInMonthForCategory) + " zł";

        if (Math.round(sumOfExpensesInMonthForCategory) > monthlyLimitForCategory) {
            alreadySpentInMonthBanner.setAttribute("class", "flash-message-warning");
        } else if (Math.round(sumOfExpensesInMonthForCategory) < monthlyLimitForCategory) {
            alreadySpentInMonthBanner.setAttribute("class", "flash-message-success");
        }

        if((predictedSumOfExpenses > monthlyLimitForCategory) && (extraAmount != 0)) {
            predictedSumOfExpensesWarningLabel.innerHTML = predictedSumOfExpenses + " zł";
            hideElement(collapsibleLimitInfo);
            showElement(collapsibleLimitWarning);
        } else if ((predictedSumOfExpenses < monthlyLimitForCategory) && (extraAmount != 0)) {
            predictedSumOfExpensesLabel.innerHTML = predictedSumOfExpenses + " zł";
            hideElement(collapsibleLimitWarning);
            showElement(collapsibleLimitInfo);
        } else {
            hideElement(collapsibleLimitInfo);
            hideElement(collapsibleLimitWarning);
        };
    }
}

// Check limit and total spendings in month for category and  
const checkLimits = async () => {
    const date = dateInput.value;
    const id = categoryDropDownList.value;
    let extraAmount = amountInput.value;

    const monthlyLimitForCategory = await getMonthlyLimitForCategory(id);
    let sumOfExpensesInMonthForCategory = await getSumOfExpensesInMonthForCategory(id, date);

    if(extraAmount === null || extraAmount === undefined || extraAmount === '') extraAmount = 0;
    if(sumOfExpensesInMonthForCategory === null) sumOfExpensesInMonthForCategory = 0;

    renderOnDOM(monthlyLimitForCategory, sumOfExpensesInMonthForCategory, extraAmount);
}

const addListeners = () => {
    categoryDropDownList.addEventListener('change', async () => {
        checkLimits();
    });
    dateInput.addEventListener('change', async () => {
        if (categoryDropDownList.selectedIndex !== 0) {
            checkLimits();
        }
    });
    amountInput.addEventListener('input', async () => {
        if (categoryDropDownList.selectedIndex !== 0) {
            checkLimits();
        }
    });
};

const main = () => {
    setCurrentDate();
    addListeners();
};

main();