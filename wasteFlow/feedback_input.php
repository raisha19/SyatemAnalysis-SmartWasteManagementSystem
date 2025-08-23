<?php
include 'db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $userId = $conn->real_escape_string($_POST['userId']);
    $type = $conn->real_escape_string($_POST['type']);
    $subject = $conn->real_escape_string($_POST['subject']);
    $details = $conn->real_escape_string($_POST['details']);
    $priority = $conn->real_escape_string($_POST['priority']);
    
  

    $sql = "INSERT INTO feedbacks (user_id, type, subject, details, priority) 
            VALUES ('$userId', '$type', '$subject', '$details', '$priority')";

    if ($conn->query($sql) === TRUE) {
        header("Location: feedback_output.php?status=success");
        exit();
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }

    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>WasteFlow — Feedback & Complaint (Input)</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="style.css">
</head>
<body class="wf-body">
    <header class="wf-header">
        <div class="wf-title">WasteFlow — Feedback & Complaint (Input)</div>
        <nav class="wf-nav">
            <a href="dashboard.php">Dashboard</a>
            <a href="feedback_output.php">Output</a>
        </nav>
    </header>

    <main class="wf-main">
        <section class="wf-section">
            <h2 class="wf-h2">Submit Feedback or Complaint</h2>
            <form class="wf-form" action="feedback_input.php" method="post">
                <div class="wf-grid-2">
                    <div class="wf-field">
                        <label for="userId">User ID</label>
                        <input type="text" id="userId" name="userId" placeholder="Enter your User ID" required>
                    </div>

                    <div class="wf-field">
                        <label for="type">Type</label>
                        <select id="type" name="type" required>
                            <option value="">Select Type</option>
                            <option value="Feedback">Feedback</option>
                            <option value="Complaint">Complaint</option>
                        </select>
                    </div>

                    <div class="wf-field">
                        <label for="subject">Subject</label>
                        <input type="text" id="subject" name="subject" placeholder="Short subject" required>
                    </div>
                    
                    <div class="wf-field" style="grid-column: 1 / -1;">
                        <label for="details">Details</label>
                        <textarea id="details" name="details" placeholder="Enter detailed description" required></textarea>
                    </div>

                    <div class="wf-field">
                        <label for="priority">Priority</label>
                        <select id="priority" name="priority" required>
                            <option value="Normal">Normal</option>
                            <option value="High">High</option>
                        </select>
                    </div>
                </div>

                <div class="wf-actions">
                    <button type="submit" class="wf-btn primary">Submit</button>
                    <a class="wf-btn ghost" href="dashboard.php">Cancel</a>
                </div>
            </form>
        </section>
    </main>

    <footer class="wf-footer">© 2025 WasteFlow · Dhaka City</footer>
</body>
</html>
