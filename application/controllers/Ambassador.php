<?php

defined('BASEPATH') or exit('No direct script access allowed');

use chriskacerguis\RestServer\RestController;

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, PUT, PATCH, POST, DELETE');
header("Access-Control-Allow-Headers: X-Requested-With");

class Ambassador extends RestController
{

    function __construct()
    {
        parent::__construct();
    }

    function index_get()
    {
        $id = $this->get('id');
        if ($id == '') {
            $data = $this->mCore->list_data('ambassador')->result_array();
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
            $data = $this->mCore->get_data('ambassador', ['id' => $id])->result_array();
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
            'program_id' => $this->post('program_id'),
            'institution' => $this->post('institution'),
            'gender' => $this->post('gender'),
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        );
        $sql = $this->mCore->save_data('ambassador', $data);
        if ($sql) {
            $last_id = $this->mCore->get_lastid('ambassador', 'id');
            $this->mCore->save_data('ambassador', ['ref_code' => strtoupper(substr(str_replace(' ', '',$this->post('name')), 0, 4)) . str_pad($last_id, 3, '0', STR_PAD_LEFT)], true, ['id' => $last_id]);
            
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
            'program_id' => $this->post('program_id'),
            'instituion' => $this->post('instituion'),
            'gender' => $this->post('gender'),
            'updated_at' => date('Y-m-d H:i:s'),
        );
        $sql = $this->mCore->save_data('ambassador', $data, true, ['id' => $id]);
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

    //DELETE DATA
    function delete_get()
    {
        $id = $this->get('id');
        $data = array(
            'is_active' => 0,
            'is_deleted' => 1
            // 'updated_at' => date('Y-m-d H:i:s')
        );
        $sql = $this->mCore->save_data('ambassador', $data, true, ['id' => $id]);
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
}
?>