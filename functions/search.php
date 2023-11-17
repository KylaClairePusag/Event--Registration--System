<?php
// Assume $searchQuery is passed to this file before including it
$searchQuery = $_GET['searchTerm'] ?? '';
?>

<!-- Search form -->
<form id="searchForm" method="GET" action="javascript:void(0);">
    <input type="text" id="search" name="search" placeholder="Search..."
        value="<?php echo htmlspecialchars($searchTerm); ?>" required>
</form>