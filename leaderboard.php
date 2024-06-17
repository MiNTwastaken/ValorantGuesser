<?php
session_start();

// Database Connection
$connection = mysqli_connect("localhost:3306", "root", "", "valorantfanpage");
if (!$connection) {
    die("Connection failed: " . mysqli_connect_error());
}

// Fetch leaderboard data
$sql = "SELECT u.*, l.name AS level_name, l.total_exp AS next_level_exp, 
       RANK() OVER (ORDER BY u.lvl DESC, u.exp DESC) AS rank
       FROM user u 
       JOIN levels l ON u.lvl = l.id";

$result = mysqli_query($connection, $sql);

if (!$result) {
    die("Query failed: " . mysqli_error($connection));
}

// Check if the user is logged in and find their rank
$isLoggedIn = isset($_SESSION["username"]);
$currentUserRank = null;
$currentUserLevel = null;
$currentUserExp = null;

if ($isLoggedIn) {
    $rankSql = "SELECT u.*, l.name AS level_name, l.total_exp AS next_level_exp,
                RANK() OVER (ORDER BY u.lvl DESC, u.exp DESC) AS rank
                FROM user u
                JOIN levels l ON u.lvl = l.id
                WHERE username = ?";
    $rankStmt = mysqli_prepare($connection, $rankSql);
    mysqli_stmt_bind_param($rankStmt, "s", $_SESSION["username"]);
    mysqli_stmt_execute($rankStmt);
    $rankResult = mysqli_stmt_get_result($rankStmt);

    if ($rankResult && mysqli_num_rows($rankResult) == 1) {
        $rankRow = mysqli_fetch_assoc($rankResult);
        $currentUserRank = $rankRow['rank'];
        $currentUserLevel = $rankRow['level_name'];
        $currentUserExp = $rankRow['exp'] . '/' . $rankRow['next_level_exp'];
    }

    mysqli_stmt_close($rankStmt);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Valorant Forum - Leaderboard</title>
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #1a1a1a;
            color: #fff;
        }

        .leaderboard-container {
            background-color: #202124;
            border-radius: 0.625rem; /* 10px */
            padding: 1.25rem; /* 20px */
            max-width: 800px;
            margin: 0 auto;
        }

        .leaderboard-table th, .leaderboard-table td {
            border: 1px solid #303134;
        }

        .leaderboard-table th {
            background-color: #303134;
        }

        .leaderboard-table tr:nth-child(even) {
            background-color: #28292C;
        }

        .rank {
            color: #ff4500;
            font-weight: bold;
        }

        .current-user {
            background-color: #38393c;
        }

        .leaderboard-table th, .leaderboard-table td, h2 {
            font-size: 1rem;
        }

        .loginn-btn {
            background-color: #ff4500;
            color: #fff;
            text-decoration: none;
            border-radius: 0.3125rem; /* 5px */
            padding: 0.625rem 1.25rem; /* 10px 20px */
            display: inline-block;
            margin-top: 1rem;
        }

        .user-rank-info {
            background-color: #28292C;
            border-radius: 0.625rem; /* 10px */
            padding: 1rem; /* 16px */
            margin-bottom: 1.25rem; /* 20px */
            text-align: center;
        }

        .user-rank-info h2, .user-rank-info p {
            margin: 0;
            color: #fff;
        }

        h2.text-center {
            color: #ffffff; /* White color for heading */
            font-size: 1.5rem; /* Larger font size for heading */
            margin-bottom: 1rem;
        }

        .loginn-btn:hover {
            background-color: #e63900; /* Darker shade for hover effect */
        }
    </style>
</head>
<body>
    <script src="script.js"></script>
    <?php include 'navbar.php'; ?>
    <div class="container my-4">
        <div class="leaderboard-container">
            <?php if ($isLoggedIn && $currentUserRank !== null): ?>
                <div class="user-rank-info">
                    <h2>Your Rank</h2>
                    <p><strong><?php echo $currentUserRank; ?></strong> - <?php echo $currentUserLevel; ?> (<?php echo $currentUserExp; ?> EXP)</p>
                </div>
            <?php endif; ?>

            <h2 class="text-center">Leaderboard</h2> 
            <div class="table-responsive">
                <table class="table leaderboard-table text-white">
                    <thead>
                        <tr>
                            <th>Rank</th>
                            <th>Username</th>
                            <th>Level</th>
                            <th>EXP</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($row = mysqli_fetch_assoc($result)) : ?>
                            <tr class="<?php echo ($isLoggedIn && $row['username'] == $_SESSION["username"]) ? 'current-user' : ''; ?>">
                                <td class="rank"><?php echo $row['rank']; ?></td>
                                <td><?php echo htmlspecialchars($row['username']); ?></td>
                                <td><?php echo htmlspecialchars($row['level_name']); ?></td>
                                <td><?php echo $row['exp'] . '/' . $row['next_level_exp']; ?></td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
            
            <div class="text-center mt-4"> 
                <?php if (!$isLoggedIn) : ?>
                    <a href="login.php" class="loginn-btn">Login to See Your Rank</a>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
</body>
</html>
