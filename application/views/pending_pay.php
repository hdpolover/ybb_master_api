<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Pending Payment</title>
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
    <div><img src="<?= base_url('asset/img/hourglass.png') ?>" style="width:34px;" alt="Hourglass"></div>
    <h1>Your transaction is still pending</h1>

    <div class="order-details">
      <p><strong>Order ID:</strong> #<?= $id ?></p>
      <p><strong>Transaction time:</strong> <?= $date ?></p>
      <p><strong>Amount Paid:</strong> <?= $currency . ' ' . $amount ?></p>
      <p><strong><?= ($va_number ? $va_number : '') ?></strong></p>
      <p style="text-align: center;"><strong><?= ($url_qris ? '<img class="qr-image" src='.$url_qris.' style="width:200px;" alt="qr-code">' : '') ?></strong></p>
    </div>

    <!-- <button class="btn" onclick="checkStatus('<?= $id ?>')">Check Status</button> -->
  </div>
  <script type="text/javascript"
    src="https://app.sandbox.midtrans.com/snap/snap.js"
    data-client-key="<?= config_item('client_key') ?>"></script>
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.0/jquery.min.js"></script>

  <script>
    function checkStatus(id) {
      $.ajax({
        type: 'POST',
        url: '<?= base_url() ?>/Snap/check_status',
        cache: false,
        data : {
          'transaction_id' : id,
        },
        success: function(data) {
        }
      });
    }
  </script>
</body>

</html>