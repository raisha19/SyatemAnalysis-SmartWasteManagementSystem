<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>WasteFlow — Smart Bin Monitoring (Input)</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="style.css">
</head>
<body class="wf-body">
    <?php
    include 'db.php';
    
    $binId = '';
    $ward = '';
    $fillLevel = '';
    $lastCollected = '';
    $notes = '';
    $pageTitle = "Add New Smart Bin";

    // Check if an 'edit' parameter is set to pre-fill the form
    if (isset($_GET['edit'])) {
        $binIdToEdit = htmlspecialchars($_GET['edit']);
        $sql = "SELECT * FROM bins WHERE bin_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $binIdToEdit);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $binId = htmlspecialchars($row['bin_id']);
            $ward = htmlspecialchars($row['ward']);
            $fillLevel = htmlspecialchars($row['fill_level']);
            $lastCollected = htmlspecialchars($row['last_collected']);
            $notes = htmlspecialchars($row['notes']);
            $pageTitle = "Edit Bin: " . $binId;
        }
    }
    $conn->close();
    ?>

    <header class="wf-header">
        <div class="wf-title">WasteFlow — Smart Bin Monitoring (Input)</div>
        <nav class="wf-nav">
            <a href="dashboard.html">Dashboard</a>
            <a href="bin_output.php">Output</a>
        </nav>
    </header>

    <main class="wf-main">
        <section class="wf-section">
            <h2 class="wf-h2"><?php echo $pageTitle; ?></h2>
            <form class="wf-form" action="bin_handler.php" method="post">
                <input type="hidden" name="action" value="<?php echo ($binId) ? 'update' : 'add'; ?>">
                <input type="hidden" name="original_bin_id" value="<?php echo $binId; ?>">

                <div class="wf-grid-3">
                    <div class="wf-field">
                        <label for="binId">Bin ID</label>
                        <input type="text" id="binId" name="binId" placeholder="Enter Bin ID" value="<?php echo $binId; ?>" <?php echo ($binId) ? 'readonly' : 'required'; ?>>
                    </div>

                    <div class="wf-field">
                        <label for="ward">Ward / Zone</label>
                        <select id="ward" name="ward" required>
                            <option value="">Select Ward / Zone</option>
                            <option value="Ward 1" <?php echo ($ward == 'Ward 1') ? 'selected' : ''; ?>>Ward 1</option>
                            <option value="Ward 2" <?php echo ($ward == 'Ward 2') ? 'selected' : ''; ?>>Ward 2</option>
                            <option value="Ward 3" <?php echo ($ward == 'Ward 3') ? 'selected' : ''; ?>>Ward 3</option>
                            <option value="Ward 4" <?php echo ($ward == 'Ward 4') ? 'selected' : ''; ?>>Ward 4</option>
                        </select>
                    </div>

                    <div class="wf-field">
                        <label for="binLevel">Bin Fill Level</label>
                        <select id="binLevel" name="binLevel" required>
                            <option value="">Select Level</option>
                            <option value="Empty" <?php echo ($fillLevel == 'Empty') ? 'selected' : ''; ?>>Empty</option>
                            <option value="Medium" <?php echo ($fillLevel == 'Medium') ? 'selected' : ''; ?>>Medium</option>
                            <option value="Full" <?php echo ($fillLevel == 'Full') ? 'selected' : ''; ?>>Full</option>
                        </select>
                    </div>

                    <div class="wf-field">
                        <label for="lastCollected">Last Collection Date</label>
                        <input type="date" id="lastCollected" name="lastCollected" value="<?php echo $lastCollected; ?>" required>
                    </div>

                    <div class="wf-field">
                        <label for="notes">Notes</label>
                        <textarea id="notes" name="notes" placeholder="Any comments or observations"><?php echo $notes; ?></textarea>
                    </div>
                </div>

                <div class="wf-actions">
                    <button type="submit" class="wf-btn primary">Update Bin Status</button>
                    <a class="wf-btn ghost" href="dashboard.html">Cancel</a>
                </div>
            </form>
        </section>
    </main>

    <footer class="wf-footer">© 2025 WasteFlow · Dhaka City</footer>
</body>
</html>
