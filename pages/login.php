
    <style>
   

        h1 {
            text-align: center;
            margin-top: 50px;
        }

        form {
            max-width: 400px;
            margin: 0 auto;
            background-color: #ffffff;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }

        label {
            display: block;
            margin-bottom: 10px;
        }

        input[type="email"],
        input[type="password"] {
            width: 100%;
            padding: 10px;
            margin-bottom: 20px;
            border: 1px solid #ccc;
            border-radius: 4px;
            box-sizing: border-box;
        }

        button[type="submit"] {
            width: 100%;
            padding: 10px;
            background-color: #4caf50;
            color: #ffffff;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }

        button[type="submit"]:hover {
            background-color: #45a049;
        }

        form>p {
            color: #ff0000;
            margin-top: 10px;
        }
    </style>
</head>
<body>
    <h1>Lecturer Login</h1>

    <form id="loginForm" method="post" action="">
        <label for="email">Email:</label>
        <input type="email" id="email" name="email" required>

        <label for="password">Password:</label>
        <input type="password" id="password" name="password" required>

        <button type="submit">Login</button>

        <?php
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $email = $_POST["email"];
            $password = $_POST["password"];

            // Check if the connection was successful
            if ($conn->connect_error) {
                die("Connection failed: " . $conn->connect_error);
            }

            // Prepare the SQL statement
            $stmt = $conn->prepare("SELECT * FROM lecturer WHERE email = ? AND password = ?");
            $stmt->bind_param("ss", $email, $password);

            // Execute the statement
            $stmt->execute();

            // Fetch the result
            $result = $stmt->get_result();

            // Check if a matching lecturer was found
            if ($result->num_rows == 1) {
                // Authentication successful
                $lecturer = $result->fetch_assoc();

                // Start the session
                session_start();

                // Set session variables
                $_SESSION["logged_in"] = true;
                $_SESSION["user_id"] = $lecturer["lecturer_id"];
                $_SESSION["user_name"] = $lecturer["name"];
                $_SESSION["user_email"] = $lecturer["email"];

                // Redirect the user to another page after successful login
                header("location: index.php?page=dashboard");
                exit();
            } else {
                // Authentication failed
                $message = "Invalid email or password. Please try again.";
            }

            // Close the statement and connection
            $stmt->close();
            $conn->close();
        }
        ?>

        <?php if (!empty($message)) : ?>
            <p><?php echo $message; ?></p>
        <?php endif; ?>
    </form>
