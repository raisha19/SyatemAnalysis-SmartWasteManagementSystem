<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>WasteFlow — Smart Bin Monitoring (Output)</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="style.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
</head>
<body class="wf-body">
    <header class="wf-header">
        <div class="wf-title">WasteFlow — Smart Bin Monitoring (Output)</div>
        <nav class="wf-nav">
            <a href="dashboard.html">Dashboard</a>
            <a href="bin_input.php">Input</a>
        </nav>
    </header>

    <main class="wf-main">
        <section class="wf-section">
            <h2 class="wf-h2">Smart Bin Status Overview</h2>
            
            <form class="wf-form compact" method="GET" action="bin_output.php">
                <div class="wf-grid-4">
                    <div class="wf-field">
                        <label for="fFillLevel">Fill Level</label>
                        <select id="fFillLevel" name="fill_level" onchange="this.form.submit()">
                            <option value="">All</option>
                            <option value="Empty" <?php echo (isset($_GET['fill_level']) && $_GET['fill_level'] == 'Empty') ? 'selected' : ''; ?>>Empty</option>
                            <option value="Medium" <?php echo (isset($_GET['fill_level']) && $_GET['fill_level'] == 'Medium') ? 'selected' : ''; ?>>Medium</option>
                            <option value="Full" <?php echo (isset($_GET['fill_level']) && $_GET['fill_level'] == 'Full') ? 'selected' : ''; ?>>Full</option>
                        </select>
                    </div>
                    <div class="wf-field">
                        <label for="fWard">Ward</label>
                        <select id="fWard" name="ward" onchange="this.form.submit()">
                            <option value="">All</option>
                            <option value="Ward 1" <?php echo (isset($_GET['ward']) && $_GET['ward'] == 'Ward 1') ? 'selected' : ''; ?>>Ward 1</option>
                            <option value="Ward 2" <?php echo (isset($_GET['ward']) && $_GET['ward'] == 'Ward 2') ? 'selected' : ''; ?>>Ward 2</option>
                            <option value="Ward 3" <?php echo (isset($_GET['ward']) && $_GET['ward'] == 'Ward 3') ? 'selected' : ''; ?>>Ward 3</option>
                            <option value="Ward 4" <?php echo (isset($_GET['ward']) && $_GET['ward'] == 'Ward 4') ? 'selected' : ''; ?>>Ward 4</option>
                        </select>
                    </div>
                    <div class="wf-field">
                        <label for="fSearch">Search Bin ID</label>
                        <input id="fSearch" name="search" type="text" placeholder="Type to filter..." value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>" />
                    </div>
                    <div class="wf-actions end">
                        <button type="submit" class="wf-btn primary">Filter</button>
                        <a href="bin_output.php" class="wf-btn ghost">Reset</a>
                    </div>
                </div>
            </form>

            <div class="table-scroll">
                <table class="wf-table">
                    <thead>
                        <tr>
                            <th>Bin ID</th>
                            <th>Ward</th>
                            <th>Fill Level</th>
                            <th>Last Collection</th>
                            <th>Notes</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        include 'db.php';
                        
                        $sql = "SELECT * FROM bins WHERE 1=1";
                        $params = [];
                        $types = "";

                        if (isset($_GET['fill_level']) && !empty($_GET['fill_level'])) {
                            $sql .= " AND fill_level = ?";
                            $params[] = $_GET['fill_level'];
                            $types .= "s";
                        }
                        if (isset($_GET['ward']) && !empty($_GET['ward'])) {
                            $sql .= " AND ward = ?";
                            $params[] = $_GET['ward'];
                            $types .= "s";
                        }
                        if (isset($_GET['search']) && !empty($_GET['search'])) {
                            $searchTerm = "%" . $_GET['search'] . "%";
                            $sql .= " AND bin_id LIKE ?";
                            $params[] = $searchTerm;
                            $types .= "s";
                        }
                        
                        $stmt = $conn->prepare($sql);
                        if (!empty($params)) {
                            $stmt->bind_param($types, ...$params);
                        }
                        $stmt->execute();
                        $result = $stmt->get_result();
                        
                        if ($result->num_rows > 0) {
                            while($row = $result->fetch_assoc()) {
                                echo "<tr>";
                                echo "<td>" . htmlspecialchars($row['bin_id']) . "</td>";
                                echo "<td>" . htmlspecialchars($row['ward']) . "</td>";
                                echo "<td>" . htmlspecialchars($row['fill_level']) . "</td>";
                                echo "<td>" . htmlspecialchars($row['last_collected']) . "</td>";
                                echo "<td>" . htmlspecialchars($row['notes']) . "</td>";
                                echo "<td>
                                        <a href='bin_input.php?edit=" . $row['bin_id'] . "' class='wf-btn small edit'>Edit</a>
                                        <a href='bin_handler.php?delete=" . $row['bin_id'] . "' onclick=\"return confirm('Are you sure you want to delete this bin?');\" class='wf-btn small delete'>Delete</a>
                                    </td>";
                                echo "</tr>";
                            }
                        } else {
                            echo "<tr><td colspan='6'>No bins found.</td></tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </section>
        
        <section class="wf-section">
            <div class="wf-grid-3" style="display: flex; gap: 20px;">
                <div class="chart-box" style="flex: 1;">
                    <h3>Bin Fill Level Distribution</h3>
                    <canvas id="binChart" height="200"></canvas>
                </div>
                <div class="chart-box" style="flex: 1;">
                    <h3>Bins per Ward</h3>
                    <canvas id="wardBinChart" height="200"></canvas>
                </div>
                <div class="chart-box" style="flex: 1;">
                    <h3>Collections Over Time</h3>
                    <canvas id="collectionsChart" height="200"></canvas>
                </div>
            </div>
        </section>

        <?php
        // Fetch data for charts
        $chart_data = [];
        
        // Bin Fill Level Distribution
        $sql_levels = "SELECT fill_level, COUNT(*) as count FROM bins GROUP BY fill_level";
        $result_levels = $conn->query($sql_levels);
        while ($row = $result_levels->fetch_assoc()) {
            $chart_data['binLevels'][$row['fill_level']] = $row['count'];
        }

        // Bins per Ward
        $sql_wards = "SELECT ward, COUNT(*) as count FROM bins GROUP BY ward";
        $result_wards = $conn->query($sql_wards);
        while ($row = $result_wards->fetch_assoc()) {
            $chart_data['wards'][$row['ward']] = $row['count'];
        }

        // Bins by Last Collection Date (for line chart)
        $sql_collections = "SELECT last_collected, COUNT(*) as count FROM bins GROUP BY last_collected ORDER BY last_collected ASC";
        $result_collections = $conn->query($sql_collections);
        $chart_data['collections'] = [];
        while ($row = $result_collections->fetch_assoc()) {
            $chart_data['collections'][$row['last_collected']] = $row['count'];
        }

        $conn->close();
        ?>

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
            lightBlue: '#9ce6e6'
        };

        // Fill Level Chart (Doughnut)
        new Chart(document.getElementById('binChart'), {
            type: 'doughnut',
            data: {
                labels: Object.keys(chartData.binLevels),
                datasets: [{
                    data: Object.values(chartData.binLevels),
                    backgroundColor: [soothingColors.green, soothingColors.yellow, soothingColors.red],
                    borderColor: '#ffffff',
                    borderWidth: 2
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: { position: 'bottom' },
                    tooltip: {
                        backgroundColor: 'rgba(0, 0, 0, 0.7)',
                        bodyFont: { size: 14 }
                    }
                }
            }
        });

        // Bins per Ward Chart (Bar)
        new Chart(document.getElementById('wardBinChart'), {
            type: 'bar',
            data: {
                labels: Object.keys(chartData.wards),
                datasets: [{
                    label: 'Number of Bins',
                    data: Object.values(chartData.wards),
                    backgroundColor: soothingColors.blue,
                    borderColor: 'rgba(0,0,0,0)',
                    borderWidth: 1,
                    borderRadius: 5
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

        // Collections Over Time Chart (Line)
        new Chart(document.getElementById('collectionsChart'), {
            type: 'line',
            data: {
                labels: Object.keys(chartData.collections),
                datasets: [{
                    label: 'Daily Collections',
                    data: Object.values(chartData.collections),
                    borderColor: soothingColors.lightBlue,
                    backgroundColor: 'rgba(156, 230, 230, 0.2)', // Light fill color
                    borderWidth: 2,
                    pointRadius: 5,
                    pointBackgroundColor: soothingColors.lightBlue,
                    tension: 0.4, // Makes the line curved
                    fill: true
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: 'rgba(0, 0, 0, 0.7)',
                        bodyFont: { size: 14 }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true
                    },
                    x: {
                        ticks: {
                            // Display only the date part
                            callback: function(val, index) {
                                return this.getLabelForValue(val);
                            }
                        }
                    }
                }
            }
        });
    </script>
</body>
</html>
