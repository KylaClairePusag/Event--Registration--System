<?php
include '../../config/config.php';

$conn = new mysqli('localhost', 'root', '', 'db_ba3101');
if(isset($_POST["add_event"])) {
    $event_title = htmlspecialchars($_POST["event_title"], ENT_QUOTES, "UTF-8");
    $event_detail = htmlspecialchars($_POST["event_detail"], ENT_QUOTES, "UTF-8");
    $event_date = htmlspecialchars($_POST["event_date"], ENT_QUOTES, "UTF-8");

    // Image upload code for header_image
    $target_dir = "../../images/events/";
    $original_filename = basename($_FILES["header_image"]["name"]);
    $uploadOk = 1;
    $imageFileType = strtolower(pathinfo($original_filename, PATHINFO_EXTENSION));

    // Generate a unique filename for header_image
    $unique_filename = uniqid()."_".$event_title."_".time().".".$imageFileType;
    $target_file = $target_dir.$unique_filename;

    // Check if the file is a valid image
    $check = getimagesize($_FILES["header_image"]["tmp_name"]);
    if($check !== false) {
        $uploadOk = 1;
    } else {
        $uploadOk = 0;
    }

    // Check file size (500 KB limit)
    if($_FILES["header_image"]["size"] > 500000) {
        $uploadOk = 0;
    }

    // Allow only certain file formats
    $allowedFormats = ["jpg", "jpeg", "png", "gif"];
    if(!in_array($imageFileType, $allowedFormats)) {
        $uploadOk = 0;
    }

    // Check if $uploadOk is set to 0 by an error
    if($uploadOk == 0) {
        echo "Sorry, your file was not uploaded.";
    } else {
        // Try to upload the file
        if(move_uploaded_file($_FILES["header_image"]["tmp_name"], $target_file)) {
            // Insert data into the database for tb_event
            $department_id = $_SESSION['department_id'];
            $query = $conn->prepare("INSERT INTO tb_event (department_id, event_title, event_detail, event_date, header_image) VALUES (?, ?, ?, ?, ?)");

            $filename = "images/events/".$unique_filename;
            $query->bind_param('issss', $department_id, $event_title, $event_detail, $event_date, $filename);

            if($query->execute()) {
                // Retrieve the event_id of the inserted event
                $event_id = $conn->insert_id;

                // Insert event images into tb_event_images
                if(!empty($_FILES["event_image"]["name"])) {
                    $event_images = $_FILES["event_image"];
                    $fileCount = count($event_images['name']);

                    for($i = 0; $i < $fileCount; $i++) {
                        $original_filename_image = basename($event_images["name"][$i]);
                        $unique_filename_image = uniqid()."_".$event_title."_image_".time()."_".$i.".".pathinfo($original_filename_image, PATHINFO_EXTENSION);

                        $target_file_image = "../../images/events/".$unique_filename_image;
                        $target_file_images = "images/events/".$unique_filename_image;
                        // Check if the file is a valid image
                        $check_image = getimagesize($event_images["tmp_name"][$i]);
                        if($check_image !== false) {
                            $uploadOk = 1;
                        } else {
                            $uploadOk = 0;
                        }

                        // Check file size (500 KB limit)
                        if($event_images["size"][$i] > 500000) {
                            $uploadOk = 0;
                        }

                        // Allow only certain file formats
                        if(!in_array(strtolower(pathinfo($original_filename_image, PATHINFO_EXTENSION)), $allowedFormats)) {
                            $uploadOk = 0;
                        }

                        // Check if $uploadOk is set to 0 by an error
                        if($uploadOk == 0) {
                            echo "Sorry, your file was not uploaded.";
                        } else {
                            // Try to upload the file
                            if(move_uploaded_file($event_images["tmp_name"][$i], $target_file_image)) {
                                // Insert image information into tb_event_images
                                $insertImageQuery = $conn->prepare("INSERT INTO tb_event_images (event_id, image_filename) VALUES (?, ?)");
                                $insertImageQuery->bind_param('is', $event_id, $target_file_images);

                                if($insertImageQuery->execute()) {
                                    // Image information inserted successfully
                                } else {
                                    echo "Error inserting event image information.";
                                }
                            } else {
                                echo "Sorry, there was an error uploading your file.";
                            }
                        }
                    }
                }

                header("Location: $requestUri");
                exit();
            } else {
                echo "Error adding event.";
            }
        } else {
            echo "Sorry, there was an error uploading your file.";
        }
    }
}

// Delete event
if(isset($_POST["delete_event"])) {
    $event_id = filter_input(INPUT_POST, "delete_event", FILTER_VALIDATE_INT);

    // Retrieve event images filenames
    $getImagesQuery = $conn->prepare("SELECT image_filename FROM tb_event_images WHERE event_id = ?");
    $getImagesQuery->bind_param('i', $event_id);
    $getImagesQuery->execute();
    $resultImages = $getImagesQuery->get_result();
    $imageFilenames = $resultImages->fetch_all(MYSQLI_ASSOC);

    // Delete event images from the folder
    foreach($imageFilenames as $image) {
        $imageFilePath = "../../".$image["image_filename"];
        if(file_exists($imageFilePath)) {
            unlink($imageFilePath);
        }
    }

    // Delete event from the database
    $deleteEventQuery = $conn->prepare("DELETE FROM tb_event WHERE event_id = ?");
    $deleteEventQuery->bind_param('i', $event_id);

    if($deleteEventQuery->execute()) {
        // Check if the deletion from the database was successful
        if($deleteEventQuery->affected_rows > 0) {
            // Delete event images from the database
            $deleteImagesQuery = $conn->prepare("DELETE FROM tb_event_images WHERE event_id = ?");
            $deleteImagesQuery->bind_param('i', $event_id);
            $deleteImagesQuery->execute();

            header("Location: $requestUri");
        } else {
            echo "Event not found or already deleted.";
        }
    } else {
        echo "Error deleting event.";
    }
}

// Edit event
if(isset($_POST["edit_event"])) {
    $edit_event_id = filter_input(INPUT_POST, "edit_event_id", FILTER_VALIDATE_INT);
    $edit_event_title = htmlspecialchars($_POST["edit_event_title"], ENT_QUOTES, "UTF-8");
    $edit_event_detail = htmlspecialchars($_POST["edit_event_detail"], ENT_QUOTES, "UTF-8");
    $edit_event_date = htmlspecialchars($_POST["edit_event_date"], ENT_QUOTES, "UTF-8");

    // Update data in the database
    $query = $conn->prepare("UPDATE tb_event SET event_title = ?, event_detail = ?, event_date = ? WHERE event_id = ?");
    $query->bind_param('sssi', $edit_event_title, $edit_event_detail, $edit_event_date, $edit_event_id);

    if($query->execute()) {
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
    $query = $conn->prepare("SELECT * FROM tb_event WHERE department_id = ? AND (event_title LIKE ? OR event_detail LIKE ? OR event_date LIKE ?) LIMIT ? OFFSET ?");
    $query->bind_param('issssi', $departmentId, $searchTerm, $searchTerm, $searchTerm, $limit, $offset);

    $departmentId = $departmentId = $_SESSION['department_id']; // Replace with your desired department_id
    $searchTerm = '%'.$searchTerm.'%';

    if(!$query->execute()) {
        throw new Exception("Query failed: ".$query->error);
    }

    // Fetch results
    $result = $query->get_result();
    $rows = $result->fetch_all(MYSQLI_ASSOC);

    // Pagination query
    $paginationQuery = $conn->prepare("SELECT COUNT(*) AS total FROM tb_event WHERE department_id = ? AND (event_title LIKE ? OR event_date LIKE ?)");
    $paginationQuery->bind_param('iss', $departmentId, $searchTerm, $searchTerm);

    if(!$paginationQuery->execute()) {
        throw new Exception("Pagination query failed: ".$paginationQuery->error);
    }

    $totalResult = $paginationQuery->get_result();
    $totalRows = $totalResult->fetch_assoc()['total'];
} catch (Exception $ex) {
    echo "Error: ".$ex->getMessage();
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
                <?php include '../../components/search.php'; ?>
                <?php if(!empty($searchTerm)): ?>
                    <img src='../../images/cross.png' alt='Image' class="icon" onclick="clearSearch()" id='clearBtn' />
                <?php endif; ?>
            </div>
            <div class="headbtn">
                <?php include '../../components/limit.php'; ?>
                <button type="button" onclick="document.getElementById('addModal').showModal()">Add New Event <img
                        src='../../images/plus.png' alt='Image' class="icon" /> </button>
            </div>
        </section>

        <?php
        include '../../components/table.component.php';

        $head = array('ID', 'Name', 'Details', 'Date', 'Status', 'Actions');

        $body = array();

        foreach($rows as $row) {
            $event_id = $row["event_id"];
            $event_title = $row["event_title"];
            $event_detail = $row["event_detail"];
            $event_date = $row["event_date"];
            $status = $row["status"];
            $actions = '<button type="button" onclick="editevent('.$event_id.', \''.$event_title.'\', \''.$event_detail.'\', \''.$event_date.'\')">Edit</button> <button type="button" onclick="showDeleteModal('.$event_id.')">Delete</button>';

            $body[] = array($event_id, $event_title, $event_detail, $event_date, $status, $actions);
        }
        createTable($head, $body);
        ?>

        <dialog id="addModal" class="modal">
            <div class="modal-content">
                <button class="close" onclick="resetAddModal(true)">&times;</button>
                <h2>CREATE NEW EVENT</h2>
                <form method="POST" action="" enctype="multipart/form-data">
                    <div class="error-container">Event Already Exist</div>
                    <label for="event-name">Name:</label>
                    <input type="text" id="event-name" name="event_title" required>
                    <label for="event-detail">Detail:</label>
                    <input type="text" id="event-detail" name="event_detail" required>
                    <label for="event-date">Date:</label>
                    <input type="date" id="event-date" name="event_date" required>
                    <label for="header_image">Header Image:</label>
                    <input type="file" name="header_image" accept="image/*">
                    <label for="event-image">Event Image:</label>
                    <input type="file" name="event_image[]" accept="image/*" multiple>
                    <button type="submit" name="add_event">Create event </button>
                </form>
            </div>
        </dialog>

        <dialog id="editModal" class="modal">
            <div class="modal-content">
                <button class="close" onclick="resetAddModal(true)">&times;</button>
                <h2>EDIT EVENT DETAILS</h2>
                <div class="error-container2">Event Already Exist</div>
                <form method="POST" action="" enctype="multipart/form-data">
                    <input type="hidden" id="edit-event-id" name="edit_event_id">
                    <label for="edit-event-title">Name:</label>
                    <input type="text" id="edit-event-title" name="edit_event_title">
                    <label for="edit-event-detail">Detail:</label>
                    <input type="text" id="edit-event-detail" name="edit_event_detail">
                    <label for="edit-event-date">Date:</label>
                    <input type="date" id="edit-event-date" name="edit_event_date">
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
            <?php include '../../components/pagination.php';
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