<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Valorant Live Stream</title>
    <link rel="stylesheet" href="styless.css"> <!-- Link to your styles.css file -->
    <style>
        /* Style the iframe container */
        .stream-container {
            max-width: 800px;
            margin: 20px auto; /* Added margin */
            padding: 20px;
            border: 1px solid #ddd;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            background-color: #fff;
        }

        /* Style the iframe */
        .stream-iframe {
            width: 100%;
            height: 450px;
            border: none;
            border-radius: 8px;
            margin-bottom: 20px;
        }

        /* Style the stream name */
        .stream-name {
            font-weight: bold;
            margin-bottom: 10px;
        }
    </style>
</head>
<body>
    <?php include 'navbar.php'; ?>
    <?php
        // Array of stream names
        $streams = array("VALORANT", "VALORANT_BR", "VALORANT_EMEA", "VALORANT_jpn", "valorant_americas", "valorant_pacific", "VALORANT_NorthAmerica", "valorantesports_ar", "LVPes2");

        // Loop through the stream names and create stream containers
        foreach ($streams as $stream) {
            echo '<div class="stream-container">';
            echo '<p class="stream-name">' . $stream . '</p>'; // Echo stream name
            echo '<iframe class="stream-iframe" src="https://player.twitch.tv/?channel=' . $stream . '&parent=localhost" allowfullscreen></iframe>';
            echo '</div>';
        }
    ?>

</body>
</html>
