<?php
session_start();
// Database connection setup
include '../../config/config.php';

// Get the current request URI
$requestUri = $_SERVER['REQUEST_URI'];

// Fetch the logged-in rso's data
$loggedInrsoEmail = $_SESSION['rso_email'];

$rsoQuery = $pdo->prepare("SELECT * FROM tb_rso WHERE rso_email = :rso_email");
if ($rsoQuery->execute([':rso_email' => $loggedInrsoEmail])) {
    $rso = $rsoQuery->fetch(PDO::FETCH_ASSOC);

    if (!$rso || !array_key_exists('rso_email', $rso)) {
        echo "Error fetching rso data.";
        exit();
    }
} else {
    echo "Error fetching rso data.";
    exit();
}

// Edit rso
if (isset($_POST["edit_rso"])) {
    $edit_rso_name = htmlspecialchars($_POST["edit_rso_name"], ENT_QUOTES, "UTF-8");
    $edit_rso_password = htmlspecialchars($_POST["edit_rso_password"], ENT_QUOTES, "UTF-8");

    // Check if a new profile picture is provided
    $newProfilePicture = !empty($_FILES["edit_rso_profile"]["name"]);

    // Compare with current values and use current values if submitted values are empty
    $edit_rso_name = ($edit_rso_name === '') ? $rso['rso_name'] : $edit_rso_name;
    $edit_rso_password = ($edit_rso_password === '') ? $rso['rso_password'] : $edit_rso_password;

    // Check if the data is different from the current data
    if ($edit_rso_name !== $rso['rso_name'] || $edit_rso_password !== $rso['rso_password'] || $newProfilePicture) {
        if ($newProfilePicture) {
            // Image upload code
            $target_dir = "../../images/profiles/";
            $original_filename = basename($_FILES["edit_rso_profile"]["name"]);
            $uploadOk = 1;
            $imageFileType = strtolower(pathinfo($original_filename, PATHINFO_EXTENSION));

            $unique_filename = uniqid() . "_" . $edit_rso_name . "_" . time() . "." . $imageFileType;
            $target_file = $target_dir . $unique_filename;

            // Check if the file is a valid image
            $check = getimagesize($_FILES["edit_rso_profile"]["tmp_name"]);
            if ($check !== false) {
                $uploadOk = 1;
            } else {
                $uploadOk = 0;
            }

            // Check file size (500 KB limit)
            if ($_FILES["edit_rso_profile"]["size"] > 500000) {
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
                if (move_uploaded_file($_FILES["edit_rso_profile"]["tmp_name"], $target_file)) {
                    // Update data in the database with the new profile picture filename
                    $query = $pdo->prepare("UPDATE tb_rso SET rso_name = :rso_name, rso_password = :rso_password, rso_profile = :rso_profile WHERE rso_email = :rso_email");
                    $filename = "images/profiles/" . basename($target_file);
                    if ($query->execute([':rso_name' => $edit_rso_name, ':rso_password' => $edit_rso_password, ':rso_profile' => $filename, ':rso_email' => $loggedInrsoEmail])) {
                        header("Location: $requestUri");
                        exit();
                    } else {
                        echo "Error editing rso.";
                    }
                } else {
                    echo "Sorry, there was an error uploading your file.";
                }
            }
        } else {

            $query = $pdo->prepare("UPDATE tb_rso SET rso_name = :rso_name, rso_password = :rso_password WHERE rso_email = :rso_email");
            if ($query->execute([':rso_name' => $edit_rso_name, ':rso_password' => $edit_rso_password, ':rso_email' => $loggedInrsoEmail])) {
                $_SESSION['rso_name'] = $edit_rso_name;
                header("Location: $requestUri");
                exit();
            } else {
                echo "Error editing rso.";
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
    body {
        display: flex;
        flex-direction: column;
        min-height: 100vh;
    }

    header {
        flex-shrink: 0;
    }

    main {
        flex-grow: 1;
        padding: 20px;
    }

    main form {
        display: flex;
        flex-direction: column;
        align-items: left;
    }

    label {
        margin-bottom: 5px;
        display: block;
    }

    input {
        width: 100%;
        padding: 10px;
        border: 1px solid #ccc;
        margin-bottom: 10px;
    }

    .button-container {
        display: flex;
        justify-content: center;
    }

    button {
        padding: 10px 20px;
        background-color: #007bff;
        color: #fff;
        border: none;
        cursor: pointer;
    }

    .passcont {
        display: flex;
        align-items: center;
        width: 100%;
    }

    #edit-rso-password,
    #edit-rso-password {
        width: 100%;
        border: none;
        margin: 0;
        outline: none;
    }

    .icon {
        width: 16px;
        height: 16px;
        cursor: pointer;
    }

    .hide {
        display: none;
    }
    </style>
</head>

<body>
    <?php include '../../components/rsoHeader.php'; ?>

    <main>
        <h2>ACCOUNT SETTINGS</h2>

        <form method="POST" action="" enctype="multipart/form-data">
            <label for="edit-rso-name">New Name:</label>
            <input type="text" id="edit-rso-name" name="edit_rso_name"><br>

            <label for="edit-rso-password">New Password:</label>
            <div class='passcont'>
                <input type="password" id="edit-rso-password" name="edit_rso_password">
                <img src='../../images/view.png' alt='Show Password' class="icon"
                    onclick="togglePasswordVisibility('edit-rso-password')" />
                <img src='../../images/hide.png' alt='Hide Password' class="icon hide"
                    onclick="togglePasswordVisibility('edit-rso-password')" />
            </div>

            <label for="edit_rso_profile">New Profile Picture:</label>
            <input type="file" name="edit_rso_profile" id="edit_rso_profile" accept="image/*"><br>

            <div class="button-container">
                <button type="submit" name="edit_rso">Save</button>
            </div>
        </form>
    </main>

    <script>
    const base_url = "<?php echo htmlspecialchars($requestUri, ENT_QUOTES, 'UTF-8'); ?>";

    function resetEditForm() {}

    function togglePasswordVisibility(passwordFieldId) {}
    </script>
    <script src="../../script/rso.js"></script>
</body>

</html>