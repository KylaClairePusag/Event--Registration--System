<?php
// Database connection parameters
$host = "localhost"; // Your database host
$username = "root"; // Your database user
$password = ""; // Your database password
$dbname = "db_ba3101"; // Your database name

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
    die();
}

if (isset($_POST["add_faculty"])) {
    $faculty_name = htmlspecialchars($_POST["faculty_name"], ENT_QUOTES, "UTF-8");
    $faculty_password = htmlspecialchars($_POST["faculty_password"], ENT_QUOTES, "UTF-8");
    $email = htmlspecialchars($_POST["email"], ENT_QUOTES, "UTF-8");
    $department_id = $_POST["department_id"]; // Added department_id
    $query = $pdo->prepare("INSERT INTO tb_faculty (faculty_name, faculty_password, email, department_id) VALUES (:faculty_name, :faculty_password, :email, :department_id)");
    if ($query->execute([':faculty_name' => $faculty_name, ':faculty_password' => $faculty_password, ':email' => $email, ':department_id' => $department_id])) {
        header("Location: " . $_SERVER["PHP_SELF"]);
        exit();
    } else {
        echo "Error adding faculty.";
    }
}

if (isset($_POST["delete_faculty"])) {
    $faculty_id = filter_input(INPUT_POST, "delete_faculty", FILTER_VALIDATE_INT);
    $query = $pdo->prepare("DELETE FROM tb_faculty WHERE faculty_id = :faculty_id");
    if ($query->execute([':faculty_id' => $faculty_id])) {
        header("Location: " . $_SERVER["PHP_SELF"]);
        exit();
    } else {
        echo "Error deleting faculty.";
    }
}

if (isset($_POST["edit_faculty"])) {
    $edit_faculty_id = filter_input(INPUT_POST, "edit_faculty_id", FILTER_VALIDATE_INT);
    $edit_faculty_name = htmlspecialchars($_POST["edit_faculty_name"], ENT_QUOTES, "UTF-8");
    $edit_faculty_password = htmlspecialchars($_POST["edit_faculty_password"], ENT_QUOTES, "UTF-8");
    $edit_email = htmlspecialchars($_POST["edit_email"], ENT_QUOTES, "UTF-8");
    $edit_department_id = $_POST["edit_department_id"];
    $query = $pdo->prepare("UPDATE tb_faculty SET faculty_name = :faculty_name, faculty_password = :faculty_password, email = :email, department_id = :department_id WHERE faculty_id = :faculty_id");
    if ($query->execute([':faculty_name' => $edit_faculty_name, ':faculty_password' => $edit_faculty_password, ':email' => $edit_email, ':department_id' => $edit_department_id, ':faculty_id' => $edit_faculty_id])) {
        header("Location: " . $_SERVER["PHP_SELF"]);
        exit();
    } else {
        echo "Error editing faculty.";
    }
}

$query = $pdo->prepare("SELECT * FROM tb_faculty");
$query->execute();
?>
<!DOCTYPE html>
<html>

<head>
    <title>faculty List</title>
</head>

<body>
    <div>
        <h1>faculty List</h1>
        <button type="button" onclick="document.getElementById('addModal').showModal()">Add faculty</button>
        <?php
        include 'table.component.php';

        $head = array('ID', 'Name', 'Password', 'Email', 'Department', 'Actions'); // Added 'Department'
        $body = array();

        $query = $pdo->prepare("SELECT * FROM tb_faculty");
        $query->execute();

        while ($row = $query->fetch()) {
            $faculty_id = $row["faculty_id"];
            $faculty_name = $row["faculty_name"];
            $faculty_password = $row["faculty_password"];
            $email = $row["email"];
            $department_id = $row["department_id"]; // Added department_id
            $department_name = ''; // Initialize department_name
        
            // Fetch department name based on department_id
            $departmentQuery = $pdo->prepare("SELECT department_name FROM tb_department WHERE department_id = :department_id");
            $departmentQuery->execute([':department_id' => $department_id]);
            if ($deptRow = $departmentQuery->fetch()) {
                $department_name = $deptRow['department_name'];
            }

            $actions = '<button type="button" onclick="viewfaculty(' . $faculty_id . ', \'' . $faculty_name . '\', \'' . $faculty_password . '\', \'' . $email . '\', \'' . $department_id . '\')">View</button> <button type="button" onclick="showDeleteModal(' . $faculty_id . ')">Delete</button>';
            $body[] = array($faculty_id, $faculty_name, $faculty_password, $email, $department_name, $actions);
        }

        createTable($head, $body);
        ?>
        <dialog id="addModal" class="modal">
            <div class="modal-content">
                <button class="close" onclick="document.getElementById('addModal').close()">&times;</button>
                <h2>Add faculty</h2>
                <form method="POST" action="<?php echo $_SERVER['PHP_SELF']; ?>">
                    <label for="faculty-name">Name:</label>
                    <input type="text" id="faculty-name" name="faculty_name">
                    <label for="faculty-password">Password:</label>
                    <input type="password" id="faculty-password" name="faculty_password">
                    <label for="email">Email:</label>
                    <input type="email" id="email" name="email">
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
                    <button type="submit" name="add_faculty">Add faculty</button>
                </form>
            </div>
        </dialog>
        <dialog id="viewModal" class="modal">
            <div class="modal-content">
                <button class="close" onclick="document.getElementById('viewModal').close()">&times;</button>
                <h2>View faculty</h2>
                <form method="POST" action="<?php echo $_SERVER['PHP_SELF']; ?>">
                    <input type="hidden" id="edit-faculty-id" name="edit_faculty_id">
                    <label for="edit-faculty-name">Name:</label>
                    <input type="text" id="edit-faculty-name" name="edit_faculty_name">
                    <label for="edit-faculty-password">Password:</label>
                    <input type="password" id="edit-faculty-password" name="edit_faculty_password">
                    <label for="edit-email">Email:</label>
                    <input type="email" id="edit-email" name="edit_email">
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
                    <button type="submit" name="edit_faculty">Save</button>
                </form>
            </div>
        </dialog>
        <dialog id="deleteModal" class="modal">
            <div class="modal-content">
                <button class="close" onclick="document.getElementById('deleteModal').close()">&times;</button>
                <h2>Delete faculty</h2>
                <p>Are you sure you want to delete this faculty?</p>
                <div class="clearfix">
                    <button type="button" class="cancelbtn"
                        onclick="document.getElementById('deleteModal').close()">Cancel</button>
                    <button type="button" class="deletebtn" onclick="deletefacultyConfirmed()">Delete</button>
                </div>
            </div>
        </dialog>
    </div>
    <script>
    function showDeleteModal(faculty_id) {
        const deleteModal = document.getElementById("deleteModal");
        deleteModal.showModal();
        const deleteBtn = deleteModal.querySelector(".deletebtn");
        deleteBtn.addEventListener("click", function() {
            const form = document.createElement("form");
            form.setAttribute("method", "POST");
            form.setAttribute("action", "<?php echo $_SERVER['PHP_SELF']; ?>");

            const hiddenField = document.createElement("input");
            hiddenField.setAttribute("type", "hidden");
            hiddenField.setAttribute("name", "delete_faculty");
            hiddenField.setAttribute("value", faculty_id);

            form.appendChild(hiddenField);

            document.body.appendChild(form);
            form.submit();
        });
    }

    function deletefacultyConfirmed() {
        const deleteModal = document.getElementById("deleteModal");
        deleteModal.close();
    }

    function viewfaculty(faculty_id, faculty_name, faculty_password, email, department_id) {
        document.getElementById("edit-faculty-id").value = faculty_id;
        document.getElementById("edit-faculty-name").value = faculty_name;
        document.getElementById("edit-faculty-password").value = faculty_password;
        document.getElementById("edit-email").value = email;
        document.getElementById("edit-department").value = department_id;
        document.getElementById("viewModal").showModal();
    }

    const cells = document.querySelectorAll("td");
    cells.forEach(cell => cell.removeAttribute("style"));
    </script>
</body>

</html>