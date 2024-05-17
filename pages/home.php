<?php

if(isset($_SESSION["logged_in"])){
  header("location:index.php?page=dashboard");
}
?>
<section class="hero">
      <div class="hero-content">
        <h1>Welcome, Lecturers!</h1>
        <p>Manage your courses and schedule with ease.</p>
        <a href="index.php?page=login" class="btn">LogIn</a>
        <a href="index.php?page=register" class="btn">Register</a>
      </div>
    </section>