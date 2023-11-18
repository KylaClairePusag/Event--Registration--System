<?php

function getDepartments($conn)
{
    $departments = [];
    $dept_query = "SELECT department_id, department_name FROM tb_department";
    $dept_result = $conn->query($dept_query);
    if ($dept_result->num_rows > 0) {
        while ($row = $dept_result->fetch_assoc()) {
            $departments[] = $row;
        }
    }
    return $departments;
}

function validateRSO($rso_name, $rso_password, $department_id)
{
    $errors = [
        'rso_nameErr' => '',
        'rso_passwordErr' => '',
        'deptErr' => ''
    ];

    if (empty(trim($rso_name))) {
        $errors['rso_nameErr'] = 'Name is required';
    }

    if (empty(trim($rso_password))) {
        $errors['rso_passwordErr'] = 'Password is required';
    }

    if (empty($department_id)) {
        $errors['deptErr'] = 'Department is required';
    }

    return $errors;
}

function executeQuery($conn, $sql, $types = null, $params = [])
{
    $stmt = $conn->prepare($sql);
    if ($types && $params) {
        $stmt->bind_param($types, ...$params);
    }
    $stmt->execute();
    return $stmt;
}

function fetchAll($result, $resulttype = MYSQLI_ASSOC)
{
    return $result->fetch_all($resulttype);
}

function addRSO($conn, $rso_name, $rso_password, $department_id)
{
    $stmt = executeQuery($conn, "INSERT INTO tb_rso (rso_name, rso_password, department_id) VALUES (?, ?, ?)", "ssi", [$rso_name, $rso_password, $department_id]);
    $stmt->close();
}

function updateRSO($conn, $rso_id, $rso_name, $rso_password, $department_id)
{
    $stmt = executeQuery($conn, "UPDATE tb_rso SET rso_name=?, rso_password=?, department_id=? WHERE rso_id=?", "ssii", [$rso_name, $rso_password, $department_id, $rso_id]);
    $stmt->close();
}

function deleteRSO($conn, $rso_id)
{
    $stmt = executeQuery($conn, "DELETE FROM tb_rso WHERE rso_id=?", "i", [$rso_id]);
    $stmt->close();
}

function searchRSO($conn, $searchQuery)
{
    $likeQuery = '%' . $searchQuery . '%';
    $stmt = executeQuery($conn, "SELECT * FROM tb_rso WHERE rso_name LIKE ?", "s", [$likeQuery]);
    $rso = fetchAll($stmt->get_result());
    $stmt->close();
    return $rso;
}

function paginateRSO($conn, $searchQuery, $startAt, $perPage)
{
    $likeQuery = '%' . $searchQuery . '%';
    $stmt = executeQuery($conn, "SELECT * FROM tb_rso WHERE rso_name LIKE ? LIMIT ?, ?", "sii", [$likeQuery, $startAt, $perPage]);
    $rso = fetchAll($stmt->get_result());
    $stmt->close();
    return $rso;
}

function getPaginationCount($conn, $searchQuery)
{
    $likeQuery = '%' . $searchQuery . '%';
    $stmt = executeQuery($conn, "SELECT COUNT(*) as count FROM tb_rso WHERE rso_name LIKE ?", "s", [$likeQuery]);
    $result = $stmt->get_result()->fetch_assoc();
    $stmt->close();
    return $result['count'];
}

// You can continue to add more functions as needed.

?>