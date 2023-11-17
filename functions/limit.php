<?php
// Assume $searchQuery is passed to this file before including it
$searchQuery = $_GET['limit'] ?? '';
?>

<label for="limit">Records per page:</label>
<select id="limit" name="limit" onchange="changeLimit(this.value)">
    <option value="5" <?php if ($limit == 5)
        echo 'selected'; ?>>5</option>
    <option value="10" <?php if ($limit == 10)
        echo 'selected'; ?>>10</option>
    <option value="15" <?php if ($limit == 15)
        echo 'selected'; ?>>15</option>
    <option value="20" <?php if ($limit == 20)
        echo 'selected'; ?>>20</option>
</select>