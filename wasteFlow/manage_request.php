<?php
// Include the database connection file
require 'db.php';

// Check for the action parameter
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['action'])) {
    $action = $_POST['action'];
    $request_id = $_POST['id'];

    if ($action == 'update') {
        $ward = $_POST['ward'];
        $type = $_POST['type'];
        $quantity = $_POST['quantity'];
        $service = $_POST['service'];
        $schedule = $_POST['schedule'];
        list($date, $time) = explode("T", $schedule);

        $sql = "UPDATE requests SET location=?, waste_type=?, quantity=?, service_type=?, schedule_date=?, schedule_time=? WHERE id=?";
        $stmt = $conn->prepare($sql);
        if ($stmt) {
            $stmt->bind_param("ssisssi", $ward, $type, $quantity, $service, $date, $time, $request_id);

            if ($stmt->execute()) {
                echo "Request updated successfully.";
            } else {
                http_response_code(500);
                echo "Error updating request: " . $stmt->error;
            }
            $stmt->close();
        } else {
            http_response_code(500);
            echo "Error preparing statement: " . $conn->error;
        }

    } elseif ($action == 'delete') {
        $sql = "DELETE FROM requests WHERE id=?";
        $stmt = $conn->prepare($sql);
        if ($stmt) {
            $stmt->bind_param("i", $request_id);

            if ($stmt->execute()) {
                echo "Request deleted successfully.";
            } else {
                http_response_code(500);
                echo "Error deleting request: " . $stmt->error;
            }
            $stmt->close();
        } else {
            http_response_code(500);
            echo "Error preparing statement: " . $conn->error;
        }
    } else {
        http_response_code(400);
        echo "Invalid action.";
    }
} else {
    http_response_code(400);
    echo "Invalid request.";
}

// Close the database connection
$conn->close();
?>