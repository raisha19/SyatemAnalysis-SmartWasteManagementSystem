<?php
include 'db.php';

$edit_mode = false;
$edit_id = "";
$edit_fullName = "";
$edit_mobile = "";
$edit_nid = "";
$edit_role = "Resident";
$edit_ward = "";
$edit_address = "";
$edit_email = "";
$edit_pass = "";

if (isset($_GET['edit'])) {
    $edit_mode = true;
    $edit_id = $_GET['edit'];

    $sql = "SELECT * FROM users WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $edit_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();

    if ($row) {
        $edit_fullName = $row['full_name'];
        $edit_mobile = $row['mobile'];
        $edit_nid = $row['nid'];
        $edit_role = $row['role'];
        $edit_ward = $row['ward'];
        $edit_address = $row['address'];
        $edit_email = $row['email'];
        // Note: We don't fetch password for security reasons
    }
    $stmt->close();
}
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>WasteFlow — User Management (Input)</title>
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <link rel="stylesheet" href="style.css" />
</head>
<body class="wf-body">
  <header class="wf-header">
    <div class="wf-title">WasteFlow — User Management (Input)</div>
    <nav class="wf-nav">
      <a href="dashboard.html">Dashboard</a>
      <a href="user_output.php">Output</a>
    </nav>
  </header>

  <main class="wf-main">
    <!-- 1.1 Register New User -->
    <section class="wf-section">
      <h2 class="wf-h2">1.1 Register New User</h2>
      <form class="wf-form" action="user_handler.php" method="post" autocomplete="on">
        <input type="hidden" name="id" value="<?php echo htmlspecialchars($edit_id); ?>">

        <div class="wf-grid-3">
          <div class="wf-field">
            <label for="fullName">Full Name</label>
            <input id="fullName" name="fullName" type="text" placeholder="e.g., Md. Rahim" value="<?php echo htmlspecialchars($edit_fullName); ?>" required />
          </div>

          <div class="wf-field">
            <label for="mobile">Mobile (BD)</label>
            <input id="mobile" name="mobile" type="tel" inputmode="tel" placeholder="017XXXXXXXX" value="<?php echo htmlspecialchars($edit_mobile); ?>" required />
            <small class="hint">11 digits, no +88</small>
          </div>

          <div class="wf-field">
            <label for="nid">NID Number</label>
            <input id="nid" name="nid" type="text" inputmode="numeric" placeholder="10–17 digits" value="<?php echo htmlspecialchars($edit_nid); ?>" required />
          </div>

          <div class="wf-field">
            <label for="role">Role</label>
            <select id="role" name="role" required>
              <option value="Resident" <?php echo ($edit_role == 'Resident') ? 'selected' : ''; ?>>Resident</option>
              <option value="Collector" <?php echo ($edit_role == 'Collector') ? 'selected' : ''; ?>>Collector</option>
              <option value="Admin" <?php echo ($edit_role == 'Admin') ? 'selected' : ''; ?>>City Authority Admin</option>
            </select>
          </div>

          <div class="wf-field">
            <label for="ward">Ward No.</label>
            <input id="ward" name="ward" type="number" min="1" max="100" placeholder="e.g., 12" value="<?php echo htmlspecialchars($edit_ward); ?>" required />
          </div>

          <div class="wf-field">
            <label for="address">Address</label>
            <input id="address" name="address" type="text" placeholder="House, Road, Area" value="<?php echo htmlspecialchars($edit_address); ?>" required />
          </div>

          <div class="wf-field">
            <label for="email">Email (optional)</label>
            <input id="email" name="email" type="email" placeholder="name@email.com" value="<?php echo htmlspecialchars($edit_email); ?>" />
          </div>
          
          <div class="wf-field">
            <label for="pass">Password</label>
            <input id="pass" name="pass" type="password" minlength="8" placeholder="Min 8 characters" required />
          </div>
          
          <div class="wf-field">
            <label for="pass2">Confirm Password</label>
            <input id="pass2" name="pass2" type="password" minlength="8" placeholder="Re-enter password" required />
          </div>
        </div>

        <div class="wf-actions">
          <button type="submit" class="wf-btn primary"><?php echo $edit_mode ? 'Update User' : 'Create Account'; ?></button>
          <a class="wf-btn ghost" href="user_output.php">Cancel</a>
        </div>
      </form>
    </section>

    
  </main>

  <footer class="wf-footer">© 2025 WasteFlow · Dhaka City</footer>
</body>
</html>
