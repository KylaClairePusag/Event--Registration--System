<?php
// Database connection setup
include '../../config/config.php';
// Get the current request URI
$requestUri = $_SERVER['REQUEST_URI'];

// Add new Admin
if (isset($_POST["add_admin"])) {
    $admin_name = htmlspecialchars($_POST["admin_name"], ENT_QUOTES, "UTF-8");
    $admin_password = htmlspecialchars($_POST["admin_password"], ENT_QUOTES, "UTF-8");
    $admin_email = htmlspecialchars($_POST["admin_email"], ENT_QUOTES, "UTF-8");

    // Image upload code
    $target_dir = "../../images/profiles/";
    $original_filename = basename($_FILES["fileToUpload"]["name"]);
    $uploadOk = 1;
    $imageFileType = strtolower(pathinfo($original_filename, PATHINFO_EXTENSION));

    // Generate a unique filename
    $unique_filename = uniqid() . "_" . $admin_name . "_" . time() . "." . $imageFileType;
    $target_file = $target_dir . $unique_filename;

    // Check if the file is a valid image
    $check = getimagesize($_FILES["fileToUpload"]["tmp_name"]);
    if ($check !== false) {
        $uploadOk = 1;
    } else {
        $uploadOk = 0;
    }

    // Check file size (500 KB limit)
    if ($_FILES["fileToUpload"]["size"] > 500000) {
        $uploadOk = 0;
    }

    // Allow only certain file formats
    $allowedFormats = ["jpg", "jpeg", "png", "gif"];
    if (!in_array($imageFileType, $allowedFormats)) {
        $uploadOk = 0;
    }

    // Check if $uploadOk is set to 0 by an error
    if ($uploadOk == 0) {
        echo "Sorry, your file was not uploaded.";
    } else {
        // Try to upload the file
        if (move_uploaded_file($_FILES["fileToUpload"]["tmp_name"], $target_file)) {
            // Insert data into the database
            $query = $pdo->prepare("INSERT INTO tb_admin (admin_name, admin_password, admin_email, admin_profile) VALUES (:admin_name, :admin_password, :admin_email, :admin_profile)");

            // ...

            $filename = "images/profiles/" . $unique_filename;

            if ($query->execute([':admin_name' => $admin_name, ':admin_password' => $admin_password, ':admin_email' => $admin_email, ':admin_profile' => $filename])) {
                header("Location: $requestUri");
                exit();
            } else {
                echo "Error adding admin.";
            }

        } else {
            echo "Sorry, there was an error uploading your file.";
        }
    }

}

// Delete Admin
if (isset($_POST["delete_admin"])) {
    $admin_id = filter_input(INPUT_POST, "delete_admin", FILTER_VALIDATE_INT);

    // Get the filename of the admin's profile image
    $getImageFilenameQuery = $pdo->prepare("SELECT admin_profile FROM tb_admin WHERE admin_ID = :admin_id");
    if ($getImageFilenameQuery->execute([':admin_id' => $admin_id])) {
        $imageFilenameRow = $getImageFilenameQuery->fetch(PDO::FETCH_ASSOC);
        $imageFilename = $imageFilenameRow['admin_profile'];

        // Delete data from the database
        $deleteAdminQuery = $pdo->prepare("DELETE FROM tb_admin WHERE admin_ID = :admin_id");

        if ($deleteAdminQuery->execute([':admin_id' => $admin_id])) {
            // Check if the deletion from the database was successful
            if ($deleteAdminQuery->rowCount() > 0) {
                // Delete the associated image file
                $imageFilePath = "../../" . $imageFilename;
                if (file_exists($imageFilePath)) {
                    unlink($imageFilePath);
                }
                header("Location: $requestUri");
            } else {
                echo "Admin not found or already deleted.";
            }
        } else {
            echo "Error deleting admin.";
        }
    } else {
        echo "Error retrieving admin image filename.";
    }
}


// Edit Admin
if (isset($_POST["edit_admin"])) {
    $edit_admin_id = filter_input(INPUT_POST, "edit_admin_id", FILTER_VALIDATE_INT);
    $edit_admin_name = htmlspecialchars($_POST["edit_admin_name"], ENT_QUOTES, "UTF-8");
    $edit_admin_password = htmlspecialchars($_POST["edit_admin_password"], ENT_QUOTES, "UTF-8");
    $edit_admin_email = htmlspecialchars($_POST["edit_admin_email"], ENT_QUOTES, "UTF-8");

    // Update data in the database
    $query = $pdo->prepare("UPDATE tb_admin SET admin_name = :admin_name, admin_password = :admin_password, admin_email = :admin_email WHERE admin_ID = :admin_id");
    if ($query->execute([':admin_name' => $edit_admin_name, ':admin_password' => $edit_admin_password, ':admin_email' => $edit_admin_email, ':admin_id' => $edit_admin_id])) {
        header("Location: $requestUri");
        exit();
    } else {
        echo "Error editing admin.";
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
    $query = $pdo->prepare("SELECT * FROM tb_admin WHERE admin_name LIKE :searchTerm OR admin_email LIKE :searchTerm LIMIT :limit OFFSET :offset");
    $query->bindValue(':searchTerm', '%' . $searchTerm . '%', PDO::PARAM_STR);
    $query->bindValue(':limit', $limit, PDO::PARAM_INT);
    $query->bindValue(':offset', $offset, PDO::PARAM_INT);

    if (!$query->execute()) {
        throw new Exception("Query failed: " . implode(" ", $query->errorInfo()));
    }

    // Fetch results
    $rows = $query->fetchAll(PDO::FETCH_ASSOC);

    // Pagination query
    $paginationQuery = $pdo->prepare("SELECT COUNT(*) AS total FROM tb_admin WHERE admin_name LIKE :searchTerm OR admin_email LIKE :searchTerm");
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
    <title>Admin</title>
</head>

<body>
    <?php include '../../components/adminHeader.php'; ?>

    <main>
        <section class="head">
            <div class="searchCont">
                <?php include '../../components/search.php'; ?>
                <?php if (!empty($searchTerm)): ?>
                    <img src='../../images/cross.png' alt='Image' class="icon" onclick="clearSearch()" id='clearBtn' />
                <?php endif; ?>
            </div>
            <div class="headbtn">
                <?php include '../../components/limit.php'; ?>
                <button type="button" onclick="document.getElementById('addModal').showModal()">Add Admin <img
                        src='../../images/plus.png' alt='Image' class="icon" /> </button>
            </div>
        </section>

        <section class="tableContainer">
            <?php include '../../components/table.component.php';

            $head = array('ID', 'Profile', 'Name', 'Password', 'Email', 'Actions');
            $body = array();

            foreach ($rows as $row) {
                $admin_id = $row["admin_ID"];
                $admin_name = $row["admin_name"];
                $admin_password = $row["admin_password"];
                $admin_email = $row["admin_email"];
                $admin_profile = $row["admin_profile"];

                $actions = '<button type="button" onclick="editAdmin(' . $admin_id . ', \'' . $admin_name . '\', \'' . $admin_password . '\', \'' . $admin_email . '\')">Edit</button> <button type="button" onclick="showDeleteModal(' . $admin_id . ')">Delete</button>';
                $body[] = array($admin_id, '<img src="../../' . $admin_profile . '" alt="Admin Profile Image"  style="width: 30px; height: 30px; border-radius: 50px;">', $admin_name, $admin_password, $admin_email, $actions);

            }
            createTable($head, $body);
            ?>

            <dialog id="addModal" class="modal">
                <div class="modal-content">
                    <button class="close" onclick="resetAddModal(true)">&times;</button>

                    <h2>CREATE ADMIN ACCOUNT</h2>
                    <form method="POST" action="" enctype="multipart/form-data">
                        <div class="error-container">Email Already Taken</div>

                        <label for="admin-name">Name:</label>
                        <input type="text" id="admin-name" name="admin_name" required>
                        <label for="admin-password">Password:</label>
                        <input type="password" id="admin-password" name="admin_password" required>
                        <label for="admin-email">Email:</label>
                        <input type="email" id="admin-email" name="admin_email" required>
                        <label for="fileToUpload">Choose an image:</label>
                        <input type="file" name="fileToUpload" id="fileToUpload" accept="image/*" required>
                        <button type="submit" name="add_admin">Create Admin Account</button>
                    </form>
                </div>
            </dialog>
            <dialog id="editModal" class="modal">
                <div class="modal-content">
                    <button class="close" onclick="resetAddModal(true)">&times;</button>

                    <h2>EDIT ADMIN ACCOUNT</h2>
                    <div class="error-container2">Email Already Taken</div>

                    <form method="POST" action="">
                        <input type="hidden" id="edit-admin-id" name="edit_admin_id">
                        <label for="edit-admin-name">Name:</label>
                        <input type="text" id="edit-admin-name" name="edit_admin_name">
                        <label for="edit-admin-password">Password:</label>

                        <div class='passcont'>
                            <input type="password" id="edit-admin-password" name="edit_admin_password">
                            <img src='../../images/view.png' alt='Show Password' class="icon"
                                onclick="togglePasswordVisibility('edit-admin-password')" />
                            <img src='../../images/hide.png' alt='Hide Password' class="icon hide"
                                onclick="togglePasswordVisibility('edit-admin-password')" />
                        </div>
                        <label for="edit-admin-email">Email:</label>
                        <input type="email" id="edit-admin-email" name="edit_admin_email">
                        <input type="hidden" id="original-email" value="<?php echo $originalEmail; ?>">
                        <button type="submit" name="edit_admin">Save</button>
                    </form>
                </div>
            </dialog>

            <dialog id="deleteModal" class="modal">
                <div class="modal-content">
                    <button class="close" onclick="closeModal('deleteModal')">&times;</button>
                    <h2>DELETE ADMIN ACCOUNT</h2>
                    <p>Are you sure you want to delete this admin?</p>
                    <div class="clearfix">
                        <button type="button" class="cancelbtn" onclick="closeModal('deleteModal')">Cancel</button>
                        <button type="button" class="deletebtn" onclick="deleteAdmin()">Delete</button>
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
        const emailExistenceCheck = <?php echo json_encode(array_column($rows, 'admin_email')); ?>;
    </script>
    <script src="../../script/admin.js"></script>
</body>

</html>