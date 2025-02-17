<?php
@include 'config.php';
session_start();

if (isset($_POST['submit'])) {
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $pass = md5($_POST['password']); // Use a hashed password for security
    $cpass = md5($_POST['cpassword']);
    $user_type = $_POST['user_type'];

    $select = "SELECT * FROM log_detail WHERE eid = '$email' AND password = '$pass'";
    $result = mysqli_query($conn, $select);

    if (mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_array($result);

        // Store essential session variables
        $_SESSION['eid'] = $row['eid']; // Email ID
        $_SESSION['user_name'] = $row['uname']; // User Name
        $_SESSION['user_type'] = $row['type']; // User Type (admin/user)

        if ($row['type'] === 'admin') {
            header('Location: ../../../Frontify/Main_Templete/Admin/index.php');
            exit();
        } elseif ($row['type'] === 'user') {
            header('Location: ../../../Frontify/Main_Templete/User/MainIndex.php');
            exit();
        } else {
            $error[] = 'User type is not defined!';
        }
    } else {
        $error[] = 'Incorrect email or password!';
    }
}
?>