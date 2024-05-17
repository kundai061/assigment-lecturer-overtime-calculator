<?php
// Establish a database connection
$host = 'your_host';
$dbname = 'your_database';
$username = 'your_username';
$password = 'your_password';

// Create a new mysqli object
$mysqli = new mysqli($host, $username, $password, $dbname);

// Check the connection
if ($mysqli->connect_errno) {
    echo "Connection failed: " . $mysqli->connect_error;
    exit();
}

// Function to create earnings
function createEarnings($lecturerId, $sessionId, $date, $earningsAmount) {
    global $mysqli;
    $query = "INSERT INTO Earnings (lecturer_id, session_id, date, earnings_amount) VALUES (?, ?, ?, ?)";
    $stmt = $mysqli->prepare($query);
    $stmt->bind_param("iisd", $lecturerId, $sessionId, $date, $earningsAmount);
    $stmt->execute();
    return $mysqli->insert_id;
}

// Function to retrieve earnings by ID
function getEarningsById($earningsId) {
    global $mysqli;
    $query = "SELECT * FROM Earnings WHERE earnings_id = ?";
    $stmt = $mysqli->prepare($query);
    $stmt->bind_param("i", $earningsId);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->fetch_assoc();
}

// Function to update earnings
function updateEarnings($earningsId, $lecturerId, $sessionId, $date, $earningsAmount) {
    global $mysqli;
    $query = "UPDATE Earnings SET lecturer_id = ?, session_id = ?, date = ?, earnings_amount = ? WHERE earnings_id = ?";
    $stmt = $mysqli->prepare($query);
    $stmt->bind_param("iisdi", $lecturerId, $sessionId, $date, $earningsAmount, $earningsId);
    return $stmt->execute();
}

// Function to delete earnings
function deleteEarnings($earningsId) {
    global $mysqli;
    $query = "DELETE FROM Earnings WHERE earnings_id = ?";
    $stmt = $mysqli->prepare($query);
    $stmt->bind_param("i", $earningsId);
    return $stmt->execute();
}

// Example usage

// Create earnings
$newEarningsId = createEarnings(1, 1, "2024-04-01", 100.50);
echo "New Earnings ID: " . $newEarningsId;

// Retrieve earnings by ID
$earningsId = 1; // Example earnings ID
$earnings = getEarningsById($earningsId);
print_r($earnings);

// Update earnings
$updateSuccess = updateEarnings(1, 2, 2, "2024-04-02", 150.75);
if ($updateSuccess) {
    echo "Earnings updated successfully.";
}

// Delete earnings
$deleteSuccess = deleteEarnings(1);
if ($deleteSuccess) {
    echo "Earnings deleted successfully.";
}

// Close the database connection
$mysqli->close();
?>