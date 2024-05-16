<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Payment Confirmation</title>
  <style>
    body {
      font-family: Arial, sans-serif;
      display: flex;
      justify-content: center;
      align-items: center;
      height: 100vh;
      margin: 0;
      background-color: #f4f4f4;
    }

    .container {
      text-align: center;
      background: white;
      padding: 30px 40px;
      border-radius: 10px;
      box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
      max-width: 400px;
      width: 100%;
    }

    .logo {
      width: 100px;
      margin-bottom: 20px;
    }

    .icon {
      font-size: 50px;
      color: green;
      margin-bottom: 20px;
    }

    .btn {
      margin-top: 20px;
      padding: 10px 20px;
      background-color: #007bff;
      color: white;
      border: none;
      border-radius: 5px;
      cursor: pointer;
    }

    .btn:hover {
      background-color: #0056b3;
    }

    .order-details {
      text-align: left;
      margin-top: 20px;
      font-size: 14px;
      color: #555;
    }

    .order-details p {
      margin: 5px 0;
    }
  </style>
</head>

<body>
  <div class="container">
    <img src="<?= $logo_url ?>" alt="Company Logo" class="logo" />
    <div class="icon">✔</div>
    <h1>Thank You!</h1>
    <p>Your payment has been successfully completed.</p>
    <p>We appreciate your participation in our program.</p>

    <div class="order-details">
      <p><strong>Order ID:</strong> #<?= $id ?></p>
      <p><strong>Payment Date:</strong> <?= $date ?></p>
      <p><strong>Amount Paid:</strong> <?= $currency . ' ' . $amount ?></p>
    </div>

    <button class="btn" onclick="goHome()">Back to Previous Page</button>
  </div>

  <script>
    function goBack() {
      window.history.back(-3);
    }

    function goHome() {
      window.location.href = "http://worldyouthfest.com";
    }
  </script>
</body>

</html>