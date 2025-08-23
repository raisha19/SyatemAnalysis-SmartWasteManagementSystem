<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>WasteFlow — User Management (Output)</title>
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <link rel="stylesheet" href="style.css" />
  <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
</head>
<body class="wf-body">
  <header class="wf-header">
    <div class="wf-title">WasteFlow — User Management (Output)</div>
    <nav class="wf-nav">
      <a href="dashboard.html">Dashboard</a>
      <a href="user_input.php">Input</a>
    </nav>
  </header>

  <main class="wf-main">
    <section class="wf-section">
      <h2 class="wf-h2">Registered Users</h2>
      
      <!-- Filters -->
      <form class="wf-form compact" method="GET" action="user_output.php">
        <div class="wf-grid-4">
          <div class="wf-field">
            <label for="fRole">Role</label>
            <select id="fRole" name="role" onchange="this.form.submit()">
              <option value="">All</option>
              <option value="Resident" <?php echo (isset($_GET['role']) && $_GET['role'] == 'Resident') ? 'selected' : ''; ?>>Resident</option>
              <option value="Collector" <?php echo (isset($_GET['role']) && $_GET['role'] == 'Collector') ? 'selected' : ''; ?>>Collector</option>
              <option value="Admin" <?php echo (isset($_GET['role']) && $_GET['role'] == 'Admin') ? 'selected' : ''; ?>>Admin</option>
            </select>
          </div>
          <div class="wf-field">
            <label for="fWard">Ward</label>
            <input id="fWard" name="ward" type="number" min="1" max="100" placeholder="e.g., 12" value="<?php echo isset($_GET['ward']) ? htmlspecialchars($_GET['ward']) : ''; ?>" />
          </div>
          <div class="wf-field">
            <label for="fSearch">Search Name/Mobile</label>
            <input id="fSearch" name="search" type="text" placeholder="Type to filter..." value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>" />
          </div>
          <div class="wf-actions end">
            <button type="submit" class="wf-btn primary">Filter</button>
            <a href="user_output.php" class="wf-btn ghost">Reset</a>
          </div>
        </div>
      </form>

      <!-- Table -->
      <div class="table-scroll">
        <table class="wf-table" id="usersTable">
          <thead>
            <tr>
              <th>User ID</th>
              <th>Name</th>
              <th>Role</th>
              <th>Ward</th>
              <th>Mobile</th>
              <th>Status</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            <?php
            include 'db.php';

            $sql = "SELECT * FROM users WHERE 1=1";
            $params = [];
            $types = "";

            if (isset($_GET['role']) && !empty($_GET['role'])) {
                $sql .= " AND role = ?";
                $params[] = $_GET['role'];
                $types .= "s";
            }
            if (isset($_GET['ward']) && !empty($_GET['ward'])) {
                $sql .= " AND ward = ?";
                $params[] = $_GET['ward'];
                $types .= "i";
            }
            if (isset($_GET['search']) && !empty($_GET['search'])) {
                $searchTerm = "%" . $_GET['search'] . "%";
                $sql .= " AND (full_name LIKE ? OR mobile LIKE ?)";
                $params[] = $searchTerm;
                $params[] = $searchTerm;
                $types .= "ss";
            }
            
            $stmt = $conn->prepare($sql);
            if (!empty($params)) {
                $stmt->bind_param($types, ...$params);
            }
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    echo "<tr>";
                    echo "<td>" . htmlspecialchars($row['id']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['full_name']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['role']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['ward']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['mobile']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['status']) . "</td>";
                    echo "<td>
                            <a href='user_input.php?edit=" . $row['id'] . "' class='wf-btn small edit'>Edit</a>
                            <a href='user_handler.php?delete=" . $row['id'] . "' onclick=\"return confirm('Are you sure you want to delete this user?');\" class='wf-btn small delete'>Delete</a>
                          </td>";
                    echo "</tr>";
                }
            } else {
                echo "<tr><td colspan='7'>No users found.</td></tr>";
            }
            ?>
          </tbody>
        </table>
      </div>
    </section>

    <!-- Charts -->
    <section class="wf-section">
      <div class="wf-grid-2" style="display: flex; gap: 20px;">
        <div class="chart-box" style="flex: 1;">
          <h3>User Roles Distribution</h3>
          <canvas id="roleChart" height="200"></canvas>
        </div>
        <div class="chart-box" style="flex: 1;">
          <h3>Users by Ward (Top)</h3>
          <canvas id="wardChart" height="200"></canvas>
        </div>
      </div>
    </section>

    <?php
    // --- PHP logic to fetch data for charts ---
    $data = [
      'roleCounts' => [],
      'wardCounts' => []
    ];

    // Data for Role Distribution Chart
    $sql_roles = "SELECT role, COUNT(*) as count FROM users GROUP BY role";
    $result_roles = $conn->query($sql_roles);
    while ($row = $result_roles->fetch_assoc()) {
      $data['roleCounts'][$row['role']] = $row['count'];
    }

    // Data for Users by Ward Chart
    $sql_wards = "SELECT ward, COUNT(*) as count FROM users GROUP BY ward ORDER BY count DESC LIMIT 5";
    $result_wards = $conn->query($sql_wards);
    while ($row = $result_wards->fetch_assoc()) {
      $data['wardCounts'][$row['ward']] = $row['count'];
    }
    
    $conn->close();
    ?>

    <script>
        const chartData = <?php echo json_encode($data); ?>;
        
        // Soothing colors based on the image provided
        const pieColors = ['#5cb85c', '#66a3d2', '#ffc107', '#ff85a1', '#808080'];
        const barColors = ['#9ce6e6', '#b388ff'];

        // ========== User Roles Distribution (Pie Chart) ==========
        new Chart(document.getElementById('roleChart'), {
          type: 'pie',
          data: {
            labels: Object.keys(chartData.roleCounts),
            datasets: [{ 
              data: Object.values(chartData.roleCounts),
              backgroundColor: pieColors,
              borderColor: '#ffffff',
              borderWidth: 2
            }]
          },
          options: { 
            responsive: true,
            plugins: { 
              legend: { 
                position: 'bottom',
                labels: {
                  font: {
                    size: 14
                  },
                  color: '#333'
                }
              },
              tooltip: {
                backgroundColor: 'rgba(0, 0, 0, 0.7)',
                bodyFont: {
                  size: 14
                }
              }
            }
          }
        });

        // ========== Users by Ward (Bar Chart) ==========
        new Chart(document.getElementById('wardChart'), {
          type: 'bar',
          data: {
            labels: Object.keys(chartData.wardCounts).map(ward => `Ward ${ward}`),
            datasets: [{ 
              label: 'Users', 
              data: Object.values(chartData.wardCounts),
              // Use an array of colors to match the bar chart in the image
              backgroundColor: barColors,
              borderColor: 'rgba(0,0,0,0)', // Hide border
              borderWidth: 1,
              hoverBackgroundColor: barColors,
              borderRadius: 5,
              categoryPercentage: 0.8,
              barPercentage: 0.9
            }]
          },
          options: { 
            responsive: true,
            plugins: { 
              legend: { 
                display: false 
              },
              tooltip: {
                backgroundColor: 'rgba(0, 0, 0, 0.7)',
                bodyFont: {
                  size: 14
                }
              }
            },
            scales: {
              y: {
                beginAtZero: true,
                grid: {
                  display: true,
                  color: '#e0e0e0' // Subtle grid lines
                }
              },
              x: {
                grid: {
                  display: false 
                }
              }
            }
          }
        });
    </script>
</body>
</html>
