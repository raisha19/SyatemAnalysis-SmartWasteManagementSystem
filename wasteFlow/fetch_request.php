<?php
require 'db.php';

$sql = "SELECT id, resident_id, location, waste_type, quantity, service_type, schedule_date, schedule_time FROM requests ORDER BY created_at DESC";
$result = $conn->query($sql);

$data = [];
if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $data[] = $row;
    }
}

header('Content-Type: application/json');
echo json_encode($data);

// Close the database connection
$conn->close();
?>
