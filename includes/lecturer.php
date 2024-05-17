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

// Function to retrieve all lecturers
function getAllLecturers() {
    global $mysqli;
    $query = "SELECT * FROM Lecturer";
    $result = $mysqli->query($query);
    return $result->fetch_all(MYSQLI_ASSOC);
}

// Function to create a new lecturer
function createLecturer($name, $email, $password) {
    global $mysqli;
    $query = "INSERT INTO Lecturer (name, email, password) VALUES (?, ?, ?)";
    $stmt = $mysqli->prepare($query);
    $stmt->bind_param("sss", $name, $email, $password);
    $stmt->execute();
    return $mysqli->insert_id;
}

// Function to retrieve a specific lecturer
function getLecturerById($lecturerId) {
    global $mysqli;
    $query = "SELECT * FROM Lecturer WHERE lecturer_id = ?";
    $stmt = $mysqli->prepare($query);
    $stmt->bind_param("i", $lecturerId);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->fetch_assoc();
}

// Example usage

// Retrieve all lecturers
$lecturers = getAllLecturers();
print_r($lecturers);

// Create a new lecturer
$newLecturerId = createLecturer("John Doe", "john.doe@example.com", "password123");
echo "New Lecturer ID: " . $newLecturerId;

// Retrieve a specific lecturer
$lecturerId = 1; // Example lecturer ID
$lecturer = getLecturerById($lecturerId);
print_r($lecturer);

// Close the database connection
$mysqli->close();
?>