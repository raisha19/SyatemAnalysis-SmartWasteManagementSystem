<?php
include 'db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $resident_id = $_POST['residentId'];
    $location = $_POST['location'];
    $waste_type = $_POST['wasteType'];
    $quantity = $_POST['quantity'];
    $service_type = $_POST['serviceType'];
    $schedule_date = $_POST['scheduleDate'];
    $schedule_time = $_POST['scheduleTime'];

    $sql = "INSERT INTO requests (resident_id, location, waste_type, quantity, service_type, schedule_date, schedule_time) 
            VALUES (?, ?, ?, ?, ?, ?, ?)";
    
    $stmt = $conn->prepare($sql);
    
    if ($stmt) {
        $stmt->bind_param("sssisss", $resident_id, $location, $waste_type, $quantity, $service_type, $schedule_date, $schedule_time);
        
        if ($stmt->execute()) {
            header("Location: request_output.php?status=success_insert");
            exit();
        } else {
            echo "Error: " . $stmt->error;
        }
        $stmt->close();
    } else {
        echo "Error preparing statement: " . $conn->error;
    }
}
$conn->close();
?>