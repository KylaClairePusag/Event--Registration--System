<?php

// Database connection setup
include '../../config/config.php';

// Get the current request URI
$requestUri = $_SERVER['REQUEST_URI'];

// Fetch the logged-in student's data
$loggedInfacultyEmail = $_SESSION['faculty_email'];

$facultyQuery = $pdo->prepare("SELECT * FROM tb_faculty WHERE faculty_email = :faculty_email");
if ($facultyQuery->execute([':faculty_email' => $loggedInfacultyEmail])) {
    $faculty = $facultyQuery->fetch(PDO::FETCH_ASSOC);

    if (!$faculty || !array_key_exists('faculty_email', $faculty)) {
        echo "Error fetching faculty data.";
        exit();
    }
} else {
    echo "Error fetching faculty data.";
    exit();
}

if (isset($_POST["edit_faculty"])) {
    $edit_faculty_name = htmlspecialchars($_POST["edit_faculty_name"], ENT_QUOTES, "UTF-8");
    $edit_faculty_position = htmlspecialchars($_POST["edit_faculty_position"], ENT_QUOTES, "UTF-8");
    $edit_faculty_password = htmlspecialchars($_POST["edit_faculty_password"]);

    $newProfilePicture = !empty($_FILES["edit_faculty_profile"]["name"]);


    $edit_faculty_name = ($edit_faculty_name === '') ? $faculty['faculty_name'] : $edit_faculty_name;
    $edit_faculty_position = ($edit_faculty_position === '') ? $faculty['faculty_position'] : $edit_faculty_position;
    $edit_faculty_password = ($edit_faculty_password === '') ? $facultyt['faculty_password'] : $edit_faculty_password;

    // Check if the data is different from the current data
    if ($edit_faculty_name !== $faculty['faculty_name'] || $edit_faculty_position !== $faculty['faculty_position '] || $edit_faculty_password !== $faculty['faculty_password'] || $newProfilePicture) {
        if ($newProfilePicture) {
            // Image upload code
            $target_dir = "../../images/profiles/";
            $original_filename = basename($_FILES["edit_faculty_profile"]["name"]);
            $uploadOk = 1;
            $imageFileType = strtolower(pathinfo($original_filename, PATHINFO_EXTENSION));

            $unique_filename = uniqid() . "_" . $edit_faculty_name . "_" . $edit_faculty_position . "_" . time() . "." . $imageFileType;
            $target_file = $target_dir . $unique_filename;

            // Check if the file is a valid image
            $check = getimagesize($_FILES["edit_faculty_profile"]["tmp_name"]);
            if ($check !== false) {
                $uploadOk = 1;
            } else {
                $uploadOk = 0;
            }

            // Check file size (500 KB limit)
            if ($_FILES["edit_faculty_profile"]["size"] > 500000) {
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
                if (move_uploaded_file($_FILES["edit_faculty_profile"]["tmp_name"], $target_file)) {
                    // Update data in the database with the new profile picture filename
                    $filename = "images/profiles/" . basename($target_file);
                    $query = $pdo->prepare("UPDATE tb_faculty SET faculty_name = :faculty_name, faculty_position = :faculty_position, faculty_password = :faculty_password, faculty_profile = :faculty_profile WHERE faculty_email = :faculty_email");
                    if ($query->execute([':faculty_name' => $edit_faculty_name, ':faculty_position' => $edit_faculty_position, ':faculty_password' => $edit_faculty_password, ':faculty_profile' => $filename, ':faculty_email' => $loggedInfacultyEmail])) {
                        header("Location: $requestUri");
                        exit();
                    } else {
                        echo "Error editing faculty.";
                    }
                } else {
                    echo "Sorry, there was an error uploading your file.";
                }
            }
        } else {
            $query = $pdo->prepare("UPDATE tb_faculty SET faculty_name = :faculty_name, faculty_position = :faculty_position, faculty_password = :faculty_password WHERE faculty_email = :faculty_email");
            if ($query->execute([':faculty_name' => $edit_faculty_name, ':faculty_position' => $edit_faculty_position, ':faculty_password' => $edit_facultypassword, ':faculty_email' => $loggedInfacultyEmail])) {
                header("Location: $requestUri");
                exit();
            } else {
                echo "Error editing faculty.";
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
            border: 2px solid #848484;
            display: flex;
            align-items: center;
            flex-wrap: nowrap;

        }

    #edit-faculty-password,
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
    <?php include '../../components/facultyHeader.php'; ?>

    <main>
        <h2>ACCOUNT SETTINGS</h2>

        <form method="POST" action="" enctype="multipart/form-data">
            <label for="edit-faculty-name">Name:</label>
            <input type="text" id="edit-faculty-name" name="edit_faculty_name"><br>
            <label for="edit-faculty-position">Position:</label>
            <input type="text" id="edit-faculty-position" name="edit_faculty_position"><br>

            <label for="edit-faculty-password">New Password:</label>
            <div class='passcont'>
                <input type="password" id="edit-faculty-password" name="edit_faculty_password">
                <img src='../../images/view.png' alt='Show Password' class="icon"
                    onclick="togglePasswordVisibility('edit-faculty-password')" />
                <img src='../../images/hide.png' alt='Hide Password' class="icon hide"
                    onclick="togglePasswordVisibility('edit-faculty-password')" />
            </div>

            <label for="edit_faculty_profile">New Profile Picture:</label>
            <input type="file" name="edit_faculty_profile" id="edit_faculty_profile" accept="image/*"><br>

            <div class="button-container">
                <button type="submit" name="edit_faculty">Save</button>
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
