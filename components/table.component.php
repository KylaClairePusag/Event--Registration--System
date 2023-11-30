<?php
function createTable($head, $body) {
    if(empty($body)) {
        echo '<p>No data available.</p>';
        return;
    }

    $hasPasswordColumn = in_array('Password', $head);

    echo '<table class="event-table">'; 
    echo '<thead>';
    echo '<tr>';
    foreach($head as $column) {
        if(!$hasPasswordColumn || (strtolower($column) !== 'password')) {
            echo '<th>'.$column.'</th>';
        }
    }
    echo '</tr>';
    echo '</thead>';
    echo '<tbody>';
    foreach($body as $row) {
        echo '<tr>';
        foreach($row as $key => $cell) {
            if(!$hasPasswordColumn || (strtolower($head[$key]) !== 'password')) {
                $nowrapStyle = (strtolower($head[$key]) === 'details') ? ' style="white-space: nowrap; overflow: hidden; text-overflow: ellipsis; max-width: 200px;"' : ''; 
                echo '<td'.$nowrapStyle.'>'.$cell.'</td>';
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