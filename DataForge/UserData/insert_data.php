<?php
@include 'config.php';
@include './index_Set.php';
session_start();

// Check if the database connection is established
if (!$conn) {
    die("Database connection is not established.");
}

// Check if the user is logged in
if (!isset($_SESSION['eid'])) {
    header('Location: ../../../Frontify/Enterance/login.php');
    exit();
}

// Get the logged-in user's name
$username = $_SESSION['user_name'] ?? "Guest";

//
?>