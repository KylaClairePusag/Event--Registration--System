<?php
function generateTable($headers, $rows)
{
    // Output the start of the table
    echo '<table border="2" width="100%"><thead><tr>';

    // Generate the headers, skip 'DEPARTMENT ID'
    foreach ($headers as $header) {
        if ($header !== 'DEPARTMENT ID') {
            echo "<th>{$header}</th>";
        }
    }

    // Add a header for the action column
    echo '<th>Action</th></thead><tbody>';

    // Generate the table rows
    foreach ($rows as $row) {
        echo '<tr>';
        foreach ($row as $column => $value) {
            // Skip the department_id column
            if ($column !== 'department_id') {
                echo "<td>" . htmlspecialchars($value) . "</td>";
            }
        }
        // Call the function to generate buttons for the row
        echo '<td>' . generateButton($row) . '</td>';
        echo '</tr>';
    }

    // Close the table tags
    echo '</tbody></table>';
}
?>