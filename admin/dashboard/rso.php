<?php
include '../../config/config.php';
$requestUri = $_SERVER['REQUEST_URI'];

if(isset($_POST["add_rso"])) {
    $rso_name = htmlspecialchars($_POST["rso_name"], ENT_QUOTES, "UTF-8");
    $rso_password = htmlspecialchars($_POST["rso_password"], ENT_QUOTES, "UTF-8");
    $rso_email = htmlspecialchars($_POST["email"], ENT_QUOTES, "UTF-8");
    $department_id = $_POST["department_id"];

    $target_dir = "../../images/profiles/";
    $original_filename = basename($_FILES["fileToUpload"]["name"]);
    $uploadOk = 1;
    $imageFileType = strtolower(pathinfo($original_filename, PATHINFO_EXTENSION));
    $unique_filename = uniqid()."_".$rso_email."_".time().".".$imageFileType;
    $path = "images/profiles/".$unique_filename;
    $target_file = $target_dir.$unique_filename;

    $check = getimagesize($_FILES["fileToUpload"]["tmp_name"]);
    if($check === false) {
        echo "File is not an image.";
        exit();
    }

    if($_FILES["fileToUpload"]["size"] > 5000000) {
        echo "Sorry, your file is too large.";
        exit();
    }

    $allowed_formats = ["jpg", "jpeg", "png"];
    if(!in_array($imageFileType, $allowed_formats)) {
        echo "Sorry, only JPG, JPEG, and PNG files are allowed.";
        exit();
    }

    if(move_uploaded_file($_FILES["fileToUpload"]["tmp_name"], $target_file)) {
        try {
            $sql_account = "INSERT INTO tb_rso (rso_name, rso_password, rso_email, department_id, rso_profile) VALUES (?, ?, ?, ?, ?)";
            $stmt_account = $pdo->prepare($sql_account);
            $stmt_account->execute([$rso_name, $rso_password, $rso_email, $department_id, $path]);
            header("Location: rso.php");
        } catch (PDOException $e) {
            echo "Error: ".$e->getMessage();
            exit();
        }
    } else {
        echo "Sorry, there was an error uploading your file.";
        exit();
    }

    $query = $pdo->prepare("INSERT INTO tb_rso (rso_name, rso_password, rso_email, department_id) VALUES (:rso_name, :rso_password, :rso_email, :department_id)");
    if($query->execute([':rso_name' => $rso_name, ':rso_password' => $rso_password, ':rso_email' => $rso_email, ':department_id' => $department_id])) {
        header("Location: $requestUri");
        exit();
    } else {
        echo "Error adding rso.";
    }
}

if(isset($_POST["delete_rso"])) {
    $rso_id = filter_input(INPUT_POST, "delete_rso", FILTER_VALIDATE_INT);

    $query = $pdo->prepare("DELETE FROM tb_rso WHERE rso_id = :rso_id");
    if($query->execute([':rso_id' => $rso_id])) {
        header("Location: $requestUri");
    } else {
        echo "Error deleting rso.";
    }
}

if(isset($_POST["edit_rso"])) {
    $edit_rso_id = filter_input(INPUT_POST, "edit_rso_id", FILTER_VALIDATE_INT);
    $edit_rso_name = htmlspecialchars($_POST["edit_rso_name"], ENT_QUOTES, "UTF-8");
    $edit_rso_password = htmlspecialchars($_POST["edit_rso_password"], ENT_QUOTES, "UTF-8");
    $edit_rso_email = htmlspecialchars($_POST["edit_rso_email"], ENT_QUOTES, "UTF-8");
    $edit_department_id = $_POST["edit_department_id"];

    $query = $pdo->prepare("UPDATE tb_rso SET rso_name = :rso_name, rso_password = :rso_password, rso_email = :rso_email, department_id = :department_id WHERE rso_id = :rso_id");
    if($query->execute([':rso_name' => $edit_rso_name, ':rso_password' => $edit_rso_password, ':rso_email' => $edit_rso_email, ':department_id' => $edit_department_id, ':rso_id' => $edit_rso_id])) {
        header("Location: $requestUri");
        exit();
    } else {
        echo "Error editing rso.";
    }
}

$limit = isset($_GET['limit']) ? $_GET['limit'] : 10;
$page = isset($_GET['page']) ? $_GET['page'] : 1;
$offset = ($page - 1) * $limit;

$searchTerm = isset($_GET['search']) ? $_GET['search'] : '';
$selectedDepartment = isset($_GET['departmentFilter']) ? $_GET['departmentFilter'] : '';

try {
    $query = $pdo->prepare("SELECT * FROM tb_rso WHERE (rso_name LIKE :searchTerm OR rso_email LIKE :searchTerm) ".
        ($selectedDepartment ? "AND department_id = :department_id" : "").
        " LIMIT :limit OFFSET :offset");
    $query->bindValue(':searchTerm', '%'.$searchTerm.'%', PDO::PARAM_STR);
    $query->bindValue(':limit', $limit, PDO::PARAM_INT);
    $query->bindValue(':offset', $offset, PDO::PARAM_INT);
    if($selectedDepartment) {
        $query->bindValue(':department_id', $selectedDepartment, PDO::PARAM_INT);
    }

    if(!$query->execute()) {
        throw new Exception("Query failed: ".implode(" ", $query->errorInfo()));
    }

    $rows = $query->fetchAll(PDO::FETCH_ASSOC);
    $paginationQuery = $pdo->prepare("SELECT COUNT(*) AS total FROM tb_rso WHERE rso_name LIKE :searchTerm OR rso_email LIKE :searchTerm");
    $paginationQuery->bindValue(':searchTerm', '%'.$searchTerm.'%', PDO::PARAM_STR);
} catch (Exception $ex) {
    echo "Error: ".$ex->getMessage();
    die();
}
?>
<!DOCTYPE html>
<html>

<head>
    <link rel="stylesheet" href="../../styles/rso.css">
    <title>Rso</title>
</head>

<body>

    <?php include '../../components/adminHeader.php'; ?>

    <main>
        <section class="head">
            <div class="searchCont">
                <?php
                include '../../components/search.php';
                ?>
                <?php if(!empty($searchTerm)): ?>
                    <img src='../../images/cross.png' alt='Image' class="icon" onclick="clearSearch()" id='clearBtn' />
                <?php endif; ?>
            </div>
            <div class="headbtn">
                <select id="departmentFilter" name="departmentFilter" onchange="applyDepartmentFilter()">
                    <option value="">All Departments</option>
                    <?php
                    $departmentQuery = $pdo->prepare("SELECT department_id, department_name FROM tb_department");
                    $departmentQuery->execute();

                    while($deptRow = $departmentQuery->fetch()) {
                        $selected = ($deptRow['department_id'] == $selectedDepartment) ? "selected" : "";
                        echo "<option value='".$deptRow['department_id']."' $selected>".$deptRow['department_name']."</option>";
                    }
                    ?>
                </select>

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

            $head = array('ID', 'Profile', 'Name', 'Password', 'Email', 'Department', 'Actions');
            $body = array();

            foreach($rows as $row) {
                $rso_id = $row["rso_id"];
                $rso_name = $row["rso_name"];
                $rso_password = $row["rso_password"];
                $rso_email = $row["rso_email"];
                $department_id = $row["department_id"];
                $department_name = '';

                $departmentQuery = $pdo->prepare("SELECT department_name FROM tb_department WHERE department_id = :department_id");
                $departmentQuery->execute([':department_id' => $department_id]);
                if($deptRow = $departmentQuery->fetch()) {
                    $department_name = $deptRow['department_name'];
                }

                $profile_image = "<img src='../../".$row["rso_profile"]."' alt='Profile Image' class='profile-image' style='width: 30px; height: 30px; border-radius: 50px'>";

                $actions = '<button type="button" onclick="editRso('.$rso_id.', \''.$rso_name.'\', \''.$rso_password.'\', \''.$rso_email.'\', \''.$department_id.'\')">Edit</button> <button type="button" onclick="showDeleteModal('.$rso_id.')">Delete</button>';
                $body[] = array($rso_id, $profile_image, $rso_name, $rso_password, $rso_email, $department_name, $actions);
            }

            createTable($head, $body);
            ?>

            <dialog id="addModal" class="modal">
                <div class="modal-content">
                    <button class="close" onclick="resetAddModal(true)">&times;</button>
                    <h2>CREATE RSO ACCOUNT</h2>
                    <form method="POST" action="" enctype="multipart/form-data">
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

                            while($deptRow = $departmentQuery->fetch()) {
                                echo "<option value='".$deptRow['department_id']."'>".$deptRow['department_name']."</option>";
                            }
                            ?>
                        </select>
                        <label for="fileToUpload">Upload Image:</label>
                        <input type="file" id="fileToUpload" name="fileToUpload" accept="image/*" required>
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

                            while($deptRow = $departmentQuery->fetch()) {
                                $selected = ($deptRow['department_id'] == $row['department_id']) ? "selected" : "";
                                echo "<option value='".$deptRow['department_id']."' $selected>".$deptRow['department_name']."</option>";
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
            generatePaginationLinks($searchTerm, $limit, $paginationQuery, null, null);
            ?>
        </section>
    </main>
    <?php
    $requestUri = $_SERVER['REQUEST_URI'];
    ?>
    <script>
        function applyDepartmentFilter() {
            const selectedDepartment = document.getElementById('departmentFilter').value;
            const urlParams = new URLSearchParams(window.location.search);
            urlParams.set('departmentFilter', selectedDepartment);
            window.location.href = window.location.pathname + '?' + urlParams.toString();
        }
        const base_url = "<?php echo htmlspecialchars($requestUri, ENT_QUOTES, 'UTF-8'); ?>";
        const emailExistenceCheck = <?php echo json_encode(array_column($rows, 'rso_email')); ?>;
    </script>
    <script src="../../script/rso.js"></script>

</body>

</html>