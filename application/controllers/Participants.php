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
    function save_post()
    {
        $data = array(
            'user_id' => $this->post('user_id'),
            'account_id' => Uuid::uuid5(Uuid::NAMESPACE_URL, $this->post('user_id')),
            'full_name' => $this->post('full_name'),
            'birthdate' => $this->post('birthdate'),
            'nationality' => $this->post('nationality'),
            'gender' => $this->post('gender'),
            'phone_number' => $this->post('phone_number'),
            'country_code' => $this->post('country_code'),
            'picture_url' => NULL,
            'progam_id' => $this->post('progam_id'),
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        );
        $sql = $this->mCore->save_data('participants', $data);
        if ($sql) {
            $last_id = $this->mCore->get_lastid('participants', 'id');
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

    //UPDATE DATA
    function update_put()
    {
        $id = $this->put('id');
        $data = array(
            'full_name' => $this->post('full_name'),
            'birthdate' => $this->post('birthdate'),
            'nationality' => $this->post('nationality'),
            'gender' => $this->post('gender'),
            'phone_number' => $this->post('phone_number'),
            'country_code' => $this->post('country_code'),
            'progam_id' => $this->post('progam_id'),
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

            $this->ftp->delete_file('participants/' . $data['program_id'] . '/'. $data['uid'] . '/' . $temp_img);

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

    // UPLOAD PICTURE
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
?>