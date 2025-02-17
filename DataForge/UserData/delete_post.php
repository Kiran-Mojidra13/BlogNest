<?php
@include 'config.php';
session_start();

// Check if the user is logged in
if (!isset($_SESSION['eid'])) {
    echo "Unauthorized";
    exit();
}

// Check if the ID is provided via POST
if (isset($_POST['id']) && is_numeric($_POST['id'])) {
    $postId = (int) $_POST['id'];

    // Prepare the SQL query to delete the post
    $query = "DELETE FROM post_manage WHERE id = ?";
    $stmt = mysqli_prepare($conn, $query);

    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "i", $postId);
        $deleteResult = mysqli_stmt_execute($stmt);

        if ($deleteResult) {
            echo "success";
        } else {
            echo "Error deleting the post.";
        }

        mysqli_stmt_close($stmt);
    } else {
        echo "Error preparing the query.";
    }
} else {
    echo "Invalid post ID.";
}

// Close the connection
mysqli_close($conn);
?>