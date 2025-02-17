<?php
@include '../../../DataForge/AdminData/Aindex_set.php';
@include '../../../DataForge/AdminData/config.php';

if (!$conn) {
    die("Database connection failed: " . mysqli_connect_error());
}

// Handle search functionality
$searchQuery = '';
if (isset($_POST['search'])) {
    $searchQuery = mysqli_real_escape_string($conn, $_POST['search']);
    $query = "SELECT * FROM log_detail WHERE type='admin' 
              AND (uname LIKE '%$searchQuery%' 
              OR eid LIKE '%$searchQuery%' 
              OR status like '%$searchQuery%')";
} else {
    $query = "SELECT * FROM log_detail WHERE type='admin'";
}

// Handle delete request
if (isset($_GET['delete_id'])) {
    $deleteId = intval($_GET['delete_id']);
    mysqli_query($conn, "DELETE FROM log_detail WHERE id = $deleteId") or die("Error deleting record: " . mysqli_error($conn));
    header("Location: admin.php");
    exit();
}

// Handle block request
if (isset($_GET['block_id'])) {
    $blockId = intval($_GET['block_id']);
    mysqli_query($conn, "UPDATE log_detail SET status = 'blocked' WHERE id = $blockId") or die("Error blocking record: " . mysqli_error($conn));
    header("Location: admin.php");
    exit();
}

$result = mysqli_query($conn, $query) or die("Error in query: " . mysqli_error($conn));
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../../Style/Admin/setting.css">
    <style>
    .container {
        max-width: 1200px;
        margin: 20px auto;
        padding: 20px;
        background-color: #fff;
        border-radius: 10px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
    }

    .search-form {
        display: flex;
        justify-content: flex-end;
        margin-bottom: 20px;
    }

    .search-form input {
        padding: 10px;
        width: 300px;
        border: 1px solid #ccc;
        border-radius: 5px 0 0 5px;
        outline: none;
    }

    .search-form button {
        padding: 10px;
        border: none;
        background-color: #0078d7;
        color: #fff;
        font-weight: bold;
        border-radius: 0 5px 5px 0;
        cursor: pointer;
        transition: background-color 0.3s;
    }

    .search-form button:hover {
        background-color: #005bb5;
    }

    table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 20px;
    }

    table thead tr {
        background-color: #0078d7;
        color: #fff;
    }

    table th,
    table td {
        padding: 15px;
        text-align: left;
        border-bottom: 1px solid #ddd;
    }

    table tbody tr:hover {
        background-color: #f1f1f1;
    }

    table td a {
        text-decoration: none;
        padding: 8px 15px;
        border-radius: 5px;
        color: #fff;
        font-size: 14px;
        margin-right: 5px;
        transition: background-color 0.3s;
    }

    table td a:hover {
        opacity: 0.9;
    }

    table td a:nth-child(1) {
        background-color: #ff4d4d;
    }

    table td a:nth-child(2) {
        background-color: #ffa500;
    }

    table td {
        vertical-align: middle;
    }

    table td:last-child {
        text-align: center;
    }

    .no-records {
        text-align: center;
        font-size: 18px;
        color: #888;
        margin-top: 20px;
    }
    </style>
</head>
<header>
    <div class="logosec">
        <div class="logo">
            <img src="../../images/blognest_logo.png" alt="BlogNest Logo" class="blognest-logo">
            BlogNest
        </div>
        <a href="index.php">
            <svg xmlns="http://www.w3.org/2000/svg" class="icn menuicn" id="menuicn" alt="menu-icon" fill="none"
                viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" style="height: 30px; width: 30px;">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
            </svg>
        </a>

    </div>


    <div class="message">
        <div class="circle"></div>
        <div class="header_profile">
            <a href="./setting.php" class="profile_image_container">
                <img src="<?php echo htmlspecialchars($_SESSION['profile_image'] ?: '../../../Frontify/Images/Person.jpg'); ?>"
                    alt="Profile Picture" class="profile_image">
            </a>
            <span class="username"><?php echo htmlspecialchars($_SESSION['user_name']); ?></span>
        </div>
    </div>
</header>

<body>

    <div class="container">
        <form method="post" class="search-form"><input type="text" name="search"
                placeholder="Search by username, email, or status"
                value="<?php echo htmlspecialchars($searchQuery); ?>" /><button type="submit">Search</button></form>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Username</th>
                    <th>Email</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody><?php if (mysqli_num_rows($result) > 0): ?><?php while ($row=mysqli_fetch_assoc($result)): ?><tr>
                    <td><?php echo $row['id'];
    ?></td>
                    <td><?php echo htmlspecialchars($row['uname']);
    ?></td>
                    <td><?php echo htmlspecialchars($row['eid']);
    ?></td>
                    <td><?php echo htmlspecialchars($row['status']);
    ?></td>
                    <td><a href="setting.php?delete_id=<?php echo $row['id']; ?>"
                            onclick="return confirm('Are you sure you want to delete this admin?');">Delete</a><a
                            href="setting.php?block_id=<?php echo $row['id']; ?>"
                            onclick="return confirm('Are you sure you want to block this admin?');">Block</a></td>
                </tr><?php endwhile;
    ?><?php else: ?><tr>
                    <td colspan="5">No records found.</td>
                </tr><?php endif;
    ?></tbody>
        </table>
    </div>
</body>

</html>