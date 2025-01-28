<?php
// Database connection
$host = "localhost";
$username = "root";
$password = ""; // Replace with your database password
$database = "your_database_name"; // Replace with your database name

$conn = new mysqli($host, $username, $password, $database);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Query to fetch departments
$sql = "SELECT id, name FROM departments ORDER BY name ASC";
$result = $conn->query($sql);

// Check if departments exist
if ($result->num_rows > 0) {
    echo '<option value="" disabled selected>-- Select Department --</option>';
    while ($row = $result->fetch_assoc()) {
        echo '<option value="' . htmlspecialchars($row['id']) . '">' . htmlspecialchars($row['name']) . '</option>';
    }
} else {
    echo '<option value="" disabled>No departments available</option>';
}

// Close the connection
$conn->close();
?>
