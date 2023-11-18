<?php
$searchQuery = $_GET['limit'] ?? '';
?>

<label for="limit">

</label>
<select id="limit" name="limit" onchange="changeLimit(this.value)">
    <option value="10" <?php if ($limit == 10)
        echo 'selected'; ?>>Limit: 10</option>
    <option value="15" <?php if ($limit == 15)
        echo 'selected'; ?>>Limit: 15</option>
    <option value="20" <?php if ($limit == 20)
        echo 'selected'; ?>>Limit: 20</option>

</select>