<?php
include '../../config/config.php';
if($_SERVER['REQUEST_METHOD'] === 'POST') {
    if(isset($_POST["add_emp"])) {
        $empid = $_POST["empid"];
        $emp_email = $_POST["email"];
        $emp_password = $_POST["emp_password"];
        $role_id = $_POST["role_id"];
        $department_id = $_POST["department_id"];
        $target_dir = "../../images/profiles/";
        $original_filename = basename($_FILES["fileToUpload"]["name"]);
        $uploadOk = 1;
        $imageFileType = strtolower(pathinfo($original_filename, PATHINFO_EXTENSION));
        $unique_filename = uniqid()."_".$emp_email."_".time().".".$imageFileType;
        $target_file = $target_dir.$unique_filename;

        // Check if the file is an image
        $check = getimagesize($_FILES["fileToUpload"]["tmp_name"]);
        if($check === false) {
            echo "File is not an image.";
            exit();
        }

        // Check file size
        if($_FILES["fileToUpload"]["size"] > 5000000) {
            echo "Sorry, your file is too large.";
            exit();
        }

        // Allow certain file formats
        $allowed_formats = ["jpg", "jpeg", "png"];
        if(!in_array($imageFileType, $allowed_formats)) {
            echo "Sorry, only JPG, JPEG, and PNG files are allowed.";
            exit();
        }

        // Move the uploaded file to the target directory
        if(move_uploaded_file($_FILES["fileToUpload"]["tmp_name"], $target_file)) {
            try {
                // Insert data into tbempaccount
                $sql_account = "INSERT INTO tbempaccount (empid, emp_email, emp_password, role_id, department_id, emp_profile) VALUES (?, ?, ?, ?, ?, ?)";
                $stmt_account = $pdo->prepare($sql_account);
                $stmt_account->execute([$empid, $emp_email, $emp_password, $role_id, $department_id, $unique_filename]);
                header("Location: emp.php");
            } catch (PDOException $e) {
                echo "Error: ".$e->getMessage();
                exit();
            }
        } else {
            echo "Sorry, there was an error uploading your file.";
            exit();
        }
    }
}
if(isset($_POST["delete_emp"])) {
    $empaccountId = filter_input(INPUT_POST, "delete_emp", FILTER_VALIDATE_INT);

    $query = $pdo->prepare("DELETE FROM tbempaccount WHERE empaccountId = :empaccountId");
    if($query->execute([':empaccountId' => $empaccountId])) {
        header("Location: $requestUri");
    } else {
        echo "Error deleting emp.";
    }
}

if(isset($_POST["edit_emp"])) {
    $edit_empaccountId = filter_input(INPUT_POST, "edit_empaccountId", FILTER_VALIDATE_INT);
    $edit_emp_password = htmlspecialchars($_POST["edit_emp_password"], ENT_QUOTES, "UTF-8");
    $edit_emp_email = htmlspecialchars($_POST["edit_emp_email"], ENT_QUOTES, "UTF-8");
    $edit_department_id = $_POST["edit_department_id"];

    $query = $pdo->prepare("UPDATE tbempaccount SET emp_password = :emp_password, emp_email = :emp_email, department_id = :department_id WHERE empaccountId = :empaccountId");
    if($query->execute([':emp_password' => $edit_emp_password, ':emp_email' => $edit_emp_email, ':department_id' => $edit_department_id, ':empaccountId' => $edit_empaccountId])) {
        header("Location: $requestUri");
        exit();
    } else {
        echo "Error editing emp.";
    }
}
$limit = isset($_GET['limit']) ? $_GET['limit'] : 10;
$page = isset($_GET['page']) ? $_GET['page'] : 1;
$offset = ($page - 1) * $limit;

$searchTerm = isset($_GET['search']) ? $_GET['search'] : '';

try {
    $query = $pdo->prepare("SELECT e.empaccountId, e.emp_password, e.emp_email, e.department_id, e.emp_profile, i.firstname, i.lastname
                           FROM tbempaccount e
                           JOIN tbempinfo i ON e.empid = i.empid
                           WHERE e.emp_email LIKE :searchTerm LIMIT :limit OFFSET :offset");
    $query->bindValue(':searchTerm', '%'.$searchTerm.'%', PDO::PARAM_STR);
    $query->bindValue(':limit', $limit, PDO::PARAM_INT);
    $query->bindValue(':offset', $offset, PDO::PARAM_INT);

    if(!$query->execute()) {
        throw new Exception("Query failed: ".implode(" ", $query->errorInfo()));
    }

    $rows = $query->fetchAll(PDO::FETCH_ASSOC);
    $paginationQuery = $pdo->prepare("SELECT COUNT(*) AS total FROM tbempaccount WHERE emp_email LIKE :searchTerm");
    $paginationQuery->bindValue(':searchTerm', '%'.$searchTerm.'%', PDO::PARAM_STR);
} catch (Exception $ex) {
    echo "Error: ".$ex->getMessage();
    die();
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <link rel="stylesheet" href="../../styles/rso.css">
    <title>emp</title>
</head>

<body>

    <?php include '../../components/adminHeader.php'; ?>

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
                <button type="button" onclick="document.getElementById('addModal').showModal()">Add emp <img
                        src='../../images/plus.png' alt='Image' class="icon" /> </button>
            </div>
        </section>

        <section class="tableContainer">
            <?php include '../../components/table.component.php';

            $head = array('ID', 'Password', 'Email', 'Department', 'Profile', 'Actions');
            $body = array();

            foreach($rows as $row) {
                $empaccountId = $row["empaccountId"];
                $emp_password = $row["emp_password"];
                $emp_email = $row["emp_email"];
                $department_id = $row["department_id"];
                $emp_profile = $row["emp_profile"];
                $firstname = ucwords($row["firstname"]);
                $lastname = ucwords($row["lastname"]);

                $department_name = '';

                $departmentQuery = $pdo->prepare("SELECT department_name FROM tb_department WHERE department_id = :department_id");
                $departmentQuery->execute([':department_id' => $department_id]);
                if($deptRow = $departmentQuery->fetch()) {
                    $department_name = $deptRow['department_name'];
                }

                $actions = '<button type="button" onclick="editemp('.$empaccountId.', \''.$emp_password.'\', \''.$emp_email.'\', \''.$department_id.'\')">Edit</button> <button type="button" onclick="showDeleteModal('.$empaccountId.')">Delete</button>';
                $body[] = array($empaccountId, $emp_password, $emp_email, $department_name, '<img src="../../images/profiles/'.$emp_profile.'" alt="Profile Image" class="profile-img">', $actions);
            }
            createTable($head, $body);
            ?>

            <dialog id="addModal" class="modal">
                <div class="modal-content">
                    <button class="close" onclick="resetAddModal(true)">&times;</button>

                    <h2>CREATE emp ACCOUNT</h2>
                    <form method="POST" action="" enctype="multipart/form-data">
                        <div class="error-container">Email Already Taken</div>

                        <label for="empid">Employee ID:</label>
                        <select id="empid" name="empid" required>
                            <option value="">Select an Employee ID</option>
                            <?php
                            $empInfoQuery = $pdo->prepare("SELECT empid, lastname, firstname 
                                   FROM tbempinfo 
                                   WHERE empid NOT IN (SELECT empid FROM tbempaccount)");
                            $empInfoQuery->execute();

                            while($empInfoRow = $empInfoQuery->fetch()) {
                                $fullName = $empInfoRow['lastname'].', '.$empInfoRow['firstname'];
                                echo "<option value='".$empInfoRow['empid']."'>".htmlspecialchars($fullName, ENT_QUOTES, 'UTF-8')."</option>";
                            }
                            ?>
                        </select>

                        <label for="emp-password">Password:</label>
                        <input type="password" id="emp-password" name="emp_password" required>
                        <label for="emp-email">Email:</label>
                        <input type="email" id="emp-email" name="email" required>
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
                        <label for="role">Role:</label>
                        <select id="role" name="role_id" required>
                            <option value="">Select a Role</option>
                            <?php
                            $roleQuery = $pdo->prepare("SELECT role_id, role_name FROM tb_roles");
                            $roleQuery->execute();

                            while($roleRow = $roleQuery->fetch()) {
                                echo "<option value='".$roleRow['role_id']."'>".$roleRow['role_name']."</option>";
                            }
                            ?>
                        </select>
                        <label for="fileToUpload">Profile Image:</label>
                        <input type="file" name="fileToUpload" id="fileToUpload" accept=".jpg, .jpeg, .png" required>
                        <button type="submit" name="add_emp">Create emp Account</button>
                    </form>
                </div>
            </dialog>

            <dialog id="editModal" class="modal">
                <div class="modal-content">
                    <button class="close" onclick="resetAddModal(true)">&times;</button>

                    <h2>EDIT emp ACCOUNT</h2>
                    <div class="error-container2">Email Already Taken</div>

                    <form method="POST" action="">
                        <input type="hidden" id="edit-emp-id" name="edit_empaccountId">
                        <label for="edit-emp-password">Password:</label>

                        <div class='passcont'>
                            <input type="password" id="edit-emp-password" name="edit_emp_password">
                            <img src='../../images/view.png' alt='Show Password' class="icon"
                                onclick="togglePasswordVisibility('edit-emp-password')" />
                            <img src='../../images/hide.png' alt='Hide Password' class="icon hide"
                                onclick="togglePasswordVisibility('edit-emp-password')" />
                        </div>
                        <label for="edit-email">Email:</label>
                        <input type="email" id="edit-email" name="edit_emp_email">
                        <input type="hidden" id="original-email" value="<?php echo $originalEmail; ?>">

                        <label for="edit-department">Department:</label>
                        <select id="edit-department" name="edit_department_id">
                            <?php
                            $departmentQuery = $pdo->prepare("SELECT department_id, department_name FROM tb_department");
                            $departmentQuery->execute();

                            while($deptRow = $departmentQuery->fetch()) {
                                $selected = ($deptRow['department_id'] == $edit_department_id) ? "selected" : "";
                                echo "<option value='".$deptRow['department_id']."' $selected>".$deptRow['department_name']."</option>";
                            }
                            ?>
                        </select>
                        <button type="submit" name="edit_emp">Save</button>
                    </form>
                </div>
            </dialog>

            <dialog id="deleteModal" class="modal">
                <div class="modal-content">
                    <button class="close" onclick="closeModal('deleteModal')">&times;</button>
                    <h2>DELETE emp ACCOUNT</h2>
                    <p>Are you sure you want to delete this emp?</p>
                    <div class="clearfix">
                        <button type="button" class="cancelbtn" onclick="closeModal('deleteModal')">Cancel</button>
                        <button type="button" class="deletebtn" onclick="deleteemp()">Delete</button>
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

    $requestUri = $_SERVER['REQUEST_URI'];
    ?>
    <script>
        const base_url = "<?php echo htmlspecialchars($requestUri, ENT_QUOTES, 'UTF-8'); ?>";
        const emailExistenceCheck = <?php echo json_encode(array_column($rows, 'emp_email')); ?>;
    </script>
    <script src="../../script/rso.js"></script>
</body>

</html>