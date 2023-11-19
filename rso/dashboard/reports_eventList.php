<?php
// Database connection setup
include '../../config/config.php';

// Get the current request URI
$requestUri = $_SERVER['REQUEST_URI'];

$conn = new mysqli('localhost', 'root', '', 'db_ba3101');
if ($conn->connect_error) {
    die('Connection Failed: ' . $conn->connect_error);
}

// Pagination setup
$limit = isset($_GET['limit']) ? $_GET['limit'] : 10;
$page = isset($_GET['page']) ? $_GET['page'] : 1;
$offset = ($page - 1) * $limit;

// Search functionality
$searchTerm = isset($_GET['search']) ? $_GET['search'] : '';

try {
    // Query to get a subset of records based on search and pagination
    $query = $pdo->prepare("SELECT * FROM tb_event WHERE event_title LIKE :searchTerm LIMIT :limit OFFSET :offset");
    $query->bindValue(':searchTerm', '%' . $searchTerm . '%', PDO::PARAM_STR);
    $query->bindValue(':limit', $limit, PDO::PARAM_INT);
    $query->bindValue(':offset', $offset, PDO::PARAM_INT);

    if (!$query->execute()) {
        throw new Exception("Query failed: " . implode(" ", $query->errorInfo()));
    }

    // Fetch results
    $rows = $query->fetchAll(PDO::FETCH_ASSOC);

    // Pagination query
    $paginationQuery = $pdo->prepare("SELECT COUNT(*) AS total FROM tb_event WHERE event_title LIKE :searchTerm");
    $paginationQuery->bindValue(':searchTerm', '%' . $searchTerm . '%', PDO::PARAM_STR);
} catch (Exception $ex) {
    echo "Error: " . $ex->getMessage();
    die();
}
?>

<!DOCTYPE html>
<html>

<head>
    <link rel="stylesheet" href="../../styles/rso.css">
</head>

<body>


    <main>
        <?php include '../../components/rsoHeader.php'; ?>
        <section class="head">
            <div class="searchCont">
                <?php include '../../components/search.php'; ?>
                <?php if (!empty($searchTerm)): ?>
                <img src='../../images/cross.png' alt='Image' class="icon" onclick="clearSearch()" id='clearBtn' />
                <?php endif; ?>
            </div>
            <div class="headbtn">
                <?php include '../../components/limit.php'; ?>
            </div>
        </section>

        <section class="tableContainer">
            <?php include '../../components/table.component.php';

            $head = array('Events', 'Action');
            $body = array();

            foreach ($rows as $row) {
    $event_id = $row["event_id"]; 
    $event_title = $row["event_title"];


    $viewAttendeesButton = '<a href="attendeesList.php?event_id=' . $event_id . '"><button>Print Report</button></a>';

    $body[] = array($event_title, $viewAttendeesButton);
}

            createTable($head, $body);
            ?>
            <section class="paginationCont">
                <?php
                include '../../components/pagination.php';
                generatePaginationLinks($pdo, $searchTerm, $limit, $paginationQuery);
                ?>
            </section>
    </main>
    <?php
    // Include your PHP code here to set $requestUri
    $requestUri = $_SERVER['REQUEST_URI'];
    ?>
    <script>
    const base_url = "<?php echo htmlspecialchars($requestUri, ENT_QUOTES, 'UTF-8'); ?>";
    </script>
    <script src="../../script/event.js"></script>
</body>

</html>