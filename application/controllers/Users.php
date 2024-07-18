<?php

defined('BASEPATH') or exit('No direct script access allowed');

use chriskacerguis\RestServer\RestController;

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, PUT, PATCH, POST, DELETE');
header("Access-Control-Allow-Headers: X-Requested-With");

class Users extends RestController
{

  public function __construct()
  {
    parent::__construct();
  }

  public function index_get()
  {
    $id = $this->get('id');
    if ($id == '') {
      $users = $this->mCore->get_data('users', ['is_active' => 1])->result_array();
      if ($users) {
        $this->response([
          'status' => true,
          'data' => $users,
        ], 200);
      } else {
        $this->response([
          'status' => false,
          'message' => 'No result were found',
        ], 404);
      }
    } else {
      $users = $this->mCore->get_data('users', ['id' => $id, 'is_active' => 1])->row_array();
      if ($users) {
        $this->response([
          'status' => true,
          'data' => $users,
        ], 200);
      } else {
        $this->response([
          'status' => false,
          'message' => 'No result were found',
        ], 404);
      }
    }
  }

  public function check_email_get()
  {
    $email = $this->get('email');
    $users = $this->mCore->get_data('users', ['email' => $email, 'is_active' => 1])->row_array();
    if ($users) {
      $this->response([
        'status' => true,
        'data' => $users,
      ], 200);
    } else {
      $this->response([
        'status' => false,
        'message' => 'No result were found',
      ], 404);
    }
  }
  //SIMPAN DATA
  public function save_post()
  {
    // cek data jika sudah terdaftar di program itu
    $opt = array(
      'select' => 'participants.*',
      'table' => 'users',
      'join' => ['participants' => 'users.id = participants.user_id'],
      'where' => ['email' => $this->post('email'), 'program_id' => $this->post('program_id')],
    );

    $check_data = $this->mCore->join_table($opt)->num_rows();
    if ($check_data) {
      $this->response([
        'status' => false,
        'message' => "You are already registered as a participant. Please sign in to continue.",
      ], 404);
    } else {
      $data = array(
        'full_name' => $this->post('full_name'),
        'email' => $this->post('email'),
        'password' => md5($this->post('password')),
        'program_category_id' => $this->post('program_category_id'),
        'created_at' => date('Y-m-d H:i:s'),
        'updated_at' => date('Y-m-d H:i:s'),
      );
      $sql = $this->mCore->save_data('users', $data);
      if ($sql) {
        $last_id = $this->mCore->get_lastid('users', 'id');

        //insert data participants
        $ref_code = null;
        if ($this->post('ref_code')) {
          $ref_code = $this->post('ref_code');
        }
        $participants = array(
          'user_id' => $last_id,
          'account_id' => uniqid($last_id),
          'full_name' => $data['full_name'],
          'ref_code_ambassador' => $ref_code,
          'program_id' => $this->post('program_id'),
          'created_at' => date('Y-m-d H:i:s'),
          'updated_at' => date('Y-m-d H:i:s'),
        );
        $this->mCore->save_data('participants', $participants);
        $last_participant_id = $this->mCore->get_lastid('participants', 'id');

        // participant statues
        $participant_statuses = array(
          'participant_id' => $last_participant_id,
          'general_status' => 0,
          'form_status' => 0,
          'document_status' => 0,
          'payment_status' => 0,
          'created_at' => date('Y-m-d H:i:s'),
          'updated_at' => date('Y-m-d H:i:s'),
        );
        $this->mCore->save_data('participant_statuses', $participant_statuses);

        $last_data = $this->mCore->get_data('users', ['id' => $last_id])->row();
        $this->response([
          'status' => true,
          'data' => $last_data,
        ], 200);
      } else {
        $this->response([
          'status' => false,
          'message' => 'Sorry, failed to save',
        ], 404);
      }
    }
  }

  // SIGNIN
  public function signin_post()
  {
    $id_login = $this->mCore->do_signin_user($this->post('email'), $this->post('password'));
    if ($id_login) {
      $sql = $this->mCore->get_data('users', ['id' => $id_login])->row_array();
      unset($sql['password']);
      $this->response([
        'status' => true,
        'data' => $sql,
      ], 200);
    } else {
      $this->response([
        'status' => false,
        'message' => 'Email/Password are Incorrect!',
      ], 404);
    }
  }

  //UPDATE DATA
  public function update_post($id)
  {
    $data = array(
      'full_name' => $this->post('full_name'),
      'email' => $this->post('email'),
      // 'program_category_id' => $this->put('program_category_id'),
      'updated_at' => date('Y-m-d H:i:s'),
    );
    $sql = $this->mCore->save_data('users', array_filter($data), true, ['id' => $id]);
    if ($sql) {
      $last_data = $this->mCore->get_data('users', ['id' => $id])->row_array();
      $this->response([
        'status' => true,
        'data' => $last_data,
      ], 200);
    } else {
      $this->response([
        'status' => false,
        'message' => 'Sorry, failed to update',
      ], 404);
    }
  }

  //UPDATE DATA
  public function update_password_put()
  {
    $new_password = $this->put('password');
    $new_password_confirm = $this->put('password_confirm');
    if ($new_password != $new_password_confirm) {
      $this->response([
        'status' => false,
        'message' => 'Sorry, the password is not the same',
      ], 404);
    }

    $id = $this->put('id');

    $data = array(
      'password' => md5($this->put('password')),
      'updated_at' => date('Y-m-d H:i:s'),
    );
    $sql = $this->mCore->save_data('users', $data, true, ['id' => $id]);
    if ($sql) {
      $this->response([
        'status' => true,
        'message' => 'Data saved successfully',
      ], 200);
    } else {
      $this->response([
        'status' => false,
        'message' => 'Sorry, failed to update',
      ], 404);
    }
  }

  //DELETE DATA
  public function delete_get()
  {
    $id = $this->get('id');
    $data = array(
      'is_active' => 0,
      'is_deleted' => 1,
      // 'updated_at' => date('Y-m-d H:i:s')
    );
    $sql = $this->mCore->save_data('users', $data, true, ['id' => $id]);
    if ($sql) {
      $this->response([
        'status' => true,
        'message' => 'Data deleted successfully',
      ], 200);
    } else {
      $this->response([
        'status' => false,
        'message' => 'Sorry, failed to delete',
      ], 404);
    }
  }

  // VERIF EMAIL
  public function email_verif_post()
  {
    $opt = array(
      'select' => 'users.id, users.full_name, users.email, programs.name, programs.logo_url, program_categories.web_url',
      'table' => 'users',
      'join' => [
        'participants' => 'participants.user_id = users.id',
        'programs' => 'participants.program_id = programs.id',
        'program_categories' => 'programs.program_category_id = program_categories.id',
      ],
      'where' => 'users.id = ' . $this->post('id'),
    );
    $data = $this->mCore->join_table($opt)->row_array();

    $config = array(
      'protocol' => 'smtp',
      'smtp_host' => 'ssl://smtp.googlemail.com',
      'smtp_port' => 465,
      'smtp_user' => config_item('user_email'),
      'smtp_pass' => config_item('pass_email'),
      'mailtype' => 'html',
      'charset' => 'iso-8859-1',
      'wordwrap' => true,
    );

    $message = ('
        <!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>

<head>
  <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
  <meta property="og:title" content="Verify Your Email">
  <title>Verify Your Email</title>
  <style type="text/css">
    #outlook a {
      padding: 0;
    }

    body {
      width: 100% !important;
    }

    .ReadMsgBody {
      width: 100%;
    }

    .ExternalClass {
      width: 100%;
    }

    body {
      -webkit-text-size-adjust: none;
    }

    body {
      margin: 0;
      padding: 0;
    }

    img {
      border: 0;
      height: auto;
      line-height: 100%;
      outline: none;
      text-decoration: none;
    }

    table td {
      border-collapse: collapse;
    }

    #backgroundTable {
      height: 100% !important;
      margin: 0;
      padding: 0;
      width: 100% !important;
    }

    body,
    #backgroundTable {
      background-color: #FAFAFA;
    }

    #templateContainer {
      border: 1px none #DDDDDD;
    }

    h1,
    .h1 {

      color: #202020;
      display: block;

      font-family: Arial;

      font-size: 24px;

      font-weight: bold;

      line-height: 100%;
      margin-top: 20px;
      margin-right: 0;
      margin-bottom: 20px;
      margin-left: 0;

      text-align: center;
    }

    h2,
    .h2 {

      color: #202020;
      display: block;

      font-family: Arial;

      font-size: 30px;

      font-weight: bold;

      line-height: 100%;
      margin-top: 0;
      margin-right: 0;
      margin-bottom: 10px;
      margin-left: 0;

      text-align: center;
    }

    h3,
    .h3 {

      color: #202020;
      display: block;

      font-family: Arial;

      font-size: 26px;

      font-weight: bold;

      line-height: 100%;
      margin-top: 0;
      margin-right: 0;
      margin-bottom: 10px;
      margin-left: 0;

      text-align: center;
    }

    h4,
    .h4 {

      color: #202020;
      display: block;

      font-family: Arial;

      font-size: 22px;

      font-weight: bold;

      line-height: 100%;
      margin-top: 0;
      margin-right: 0;
      margin-bottom: 10px;
      margin-left: 0;

      text-align: center;
    }

    #templatePreheader {

      background-color: #FAFAFA;
    }

    .preheaderContent div {

      color: #505050;

      font-family: Arial;

      font-size: 10px;

      line-height: 100%;

      text-align: left;
    }

    .preheaderContent div a:link,
    .preheaderContent div a:visited,
    .preheaderContent div a .yshortcuts {

      color: #336699;

      font-weight: normal;

      text-decoration: underline;
    }

    .preheaderContent img {
      display: inline;
      height: auto;
      margin-bottom: 10px;
      max-width: 280px;
    }

    #templateHeader {

      background-color: #FFFFFF;

      border-bottom: 0;
    }

    .headerContent {

      color: #202020;

      font-family: Arial;

      font-size: 34px;

      font-weight: bold;

      line-height: 100%;

      padding: 0;

      text-align: left;

      vertical-align: middle;
      background-color: #FAFAFA;
      padding-bottom: 14px;
    }

    .headerContent a:link,
    .headerContent a:visited,
    .headerContent a .yshortcuts {

      color: #336699;

      font-weight: normal;

      text-decoration: underline;
    }

    #headerImage {
      height: auto;
      max-width: 400px !important;
    }

    #templateContainer,
    .bodyContent {

      background-color: #FFFFFF;
    }

    .bodyContent div {

      color: #505050;

      font-family: Arial;

      font-size: 14px;

      line-height: 150%;

      text-align: left;
    }

    .bodyContent div a:link,
    .bodyContent div a:visited,
    .bodyContent div a .yshortcuts {

      color: #336699;

      font-weight: normal;

      text-decoration: underline;
    }

    .bodyContent img {
      display: inline;
      height: auto;
      margin-bottom: 10px;
      max-width: 280px;
    }

    #templateFooter {

      background-color: #FFFFFF;

      border-top: 0;
    }

    .footerContent {
      background-color: #fafafa;
    }

    .footerContent div {

      color: #707070;

      font-family: Arial;

      font-size: 11px;

      line-height: 150%;

      text-align: left;
    }

    .footerContent div a:link,
    .footerContent div a:visited,
    .footerContent div a .yshortcuts {

      color: #336699;

      font-weight: normal;

      text-decoration: underline;
    }

    .footerContent img {
      display: inline;
    }

    #social {

      background-color: #FAFAFA;

      border: 0;
    }

    #social div {

      text-align: left;
    }

    #utility {

      background-color: #FFFFFF;

      border: 0;
    }

    #utility div {

      text-align: left;
    }

    #monkeyRewards img {
      display: inline;
      height: auto;
      max-width: 280px;
    }

    .buttonText {
      color: #4A90E2;
      text-decoration: none;
      font-weight: normal;
      display: block;
      border: 2px solid #585858;
      padding: 10px 80px;
      font-family: Arial;
    }

    #supportSection,
    .supportContent {
      background-color: white;
      font-family: arial;
      font-size: 12px;
      border-top: 1px solid #e4e4e4;
    }

    .bodyContent table {
      padding-bottom: 10px;
    }


    .footerContent p {
      margin: 0;
      margin-top: 2px;
    }

    .headerContent.centeredWithBackground {
      background-color: #F4EEE2;
      text-align: center;
      padding-top: 20px;
      padding-bottom: 20px;
    }

    @media only screen and (min-device-width: 320px) and (max-device-width: 480px) {
      h1 {
        font-size: 40px !important;
      }

      .content {
        font-size: 22px !important;
      }

      .bodyContent p {
        font-size: 22px !important;
      }

      .buttonText {
        font-size: 22px !important;
      }

      p {

        font-size: 16px !important;

      }

      .footerContent p {
        padding-left: 5px !important;
      }

      .mainContainer {
        padding-bottom: 0 !important;
      }
    }
  </style>
</head>

<body leftmargin="0" marginwidth="0" topmargin="0" marginheight="0" offset="0" style="width:100% ;-webkit-text-size-adjust:none;margin:0;padding:0;background-color:#FAFAFA;">
  <center>
    <table border="0" cellpadding="0" cellspacing="0" height="100%" width="100%" id="backgroundTable" style="height:100% ;margin:0;padding:0;width:100% ;background-color:#FAFAFA;">
      <tr>
        <td align="center" valign="top" style="border-collapse:collapse;">
          <table border="0" cellpadding="10" cellspacing="0" width="450" id="templatePreheader" style="background-color:#FAFAFA;">
            <tr>
              <table border="0" cellpadding="10" cellspacing="0" width="100%">
                <tr>
                  <td valign="top" style="border-collapse:collapse;">
                  </td>
                </tr>
              </table>
            </tr>
        </td>
      </tr>
    </table>
    <table border="0" cellpadding="0" cellspacing="0" width="450" id="templateContainer" style="border:1px none #DDDDDD;background-color:#FFFFFF;">
      <tr>
        <td align="center" valign="top" style="border-collapse:collapse;">
          <table border="0" cellpadding="0" cellspacing="0" width="450" id="templateHeader" style="background-color:#FFFFFF;border-bottom:0;">
            <tr>
              <td class="headerContent centeredWithBackground" style="border-collapse:collapse;color:#202020;font-family:Arial;font-size:34px;font-weight:bold;line-height:100%;padding:0;text-align:center;vertical-align:middle;background-color:#F4EEE2;padding-bottom:20px;padding-top:20px;">
                <img width="130" src="' . $data['logo_url'] . '" style="width:130px;max-width:130px;border:0;height:auto;line-height:100%;outline:none;text-decoration:none;" id="headerImage campaign-icon">
              </td>
            </tr>
          </table>
        </td>
      </tr>
      <tr>
        <td align="center" valign="top" style="border-collapse:collapse;">
          <table border="0" cellpadding="0" cellspacing="0" width="450" id="templateBody">
            <tr>
              <td valign="top" class="bodyContent" style="border-collapse:collapse;background-color:#FFFFFF;">
                <table border="0" cellpadding="20" cellspacing="0" width="100%" style="padding-bottom:10px;">
                  <tr>
                    <td valign="top" style="padding-bottom:1rem;border-collapse:collapse;" class="mainContainer">
                      <div style="text-align:left;color:#505050;font-family:Arial;">
                        <p>
                        <div style="color: #202020;display: block;font-family: Arial;font-size: 18px;font-weight: bold;line-height: 100%;margin-top: 0;margin-right: 0;margin-bottom: 10px;margin-left: 0;">Dear ' . $data['full_name'] . ',</div>
                        Thank you for registering for the ' . $data['name'] . '!<br><br>
                        To complete your participation and complete registration, please verify your email address by clicking the link below:
                        </p>
                    </td>
                  </tr>
                  <tr>
                    <td align="center" style="border-collapse:collapse;">
                      <table border="0" cellpadding="0" cellspacing="0" style="padding-bottom:10px;">
                        <tbody>
                          <tr align="center">
                            <td align="center" valign="middle" style="border-collapse:collapse;">
                              <a class="buttonText" href="https://master-api.ybbfoundation.com/users/verif?id=' . $data['id'] . '" target="_blank" style="color: #fff;text-decoration: none;font-weight: normal;display: block;border:none;border-radius:3px;padding: 10px 80px;font-family: Arial;background-color:#7289DA">Verify Account</a>
                            </td>
                          </tr>
                        </tbody>
                      </table>
                    </td>
                  </tr>
                  <tr>
                    <td align="center" style="border-collapse:collapse;">
                      <table border="0" cellpadding="0" cellspacing="0" style="padding-bottom:10px;">
                        <tbody>
                          <tr>
                            <td style="border-collapse:collapse;">
                              <p style="text-align:left;color:#505050;font-family:Arial;font-size:14px;">
                                If you did not register for the ' . $data['name'] . ' or received this email in error, please disregard this message.<br><br>
                                We look forward to your active participation in the program!<br>
                                <br>
                                Best regards,
                                ' . $data['name'] . ' Team
                            </td>
                          </tr>
                        </tbody>
                      </table>
                    </td>
                  </tr>
                  </p>
                </table>
              </td>
            </tr>
          </table>
        </td>
      </tr>
      <tr>
        <td align="center" valign="top" style="border-collapse:collapse;">
          <table border="0" cellpadding="10" cellspacing="0" width="450" id="supportSection" style="background-color:white;font-family:arial;font-size:12px;border-top:1px solid #e4e4e4;">
            <tr>
              <td valign="top" class="supportContent" style="border-collapse:collapse;background-color:white;font-family:arial;font-size:12px;border-top:1px solid #e4e4e4;">

                <table border="0" cellpadding="10" cellspacing="0" width="100%">
                  <tr>
                    <td valign="top" width="100%" style="border-collapse:collapse;">
                      <br>
                      <div style="text-align: center; color: #c9c9c9;">
                        <p>Questions? Get your answers here:&nbsp;
                          <a href="' . $data['web_url'] . '/faq" style="color:#4a90e2;font-weight:normal;text-decoration:underline; font-size: 12px;">FAQ</a>.
                        </p>
                      </div>
                      <br>
                    </td>
                  </tr>
                </table>
              </td>
            </tr>
          </table>
        </td>
      </tr>
      <tr>
        <td align="center" valign="top" style="border-collapse:collapse;">
          <table border="0" cellpadding="10" cellspacing="0" width="450" id="templateFooter" style="background-color:#FFFFFF;border-top:0;">
            <tr>
              <td valign="top" class="footerContent" style="padding-left:0;border-collapse:collapse;background-color:#fafafa;">
                <div style="text-align:center;color:#c9c9c9;font-family:Arial;font-size:11px;line-height:150%;">
                  <p style="text-align:left;margin:0;margin-top:2px;">YBB Foundation | Copyright © 2024 | All rights reserved</p>
                </div>
              </td>
            </tr>
          </table>
        </td>
      </tr>
    </table>
    <br>
    </td>
    </tr>
    </table>
  </center>
</body>
</html>');

    $this->load->library('email', $config);
    $this->email->set_mailtype("html");
    $this->email->set_newline("\r\n");
    $this->email->set_crlf("\r\n");
    $this->email->from('paywithalla@gmail.com');
    $this->email->to($data['email']);
    $this->email->subject('Verify Your Email Address for ' . $data['name']);
    $this->email->message($message);
    if ($this->email->send()) {
      $this->response([
        'status' => true,
        'message' => 'The verification email has been successfully sent, check your inbox or spam.',
      ], 200);
    } else {
      $this->response([
        'status' => false,
        'message' => $this->email->print_debugger(),
      ], 404);
    }
  }

  //VERIF DATA
  public function verif_get()
  {
    $id = $this->get('id');
    $data = array(
      'is_verified' => 1,
      'updated_at' => date('Y-m-d H:i:s'),
    );
    $sql = $this->mCore->save_data('users', $data, true, ['id' => $id]);
    if ($sql) {
      $opt = array(
        'select' => 'users.id, users.full_name, users.email, programs.name, programs.logo_url, program_categories.web_url',
        'table' => 'users',
        'join' => [
          'participants' => 'participants.user_id = users.id',
          'programs' => 'participants.program_id = programs.id',
          'program_categories' => 'programs.program_category_id = program_categories.id',
        ],
        'where' => 'users.id = ' . $id,
      );
      $data = $this->mCore->join_table($opt)->row_array();

      $data_view = array(
        'logo_url' => $data['logo_url'],
        'web_url' => $data['web_url']
      );

      $this->load->view("success_verif", $data_view);
    } else {
      $this->response([
        'status' => false,
        'message' => 'Sorry, failed to verification',
      ], 404);
    }
  }

  // RESET PASSWORD
  public function email_reset_password_post()
  {
    $opt = array(
      'select' => 'users.id, users.full_name, users.email, programs.name, programs.logo_url, program_categories.web_url',
      'table' => 'users',
      'join' => [
        'participants' => 'participants.user_id = users.id',
        'programs' => 'participants.program_id = programs.id',
        'program_categories' => 'programs.program_category_id = program_categories.id',
      ],
      'where' => 'users.id = ' . $this->post('id'),
    );
    $data = $this->mCore->join_table($opt)->row_array();

    // encrypt
    $id_encrypt = $data['id'];
    $method = "AES-256-CBC";
    $key = "encryptionKey123";
    $options = 0;
    $iv = '1234567891011121';

    $encryptedData = openssl_encrypt($id_encrypt, $method, $key, $options, $iv);

    $config = array(
      'protocol' => 'smtp',
      'smtp_host' => 'ssl://smtp.googlemail.com',
      'smtp_port' => 465,
      'smtp_user' => config_item('user_email'),
      'smtp_pass' => config_item('pass_email'),
      'mailtype' => 'html',
      'charset' => 'iso-8859-1',
      'wordwrap' => true,
    );

    $message = ('
        <!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>

<head>
  <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
  <meta property="og:title" content="Verify Your Email">
  <title>Verify Your Email</title>
  <style type="text/css">
    #outlook a {
      padding: 0;
    }

    body {
      width: 100% !important;
    }

    .ReadMsgBody {
      width: 100%;
    }

    .ExternalClass {
      width: 100%;
    }

    body {
      -webkit-text-size-adjust: none;
    }

    body {
      margin: 0;
      padding: 0;
    }

    img {
      border: 0;
      height: auto;
      line-height: 100%;
      outline: none;
      text-decoration: none;
    }

    table td {
      border-collapse: collapse;
    }

    #backgroundTable {
      height: 100% !important;
      margin: 0;
      padding: 0;
      width: 100% !important;
    }

    body,
    #backgroundTable {
      background-color: #FAFAFA;
    }

    #templateContainer {
      border: 1px none #DDDDDD;
    }

    h1,
    .h1 {

      color: #202020;
      display: block;

      font-family: Arial;

      font-size: 24px;

      font-weight: bold;

      line-height: 100%;
      margin-top: 20px;
      margin-right: 0;
      margin-bottom: 20px;
      margin-left: 0;

      text-align: center;
    }

    h2,
    .h2 {

      color: #202020;
      display: block;

      font-family: Arial;

      font-size: 30px;

      font-weight: bold;

      line-height: 100%;
      margin-top: 0;
      margin-right: 0;
      margin-bottom: 10px;
      margin-left: 0;

      text-align: center;
    }

    h3,
    .h3 {

      color: #202020;
      display: block;

      font-family: Arial;

      font-size: 26px;

      font-weight: bold;

      line-height: 100%;
      margin-top: 0;
      margin-right: 0;
      margin-bottom: 10px;
      margin-left: 0;

      text-align: center;
    }

    h4,
    .h4 {

      color: #202020;
      display: block;

      font-family: Arial;

      font-size: 22px;

      font-weight: bold;

      line-height: 100%;
      margin-top: 0;
      margin-right: 0;
      margin-bottom: 10px;
      margin-left: 0;

      text-align: center;
    }

    #templatePreheader {

      background-color: #FAFAFA;
    }

    .preheaderContent div {

      color: #505050;

      font-family: Arial;

      font-size: 10px;

      line-height: 100%;

      text-align: left;
    }

    .preheaderContent div a:link,
    .preheaderContent div a:visited,
    .preheaderContent div a .yshortcuts {

      color: #336699;

      font-weight: normal;

      text-decoration: underline;
    }

    .preheaderContent img {
      display: inline;
      height: auto;
      margin-bottom: 10px;
      max-width: 280px;
    }

    #templateHeader {

      background-color: #FFFFFF;

      border-bottom: 0;
    }

    .headerContent {

      color: #202020;

      font-family: Arial;

      font-size: 34px;

      font-weight: bold;

      line-height: 100%;

      padding: 0;

      text-align: left;

      vertical-align: middle;
      background-color: #FAFAFA;
      padding-bottom: 14px;
    }

    .headerContent a:link,
    .headerContent a:visited,
    .headerContent a .yshortcuts {

      color: #336699;

      font-weight: normal;

      text-decoration: underline;
    }

    #headerImage {
      height: auto;
      max-width: 400px !important;
    }

    #templateContainer,
    .bodyContent {

      background-color: #FFFFFF;
    }

    .bodyContent div {

      color: #505050;

      font-family: Arial;

      font-size: 14px;

      line-height: 150%;

      text-align: left;
    }

    .bodyContent div a:link,
    .bodyContent div a:visited,
    .bodyContent div a .yshortcuts {

      color: #336699;

      font-weight: normal;

      text-decoration: underline;
    }

    .bodyContent img {
      display: inline;
      height: auto;
      margin-bottom: 10px;
      max-width: 280px;
    }

    #templateFooter {

      background-color: #FFFFFF;

      border-top: 0;
    }

    .footerContent {
      background-color: #fafafa;
    }

    .footerContent div {

      color: #707070;

      font-family: Arial;

      font-size: 11px;

      line-height: 150%;

      text-align: left;
    }

    .footerContent div a:link,
    .footerContent div a:visited,
    .footerContent div a .yshortcuts {

      color: #336699;

      font-weight: normal;

      text-decoration: underline;
    }

    .footerContent img {
      display: inline;
    }

    #social {

      background-color: #FAFAFA;

      border: 0;
    }

    #social div {

      text-align: left;
    }

    #utility {

      background-color: #FFFFFF;

      border: 0;
    }

    #utility div {

      text-align: left;
    }

    #monkeyRewards img {
      display: inline;
      height: auto;
      max-width: 280px;
    }

    .buttonText {
      color: #4A90E2;
      text-decoration: none;
      font-weight: normal;
      display: block;
      border: 2px solid #585858;
      padding: 10px 80px;
      font-family: Arial;
    }

    #supportSection,
    .supportContent {
      background-color: white;
      font-family: arial;
      font-size: 12px;
      border-top: 1px solid #e4e4e4;
    }

    .bodyContent table {
      padding-bottom: 10px;
    }


    .footerContent p {
      margin: 0;
      margin-top: 2px;
    }

    .headerContent.centeredWithBackground {
      background-color: #F4EEE2;
      text-align: center;
      padding-top: 20px;
      padding-bottom: 20px;
    }

    @media only screen and (min-device-width: 320px) and (max-device-width: 480px) {
      h1 {
        font-size: 40px !important;
      }

      .content {
        font-size: 22px !important;
      }

      .bodyContent p {
        font-size: 22px !important;
      }

      .buttonText {
        font-size: 22px !important;
      }

      p {

        font-size: 16px !important;

      }

      .footerContent p {
        padding-left: 5px !important;
      }

      .mainContainer {
        padding-bottom: 0 !important;
      }
    }
  </style>
</head>

<body leftmargin="0" marginwidth="0" topmargin="0" marginheight="0" offset="0" style="width:100% ;-webkit-text-size-adjust:none;margin:0;padding:0;background-color:#FAFAFA;">
  <center>
    <table border="0" cellpadding="0" cellspacing="0" height="100%" width="100%" id="backgroundTable" style="height:100% ;margin:0;padding:0;width:100% ;background-color:#FAFAFA;">
      <tr>
        <td align="center" valign="top" style="border-collapse:collapse;">
          <table border="0" cellpadding="10" cellspacing="0" width="450" id="templatePreheader" style="background-color:#FAFAFA;">
            <tr>
              <table border="0" cellpadding="10" cellspacing="0" width="100%">
                <tr>
                  <td valign="top" style="border-collapse:collapse;">
                  </td>
                </tr>
              </table>
            </tr>
        </td>
      </tr>
    </table>
    <table border="0" cellpadding="0" cellspacing="0" width="450" id="templateContainer" style="border:1px none #DDDDDD;background-color:#FFFFFF;">
      <tr>
        <td align="center" valign="top" style="border-collapse:collapse;">
          <table border="0" cellpadding="0" cellspacing="0" width="450" id="templateHeader" style="background-color:#FFFFFF;border-bottom:0;">
            <tr>
              <td class="headerContent centeredWithBackground" style="border-collapse:collapse;color:#202020;font-family:Arial;font-size:34px;font-weight:bold;line-height:100%;padding:0;text-align:center;vertical-align:middle;background-color:#F4EEE2;padding-bottom:20px;padding-top:20px;">
                <img width="130" src="' . $data['logo_url'] . '" style="width:130px;max-width:130px;border:0;height:auto;line-height:100%;outline:none;text-decoration:none;" id="headerImage campaign-icon">
              </td>
            </tr>
          </table>
        </td>
      </tr>
      <tr>
        <td align="center" valign="top" style="border-collapse:collapse;">
          <table border="0" cellpadding="0" cellspacing="0" width="450" id="templateBody">
            <tr>
              <td valign="top" class="bodyContent" style="border-collapse:collapse;background-color:#FFFFFF;">
                <table border="0" cellpadding="20" cellspacing="0" width="100%" style="padding-bottom:10px;">
                  <tr>
                    <td valign="top" style="padding-bottom:1rem;border-collapse:collapse;" class="mainContainer">
                      <div style="text-align:left;color:#505050;font-family:Arial;">
                        <p>
                        <div style="color: #202020;display: block;font-family: Arial;font-size: 18px;font-weight: bold;line-height: 100%;margin-top: 0;margin-right: 0;margin-bottom: 10px;margin-left: 0;">Dear ' . $data['full_name'] . ',</div>
                        <br>
                        It seems you`ve forgotten your password for ' . $data['name'] . '. Don`t worry, we`re here to help!<br><br>
                        Please click the link below to reset your password:
                        </p>
                    </td>
                  </tr>
                  <tr>
                    <td align="center" style="border-collapse:collapse;">
                      <table border="0" cellpadding="0" cellspacing="0" style="padding-bottom:10px;">
                        <tbody>
                          <tr align="center">
                            <td align="center" valign="middle" style="border-collapse:collapse;">
                              <a class="buttonText" href="https://redirect.ybbfoundation.com/reset-password.php?id=' . $encryptedData . '" target="_blank" style="color: #fff;text-decoration: none;font-weight: normal;display: block;border:none;border-radius:3px;padding: 10px 80px;font-family: Arial;background-color:#7289DA">Reset Password</a>
                            </td>
                          </tr>
                        </tbody>
                      </table>
                    </td>
                  </tr>
                  <tr>
                    <td align="center" style="border-collapse:collapse;">
                      <table border="0" cellpadding="0" cellspacing="0" style="padding-bottom:10px;">
                        <tbody>
                          <tr>
                            <td style="border-collapse:collapse;">
                              <p style="text-align:left;color:#505050;font-family:Arial;font-size:14px;">
                              If you did not request a password reset or believe you received this email in error, please ignore it.
                                <br>
                                <br>
                                Thank you,
                                ' . $data['name'] . ' Team
                            </td>
                          </tr>
                        </tbody>
                      </table>
                    </td>
                  </tr>
                  </p>
                </table>
              </td>
            </tr>
          </table>
        </td>
      </tr>
      <tr>
        <td align="center" valign="top" style="border-collapse:collapse;">
          <table border="0" cellpadding="10" cellspacing="0" width="450" id="supportSection" style="background-color:white;font-family:arial;font-size:12px;border-top:1px solid #e4e4e4;">
            <tr>
              <td valign="top" class="supportContent" style="border-collapse:collapse;background-color:white;font-family:arial;font-size:12px;border-top:1px solid #e4e4e4;">

                <table border="0" cellpadding="10" cellspacing="0" width="100%">
                  <tr>
                    <td valign="top" width="100%" style="border-collapse:collapse;">
                      <br>
                      <div style="text-align: center; color: #c9c9c9;">
                        <p>Questions? Get your answers here:&nbsp;
                          <a href="' . $data['web_url'] . '/faq" style="color:#4a90e2;font-weight:normal;text-decoration:underline; font-size: 12px;">FAQ</a>.
                        </p>
                      </div>
                      <br>
                    </td>
                  </tr>
                </table>
              </td>
            </tr>
          </table>
        </td>
      </tr>
      <tr>
        <td align="center" valign="top" style="border-collapse:collapse;">
          <table border="0" cellpadding="10" cellspacing="0" width="450" id="templateFooter" style="background-color:#FFFFFF;border-top:0;">
            <tr>
              <td valign="top" class="footerContent" style="padding-left:0;border-collapse:collapse;background-color:#fafafa;">
                <div style="text-align:center;color:#c9c9c9;font-family:Arial;font-size:11px;line-height:150%;">
                  <p style="text-align:left;margin:0;margin-top:2px;">YBB Foundation | Copyright © 2024 | All rights reserved</p>
                </div>
              </td>
            </tr>
          </table>
        </td>
      </tr>
    </table>
    <br>
    </td>
    </tr>
    </table>
  </center>
</body>
</html>');

    $this->load->library('email', $config);
    $this->email->set_mailtype("html");
    $this->email->set_newline("\r\n");
    $this->email->set_crlf("\r\n");
    $this->email->from('paywithalla@gmail.com');
    $this->email->to($data['email']);
    $this->email->subject('Reset Your ' . $data['name'] . ' Account Password');
    $this->email->message($message);
    if ($this->email->send()) {
      $this->response([
        'status' => true,
        'message' => 'The reset password has been successfully sent, check your inbox or spam.',
      ], 200);
    } else {
      $this->response([
        'status' => false,
        'message' => $this->email->print_debugger(),
      ], 404);
    }
  }
}
