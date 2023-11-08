<?php
// Assume $searchQuery is passed to this file before including it
$searchQuery = $_GET['search_query'] ?? '';
?>

<!-- Search form -->
<form method="get" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>">
    Search RSO: <input type="text" name="search_query" value="<?php echo htmlspecialchars($searchQuery); ?>">
    <input type="submit" value="Search">
</form>