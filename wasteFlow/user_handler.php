<?php
include 'db.php';

// Check if a form was submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Sanitize and validate input
    $id = isset($_POST['id']) ? $_POST['id'] : null;
    $fullName = $_POST['fullName'];
    $mobile = $_POST['mobile'];
    $nid = $_POST['nid'];
    $role = $_POST['role'];
    $ward = $_POST['ward'];
    $address = $_POST['address'];
    $email = $_POST['email'];
    $password = $_POST['pass'];
    
    // Hash the password for security before storing it in the database
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    if (empty($id)) {
        // Handle new user creation (Register)
        $sql = "INSERT INTO users (full_name, mobile, nid, role, ward, address, email, password) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssssisss", $fullName, $mobile, $nid, $role, $ward, $address, $email, $hashed_password);
        if ($stmt->execute()) {
            echo "New user created successfully!";
        } else {
            echo "Error: " . $stmt->error;
        }
    } else {
        // Handle user update (Update Profile)
        $sql = "UPDATE users SET full_name=?, mobile=?, nid=?, role=?, ward=?, address=?, email=? WHERE id=?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssssissi", $fullName, $mobile, $nid, $role, $ward, $address, $email, $id);
        if ($stmt->execute()) {
            echo "User updated successfully!";
        } else {
            echo "Error: " . $stmt->error;
        }
    }
    
    $stmt->close();
} elseif ($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET['delete'])) {
    // Handle user deletion
    $id = $_GET['delete'];
    $sql = "DELETE FROM users WHERE id=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    if ($stmt->execute()) {
        echo "User deleted successfully!";
    } else {
        echo "Error: " . $stmt->error;
    }
    $stmt->close();
}

$conn->close();
// Redirect back to the output page after operation
header('Location: user_output.php');
exit;
