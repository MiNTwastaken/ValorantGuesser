<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Valorant Live Streams</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <style>
        /* Basic styling for the page */
        body {
            background-color: #f8f9fa;
            font-family: Arial, sans-serif;
        }

        .stream-container {
            margin-bottom: 1.25rem;
            border-radius: 0.625rem;
            box-shadow: 0 0.25rem 0.5rem rgba(0, 0, 0, 0.1);
            transition: transform 0.2s;
        }

        .stream-container:hover {
            transform: translateY(-0.1875rem);
        }

        .stream-name {
            font-size: 1rem;
            font-weight: bold;
            text-align: center;
            margin-top: 0.625rem;
            color: #ff6666;
        }

        .stream-iframe {
            width: 100%;
            height: 21.875rem;
            border-radius: 0 0 0.625rem 0.625rem;
        }

        .seethrough {
            background-color: rgba(248, 249, 250, 0.9);
        }
    </style>
</head>
<body>
    <?php include 'navbar.php'; ?>

    <div class="container mt-sm-4 rounded-lg seethrough">
        <h1 class="text-center mb-4">Valorant Live Streams</h1>

        <div class="row">
            <?php
                $streams = array("VALORANT", "VALORANT_BR", "VALORANT_EMEA", "VALORANT_jpn", "valorant_americas", "valorant_pacific", "VALORANT_NorthAmerica", "valorantesports_ar", "LVPes2");
    
                foreach ($streams as $stream) {
                    echo '<div class="col-md-6 col-lg-4">';
                    echo '<div class="stream-container">';
                    echo '<div class="stream-name">' . $stream . '</div>';
                    echo '<iframe class="stream-iframe" src="https://player.twitch.tv/?channel=' . $stream . '&parent=localhost" allowfullscreen></iframe>';
                    echo '</div>';
                    echo '</div>';
                }
            ?>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script> 
</body>
</html>
