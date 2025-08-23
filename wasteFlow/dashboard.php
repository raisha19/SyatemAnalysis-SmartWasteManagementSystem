<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>WasteFlow — Dashboard</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="dashboard.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
</head>
<body class="wf-body">
    <?php
    include 'db.php';

    // --- Fetch Key Statistics ---
    $total_users = $conn->query("SELECT COUNT(*) FROM users")->fetch_row()[0];
    $total_bins = $conn->query("SELECT COUNT(*) FROM bins")->fetch_row()[0];
    // The query for pending requests has been removed as it requires the 'status' column.
    // $pending_requests = $conn->query("SELECT COUNT(*) FROM requests WHERE status = 'Pending'")->fetch_row()[0];

    // --- Fetch Data for Charts ---
    $chart_data = [];

    // User Roles Distribution
    $sql_user_roles = "SELECT role, COUNT(*) as count FROM users GROUP BY role";
    $result_user_roles = $conn->query($sql_user_roles);
    $chart_data['userRoles'] = [];
    while ($row = $result_user_roles->fetch_assoc()) {
        $chart_data['userRoles'][$row['role']] = $row['count'];
    }

    // Bin Fill Level Distribution
    $sql_bin_levels = "SELECT fill_level, COUNT(*) as count FROM bins GROUP BY fill_level";
    $result_bin_levels = $conn->query($sql_bin_levels);
    $chart_data['binLevels'] = [];
    while ($row = $result_bin_levels->fetch_assoc()) {
        $chart_data['binLevels'][$row['fill_level']] = $row['count'];
    }

    // Waste by Type (for WasteType Chart)
    $sql_waste_type = "SELECT waste_type, SUM(quantity) as total_quantity FROM requests GROUP BY waste_type";
    $result_waste_type = $conn->query($sql_waste_type);
    $chart_data['wasteType'] = [];
    while ($row = $result_waste_type->fetch_assoc()) {
        $chart_data['wasteType'][$row['waste_type']] = $row['total_quantity'];
    }
    
    // Requests Trend (for Requests Chart)
    $sql_requests_trend = "SELECT DATE(schedule_date) as request_date, COUNT(*) as count FROM requests WHERE schedule_date >= CURDATE() - INTERVAL 6 DAY GROUP BY DATE(schedule_date) ORDER BY request_date ASC";
    $result_requests_trend = $conn->query($sql_requests_trend);
    $requests_trend = [];
    $labels_trend = [];
    while ($row = $result_requests_trend->fetch_assoc()) {
        $labels_trend[] = date('D', strtotime($row['request_date'])); // Get day of the week
        $requests_trend[] = $row['count'];
    }
    $chart_data['requestsTrend']['labels'] = $labels_trend;
    $chart_data['requestsTrend']['data'] = $requests_trend;

    $conn->close();
    ?>

    <header class="wf-header">
        <div class="wf-title">WasteFlow — Dashboard</div>
        <nav class="wf-nav">
            <a href="dashboard.php" aria-current="page">Dashboard</a>
            <a href="payment_input.html">Payment</a>
            <a href="index.html">Logout</a>
        </nav>
    </header>

    <main class="wf-main">

        <!-- Quick Actions -->
        <section class="wf-section">
            <div class="grid-container">
                <div class="card">
                    <span class="card-icon">👤</span>
                    <span class="card-title">User</span>
                    <div class="card-actions">
                        <a href="user_input.php" class="action-link">User Management</a>
                        <a href="user_output.php" class="action-link">View Users & Manage</a>
                    </div>
                </div>
                <div class="card">
                    <span class="card-icon">📝</span>
                    <span class="card-title">Request</span>
                    <div class="card-actions">
                        <a href="request_input.php" class="action-link">Request Waste Collection</a>
                        <a href="request_output.php" class="action-link">View Request</a>
                    </div>
                </div>
                <div class="card">
                    <span class="card-icon">🗑</span>
                    <span class="card-title">Smart Bin</span>
                    <div class="card-actions">
                        <a href="bin_input.php" class="action-link">Input</a>
                        <a href="bin_output.php" class="action-link">View Bin Data</a>
                    </div>
                </div>
            </div>
            <div class="grid-container">
                <div class="card">
                    <span class="card-icon">📅</span>
                    <span class="card-title">Schedule & Task</span>
                    <div class="card-actions">
                        <a href="schedule_input.php" class="action-link">Create Schedule & Assign Task</a>
                        <a href="schedule_output.php" class="action-link">View Schedule & Task</a>
                    </div>
                </div>
                <div class="card">
                    <span class="card-icon">💬</span>
                    <span class="card-title">Feedback</span>
                    <div class="card-actions">
                        <a href="feedback_input.php" class="action-link">Submit Feedback & Complaint</a>
                        <a href="feedback_output.php" class="action-link">View Feedback & Complaint</a>
                    </div>
                </div>
                <div class="card">
                    <span class="card-icon">🔎</span>
                    <span class="card-title">Analytics</span>
                    <div class="card-actions">
                        <a href="analysis.php" class="action-link">View Analytics</a>
                    </div>
                </div>
            </div>
            <div class="grid-container">
                <div class="card emphasis">
                    <span class="card-icon">💳</span>
                    <span class="card-title">Resident Payment</span>
                    <div class="card-actions">
                        <a href="payment_input.html" class="action-link">Input</a>
                        <a href="payment_output.html" class="action-link">Output</a>
                    </div>
                </div>
            </div>
        </section>

        <!-- Dynamic Charts -->
        <section class="wf-section">
            <h2 class="wf-h2">System Insights</h2>
            <div class="wf-grid-3" style="display: flex; gap: 20px;">
                <div class="chart-box" style="flex: 1;">
                    <h3>Waste by Type (This Week)</h3>
                    <canvas id="chartWasteType" height="200"></canvas>
                </div>
                <div class="chart-box" style="flex: 1;">
                    <h3>Smart Bin Status</h3>
                    <canvas id="chartBinStatus" height="200"></canvas>
                </div>
                <div class="chart-box" style="flex: 1;">
                    <h3>Requests Trend (7 days)</h3>
                    <canvas id="chartRequests" height="200"></canvas>
                </div>
            </div>
        </section>

    </main>

    <footer class="wf-footer">© 2025 WasteFlow · Dhaka City</footer>

    <script>
        const chartData = <?php echo json_encode($chart_data); ?>;
        
        // Soothing color palette
        const soothingColors = {
            blue: '#66a3d2',
            green: '#5cb85c',
            yellow: '#ffc107',
            red: '#dc3545',
            gray: '#808080'
        };

        // Waste by Type Chart (Bar)
        new Chart(document.getElementById('chartWasteType'), {
            type: 'bar',
            data: {
                labels: Object.keys(chartData.wasteType),
                datasets: [{
                    label: 'KG',
                    data: Object.values(chartData.wasteType),
                    backgroundColor: [soothingColors.blue, soothingColors.green, soothingColors.yellow, soothingColors.red, soothingColors.gray],
                    borderColor: '#ffffff',
                    borderWidth: 2
                }]
            },
            options: {
                responsive: true,
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

        // Smart Bin Status Chart (Doughnut)
        new Chart(document.getElementById('chartBinStatus'), {
            type: 'doughnut',
            data: {
                labels: Object.keys(chartData.binLevels),
                datasets: [{
                    data: Object.values(chartData.binLevels),
                    backgroundColor: [soothingColors.red, soothingColors.yellow, soothingColors.green],
                    borderColor: '#ffffff',
                    borderWidth: 2
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: { position: 'bottom' }
                }
            }
        });

        // Requests Trend Chart (Line)
        new Chart(document.getElementById('chartRequests'), {
            type: 'line',
            data: {
                labels: chartData.requestsTrend.labels,
                datasets: [{
                    label: 'Requests',
                    data: chartData.requestsTrend.data,
                    tension: 0.3,
                    fill: false,
                    borderColor: soothingColors.blue,
                    backgroundColor: soothingColors.blue
                }]
            },
            options: {
                responsive: true
            }
        });
    </script>
</body>
</html>
