<html>
<title>Checkout</title>

<head>
  <style>
    /* Mengatur body agar tampilan full-screen dan center */
    body {
      margin: 0;
      height: 100vh;
      display: flex;
      justify-content: center;
      align-items: center;
      background: linear-gradient(135deg, #6e7fdb, #6ac1ff);
      font-family: 'Arial', sans-serif;
    }

    /* Styling tombol action */
    .action-button {
      padding: 20px 40px;
      font-size: 18px;
      font-weight: bold;
      color: white;
      background-color: #F39C12;
      border: none;
      border-radius: 50px;
      cursor: pointer;
      transition: background-color 0.3s, transform 0.3s;
    }

    .action-button:hover {
      background-color: #E67E22;
      /* Kuning lebih gelap saat hover */
      transform: scale(1.1);
    }

    /* Efek focus untuk tombol */
    .action-button:focus {
      outline: none;
    }
  </style>
  <script type="text/javascript"
    src="https://app.sandbox.midtrans.com/snap/snap.js"
    data-client-key="<?= config_item('client_key') ?>"></script>
  <script src="//ajax.googleapis.com/ajax/libs/jquery/1.11.0/jquery.min.js"></script>
</head>

<body>


  <form id="payment-form" method="post" action="<?= site_url() ?>/snap/finish">
    <input type="hidden" name="result_type" id="result-type" value=""></div>
    <input type="hidden" name="result_data" id="result-data" value=""></div>
  </form>

  <button class="action-button" id="pay-button">Pay!</button>
  <script type="text/javascript">
    $('#pay-button').click(function(event) {
      event.preventDefault();
      $(this).attr("disabled", "disabled");

      $.ajax({
        type: 'POST',
        url: '<?= site_url() ?>/snap/token',
        data: {
          'id': "<?= $id ?>",
          'price': "<?= $price ?>",
          'description': "<?= $description ?>",
          'name': "<?= $name ?>",
          'email': "<?= $email ?>",
          'phone': "<?= $phone ?>",
          'participant_id': "<?= $participant_id ?>",
          'payment_id': "<?= $payment_id ?>",
          'program_id': "<?= $program_id ?>",
          'program_payment_id': "<?= $program_payment_id ?>",
          'payment_method_id': "<?= $payment_method_id ?>",
        },
        cache: false,

        success: function(data) {
          //location = data;

          //console.log('token = '+data);

          var resultType = document.getElementById('result-type');
          var resultData = document.getElementById('result-data');

          function changeResult(type, data) {
            $("#result-type").val(type);
            $("#result-data").val(JSON.stringify(data));
            //resultType.innerHTML = type;
            //resultData.innerHTML = JSON.stringify(data);
          }

          snap.pay(data, {

            onSuccess: function(result) {
              changeResult('success', result);
              console.log(result.status_message);
              console.log(result);
              $("#payment-form").submit();
            },
            onPending: function(result) {
              changeResult('pending', result);
              console.log(result.status_message);
              $("#payment-form").submit();
            },
            onError: function(result) {
              changeResult('error', result);
              console.log(result.status_message);
              $("#payment-form").submit();
            }
          });
        }
      });
    });
  </script>


</body>

</html>