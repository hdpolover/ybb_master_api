<?php

defined('BASEPATH') or exit('No direct script access allowed');

use chriskacerguis\RestServer\RestController;

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, PUT, PATCH, POST, DELETE');
header("Access-Control-Allow-Headers: X-Requested-With");

class Admins extends RestController
{

    function __construct()
    {
        parent::__construct();
    }

    function index_get()
    {
        $id = $this->get('id');
        if ($id == '') {
            $data = $this->mCore->list_data('admins')->result_array();
            if ($data) {
                $this->response([
                    'status' => true,
                    'data' => $data
                ], 200);
            } else {
                $this->response([
                    'status' => false,
                    'message' => 'No result were found'
                ], 404);
            }
        } else {
            $data = $this->mCore->get_data('admins', ['id' => $id])->result_array();
            if ($data) {
                $this->response([
                    'status' => true,
                    'data' => $data
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
            'name' => $this->post('name'),
            'email' => $this->post('email'),
            'password' => md5($this->post('password')),
            'program_id' => $this->post('program_id'),
            'role' => $this->post('role'),
            'profile_url' => NULL,
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        );
        $sql = $this->mCore->save_data('admins', $data);
        if ($sql) {
            $this->response([
                'status' => true,
                'data' => $data
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
            'name' => $this->post('name'),
            'email' => $this->post('email'),
            // 'password' => $this->post('password'),
            'program_id' => $this->post('program_id'),
            'role' => $this->post('role'),
            'is_active' => $this->post('is_active'),
            'updated_at' => date('Y-m-d H:i:s')
        );
        $sql = $this->mCore->save_data('admins', $data, true, ['id' => $id]);
        if ($sql) {
            $this->response([
                'status' => true,
                'data' => $data
            ], 200);
        } else {
            $this->response([
                'status' => false,
                'message' => 'Sorry, failed to update'
            ], 404);
        }
    }

    //UPDATE DATA
    function update_password_put()
    {
        $new_password = $this->put('password');
        $new_password_confirm = $this->put('password_confirm');
        if ($new_password != $new_password_confirm) {
            $this->response([
                'status' => false,
                'message' => 'Sorry, the password is not the same'
            ], 404);
        }

        $id = $this->put('id');
        $data = array(
            'password' => $this->put('password'),
            'updated_at' => date('Y-m-d H:i:s'),
        );
        $sql = $this->mCore->save_data('admins', $data, true, ['id' => $id]);
        if ($sql) {
            $this->response([
                'status' => true,
                'message' => 'Data saved successfully'
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
        $sql = $this->mCore->save_data('admins', $data, true, ['id' => $id]);
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

    function program_list_get()
    {
        $program_id = $this->get('id');

        $admins = $this->mCore->get_data('admins', ['program_id' => $program_id])->result_array();
        if ($admins) {
            $this->response($admins, 200);
        } else {
            $this->response([
                'status' => false,
                'message' => 'No result were found'
            ], 404);
        }
    }

    // LOGIN
    function login_post()
    {
        $id_login = $this->mCore->do_login($this->post('email'), $this->post('password'), $this->post('program_id'));
        if ($id_login) {
            $sql = $this->mCore->get_data('admins', ['id' => $id_login])->row_array();
            $this->response($sql, 200);
        } else {
            $this->response([
                'status' => false,
                'message' => 'Email/Password are Incorrect!'
            ], 404);
        }
    }

    // UPLOAD PROFILE
    public function do_upload_profile_post()
    {
        $this->load->library('ftp');

        $id = $this->post('id');

        $data = $this->mCore->get_data('admins', 'id = ' . $id)->row_array();
        if ($data['profile_url'] != '') {
            $exp = (explode('/', $data['profile_url']));
            $temp_img = end($exp);

            //FTP configuration
            $ftp_config['hostname'] = config_item('hostname_upload');
            $ftp_config['username'] = config_item('username_upload');
            $ftp_config['password'] = config_item('password_upload');
            $ftp_config['port'] = config_item('port_upload');
            $ftp_config['debug'] = TRUE;

            $this->ftp->connect($ftp_config);

            $this->ftp->delete_file('admins/' . $id . '/'. $temp_img);

            $this->ftp->close();
        }
        

        $config['upload_path'] = './uploads';
        $config['allowed_types'] = 'gif|jpg|png|jpeg';
        $config['max_size'] = 5000;
        $config['file_name'] = time();
        // $config['max_width']            = 1024;
        // $config['max_height']           = 768;

        $this->load->library('upload', $config);
        $this->upload->initialize($config);
        if ($this->upload->do_upload("profile")) {

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

            $destination = 'admins/' . $fileName;

            $this->ftp->upload($source, $destination);

            $this->ftp->close();

            //Delete file from local server
            @unlink($source);

            $sql = $this->mCore->save_data('admins', ['profile_url' => config_item('dir_upload') . 'admins/' . $fileName], true, array('id' => $id));
            
            if ($sql) {
                $this->response([
                    'status' => true,
                    'message' => 'Profile saved successfully'
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