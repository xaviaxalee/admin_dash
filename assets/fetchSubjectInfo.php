<?php

include('connect.php');

if (isset($_POST['department_id'])) {
    $id = $_POST['department_id'];
    $data = array(
        'department_id' => $id
    );

    // Query to fetch department details
    $sql = "SELECT *
            FROM departments
            WHERE id = ?";

    $stmt = mysqli_prepare($conn, $sql);

    // Bind the parameter
    mysqli_stmt_bind_param($stmt, "s", $id);

    // Execute the statement
    mysqli_stmt_execute($stmt);

    // Get the result
    $result = mysqli_stmt_get_result($stmt);

    if (mysqli_num_rows($result)) {
        while ($row = mysqli_fetch_assoc($result)) {
            // Add department name to the data array
            $data["department_name"] = $row["department_name"];
        }
    }

    // Convert data to JSON format
    $jsonData = json_encode($data);
    header('Content-Type: application/json');
    echo $jsonData;
}

?>
