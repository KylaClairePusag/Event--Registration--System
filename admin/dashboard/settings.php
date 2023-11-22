<?php
session_start();
// Database connection setup
include '../../config/config.php';

// Get the current request URI
$requestUri = $_SERVER['REQUEST_URI'];

// Fetch the logged-in admin's data
$loggedInAdminEmail = $_SESSION['admin_email'];

$adminQuery = $pdo->prepare("SELECT * FROM tb_admin WHERE admin_email = :admin_email");
if ($adminQuery->execute([':admin_email' => $loggedInAdminEmail])) {
    $admin = $adminQuery->fetch(PDO::FETCH_ASSOC);

    if (!$admin || !array_key_exists('admin_email', $admin)) {
        echo "Error fetching admin data.";
        exit();
    }
} else {
    echo "Error fetching admin data.";
    exit();
}

// Edit Admin
if (isset($_POST["edit_admin"])) {
    $edit_admin_name = htmlspecialchars($_POST["edit_admin_name"], ENT_QUOTES, "UTF-8");
    $edit_admin_password = htmlspecialchars($_POST["edit_admin_password"], ENT_QUOTES, "UTF-8");

    // Check if a new profile picture is provided
    $newProfilePicture = !empty($_FILES["edit_admin_profile"]["name"]);

    // Compare with current values and use current values if submitted values are empty
    $edit_admin_name = ($edit_admin_name === '') ? $admin['admin_name'] : $edit_admin_name;
    $edit_admin_password = ($edit_admin_password === '') ? $admin['admin_password'] : $edit_admin_password;

    // Check if the data is different from the current data
    if ($edit_admin_name !== $admin['admin_name'] || $edit_admin_password !== $admin['admin_password'] || $newProfilePicture) {
        if ($newProfilePicture) {
            // Image upload code
            $target_dir = "../../images/profiles/";
            $original_filename = basename($_FILES["edit_admin_profile"]["name"]);
            $uploadOk = 1;
            $imageFileType = strtolower(pathinfo($original_filename, PATHINFO_EXTENSION));

            $unique_filename = uniqid() . "_" . $edit_admin_name . "_" . time() . "." . $imageFileType;
            $target_file = $target_dir . $unique_filename;

            // Check if the file is a valid image
            $check = getimagesize($_FILES["edit_admin_profile"]["tmp_name"]);
            if ($check !== false) {
                $uploadOk = 1;
            } else {
                $uploadOk = 0;
            }

            // Check file size (500 KB limit)
            if ($_FILES["edit_admin_profile"]["size"] > 500000) {
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
                if (move_uploaded_file($_FILES["edit_admin_profile"]["tmp_name"], $target_file)) {
                    // Update data in the database with the new profile picture filename
                    $query = $pdo->prepare("UPDATE tb_admin SET admin_name = :admin_name, admin_password = :admin_password, admin_profile = :admin_profile WHERE admin_email = :admin_email");
                    $filename = "images/profiles/" . basename($target_file);
                    if ($query->execute([':admin_name' => $edit_admin_name, ':admin_password' => $edit_admin_password, ':admin_profile' => $filename, ':admin_email' => $loggedInAdminEmail])) {
                        header("Location: $requestUri");
                        exit();
                    } else {
                        echo "Error editing admin.";
                    }
                } else {
                    echo "Sorry, there was an error uploading your file.";
                }
            }
        } else {

            $query = $pdo->prepare("UPDATE tb_admin SET admin_name = :admin_name, admin_password = :admin_password WHERE admin_email = :admin_email");
            if ($query->execute([':admin_name' => $edit_admin_name, ':admin_password' => $edit_admin_password, ':admin_email' => $loggedInAdminEmail])) {
                $_SESSION['admin_name'] = $edit_admin_name;
                header("Location: $requestUri");
                exit();
            } else {
                echo "Error editing admin.";
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

        #edit-admin-password,
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
    <?php include '../../components/adminHeader.php'; ?>

    <main>
        <h2>ACCOUNT SETTINGS</h2>

        <form method="POST" action="" enctype="multipart/form-data">
            <div class='name'>
            <label for="edit-admin-name">New Name:</label>
            <input type="text" id="edit-admin-name" name="edit_admin_name"><br>
            </div>
            <label for="edit-admin-password">New Password:</label>
            <div class='passcont'>
                <input type="password" id="edit-admin-password" name="edit_admin_password">
                <img src='../../images/view.png' alt='Show Password' class="icon"
                    onclick="togglePasswordVisibility('edit-admin-password')" />
                <img src='../../images/hide.png' alt='Hide Password' class="icon hide"
                    onclick="togglePasswordVisibility('edit-admin-password')" />
            </div>

            <label for="edit_admin_profile">New Profile Picture:</label>
            <input type="file" name="edit_admin_profile" id="edit_admin_profile" accept="image/*"><br>

            <div class="button-container">
                <button type="submit" name="edit_admin">Save</button>
            </div>
        </form>
    </main>

    <script>
        const base_url = "<?php echo htmlspecialchars($requestUri, ENT_QUOTES, 'UTF-8'); ?>";

        function resetEditForm() { }

        function togglePasswordVisibility(passwordFieldId) { }
    </script>
    <script src="../../script/admin.js"></script>
</body>

</html>
