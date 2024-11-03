<?php

defined('BASEPATH') or exit('No direct script access allowed');

use chriskacerguis\RestServer\RestController;

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST');
header("Access-Control-Allow-Headers: X-Requested-With");

class Paper_details extends RestController
{

    function __construct()
    {
        parent::__construct();
    }

    function index_get()
    {
        $id = $this->get('id');
        if ($id == '') {
            $paper_details = $this->mCore->get_data('paper_details', ['is_active' => 1])->result_array();
            if ($paper_details) {
                $this->response([
                    'status' => true,
                    'data' => $paper_details
                ], 200);
            } else {
                $this->response([
                    'status' => false,
                    'message' => 'No result were found'
                ], 404);
            }
        } else {
            $paper_details = $this->mCore->get_data('paper_details', ['id' => $id, 'is_active' => 1])->row_array();
            if ($paper_details) {
                $this->response([
                    'status' => true,
                    'data' => $paper_details
                ], 200);
            } else {
                $this->response([
                    'status' => false,
                    'message' => 'No result were found'
                ], 404);
            }
        }
    }

    function list_program_get()
    {
        $id = $this->get('id');
        $paper_details = $this->mCore->get_data('paper_details', ['program_id' => $id, 'is_active' => 1])->result_array();
        if ($paper_details) {
            $this->response([
                'status' => true,
                'data' => $paper_details
            ], 200);
        } else {
            $this->response([
                'status' => false,
                'message' => 'No result were found'
            ], 404);
        }
    }

    function list_paper_abstract_get()
    {
        $id = $this->get('id');
        $paper_details = $this->mCore->get_data('paper_details', ['paper_abstract_id' => $id, 'is_active' => 1])->result_array();
        if ($paper_details) {
            $this->response([
                'status' => true,
                'data' => $paper_details
            ], 200);
        } else {
            $this->response([
                'status' => false,
                'message' => 'No result were found'
            ], 404);
        }
    }

    function list_paper_get()
    {
        $id = $this->get('id');
        $paper_details = $this->mCore->get_data('paper_details', ['paper_id' => $id, 'is_active' => 1])->result_array();
        if ($paper_details) {
            $this->response([
                'status' => true,
                'data' => $paper_details
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
            'program_id' => $this->post('program_id'),
            'paper_abstract_id' => $this->post('paper_abstract_id'),
            'paper_id' => $this->post('paper_id'),
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        );
        $sql = $this->mCore->save_data('paper_details', array_filter($data));
        if ($sql) {
            $last_id = $this->mCore->get_lastid('paper_details', 'id');
            $last_data = $this->mCore->get_data('paper_details', ['id' => $last_id])->row_array();
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
            'program_id' => $this->post('program_id'),
            'paper_abstract_id' => $this->post('paper_abstract_id'),
            'paper_id' => $this->post('paper_id'),
            'updated_at' => date('Y-m-d H:i:s'),
        );
        $sql = $this->mCore->save_data('paper_details', array_filter($data), true, ['id' => $id]);
        if ($sql) {
            $last_data = $this->mCore->get_data('paper_details', ['id' => $id])->row_array();
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
        $sql = $this->mCore->save_data('paper_details', $data, true, ['id' => $id]);
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
