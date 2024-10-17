<!DOCTYPE html>
<html>

<head>
    <style>
        @page {
            margin: 10px 0 10px 0 !important;
        }

        body {
            margin-top: 4.8cm;
            margin-left: 3cm;
            margin-right: 3cm;
            margin-bottom: 4.8cm;
        }

        header {
            position: fixed;
            top: 2cm;
            left: 3cm;
            right: 3cm;
            height: 2cm;
        }

        footer {
            position: fixed;
            bottom: -1cm;
            left: 0cm;
            right: 0cm;
            height: 2cm;
        }

        footer .pagenum:before {
            content: counter(page);
        }

        .font-xl {
            font-size: 24px;
        }

        .font-lg {
            font-size: 20px;
        }

        .font-md {
            font-size: 14px;
        }

        .font-sm {
            font-size: 10px;
        }

        .text-bold {
            font-weight: bold;
        }

        .text-center {
            text-align: center;
        }

        .text-right {
            text-align: right;
        }

        .bl {
            border-left: 1px solid #000;
        }

        .bb-clear {
            border-bottom: 1px solid #FFF;
        }

        .b-b-double th {
            border-bottom: double 3px #000;
        }

        /* .b-b-clear {
            border-bottom: 1px solid #FFF !important;
        }

        .b-x-clear {
            border-left: 1px solid #FFF !important;
            border-right: 1px solid #FFF !important;
        }

        .b-r-clear {
            border-right: 1px solid #FFF !important;
        } */
        .footer {
            position: fixed;
            right: -280px;
            bottom: 40px;
            width: 100%;
            text-align: center;
        }

        /* .footer {
            position: fixed;
            right: 0px;
            bottom: 40px;
            width: 100%;
            text-align: center;
        } */
    </style>
</head>

<body style="font-size: 18px; font-family: Tahoma, Helvetica, sans-serif;">
    <header>
        <?php
        $path = $logo_url;
        $type = pathinfo($path, PATHINFO_EXTENSION);
        $data = file_get_contents($path);
        $logo = 'data:image/' . $type . ';base64,' . base64_encode($data);

        ?>
        <div style="border-bottom: solid 2px #0CF0C5;margin-bottom:12px;"></div>
        <div style="width: 15%; float: left;"><img src="<?= $logo ?>" style="width: 80%;"></div>
        <div style="width: 60%; float: left;">
            <div class="font-sm">Youth Break the Boundaries</div>
            <div style="font-size:28px;font-weight:bold;"><?= $name_categories ?></div>
            <div class="font-md"><?= $tagline ?></div>
        </div>
        <div style="width: 25%; float: left;margin-top:12px;">
            <div class="font-sm"><?= $web_categories ?><br><?= $email_categories ?><br><?= $contact_categories . ' (YBB Admin)' ?></div>
        </div>
        <div style="clear: both;border-top: solid 2px #0CF0C5;margin-top:12px;">
        </div>
    </header>
    <!-- <footer style="background-color:#0CF0C5">
        <?php
        $path_tel = getcwd() . '/assets/img/tel.png';
        $type_tel = pathinfo($path_tel, PATHINFO_EXTENSION);
        $data_tel = file_get_contents($path_tel);
        $img_tel = 'data:image/' . $type_tel . ';base64,' . base64_encode($data_tel);

        $path_email = getcwd() . '/assets/img/email.png';
        $type_email = pathinfo($path_email, PATHINFO_EXTENSION);
        $data_email = file_get_contents($path_email);
        $img_email = 'data:image/' . $type_email . ';base64,' . base64_encode($data_email);

        $path_web = getcwd() . '/assets/img/web.png';
        $type_web = pathinfo($path_web, PATHINFO_EXTENSION);
        $data_web = file_get_contents($path_web);
        $img_web = 'data:image/' . $type_web . ';base64,' . base64_encode($data_web);
        ?>
        <div style="position: fixed;margin-top:16px;margin-left:180px;color: #003487;font-size:12px;"><span style="margin-right: 10px;"><img src="<?= $img_tel ?>" width="10px" alt=""> +62 851-7338-662</span><span style="margin-right: 10px;"><img src="<?= $img_email ?>" width="10px" alt=""> worldyouthfest@gmail.com</span><span style="margin-right: 10px;"><img src="<?= $img_web ?>" width="10px" alt=""> www.worldyouthfest.com</span></div>
    </footer> -->
    <main class="font-md" style="margin-top: 20px;">
        <?php
        $path_logo = $logo_categories;
        $type_logo = pathinfo($path_logo, PATHINFO_EXTENSION);
        $data_logo = file_get_contents($path_logo);
        $img_logo = 'data:image/' . $type_logo . ';base64,' . base64_encode($data_logo);

        $path_sign = $sign_url;
        $type_sign = pathinfo($path_sign, PATHINFO_EXTENSION);
        $data_sign = file_get_contents($path_sign);
        $img_sign = 'data:image/' . $type_sign . ';base64,' . base64_encode($data_sign);
        ?>
        <div>
            <div style="margin-bottom:20px;">
                <div style="color: #003487;font-weight:bold;font-size:30px"><?= $name ?></div>
                <div style="color: #003487">for <?= strtoupper($full_name) ?></div>
            </div>
            <div style="text-align: justify;margin-bottom:100px;">
                <?= $content ?>
            </div>
            <div style="clear: both;">
            </div>
            <div style="width: 55%; float: left;"></div>
            <div style="width: 45%; float: left;">
                <div>Sincerely,</div>
                <div>
                    <img src="<?= $img_logo ?>" style="opacity: 0.3;margin-left:-20px;" alt="" width="50px;">
                    <img src="<?= $img_sign ?>" alt="" width="100px;">
                </div>
                <div style="font-weight:bold;"><?= $sincerely ?></div>
                <div>Chairman of <?= $name_categories ?></div>
            </div>
            <div style="clear: both;">
            </div>
        </div>
    </main>
</body>

</html>