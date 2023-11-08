<?php
$conn = new mysqli('localhost', 'root', '', 'db_ba3101'); 
if ($conn->connect_error) {
    die('Connection failed: ' . $conn->connect_error);
}

if (isset($_POST['event_id'])) {
    $event_id = $_POST['event_id'];

    $sql = "DELETE FROM tb_event WHERE event_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('i', $event_id);

    if ($stmt->execute()) {
        header("location:event_rso.php");
    } else {
        echo "Event deletion failed.";
    }
}
$stmt->close();
$conn->close(); 
?>