<?php

defined('BASEPATH') or exit('No direct script access allowed');

use chriskacerguis\RestServer\RestController;

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, PUT, PATCH, POST, DELETE');
header("Access-Control-Allow-Headers: X-Requested-With");

class Program_payments extends RestController
{

    public function __construct()
    {
        parent::__construct();
    }

    public function index_get()
    {
        $id = $this->get('id');
        if ($id == '') {
            $program_payments = $this->mCore->get_data('program_payments', ['is_active' => 1])->result_array();
            if ($program_payments) {
                $this->response([
                    'status' => true,
                    'data' => $program_payments,
                ], 200);
            } else {
                $this->response([
                    'status' => false,
                    'message' => 'No result were found',
                ], 404);
            }
        } else {
            $program_payments = $this->mCore->get_data('program_payments', ['id' => $id, 'is_active' => 1])->row_array();
            if ($program_payments) {
                $this->response([
                    'status' => true,
                    'data' => $program_payments,
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
        $program_payments = $this->mCore->get_data('program_payments', ['program_id' => $program_id, 'is_active' => 1])->result_array();
        if ($program_payments) {
            $this->response([
                'status' => true,
                'data' => $program_payments,
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
            'name' => $this->post('name'),
            'description' => $this->post('description'),
            'start_date' => $this->post('start_date'),
            'end_date' => $this->post('end_date'),
            'order_number' => $this->post('order_number'),
            'idr_amount' => $this->post('idr_amount'),
            'usd_amount' => $this->post('usd_amount'),
            'category' => $this->post('category'),
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        );
        $sql = $this->mCore->save_data('program_payments', array_filter($data));
        if ($sql) {
            $last_id = $this->mCore->get_lastid('program_payments', 'id');
            $last_data = $this->mCore->get_data('program_payments', ['id' => $last_id])->row_array();
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
            'name' => $this->post('name'),
            'description' => $this->post('description'),
            'start_date' => $this->post('start_date'),
            'end_date' => $this->post('end_date'),
            'order_number' => $this->post('order_number'),
            'idr_amount' => $this->post('idr_amount'),
            'usd_amount' => $this->post('usd_amount'),
            'category' => $this->post('category'),
            'updated_at' => date('Y-m-d H:i:s'),
        );
        $sql = $this->mCore->save_data('program_payments', array_filter($data), true, ['id' => $id]);
        if ($sql) {
            $last_data = $this->mCore->get_data('program_payments', ['id' => $id])->row_array();
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
        $sql = $this->mCore->save_data('program_payments', $data, true, ['id' => $id]);
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
