<!-- modal.php -->

<?php

function generateModal($modalId, $title, $fields, $buttonText, $action, $extraAttributes = '')
{
    echo <<<HTML
        <dialog id="$modalId" class="modal">
            <div class="modal-content">
                <button class="close" onclick="document.getElementById('$modalId').close()">&times;</button>
                <h2>$title</h2>
                <form method="POST" action="{$_SERVER['PHP_SELF']}" $extraAttributes>
    HTML;

    foreach ($fields as $field) {
        $id = $field['id'];
        $label = $field['label'];
        $type = $field['type'];
        $name = $field['name'];

        if ($type === 'select') {
            $options = '';
            foreach ($field['options'] as $optionValue => $optionText) {
                $options .= "<option value='$optionValue'>$optionText</option>";
            }
            echo <<<HTML
                <label for="$id">$label:</label>
                <select id="$id" name="$name">
                    $options
                </select>
            HTML;
        } else {
            echo <<<HTML
                <label for="$id">$label:</label>
                <input type="$type" id="$id" name="$name">
            HTML;
        }
    }

    echo <<<HTML
                <button type="submit" name="$action">$buttonText</button>
                </form>
            </div>
        </dialog>
    HTML;
}

?>