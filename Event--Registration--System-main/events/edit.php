<?php

$conn = new mysqli('localhost','root','','db_ba3101');
if ($conn->connect_error) {
    die('Connection failed'. $conn->connect_error);
}
    
    $event = $_POST['event_name'];
    $detail = $_POST['details'];
    $date = date('Y-m-d',strtotime($_POST['date']));
    $id = $_POST['event_id'];
    $dept = $_POST['dept_name'];


    $stmt = $conn->prepare("UPDATE `tb_event` SET `event_title`=?,`event_detail`=?,`event_date`=?,`department_id`=? WHERE `event_id`=?");
    $stmt->bind_param('ssssi', $event, $detail, $date, $dept, $id);
    $stmt->execute();
    echo"<script>alert('Updated Successfully!');</script>";
    $stmt->close();
    $conn->close();
    header("location:event_rso.php");
?>