<?php
// Include the database connection file
include 'db.php';

// --- Fetch all tasks for the table ---
$sql_tasks = "SELECT id, collector_id, area, task_type, priority, scheduled_date, scheduled_time, notes FROM tasks ORDER BY id DESC";
$result_tasks = $conn->query($sql_tasks);

$tasks = [];
if ($result_tasks->num_rows > 0) {
    while ($row = $result_tasks->fetch_assoc()) {
        $tasks[] = $row;
    }
}

// --- Fetch data for charts ---
$chart_data = [];

// Tasks per Ward
$sql_ward_chart = "SELECT area, COUNT(*) as count FROM tasks GROUP BY area";
$result_ward_chart = $conn->query($sql_ward_chart);
$chart_data['wards'] = [];
while ($row = $result_ward_chart->fetch_assoc()) {
    $chart_data['wards'][$row['area']] = $row['count'];
}

// Task Priority Distribution
$sql_priority_chart = "SELECT priority, COUNT(*) as count FROM tasks GROUP BY priority";
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
    <title>WasteFlow — Scheduling & Task Assignment (Output)</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="style.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        .chart-container {
            display: flex;
            flex-wrap: nowrap;
            gap: 20px;
            justify-content: space-around;
        }
        .chart-box {
            flex: 1 1 45%;
        }
    </style>
</head>
<body class="wf-body">
    <header class="wf-header">
        <div class="wf-title">WasteFlow — Scheduling & Task Assignment (Output)</div>
        <nav class="wf-nav">
            <a href="dashboard.html">Dashboard</a>
            <a href="schedule_input.php">Input</a>
        </nav>
    </header>

    <main class="wf-main">
        <section class="wf-section">
            <h2 class="wf-h2">Assigned Collection Tasks</h2>
            <div class="table-scroll">
                <table class="wf-table">
                    <thead>
                        <tr>
                            <th>Task ID</th>
                            <th>Collector ID</th>
                            <th>Ward</th>
                            <th>Task Type</th>
                            <th>Priority</th>
                            <th>Date</th>
                            <th>Time</th>
                            <th>Notes</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Loop through the fetched tasks from the database -->
                        <?php if (count($tasks) > 0): ?>
                            <?php foreach ($tasks as $task): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($task['id']); ?></td>
                                    <td><?php echo htmlspecialchars($task['collector_id']); ?></td>
                                    <td><?php echo htmlspecialchars($task['area']); ?></td>
                                    <td><?php echo htmlspecialchars($task['task_type']); ?></td>
                                    <td><?php echo htmlspecialchars($task['priority']); ?></td>
                                    <td><?php echo htmlspecialchars($task['scheduled_date']); ?></td>
                                    <td><?php echo htmlspecialchars($task['scheduled_time']); ?></td>
                                    <td><?php echo htmlspecialchars($task['notes']); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="8">No tasks assigned yet.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </section>

        <section class="wf-section">
            <div class="chart-container">
                <div class="chart-box">
                    <h3>Tasks per Ward</h3>
                    <canvas id="tasksWardChart" height="200"></canvas>
                </div>
                <div class="chart-box">
                    <h3>Task Priority Distribution</h3>
                    <canvas id="priorityChart" height="200"></canvas>
                </div>
            </div>
        </section>
    </main>

    <footer class="wf-footer">© 2025 WasteFlow · Dhaka City</footer>

    <script>
        // Pass PHP data to a JavaScript variable
        const chartData = <?php echo json_encode($chart_data); ?>;
        
        // Use the original colors for the charts
        const colors = {
            green: '#28a745',
            red: '#dc3545',
            blue: '#007bff',
            yellow: '#ffc107',
            lightgray: '#f8f9fa'
        };

        // Tasks per Ward Chart
        new Chart(document.getElementById('tasksWardChart'), {
            type: 'bar',
            data: {
                labels: Object.keys(chartData.wards),
                datasets: [{ 
                    label: 'Tasks', 
                    data: Object.values(chartData.wards), 
                    backgroundColor: colors.blue 
                }]
            },
            options: { 
                responsive: true,
                maintainAspectRatio: true,
                plugins: { legend: { display: false } },
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });

        // Task Priority Distribution Chart
        new Chart(document.getElementById('priorityChart'), {
            type: 'doughnut',
            data: {
                labels: Object.keys(chartData.priority),
                datasets: [{ 
                    data: Object.values(chartData.priority), 
                    backgroundColor: [colors.green, colors.red] 
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
    </script>
</body>
</html>
