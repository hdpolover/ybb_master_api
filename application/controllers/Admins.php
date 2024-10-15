<?php

defined('BASEPATH') or exit('No direct script access allowed');

use chriskacerguis\RestServer\RestController;

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST');
header("Access-Control-Allow-Headers: X-Requested-With");

class Admins extends RestController
{

    public function __construct()
    {
        parent::__construct();
    }

    public function index_get()
    {
        $id = $this->get('id');
        if ($id == '') {
            $data = $this->mCore->get_data('admins', ['is_active' => 1])->result_array();
            if ($data) {
                $this->response([
                    'status' => true,
                    'data' => $data,
                ], 200);
            } else {
                $this->response([
                    'status' => false,
                    'message' => 'No result were found',
                ], 404);
            }
        } else {
            $data = $this->mCore->get_data('admins', ['id' => $id, 'is_active' => 1])->row_array();
            if ($data) {
                $this->response([
                    'status' => true,
                    'data' => $data,
                ], 200);
            } else {
                $this->response([
                    'status' => false,
                    'message' => 'No result were found',
                ], 404);
            }
        }
    }

    //SIMPAN DATA
    public function save_post()
    {
        $data = array(
            'name' => $this->post('name'),
            'email' => $this->post('email'),
            'password' => md5($this->post('password')),
            'program_id' => $this->post('program_id'),
            'role' => $this->post('role'),
            'profile_url' => null,
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        );
        $sql = $this->mCore->save_data('admins', array_filter($data));
        if ($sql) {
            $last_id = $this->mCore->get_lastid('admins', 'id');
            if (!empty($_FILES['profile_url']['name'])) {
                $upload_file = $this->upload_profile('profile_url', $last_id);
                if ($upload_file['status'] == 0) {
                    $this->response([
                        'status' => false,
                        'message' => $upload_file['message'],
                    ], 404);
                }
            }
            $last_data = $this->mCore->get_data('admins', ['id' => $last_id])->row_array();
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

    //UPDATE DATA
    public function update_post($id)
    {
        if ($id == '') {
            $this->response([
                'status' => false,
                'message' => "Sorry, id doesn't exists",
            ], 404);
        }
        $data = array(
            'name' => $this->post('name'),
            'email' => $this->post('email'),
            // 'password' => $this->post('password'),
            'program_id' => $this->post('program_id'),
            // 'role' => $this->post('role'),
            'is_active' => $this->post('is_active'),
            'updated_at' => date('Y-m-d H:i:s'),
        );
        $sql = $this->mCore->save_data('admins', array_filter($data), true, ['id' => $id]);
        if ($sql) {
            if (!empty($_FILES['profile_url']['name'])) {
                $this->upload_profile('profile_url', $id);
            }
            $last_data = $this->mCore->get_data('admins', ['id' => $id])->row_array();
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
            'password' => $this->put('password'),
            'updated_at' => date('Y-m-d H:i:s'),
        );
        $sql = $this->mCore->save_data('admins', $data, true, ['id' => $id]);
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
        $sql = $this->mCore->save_data('admins', $data, true, ['id' => $id]);
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

    public function program_list_get()
    {
        $program_id = $this->get('id');

        $admins = $this->mCore->get_data('admins', ['program_id' => $program_id])->result_array();
        if ($admins) {
            $this->response($admins, 200);
        } else {
            $this->response([
                'status' => false,
                'message' => 'No result were found',
            ], 404);
        }
    }

    // LOGIN
    public function login_post()
    {
        $id_login = $this->mCore->do_login($this->post('email'), $this->post('password'), $this->post('program_id'));
        if ($id_login) {
            $sql = $this->mCore->get_data('admins', ['id' => $id_login])->row_array();
            $this->response($sql, 200);
        } else {
            $this->response([
                'status' => false,
                'message' => 'Email/Password are Incorrect!',
            ], 404);
        }
    }

    // UPLOAD PROFILE
    private function upload_profile($profile_url, $id)
    {
        $this->load->library('ftp');

        $data = $this->mCore->get_data('admins', 'id = ' . $id)->row_array();
        if ($data['profile_url'] != '') {
            $exp = (explode('/', $data['profile_url']));
            $temp_img = end($exp);

            //FTP configuration
            $ftp_config['hostname'] = config_item('hostname_upload');
            $ftp_config['username'] = config_item('username_upload');
            $ftp_config['password'] = config_item('password_upload');
            $ftp_config['port'] = config_item('port_upload');
            $ftp_config['debug'] = true;

            $this->ftp->connect($ftp_config);

            $this->ftp->delete_file('admins/' . $id . '/' . $temp_img);

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
        if ($this->upload->do_upload($profile_url)) {

            $upload_data = $this->upload->data();
            $fileName = $upload_data['file_name'];

            $source = './uploads/' . $fileName;

            //FTP configuration
            $ftp_config['hostname'] = config_item('hostname_upload');
            $ftp_config['username'] = config_item('username_upload');
            $ftp_config['password'] = config_item('password_upload');
            $ftp_config['port'] = config_item('port_upload');
            $ftp_config['debug'] = true;

            $this->ftp->connect($ftp_config);

            $destination = 'admins/' . $fileName;

            $this->ftp->upload($source, $destination);

            $this->ftp->close();

            //Delete file from local server
            @unlink($source);

            $sql = $this->mCore->save_data('admins', ['profile_url' => config_item('dir_upload') . 'admins/' . $fileName], true, array('id' => $id));

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
            $ftp_config['debug'] = true;

            $this->ftp->connect($ftp_config);

            $this->ftp->delete_file('admins/' . $id . '/' . $temp_img);

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
            $ftp_config['debug'] = true;

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
                    'message' => 'Profile saved successfully',
                ], 200);
            } else {
                $this->response([
                    'status' => false,
                    'message' => 'Sorry, failed to update',
                ], 404);
            }
        } else {
            $this->response([
                'status' => false,
                'message' => $this->upload->display_errors(),
            ], 404);
        }
    }
}
