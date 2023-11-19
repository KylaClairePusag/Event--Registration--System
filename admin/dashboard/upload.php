<?php

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
    $target_dir = "../../images/profiles/";
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