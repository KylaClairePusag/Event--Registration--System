

function addOverlayClickListener(modalId) {
    const modal = document.getElementById(modalId);
    modal.addEventListener('click', function (event) {
        if (event.target === modal) {
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



// Function to show details in view modal
function viewRso(rso_id, rso_name, rso_password, email, department_id) {
    showModal("viewModal");
    document.getElementById("edit-rso-id").value = rso_id;
    document.getElementById("edit-rso-name").value = rso_name;
    document.getElementById("edit-rso-password").value = rso_password;
    document.getElementById("edit-email").value = email;
    document.getElementById("edit-department").value = department_id;
}

// Add overlay click listeners for modals
addOverlayClickListener("deleteModal");
addOverlayClickListener("viewModal");
addOverlayClickListener("addModal");

// Function to change the limit and reload the page
function changeLimit(newLimit) {
    const currentUrl = new URL(window.location.href);
    const searchParam = currentUrl.searchParams.get('search');
    currentUrl.searchParams.set('limit', newLimit);
    if (searchParam) {
        currentUrl.searchParams.set('search', searchParam);
    } else {
        currentUrl.searchParams.delete('search');
    }
    history.pushState({}, '', currentUrl);
    window.location.reload();
}

// Function to change the page and reload
function changePage(newPage) {
    const currentUrl = new URL(window.location.href);
    const limitParam = currentUrl.searchParams.get('limit');
    window.location.href = "<?php echo $_SERVER['PHP_SELF']; ?>?page=" + newPage + "&limit=" + limitParam;
}

// Function to clear search and reload
function clearSearch() {
    const currentUrl = new URL(window.location.href);
    currentUrl.searchParams.delete('search');
    currentUrl.searchParams.delete('page');
    history.pushState({}, '', currentUrl);
    window.location.reload();
}

// Event listener for search form submission
document.getElementById('searchForm').addEventListener('submit', function (event) {
    event.preventDefault();
    const searchInput = document.getElementById('search');
    const searchTerm = searchInput.value;
    const currentUrl = new URL(window.location.href);
    if (searchTerm.trim() !== '') {
        currentUrl.searchParams.set('search', searchTerm);
    } else {
        currentUrl.searchParams.delete('search');
    }
    history.pushState({}, '', currentUrl);
    window.location.reload();
});