function closeModal(modalId) {
  const modal = document.getElementById(modalId);
  modal.close();
}

function resetAddModal() {
  const addModal = document.getElementById("addModal");
  const editModal = document.getElementById("editModal");
  const form = addModal.querySelector("form");

  const errorContainer = document.querySelector(".error-container");
  const errorContainer2 = document.querySelector(".error-container2");
  const addNewEmpModal = document.getElementById("addNewEmpModal");
  const form2 = addNewEmpModal.querySelector("form");

  const errorContainer3 = document.querySelector(".error-container3");
  form2.reset();
  form.reset();
  errorContainer3.style.display = "none";
  errorContainer.style.display = "none";
  errorContainer2.style.display = "none";
  setTimeout(function () {
    addModal.close();
    editModal.close();
    addNewEmpModal.close();
  }, 0);
}

function addOverlayClickListener(modalId) {
  const modal = document.getElementById(modalId);
  modal.addEventListener("click", function (event) {
    if (event.target === modal) {
      resetAddModal();
      modal.close();
    }
  });
}

function showModal(modalId) {
  const modal = document.getElementById(modalId);
  modal.showModal();
  addOverlayClickListener(modalId);
}

function editemp(
  emp_id,
  emp_password,
  emp_email,
  department_id,
  firstname,
  lastname,
  role_id
) {
  console.log("emp_id:", emp_id);
  console.log("emp_password:", emp_password);
  console.log("emp_email:", emp_email);
  console.log("department_id:", department_id);
  console.log("firstname:", firstname);
  console.log("lastname:", lastname);
  console.log("role_id:", role_id);

  showModal("editModal");

  document.getElementById("edit-emp-id").value = emp_id;
  document.getElementById("edit-emp-password").value = emp_password;
  document.getElementById("edit-email").value = emp_email;
  document.getElementById("original-email").value = emp_email;

  const departmentDropdown = document.getElementById("edit-department");
  for (let i = 0; i < departmentDropdown.options.length; i++) {
    if (departmentDropdown.options[i].value == department_id) {
      departmentDropdown.selectedIndex = i;
      break;
    }
  }

  document.getElementById("edit-firstname").value = firstname;
  document.getElementById("edit-lastname").value = lastname;

  const roleDropdown = document.getElementById("edit-role");
  for (let i = 0; i < roleDropdown.options.length; i++) {
    if (roleDropdown.options[i].value == role_id) {
      roleDropdown.selectedIndex = i;
      break;
    }
  }
}

addOverlayClickListener("deleteModal");
addOverlayClickListener("editModal");
addOverlayClickListener("addModal");
addOverlayClickListener("addNewEmpModal");

function changeLimit(newLimit) {
  const currentUrl = new URL(window.location.href);
  const searchParam = currentUrl.searchParams.get("search");
  currentUrl.searchParams.set("limit", newLimit);
  if (searchParam) {
    currentUrl.searchParams.set("search", searchParam);
  }
  history.pushState({}, "", currentUrl);
  window.location.reload();
}

function changePage(newPage) {
  const currentUrl = new URL(window.location.href);
  const limitParam = currentUrl.searchParams.get("limit");
  window.location.href = base_url + "?page=" + newPage + "&limit=" + limitParam;
}

function clearSearch() {
  const currentUrl = new URL(window.location.href);
  currentUrl.searchParams.delete("search");
  currentUrl.searchParams.delete("page");
  history.pushState({}, "", currentUrl);
  window.location.reload();
}

document
  .getElementById("searchForm")
  .addEventListener("submit", function (event) {
    event.preventDefault();
    const searchInput = document.getElementById("search");
    const searchTerm = searchInput.value;
    const currentUrl = new URL(window.location.href);

    if (searchTerm.trim() !== "") {
      currentUrl.searchParams.set("search", searchTerm);
    } else {
      currentUrl.searchParams.delete("search");
    }

    currentUrl.searchParams.delete("page");

    history.pushState({}, "", currentUrl);
    window.location.reload();
  });

function showDeleteModal(emp_id) {
  showModal("deleteModal");
  const deleteBtn = document
    .getElementById("deleteModal")
    .querySelector(".deletebtn");
  deleteBtn.addEventListener("click", function () {
    const form = document.createElement("form");
    form.setAttribute("method", "POST");
    form.setAttribute("action", base_url);
    const hiddenField = document.createElement("input");
    hiddenField.setAttribute("type", "hidden");
    hiddenField.setAttribute("name", "delete_emp");
    hiddenField.setAttribute("value", emp_id);
    form.appendChild(hiddenField);
    document.body.appendChild(form);
    form.submit();
  });
}

function deleteemp() {
  const emp_id = document.getElementById("edit-emp-id").value;
  const form = document.createElement("form");
  form.setAttribute("method", "POST");
  form.setAttribute("action", base_url);

  const hiddenField = document.createElement("input");
  hiddenField.setAttribute("type", "hidden");
  hiddenField.setAttribute("name", "delete_emp");
  hiddenField.setAttribute("value", emp_id);

  form.appendChild(hiddenField);
  document.body.appendChild(form);
  form.submit();
}

function isEmailTaken(email) {
  return emailExistenceCheck.includes(email);
}

document
  .getElementById("addModal")
  .querySelector("form")
  .addEventListener("submit", function (event) {
    const emailInput = document.getElementById("emp-email");
    const email = emailInput.value.trim();
    const errorContainer = document.querySelector(".error-container");

    if (isEmailTaken(email)) {
      event.preventDefault();
      errorContainer.style.display = "block";
    } else {
      errorContainer.style.display = "none";
    }
  });

document
  .getElementById("addNewEmpModal")
  .querySelector("form")
  .addEventListener("submit", function (event) {
    const emailInput = document.getElementById("emp-emails");
    const email = emailInput.value.trim();
    const errorContainer3 = document.querySelector(".error-container3");

    if (isEmailTaken(email)) {
      event.preventDefault();
      errorContainer3.style.display = "block";
    } else {
      errorContainer3.style.display = "none";
    }
  });

document
  .getElementById("editModal")
  .querySelector("form")
  .addEventListener("submit", function (event) {
    const emailInput = document.getElementById("edit-email");
    const email = emailInput.value.trim();
    const originalEmailInput = document.getElementById("original-email");
    const originalEmail = originalEmailInput.value.trim();
    const errorContainer = document.querySelector(".error-container2");

    if (email !== originalEmail) {
      if (emailExistenceCheck.includes(email)) {
        event.preventDefault();
        console.log("email taken");
        errorContainer.style.display = "block";
      } else {
        errorContainer.style.display = "none";
      }
    } else {
      errorContainer.style.display = "none";
    }
  });

function togglePasswordVisibility(passwordId) {
  const passwordInput = document.getElementById(passwordId);
  const viewIcon = document.querySelector('img[src="../../images/view.png"]');
  const hideIcon = document.querySelector('img[src="../../images/hide.png"]');

  if (passwordInput.type === "password") {
    passwordInput.type = "text";
    viewIcon.style.display = "none";
    hideIcon.style.display = "inline-block";
  } else {
    passwordInput.type = "password";
    viewIcon.style.display = "inline-block";
    hideIcon.style.display = "none";
  }
}
