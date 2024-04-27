<?php

// Ini akan menjadi Token Verifikasi Callback Anda yang dapat Anda peroleh dari dasbor.
// Pastikan untuk menjaga kerahasiaan token ini dan tidak mengungkapkannya kepada siapa pun.
// Token ini akan digunakan untuk melakukan verfikasi pesan callback bahwa pengirim callback tersebut adalah Xendit
$xenditXCallbackToken = 'xnd_public_development_Gvveev_B6FA99XN6OkEfysjQYw4zrVaMy0Tf6eMcmyrF2AYBKq0TM_WINMQJWtfa';

// Bagian ini untuk mendapatkan Token callback dari permintaan header,
// yang kemudian akan dibandingkan dengan token verifikasi callback Xendit
$reqHeaders = getallheaders();
$xIncomingCallbackTokenHeader = isset($reqHeaders['x-callback-token']) ? $reqHeaders['x-callback-token'] : "";

// Untuk memastikan permintaan datang dari Xendit
// Anda harus membandingkan token yang masuk sama dengan token verifikasi callback Anda
// Ini untuk memastikan permintaan datang dari Xendit dan bukan dari pihak ketiga lainnya.
if ($xIncomingCallbackTokenHeader === $xenditXCallbackToken) {
    // Permintaan masuk diverifikasi berasal dari Xendit

    // Baris ini untuk mendapatkan semua input pesan dalam format JSON teks mentah
    $rawRequestInput = file_get_contents("php://input");
    // Baris ini melakukan format input mentah menjadi array asosiatif
    $arrRequestInput = json_decode($rawRequestInput, true);
    print_r($arrRequestInput);

    $_id = $arrRequestInput['id'];
    $_externalId = $arrRequestInput['external_id'];
    $_userId = $arrRequestInput['user_id'];
    $_status = $arrRequestInput['status'];
    $_paidAmount = $arrRequestInput['paid_amount'];
    $_paidAt = $arrRequestInput['paid_at'];
    $_paymentChannel = $arrRequestInput['payment_channel'];
    $_paymentDestination = $arrRequestInput['payment_destination'];

    // Kamu bisa menggunakan array objek diatas sebagai informasi callback yang dapat digunaka untuk melakukan pengecekan atau aktivas tertentu di aplikasi atau sistem kamu.

} else {
    // Permintaan bukan dari Xendit, tolak dan buang pesan dengan HTTP status 403
    http_response_code(403);
}
