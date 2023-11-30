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
  form.reset();
  errorContainer.style.display = "none";
  errorContainer2.style.display = "none";
  setTimeout(function () {
    addModal.close();
    editModal.close();
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

function editRso(rso_id, rso_name, rso_password, email, department_id) {
  showModal("editModal");
  document.getElementById("edit-rso-id").value = rso_id;
  document.getElementById("edit-rso-name").value = rso_name;
  document.getElementById("edit-rso-password").value = rso_password;
  document.getElementById("edit-email").value = email;
  document.getElementById("original-email").value = email;
  document.getElementById("edit-department").value = department_id;
}

addOverlayClickListener("deleteModal");
addOverlayClickListener("editModal");
addOverlayClickListener("addModal");

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

function showDeleteModal(rso_id) {
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
    hiddenField.setAttribute("name", "delete_rso");
    hiddenField.setAttribute("value", rso_id);
    form.appendChild(hiddenField);
    document.body.appendChild(form);
    form.submit();
  });
}

function deleteRso() {
  const rso_id = document.getElementById("edit-rso-id").value;
  const form = document.createElement("form");
  form.setAttribute("method", "POST");
  form.setAttribute("action", base_url);

  const hiddenField = document.createElement("input");
  hiddenField.setAttribute("type", "hidden");
  hiddenField.setAttribute("name", "delete_rso");
  hiddenField.setAttribute("value", rso_id);

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
    const emailInput = document.getElementById("rso-email");
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
