<?php
// Include the database connection file
require 'db.php';

// SQL query to select all requests
$sql = "SELECT id, resident_id, location, waste_type, quantity, service_type, schedule_date, schedule_time FROM requests ORDER BY created_at DESC";
$result = $conn->query($sql);

$data = [];
if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $data[] = $row;
    }
}

// Return data as JSON
header('Content-Type: application/json');
echo json_encode($data);

// Close the database connection
$conn->close();
?>