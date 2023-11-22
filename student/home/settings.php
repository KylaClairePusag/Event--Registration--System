<?php

// Database connection setup
include '../../config/config.php';

// Get the current request URI
$requestUri = $_SERVER['REQUEST_URI'];

// Fetch the logged-in student's data
$loggedInstudentEmail = $_SESSION['student_email'];

$studentQuery = $pdo->prepare("SELECT * FROM tbstudinfo WHERE student_email = :student_email");
if ($studentQuery->execute([':student_email' => $loggedInstudentEmail])) {
    $student = $studentQuery->fetch(PDO::FETCH_ASSOC);

    if (!$student || !array_key_exists('student_email', $student)) {
        echo "Error fetching student data.";
        exit();
    }
} else {
    echo "Error fetching student data.";
    exit();
}

if (isset($_POST["edit_student"])) {
    $edit_student_lastname = htmlspecialchars($_POST["edit_student_lastname"], ENT_QUOTES, "UTF-8");
    $edit_student_firstname = htmlspecialchars($_POST["edit_student_firstname"], ENT_QUOTES, "UTF-8");
    $edit_student_password = htmlspecialchars($_POST["edit_student_password"]);

    $newProfilePicture = !empty($_FILES["edit_student_profile"]["name"]);


    $edit_student_lastname = ($edit_student_lastname === '') ? $student['lastname'] : $edit_student_lastname;
    $edit_student_firstname = ($edit_student_firstname === '') ? $student['firstname'] : $edit_student_firstname;
    $edit_student_password = ($edit_student_password === '') ? $student['student_password'] : $edit_student_password;

    // Check if the data is different from the current data
    if ($edit_student_lastname !== $student['lastname'] || $edit_student_firstname !== $student['firstname'] || $edit_student_password !== $student['student_password'] || $newProfilePicture) {
        if ($newProfilePicture) {
            // Image upload code
            $target_dir = "../../images/profiles/";
            $original_filename = basename($_FILES["edit_student_profile"]["name"]);
            $uploadOk = 1;
            $imageFileType = strtolower(pathinfo($original_filename, PATHINFO_EXTENSION));

            $unique_filename = uniqid() . "_" . $edit_student_lastname . "_" . $edit_student_firstname . "_" . time() . "." . $imageFileType;
            $target_file = $target_dir . $unique_filename;

            // Check if the file is a valid image
            $check = getimagesize($_FILES["edit_student_profile"]["tmp_name"]);
            if ($check !== false) {
                $uploadOk = 1;
            } else {
                $uploadOk = 0;
            }

            // Check file size (500 KB limit)
            if ($_FILES["edit_student_profile"]["size"] > 500000) {
                $uploadOk = 0;
            }

            // Allow only certain file formats
            $allowedFormats = ["jpg", "jpeg", "png", "gif"];
            if (!in_array($imageFileType, $allowedFormats)) {
                $uploadOk = 0;
            }

            if ($uploadOk == 0) {
                echo "Sorry, your file was not uploaded.";
            } else {
                if (move_uploaded_file($_FILES["edit_student_profile"]["tmp_name"], $target_file)) {
                    // Update data in the database with the new profile picture filename
                    $filename = "images/profiles/" . basename($target_file);
                    $query = $pdo->prepare("UPDATE tbstudinfo SET lastname = :lastname, firstname = :firstname, student_password = :student_password, student_profile = :student_profile WHERE student_email = :student_email");
                    if ($query->execute([':lastname' => $edit_student_lastname, ':firstname' => $edit_student_firstname, ':student_password' => $edit_student_password, ':student_profile' => $filename, ':student_email' => $loggedInstudentEmail])) {
                        header("Location: $requestUri");
                        exit();
                    } else {
                        echo "Error editing student.";
                    }
                } else {
                    echo "Sorry, there was an error uploading your file.";
                }
            }
        } else {
            $query = $pdo->prepare("UPDATE tbstudinfo SET lastname = :lastname, firstname = :firstname, student_password = :student_password WHERE student_email = :student_email");
            if ($query->execute([':lastname' => $edit_student_lastname, ':firstname' => $edit_student_firstname, ':student_password' => $edit_student_password, ':student_email' => $loggedInstudentEmail])) {
                header("Location: $requestUri");
                exit();
            } else {
                echo "Error editing student.";
            }
        }
    } else {
        header("Location: $requestUri");
        exit();
    }
}
?>

<!DOCTYPE html>
<html>

<head>
    <link rel="stylesheet" href="../../styles/rso.css">
    <style>
        .passcont {
            margin-left:120px;
            margin-top:-20px;
            width: 200px;
            border: 1px solid #848484;
            display: flex;
            align-items: center;
            flex-wrap: nowrap;

        }

    #edit-student-password,
    #edit-rso-password {
        width: 100%;
        /* Make the password input fill the available width */
        border: none;
        margin: 0;
        outline: none;
    }
    </style>
</head>

<body>
    <?php include '../../components/studentHeader.php'; ?>

    <main>
        <h2>ACCOUNT SETTINGS</h2>

        <form method="POST" action="" enctype="multipart/form-data">
            <label for="edit-student-firstname">First Name:</label>
            <input type="text" id="edit-student-firstname" name="edit_student_firstname"><br>

            <label for="edit-student-lastname">Last Name:</label>
            <input type="text" id="edit-student-lastname" name="edit_student_lastname"><br>

            <label for="edit-student-password">New Password:</label>
            <div class='passcont'>
                <input type="password" id="edit-student-password" name="edit_student_password">
                <img src='../../images/view.png' alt='Show Password' class="icon"
                    onclick="togglePasswordVisibility('edit-student-password')" />
                <img src='../../images/hide.png' alt='Hide Password' class="icon hide"
                    onclick="togglePasswordVisibility('edit-student-password')" />
            </div>

            <label for="edit_student_profile">New Profile Picture:</label>
            <input type="file" name="edit_student_profile" id="edit_student_profile" accept="image/*"><br>

            <div class="button-container">
                <button type="submit" name="edit_student">Save</button>
            </div>
        </form>
    </main>

    <script>
    const base_url = "<?php echo htmlspecialchars($requestUri, ENT_QUOTES, 'UTF-8'); ?>";

    function resetEditForm() {}

    function togglePasswordVisibility(passwordFieldId) {}
    </script>
    <script src="../../script/admin.js"></script>
</body>

</html>
