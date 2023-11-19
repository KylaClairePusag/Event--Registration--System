<?php
$target_dir = "images/";
$target_file = $target_dir . basename($_FILES["fileToUpload"]["name"]);
$uploadOk = 1;
$imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
$uploadMessage = ""; // Variable to store the upload result message

// Check if the form was submitted
if (isset($_POST["submit"])) {
    // Check if the file is a valid image
    $check = getimagesize($_FILES["fileToUpload"]["tmp_name"]);
    if ($check !== false) {
        $uploadMessage .= "File is an image - " . $check["mime"] . ". ";
        $uploadOk = 1;
    } else {
        $uploadMessage .= "File is not an image. ";
        $uploadOk = 0;
    }

    // Check if file already exists
    if (file_exists($target_file)) {
        $uploadMessage .= "Sorry, file already exists. ";
        $uploadOk = 0;
    }

    // Check file size (500 KB limit)
    if ($_FILES["fileToUpload"]["size"] > 500000) {
        $uploadMessage .= "Sorry, your file is too large. ";
        $uploadOk = 0;
    }

    // Allow only certain file formats
    $allowedFormats = ["jpg", "jpeg", "png", "gif"];
    if (!in_array($imageFileType, $allowedFormats)) {
        $uploadMessage .= "Sorry, only JPG, JPEG, PNG & GIF files are allowed. ";
        $uploadOk = 0;
    }

    // Check if $uploadOk is set to 0 by an error
    if ($uploadOk == 0) {
        $uploadMessage .= "Sorry, your file was not uploaded.";
    } else {
        // Try to upload the file
        if (move_uploaded_file($_FILES["fileToUpload"]["tmp_name"], $target_file)) {
            $uploadMessage .= "The file " . htmlspecialchars(basename($_FILES["fileToUpload"]["name"])) . " has been uploaded.";
        } else {
            $uploadMessage .= "Sorry, there was an error uploading your file.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Image Upload</title>
</head>

<body>
    <?php
    $target_dir = "images/";
    $target_file = $target_dir . basename($_FILES["fileToUpload"]["name"]);

    if (move_uploaded_file($_FILES["fileToUpload"]["tmp_name"], $target_file)) {
        echo "File has been uploaded.";
    } else {
        echo "Sorry, there was an error uploading your file.";
    }
    ?>

    <?php
    // Display the upload result message
    echo "<p>{$uploadMessage}</p>";

    // Display the uploaded image if it was successful
    if ($uploadOk == 1) {
        echo '<img src="' . $target_file . '" alt="Uploaded Image">';
    }
    ?>

    <!-- Your form goes here -->
    <form action="" method="post" enctype="multipart/form-data">
        <input type="file" name="fileToUpload" id="fileToUpload">
        <input type="submit" value="Upload Image" name="submit">
    </form>
</body>

</html>