<?php
// pagination.php

function generatePaginationLinks($pdo, $searchTerm, $limit, $paginationQuery)
{
    try {
        if (!$paginationQuery->execute()) {
            throw new Exception("Pagination query failed: " . implode(" ", $paginationQuery->errorInfo()));
        }

        $result = $paginationQuery->fetch();
        $totalRecords = $result['total'];
        $totalPages = ceil($totalRecords / $limit);

        echo "<div class='pagination-container'>";

        // Check if $_GET['page'] is set, otherwise default to 1
        $currentPage = isset($_GET['page']) ? $_GET['page'] : 1;

        for ($i = 1; $i <= $totalPages; $i++) {
            $class = ($i == $currentPage) ? 'current-page' : '';
            echo "<a class='$class' href='" . $_SERVER['PHP_SELF'] . "?page=" . $i . "&search=" . urlencode($searchTerm) . "&limit=" . $limit . "'>" . $i . "</a> ";
        }
        echo "</div>";
    } catch (Exception $ex) {
        echo "Error: " . $ex->getMessage();
        die();
    }
}
?>