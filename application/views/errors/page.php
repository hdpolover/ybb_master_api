<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?></title>
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

    </style>
</head>

<body>
    <div class="container">
        <h1><?= $message ?></h1>
    </div>
</body>

</html>