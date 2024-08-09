<?php

include 'connection.php';

$ref_code = isset($_GET['ref_code']) ? $_GET['ref_code'] : '';

if ($ref_code) {
    $cek_ambassador = mysqli_query($db, "SELECT ambassadors.*, programs.program_category_id, programs.logo_url FROM ambassadors JOIN programs ON programs.id = ambassadors.program_id WHERE ref_code = '" . $ref_code . "'");
    $cek_ambassador_cnt = mysqli_num_rows($cek_ambassador);
    if ($cek_ambassador_cnt == 0) {
        echo '<script language="javascript">';
        echo 'alert("Ambassador Code not found!")';
        echo '</script>';
    }
} else {

    echo '<script language="javascript">';
    echo 'alert("REF CODE not found!")';
    echo '</script>';
}
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
    <?php

    $ambassador = mysqli_query($db, "SELECT ambassadors.*, programs.program_category_id, programs.logo_url FROM ambassadors JOIN programs ON programs.id = ambassadors.program_id WHERE ref_code = '" . $ref_code . "'");
    $row = mysqli_fetch_array($ambassador);

    ?>
    <div class="container">
        <div class="header">
            <img src="<?= $row['logo_url'] ?>" alt="Logo">
            <h1>Register Now</h1>
            <p>Please fill in the form below to create an account.</p>
        </div>
        <form class="user" method="POST" action="">
            <input type="hidden" name="code" value="<?= $ref_code ?>">
            <input type="hidden" name="program_id" value="<?= $row['program_id'] ?>">
            <input type="hidden" name="program_category_id" value="<?= $row['program_category_id'] ?>">
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
            <button id="btn-register" type="submit" class="btn btn-primary btn-user btn-block">Register</button>
        </form>
    </div>
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.1.1/jquery.min.js"></script>
    <!-- <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script> -->
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.2/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <?php ?>
</body>

</html>
<?php
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $fullName = $_POST['fullName'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $confirmPassword = $_POST['confirmPassword'];
    $ref_code = $_POST['code'];
    $program_id = $_POST['program_id'];
    $program_category_id = $_POST['program_category_id'];
    $now = date('Y-m-d H:i:s');

    $cek_ambassador = mysqli_query($db, "SELECT ambassadors.*, programs.program_category_id, programs.logo_url FROM ambassadors JOIN programs ON programs.id = ambassadors.program_id WHERE ref_code = '" . $ref_code . "'");
    $cek_ambassador_cnt = mysqli_num_rows($cek_ambassador);
    if ($cek_ambassador_cnt == 0) {
        echo '<script language="javascript">';
        echo 'alert("Ambassador Code not found!")';
        echo '</script>';
    } else {
        // Perform form validation and processing here

        // Example: Simple validation
        if ($password === $confirmPassword) {
            // cek data jika sudah terdaftar di program itu
            $cek_data_program = mysqli_query($db, "SELECT participants.* FROM users JOIN participants ON users.id = participants.user_id WHERE email = '" . $email . "' AND program_id = '" . $program_id . "'");
            $cek_data_program_cnt = mysqli_num_rows($cek_data_program);
            if ($cek_data_program_cnt) {
                echo '<script language="javascript">';
                echo 'alert("You are already registered as a participant. Please sign in to continue!")';
                echo '</script>';
            } else {
                // insert users
                if (!mysqli_query($db, "INSERT INTO users (full_name,email,password,program_category_id,created_at,updated_at) values('$fullName','$email','" . md5($password) . "','$program_category_id', '$now','$now')")) {
                    echo ("Error description: " . mysqli_error($db));
                    die();
                }

                // get last id 
                $last_id_qry = mysqli_query($db, "SELECT MAX(id) AS id FROM users");
                $last_id = mysqli_fetch_array($last_id_qry);

                // insert participant
                if (!mysqli_query($db, "INSERT INTO participants (user_id,account_id,full_name,ref_code_ambassador,program_id,created_at,updated_at) values('" . $last_id['id'] . "','" . uniqid($last_id['id']) . "','$fullName','$ref_code','$program_id','$now','$now')")) {
                    echo ("Error description: " . mysqli_error($db));
                    die();
                }
                // get last id 
                $last_participant_id_qry = mysqli_query($db, "SELECT MAX(id) AS id FROM participants");
                $last_participant_id = mysqli_fetch_array($last_participant_id_qry);

                // insert partisipant
                if (!mysqli_query($db, "INSERT INTO participant_statuses (participant_id,general_status, form_status,document_status,payment_status,created_at,updated_at) values('" . $last_participant_id['id'] . "',0,0,0,0,'$now','$now')")) {
                    echo ("Error description: " . mysqli_error($db));
                    die();
                }
                // echo '<script language="javascript">';
                // echo 'alert("Data saved successfully!")';
                // echo '</script>';
                // query cari data nya
                $data_user_qry = mysqli_query($db, "SELECT users.id, programs.name, programs.logo_url,program_categories.web_url
                FROM users
                JOIN participants ON participants.user_id = users.id
                JOIN programs ON participants.program_id = programs.id
                JOIN program_categories ON programs.program_category_id = program_categories.id
                WHERE users.id = '" . $last_id['id'] . "'");
                $data_user = mysqli_fetch_array($data_user_qry);
?>
                <script>
                    var urlVerifEmail = "https://master-api.ybbfoundation.com/Users/email_verif/";
                    // var urlVerifEmail = "Users/email_verif/";

                    $.ajax({
                        url: urlVerifEmail,
                        data: {
                        id :<?= $data_user['id'] ?>
                        },
                        async:false,
                        type: 'POST',
                        success: function(resp) {
                            if (resp.status) {
                                window.location.href = 'success_register.php?logo_url=<?= $data_user['logo_url'] ?>&program=<?= $data_user['name'] ?>&web_url=<?= $data_user['web_url'] ?>';
                            } else {
                                alert('Error: ' + e);
                            }
                        },
                        error: function(e) {
                            alert('Error: ' + e);
                        }
                    });
                </script>
<?php
            }
        } else {
            echo '<script language="javascript">';
            echo 'alert("Passwords do not match!")';
            echo '</script>';
        }
    }
}
?>