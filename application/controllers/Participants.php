<?php

defined('BASEPATH') or exit('No direct script access allowed');

use chriskacerguis\RestServer\RestController;
use Ramsey\Uuid\Uuid;

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, PUT, PATCH, POST, DELETE');
header("Access-Control-Allow-Headers: X-Requested-With");

class Participants extends RestController
{

    function __construct()
    {
        parent::__construct();
    }

    function index_get()
    {
        $id = $this->get('id');
        if ($id == '') {
            $participants = $this->mCore->list_data('participants')->result_array();
            if ($participants) {
                $this->response([
                    'status' => true,
                    'data' => $participants
                ], 200);
            } else {
                $this->response([
                    'status' => false,
                    'message' => 'No result were found'
                ], 404);
            }
        } else {
            $participants = $this->mCore->get_data('participants', ['id' => $id])->row_array();
            if ($participants) {
                $this->response([
                    'status' => true,
                    'data' => $participants
                ], 200);
            } else {
                $this->response([
                    'status' => false,
                    'message' => 'No result were found'
                ], 404);
            }
        }
    }

    //SIMPAN DATA
    function save_post($ref_code = NULL)
    {
        $data = array(
            'user_id' => $this->post('user_id'),
            'account_id' => uniqid($this->post('user_id')),
            'full_name' => $this->post('full_name'),
            'ref_code_ambassador' => $ref_code,
            'birthdate' => $this->post('birthdate'),
            'nationality' => $this->post('nationality'),
            'gender' => $this->post('gender'),
            'phone_number' => $this->post('phone_number'),
            'country_code' => $this->post('country_code'),
            'picture_url' => NULL,
            'program_id' => $this->post('program_id'),
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        );
        $sql = $this->mCore->save_data('participants', $data);
        if ($sql) {
            $last_id = $this->mCore->get_lastid('participants', 'id');
            if (!empty($_FILES['picture_url']['name'])) {
                $upload_file = $this->upload_picture('picture_url', $last_id);
                if ($upload_file['status'] == 0) {
                    $this->response([
                        'status' => false,
                        'message' => $upload_file['message']
                    ], 404);
                }
            }
            $last_data = $this->mCore->get_data('participants', ['id' => $last_id])->row_array();
            $this->response([
                'status' => true,
                'data' => $last_data
            ], 200);
        } else {
            $this->response([
                'status' => false,
                'message' => 'Sorry, failed to save'
            ], 404);
        }
    }

    // SIGNIN
    function signin_post()
    {
        $id_login = $this->mCore->do_signin_participant($this->post('email'), $this->post('password'), $this->post('program_category_id'));
        if ($id_login) {
            $sql = $this->mCore->get_data('participants', ['user_id' => $id_login])->result_array();
            $this->response([
                'status' => true,
                'data' => $sql,
            ], 200);
        } else {
            $this->response([
                'status' => false,
                'message' => 'Email/Password are Incorrect!'
            ], 404);
        }
    }

    //UPDATE DATA
    function update_put()
    {
        $id = $this->put('id');
        $data = array(
            'full_name' => $this->put('full_name'),
            'birthdate' => $this->put('birthdate'),
            'nationality' => $this->put('nationality'),
            'gender' => $this->put('gender'),
            'phone_number' => $this->put('phone_number'),
            'country_code' => $this->put('country_code'),
            'program_id' => $this->put('program_id'),
            'updated_at' => date('Y-m-d H:i:s'),
        );
        $sql = $this->mCore->save_data('participants', $data, true, ['id' => $id]);
        if ($sql) {
            $last_data = $this->mCore->get_data('payment_methods', ['id' => $id])->row_array();
            $this->response([
                'status' => true,
                'data' => $last_data
            ], 200);
        } else {
            $this->response([
                'status' => false,
                'message' => 'Sorry, failed to update'
            ], 404);
        }
    }

    //DELETE DATA
    function delete_get()
    {
        $id = $this->get('id');
        $data = array(
            'is_active' => 0,
            'is_deleted' => 1
            // 'updated_at' => date('Y-m-d H:i:s')
        );
        $sql = $this->mCore->save_data('participants', $data, true, ['id' => $id]);
        if ($sql) {
            $this->response([
                'status' => true,
                'message' => 'Data deleted successfully'
            ], 200);
        } else {
            $this->response([
                'status' => false,
                'message' => 'Sorry, failed to delete'
            ], 404);
        }
    }

    // UPLOAD PICTURE
    private function upload_picture($picture_url, $id)
    {

        $this->load->library('ftp');

        $data = $this->mCore->get_data('participants', 'id = ' . $id)->row_array();
        if ($data['picture_url'] != '') {
            $exp = (explode('/', $data['picture_url']));
            $temp_img = end($exp);

            //FTP configuration
            $ftp_config['hostname'] = config_item('hostname_upload');
            $ftp_config['username'] = config_item('username_upload');
            $ftp_config['password'] = config_item('password_upload');
            $ftp_config['port'] = config_item('port_upload');
            $ftp_config['debug'] = TRUE;

            $this->ftp->connect($ftp_config);

            $this->ftp->delete_file('participants/' . $data['program_id'] . '/' . $data['uid'] . '/' . $temp_img);

            $this->ftp->close();
        }

        $config['upload_path'] = './uploads';
        $config['allowed_types'] = 'gif|jpg|png|jpeg';
        $config['max_size'] = 5000;
        $config['file_name'] = time();

        $this->load->library('upload', $config);
        $this->upload->initialize($config);
        if ($this->upload->do_upload($picture_url)) {

            $upload_data = $this->upload->data();
            $fileName = $upload_data['file_name'];

            $source = './uploads/' . $fileName;

            //FTP configuration
            $ftp_config['hostname'] = config_item('hostname_upload');
            $ftp_config['username'] = config_item('username_upload');
            $ftp_config['password'] = config_item('password_upload');
            $ftp_config['port'] = config_item('port_upload');
            $ftp_config['debug'] = TRUE;

            $this->ftp->connect($ftp_config);

            if ($this->ftp->list_files('participants/' . $data['program_id'] . '/' . $data['account_id'] . '/pictures/') == FALSE) {
                $this->ftp->mkdir('participants/' . $data['program_id'] . '/' . $data['account_id'] . '/pictures/', DIR_WRITE_MODE);
            }

            $destination = 'participants/' . $data['program_id'] . '/' . $data['account_id'] . '/pictures/' . $fileName;

            $this->ftp->upload($source, $destination);

            $this->ftp->close();

            //Delete file from local server
            @unlink($source);

            $sql = $this->mCore->save_data('participants', ['picture_url' => config_item('dir_upload') . 'participants/' . $data['program_id'] . '/' . $data['account_id'] . '/pictures/' . $fileName], true, array('id' => $id));

            if ($sql) {
                $data['status'] = 1;
                $data['message'] = 'Image saved successfully';
            } else {
                $data['status'] = 0;
                $data['message'] = 'Sorry, failed to update';
            }
        } else {
            $data['status'] = 0;
            $data['message'] = $this->upload->display_errors();
        }

        return $data;
    }

    // UPLOAD PICTURE DIRECT
    public function do_upload_picture_post()
    {

        $this->load->library('ftp');

        $id = $this->post('id');

        $data = $this->mCore->get_data('participants', 'id = ' . $id)->row_array();
        if ($data['picture_url'] != '') {
            $exp = (explode('/', $data['picture_url']));
            $temp_img = end($exp);

            //FTP configuration
            $ftp_config['hostname'] = config_item('hostname_upload');
            $ftp_config['username'] = config_item('username_upload');
            $ftp_config['password'] = config_item('password_upload');
            $ftp_config['port'] = config_item('port_upload');
            $ftp_config['debug'] = TRUE;

            $this->ftp->connect($ftp_config);

            $this->ftp->delete_file('participants/' . $data['program_id'] . '/' . $data['uid'] . '/' . $temp_img);

            $this->ftp->close();
        }

        $config['upload_path'] = './uploads';
        $config['allowed_types'] = 'gif|jpg|png|jpeg';
        $config['max_size'] = 5000;
        $config['file_name'] = time();

        $this->load->library('upload', $config);
        $this->upload->initialize($config);
        if ($this->upload->do_upload("picture")) {

            $upload_data = $this->upload->data();
            $fileName = $upload_data['file_name'];

            $source = './uploads/' . $fileName;

            //FTP configuration
            $ftp_config['hostname'] = config_item('hostname_upload');
            $ftp_config['username'] = config_item('username_upload');
            $ftp_config['password'] = config_item('password_upload');
            $ftp_config['port'] = config_item('port_upload');
            $ftp_config['debug'] = TRUE;

            $this->ftp->connect($ftp_config);

            if ($this->ftp->list_files('participants/' . $data['program_id'] . '/' . $data['account_id'] . '/pictures/') == FALSE) {
                $this->ftp->mkdir('participants/' . $data['program_id'] . '/' . $data['account_id'] . '/pictures/', DIR_WRITE_MODE);
            }

            $destination = 'participants/' . $data['program_id'] . '/' . $data['account_id'] . '/pictures/' . $fileName;

            $this->ftp->upload($source, $destination);

            $this->ftp->close();

            //Delete file from local server
            @unlink($source);

            $sql = $this->mCore->save_data('participants', ['picture_url' => config_item('dir_upload') . 'participants/' . $data['program_id'] . '/' . $data['account_id'] . '/pictures/' . $fileName], true, array('id' => $id));

            if ($sql) {
                $this->response([
                    'status' => true,
                    'message' => 'Picture saved successfully'
                ], 200);
            } else {
                $this->response([
                    'status' => false,
                    'message' => 'Sorry, failed to update'
                ], 404);
            }
        } else {
            $this->response([
                'status' => false,
                'message' => $this->upload->display_errors()
            ], 404);
        }
    }

    // UPLOAD PICTURE DIRECT
    public function do_upload_document_post()
    {

        $this->load->library('ftp');

        $id = $this->post('id');

        $data = $this->mCore->get_data('participants', 'id = ' . $id)->row_array();
        // if ($data['img_url'] != '') {
        //     $exp = (explode('/', $data['img_url']));
        //     $temp_img = end($exp);

        //     //FTP configuration
        //     $ftp_config['hostname'] = config_item('hostname_upload');
        //     $ftp_config['username'] = config_item('username_upload');
        //     $ftp_config['password'] = config_item('password_upload');
        //     $ftp_config['port'] = config_item('port_upload');
        //     $ftp_config['debug'] = TRUE;

        //     $this->ftp->connect($ftp_config);

        //     $this->ftp->delete_file('participants/' . $program_id . '/' . $temp_img);

        //     $this->ftp->close();
        // }

        $config['upload_path'] = './uploads';
        $config['allowed_types'] = '*';
        $config['max_size'] = 5000;
        $config['file_name'] = time();

        $this->load->library('upload', $config);
        $this->upload->initialize($config);
        if ($this->upload->do_upload("document")) {

            $upload_data = $this->upload->data();
            $fileName = $upload_data['file_name'];

            $source = './uploads/' . $fileName;

            //FTP configuration
            $ftp_config['hostname'] = config_item('hostname_upload');
            $ftp_config['username'] = config_item('username_upload');
            $ftp_config['password'] = config_item('password_upload');
            $ftp_config['port'] = config_item('port_upload');
            $ftp_config['debug'] = TRUE;

            $this->ftp->connect($ftp_config);

            if ($this->ftp->list_files('participants/' . $data['program_id'] . '/' . $data['account_id'] . '/documents/') == FALSE) {
                $this->ftp->mkdir('participants/' . $data['program_id'] . '/' . $data['account_id'] . '/documents/', DIR_WRITE_MODE);
            }

            $destination = 'participants/' . $data['program_id'] . '/' . $data['account_id'] . '/documents/' . $fileName;

            $this->ftp->upload($source, $destination);

            $this->ftp->close();

            //Delete file from local server
            @unlink($source);

            $sql = $this->mCore->save_data('participants', ['document_url' => config_item('dir_upload') . 'participants/' . $data['program_id'] . '/' . $data['account_id'] . '/documents/' . $fileName], true, array('id' => $id));

            if ($sql) {
                $this->response([
                    'status' => true,
                    'message' => 'Document saved successfully'
                ], 200);
            } else {
                $this->response([
                    'status' => false,
                    'message' => 'Sorry, failed to update'
                ], 404);
            }
        } else {
            $this->response([
                'status' => false,
                'message' => $this->upload->display_errors()
            ], 404);
        }
    }
}
