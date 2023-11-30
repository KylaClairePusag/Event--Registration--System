<?php

function generateButton($row)
{
    $buttonHTML = '<button class="edit-button" data-rso-id="' . htmlspecialchars($row['rso_id']) .
        '" data-rso-name="' . htmlspecialchars($row['rso_name']) .
        '" data-rso-password="' . htmlspecialchars($row['rso_password']) .
        '" data-rso-email="' . htmlspecialchars($row['email']) . '"';

    if (isset($row['department_id'])) {
        $buttonHTML .= ' data-department-id="' . htmlspecialchars($row['department_id']) . '"';
    }

    $buttonHTML .= '>Edit</button>';

    $buttonHTML .= '<form method="post" action="">';
    $buttonHTML .= '<input type="hidden" name="action" value="delete">';
    $buttonHTML .= '<input type="hidden" name="rso_id" value="' . htmlspecialchars($row['rso_id']) . '">';
    $buttonHTML .= '<input type="submit" value="Delete">';
    $buttonHTML .= '</form>';

    return $buttonHTML;
}

?>