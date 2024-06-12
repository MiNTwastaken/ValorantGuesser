<!DOCTYPE html>
<html>
<head>
    <title>Valorant Fanpage</title>
    <link rel="stylesheet" href="styless.css">
</head>
<body>
    <script src="script.js"></script>
    <?php include 'navbar.php'; ?>

    <div class="content">
        <section class="hero"> 
            <h2>Welcome to the Ultimate Valorant Fan Hub!</h2>
            <p>Ignite your passion for Valorant in our vibrant community! Dive into the in-depth knowledge of our Wiki, strategize with fellow players on the social, and test your skills with our thrilling Minigames.</p>
        </section>

        <section class="features">
            <h3>What Awaits You?</h3>
            <ul>
                <li><strong>Master the Game:</strong> Our Wiki is your go-to resource for agent guides, weapon stats, map breakdowns, and advanced strategies.</li>
                <li><strong>Connect and Discuss:</strong> Join the conversation in our social, share your experiences, debate the meta, and forge friendships with other Valorant enthusiasts.</li>
                <li><strong>Challenge Yourself:</strong> Put your knowledge to the test with our Daily Quizzes, Test your aim at the Aim Trainer Minigame, and guess countless things Free Play.</li>
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