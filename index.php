<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Valorant Fanpage</title>
    <link rel="stylesheet" href="styless.css">
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }
        .hero {
            background: url('hero-bg.jpg') no-repeat center center/cover;
            color: rgb(194, 97, 97);
            padding: 50px 20px;
            text-align: center;
        }
        .hero h2 {
            font-size: 2em;
            margin-bottom: 20px;
        }
        .hero p {
            font-size: 1em;
            margin-bottom: 20px;
        }
        .features {
            padding: 30px 20px;
            background-color: #fff;
            text-align: center;
        }
        .features h3 {
            font-size: 2em;
            margin-bottom: 20px;
        }
        .features ul {
            list-style: none;
            padding: 0;
        }
        .features li {
            font-size: 1em;
            margin-bottom: 15px;
        }
        .features li strong {
            color: #ff4655;
        }
        .call-to-action {
            padding: 30px 20px;
            background-color: #282c34;
            color: white;
            text-align: center;
        }
        .call-to-action h3 {
            font-size: 2em;
            margin-bottom: 20px;
        }
        .call-to-action p {
            font-size: 1em;
            margin-bottom: 20px;
        }
        .login-btn {
            background-color: #ff4655;
            color: white;
            padding: 15px 30px;
            text-decoration: none;
            border-radius: 5px;
            font-size: 1.2em;
        }
        .login-btn:hover {
            background-color: #e63946;
        }

        /* Responsive Styles */
        @media (min-width: 600px) {
            .hero h2 {
                font-size: 2.5em;
            }
            .hero p {
                font-size: 1.2em;
            }
            .features h3 {
                font-size: 2.5em;
            }
            .features li {
                font-size: 1.2em;
            }
            .call-to-action h3 {
                font-size: 2.5em;
            }
            .call-to-action p {
                font-size: 1.2em;
            }
        }

        @media (min-width: 768px) {
            .hero {
                padding: 100px 0;
            }
            .hero h2 {
                font-size: 3em;
            }
            .hero p {
                font-size: 1.5em;
            }
            .features {
                padding: 50px 20px;
            }
            .features h3 {
                font-size: 3em;
            }
            .features li {
                font-size: 1.5em;
            }
            .call-to-action {
                padding: 50px 20px;
            }
            .call-to-action h3 {
                font-size: 3em;
            }
            .call-to-action p {
                font-size: 1.5em;
            }
        }
    </style>
</head>
<body>
    <script src="script.js"></script>
    <?php include 'navbar.php'; ?>
    <div class="content mt-sm-5">
        <section class="hero"> 
            <h2>Welcome to the Ultimate Valorant Fan Hub!</h2>
            <p>Ignite your passion for Valorant in our vibrant community! Dive into the in-depth knowledge of our Wiki, strategize with fellow players on the social, and test your skills with our thrilling Minigames.</p>
        </section>

        <section class="features">
            <h3>What Awaits You?</h3>
            <ul>
                <li><strong>Master the Game:</strong> Our Wiki is your go-to resource for agent guides, weapon stats, map breakdowns, and advanced strategies.</li>
                <li><strong>Connect and Discuss:</strong> Join the conversation in our social, share your experiences, debate the meta, and forge friendships with other Valorant enthusiasts.</li>
                <li><strong>Challenge Yourself:</strong> Put your knowledge to the test with our Daily Quizzes, test your aim at the Aim Trainer Minigame, and guess countless things in Free Play.</li>
            </ul>
        </section>

        <section class="call-to-action">
            <h3>Ready to Join the Fun?</h3>
            <p>Don't miss out on the action! Create your account today and unlock a world of Valorant knowledge, camaraderie, and excitement.</p>
            <?php if (!$isLoggedIn) : ?>
                <a href="register.php" class="login-btn">Register Now</a> 
            <?php endif; ?>
        </section>
    </div>
</body>
</html>
