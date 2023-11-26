<?php
// pagination.php

// Update the function to handle potential errors
function generatePaginationLinks($conn, $searchTerm, $limit, $paginationQuery) {
    try {
        if(!$paginationQuery->execute()) {
            throw new Exception("Pagination query failed: ".$paginationQuery->error);
        }

        $result = $paginationQuery->get_result();

        // Check if there are rows in the result
        if($result->num_rows > 0) {
            $totalRecords = $result->fetch_assoc()['total'];
            $totalPages = ceil($totalRecords / $limit);

            echo "<div class='pagination-container'>";

            // Check if $_GET['page'] is set, otherwise default to 1
            $currentPage = isset($_GET['page']) ? $_GET['page'] : 1;

            for($i = 1; $i <= $totalPages; $i++) {
                $class = ($i == $currentPage) ? 'current-page' : '';
                echo "<a class='$class' href='".$_SERVER['PHP_SELF']."?page=".$i."&search=".urlencode($searchTerm)."&limit=".$limit."'>".$i."</a> ";
            }

            echo "</div>";
        } else {
            echo "No records found.";
        }
    } catch (Exception $ex) {
        echo "Error: ".$ex->getMessage();
    }
}

?>