<?php
session_start();
include '../../config/config.php';

if($_SERVER['REQUEST_METHOD'] === 'POST') {
    if(isset($_POST["add_event"])) {
        $event_title = htmlspecialchars($_POST["event_title"], ENT_QUOTES, "UTF-8");
        $event_detail = htmlspecialchars($_POST["event_detail"], ENT_QUOTES, "UTF-8");
        $event_date = htmlspecialchars($_POST["event_date"], ENT_QUOTES, "UTF-8");

        $target_dir = "../../images/events/";
        $original_filename = basename($_FILES["header_image"]["name"]);
        $uploadOk = 1;
        $imageFileType = strtolower(pathinfo($original_filename, PATHINFO_EXTENSION));

        $unique_filename = uniqid()."_".$event_title."_".time().".".$imageFileType;
        $target_file = $target_dir.$unique_filename;

        $check = getimagesize($_FILES["header_image"]["tmp_name"]);
        if($check === false) {
            echo "File is not an image.";
            exit();
        }

        if($_FILES["header_image"]["size"] > 500000) {
            echo "Sorry, your file is too large.";
            exit();
        }

        $allowedFormats = ["jpg", "jpeg", "png", "gif"];
        if(!in_array($imageFileType, $allowedFormats)) {
            echo "Sorry, only JPG, JPEG, PNG, and GIF files are allowed.";
            exit();
        }

        if(move_uploaded_file($_FILES["header_image"]["tmp_name"], $target_file)) {
            try {
                $department_id = $_SESSION['department_id'];
                $query = $pdo->prepare("INSERT INTO tb_event (department_id, event_title, event_detail, event_date, header_image) VALUES (?, ?, ?, ?, ?)");

                $filename = "images/events/".$unique_filename;
                $query->execute([$department_id, $event_title, $event_detail, $event_date, $filename]);

                $event_id = $pdo->lastInsertId();

                if(!empty($_FILES["event_image"]["name"])) {
                    $event_images = $_FILES["event_image"];
                    $fileCount = count($event_images['name']);

                    for($i = 0; $i < $fileCount; $i++) {
                        $original_filename_image = basename($event_images["name"][$i]);
                        $unique_filename_image = uniqid()."_".$event_title."_image_".time()."_".$i.".".pathinfo($original_filename_image, PATHINFO_EXTENSION);

                        $target_file_image = "../../images/events/".$unique_filename_image;
                        $target_file_images = "images/events/".$unique_filename_image;

                        $check_image = getimagesize($event_images["tmp_name"][$i]);
                        if($check_image !== false) {
                            $uploadOk = 1;
                        } else {
                            $uploadOk = 0;
                        }

                        if($event_images["size"][$i] > 500000) {
                            $uploadOk = 0;
                        }

                        if(!in_array(strtolower(pathinfo($original_filename_image, PATHINFO_EXTENSION)), $allowedFormats)) {
                            $uploadOk = 0;
                        }

                        if($uploadOk == 0) {
                            echo "Sorry, your file was not uploaded.";
                        } else {
                            if(move_uploaded_file($event_images["tmp_name"][$i], $target_file_image)) {
                                $insertImageQuery = $pdo->prepare("INSERT INTO tb_event_images (event_id, image_filename) VALUES (?, ?)");
                                $insertImageQuery->execute([$event_id, $target_file_images]);
                            } else {
                                echo "Sorry, there was an error uploading your file.";
                            }
                        }
                    }
                }

                header("Location: ".$_SERVER['REQUEST_URI']);
                exit();
            } catch (PDOException $e) {
                echo "Error adding event: ".$e->getMessage();
                exit();
            }
        } else {
            echo "Sorry, there was an error uploading your file.";
            exit();
        }
    } elseif(isset($_POST["delete_event"])) {
        $event_id = filter_input(INPUT_POST, "delete_event", FILTER_VALIDATE_INT);

        $getImagesQuery = $pdo->prepare("SELECT image_filename FROM tb_event_images WHERE event_id = ?");
        $getImagesQuery->execute([$event_id]);
        $resultImages = $getImagesQuery->fetchAll(PDO::FETCH_ASSOC);

        foreach($resultImages as $image) {
            $imageFilePath = "../../".$image["image_filename"];
            if(file_exists($imageFilePath)) {
                unlink($imageFilePath);
            }
        }

        $deleteEventQuery = $pdo->prepare("DELETE FROM tb_event WHERE event_id = ?");
        $deleteEventQuery->execute([$event_id]);

        if($deleteEventQuery->rowCount() > 0) {
            $deleteImagesQuery = $pdo->prepare("DELETE FROM tb_event_images WHERE event_id = ?");
            $deleteImagesQuery->execute([$event_id]);

            header("Location: ".$_SERVER['REQUEST_URI']);
        } else {
            echo "Event not found or already deleted.";
        }
    } elseif(isset($_POST["edit_event"])) {
        $edit_event_id = filter_input(INPUT_POST, "edit_event_id", FILTER_VALIDATE_INT);
        $edit_event_title = htmlspecialchars($_POST["edit_event_title"], ENT_QUOTES, "UTF-8");
        $edit_event_detail = htmlspecialchars($_POST["edit_event_detail"], ENT_QUOTES, "UTF-8");
        $edit_event_date = htmlspecialchars($_POST["edit_event_date"], ENT_QUOTES, "UTF-8");

        $query = $pdo->prepare("UPDATE tb_event SET event_title = ?, event_detail = ?, event_date = ? WHERE event_id = ?");
        $query->execute([$edit_event_title, $edit_event_detail, $edit_event_date, $edit_event_id]);

        if($query->rowCount() > 0) {
            header("Location: ".$_SERVER['REQUEST_URI']);
            exit();
        } else {
            echo "Error editing event.";
        }
    }
}


$limit = isset($_GET['limit']) ? $_GET['limit'] : 10;
$page = isset($_GET['page']) ? $_GET['page'] : 1;
$offset = ($page - 1) * $limit;

$searchTerm = isset($_GET['search']) ? $_GET['search'] : '';

$statusFilter = isset($_GET['status']) ? $_GET['status'] : '';
$statusCondition = '';

// Validate and set the appropriate status condition
if($statusFilter === 'ongoing' || $statusFilter === 'upcoming' || $statusFilter === 'ended') {
    $statusCondition = (!empty($statusFilter)) ? ' AND status = :status' : '';
}

try {
    $query = $pdo->prepare("
        SELECT * FROM tb_event 
        WHERE department_id = :department_id 
        AND (event_title LIKE :searchTerm OR event_detail LIKE :searchTerm OR event_date LIKE :searchTerm)
        ".$statusCondition." 
        LIMIT :limit OFFSET :offset");

    $query->bindValue(':department_id', $_SESSION['department_id'], PDO::PARAM_INT);
    $query->bindValue(':searchTerm', '%'.$searchTerm.'%', PDO::PARAM_STR);
    $query->bindValue(':limit', $limit, PDO::PARAM_INT);
    $query->bindValue(':offset', $offset, PDO::PARAM_INT);

    // Bind status parameter if a filter is applied
    if(!empty($statusFilter)) {
        $query->bindValue(':status', $statusFilter, PDO::PARAM_STR);
    }

    if(!$query->execute()) {
        throw new Exception("Query failed: ".implode(" ", $query->errorInfo()));
    }

    $rows = $query->fetchAll(PDO::FETCH_ASSOC);

    $paginationQuery = $pdo->prepare("
        SELECT COUNT(*) AS total FROM tb_event 
        WHERE department_id = :department_id 
        AND (event_title LIKE :searchTerm OR event_date LIKE :searchTerm)
        ".$statusCondition);

    $paginationQuery->bindValue(':department_id', $_SESSION['department_id'], PDO::PARAM_INT);
    $paginationQuery->bindValue(':searchTerm', '%'.$searchTerm.'%', PDO::PARAM_STR);

    // Bind status parameter if a filter is applied
    if(!empty($statusFilter)) {
        $paginationQuery->bindValue(':status', $statusFilter, PDO::PARAM_STR);
    }

    if(!$paginationQuery->execute()) {
        throw new Exception("Pagination query failed: ".implode(" ", $paginationQuery->errorInfo()));
    }

    $totalResult = $paginationQuery->fetch(PDO::FETCH_ASSOC);
    $totalRows = $totalResult['total'];
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
                <select id="status-filter" name="status">
                    <option value="">Status: All</option>
                    <option value="ongoing" <?php echo ($statusFilter === 'ongoing') ? 'selected' : ''; ?>>Status: Ongoing
                    </option>
                    <option value="upcoming" <?php echo ($statusFilter === 'upcoming') ? 'selected' : ''; ?>>Status:
                        Upcoming
                    </option>
                    <option value="ended" <?php echo ($statusFilter === 'ended') ? 'selected' : ''; ?>>Status: Ended
                    </option>
                    <!-- Add other status options as needed -->
                </select>

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
            $actions = '<button type="button" onclick="editevent('.$event_id.', \''.$event_title.'\', \''.$event_detail.'\', \''.$event_date.'\')">Edit</button> 
            <button type="button" onclick="showDeleteModal('.$event_id.')">Delete</button> 
            <a href="editevent.php?event_id='.$event_id.'" type="button">View</a>'; // Modified this line
        
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
            generatePaginationLinks($searchTerm, $limit, $paginationQuery, null, $statusFilter);
            ?>
        </section>
    </main>

    <?php
    // Include your PHP code here to set $requestUri
    $requestUri = $_SERVER['REQUEST_URI'];
    ?>
    <script>
        // Add this code in your event.js file
        document.addEventListener("DOMContentLoaded", function () {
            const statusFilter = document.getElementById('status-filter');

            // Add an event listener for the status filter
            statusFilter.addEventListener('change', function () {
                const selectedStatus = statusFilter.value;

                // Get the current URL
                const currentUrl = new URL(window.location.href);

                // Remove the 'page' query parameter if it exists
                currentUrl.searchParams.delete('page');

                // Update the 'status' query parameter or add it if not present
                currentUrl.searchParams.set('status', selectedStatus);

                // Reload the page with the updated URL
                window.location.href = currentUrl.href;
            });
        });


        const base_url = "<?php echo htmlspecialchars($requestUri, ENT_QUOTES, 'UTF-8'); ?>";
        const emailExistenceCheck = <?php echo json_encode(array_column($rows, 'event_date')); ?>;
    </script>
    <script src="../../script/event.js"></script>
</body>

</html>