<?php

defined('BASEPATH') or exit('No direct script access allowed');

use chriskacerguis\RestServer\RestController;

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, PUT, PATCH, POST, DELETE');
header("Access-Control-Allow-Headers: X-Requested-With");

class Program_essays extends RestController
{

    public function __construct()
    {
        parent::__construct();
    }

    public function index_get()
    {
        $id = $this->get('id');
        if ($id == '') {
            $program_essays = $this->mCore->list_data('program_essays')->result_array();
            if ($program_essays) {
                $this->response([
                    'status' => true,
                    'data' => $program_essays,
                ], 200);
            } else {
                $this->response([
                    'status' => false,
                    'message' => 'No result were found',
                ], 404);
            }
        } else {
            $program_essays = $this->mCore->get_data('program_essays', ['id' => $id])->row_array();
            if ($program_essays) {
                $this->response([
                    'status' => true,
                    'data' => $program_essays,
                ], 200);
            } else {
                $this->response([
                    'status' => false,
                    'message' => 'No result were found',
                ], 404);
            }
        }
    }

    //LIST PROGRAM
    public function list_get()
    {
        $program_id = $this->get('program_id');
        $program_essays = $this->mCore->get_data('program_essays', ['program_id' => $program_id])->result_array();
        if ($program_essays) {
            $this->response([
                'status' => true,
                'data' => $program_essays,
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
        $data = array(
            'program_id' => $this->post('program_id'),
            'questions' => $this->post('questions'),
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        );
        $sql = $this->mCore->save_data('program_essays', array_filter($data));
        if ($sql) {
            $last_id = $this->mCore->get_lastid('program_essays', 'id');
            $last_data = $this->mCore->get_data('program_essays', ['id' => $last_id])->row_array();
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
        $data = array(
            'questions' => $this->post('questions'),
            'updated_at' => date('Y-m-d H:i:s'),
        );
        $sql = $this->mCore->save_data('program_essays', array_filter($data), true, ['id' => $id]);
        if ($sql) {
            $last_data = $this->mCore->get_data('program_essays', ['id' => $id])->row_array();
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

    //DELETE DATA
    public function delete_get()
    {
        $id = $this->get('id');
        $data = array(
            'is_active' => 0,
            'is_deleted' => 1,
            // 'updated_at' => date('Y-m-d H:i:s')
        );
        $sql = $this->mCore->save_data('program_essays', $data, true, ['id' => $id]);
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
}
