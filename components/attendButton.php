<?php


function generateAttenButton($event_id, $pdo)
{
    $checkQuery = "SELECT * FROM tb_attendees WHERE event_id = :event_id AND student_id = :student_id";
    $checkStmt = $pdo->prepare($checkQuery);
    $checkStmt->bindParam(':event_id', $event_id, PDO::PARAM_INT);
    $checkStmt->bindParam(':student_id', $_SESSION['student_id'], PDO::PARAM_INT);
    $checkStmt->execute();
    $attendeeExists = $checkStmt->rowCount() > 0;

    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['attend'])) {
        if (!$attendeeExists) {
            $query = "INSERT INTO tb_attendees (event_id, student_id) VALUES (:event_id, :student_id)";
            $stmt = $pdo->prepare($query);
            $stmt->bindParam(':event_id', $event_id, PDO::PARAM_INT);
            $stmt->bindParam(':student_id', $_SESSION['student_id'], PDO::PARAM_INT);
            $stmt->execute();
        }

        header("Location: index.php");
        exit;
    }

    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['delete'])) {
        $deleteQuery = "DELETE FROM tb_attendees WHERE student_id = :student_id";
        $deleteStmt = $pdo->prepare($deleteQuery);
        $deleteStmt->bindParam(':student_id', $_SESSION['student_id'], PDO::PARAM_INT);
        $deleteStmt->execute();

        $_SESSION['delete_action_completed'] = true;

        header("Location: index.php");
        exit;
    }
    ?>
    <form action="" method="POST">
        <?php
        if ($attendeeExists) {
            echo '<button type="submit" name="delete" id="cancelbtn">Cancel</button>';
        } else {
            echo '<button type="submit" name="attend">Interested</button>';
        }
        ?>
    </form>
    <?php
}

?>