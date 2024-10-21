<?php
include 'connection.php';

// Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id = $_GET['id'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];

    // Validate passwords
    if ($new_password == $confirm_password) {
        // dekrip
        $encryptedData = $id;
        $method = "AES-256-CBC";
        $key = "encryptionKey123";
        $options = 0;
        $iv = '1234567891011121';

        $decryptedData = openssl_decrypt($encryptedData, $method, $key, $options, $iv);

        $con = mysqli_connect($_hostname, $_username, $_password, $_database);

        // $result = mysqli_query($con, "SELECT * FROM users WHERE id = '" . $decryptedData . "'");
        if (mysqli_query($con, "SELECT * FROM users WHERE id = '" . $decryptedData . "'")) {
            $result = mysqli_query($con, "SELECT * FROM users WHERE id = '" . $decryptedData . "'");
        } else {
            echo ("Error description: " . mysqli_error($con));
            die();
        }
        // $data = $result->fetch_all(MYSQLI_ASSOC);
        // echo $id;
        // echo $decryptedData;
        // echo print_r($result->fetch_assoc());
        // echo print_r(mysqli_fetch_array($result));
        // die();
        $encrypt_pass = md5($new_password);
        // echo $encrypt_pass;
        // die();
        if (mysqli_num_rows($result)) {
            $sql = mysqli_query($con, "UPDATE users SET password = '" . $encrypt_pass . "' WHERE id = '" . $decryptedData . "'");
            if ($sql) {
                $message = "Password has been reset successfully!";
            } else {
                $message = mysqli_error($con);
            }
        } else {
            $message = "An error occurred while resetting the password.";
        }
    } else {
        $message = "Passwords do not match!";
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f9;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }

        form {
            background-color: #fff;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 400px;
        }

        h2 {
            margin-top: 0;
            color: #333;
        }

        .pwd {
            display: flex;
            align-items: center;
            border: 1px solid #ddd;
            border-radius: 5px;
            margin-bottom: 15px;
            background-color: #fff;
        }

        .pwd input[type="password"],
        .pwd input[type="text"] {
            border: none;
            padding: 10px;
            flex: 1;
            outline: none;
            border-radius: 5px;
        }

        .p-viewer {
            padding: 10px;
            cursor: pointer;
            font-size: 16px;
            color: #007bff;
        }

        button {
            padding: 10px;
            background-color: #007bff;
            border: none;
            border-radius: 5px;
            color: #fff;
            font-size: 16px;
            cursor: pointer;
        }

        button:hover {
            background-color: #0056b3;
        }

        .message {
            margin-top: 15px;
            color: #d9534f;
        }
    </style>
</head>

<body>

    <form action="" method="POST">
        <h2>Reset Password</h2>
        <?php if (isset($message)) : ?>
            <p class="message"><?php echo $message; ?></p>
        <?php endif; ?>
        <div class="pwd">
            <input type="password" name="new_password" id="new_password" placeholder="New Password" required>
            <span class="p-viewer" onclick="togglePassword('new_password', this)">
                <i class="fa fa-eye" aria-hidden="true"></i>
            </span>
        </div>
        <div class="pwd">
            <input type="password" name="confirm_password" id="confirm_password" placeholder="Confirm Password" required>
            <span class="p-viewer" onclick="togglePassword('confirm_password', this)">
                <i class="fa fa-eye" aria-hidden="true"></i>
            </span>
        </div>
        <button type="submit">Save</button>
    </form>

    <script>
        function togglePassword(id, element) {
            var passwordInput = document.getElementById(id);
            var icon = element.querySelector('i');
            if (passwordInput.type === "password") {
                passwordInput.type = "text";
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            } else {
                passwordInput.type = "password";
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            }
        }
    </script>

</body>

</html>