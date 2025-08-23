<?php
// Include the database connection file
include 'db.php';

// --- Fetch all feedback and complaints for the table ---
$sql_feedbacks = "SELECT id, user_id, type, subject, priority, status FROM feedbacks ORDER BY id DESC";
$result_feedbacks = $conn->query($sql_feedbacks);

$feedbacks = [];
if ($result_feedbacks->num_rows > 0) {
    while ($row = $result_feedbacks->fetch_assoc()) {
        $feedbacks[] = $row;
    }
}

// --- Fetch data for charts ---
$chart_data = [];

// Feedback vs Complaint Distribution
$sql_type_chart = "SELECT type, COUNT(*) as count FROM feedbacks GROUP BY type";
$result_type_chart = $conn->query($sql_type_chart);
$chart_data['type'] = [];
while ($row = $result_type_chart->fetch_assoc()) {
    $chart_data['type'][$row['type']] = $row['count'];
}

// Complaint Priority
$sql_priority_chart = "SELECT priority, COUNT(*) as count FROM feedbacks WHERE type = 'Complaint' GROUP BY priority";
$result_priority_chart = $conn->query($sql_priority_chart);
$chart_data['priority'] = [];
while ($row = $result_priority_chart->fetch_assoc()) {
    $chart_data['priority'][$row['priority']] = $row['count'];
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>WasteFlow — Feedback & Complaint (Output)</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="style.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        /* New chart container styles for side-by-side layout */
        .chart-container {
            display: flex;
            flex-wrap: nowrap;
            gap: 20px;
            justify-content: space-around;
        }
        .chart-box {
            flex: 1 1 45%; /* Allow charts to grow but be at least 45% of the container width */
        }
    </style>
</head>
<body class="wf-body">
    <header class="wf-header">
        <div class="wf-title">WasteFlow — Feedback & Complaint (Output)</div>
        <nav class="wf-nav">
            <a href="dashboard.php">Dashboard</a>
            <a href="feedback_input.php">Input</a>
        </nav>
    </header>

    <main class="wf-main">
        <section class="wf-section">
            <h2 class="wf-h2">Feedback & Complaints Overview</h2>
            <div class="table-scroll">
                <table class="wf-table">
                    <thead>
                        <tr>
                            <th>Case ID</th>
                            <th>User ID</th>
                            <th>Type</th>
                            <th>Subject</th>
                            <th>Priority</th>
                            <th>Status</th>
                            <th>Response</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (count($feedbacks) > 0): ?>
                            <?php foreach ($feedbacks as $feedback): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($feedback['id']); ?></td>
                                    <td><?php echo htmlspecialchars($feedback['user_id']); ?></td>
                                    <td><?php echo htmlspecialchars($feedback['type']); ?></td>
                                    <td><?php echo htmlspecialchars($feedback['subject']); ?></td>
                                    <td><?php echo htmlspecialchars($feedback['priority']); ?></td>
                                    <td><?php echo htmlspecialchars($feedback['status']); ?></td>
                                    <td>-</td> <!-- Response field is not in the db schema, so we keep it static for now -->
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="7">No feedback or complaints submitted yet.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </section>

        <section class="wf-section">
            <!-- New container for charts -->
            <div class="chart-container">
                <div class="chart-box">
                    <h3>Feedback vs Complaint Distribution</h3>
                    <canvas id="typeChart" height="200"></canvas>
                </div>
                <div class="chart-box">
                    <h3>Complaint Priority</h3>
                    <canvas id="priorityChart" height="200"></canvas>
                </div>
            </div>
        </section>
    </main>

    <footer class="wf-footer">© 2025 WasteFlow · Dhaka City</footer>

    <script>
        // Pass PHP data to a JavaScript variable
        const chartData = <?php echo json_encode($chart_data); ?>;
        
        // Use a consistent color palette
        const colors = {
            green: '#28a745',
            red: '#dc3545',
            blue: '#007bff',
            yellow: '#ffc107',
            lightgray: '#f8f9fa'
        };

        // Feedback vs Complaint Distribution Chart
        new Chart(document.getElementById('typeChart'), {
            type: 'doughnut',
            data: {
                labels: Object.keys(chartData.type),
                datasets: [{ 
                    data: Object.values(chartData.type), 
                    backgroundColor: [colors.green, colors.red],
                    borderColor: 'white',
                    borderWidth: 2
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                plugins: {
                    legend: {
                        position: 'bottom'
                    }
                }
            }
        });

        // Complaint Priority Chart
        new Chart(document.getElementById('priorityChart'), {
            type: 'bar',
            data: {
                labels: Object.keys(chartData.priority),
                datasets: [{ 
                    label: 'Cases', 
                    data: Object.values(chartData.priority), 
                    backgroundColor: [colors.blue, colors.yellow],
                    borderColor: 'white',
                    borderWidth: 2
                }]
            },
            options: { 
                responsive: true,
                maintainAspectRatio: true,
                plugins: { 
                    legend: { display: false } 
                },
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
    </script>
</body>
</html>
