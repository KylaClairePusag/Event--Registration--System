<?php
function generatePaginationLinks($searchTerm, $limit, $paginationQuery, $selectedDepartment, $statusFilter) {
    try {
        if(!$paginationQuery->execute()) {
            throw new Exception("Pagination query failed: ".implode(" ", $paginationQuery->errorInfo()));
        }

        $result = $paginationQuery->fetch();
        $totalRecords = $result['total'];
        $totalPages = ceil($totalRecords / $limit);

        echo "<div class='pagination-container'>";

        $currentPage = isset($_GET['page']) ? $_GET['page'] : 1;

        $departmentParam = $selectedDepartment ? "&department=".urlencode($selectedDepartment) : "";
        $statusParam = $statusFilter ? "&status=".urlencode($statusFilter) : "";

        for($i = 1; $i <= $totalPages; $i++) {
            $class = ($i == $currentPage) ? 'current-page' : '';
            echo "<a class='$class' href='".$_SERVER['PHP_SELF']."?page=".$i."&search=".urlencode($searchTerm)."&limit=".$limit.$departmentParam.$statusParam."'>".$i."</a> ";
        }
        echo "</div>";
    } catch (Exception $ex) {
        echo "Error: ".$ex->getMessage();
        die();
    }
}



?>