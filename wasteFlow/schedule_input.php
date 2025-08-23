<?php
// Include the database connection file
include 'db.php';

// Check if the form was submitted using the POST method
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // Sanitize and escape the input data to prevent SQL injection
    $collectorId = $conn->real_escape_string($_POST['collectorId']);
    $area = $conn->real_escape_string($_POST['area']);
    $taskType = $conn->real_escape_string($_POST['taskType']);
    $priority = $conn->real_escape_string($_POST['priority']);
    $date = $conn->real_escape_string($_POST['date']);
    $time = $conn->real_escape_string($_POST['time']);
    $notes = $conn->real_escape_string($_POST['notes']);

    // SQL query to insert the new task into the 'tasks' table
    $sql = "INSERT INTO tasks (collector_id, area, task_type, priority, scheduled_date, scheduled_time, notes) 
            VALUES ('$collectorId', '$area', '$taskType', '$priority', '$date', '$time', '$notes')";

    if ($conn->query($sql) === TRUE) {
        // If the query is successful, redirect to the output page
        header("Location: schedule_output.php");
        exit();
    } else {
        // Handle any errors during insertion
        echo "Error: " . $sql . "<br>" . $conn->error;
    }

    // Close the database connection
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>WasteFlow — Scheduling & Task Assignment (Input)</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="style.css">
</head>
<body class="wf-body">
    <header class="wf-header">
        <div class="wf-title">WasteFlow — Scheduling & Task Assignment (Input)</div>
        <nav class="wf-nav">
            <a href="dashboard.html">Dashboard</a>
            <a href="schedule_output.php">Output</a>
        </nav>
    </header>

    <main class="wf-main">
        <section class="wf-section">
            <h2 class="wf-h2">Assign Collection Tasks</h2>
            <!-- IMPORTANT: Update the form's action and method -->
            <form class="wf-form" action="schedule_input.php" method="post">
                <div class="wf-grid-3">
                    <div class="wf-field">
                        <label for="collectorId">Collector ID</label>
                        <input type="text" id="collectorId" name="collectorId" placeholder="Enter Collector ID" required>
                    </div>

                    <div class="wf-field">
                        <label for="area">Area / Ward</label>
                        <select id="area" name="area" required>
                            <option value="">Select Ward / Area</option>
                            <option value="Ward 1">Ward 1</option>
                            <option value="Ward 2">Ward 2</option>
                            <option value="Ward 3">Ward 3</option>
                        </select>
                    </div>

                    <div class="wf-field">
                        <label for="taskType">Task Type</label>
                        <select id="taskType" name="taskType" required>
                            <option value="Pickup">Pickup</option>
                            <option value="Smart Bin Collection">Smart Bin Collection</option>
                        </select>
                    </div>

                    <div class="wf-field">
                        <label for="priority">Priority</label>
                        <select id="priority" name="priority" required>
                            <option value="Normal">Normal</option>
                            <option value="High">High</option>
                        </select>
                    </div>

                    <div class="wf-field">
                        <label for="date">Scheduled Date</label>
                        <input type="date" id="date" name="date" required>
                    </div>

                    <div class="wf-field">
                        <label for="time">Scheduled Time</label>
                        <input type="time" id="time" name="time" required>
                    </div>

                    <div class="wf-field">
                        <label for="notes">Notes</label>
                        <textarea id="notes" name="notes" placeholder="Any instructions for collector"></textarea>
                    </div>
                </div>

                <div class="wf-actions">
                    <button type="submit" class="wf-btn primary">Assign Task</button>
                    <a class="wf-btn ghost" href="dashboard.html">Cancel</a>
                </div>
            </form>
        </section>
    </main>

    <footer class="wf-footer">© 2025 WasteFlow · Dhaka City</footer>
</body>
</html>
