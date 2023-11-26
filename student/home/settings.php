<?php

// Database connection setup
include '../../config/config.php';

// Get the current request URI
$requestUri = $_SERVER['REQUEST_URI'];

// Fetch the logged-in student's data
$loggedInstudentEmail = $_SESSION['student_email'];

if(isset($_POST["edit_student_profile"])) {
    $newProfilePicture = !empty($_FILES["edit_student_profile"]["name"]);

    if($newProfilePicture) {
        // Image upload code
        $target_dir = "../../images/profiles/";
        $original_filename = basename($_FILES["edit_student_profile"]["name"]);
        $uploadOk = 1;
        $imageFileType = strtolower(pathinfo($original_filename, PATHINFO_EXTENSION));

        $unique_filename = uniqid()."_".time().".".$imageFileType;
        $target_file = $target_dir.$unique_filename;

        // Check if the file is a valid image
        $check = getimagesize($_FILES["edit_student_profile"]["tmp_name"]);
        if($check === false) {
            $uploadOk = 0;
        }

        // Check file size (500 KB limit)
        if($_FILES["edit_student_profile"]["size"] > 500000) {
            $uploadOk = 0;
        }

        // Allow only certain file formats
        $allowedFormats = ["jpg", "jpeg", "png", "gif"];
        if(!in_array($imageFileType, $allowedFormats)) {
            $uploadOk = 0;
        }

        if($uploadOk == 0) {
            echo "Sorry, your file was not uploaded.";
        } else {
            if(move_uploaded_file($_FILES["edit_student_profile"]["tmp_name"], $target_file)) {
                // Update data in the database with the new profile picture filename
                $filename = "images/profiles/".basename($target_file);
                $query = $pdo->prepare("UPDATE tbstudentaccount SET student_profile = :student_profile WHERE student_email = :student_email");
                if($query->execute([':student_profile' => $filename, ':student_email' => $loggedInstudentEmail])) {
                    header("Location: $requestUri");
                    exit();
                } else {
                    echo "Error updating profile picture.";
                }
            } else {
                echo "Sorry, there was an error uploading your file.";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html>

<head>
    <!-- Include your styles and scripts here -->
</head>

<body>
    <?php include '../../components/studentHeader.php'; ?>

    <main>
        <h2>CHANGE PROFILE PICTURE</h2>

        <form method="POST" action="" enctype="multipart/form-data">
            <label for="edit_student_profile">New Profile Picture:</label>
            <input type="file" name="edit_student_profile" id="edit_student_profile" accept="image/*"><br>

            <div class="button-container">
                <button type="submit" name="edit_student_profile">Upload</button>
            </div>
        </form>
    </main>

    <!-- Include your scripts here -->
</body>

</html>