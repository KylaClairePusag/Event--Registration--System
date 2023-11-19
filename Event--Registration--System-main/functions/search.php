<?php
// Assume $searchQuery is passed to this file before including it
$searchQuery = $_GET['searchTerm'] ?? '';
?>

<!-- Search form -->
<form id="searchForm" method="GET" action="javascript:void(0);">
    <label for="search">Search:</label>
    <input type="text" id="search" name="search" value="<?php echo htmlspecialchars($searchTerm); ?>">
    <button type="submit">Submit</button>
</form>