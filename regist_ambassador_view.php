<?php 

include 'connection.php';
$ref_code = isset($_GET['ref_code']) ? $_GET['ref_code'] : '';

$ambassador = mysqli_query($koneksi,"SELECT ambassadors.*, programs.program_category_id, programs.logo_url FROM ambassadors JOIN programs ON programs.id = ambassadors.program_id WHERE ref_code = '.$ref_code.'");

while($row = $ambassador->fetch_assoc())
{
    $rows[] = $row;
}
return $rows;
die();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registration Form</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f0f8ff;
            font-family: Arial, sans-serif;
        }

        .container {
            max-width: 500px;
            margin-top: 50px;
            background-color: #ffffff;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            padding: 20px;
        }

        .header {
            text-align: center;
            margin-bottom: 20px;
        }

        .header img {
            max-width: 100px;
            margin-bottom: 10px;
        }

        .header h1 {
            font-size: 24px;
            margin-bottom: 10px;
            color: #007bff;
        }

        .header p {
            color: #666;
            font-size: 14px;
        }

        .form-group label {
            font-weight: bold;
        }

        .btn-primary {
            background-color: #007bff;
            border-color: #007bff;
        }

        .btn-primary:hover {
            background-color: #0056b3;
            border-color: #0056b3;
        }
    </style>
</head>

<body>

    <div class="container">
        <div class="header">
            <img src="<?= $logo_url ?>" alt="Logo">
            <h1>Register Now</h1>
            <p>Please fill in the form below to create an account.</p>
        </div>
        <form class="user" id="form-data" method="POST" action="">
            <input type="hidden" name="code" value="<?= $ref_code ?>">
            <input type="hidden" name="code" value="<?= $ref_code ?>">
            <div class="form-group">
                <label for="fullName">Full Name</label>
                <input type="text" class="form-control form-control-user" id="fullName" name="fullName" placeholder="Enter your full name" required>
            </div>
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" class="form-control form-control-user" id="email" name="email" placeholder="Enter your email" required>
            </div>
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" class="form-control form-control-user" id="password" name="password" placeholder="Enter your password" required>
            </div>
            <div class="form-group">
                <label for="confirmPassword">Confirm Password</label>
                <input type="password" class="form-control form-control-user" id="confirmPassword" name="confirmPassword" placeholder="Confirm your password" required>
            </div>
            <button id="btn-register" type="button" class="btn btn-primary btn-user btn-block">Register</button>
        </form>
    </div>
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.1.1/jquery.min.js"></script>
    <!-- <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script> -->
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.2/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

</body>

</html>
<?php
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $fullName = $_POST['fullName'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $confirmPassword = $_POST['confirmPassword'];

    // Perform form validation and processing here

    // Example: Simple validation
    if ($password === $confirmPassword) {
        // Process registration (e.g., save to database)
        echo "Registration successful!";
    } else {
        echo "Passwords do not match!";
    }
}
?>
<script>
    $(document).ready(function() {
        // $('#btn-register').click(function() {
        //     var form = $('#form-data'),
        //         dt = form.serializeArray();
        //     $.ajax({
        //         type: 'POST',
        //         url: '<?= base_url('Register/save') ?>',
        //         data: dt,
        //         dataType: 'JSON',
        //         beforeSend: function() {},
        //         success: function(result) {
        //             if (result.status) {
        //                 alert(result.message);
        //             } else {
        //                 alert(result.message);
        //             }
        //         },
        //         error: function() {
        //             alert("ERROR");
        //         }
        //     });
        // });
    });
</script>