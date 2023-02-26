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
    return fetch(`http://localhost/api/expense-limit/${id}`)
        .then((response) => response.json())
        .then((data) => data[0].monthly_limit);
}

// API GET method
const getSumOfExpensesInMonthForCategory = async (id, date) => {
    return fetch(`http://localhost/api/expense-sum/${id}?date=${date}`)
        .then((response) => response.json())
        .then((data) => data[0].categoryAmount);
}

// Render results on view
const renderOnDOM = (monthlyLimitForCategory, sumOfExpensesInMonthForCategory) => {
    const categoryDropDownList = document.querySelector('#categoryId');
    const dateInput = document.querySelector('#date');
    const monthlyLimitBanner = document.querySelector('#monthlyLimitBanner');
    const alreadySpentInMonthBanner = document.querySelector('#alreadySpentInMonthBanner');
    const collapsibleBanner = document.querySelector('#collapsibleBanner');

    if (monthlyLimitForCategory === null) {
        if (!collapsibleBanner.classList.contains("collapsible")) 
            collapsibleBanner.classList.toggle("collapsible");
    }
    else {
        monthlyLimitBanner.innerHTML = monthlyLimitForCategory + " zł";
        if (collapsibleBanner.classList.contains("collapsible")) 
            collapsibleBanner.classList.toggle("collapsible");
    }

    alreadySpentInMonthBanner.innerHTML = Math.round(sumOfExpensesInMonthForCategory) + " zł";

    if (Math.round(sumOfExpensesInMonthForCategory) > monthlyLimitForCategory)
        alreadySpentInMonthBanner.setAttribute("class", "flash-message-warning");
    else if (Math.round(sumOfExpensesInMonthForCategory) < monthlyLimitForCategory)
        alreadySpentInMonthBanner.setAttribute("class", "flash-message-success");
}

// Check limit and total spendings in month for category and  
const checkLimits = async (id, date) => {
    const monthlyLimitForCategory = await getMonthlyLimitForCategory(id);
    const sumOfExpensesInMonthForCategory = await getSumOfExpensesInMonthForCategory(id, date);
    renderOnDOM(monthlyLimitForCategory, sumOfExpensesInMonthForCategory);
}

const addListeners = () => {
    // Category changing
    categoryDropDownList.addEventListener('change', function () {
        const date = dateInput.value;
        const id = categoryDropDownList.value;
        checkLimits(id, date);
    });

    // Date changing
    dateInput.addEventListener('change', function () {
        const date = dateInput.value;
        const id = categoryDropDownList.value;
        checkLimits(id, date);
    });
};

const main = () => {
    setCurrentDate();
    addListeners();
};

main();