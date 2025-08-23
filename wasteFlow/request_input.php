<!DOCTYPE html>
<html>
<head>
    <title>WasteFlow - Submit/Update Request</title>
    <link rel="stylesheet" type="text/css" href="style.css">
</head>
<body class="wf-body">
    <header class="wf-header">
        <div class="wf-title">WasteFlow</div>
        <nav class="wf-nav">
            <a href="request_input2.php" aria-current="page">Submit Request</a>
            <a href="request_output2.php">View Requests</a>
        </nav>
    </header>
    <main class="wf-main">
        <section class="wf-section">
            <h2 class="wf-h2">♻️ Waste Collection Request Form</h2>
            
            <?php
            include 'db.php';

            $edit_mode = false;
            $edit_id = "";
            $edit_residentId = "";
            $edit_location = "";
            $edit_wasteType = "";
            $edit_quantity = "";
            $edit_serviceType = "Regular";
            $edit_scheduleDate = "";
            $edit_scheduleTime = "";

            if (isset($_GET['edit'])) {
                $edit_mode = true;
                $edit_id = $_GET['edit'];

                $sql = "SELECT * FROM requests WHERE id = ?";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("i", $edit_id);
                $stmt->execute();
                $result = $stmt->get_result();
                $row = $result->fetch_assoc();

                if ($row) {
                    $edit_residentId = $row['resident_id'];
                    $edit_location = $row['location'];
                    $edit_wasteType = $row['waste_type'];
                    $edit_quantity = $row['quantity'];
                    $edit_serviceType = $row['service_type'];
                    $edit_scheduleDate = $row['schedule_adate'];
                    $edit_scheduleTime = $row['schedule_time'];
                }
                $stmt->close();
            }
            ?>

            <form method="POST" action="<?php echo $edit_mode ? 'request_update.php' : 'request_insert.php'; ?>" class="wf-form">
                <input type="hidden" name="id" value="<?php echo htmlspecialchars($edit_id); ?>">
                
                <label for="residentId">Resident ID:</label>
                <input type="text" id="residentId" name="residentId" value="<?php echo htmlspecialchars($edit_residentId); ?>" required>
                
                <label for="location">Location / Ward:</label>
                <select id="location" name="location" required>
                    <option value="">Select Ward</option>
                    <option value="Ward 1" <?php echo ($edit_location == 'Ward 1') ? 'selected' : ''; ?>>Ward 1</option>
                    <option value="Ward 2" <?php echo ($edit_location == 'Ward 2') ? 'selected' : ''; ?>>Ward 2</option>
                    <option value="Ward 3" <?php echo ($edit_location == 'Ward 3') ? 'selected' : ''; ?>>Ward 3</option>
                    <option value="Ward 4" <?php echo ($edit_location == 'Ward 4') ? 'selected' : ''; ?>>Ward 4</option>
                </select>

                <label for="wasteType">Waste Type:</label>
                <select id="wasteType" name="wasteType" required>
                    <option value="">Select Type</option>
                    <option value="Organic" <?php echo ($edit_wasteType == 'Organic') ? 'selected' : ''; ?>>Organic</option>
                    <option value="Plastic" <?php echo ($edit_wasteType == 'Plastic') ? 'selected' : ''; ?>>Plastic</option>
                    <option value="E-Waste" <?php echo ($edit_wasteType == 'E-Waste') ? 'selected' : ''; ?>>E-Waste</option>
                    <option value="Medical" <?php echo ($edit_wasteType == 'Medical') ? 'selected' : ''; ?>>Medical</option>
                    <option value="Others" <?php echo ($edit_wasteType == 'Others') ? 'selected' : ''; ?>>Others</option>
                </select>

                <label for="quantity">Quantity (kg):</label>
                <input type="number" id="quantity" name="quantity" value="<?php echo htmlspecialchars($edit_quantity); ?>" required>

                <label>Service Type:</label>
                <div style="display: flex; gap: 10px;">
                    <input type="radio" id="regular" name="serviceType" value="Regular" <?php echo ($edit_serviceType == 'Regular') ? 'checked' : ''; ?>>
                    <label for="regular">Regular</label>
                    <input type="radio" id="express" name="serviceType" value="Express" <?php echo ($edit_serviceType == 'Express') ? 'checked' : ''; ?>>
                    <label for="express">Express</label>
                </div>

                <label for="scheduleDate">Preferred Date:</label>
                <input type="date" id="scheduleDate" name="scheduleDate" value="<?php echo htmlspecialchars($edit_scheduleDate); ?>" required>

                <label for="scheduleTime">Preferred Time:</label>
                <input type="time" id="scheduleTime" name="scheduleTime" value="<?php echo htmlspecialchars($edit_scheduleTime); ?>" required>

                <div class="wf-actions">
                    <button type="submit" class="wf-btn primary"><?php echo $edit_mode ? 'Update Request' : 'Add Request'; ?></button>
                    <a href="request_output.php" class="wf-btn ghost">View All Requests</a>
                </div>
            </form>
        </section>
    </main>
    <footer class="wf-footer">
        <p>&copy; 2023 WasteFlow. All rights reserved.</p>
    </footer>
</body>
</html>
