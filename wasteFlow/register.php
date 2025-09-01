<?php
// 1. Connect to DB
$conn = new mysqli("localhost", "root", "", "wasteflow_db");

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// 2. Get POST data from form
$name     = $_POST['regName'];
$mobile   = $_POST['regMobile'];
$nid      = $_POST['regNid'];
$role     = $_POST['regRole'];
$ward     = $_POST['regWard'];
$address  = $_POST['regAddress'];
$password = password_hash($_POST['regPassword'], PASSWORD_DEFAULT); // hashed

// 3. Prepare SQL Insert
$stmt = $conn->prepare("INSERT INTO users (name, mobile, nid, role, ward, address, password) VALUES (?, ?, ?, ?, ?, ?, ?)");
$stmt->bind_param("ssssiss", $name, $mobile, $nid, $role, $ward, $address, $password);

// 4. Execute + Redirect
if ($stmt->execute()) {
    // ✅ Add this block for redirect
    header("Location: dashboard.html");
    exit(); // important to stop further execution
} else {
    echo "Error: " . $stmt->error;
}

$stmt->close();
$conn->close();
?>
