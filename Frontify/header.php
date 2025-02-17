<?php
    @include '../DataForge/UserData/index_Set.php';
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="./Style/user/header.css">
    <title>Document</title>
</head>
<header class=" header" id="header">
    <div class="header_toggle"><i class='bx bx-menu' id="header-toggle"></i></div>
    <div class="header_profile">
        <!-- Profile Image --><a href="./setting.php" class="profile_image_container"><img
                src="<?php echo htmlspecialchars($_SESSION['profile_image']); ?>" alt="Profile Picture"
                class="profile_image"></a>
        <!-- Username --><span class="username"><?php echo htmlspecialchars($_SESSION['user_name']);
    ?></span>
    </div>
</header>

<body>

</body>
<script src="../../Script/user/create.js"></script>

</html>