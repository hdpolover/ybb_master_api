<?php

include 'connection.php';

$news_id = isset($_GET['id']) ? $_GET['id'] : '';

if ($news_id) {

    // dekrip
    $encryptedData = $news_id;
    $method = "AES-256-CBC";
    $key = "encryptionKey123";
    $options = 0;
    $iv = '1234567891011121';

    $decryptedData = openssl_decrypt($encryptedData, $method, $key, $options, $iv);

    $cek_program_categories = mysqli_query($db, "SELECT * FROM program_categories WHERE id = '" . $decryptedData . "'");
    $cek_program_categories_cnt = mysqli_num_rows($cek_program_categories);
    if ($cek_program_categories_cnt == 0) {
        echo '<script language="javascript">';
        echo 'alert("Program Categories not found!")';
        echo '</script>';
    } else {
        $url_news = mysqli_fetch_array($cek_program_categories);
        header("Location: https://".$url_news['web_url'].'/news');
        die();
    }
} else {
    echo '<script language="javascript">';
    echo 'alert("NEWS not found!")';
    echo '</script>';
}
