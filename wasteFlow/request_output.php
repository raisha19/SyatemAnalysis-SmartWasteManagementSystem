<!DOCTYPE html>
<html>
<head>
    <title>WasteFlow - Submitted Requests & Analytics</title>
    <link rel="stylesheet" type="text/css" href="style.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
</head>
<body class="wf-body">
    <header class="wf-header">
        <div class="wf-title">WasteFlow</div>
        <nav class="wf-nav">
            <a href="wasteflow_input.php">Submit Request</a>
            <a href="wasteflow_output.php" aria-current="page">View Requests</a>
        </nav>
    </header>
    <main class="wf-main">
        <section class="wf-section">
            <h2 class="wf-h2">📋 Submitted Requests</h2>
            <div class="wf-card">
                <form method="GET" action="">
                    <label for="search">Search Requests:</label>
                    <input type="text" id="search" name="search" placeholder="Enter user ID or location">
                    <button type="submit" class="wf-btn primary">Search</button>
                </form>
            </div>
            
            <div class="table-scroll">
                <table class="wf-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Resident ID</th>
                            <th>Location</th>
                            <th>Waste Type</th>
                            <th>Quantity (kg)</th>
                            <th>Service Type</th>
                            <th>Schedule</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        include 'db.php';
                        
                        $sql = "SELECT * FROM requests";
                        if (isset($_GET['search']) && !empty($_GET['search'])) {
                            $searchTerm = "%" . $_GET['search'] . "%";
                            $sql .= " WHERE resident_id LIKE ? OR location LIKE ?";
                            $stmt = $conn->prepare($sql);
                            $stmt->bind_param("ss", $searchTerm, $searchTerm);
                            $stmt->execute();
                            $result = $stmt->get_result();
                        } else {
                            $result = $conn->query($sql);
                        }

                        if ($result->num_rows > 0) {
                            while ($row = $result->fetch_assoc()) {
                                echo "<tr>";
                                echo "<td>" . htmlspecialchars($row['id']) . "</td>";
                                echo "<td>" . htmlspecialchars($row['resident_id']) . "</td>";
                                echo "<td>" . htmlspecialchars($row['location']) . "</td>";
                                echo "<td>" . htmlspecialchars($row['waste_type']) . "</td>";
                                echo "<td>" . htmlspecialchars($row['quantity']) . "</td>";
                                echo "<td>" . htmlspecialchars($row['service_type']) . "</td>";
                                echo "<td>" . htmlspecialchars($row['schedule_date']) . " " . htmlspecialchars($row['schedule_time']) . "</td>";
                                echo "<td>
                                        <a href='request_input2.php?edit=" . $row['id'] . "'>Update</a> |
                                        <a href='request_delete.php?id=" . $row['id'] . "' onclick=\"return confirm('Delete this request?');\">Delete</a>
                                    </td>";
                                echo "</tr>";
                            }
                        } else {
                            echo "<tr><td colspan='8'>No requests found.</td></tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </section>

        <section class="wf-section">
            <h2 class="wf-h2">📊 Analytics & Charts</h2>
            <div class="dash-charts">
                <div class="chart-box">
                    <canvas id="wasteTypeChart"></canvas>
                </div>
                <div class="chart-box">
                    <canvas id="serviceTypeChart"></canvas>
                </div>
                <div class="chart-box">
                    <canvas id="requestsPerDayChart"></canvas>
                </div>
            </div>
            
            <?php
            // --- PHP logic to fetch data for charts ---
            $data = [
                'wasteType' => ['labels' => [], 'counts' => []],
                'serviceType' => ['labels' => [], 'counts' => []],
                'requestsPerDay' => ['labels' => [], 'counts' => []]
            ];
            
            // Data for Waste Type Pie Chart
            $sql_pie = "SELECT waste_type, COUNT(*) as count FROM requests GROUP BY waste_type";
            $result_pie = $conn->query($sql_pie);
            while ($row = $result_pie->fetch_assoc()) {
                $data['wasteType']['labels'][] = $row['waste_type'];
                $data['wasteType']['counts'][] = $row['count'];
            }

            // Data for Service Type Bar Chart
            $sql_bar = "SELECT service_type, COUNT(*) as count FROM requests GROUP BY service_type";
            $result_bar = $conn->query($sql_bar);
            while ($row = $result_bar->fetch_assoc()) {
                $data['serviceType']['labels'][] = $row['service_type'];
                $data['serviceType']['counts'][] = $row['count'];
            }

            // Data for Requests Per Day Line Chart
            $sql_line = "SELECT schedule_date, COUNT(*) as count FROM requests GROUP BY schedule_date ORDER BY schedule_date ASC";
            $result_line = $conn->query($sql_line);
            while ($row = $result_line->fetch_assoc()) {
                $data['requestsPerDay']['labels'][] = $row['schedule_date'];
                $data['requestsPerDay']['counts'][] = $row['count'];
            }
            
            $conn->close();
            ?>

            <script>
                // Pass PHP data to a JavaScript variable
                const chartData = <?php echo json_encode($data); ?>;

                document.addEventListener('DOMContentLoaded', function() {
                    // Pie Chart for Waste Types
                    new Chart(document.getElementById('wasteTypeChart'), {
                        type: 'pie',
                        data: {
                            labels: chartData.wasteType.labels,
                            datasets: [{
                                label: 'Waste Types',
                                data: chartData.wasteType.counts,
                                backgroundColor: [
                                    '#4CAF50', // Organic
                                    '#2196F3', // Plastic
                                    '#FFC107', // E-Waste
                                    '#E91E63', // Medical
                                    '#9E9E9E'  // Others
                                ]
                            }]
                        },
                        options: {
                            responsive: true,
                            plugins: {
                                legend: {
                                    position: 'top',
                                },
                                title: {
                                    display: true,
                                    text: 'Waste Requests by Type'
                                }
                            }
                        }
                    });

                    // Bar Chart for Service Types
                    new Chart(document.getElementById('serviceTypeChart'), {
                        type: 'bar',
                        data: {
                            labels: chartData.serviceType.labels,
                            datasets: [{
                                label: 'Service Types',
                                data: chartData.serviceType.counts,
                                backgroundColor: [
                                    'rgba(75, 192, 192, 0.6)',
                                    'rgba(153, 102, 255, 0.6)'
                                ]
                            }]
                        },
                        options: {
                            responsive: true,
                            plugins: {
                                legend: { display: false },
                                title: {
                                    display: true,
                                    text: 'Requests by Service Type'
                                }
                            },
                            scales: {
                                y: {
                                    beginAtZero: true
                                }
                            }
                        }
                    });

                    // Line Chart for Requests Per Day
                    new Chart(document.getElementById('requestsPerDayChart'), {
                        type: 'line',
                        data: {
                            labels: chartData.requestsPerDay.labels,
                            datasets: [{
                                label: 'Requests Per Day',
                                data: chartData.requestsPerDay.counts,
                                borderColor: 'rgb(75, 192, 192)',
                                tension: 0.1
                            }]
                        },
                        options: {
                            responsive: true,
                            plugins: {
                                title: {
                                    display: true,
                                    text: 'Requests Over Time'
                                }
                            },
                            scales: {
                                y: {
                                    beginAtZero: true
                                }
                            }
                        }
                    });
                });
            </script>
        </section>
    </main>
    <footer class="wf-footer">
        <p>&copy; 2023 WasteFlow. All rights reserved.</p>
    </footer>
</body>
</html>
