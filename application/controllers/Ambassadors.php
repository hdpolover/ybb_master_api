<?php

defined('BASEPATH') or exit('No direct script access allowed');

use chriskacerguis\RestServer\RestController;

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST');
header("Access-Control-Allow-Headers: X-Requested-With");

class Ambassadors extends RestController
{

    function __construct()
    {
        parent::__construct();
    }

    function index_get()
    {
        $id = $this->get('id');
        if ($id == '') {
            $data = $this->mCore->get_data('ambassadors', ['is_active' => 1])->result_array();
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
            $data = $this->mCore->get_data('ambassadors', ['id' => $id, 'is_active' => 1])->row_array();
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

    function ambassador_program_get()
    {
        $program_id = $this->get('program_id');
        $data = $this->mCore->get_data('ambassadors', ['program_id' => $program_id, 'is_active' => 1])->result_array();
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

    function participant_list_get()
    {
        $code = $this->get('code');
        $data = $this->mCore->get_data('ambassadors', ['ref_code' => $code])->result_array();
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

    //SIMPAN DATA
    function save_post()
    {
        $data = array(
            'name' => $this->post('name'),
            'email' => $this->post('email'),
            'program_id' => $this->post('program_id'),
            'institution' => $this->post('institution'),
            'gender' => $this->post('gender'),
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        );
        $sql = $this->mCore->save_data('ambassadors', array_filter($data));
        if ($sql) {
            $last_id = $this->mCore->get_lastid('ambassadors', 'id');
            $this->mCore->save_data('ambassadors', ['ref_code' => strtoupper(substr(str_replace(' ', '', $this->post('name')), 0, 4)) . str_pad($last_id, 3, '0', STR_PAD_LEFT)], true, ['id' => $last_id]);

            $last_data = $this->mCore->get_data('ambassadors', ['id' => $last_id])->row_array();
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
    function update_post($id)
    {
        $data = array(
            'name' => $this->post('name'),
            'email' => $this->post('email'),
            'program_id' => $this->post('program_id'),
            'institution' => $this->post('institution'),
            'gender' => $this->post('gender'),
            'updated_at' => date('Y-m-d H:i:s'),
        );
        $sql = $this->mCore->save_data('ambassadors', array_filter($data), true, ['id' => $id]);
        if ($sql) {
            $last_data = $this->mCore->get_data('ambassadors', ['id' => $id])->row_array();
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
        $sql = $this->mCore->save_data('ambassadors', $data, true, ['id' => $id]);
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

    function login_post()
    {
        $email = $this->post('email');
        $ref_code = $this->post('ref_code');

        $check_data = $this->mCore->get_data('ambassadors', ['email' => $email, 'ref_code' => $ref_code, 'is_active' => 1]);
        if ($check_data->num_rows() > 0) {
            $this->response([
                'status' => true,
                'data' => $check_data->row()
            ], 200);
        } else {
            $this->response([
                'status' => false,
                'message' => 'Login failed'
            ], 404);
        }
    }
}
