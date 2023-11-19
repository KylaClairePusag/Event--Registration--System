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
    <title>Event-RSO</title>
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

    .modal-content {
        align-items: center;
    }

    .event-container h2 {
        border-bottom: 1px solid;
        padding-bottom: 7px;
    }

    .action-dots {
        position: absolute;
        top: 5px;
        right: 5px;
        cursor: pointer;
        font-size: 30px;
    }

    .action-menu {
        display: none;
        position: absolute;
        top: 0px;
        right: -100px;
        background-color: #fff;
        border: 1px solid #ccc;
        border-radius: 3px;
        width: 100px;
        text-align: center;
        z-index: 1;
        padding: 12px;
    }

    <style>.modal-body {
        display: flex;
        align-items: center;
    }

    form {
        display: inline-block;
        text-align: left;
    }

    label {
        display: block;
        text-align: left;
        margin-bottom: 5px;
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
    <!-- Button trigger modal -->
    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addModal">
        Add new Event
    </button>


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
            <div class="view-btn">
                <a href="event.php?event_id=<?php echo $row['event_id']; ?>">View</a>
            </div>
        </div>
        <div class="event-actions">
            <div class='action-dots' onclick='toggleActionMenu("<?php echo $row["event_id"] ?>")'>⋮</div>
            <div class="action-menu" id='action-menu-<?php echo $row["event_id"] ?>'>
                <button type='button' class='btn btn-primary' data-bs-toggle='modal'
                    data-bs-target='#editModal_<?php echo $row["event_id"] ?>'>
                    Edit
                </button>
                <form action='delete.php' method='post'>
                    <input type='hidden' name='event_id' value='<?php echo $row["event_id"] ?>'>
                    <button type='submit' class='btn btn-danger' name='delete_event'>Delete</button>
                </form>
            </div>
        </div>
    </div>
    <?php
            }
    ?>


    <!-- ADD Modal -->
    <div class="modal fade" id="addModal" tabindex="-1" aria-labelledby="addModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addModalLabel">Add New Event</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form action="add.php" method="POST">
                        <label for="event_name">Event Title: </label>
                        <input type="text" name="event_name" required> <br>
                        <label for="details">Details: </label>
                        <textarea name="details" cols="15" rows="4" required></textarea> <br>
                        <label for="date">Date: </label>
                        <input type="date" name="date" required>
                        <label for="dept_name">Department</label>
                        <select name="dept_name">
                            <?php
                            $sql = 'SELECT * FROM tb_department';
                            $result = $conn->query($sql);
                                     while ($row = $result->fetch_assoc()){
                                        echo"<option value='{$row['department_id']}'>{$row['department_name']}</option>";
                                     }
                            ?>
                        </select>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-success" name="add_event">Add</button>
                    </form>
                </div>
            </div>
        </div>
    </div>



    <?php
            $updateSql = 'SELECT * FROM tb_event';
            $updateResult = $conn->query($updateSql);  

            while ($updateRow = $updateResult->fetch_assoc()) {
             ?>


    <!-- EDIT Modal -->
    <div class="modal fade" id="editModal_<?php echo $updateRow['event_id']; ?>" tabindex="-1"
        aria-labelledby="editModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editModalLabel">Update Event Details</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form action="edit.php" method="POST">
                        <input type="hidden" name="event_id" value="<?php echo $updateRow['event_id']?>">
                        <label for="event_name">Event Title: </label>
                        <input type="text" name="event_name" value="<?php echo $updateRow['event_title']?>" required>
                        <br>
                        <label for="details">Details: </label>
                        <textarea name="details" cols="15" rows="4"
                            required><?php echo $updateRow['event_detail']?></textarea> <br>
                        <label for="date">Date: </label>
                        <input type="date" name="date" value="<?php echo $updateRow['event_date']?>" required>
                        <label for="dept_name">Department</label>
                        <select name="dept_name">
                            <?php
                            $sql = 'SELECT * FROM tb_department';
                            $result = $conn->query($sql);
                                     while ($row = $result->fetch_assoc()){
                                        echo"<option value='{$row['department_id']}'>{$row['department_name']}</option>";
                                     }
                            ?>
                        </select>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-success" name="edit_event">Save</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <?php }   ?>

    <script>
    document.addEventListener('click', function(event) {
        const actionMenus = document.querySelectorAll('.action-menu');
        actionMenus.forEach(function(menu) {
            if (!menu.contains(event.target) && event.target.className !== 'action-dots') {
                menu.style.display = 'none';
            }
        });
    });

    function toggleActionMenu(eventId) {
        const actionMenu = document.getElementById(`action-menu-${eventId}`);
        actionMenu.style.display = actionMenu.style.display === 'block' ? 'none' : 'block';
    }
    </script>



</body>

</html>