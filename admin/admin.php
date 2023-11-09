<?php
session_start();
include '../config/config.php'; // Ensure this file contains the correct database connection setup.

// Consistent use of the object-oriented style for database connection
$sql = 'SELECT * FROM tb_admin';
$result = $conn->query($sql);
$admin = $result->fetch_all(MYSQLI_ASSOC);

// Initialize variables and error messages
$admin_name = $admin_password = $department = $admin_email = '';
$admin_nameErr = $admin_passwordErr = $deptErr = $admin_emailErr = '';
;

$departments = [];
$dept_query = "SELECT department_id, department_name FROM tb_department";
$dept_result = $conn->query($dept_query);
if ($dept_result->num_rows > 0) {
    while ($row = $dept_result->fetch_assoc()) {
        $departments[] = $row;
    }
}

// Check if the form is submitted and the request method is POST
if (isset($_POST['action']) && $_POST['action'] === 'create') {
    // Validate admin Name
    if (empty(trim($_POST['admin_name']))) {
        $admin_nameErr = 'Name is required';
    } else {
        $admin_name = $conn->real_escape_string(trim($_POST['admin_name']));
    }

    // Validate admin Password
    if (empty(trim($_POST['admin_password']))) {
        $admin_passwordErr = 'Password is required';
    } else {
        $admin_password = $conn->real_escape_string(trim($_POST['admin_password']));
    }

    // Validate Department
    if (empty($_POST['department_id'])) {
        $deptErr = 'Department is required';
    } else {
        $department = $conn->real_escape_string($_POST['department_id']);
    }

    if (empty(trim($_POST['admin_email']))) {
        $admin_emailErr = 'Email is required';
    } else {
        $admin_email = $conn->real_escape_string(trim($_POST['admin_email']));
    }


    // Proceed with insertion if there are no errors
    if (empty($admin_nameErr) && empty($admin_passwordErr) && empty($deptErr)) {
        $stmt = $conn->prepare("INSERT INTO tb_admin (admin_email, admin_name, admin_password, department_id) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("sssi", $admin_email, $admin_name, $admin_password, $department);

        if ($stmt->execute()) {
            $_SESSION['success_message'] = "admin created successfully.";
            header('Location: ' . $_SERVER['PHP_SELF']);
            exit;
        } else {
            echo 'ERROR: ', $conn->error;
        }
        $stmt->close();
    }
}

// Check for a success message in the session and clear it after displaying
if (isset($_SESSION['success_message'])) {
    echo $_SESSION['success_message'];
    unset($_SESSION['success_message']);
}

// Delete operation
if (isset($_POST['action']) && $_POST['action'] == 'delete') {
    $admin_id = $conn->real_escape_string($_POST['admin_id']);

    $sql = "DELETE FROM tb_admin WHERE admin_id='$admin_id'";

    if ($conn->query($sql) === TRUE) {
        header('Location: ' . $_SERVER['PHP_SELF']);
        exit;
    } else {
        echo "Error deleting record: " . $conn->error;
    }
}

// Update operation
if (isset($_POST['action']) && $_POST['action'] == 'edit') {
    $admin_id = $conn->real_escape_string($_POST['admin_id']);
    $admin_name = $conn->real_escape_string($_POST['admin_name']);
    $admin_password = $conn->real_escape_string($_POST['admin_password']);
    $department_id = $conn->real_escape_string($_POST['department_id']);
    $admin_email = $conn->real_escape_string($_POST['admin_email']);

    $sql = "UPDATE tb_admin SET admin_name='$admin_name', admin_email='$admin_email', admin_password='$admin_password', department_id='$department_id' WHERE admin_id='$admin_id'";


    if ($conn->query($sql) === TRUE) {
        header('Location: ' . $_SERVER['PHP_SELF']);
        exit;
    } else {
        echo "Error updating record: " . $conn->error;
    }
}

// Pagination and Search Query Logic
$searchQuery = '';
$page = 1;

if (isset($_GET['search_query'])) {
    $searchQuery = $conn->real_escape_string($_GET['search_query']);
    if (!isset($_SESSION['last_search_query']) || $searchQuery != $_SESSION['last_search_query']) {
        $_SESSION['last_search_query'] = $searchQuery;
    } else {
        $page = isset($_GET['page']) ? (int) $_GET['page'] : 1;
    }
} else {
    $_SESSION['last_search_query'] = '';
    $page = isset($_GET['page']) ? (int) $_GET['page'] : 1;
}

if (!empty($searchQuery)) {
    $stmt = $conn->prepare("SELECT * FROM tb_admin WHERE admin_name LIKE ?");
    $likeQuery = '%' . $searchQuery . '%';
    $stmt->bind_param('s', $likeQuery);
    $stmt->execute();
    $result = $stmt->get_result();
    $admin = $result->fetch_all(MYSQLI_ASSOC);
    $stmt->close();
}

$perPage = 10;
$startAt = ($page - 1) * $perPage;

$stmt = $conn->prepare("SELECT * FROM tb_admin WHERE admin_name LIKE ? LIMIT ?, ?");
$likeQuery = '%' . $searchQuery . '%';
$stmt->bind_param('sii', $likeQuery, $startAt, $perPage);
$stmt->execute();
$result = $stmt->get_result();
$admin = $result->fetch_all(MYSQLI_ASSOC);
$stmt->close();

$totalResults = $conn->query("SELECT COUNT(*) as count FROM tb_admin WHERE admin_name LIKE '%$searchQuery%'")->fetch_assoc()['count'];
$totalPages = ceil($totalResults / $perPage);

$rows = [];
foreach ($admin as $item) {
    // Find the department name for the current admin item
    $departmentName = '';
    foreach ($departments as $dept) {
        if ($dept['department_id'] == $item['department_id']) {
            $departmentName = $dept['department_name'];
            break; // Exit the loop once the department is found
        }
    }

    // Append a new row to the $rows array
    $rows[] = [
        'admin_id' => $item['admin_id'],
        'email' => $item['email'],
        'admin_name' => $item['admin_name'],
        'admin_password' => $item['admin_password'],
        'department_id' => $item['department_id'],
        'department_name' => $departmentName

    ];
}

// Headers for the table
$headers = ['admin ID', 'admin EMAIL', 'admin NAME', 'PASSWORD', 'DEPARTMENT'];

?>

<!DOCTYPE html>
<html>

<head>

    <link rel="stylesheet" href="../styles/admin.css">
</head>

<body>
    <?php include('header.php'); ?>

    <main>
        <form method="post" action="">
            admin Email: <input type="email" name="admin_email" required>
            <span class="error">
                <?php echo $admin_emailErr; ?>
            </span><br>

            <input type="hidden" name="action" value="create">
            admin Name: <input type="text" name="admin_name" required>
            <span class="error">
                <?php echo $admin_nameErr; ?>
            </span><br>
            Password: <input type="password" name="admin_password" required>
            <span class="error">
                <?php echo $admin_passwordErr; ?>
            </span><br>
            Department: <select name="department_id" required>
                <?php foreach ($departments as $dept) { ?>
                    <option value="<?php echo $dept['department_id']; ?>">
                        <?php echo htmlspecialchars($dept['department_name']); ?>
                    </option>
                <?php } ?>


            </select>
            <span class="error">
                <?php echo $deptErr; ?>
            </span><br>
            <input type="submit" value="Create admin">
        </form>
        <br>
        <br>
        <!-- Search form -->
        <?php
        include '../functions/search.php';
        ?>
        <!--display admin -->
        <?php
        include '../functions/button-generator.php';
        include '../functions/table-generator.php';

        // Use the generateTable function
        generateTable($headers, $rows);
        ?>

        <!-- Pagination link generation with search query included -->
        <?php
        include '../functions/pagination.php';
        ?>

        <div id="edit-dialog">
            <div class="edit-dialog-content">
                <span id="close-edit-dialog" class="close-button">&times;</span>
                <h2>Edit admin</h2>
                <form method="post" action="">
                    <input type="hidden" name="action" value="edit">
                    <input type="hidden" name="admin_id" id="edit-admin-id" value="">
                    admin Email: <input type="email" name="admin_email" id="edit-admin-email" required>
                    admin Name: <input type="text" name="admin_name" required>
                    Password: <input type="password" name="admin_password" required>
                    Department:
                    <select name="department_id" required>

                        <?php foreach ($departments as $department) { ?>
                            <option value="<?php echo $department['department_id']; ?>" <?php if
                               ($department['department_id'] == $editDepartmentId)
                                   echo 'selected'; ?>>
                                <?php echo htmlspecialchars($department['department_name']); ?>
                            </option>
                        <?php } ?>
                    </select>
                    <input type="submit" value="Update admin">
                </form>
            </div>
        </div>
    </main>
</body>
<script src="../script/script.js"></script>

</html>