<?php
include 'db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $action = $_POST['action'];
    
    // Sanitize and validate inputs
    $binId = htmlspecialchars($_POST['binId']);
    $ward = htmlspecialchars($_POST['ward']);
    $fillLevel = htmlspecialchars($_POST['binLevel']);
    $lastCollected = htmlspecialchars($_POST['lastCollected']);
    $notes = htmlspecialchars($_POST['notes']);

    if ($action == 'add') {
        $sql = "INSERT INTO bins (bin_id, ward, fill_level, last_collected, notes) VALUES (?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssss", $binId, $ward, $fillLevel, $lastCollected, $notes);
        $stmt->execute();
    } elseif ($action == 'update') {
        $originalBinId = htmlspecialchars($_POST['original_bin_id']);
        $sql = "UPDATE bins SET ward = ?, fill_level = ?, last_collected = ?, notes = ? WHERE bin_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssss", $ward, $fillLevel, $lastCollected, $notes, $originalBinId);
        $stmt->execute();
    }
} elseif ($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET['delete'])) {
    $binIdToDelete = htmlspecialchars($_GET['delete']);
    $sql = "DELETE FROM bins WHERE bin_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $binIdToDelete);
    $stmt->execute();
}

$conn->close();

// Redirect back to the output page
header("Location: bin_output.php");
exit();
?>
