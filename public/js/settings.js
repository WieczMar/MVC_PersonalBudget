// API GET methods
const getIncomeCategories = async () => {
    return fetch(`http://localhost/api/income-categories`)
      .then((response) => response.json());
};

const getExpenseCategories = async () => {
    return fetch(`http://localhost/api/expense-categories`)
      .then((response) => response.json());
};

const getPaymentMethods = async () => {
    return fetch(`http://localhost/api/expense-payment-methods`)
      .then((response) => response.json());
};

const getUsername = async () => {
    return fetch(`http://localhost/api/username`)
      .then((response) => response.json())
      .then((data) => data[0].username);
};

const getMonthlyLimitForCategory = async (id) => {
  return fetch(`http://localhost/api/expense-limit/${id}`)
      .then((response) => response.json())
      .then((data) => data[0].monthly_limit);
};

// API PUT methods
const editUsername = async (name) => {
  fetch(`http://localhost/api/settings/username`, {
    method: 'PUT', 
    headers: {
      'Content-Type': 'application/json',
    },
    body: JSON.stringify(name),
  })
    .then(async (response) => {
      if (!response.ok) {
        const errorMessage = await response.json();
        throw new Error(errorMessage);
      }
    })
    .then(() => {
      renderUsername();
      console.log('Success: ', "Username updated.");
    })
    .catch((error) => {
      showErrorModal(error);
    });
};

const editPassword = async (data) => {
  fetch(`http://localhost/api/settings/password`, {
    method: 'PUT', 
    headers: {
      'Content-Type': 'application/json',
    },
    body: JSON.stringify(data),
  })
    .then(async (response) => {
      if (!response.ok) {
        const errorMessage = await response.json();
        throw new Error(errorMessage);
      }
    })
    .then(() => {
      console.log('Success: ', "Password updated.");
    })
    .catch((error) => {
      showErrorModal(error);
    });
};

const editCategoryOrPaymentMethod = async (controller, data) => {
  fetch(`http://localhost/api/${controller}`, {
    method: 'PUT', 
    headers: {
      'Content-Type': 'application/json',
    },
    body: JSON.stringify(data),
  })
    .then(async (response) => {
      if (!response.ok) {
        const errorMessage = await response.json();
        throw new Error(errorMessage);
      }
    })
    .then(() => {
      console.log('Success: ', "Item edited.");
      const isFirstPageLoad = false;
      getSettings(isFirstPageLoad);
    })
    .catch((error) => {
      showErrorModal(error);
    });
};

// API POST methods
const addNewCategoryOrPaymentMethod = async (controller, data) => {
  fetch(`http://localhost/api/${controller}-new`, {
    method: 'POST', 
    headers: {
      'Content-Type': 'application/json',
    },
    body: JSON.stringify(data),
  })
    .then(async (response) => {
      if (!response.ok) {
        const errorMessage = await response.json();
        throw new Error(errorMessage);
      }
    })
    .then(() => {
      console.log('Success: ', "New item created.");
      const isFirstPageLoad = false;
      getSettings(isFirstPageLoad);
    })
    .catch((error) => {
      showErrorModal(error);
    });
};

// API DELETE methods
const deleteCategoryOrPaymentMethod = async (controller, id) => {
  fetch(`http://localhost/api/${controller}-dump/${id}`, {
    method: 'DELETE',
  })
    .then(async (response) => {
      if (!response.ok) {
        const errorMessage = await response.json();
        throw new Error(errorMessage);
      }
    })
    .then(() => {
      console.log('Success: ', "Item deleted.");
      const isFirstPageLoad = false;
      getSettings(isFirstPageLoad);
    })
    .catch((error) => {
      showErrorModal(error);
    });
};

const renderItemsInList = (key, list, data) => {
  data.forEach((categoryOrMethod) => {
    let newListItem = document.createElement("li");
    newListItem.setAttribute("class", "list-group-item list-group-item-action d-flex dynamicListItem");

    if((key === "expense-category")&&(categoryOrMethod.monthly_limit != null)){
      var innerHTMLCategoryName = `
        <div class="col-8 col-sm-6">
        ${categoryOrMethod.name}
        <div class="text-secondary">
          Monthly limit: ${categoryOrMethod.monthly_limit}
        </div>
        </div>`;
    } else {
      var innerHTMLCategoryName = `
      <div class="col-8 col-sm-6">
      ${categoryOrMethod.name}
      </div>`;
    };

    const innerHTMLIcons = `
    <div class="col-4 col-sm-6 d-flex justify-content-end align-items-center">
        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-pencil-square edit-icon clickable-icon me-2 ${key}-edit-icon" viewBox="0 0 16 16" 
        id="${key}:${categoryOrMethod.id}:${categoryOrMethod.name}">
            <path style="pointer-events: none;" d="M15.502 1.94a.5.5 0 0 1 0 .706L14.459 3.69l-2-2L13.502.646a.5.5 0 0 1 .707 0l1.293 1.293zm-1.75 2.456-2-2L4.939 9.21a.5.5 0 0 0-.121.196l-.805 2.414a.25.25 0 0 0 .316.316l2.414-.805a.5.5 0 0 0 .196-.12l6.813-6.814z"/>
            <path style="pointer-events: none;" fill-rule="evenodd" d="M1 13.5A1.5 1.5 0 0 0 2.5 15h11a1.5 1.5 0 0 0 1.5-1.5v-6a.5.5 0 0 0-1 0v6a.5.5 0 0 1-.5.5h-11a.5.5 0 0 1-.5-.5v-11a.5.5 0 0 1 .5-.5H9a.5.5 0 0 0 0-1H2.5A1.5 1.5 0 0 0 1 2.5v11z"/>
        </svg>
        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-x-square text-danger clickable-icon delete-icon" viewBox="0 0 16 16"
        id="${key}:${categoryOrMethod.id}:${categoryOrMethod.name}">
            <path style="pointer-events: none;" d="M14 1a1 1 0 0 1 1 1v12a1 1 0 0 1-1 1H2a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1h12zM2 0a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V2a2 2 0 0 0-2-2H2z"/>
            <path style="pointer-events: none;" d="M4.646 4.646a.5.5 0 0 1 .708 0L8 7.293l2.646-2.647a.5.5 0 0 1 .708.708L8.707 8l2.647 2.646a.5.5 0 0 1-.708.708L8 8.707l-2.646 2.647a.5.5 0 0 1-.708-.708L7.293 8 4.646 5.354a.5.5 0 0 1 0-.708z"/>
          </svg>
    </div>`;
    
    newListItem.innerHTML = (innerHTMLCategoryName + innerHTMLIcons);
    list.appendChild(newListItem);
    });
};

const removeAllExistingElementsInList = (list) => {
  const items = list.querySelectorAll('li');
  items.forEach(item => item.remove());
};

const listIncomeCategories = async () => {
  const categories = await getIncomeCategories();
  const categoriesList = document.querySelector('#incomeCategoriesList');
  removeAllExistingElementsInList(categoriesList);
  renderItemsInList("income-category", categoriesList, categories);
};

const listExpenseCategories = async () => {
  const categories = await getExpenseCategories();
  const categoriesList = document.querySelector('#expenseCategoriesList');
  removeAllExistingElementsInList(categoriesList);
  renderItemsInList("expense-category", categoriesList, categories);
};

const listPaymentMethods = async () => {
  const paymentMethods = await getPaymentMethods();
  const paymentMethodsList = document.querySelector('#paymentMethodsList');
  removeAllExistingElementsInList(paymentMethodsList);
  renderItemsInList("expense-payment-method", paymentMethodsList, paymentMethods);
};

const renderUsername = async () => {
  const username = await getUsername();
  const disabledNameInput = document.querySelector('#disabledNameInput');
  disabledNameInput.value = username;  
  const usernameInputModal = document.querySelector('#usernameInputModal');
  usernameInputModal.value = username;  
};

const addEvents = (isFirstPageLoad) => {
  addEditCategoryEvents(isFirstPageLoad);
  addDeleteCategoryEvents(isFirstPageLoad);
  addNewCategoryEvents(isFirstPageLoad);
  addEditUsernameEvents(isFirstPageLoad);
  addEditPasswordEvents(isFirstPageLoad);
};

const addEditCategoryEvents = (isFirstPageLoad) => {
  const editCategoryModal = document.querySelector('#editCategoryModal');
  const editCategoryModalTitle = document.querySelector('#editCategoryModalLabel');
  const nameInputModal = document.querySelector('#nameInputModal');
  const monthlyLimitSettingsModal = document.querySelector('#monthlyLimitSettingsModal');
  const editCategoryButtonModal = document.querySelector('#editCategoryButtonModal');

  const monthlyLimitCheckbox = document.querySelector('#monthlyLimitCheckbox');
  const categoryLimitInput = document.querySelector('#categoryLimitInput');
  const editingItemData = document.querySelector('#editingItemData');

  // Edit income 
  const incomeCategoriesEditIcons = document.querySelectorAll('.income-category-edit-icon');
  incomeCategoriesEditIcons.forEach((incomeCategoriesEditIcon) => {
    incomeCategoriesEditIcon.addEventListener("click", (event) => {
      editCategoryModalTitle.innerHTML = "Edit Income Category";
      editingItemData.innerHTML = event.target.id;
      const incomeCategory = event.target.id.split(":");
      const name = incomeCategory[2];
      nameInputModal.value = name;
      monthlyLimitSettingsModal.setAttribute("class", "collapsible");

      let modal = new bootstrap.Modal(editCategoryModal);
      modal.show();
    });
  });

  // Edit expense 
  const expenseCategoriesEditIcons = document.querySelectorAll('.expense-category-edit-icon');
  expenseCategoriesEditIcons.forEach((expenseCategoriesEditIcon) => {
    expenseCategoriesEditIcon.addEventListener("click", async (event) => {
      editCategoryModalTitle.innerHTML = "Edit Expense Category";
      editingItemData.innerHTML = event.target.id;
      const expenseCategory = event.target.id.split(":");
      const id = expenseCategory[1];
      const monthlyLimit = await getMonthlyLimitForCategory(id);

      if(monthlyLimit === null){
        monthlyLimitCheckbox.checked = false;
        categoryLimitInput.disabled = true;
        categoryLimitInput.value = null;
      } else {
        monthlyLimitCheckbox.checked = true;
        categoryLimitInput.disabled = false;
        categoryLimitInput.value = monthlyLimit;
      }
      const name = expenseCategory[2];
      nameInputModal.value = name;
      monthlyLimitSettingsModal.classList.remove("class", "collapsible");

      let modal = new bootstrap.Modal(editCategoryModal);
      modal.show(); 
    });
  });
   
  // Edit payment method 
  const paymentMethodEditIcons = document.querySelectorAll('.expense-payment-method-edit-icon');
  paymentMethodEditIcons.forEach((paymentMethodEditIcon) => {
    paymentMethodEditIcon.addEventListener("click", (event) => {
      editCategoryModalTitle.innerHTML = "Edit Payment Method";
      editingItemData.innerHTML = event.target.id;
      const paymentMethod = event.target.id.split(":");
      const name = paymentMethod[2];
      nameInputModal.value = name;
      monthlyLimitSettingsModal.setAttribute("class", "collapsible");

      let modal = new bootstrap.Modal(editCategoryModal);
      modal.show();
    });
  });

  if(isFirstPageLoad){
    monthlyLimitCheckbox.addEventListener("click", () => {
      const toggle = monthlyLimitCheckbox.checked === true ? false : true;
      categoryLimitInput.disabled = toggle;
    });
  };
  if(isFirstPageLoad){
    editCategoryButtonModal.addEventListener("click", () => {
      const initialData = editingItemData.innerHTML.split(":");
      const controller = initialData[0];
      if ((controller === "expense-category") && (monthlyLimitCheckbox.checked === true) && (categoryLimitInput.value !== "")) {
        var data = { 
          id: initialData[1],
          name: capitalizeFirstLetters(nameInputModal.value.trim()),
          monthlyLimit: categoryLimitInput.value
        };
      } else {
        var data = { 
          id: initialData[1],
          name: capitalizeFirstLetters(nameInputModal.value.trim())
        };
      };
      editCategoryOrPaymentMethod(controller, data);
    });
  };
};

const addDeleteCategoryEvents = (isFirstPageLoad) => {
  const deleteCategoryModal = document.querySelector('#deleteCategoryModal');
  const deleteCategoryButtonModal = document.querySelector('#deleteCategoryButtonModal');

  const deletingItemName = document.querySelector('#deletingItemName');
  const deletingItemData = document.querySelector('#deletingItemData');
    
  const deleteIcons = document.querySelectorAll('.delete-icon');
  deleteIcons.forEach((deleteIcon) => {
    deleteIcon.addEventListener("click", (event) => {
      deletingItemData.innerHTML = event.target.id;
      const categoryOrMethod = event.target.id.split(":");
      const name = categoryOrMethod[2];
      deletingItemName.innerHTML = name;

      let modal = new bootstrap.Modal(deleteCategoryModal);
      modal.show(); 
    });
  });
  if(isFirstPageLoad){
    deleteCategoryButtonModal.addEventListener("click", () => {
      const initialData = deletingItemData.innerHTML.split(":");
      const controller = initialData[0];
      const id = initialData[1];
      deleteCategoryOrPaymentMethod(controller, id);
    });
  };
};

const addNewCategoryEvents = (isFirstPageLoad) => {
  const addNewCategoryModal = document.querySelector('#addNewCategoryModal');
  const addNewCategoryModalTitle = document.querySelector('#addNewCategoryModalLabel');
  const newNameInputModal = document.querySelector('#newNameInputModal');
  const addNewCategoryButtonModal = document.querySelector('#addNewCategoryButtonModal');
  const newMonthlyLimitSettingsModal = document.querySelector('#newMonthlyLimitSettingsModal');

  const newMonthlyLimitCheckbox = document.querySelector('#newMonthlyLimitCheckbox');
  const newCategoryLimitInput = document.querySelector('#newCategoryLimitInput');
  const addingItemData = document.querySelector('#addingItemData'); 

  const addNewIncomeCategoryButton = document.querySelector('#addNewIncomeCategory');
  const addNewExpenseCategoryButton = document.querySelector('#addNewExpenseCategory');
  const addNewPaymentMethodButton = document.querySelector('#addNewPaymentMethod');

  if(isFirstPageLoad){
    addNewIncomeCategoryButton.addEventListener("click", () => {
      newNameInputModal.value = "";
      addNewCategoryModalTitle.innerHTML = "Add New Income Category";
      addingItemData.innerHTML = "income-category";
      newMonthlyLimitSettingsModal.setAttribute("class", "collapsible");

      let modal = new bootstrap.Modal(addNewCategoryModal);
      modal.show();
    });
  };

  if(isFirstPageLoad){
    addNewExpenseCategoryButton.addEventListener("click", () => {
      addNewCategoryModalTitle.innerHTML = "Add New Expense Category";
      newNameInputModal.value = "";
      newMonthlyLimitCheckbox.checked = false;
      newCategoryLimitInput.disabled = true;
      newCategoryLimitInput.value = "";

      addingItemData.innerHTML = "expense-category";
      newMonthlyLimitSettingsModal.classList.remove("class", "collapsible");

      let modal = new bootstrap.Modal(addNewCategoryModal);
      modal.show();
    });
  };

  if(isFirstPageLoad){
    addNewPaymentMethodButton.addEventListener("click", () => {
      newNameInputModal.value = "";
      addNewCategoryModalTitle.innerHTML = "Add New Payment Method";
      addingItemData.innerHTML = "expense-payment-method";
      newMonthlyLimitSettingsModal.setAttribute("class", "collapsible");

      let modal = new bootstrap.Modal(addNewCategoryModal);
      modal.show();
    });
  };

  if(isFirstPageLoad){
    newMonthlyLimitCheckbox.addEventListener("click", () => {
      const toggle = newMonthlyLimitCheckbox.checked === true ? false : true;
      newCategoryLimitInput.disabled = toggle;
    });
  };

  if(isFirstPageLoad){
    addNewCategoryButtonModal.addEventListener("click", () => {
      const controller = addingItemData.innerHTML;
      if ((controller === "expense-category") && (newMonthlyLimitCheckbox.checked === true) && (newCategoryLimitInput.value !== "")) {
        var data = { 
          name: capitalizeFirstLetters(newNameInputModal.value.trim()),
          monthlyLimit: newCategoryLimitInput.value
        };
      } else {
        var data = { 
          name: capitalizeFirstLetters(newNameInputModal.value.trim())
        };
      };
      addNewCategoryOrPaymentMethod(controller, data);
    });
  };
};

const addEditUsernameEvents = (isFirstPageLoad) => {
  const editUsernameIcon = document.querySelector('#editUsernameIcon');
  const usernameButtonModal = document.querySelector('#editUsernameButtonModal');
  const usernameInputModal = document.querySelector('#usernameInputModal');

  if(isFirstPageLoad){
    editUsernameIcon.addEventListener("click", () => {
      usernameInputModal.value = disabledNameInput.value;
    });
  };
  if(isFirstPageLoad){
    usernameButtonModal.addEventListener("click", () => {
      let name = capitalizeFirstLetters(usernameInputModal.value.trim());
      editUsername(name);
    });
  };
};

const addEditPasswordEvents = (isFirstPageLoad) => {
  const editPasswordIcon = document.querySelector('#editPasswordIcon');
  const toggleOldPassword = document.querySelector('#toggleOldPassword');
  const oldPasswordInputModal = document.querySelector('#oldPasswordInputModal');
  const toggleNewPassword = document.querySelector('#toggleNewPassword');
  const newPasswordInputModal = document.querySelector('#newPasswordInputModal');
  const editPasswordButtonModal = document.querySelector('#editPasswordButtonModal');

  if(isFirstPageLoad){
    editPasswordIcon.addEventListener('click', function () {
      oldPasswordInputModal.value = "";
      oldPasswordInputModal.setAttribute("type", "password");
      newPasswordInputModal.value = "";
      newPasswordInputModal.setAttribute("type", "password");
    });
  };
  if(isFirstPageLoad){
    toggleOldPassword.addEventListener('click', function () {
      const type = oldPasswordInputModal.getAttribute("type") === "password" ? "text" : "password";
      oldPasswordInputModal.setAttribute("type", type);
      this.classList.toggle("bi-eye");
    });
  };
  if(isFirstPageLoad){
    toggleNewPassword.addEventListener('click', function () {
      const type = newPasswordInputModal.getAttribute("type") === "password" ? "text" : "password";
      newPasswordInputModal.setAttribute("type", type);
      this.classList.toggle("bi-eye");
    });
  };
  if(isFirstPageLoad){
    editPasswordButtonModal.addEventListener("click", () => {
      data = {
        oldPassword: oldPasswordInputModal.value,
        newPassword: newPasswordInputModal.value
      };
      editPassword(data);
    });
  };
};

const showErrorModal = (message) => {
  const errorModal = document.querySelector('#errorModal');
  const errorMessageModal = document.querySelector('#errorMessageModal');
  errorMessageModal.innerHTML = message;
  let modal = new bootstrap.Modal(errorModal);
  modal.show();
};

const capitalizeFirstLetters = (string) => {
  return string.replace(/\b\w/g, (txt) => {
    return txt.charAt(0).toUpperCase() + txt.slice(1).toLowerCase();
  });
}

const getSettings = async (isFirstPageLoad) => {
  await listIncomeCategories();
  await listExpenseCategories();
  await listPaymentMethods();
  await renderUsername();
  addEvents(isFirstPageLoad);
};

const main = () => {
  let isFirstPageLoad = true;
  getSettings(isFirstPageLoad);
};

main();








