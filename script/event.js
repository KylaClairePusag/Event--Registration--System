// event.js

// Function to close a modal by ID
function closeModal(modalId) {
  const modal = document.getElementById(modalId);
  modal.close();
}

// Function to reset the add modal
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

// Function to add an overlay click listener to a modal
function addOverlayClickListener(modalId) {
  const modal = document.getElementById(modalId);
  modal.addEventListener("click", function (event) {
    if (event.target === modal) {
      resetAddModal();
      modal.close();
    }
  });
}

// Function to show a modal and add overlay click listener
function showModal(modalId) {
  const modal = document.getElementById(modalId);
  modal.showModal();
  addOverlayClickListener(modalId);
}

// Function to show details in the edit modal
function editevent(
  event_id,
  event_title,
  event_detail,
  event_date,
  department_id
) {
  console.log("Edit button clicked!");
  showModal("editModal");
  document.getElementById("edit-event-id").value = event_id;
  document.getElementById("edit-event-title").value = event_title;
  document.getElementById("edit-event-detail").value = event_detail;
  document.getElementById("edit-event-date").value = event_date;
  document.getElementById("edit-department-id").value = department_id;
}

// Add overlay click listeners for modals
addOverlayClickListener("deleteModal");
addOverlayClickListener("editModal");
addOverlayClickListener("addModal");

// Function to change the limit and reload the page
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

// Function to change the page and reload
function changePage(newPage) {
  const currentUrl = new URL(window.location.href);
  const limitParam = currentUrl.searchParams.get("limit");
  window.location.href = base_url + "?page=" + newPage + "&limit=" + limitParam;
}

// Function to clear search and reload
function clearSearch() {
  const currentUrl = new URL(window.location.href);
  currentUrl.searchParams.delete("search");
  currentUrl.searchParams.delete("page");
  history.pushState({}, "", currentUrl);
  window.location.reload();
}

// Event listener for search form submission
document
  .getElementById("searchForm")
  .addEventListener("submit", function (event) {
    event.preventDefault();
    const searchInput = document.getElementById("search");
    const searchTerm = searchInput.value;
    const currentUrl = new URL(window.location.href);

    // Set or delete the 'search' parameter
    if (searchTerm.trim() !== "") {
      currentUrl.searchParams.set("search", searchTerm);
    } else {
      currentUrl.searchParams.delete("search");
    }

    // Delete the 'page' parameter
    currentUrl.searchParams.delete("page");

    // Other parameters can be handled similarly if needed

    history.pushState({}, "", currentUrl);
    window.location.reload();
  });

// Function to show delete modal and handle delete button click
function showDeleteModal(event_id) {
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
    hiddenField.setAttribute("name", "delete_event");
    hiddenField.setAttribute("value", event_id);
    form.appendChild(hiddenField);
    document.body.appendChild(form);
    form.submit();
  });
}

// Function to delete an event
function deleteevent() {
  const event_id = document.getElementById("edit-event-id").value;
  const form = document.createElement("form");
  form.setAttribute("method", "POST");
  form.setAttribute("action", base_url);

  const hiddenField = document.createElement("input");
  hiddenField.setAttribute("type", "hidden");
  hiddenField.setAttribute("name", "delete_event");
  hiddenField.setAttribute("value", event_id);

  form.appendChild(hiddenField);
  document.body.appendChild(form);
  form.submit();
}
