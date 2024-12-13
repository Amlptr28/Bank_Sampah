<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Bank Sampah Unit Sukses</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            flex-direction: column;
            height: 100vh;
            background-color: #ffffff;
        }
        .logo {
            position: fixed;
            top: 20px;
            left: 20px;
            width: 200px; /* Ukuran logo di desktop tetap sama */
            height: auto;
        }
        .login-box {
            background-color: #f5e3c6;
            padding: 40px;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            text-align: center;
            width: 90%;
            max-width: 300px;
            margin-top: 50px;
        }
        .login-box h2 {
            margin: 0 0 20px;
            font-size: 24px;
            color: #333;
        }
        .login-box input[type="text"],
        .login-box input[type="password"] {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 16px;
            box-sizing: border-box;
        }
        .login-button {
            width: 100%;
            padding: 10px;
            background-color: #689B70;
            color: #ffffff;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            font-weight: bold;
            cursor: pointer;
            margin-top: 20px;
        }
        .login-button:hover {
            background-color: #5d855d;
        }
        .signup-link {
            margin-top: 15px;
            font-size: 14px;
            color: #333;
        }
        .signup-link a {
            color: #689B70;
            text-decoration: none;
        }
        .signup-link a:hover {
            text-decoration: underline;
        }
        .error-message {
            color: red;
            margin-top: 10px;
        }

        /* Media Queries for Smaller Screens */
        @media (max-width: 768px) {
            .login-box {
                padding: 20px;
                width: 100%;
                max-width: 280px;
            }

            .login-box h2 {
                font-size: 22px;
            }

            .login-button {
                font-size: 15px;
            }
        }

        @media (max-width: 480px) {
            .logo {
                width: 120px; /* Perkecil logo di layar kecil */
            }

            .login-box {
                padding: 15px;
                width: 100%;
                max-width: 250px;
            }

            .login-box h2 {
                font-size: 20px;
            }

            .login-box input[type="text"],
            .login-box input[type="password"],
            .login-button {
                font-size: 14px;
            }
        }
    </style>
</head>
<body>
    <img src="foto/logo.png" alt="Bank Sampah Unit Sukses Logo" class="logo">
    <div class="login-box">
        <h2>LOGIN</h2>
        <form action="" method="post">
            <input type="text" name="username" placeholder="Username" required>
            <input type="password" name="password" placeholder="Password" required>
            <button type="submit" class="login-button">MASUK</button>
        </form>

        <?php
        // Start session to store user login status
        session_start();
        
        // Include database connection
        include 'config.php';

        // Check if form is submitted
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            // Get and sanitize user inputs
            $username = $conn->real_escape_string($_POST["username"]);
            $password = $conn->real_escape_string($_POST["password"]);

            // Fetch user from database
            $sql = "SELECT * FROM admin WHERE Username = '$username' AND Password = '$password'";
            $result = $conn->query($sql);

            if ($result->num_rows == 1) {
                // Login successful, set session and redirect
                $_SESSION["loggedin"] = true;
                $_SESSION["username"] = $username;
                header("Location: Dashboard.php");
                exit;
            } else {
                echo "<p class='error-message'>Username atau password salah.</p>";
            }
        }
        ?>
    </div>
    </div>
</body>
</html>
