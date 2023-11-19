<?php
        $conn = new mysqli('localhost','root', '', 'db_ba3101');
        if ($conn->connect_error) {
            die('Connection Failed: '. $conn->connect_error);}  
            
            
                if (isset($_GET['event_id'])) {
        $event_id = $_GET['event_id'];
        $sql = "SELECT * FROM tb_event e JOIN tb_department d ON e.department_id = d.department_id WHERE e.event_id = $event_id";
        $result = $conn->query($sql);
        
        while ($row = $result->fetch_assoc()) { 
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $row['event_title'] ?></title>
</head>

<body>


    <h1><?php echo $row['event_title'] ?></h1>
    <p>Hosted by:<?php echo $row['department_name'] ?> Department</p> <br>
    <p>Details: <br> <?php echo $row['event_detail'] ?></p> <br>
    <p>When: <?php echo $row['event_date'] ?></p>



    <div class="reg-btn">
        <form action="register.php" method="POST">
            <input type="hidden" name="event_id" value="<?php echo $row['event_id']?>">
            <button type="submit" name="event_attendance">Interested</button>
        </form>
    </div>


    <?php
            }
        }
    ?>
</body>

</html>