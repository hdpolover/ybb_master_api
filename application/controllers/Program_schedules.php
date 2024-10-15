<?php

defined('BASEPATH') or exit('No direct script access allowed');

use chriskacerguis\RestServer\RestController;

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST');
header("Access-Control-Allow-Headers: X-Requested-With");

class Program_schedules extends RestController
{

    function __construct()
    {
        parent::__construct();
    }

    function index_get()
    {
        $program_id = $this->get('program_id');
        if ($program_id == '') {
            $program_schedules = $this->mCore->get_data('program_schedules', ['is_active' => 1])->result_array();
            if ($program_schedules) {
                $this->response([
                    'status' => true,
                    'data' => $program_schedules
                ], 200);
            } else {
                $this->response([
                    'status' => false,
                    'message' => 'No result were found'
                ], 404);
            }
        } else {
            $program_schedules = $this->mCore->get_data('program_schedules', ['program_id' => $program_id, 'is_active' => 1])->result_array();
            if ($program_schedules) {
                $this->response([
                    'status' => true,
                    'data' => $program_schedules
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
            'program_id' => $this->post('program_id'),
            'name' => $this->post('name'),
            'description' => $this->post('description'),
            'start_date' => $this->post('start_date'),
            'end_date' => $this->post('end_date'),
            'order_number' => $this->post('order_number'),
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        );
        $sql = $this->mCore->save_data('program_schedules', array_filter($data));
        if ($sql) {
            $last_id = $this->mCore->get_lastid('program_schedules', 'id');
            $last_data = $this->mCore->get_data('program_schedules', ['id' => $last_id])->row_array();
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
            'description' => $this->post('description'),
            'start_date' => $this->post('start_date'),
            'end_date' => $this->post('end_date'),
            'order_number' => $this->post('order_number'),
            'updated_at' => date('Y-m-d H:i:s'),
        );
        $sql = $this->mCore->save_data('program_schedules', array_filter($data), true, ['id' => $id]);
        if ($sql) {
            $last_data = $this->mCore->get_data('program_schedules', ['id' => $id])->row_array();
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
        $sql = $this->mCore->save_data('program_schedules', $data, true, ['id' => $id]);
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
