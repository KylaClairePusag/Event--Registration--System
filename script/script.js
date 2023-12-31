document.addEventListener("DOMContentLoaded", function () {
  const editButtons = document.querySelectorAll(".edit-button");
  const editDialog = document.getElementById("edit-dialog");
  const closeEditDialogButton = document.getElementById("close-edit-dialog");
  const editForm = editDialog.querySelector("form");

  editButtons.forEach((editButton) => {
    editButton.addEventListener("click", function (event) {
      const rsoId = this.getAttribute("data-rso-id");
      const rsoName = this.getAttribute("data-rso-name");
      const rsoPassword = this.getAttribute("data-rso-password");
      const rsoEmail = this.getAttribute("data-rso-email");
      const departmentId = this.getAttribute("data-department-id");

      editForm.elements["rso_id"].value = rsoId;
      editForm.elements["rso_name"].value = rsoName;
      editForm.elements["rso_password"].value = rsoPassword;
      editForm.elements["rso_email"].value = rsoEmail;

      editForm.elements["department_id"].value = departmentId;

      editDialog.style.display = "block";
    });
  });

  closeEditDialogButton.addEventListener("click", function () {
    editDialog.style.display = "none";
  });
});

const searchInput = document.querySelector('input[name="search_query"]');

element.addEventListener("event", () => {
  const query = searchInput.value;
  window.location.href = `?page=1&search_query=${encodeURIComponent(query)}`;
});

searchInput.addEventListener("input");

function toggleProfileDropdown() {
  var dropdown = document.getElementById("profileDropdown");
  dropdown.style.display =
    dropdown.style.display === "block" || dropdown.style.display === ""
      ? "none"
      : "block";
}

document.addEventListener("click", function (event) {
  var dropdown = document.getElementById("profileDropdown");
  var profileImg = document.querySelector(".profile-img");

  if (!dropdown.contains(event.target) && !profileImg.contains(event.target)) {
    dropdown.style.display = "none";
  }
});
