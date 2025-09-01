<?php
include 'db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nid = $_POST['loginNid'];
    $password = $_POST['loginPassword'];

    $sql = "SELECT * FROM users WHERE nid=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $nid);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();

        if (password_verify($password, $user['password_hash'])) {
            session_start();
            $_SESSION['user_id'] = $user['user_id'];
            $_SESSION['role'] = $user['role'];

            if ($user['role'] == 'Resident') {
                header("Location: dashboard_resident.php");
            } elseif ($user['role'] == 'Collector') {
                header("Location: dashboard_collector.php");
            } elseif ($user['role'] == 'Admin') {
                header("Location: dashboard_admin.php");
            }
            exit();
        } else {
            echo "<script>alert('Invalid password.'); window.location='index.html';</script>";
        }
    } else {
        echo "<script>alert('NID not found.'); window.location='index.html';</script>";
    }

    $stmt->close();
}
$conn->close();
?>
