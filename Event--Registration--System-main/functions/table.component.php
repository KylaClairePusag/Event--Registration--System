<?php
function createTable($head, $body)
{
    echo '<table>';
    echo '<thead>';
    echo '<tr>';
    foreach ($head as $column) {
        echo '<th>' . $column . '</th>';
    }
    echo '</tr>';
    echo '</thead>';
    echo '<tbody>';
    foreach ($body as $row) {
        echo '<tr>';
        foreach ($row as $cell) {
            echo '<td>' . $cell . '</td>';
        }
        echo '</tr>';
    }
    echo '</tbody>';
    echo '</table>';
}
?>