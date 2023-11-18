<?php
function createTable($head, $body)
{
    if (empty($body)) {
        echo '<p>No data available.</p>';
        return;
    }

    // Check if the password column is present in the $head array
    $hasPasswordColumn = in_array('Password', $head);

    echo '<table>';
    echo '<thead>';
    echo '<tr>';
    foreach ($head as $column) {
        // Only display the column if it is not the password column
        if (!$hasPasswordColumn || (strtolower($column) !== 'password')) {
            echo '<th>' . $column . '</th>';
        }
    }
    echo '</tr>';
    echo '</thead>';
    echo '<tbody>';
    foreach ($body as $row) {
        echo '<tr>';
        foreach ($row as $key => $cell) {
            // Only display the cell if it corresponds to a non-password column
            if (!$hasPasswordColumn || (strtolower($head[$key]) !== 'password')) {
                echo '<td>' . $cell . '</td>';
            }
        }
        echo '</tr>';
    }
    echo '</tbody>';
    echo '</table>';
    echo '<div class="table-summary">';
    echo '</div>';
}
?>