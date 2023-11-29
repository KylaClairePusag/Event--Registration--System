<?php
include '../../config/config.php';

if($_SERVER['REQUEST_METHOD'] === 'POST') {
    if(isset($_POST["add_student"])) {
        $studid = $_POST["studid"];
        $student_email = $_POST["email"];
        $student_password = $_POST["student_password"];
        $department_id = $_POST["department_id"];
        $target_dir = "../../images/profiles/";
        $original_filename = basename($_FILES["fileToUpload"]["name"]);
        $uploadOk = 1;
        $imageFileType = strtolower(pathinfo($original_filename, PATHINFO_EXTENSION));
        $unique_filename = uniqid()."_".$student_email."_".time().".".$imageFileType;
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
                $sql_account = "INSERT INTO tbstudentaccount (studid, student_email, student_password, department_id, student_profile) VALUES (?, ?, ?, ?, ?)";
                $stmt_account = $pdo->prepare($sql_account);
                $stmt_account->execute([$studid, $student_email, $student_password, $department_id, $unique_filename]);
                header("Location: student.php");
            } catch (PDOException $e) {
                echo "Error: ".$e->getMessage();
                exit();
            }
        } else {
            echo "Sorry, there was an error uploading your file.";
            exit();
        }
    } elseif(isset($_POST["add_new_student"])) {
        $firstname = $_POST["firstname"];
        $lastname = $_POST["lastname"];
        $course = $_POST["course"];
        $student_email = $_POST["email"];
        $student_password = $_POST["student_password"];
        $department_id = $_POST["department_id"];
        $target_dir = "../../images/profiles/";
        $original_filename = basename($_FILES["fileToUpload"]["name"]);
        $uploadOk = 1;
        $imageFileType = strtolower(pathinfo($original_filename, PATHINFO_EXTENSION));
        $unique_filename = uniqid()."_".$student_email."_".time().".".$imageFileType;
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
                $sql_info = "INSERT INTO tbstudinfo (firstname, lastname, course) VALUES (?, ?, ?)";
                $stmt_info = $pdo->prepare($sql_info);
                $stmt_info->execute([$firstname, $lastname, $course]);

                $studid = $pdo->lastInsertId();

                $sql_account = "INSERT INTO tbstudentaccount (studid, student_email, student_password, department_id, student_profile) VALUES (?, ?, ?, ?, ?)";
                $stmt_account = $pdo->prepare($sql_account);
                $stmt_account->execute([$studid, $student_email, $student_password, $department_id, $path]);

                header("Location: student.php");
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
if(isset($_POST["delete_student"])) {
    $studid = filter_input(INPUT_POST, "delete_student", FILTER_VALIDATE_INT);

    $query = $pdo->prepare("DELETE FROM tbstudentaccount WHERE studid = :studid");
    if($query->execute([':studid' => $studid])) {
        header("Location: $requestUri");
    } else {
        echo "Error deleting student.";
    }
}


if(isset($_POST["edit_student"])) {
    $edit_studid = filter_input(INPUT_POST, "edit_studid", FILTER_VALIDATE_INT);
    $edit_student_password = htmlspecialchars($_POST["edit_student_password"], ENT_QUOTES, "UTF-8");
    $edit_student_email = htmlspecialchars($_POST["edit_student_email"], ENT_QUOTES, "UTF-8");
    $edit_department_id = $_POST["edit_department_id"];
    $edit_firstname = $_POST["edit_firstname"];
    $edit_lastname = $_POST["edit_lastname"];
    $edit_course = $_POST["edit_course"];

    try {
        // Update student information
        $sql_info = "UPDATE tbstudinfo SET firstname = ?, lastname = ?, course = ? WHERE studid IN (SELECT studid FROM tbstudentaccount WHERE studid = ?)";
        $stmt_info = $pdo->prepare($sql_info);
        $stmt_info->execute([$edit_firstname, $edit_lastname, $edit_course, $edit_studid]);

        // Update student account
        $sql_account = "UPDATE tbstudentaccount SET student_password = ?, student_email = ?, department_id = ? WHERE studid = ?";
        $stmt_account = $pdo->prepare($sql_account);
        $stmt_account->execute([$edit_student_password, $edit_student_email, $edit_department_id, $edit_studid]);

        header("Location: student.php");
        exit();
    } catch (PDOException $e) {
        echo "Error: ".$e->getMessage();
        exit();
    }
}

// Handle search and filter
$searchTerm = isset($_GET['search']) ? $_GET['search'] : '';
$selectedDepartment = isset($_GET['departmentFilter']) ? $_GET['departmentFilter'] : '';

// Define $limit and $offset
$limit = 10; // You can adjust this value based on your requirements
$page = isset($_GET['page']) ? $_GET['page'] : 1;
$offset = ($page - 1) * $limit;

try {
    $query = $pdo->prepare("SELECT e.studid, e.student_password, e.student_email, e.department_id, e.student_profile, i.firstname, i.lastname, i.course
                           FROM tbstudentaccount e
                           JOIN tbstudinfo i ON e.studid = i.studid
                           WHERE (e.student_email LIKE :searchTerm) AND (:selectedDepartment = '' OR e.department_id = :selectedDepartment)
                           LIMIT :limit OFFSET :offset");

    $query->bindValue(':searchTerm', '%'.$searchTerm.'%', PDO::PARAM_STR);
    $query->bindValue(':limit', $limit, PDO::PARAM_INT);
    $query->bindValue(':offset', $offset, PDO::PARAM_INT);
    $query->bindValue(':selectedDepartment', $selectedDepartment, PDO::PARAM_STR);

    if(!$query->execute()) {
        throw new Exception('Query failed: '.implode(' ', $query->errorInfo()));
    }

    $rows = $query->fetchAll(PDO::FETCH_ASSOC);
    $paginationQuery = $pdo->prepare("SELECT COUNT(*) AS total FROM tbstudentaccount WHERE (student_email LIKE :searchTerm) AND (:selectedDepartment = '' OR department_id = :selectedDepartment)");
    $paginationQuery->bindValue(':searchTerm', '%'.$searchTerm.'%', PDO::PARAM_STR);
    $paginationQuery->bindValue(':selectedDepartment', $selectedDepartment, PDO::PARAM_STR);
} catch (Exception $ex) {
    echo 'Error: '.$ex->getMessage();
    die();
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <link rel="stylesheet" href="../../styles/rso.css">
    <title>student</title>
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
                <?php include '../../components/limit.php'; ?>
                <button type="button" onclick="document.getElementById('addModal').showModal()">Add existing student
                    <img src='../../images/plus.png' alt='Image' class="icon" /> </button>
                <button type="button" onclick="document.getElementById('addNewEmpModal').showModal()">Add new student
                    <img src='../../images/plus.png' alt='Image' class="icon" /> </button>

            </div>
            <!-- Add this above the search bar -->


        </section>

        <section class="tableContainer">
            <?php include '../../components/table.component.php';
            // Main file
            
            $head = array('ID', 'Profile', 'Name', 'Password', 'Email', 'Department', 'Course', 'Actions');
            $body = array();

            foreach($rows as $row) {
                $studid = $row["studid"];
                $student_password = $row["student_password"];
                $student_email = $row["student_email"];
                $department_id = $row["department_id"];
                $student_profile = $row["student_profile"];
                $firstname = ucwords($row["firstname"]);
                $lastname = ucwords($row["lastname"]);
                $course = ucwords($row["course"]);

                $department_name = '';

                $departmentQuery = $pdo->prepare("SELECT department_name FROM tb_department WHERE department_id = :department_id");
                $departmentQuery->execute([':department_id' => $department_id]);
                if($deptRow = $departmentQuery->fetch()) {
                    $department_name = $deptRow['department_name'];
                }

                $actions = '<button type="button" onclick="editstudent(\''.$studid.'\', \''.$student_password.'\', \''.$student_email.'\', \''.$department_id.'\', \''.$firstname.'\', \''.$course.'\', \''.$lastname.'\')">Edit</button> <button type="button" onclick="showDeleteModal('.$studid.')">Delete</button>';


                // Concatenate first and last names
                $name = $firstname.' '.$lastname;

                // Add row to the $body array
                $body[] = array($studid, '<img src="../../images/profiles/'.$student_profile.'" alt="Profile" class="profile-img" style="width: 30px; height: 30px; border-radius: 50px">', $name, $student_password, $course, $student_email, $department_name, $actions);
            }

            createTable($head, $body);
            ?>


            <dialog id="addModal" class="modal">
                <div class="modal-content">
                    <button class="close" onclick="resetAddModal(true)">&times;</button>

                    <h2>CREATE student ACCOUNT</h2>
                    <form method="POST" action="" enctype="multipart/form-data">
                        <div class="error-container">Email Already Taken</div>

                        <label for="studid">Student ID:</label>
                        <select id="studid" name="studid" required>
                            <option value="">Select an Employee ID</option>
                            <?php
                            $studentInfoQuery = $pdo->prepare("SELECT studid, lastname, firstname 
                                   FROM tbstudinfo 
                                   WHERE studid NOT IN (SELECT studid FROM tbstudentaccount)");
                            $studentInfoQuery->execute();

                            while($studentInfoRow = $studentInfoQuery->fetch()) {
                                $fullName = $studentInfoRow['lastname'].', '.$studentInfoRow['firstname'];
                                echo "<option value='".$studentInfoRow['studid']."'>".htmlspecialchars($fullName, ENT_QUOTES, 'UTF-8')."</option>";
                            }
                            ?>
                        </select>

                        <label for="student-password">Password:</label>
                        <input type="password" id="student-password" name="student_password" required>
                        <label for="student-email">Email:</label>
                        <input type="email" id="student-email" name="email" required>
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

                        <label for="fileToUpload">Profile Image:</label>
                        <input type="file" name="fileToUpload" id="fileToUpload" accept=".jpg, .jpeg, .png" required>
                        <button type="submit" name="add_student">Create student Account</button>
                    </form>
                </div>
            </dialog>
            <dialog id="addNewEmpModal" class="modal">
                <div class="modal-content">
                    <button class="close" onclick="resetAddModal(true)">&times;</button>

                    <h2>CREATE student ACCOUNT</h2>
                    <form method="POST" action="" enctype="multipart/form-data">
                        <div class="error-container3">Email Already Taken</div>

                        <label for="firstname">First Name:</label>
                        <input type="text" id="firstname" name="firstname" required>

                        <label for="lastname">Last Name:</label>
                        <input type="text" id="lastname" name="lastname" required>

                        <label for="student-password">Password:</label>
                        <input type="password" id="student-password" name="student_password" required>

                        <label for="student-course">Course:</label>
                        <input type="text" id="course" name="course" required>


                        <label for="student-email">Email:</label>
                        <input type="email" id="student-emails" name="email" required>

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



                        <label for="fileToUpload">Profile Image:</label>
                        <input type="file" name="fileToUpload" id="fileToUpload" accept=".jpg, .jpeg, .png" required>

                        <button type="submit" name="add_new_student">Create student Account</button>
                    </form>
                </div>
            </dialog>

            <dialog id="editModal" class="modal">
                <div class="modal-content">
                    <button class="close" onclick="resetAddModal(true)">&times;</button>

                    <h2>EDIT student ACCOUNT</h2>
                    <div class="error-container2">Email Already Taken</div>

                    <form method="POST" action="">
                        <input type="hidden" id="edit-student-id" name="edit_studid">

                        <label for="edit-firstname">First Name:</label>
                        <input type="text" id="edit-firstname" name="edit_firstname" required>

                        <label for="edit-lastname">Last Name:</label>
                        <input type="text" id="edit-lastname" name="edit_lastname" required>

                        <label for="edit-course">course:</label>
                        <input type="text" id="edit-course" name="edit_course" required>

                        <label for="edit-student-password">Password:</label>
                        <div class='passcont'>
                            <input type="password" id="edit-student-password" name="edit_student_password">
                            <img src='../../images/view.png' alt='Show Password' class="icon"
                                onclick="togglePasswordVisibility('edit-student-password')" />
                            <img src='../../images/hide.png' alt='Hide Password' class="icon hide"
                                onclick="togglePasswordVisibility('edit-student-password')" />
                        </div>
                        <label for="edit-email">Email:</label>
                        <input type="email" id="edit-email" name="edit_student_email">
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
                        <button type="submit" name="edit_student">Save</button>
                    </form>
                </div>
            </dialog>



            <dialog id="deleteModal" class="modal">
                <div class="modal-content">
                    <button class="close" onclick="closeModal('deleteModal')">&times;</button>
                    <h2>DELETE student ACCOUNT</h2>
                    <p>Are you sure you want to delete this student account?</p>
                    <div class="clearfix">
                        <button type="button" class="cancelbtn" onclick="closeModal('deleteModal')">Cancel</button>
                        <button type="button" class="deletebtn" onclick="deletestudent()">Delete</button>
                    </div>
                </div>
            </dialog>
        </section>
        <section class="paginationCont">
            <?php include '../../components/pagination.php';
            generatePaginationLinks($searchTerm, $limit, $paginationQuery, $selectedDepartment, null);
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
        const emailExistenceCheck = <?php echo json_encode(array_column($rows, 'student_email')); ?>;
    </script>
    <script src="../../script/student.js"></script>
</body>

</html>