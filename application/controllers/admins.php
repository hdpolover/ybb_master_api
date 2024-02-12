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
            $admins = $this->mCore->list_data('admins')->result_array();
            if ($admins) {
                $this->response($admins, 200);
            } else {
                $this->response([
                    'status' => false,
                    'message' => 'No result were found'
                ], 404);
            }
        } else {
            $admins = $this->mCore->get_data('admins', ['id' => $id])->result_array();
            if ($admins) {
                $this->response($admins, 200);
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
            'profile_url' => $this->post('profile_url'),
            'created_at' => date('Y-m-d H:i:s'),
        );
        $sql = $this->mCore->save_data('admins', $data);
        if ($sql) {
            $this->response($data, 200);
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
            'profile_url' => $this->post('profile_url'),
            'is_active' => $this->post('is_active'),
            'updated_at' => date('Y-m-d H:i:s')
        );
        $sql = $this->mCore->save_data('admins', $data, true, ['id' => $id]);
        if ($sql) {
            $this->response($data, 200);
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
            $this->response($data, 200);
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
}
?>