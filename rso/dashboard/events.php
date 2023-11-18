<?php
// Database connection setup
include '../../config/config.php';
// Get the current request URI
$requestUri = $_SERVER['REQUEST_URI'];

 $conn = new mysqli('localhost','root', '', 'db_ba3101');
        if ($conn->connect_error) {
            die('Connection Failed: '. $conn->connect_error);}       

// Add new event
if (isset($_POST["add_event"])) {
    $event_title = htmlspecialchars($_POST["event_title"], ENT_QUOTES, "UTF-8");
    $event_detail = htmlspecialchars($_POST["event_detail"], ENT_QUOTES, "UTF-8");
    $event_date = htmlspecialchars($_POST["event_date"], ENT_QUOTES, "UTF-8");
    $department_id = htmlspecialchars($_POST["department_id"], ENT_QUOTES, "UTF-8");
    


    // Insert data into the database
    $query = $pdo->prepare("INSERT INTO tb_event (event_title, event_detail, event_date, department_id) VALUES (:event_title, :event_detail, :event_date, :department_id)");
    if ($query->execute([':event_title' => $event_title, ':event_detail' => $event_detail, ':event_date' => $event_date, ':department_id' => $department_id])) {
        header("Location: $requestUri");
        exit();
    } else {
        echo "Error adding event.";
    }
}

// Delete event
if (isset($_POST["delete_event"])) {
    $event_id = filter_input(INPUT_POST, "delete_event", FILTER_VALIDATE_INT);

    // Delete data from the database
    $query = $pdo->prepare("DELETE FROM tb_event WHERE event_id = :event_id");
    if ($query->execute([':event_id' => $event_id])) {
        header("Location: $requestUri");
    } else {
        echo "Error deleting event.";
    }
}

// Edit event
if (isset($_POST["edit_event"])) {
    $edit_event_id = filter_input(INPUT_POST, "edit_event_id", FILTER_VALIDATE_INT);
    $edit_event_title = htmlspecialchars($_POST["edit_event_title"], ENT_QUOTES, "UTF-8");
    $edit_event_detail = htmlspecialchars($_POST["edit_event_detail"], ENT_QUOTES, "UTF-8");
    $edit_event_date = htmlspecialchars($_POST["edit_event_date"], ENT_QUOTES, "UTF-8");
    $edit_department_id = htmlspecialchars($_POST["edit_department_id"], ENT_QUOTES, "UTF-8");


    // Update data in the database
    $query = $pdo->prepare("UPDATE tb_event SET event_title = :event_title, event_detail = :event_detail, event_date = :event_date, department_id = :department_id WHERE event_ID = :event_id");
    if ($query->execute([':event_title' => $edit_event_title, ':event_detail' => $edit_event_detail, ':event_date' => $edit_event_date, ':event_id' => $edit_event_id, ':department_id' => $edit_department_id])) {
        header("Location: $requestUri");
        exit();
    } else {
        echo "Error editing event.";
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
    $query = $pdo->prepare("SELECT * FROM tb_event WHERE event_title LIKE :searchTerm OR event_date LIKE :searchTerm LIMIT :limit OFFSET :offset");
    $query->bindValue(':searchTerm', '%' . $searchTerm . '%', PDO::PARAM_STR);
    $query->bindValue(':limit', $limit, PDO::PARAM_INT);
    $query->bindValue(':offset', $offset, PDO::PARAM_INT);

    if (!$query->execute()) {
        throw new Exception("Query failed: " . implode(" ", $query->errorInfo()));
    }

    // Fetch results
    $rows = $query->fetchAll(PDO::FETCH_ASSOC);

    // Pagination query
    $paginationQuery = $pdo->prepare("SELECT COUNT(*) AS total FROM tb_event WHERE event_title LIKE :searchTerm OR event_date LIKE :searchTerm");
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

    <?php include '../../components/rsoHeader.php'; ?>

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
                <button type="button" onclick="document.getElementById('addModal').showModal()">Add New Event <img
                        src='../../images/plus.png' alt='Image' class="icon" /> </button>
            </div>
        </section>

        <section class="tableContainer">
            <?php
            include '../../components/table.component.php';

            $head = array('ID', 'Name', 'Details', 'Date', 'Department', 'Actions');
            $body = array();

            foreach ($rows as $row) {
                $event_id = $row["event_id"];
                $event_title = $row["event_title"];
                $event_detail = $row["event_detail"];
                $event_date = $row["event_date"];
                $department_id = $row["department_id"];


                $departmentQuery = $conn->query("SELECT department_name FROM tb_department WHERE department_id = $department_id");
                $department = $departmentQuery->fetch_assoc();
                $department_name = $department ? $department["department_name"] : '';

                $actions = '<button type="button" onclick="editevent(' . $event_id . ', \'' . $event_title . '\', \'' . $event_detail . '\', \'' . $event_date . '\', \'' . $department_name . '\')">Edit</button> <button type="button" onclick="showDeleteModal(' . $event_id . ')">Delete</button>';
                
                $body[] = array($event_id, $event_title, $event_detail, $event_date, $department_name, $actions);
}
            createTable($head, $body);
            ?>

            <dialog id="addModal" class="modal">

                <div class="modal-content">
                    <button class="close" onclick="resetAddModal(true)">&times;</button>

                    <h2>CREATE NEW EVENT</h2>
                    <form method="POST" action="">
                        <div class="error-container">Event Already Exist</div>

                        <label for="event-name">Name:</label>
                        <input type="text" id="event-name" name="event_title" required>
                        <label for="event-detail">Detail:</label>
                        <input type="text" id="event-detail" name="event_detail" required>
                        <label for="event-date">Date:</label>
                        <input type="date" id="event-date" name="event_date" required>
                        <label for="department_id">Department:</label>
                        <select name="department_id">
                            <?php
                            $sql = 'SELECT * FROM tb_department';
                            $result = $conn->query($sql);
                                     while ($row = $result->fetch_assoc()){
                                        echo"<option value='{$row['department_id']}'>{$row['department_name']}</option>";
                                     }
                            ?>
                        </select>
                        <button type="submit" name="add_event">Create event </button>

                    </form>
                </div>
            </dialog>
            <dialog id="editModal" class="modal">

                <div class="modal-content">
                    <button class="close" onclick="resetAddModal(true)">&times;</button>

                    <h2>EDIT EVENT DETAILS</h2>
                    <div class="error-container2">Event Already Exist</div>

                    <form method="POST" action="">
                        <input type="hidden" id="edit-event-id" name="edit_event_id">
                        <label for="edit-event-title">Name:</label>
                        <input type="text" id="edit-event-title" name="edit_event_title">
                        <label for="edit-event-detail">Detail:</label>
                        <input type="text" id="edit-event-detail" name="edit_event_detail">
                        <label for="edit-event-date">Date:</label>
                        <input type="date" id="edit-event-date" name="edit_event_date">
                        <label for="edit-department-id">Department:</label>
                        <select name="edit_department_id">
                            <?php
                            $sql = 'SELECT * FROM tb_department';
                            $result = $conn->query($sql);
                                     while ($row = $result->fetch_assoc()){
                                        echo"<option value='{$row['department_id']}'>{$row['department_name']}</option>";
                                     }
                            ?>
                        </select>

                        <button type="submit" name="edit_event">Save</button>
                    </form>
                </div>
            </dialog>

            <dialog id="deleteModal" class="modal">
                <div class="modal-content">
                    <button class="close" onclick="closeModal('deleteModal')">&times;</button>
                    <h2>DELETE EVENT</h2>
                    <p>Are you sure you want to delete this event?</p>
                    <div class="clearfix">
                        <button type="button" class="cancelbtn" onclick="closeModal('deleteModal')">Cancel</button>
                        <button type="button" class="deletebtn" onclick="deleteevent()">Delete</button>
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
    const emailExistenceCheck = <?php echo json_encode(array_column($rows, 'event_date')); ?>;
    </script>
    <script src="../../script/event.js"></script>

</body>

</html>