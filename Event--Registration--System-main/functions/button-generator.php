<?php
// button-generator.php

function generateButton($row)
{
    // Start the button HTML string
    $buttonHTML = '<button class="edit-button" data-rso-id="' . htmlspecialchars($row['rso_id']) .
        '" data-rso-name="' . htmlspecialchars($row['rso_name']) .
        '" data-rso-password="' . htmlspecialchars($row['rso_password']) .
        '" data-rso-email="' . htmlspecialchars($row['email']) . '"';

    // Add department data attribute only if department_id exists in the row
    if (isset($row['department_id'])) {
        $buttonHTML .= ' data-department-id="' . htmlspecialchars($row['department_id']) . '"';
    }

    // Close the opening tag of the button
    $buttonHTML .= '>Edit</button>';

    // Start delete form
    $buttonHTML .= '<form method="post" action="">';
    $buttonHTML .= '<input type="hidden" name="action" value="delete">';
    $buttonHTML .= '<input type="hidden" name="rso_id" value="' . htmlspecialchars($row['rso_id']) . '">';
    $buttonHTML .= '<input type="submit" value="Delete">';
    $buttonHTML .= '</form>';

    // Return the complete HTML for the button and form
    return $buttonHTML;
}

?>