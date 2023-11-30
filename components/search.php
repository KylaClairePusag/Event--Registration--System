<?php
$searchQuery = $_GET['searchTerm'] ?? '';
?>

<form id="searchForm" method="GET" action="javascript:void(0);">
    <input type="text" id="search" name="search" placeholder="Search..."
        value="<?php echo htmlspecialchars(str_replace('%', '', $searchTerm)) ?>" required>
</form>