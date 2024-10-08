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

    $cek_news = mysqli_query($db, "SELECT program_categories.web_url FROM program_categories JOIN programs ON program_categories.id = programs.program_category_id JOIN program_announcements ON programs.id = program_announcements.program_id WHERE program_announcements.id = '" . $decryptedData . "'");
    $cek_news_cnt = mysqli_num_rows($cek_news);
    if ($cek_news_cnt == 0) {
        echo '<script language="javascript">';
        echo 'alert("News not found!")';
        echo '</script>';
    } else {
        $url_news = mysqli_fetch_array($cek_news);
        header("Location: https://".$url_news['web_url']."/news?id=".$encryptedData);
        die();
    }
} else {
    echo '<script language="javascript">';
    echo 'alert("NEWS not found!")';
    echo '</script>';
}
