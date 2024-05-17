<?php
session_start();
 include("./conn.php") ?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Lecturer Landing Page</title>
  <link rel="stylesheet" href="./assets/css/style.css">
</head>
<body>
  <header>
  <nav>
  <ul class="nav-links">
    <li><a href="index.php">Home</a></li>
    <li><a href="#">About</a></li>
    <li><a href="#">Services</a></li>
    <li><a href="#">Contact</a></li>
  </ul>
</nav>
  </header>
  
  <main>
  <?php
    $allowedPages = array("home", "about", "contact", "login", "register", "dashboard", "success"); // List of allowed page names

    $page = isset($_GET["page"]) ? $_GET["page"] : "home"; // Default to "home" if page parameter is not set
    $page = strtolower($page); // Convert page name to lowercase for consistency

    // Check if the requested page is in the list of allowed pages
    if (in_array($page, $allowedPages)) {
        $pagePath = "pages/" . $page . ".php";
        include($pagePath);
    } else {
        // Page not found, handle the error accordingly
        include("pages/error.php");
    }
?>
<br><br>
  </main>
  
  <footer>
  <div class="footer-content">
    <div class="footer-left">
      <h3>About </h3>
      <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit.</p>
    </div>
    <div class="footer-right">
      <h3>Contact </h3>
      <p>Email: example@example.com</p>
      <p>Phone: 123-456-7890</p>
    </div>
  </div>
  <div class="footer-bottom">
  <p>&copy; 2024 University Name. All rights reserved.</p>
  </div>
</footer>
</body>
</html>