<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registration Complete!</title>
    <style>
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            background-color: #e6f2ff;
            text-align: center;
            padding: 50px;
            color: #003366;
        }

        .container {
            background-color: #ffffff;
            border-radius: 12px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            padding: 40px;
            max-width: 600px;
            margin: auto;
            border: 2px solid #007bff;
        }

        .logo {
            width: 120px;
            margin-bottom: 30px;
        }

        h1 {
            color: #003366;
            font-size: 28px;
            margin-bottom: 10px;
        }

        p {
            color: #003366;
            font-size: 16px;
            line-height: 1.5;
            margin-bottom: 20px;
        }

        .btn-signin {
            display: inline-block;
            background-color: #007bff;
            color: white;
            padding: 12px 25px;
            font-size: 16px;
            text-align: center;
            text-decoration: none;
            border-radius: 5px;
            transition: background-color 0.3s, transform 0.3s;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .btn-signin:hover {
            background-color: #0056b3;
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        }
    </style>
</head>

<body>
    <div class="container">
        <img src="<?= $_GET['logo_url'] ?>" alt="Logo" class="logo">
        <h1>Registration Complete!</h1>
        <p>Thank you for registering for <?= $_GET['program'] ?>. To complete your account setup, please check your email and follow the instructions to verify your account. If you don't see the email, be sure to check your spam folder.</p>
        <p>If you have any questions or need assistance, feel free to contact our support team.</p>
        <p style="margin-top: 8px;">Welcome aboard!</p>
        <a href="https://<?= $_GET['web_url'] ?>" class="btn-signin">Okay!</a>
    </div>
</body>

</html>