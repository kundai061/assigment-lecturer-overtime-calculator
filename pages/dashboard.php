<?php

if(!isset( $_SESSION['user_name'])){ header("location: index.php"); }
$totalEarnings = 0;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_session'])) {
   if (isset($_POST['session_id'])) {
        // Code for deleting a session
        $sessionId = $_POST['session_id'];
        $deleteSessionQuery = "DELETE FROM Session WHERE session_id = ?";
        $stmt = $conn->prepare($deleteSessionQuery);
        $stmt->bind_param("s", $sessionId);
        $stmt->execute();
        $stmt->close();

        $deleteEarningsQuery = "DELETE FROM Earnings WHERE session_id = ?";
        $stmt = $conn->prepare($deleteEarningsQuery);
        $stmt->bind_param("s", $sessionId);
        $stmt->execute();
        $stmt->close();
        header("location: index.php?page=dashboard");
    }
}
// Handle form submissions for creating a new Session
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['create_session'])) {
    $date = $_POST['date'];
    $startTime = $_POST['startTime'];
    $endTime = $_POST['endTime'];
    $overtimeHours = $_POST['overtimeHours'] !== "" ? $_POST['overtimeHours']: $endTime;

    // Calculate duration
    $duration = calculateDuration($startTime, $endTime);
    $overtimeHours = calculateDuration($endTime, $overtimeHours);
    // Insert session into the database
    $insertSessionQuery = "INSERT INTO Session (lecturer_id, date, start_time, end_time, duration, extra_time) 
                            VALUES (?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($insertSessionQuery);
    $stmt->bind_param("ssssds", $_SESSION['user_id'], $date, $startTime, $endTime, $duration, $overtimeHours);
    $stmt->execute();
    $stmt->close();

    // Retrieve the session ID of the inserted session
    $sessionId = $conn->insert_id;

    // Calculate earnings amount
    $earningsAmount = calculateEarnings($duration, $overtimeHours);

    // Insert earnings for the session into the database
    $insertEarningsQuery = "INSERT INTO Earnings (lecturer_id, date, session_id, earnings_amount)
                            VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($insertEarningsQuery);
    $stmt->bind_param("ssdd", $_SESSION['user_id'], $date, $sessionId, $earningsAmount);
    $stmt->execute();
    $stmt->close();
    header("location: index.php?page=dashboard");
}

// Fetch sessions and earnings from the database
$selectSessionsQuery = "SELECT Session.date, Earnings.earnings_amount, Session.start_time, Session.end_time, Session.extra_time, Session.session_id, Session.duration, Earnings.earnings_amount
                        FROM Session
                        LEFT JOIN Earnings ON Session.session_id = Earnings.session_id
                        WHERE Session.lecturer_id = ? ORDER BY Session.date DESC";

$stmt = $conn->prepare($selectSessionsQuery);
$stmt->bind_param("s", $_SESSION['user_id']);
$stmt->execute();
$result = $stmt->get_result();
$sessions = [];
while ($row = $result->fetch_assoc()) {
    $sessions[] = $row;
    $totalEarnings += $row["earnings_amount"]; // Accumulate the total earnings
}
$stmt->close();

// Function to calculate the duration between two times
function calculateDuration($startTime, $endTime) {
    $start = new DateTime("2000-01-01 " . $startTime);
    $end = new DateTime("2000-01-01 " . $endTime);
    $interval = $start->diff($end);
    $duration = $interval->format("%H:%I:%S");
    return $duration;
}

// Function to calculate earnings based on duration and overtime hoursfunction calculateEarnings($duration, $overtimeHours)
function calculateEarnings($duration, $overtimeHours)
{
    $baseRate = 10; // $10 per hour
    $overTimeRate = 15; // $15 per overtime hour

    $timeParts = explode(':', $duration);
    if (count($timeParts) !== 3) {
        throw new InvalidArgumentException('Invalid duration format.');
    }

    $hours = (int)$timeParts[0];
    $minutes = (int)$timeParts[1];
    $seconds = (int)$timeParts[2];

    // Convert duration to decimal hours
    $totalHours = $hours + ($minutes / 60); // Remove seconds conversion

    // Round to 2 decimal places
    $totalHours = round($totalHours, 2);

    // Convert overtime hours to decimal hours
    $overtimeHoursDecimal = convertToDecimalHours($overtimeHours);

    // Calculate earnings based on total hours and overtime hours
    $overtimeEarnings = $overtimeHoursDecimal * $overTimeRate;
    $regularEarnings = $totalHours * $baseRate;
    $earnings = $overtimeEarnings + $regularEarnings;
    return $earnings;
}

// Function to convert overtime hours to decimal hours
function convertToDecimalHours($overtimeHours)
{
    $timeParts = explode(':', $overtimeHours);
    if (count($timeParts) !== 3) {
        throw new InvalidArgumentException('Invalid overtime hours format.');
    }

    $hours = (int)$timeParts[0];
    $minutes = (int)$timeParts[1];
    $seconds = (int)$timeParts[2];

    // Convert to decimal hours
    $decimalHours = $hours + ($minutes / 60) + ($seconds / 3600);

    return $decimalHours;
}



?>

<!DOCTYPE html>
<html>

<head>
    <title>Sessions and Earnings</title>
    <style>
        table {
            border-collapse: collapse;
            width: 100%;
        }

        th,
        td {
            border: 1px solid black;
            padding: 8px;
        }

        th {
            background-color: #f2f2f2;
        }
    </style>
</head>

<body>
    <div class="container">
        <h1>Welcome, <?php echo $_SESSION["user_name"] ?></h1>

        <h2>Add Session</h2>
        <p style="color:black">Earning $10/hour - Overtime $15/hour</p>
        <form id="sessionForm" method="POST" action="">
            <label for="date">Date:</label>
            <input type="date" id="date" name="date" required>

            <label for="startTime">Start Time:</label>
            <input type="time" id="startTime" name="startTime" required>

            <label for="endTime">End Time:</label>
            <input type="time" id="endTime" name="endTime" required>

            <label for="overtime">Overtime Hours:</label>
            <input type="time" style="padding: 8px;" id="overtimeHours" name="overtimeHours">

            <button type="submit" name="create_session" style="background-color:#0096FF">Capture Lecture Session</button>
        </form>

        <h4>Sessions and Earnings Gross Earnings <b style="color:green;">$<?php echo  number_format($totalEarnings, 2) ?></b></h4>
        <table>
            <tr>
                <th>Date</th>
                <th>Start Time</th>
                <th>End Time</th>
                <th>Duration (hr)</th>
                <th>Overtime (hr)</th>
                <th>Earnings</th>
                <th>Actions</
            </tr>
            <?php foreach ($sessions as $session) : ?>
                <tr>
                    <td><?php echo $session["date"]; ?></td>
                    <td><?php echo $session["start_time"]; ?></td>
                    <td><?php echo $session["end_time"]; ?></td>
                    <td><?php echo $session["duration"]; ?></td>
                    <td><?php echo $session["extra_time"]; ?></td>
                    <td>$<?php echo $session["earnings_amount"]; ?></td>
                    <td>
                    <form method="POST" action="">
                        <input type="hidden" name="session_id" value="<?php echo $session["session_id"]; ?>">
                        <button type="submit" name="delete_session" style="background-color:#ff5e5e;">Delete</button>
                    </form>
                    </td>
                </tr>
            <?php endforeach; ?>
        </table>
    </div>
    <center>
        <a href="logout.php">logout <span>&#8594;</span>
</a>
    </center>
    
</body>

</html>