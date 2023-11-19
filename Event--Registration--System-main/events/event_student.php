<?php
        $conn = new mysqli('localhost','root', '', 'db_ba3101');
        if ($conn->connect_error) {
            die('Connection Failed: '. $conn->connect_error);}       
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Event-Student</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous">
    </script>

    <style>
    .event-container {
        position: relative;
        border: 3px solid;
        padding: 10px;
        width: 300px;
        height: 270px;
        background-color: #fff;
        text-align: center;
        margin-bottom: 30px;
    }

    .event-container h2 {
        border-bottom: 1px solid;
        padding-bottom: 7px;
    }


    textarea {
        width: 100%;
    }

    .view-btn {
        position: absolute;
        display: inline-block;
        padding: 5px 15px;
        border: 1px solid;
        border-radius: 5px;
        top: 208px;
        left: 110px;

    }

    .view-btn a {
        text-decoration: none;
        color: black;
    }
    </style>
</head>

<body>
    <?php
            $sql = 'SELECT * FROM tb_event e JOIN tb_department d ON e.department_id = d.department_id';
            $result = $conn->query($sql);  

            while ($row = $result->fetch_assoc()) {
             ?>

    <div class="event-container">
        <div class="event-details">
            <h2><?php echo $row['event_title'] ?></h2>
            <p><?php echo $row['event_detail'] ?></p>
            <p><?php echo $row['event_date'] ?></p>
        </div>
        <div class="view-btn">
            <a href="event.php?event_id=<?php echo $row['event_id']; ?>">View</a>
        </div>
    </div>
    <?php
            }
    ?>


</body>

</html>