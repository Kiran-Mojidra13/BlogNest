<?php
    @include  '../../../DataForge/UserData/insert.php';
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>register form</title>

    <!-- custom css file link  -->
    <link rel="stylesheet" type="text/css" href="../CSS/Enterance/regi_Style.css">
    <style>
    .form-container img {
        width: 120px;
        height: 120px;
        border-radius: 50%;
        margin-bottom: 10px;
    }
    </style>

</head>

<body>

    <div class="form-container">

        <form action="" method="post">
            <img src="../../Images/BlogNest_LOGO.png" alt="BLOGNEST LOGO">
            <h3>register now</h3>

            <?php
      if(isset($error)){
         foreach($error as $error){
            echo '<span class="error-msg">'.$error.'</span>';
         };
      };
      ?>


            <input type="text" name="name" required placeholder="enter your name">
            <input type="email" name="email" required placeholder="enter your email">
            <input type="password" name="password" required placeholder="enter your password">
            <input type="password" name="cpassword" required placeholder="confirm your password">
            <select name="user_type">
                <option value="user">user</option>
                <option value="admin">admin</option>
            </select>
            <input type="submit" name="submit" value="register now" class="form-btn">
            <p>already have an account? <a href="./login.php">login now</a></p>
        </form>

    </div>

</body>

</html>