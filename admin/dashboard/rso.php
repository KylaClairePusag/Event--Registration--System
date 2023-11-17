<?php
// Database connection setup
$host = "localhost";
$username = "root";
$password = "";
$dbname = "db_ba3101";

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
    die();
}

// Get the current request URI
$requestUri = $_SERVER['REQUEST_URI'];

// Add new RSO
if (isset($_POST["add_rso"])) {
    // Sanitize input
    $rso_name = htmlspecialchars($_POST["rso_name"], ENT_QUOTES, "UTF-8");
    $rso_password = htmlspecialchars($_POST["rso_password"], ENT_QUOTES, "UTF-8");
    $rso_email = htmlspecialchars($_POST["email"], ENT_QUOTES, "UTF-8");
    $department_id = $_POST["department_id"];

    // Insert data into the database
    $query = $pdo->prepare("INSERT INTO tb_rso (rso_name, rso_password, rso_email, department_id) VALUES (:rso_name, :rso_password, :rso_email, :department_id)");
    if ($query->execute([':rso_name' => $rso_name, ':rso_password' => $rso_password, ':rso_email' => $rso_email, ':department_id' => $department_id])) {
        header("Location: " . $requestUri);
        exit();
    } else {
        echo "Error adding rso.";
    }
}

// Delete RSO
if (isset($_POST["delete_rso"])) {
    $rso_id = filter_input(INPUT_POST, "delete_rso", FILTER_VALIDATE_INT);

    // Delete data from the database
    $query = $pdo->prepare("DELETE FROM tb_rso WHERE rso_id = :rso_id");
    if ($query->execute([':rso_id' => $rso_id])) {
        header("Location: " . $requestUri);
    } else {
        echo "Error deleting rso.";
    }
}

// Edit RSO
if (isset($_POST["edit_rso"])) {
    $edit_rso_id = filter_input(INPUT_POST, "edit_rso_id", FILTER_VALIDATE_INT);
    $edit_rso_name = htmlspecialchars($_POST["edit_rso_name"], ENT_QUOTES, "UTF-8");
    $edit_rso_password = htmlspecialchars($_POST["edit_rso_password"], ENT_QUOTES, "UTF-8");
    $edit_rso_email = htmlspecialchars($_POST["edit_rso_email"], ENT_QUOTES, "UTF-8");
    $edit_department_id = $_POST["edit_department_id"];

    // Update data in the database
    $query = $pdo->prepare("UPDATE tb_rso SET rso_name = :rso_name, rso_password = :rso_password, rso_email = :rso_email, department_id = :department_id WHERE rso_id = :rso_id");
    if ($query->execute([':rso_name' => $edit_rso_name, ':rso_password' => $edit_rso_password, ':rso_email' => $edit_rso_email, ':department_id' => $edit_department_id, ':rso_id' => $edit_rso_id])) {
        header("Location: " . $requestUri);
        exit();
    } else {
        echo "Error editing rso.";
    }
}

// Pagination setup
$limit = isset($_GET['limit']) ? $_GET['limit'] : 10;
$page = isset($_GET['page']) ? $_GET['page'] : 1;
$offset = ($page - 1) * $limit;

// Search functionality
$searchTerm = isset($_GET['search']) ? $_GET['search'] : '';

try {
    // Query to get a subset of records based on search and pagination
    $query = $pdo->prepare("SELECT * FROM tb_rso WHERE rso_name LIKE :searchTerm OR rso_email LIKE :searchTerm LIMIT :limit OFFSET :offset");
    $query->bindValue(':searchTerm', '%' . $searchTerm . '%', PDO::PARAM_STR);
    $query->bindValue(':limit', $limit, PDO::PARAM_INT);
    $query->bindValue(':offset', $offset, PDO::PARAM_INT);

    if (!$query->execute()) {
        throw new Exception("Query failed: " . implode(" ", $query->errorInfo()));
    }

    // Fetch results
    $rows = $query->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $ex) {
    echo "Error: " . $ex->getMessage();
    die();
}
?>
<!DOCTYPE html>
<html>

<head>
    <title>rso List</title>

    <link rel="stylesheet" href="../../styles/rso.css">

</head>

<body>

    <?php include '../../components/adminHeader.php'; ?>
    <main>

        <section class="head">

            <?php
            include '../../functions/search.php';
            ?>
            <div class="headbtn">
                <?php
                include '../../functions/limit.php';
                ?>
                <button type="button" onclick="document.getElementById('addModal').showModal()">Add rso <img
                        src='../../images/plus.png' alt='Image' /> </button>

            </div>
        </section>
        <section class="tableContainer">

            <?php if (!empty($searchTerm)): ?>
                <button type="button" onclick="clearSearch()">Clear Search</button>
            <?php endif; ?>




            <?php
            include '../../table.component.php';

            $head = array('ID', 'Name', 'Email', 'Department', 'Actions'); // Remove 'Password' from the $head array
            $body = array();

            foreach ($rows as $row) {
                $rso_id = $row["rso_id"];
                $rso_name = $row["rso_name"];
                $rso_email = $row["rso_email"];
                $department_id = $row["department_id"];
                $department_name = '';

                $departmentQuery = $pdo->prepare("SELECT department_name FROM tb_department WHERE department_id = :department_id");
                $departmentQuery->execute([':department_id' => $department_id]);
                if ($deptRow = $departmentQuery->fetch()) {
                    $department_name = $deptRow['department_name'];
                }

                $actions = '<button type="button" onclick="editRso(' . $rso_id . ', \'' . $rso_name . '\', \'' . $rso_email . '\', \'' . $department_id . '\')">Edit</button> <button type="button" onclick="showDeleteModal(' . $rso_id . ')">Delete</button>';
                $body[] = array($rso_id, $rso_name, $rso_email, $department_name, $actions); // Remove $rso_password from the $body array
            
            }

            createTable($head, $body);
            ?>

            <dialog id="addModal" class="modal">
                <div class="modal-content">
                    <button class="close" onclick="resetAddModal(true)">&times;</button>

                    <h2>Add rso</h2>
                    <form method="POST" action="">
                        <label for="rso-name">Name:</label>
                        <input type="text" id="rso-name" name="rso_name">
                        <label for="rso-password">Password:</label>
                        <input type="password" id="rso-password" name="rso_password">
                        <label for="rso-email">Email:</label>
                        <input type="email" id="rso-email" name="email">
                        <label for="department">Department:</label>
                        <select id="department" name="department_id">
                            <?php
                            $departmentQuery = $pdo->prepare("SELECT department_id, department_name FROM tb_department");
                            $departmentQuery->execute();

                            while ($deptRow = $departmentQuery->fetch()) {
                                echo "<option value='" . $deptRow['department_id'] . "'>" . $deptRow['department_name'] . "</option>";
                            }
                            ?>
                        </select>
                        <div class="error-container">Email Already Taken</div>
                        <button type="submit" name="add_rso">Add rso</button>
                    </form>
                </div>
            </dialog>
            <dialog id="editModal" class="modal">
                <div class="modal-content">
                    <button class="close" onclick="resetAddModal(true)">&times;</button>
                    <h2>Edit rso</h2>
                    <form method="POST" action="">
                        <input type="hidden" id="edit-rso-id" name="edit_rso_id">
                        <label for="edit-rso-name">Name:</label>
                        <input type="text" id="edit-rso-name" name="edit_rso_name">
                        <label for="edit-rso-password">Password:</label>
                        <input type="password" id="edit-rso-password" name="edit_rso_password">
                        <label for="edit-email">Email:</label>
                        <input type="email" id="edit-email" name="edit_rso_email">
                        <input type="hidden" id="original-email" value="<?php echo $originalEmail; ?>">

                        <label for="edit-department">Department:</label>
                        <select id="edit-department" name="edit_department_id">
                            <?php
                            $departmentQuery = $pdo->prepare("SELECT department_id, department_name FROM tb_department");
                            $departmentQuery->execute();

                            while ($deptRow = $departmentQuery->fetch()) {
                                $selected = ($deptRow['department_id'] == $row['department_id']) ? "selected" : "";
                                echo "<option value='" . $deptRow['department_id'] . "' $selected>" . $deptRow['department_name'] . "</option>";
                            }
                            ?>
                        </select>
                        <div class="error-container2">Email Already Taken</div>
                        <button type="submit" name="edit_rso">Save</button>
                    </form>
                </div>
            </dialog>

            <dialog id="deleteModal" class="modal">
                <div class="modal-content">
                    <button class="close" onclick="closeModal('deleteModal')">&times;</button>
                    <h2>Delete rso</h2>
                    <p>Are you sure you want to delete this rso?</p>
                    <div class="clearfix">
                        <button type="button" class="cancelbtn" onclick="closeModal('deleteModal')">Cancel</button>
                        <button type="button" class="deletebtn" onclick="deleteRso()">Delete</button>
                    </div>
                </div>
            </dialog>
        </section>
        <section class="paginationCont">

            <?php
            include '../../functions/pagination.php';
            generatePaginationLinks($pdo, $searchTerm, $limit);
            ?>
        </section>
    </main>

    <script>
        function closeModal(modalId) {
            const modal = document.getElementById(modalId);
            modal.close();
        }

        function resetAddModal() {
            const addModal = document.getElementById('addModal');
            const editModal = document.getElementById('editModal');
            const form = addModal.querySelector('form');
            const errorContainer = document.querySelector('.error-container');
            const errorContainer2 = document.querySelector('.error-container2');
            form.reset();
            errorContainer.style.display = 'none';
            errorContainer2.style.display = 'none';
            setTimeout(function () {
                addModal.close();
                editModal.close();
            },);
        }

        function addOverlayClickListener(modalId) {
            const modal = document.getElementById(modalId);
            modal.addEventListener('click', function (event) {
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

        // Function to show details in edit modal
        function editRso(rso_id, rso_name, rso_password, email, department_id) {
            showModal("editModal");
            document.getElementById("edit-rso-id").value = rso_id;
            document.getElementById("edit-rso-name").value = rso_name;
            document.getElementById("edit-rso-password").value = rso_password;
            document.getElementById("edit-email").value = email;
            document.getElementById("original-email").value = email;
            document.getElementById("edit-department").value = department_id;
        }

        // Add overlay click listeners for modals
        addOverlayClickListener("deleteModal");
        addOverlayClickListener("editModal");
        addOverlayClickListener("addModal");

        // Function to change the limit and reload the page
        function changeLimit(newLimit) {
            const currentUrl = new URL(window.location.href);
            const searchParam = currentUrl.searchParams.get('search');
            currentUrl.searchParams.set('limit', newLimit);
            if (searchParam) {
                currentUrl.searchParams.set('search', searchParam);
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
        const base_url = "<?php echo htmlspecialchars($requestUri, ENT_QUOTES, 'UTF-8'); ?>";
        // Function to show delete modal and handle delete button click
        function showDeleteModal(rso_id) {
            showModal("deleteModal");
            const deleteBtn = document.getElementById("deleteModal").querySelector(".deletebtn");
            deleteBtn.addEventListener("click", function () {
                const form = document.createElement("form");
                form.setAttribute("method", "POST");
                form.setAttribute("action", "<?php echo $_SERVER['REQUEST_URI']; ?>");
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
        // Function to check if the email is already taken
        function isEmailTaken(email) {
            const emailExistenceCheck = <?php echo json_encode(array_column($rows, 'rso_email')); ?>;
            return emailExistenceCheck.includes(email);
        }

        // Event listener for form submission
        document.getElementById('addModal').querySelector('form').addEventListener('submit', function (event) {
            const emailInput = document.getElementById('rso-email');
            const email = emailInput.value.trim();
            const errorContainer = document.querySelector('.error-container');

            if (isEmailTaken(email)) {
                event.preventDefault();
                errorContainer.style.display = 'block';
            } else {
                errorContainer.style.display = 'none';
            }
        });

        document.getElementById('editModal').querySelector('form').addEventListener('submit', function (event) {
            const emailInput = document.getElementById('edit-email');
            const email = emailInput.value.trim();
            const originalEmailInput = document.getElementById('original-email');
            const originalEmail = originalEmailInput.value.trim();
            const errorContainer = document.querySelector('.error-container2');

            if (email !== originalEmail) {
                const emailExistenceCheck = <?php echo json_encode(array_column($rows, 'rso_email')); ?>;
                if (emailExistenceCheck.includes(email)) {
                    event.preventDefault();
                    console.log('email taken');
                    errorContainer.style.display = 'block';
                } else {
                    errorContainer.style.display = 'none';
                }
            } else {
                errorContainer.style.display = 'none';
            }
        });

    </script>

</body>

</html>