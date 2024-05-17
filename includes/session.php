<?php

// Check the connection
if ($conn->connect_errno) {
    echo "Connection failed: " . $conn->connect_error;
    exit();
}

// Function to create a new session
function createSession($lecturerId, $startTime, $endTime) {
    global $conn;
    $query = "INSERT INTO Session (lecturer_id, start_time, end_time) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("issi", $lecturerId, $startTime, $endTime);
    $stmt->execute();
    return $conn->insert_id;
}

// Function to retrieve a session by ID
function getSessionById($sessionId) {
    global $conn;
    $query = "SELECT * FROM Session WHERE session_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $sessionId);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->fetch_assoc();
}

// Function to update a session
function updateSession($sessionId, $lecturerId, $topic, $startTime, $endTime) {
    global $conn;
    $query = "UPDATE Session SET lecturer_id = ?, topic = ?, start_time = ?, end_time = ? WHERE session_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("issii", $lecturerId, $topic, $startTime, $endTime, $sessionId);
    return $stmt->execute();
}

// Function to delete a session
function deleteSession($sessionId) {
    global $conn;
    $query = "DELETE FROM Session WHERE session_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $sessionId);
    return $stmt->execute();
}


// Close the database connection
$conn->close();
?>