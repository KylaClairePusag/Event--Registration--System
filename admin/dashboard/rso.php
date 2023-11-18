<?php
include '../../config/config.php';
// Get the current request URI
$requestUri = $_SERVER['REQUEST_URI'];

// Add new RSO
if (isset($_POST["add_rso"])) {
    $rso_name = htmlspecialchars($_POST["rso_name"], ENT_QUOTES, "UTF-8");
    $rso_password = htmlspecialchars($_POST["rso_password"], ENT_QUOTES, "UTF-8");
    $rso_email = htmlspecialchars($_POST["email"], ENT_QUOTES, "UTF-8");
    $department_id = $_POST["department_id"];

    // Insert data into the database
    $query = $pdo->prepare("INSERT INTO tb_rso (rso_name, rso_password, rso_email, department_id) VALUES (:rso_name, :rso_password, :rso_email, :department_id)");
    if ($query->execute([':rso_name' => $rso_name, ':rso_password' => $rso_password, ':rso_email' => $rso_email, ':department_id' => $department_id])) {
        header("Location: $requestUri");
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
        header("Location: $requestUri");
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
        header("Location: $requestUri");
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
    $paginationQuery = $pdo->prepare("SELECT COUNT(*) AS total FROM tb_rso WHERE rso_name LIKE :searchTerm OR rso_email LIKE :searchTerm");
    $paginationQuery->bindValue(':searchTerm', '%' . $searchTerm . '%', PDO::PARAM_STR);
} catch (Exception $ex) {
    echo "Error: " . $ex->getMessage();
    die();
}
?>
<!DOCTYPE html>
<html>

<head>
    <link rel="stylesheet" href="../../styles/rso.css">
</head>

<body>

    <?php include '../../components/adminHeader.php'; ?>

    <main>
        <section class="head">
            <div class="searchCont">
                <?php
                include '../../components/search.php';
                ?>
                <?php if (!empty($searchTerm)): ?>
                    <img src='../../images/cross.png' alt='Image' class="icon" onclick="clearSearch()" id='clearBtn' />
                <?php endif; ?>
            </div>
            <div class="headbtn">
                <?php
                include '../../components/limit.php';
                ?>
                <button type="button" onclick="document.getElementById('addModal').showModal()">Add rso <img
                        src='../../images/plus.png' alt='Image' class="icon" /> </button>
            </div>
        </section>

        <section class="tableContainer">
            <?php
            include '../../components/table.component.php';

            $head = array('ID', 'Name', 'Password', 'Email', 'Department', 'Actions');
            $body = array();

            foreach ($rows as $row) {
                $rso_id = $row["rso_id"];
                $rso_name = $row["rso_name"];
                $rso_password = $row["rso_password"];
                $rso_email = $row["rso_email"];
                $department_id = $row["department_id"];
                $department_name = '';

                $departmentQuery = $pdo->prepare("SELECT department_name FROM tb_department WHERE department_id = :department_id");
                $departmentQuery->execute([':department_id' => $department_id]);
                if ($deptRow = $departmentQuery->fetch()) {
                    $department_name = $deptRow['department_name'];
                }

                $actions = '<button type="button" onclick="editRso(' . $rso_id . ', \'' . $rso_name . '\', \'' . $rso_password . '\', \'' . $rso_email . '\', \'' . $department_id . '\')">Edit</button> <button type="button" onclick="showDeleteModal(' . $rso_id . ')">Delete</button>';
                $body[] = array($rso_id, $rso_name, $rso_password, $rso_email, $department_name, $actions);

            }
            createTable($head, $body);
            ?>

            <dialog id="addModal" class="modal">

                <div class="modal-content">
                    <button class="close" onclick="resetAddModal(true)">&times;</button>

                    <h2>CREATE RSO ACCOUNT</h2>
                    <form method="POST" action="">
                        <div class="error-container">Email Already Taken</div>

                        <label for="rso-name">Name:</label>
                        <input type="text" id="rso-name" name="rso_name" required>
                        <label for="rso-password">Password:</label>
                        <input type="password" id="rso-password" name="rso_password" required>
                        <label for="rso-email">Email:</label>
                        <input type="email" id="rso-email" name="email" required>
                        <label for="department">Department:</label>
                        <select id="department" name="department_id" required>
                            <option value="">Select a department</option>
                            <?php
                            $departmentQuery = $pdo->prepare("SELECT department_id, department_name FROM tb_department");
                            $departmentQuery->execute();

                            while ($deptRow = $departmentQuery->fetch()) {
                                echo "<option value='" . $deptRow['department_id'] . "'>" . $deptRow['department_name'] . "</option>";
                            }
                            ?>
                        </select>
                        <button type="submit" name="add_rso">Create Rso Account</button>
                    </form>
                </div>
            </dialog>
            <dialog id="editModal" class="modal">

                <div class="modal-content">
                    <button class="close" onclick="resetAddModal(true)">&times;</button>

                    <h2>EDIT RSO ACCOUNT</h2>
                    <div class="error-container2">Email Already Taken</div>

                    <form method="POST" action="">
                        <input type="hidden" id="edit-rso-id" name="edit_rso_id">
                        <label for="edit-rso-name">Name:</label>
                        <input type="text" id="edit-rso-name" name="edit_rso_name">
                        <label for="edit-rso-password">Password:</label>

                        <div class='passcont'>
                            <input type="password" id="edit-rso-password" name="edit_rso_password">
                            <img src='../../images/view.png' alt='Show Password' class="icon"
                                onclick="togglePasswordVisibility('edit-rso-password')" />
                            <img src='../../images/hide.png' alt='Hide Password' class="icon hide"
                                onclick="togglePasswordVisibility('edit-rso-password')" />
                        </div>
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
                        <button type="submit" name="edit_rso">Save</button>
                    </form>
                </div>
            </dialog>

            <dialog id="deleteModal" class="modal">
                <div class="modal-content">
                    <button class="close" onclick="closeModal('deleteModal')">&times;</button>
                    <h2>DELETE RSO ACCOUNT</h2>
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
            include '../../components/pagination.php';
            generatePaginationLinks($pdo, $searchTerm, $limit, $paginationQuery);
            ?>
        </section>
    </main>
    <?php
    // Include your PHP code here to set $requestUri
    $requestUri = $_SERVER['REQUEST_URI'];
    ?>
    <script>
        const base_url = "<?php echo htmlspecialchars($requestUri, ENT_QUOTES, 'UTF-8'); ?>";
        const emailExistenceCheck = <?php echo json_encode(array_column($rows, 'rso_email')); ?>;
    </script>
    <script src="../../script/rso.js"></script>

</body>

</html>