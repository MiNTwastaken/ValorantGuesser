<?php
session_start();
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php'; // Include PHPMailer files

// Initialize variables
$usernameErr = $passwordErr = $emailErr = $confirmPasswordErr = $confirmEmailErr = "";
$username = $password = $email = $confirmPassword = $confirmEmail = "";

// Database Connection
$connection = mysqli_connect("localhost:3306", "root", "", "valorantfanpage");
if (!$connection) {
    die("Connection failed: " . mysqli_connect_error());
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validate username
    if (empty($_POST["username"])) {
        $usernameErr = "Username is required";
    } else {
        $username = htmlspecialchars($_POST["username"]);
    }

    // Validate email
    if (empty($_POST["email"])) {
        $emailErr = "Email is required";
    } else {
        $email = htmlspecialchars($_POST["email"]);
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $emailErr = "Invalid email format";
        }
    }

    // Validate confirm email
    if (empty($_POST["confirmEmail"])) {
        $confirmEmailErr = "Confirm email is required";
    } else {
        $confirmEmail = htmlspecialchars($_POST["confirmEmail"]);
        if ($email !== $confirmEmail) {
            $confirmEmailErr = "Emails do not match";
        }
    }

    // Validate password
    if (empty($_POST["password"])) {
        $passwordErr = "Password is required";
    } else {
        $password = htmlspecialchars($_POST["password"]);
    }

    // Validate confirm password
    if (empty($_POST["confirmPassword"])) {
        $confirmPasswordErr = "Confirm password is required";
    } else {
        $confirmPassword = htmlspecialchars($_POST["confirmPassword"]);
        if ($password !== $confirmPassword) {
            $confirmPasswordErr = "Passwords do not match";
        }
    }

    if (empty($usernameErr) && empty($emailErr) && empty($confirmEmailErr) && empty($passwordErr) && empty($confirmPasswordErr)) {
        $sql = "SELECT * FROM user WHERE username = '$username' OR email = '$email'";
        $result = mysqli_query($connection, $sql);

        if (mysqli_num_rows($result) > 0) {
            $usernameErr = "Username or email already exists";
        } else {
            // Hash the password
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

            // Insert new user into database
            $sql = "INSERT INTO user (username, password, email, email_confirmed) VALUES ('$username', '$hashedPassword', '$email', 0)";

            if (mysqli_query($connection, $sql)) {
                // Email Confirmation using PHPMailer
                $mail = new PHPMailer(true); // Enable exceptions

                try {
                    // Server settings
                    $mail->SMTPDebug = 0; 
                    $mail->isSMTP();
                    $mail->Host       = 'smtp.gmail.com';                    
                    $mail->SMTPAuth   = true;
                    $mail->Username   = 'valorantforumnoreply@gmail.com';
                    $mail->Password   = 'uihw cbhk trjb ngos';
                    $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
                    $mail->Port       = 465;

                    // Recipients
                    $mail->setFrom('valorantforumnoreply@gmail.com', 'Valorant Fanpage');
                    $mail->addAddress($email, $username);

                    // Content
                    $confirmationLink = "http://localhost/valorantfanpage/register_confirm.php?email=" . urlencode($email);

                    $mail->isHTML(true);                                  
                    $mail->Subject = 'Confirm your registration';
                    $mail->Body    = "Hi $username,<br><br>Please click the following link to confirm your registration:<br><br><a href='$confirmationLink'>$confirmationLink</a>";
                    $mail->AltBody = "Hi $username,\n\nPlease visit this link to confirm your registration:\n\n$confirmationLink";

                    $mail->send();
                    header("Location: register_confirm.php"); 
                    exit;

                } catch (Exception $e) {
                    // Error handling in case the email cannot be sent
                    echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
                }
            } else {
                echo "Error: " . $sql . "<br>" . mysqli_error($connection);
            }
        }
    }

    // Close the connection
    mysqli_close($connection);
}
?>

<!DOCTYPE html>
<html>
<head>
<title>Valorant Fanpage</title>
<link rel="stylesheet" href="styless.css">
<style>
    .password-strength {
        display: inline-block;
        width: 20px;
        height: 20px;
        margin-left: 10px;
        border-radius: 50%;
    }
    .weak {
        background-color: red;
    }
    .medium {
        background-color: orange;
    }
    .strong {
        background-color: green;
    }
    .error {
        color: red;
    }
</style>
<script>
function validatePasswordStrength() {
    var password = document.getElementById('password').value;
    var strengthBar = document.getElementById('strength-bar');
    var strengthText = document.getElementById('strength-text');
    var strength = 0;

    if (password.match(/[a-z]+/)) {
        strength += 1;
    }
    if (password.match(/[A-Z]+/)) {
        strength += 1;
    }
    if (password.match(/[0-9]+/)) {
        strength += 1;
    }
    if (password.match(/[$@#&!]+/)) {
        strength += 1;
    }
    if (password.length >= 8) {
        strength += 1;
    }

    switch (strength) {
        case 0:
        case 1:
        case 2:
            strengthBar.className = 'password-strength weak';
            strengthText.innerText = 'Weak';
            break;
        case 3:
        case 4:
            strengthBar.className = 'password-strength medium';
            strengthText.innerText = 'Medium';
            break;
        case 5:
            strengthBar.className = 'password-strength strong';
            strengthText.innerText = 'Strong';
            break;
    }
}

function validateMatchingFields() {
    var password = document.getElementById('password').value;
    var confirmPassword = document.getElementById('confirmPassword').value;
    var email = document.getElementById('email').value;
    var confirmEmail = document.getElementById('confirmEmail').value;

    var passwordMatchText = document.getElementById('password-match-text');
    var emailMatchText = document.getElementById('email-match-text');

    if (password && confirmPassword && password !== confirmPassword) {
        passwordMatchText.innerText = 'Passwords do not match';
        passwordMatchText.className = 'error';
    } else {
        passwordMatchText.innerText = '';
    }

    if (email && confirmEmail && email !== confirmEmail) {
        emailMatchText.innerText = 'Emails do not match';
        emailMatchText.className = 'error';
    } else {
        emailMatchText.innerText = '';
    }
}

function checkExisting(field, value) {
    var xhr = new XMLHttpRequest();
    xhr.open("POST", "check_existing.php", true);
    xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
    xhr.onreadystatechange = function() {
        if (xhr.readyState == 4 && xhr.status == 200) {
            var response = JSON.parse(xhr.responseText);
            var fieldErr = document.getElementById(field + 'Err');

            if (response.exists) {
                fieldErr.innerText = field.charAt(0).toUpperCase() + field.slice(1) + ' already exists';
                fieldErr.className = 'error';
            } else {
                fieldErr.innerText = '';
            }
        }
    };
    xhr.send(field + "=" + encodeURIComponent(value));
}

document.addEventListener("DOMContentLoaded", function() {
    document.getElementById("username").addEventListener("blur", function() {
        checkExisting("username", this.value);
    });

    document.getElementById("email").addEventListener("blur", function() {
        checkExisting("email", this.value);
    });

    document.getElementById("password").addEventListener("input", validatePasswordStrength);
    document.getElementById("confirmPassword").addEventListener("input", validateMatchingFields);
    document.getElementById("email").addEventListener("input", validateMatchingFields);
    document.getElementById("confirmEmail").addEventListener("input", validateMatchingFields);
});
</script>
</head>
<body>
    <?php include 'navbar.php'; ?>

    <div class="logreg-container register-page">
        <h2>Valorant Fanpage Register</h2>
        
        <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
            <div class="form-group">
                <label for="username">Username:</label>
                <input type="text" name="username" id="username" value="<?php echo isset($_POST["username"]) ? $_POST["username"] : ""; ?>" required>
                <span class="error" id="usernameErr"><?php echo $usernameErr; ?></span>
            </div>
        
            <div class="form-group">
                <label for="email">Email:</label>
                <input type="email" name="email" id="email" value="<?php echo isset($_POST["email"]) ? $_POST["email"] : ""; ?>" required>
                <span class="error" id="emailErr"><?php echo $emailErr; ?></span>
            </div>

            <div class="form-group">
                <label for="confirmEmail">Confirm Email:</label>
                <input type="email" name="confirmEmail" id="confirmEmail" value="<?php echo isset($_POST["confirmEmail"]) ? $_POST["confirmEmail"] : ""; ?>" required>
                <span class="error" id="confirmEmailErr"><?php echo $confirmEmailErr; ?></span>
                <span id="email-match-text" class="error"></span>
            </div>

            <div class="form-group">
                <label for="password">Password:</label>
                <input type="password" name="password" id="password" required>
                <span class="error" id="passwordErr"><?php echo $passwordErr; ?></span>
            </div>

            <div class="form-group">
                <label for="confirmPassword">Confirm Password:</label>
                <input type="password" name="confirmPassword" id="confirmPassword" required>
                <span class="error" id="confirmPasswordErr"><?php echo $confirmPasswordErr; ?></span>
                <span id="password-match-text" class="error"></span>
                <span id="strength-text">Weak</span><div id="strength-bar" class="password-strength weak"></div>
                <div>Password must be at least 8 characters long, with uppercase, lowercase, numbers, and special characters.</div>
            </div>
        
            <div class="form-group">
                <button type="submit" class="register-btn" name="register">Register</button>
            </div>
        </form>
    </div>
</body>
</html>
