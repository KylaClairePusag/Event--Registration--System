<?php
session_start();
include '../config/config.php'; // Ensure this file contains the correct database connection setup.

// Consistent use of the object-oriented style for database connection
$sql = 'SELECT * FROM tb_rso';
$result = $conn->query($sql);
$rso = $result->fetch_all(MYSQLI_ASSOC);

// Initialize variables and error messages
$rso_name = $rso_password = $department = $rso_email = '';
$rso_nameErr = $rso_passwordErr = $deptErr = $rso_emailErr = '';
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
    // Validate RSO Name
    if (empty(trim($_POST['rso_name']))) {
        $rso_nameErr = 'Name is required';
    } else {
        $rso_name = $conn->real_escape_string(trim($_POST['rso_name']));
    }

    // Validate RSO Password
    if (empty(trim($_POST['rso_password']))) {
        $rso_passwordErr = 'Password is required';
    } else {
        $rso_password = $conn->real_escape_string(trim($_POST['rso_password']));
    }

    // Validate Department
    if (empty($_POST['department_id'])) {
        $deptErr = 'Department is required';
    } else {
        $department = $conn->real_escape_string($_POST['department_id']);
    }

    if (empty(trim($_POST['rso_email']))) {
        $rso_emailErr = 'Email is required';
    } else {
        $rso_email = $conn->real_escape_string(trim($_POST['rso_email']));
    }


    // Proceed with insertion if there are no errors
    if (empty($rso_nameErr) && empty($rso_passwordErr) && empty($deptErr)) {
        $stmt = $conn->prepare("INSERT INTO tb_rso (rso_email, rso_name, rso_password, department_id) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("sssi", $rso_email, $rso_name, $rso_password, $department);

        if ($stmt->execute()) {
            $_SESSION['success_message'] = "RSO created successfully.";
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
    $rso_id = $conn->real_escape_string($_POST['rso_id']);

    $sql = "DELETE FROM tb_rso WHERE rso_id='$rso_id'";

    if ($conn->query($sql) === TRUE) {
        header('Location: ' . $_SERVER['PHP_SELF']);
        exit;
    } else {
        echo "Error deleting record: " . $conn->error;
    }
}

// Update operation
if (isset($_POST['action']) && $_POST['action'] == 'edit') {
    $rso_id = $conn->real_escape_string($_POST['rso_id']);
    $rso_name = $conn->real_escape_string($_POST['rso_name']);
    $rso_password = $conn->real_escape_string($_POST['rso_password']);
    $department_id = $conn->real_escape_string($_POST['department_id']);
    $rso_email = $conn->real_escape_string($_POST['rso_email']);

    $sql = "UPDATE tb_rso SET rso_name='$rso_name', rso_email='$rso_email', rso_password='$rso_password', department_id='$department_id' WHERE rso_id='$rso_id'";


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
    $stmt = $conn->prepare("SELECT * FROM tb_rso WHERE rso_name LIKE ?");
    $likeQuery = '%' . $searchQuery . '%';
    $stmt->bind_param('s', $likeQuery);
    $stmt->execute();
    $result = $stmt->get_result();
    $rso = $result->fetch_all(MYSQLI_ASSOC);
    $stmt->close();
}

$perPage = 10;
$startAt = ($page - 1) * $perPage;

$stmt = $conn->prepare("SELECT * FROM tb_rso WHERE rso_name LIKE ? LIMIT ?, ?");
$likeQuery = '%' . $searchQuery . '%';
$stmt->bind_param('sii', $likeQuery, $startAt, $perPage);
$stmt->execute();
$result = $stmt->get_result();
$rso = $result->fetch_all(MYSQLI_ASSOC);
$stmt->close();

$totalResults = $conn->query("SELECT COUNT(*) as count FROM tb_rso WHERE rso_name LIKE '%$searchQuery%'")->fetch_assoc()['count'];
$totalPages = ceil($totalResults / $perPage);

$rows = [];
foreach ($rso as $item) {
    // Find the department name for the current RSO item
    $departmentName = '';
    foreach ($departments as $dept) {
        if ($dept['department_id'] == $item['department_id']) {
            $departmentName = $dept['department_name'];
            break; // Exit the loop once the department is found
        }
    }

    // Append a new row to the $rows array
    $rows[] = [
        'rso_id' => $item['rso_id'],
        'email' => $item['email'],
        'rso_name' => $item['rso_name'],
        'rso_password' => $item['rso_password'],
        'department_id' => $item['department_id'],
        'department_name' => $departmentName

    ];
}

// Headers for the table
$headers = ['RSO ID', 'RSO EMAIL', 'RSO NAME', 'PASSWORD', 'DEPARTMENT'];

?>

<!DOCTYPE html>
<html>

<head>

    <link rel="stylesheet" href="../styles/rso.css">
</head>

<body>
    <?php include('header.php'); ?>

    <main>
        <form method="post" action="">
            RSO Email: <input type="email" name="rso_email" required>
            <span class="error">
                <?php echo $rso_emailErr; ?>
            </span><br>

            <input type="hidden" name="action" value="create">
            RSO Name: <input type="text" name="rso_name" required>
            <span class="error">
                <?php echo $rso_nameErr; ?>
            </span><br>
            Password: <input type="password" name="rso_password" required>
            <span class="error">
                <?php echo $rso_passwordErr; ?>
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
            <input type="submit" value="Create RSO">
        </form>
        <br>
        <br>
        <!-- Search form -->
        <?php
        include '../functions/search.php';
        ?>
        <!--display rso -->
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
                <h2>Edit RSO</h2>
                <form method="post" action="">
                    <input type="hidden" name="action" value="edit">
                    <input type="hidden" name="rso_id" id="edit-rso-id" value="">
                    RSO Email: <input type="email" name="rso_email" id="edit-rso-email" required>
                    RSO Name: <input type="text" name="rso_name" required>
                    Password: <input type="password" name="rso_password" required>
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
                    <input type="submit" value="Update RSO">
                </form>
            </div>
        </div>
    </main>
</body>
<script src="../script/script.js"></script>

</html>