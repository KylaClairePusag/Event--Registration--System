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
