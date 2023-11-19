<?php

$conn = new mysqli('localhost','root','','db_ba3101');
if ($conn->connect_error) {
    die('Connection failed'. $conn->connect_error);
}


    $event = $_POST['event_name'];
    $detail = $_POST['details'];
    $date = date('Y-m-d',strtotime($_POST['date']));
    $dept = $_POST['dept_name'];

    
    $stmt = $conn->prepare('insert into tb_event(event_title,event_detail,event_date,department_id) values(?,?,?,?)');
    $stmt->bind_param('ssss', $event, $detail, $date, $dept);   
    $stmt->execute();
    echo"<script>alert('Added Successfully!');</script>";
    $stmt->close();
    $conn->close();
    header("location:event_rso.php");
?>