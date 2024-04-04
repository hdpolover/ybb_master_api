<?php

defined('BASEPATH') or exit('No direct script access allowed');

use chriskacerguis\RestServer\RestController;

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, PUT, PATCH, POST, DELETE');
header("Access-Control-Allow-Headers: X-Requested-With");

class Program_payments extends RestController
{

    function __construct()
    {
        parent::__construct();
    }

    function index_get()
    {
        $id = $this->get('id');
        if ($id == '') {
            $program_payments = $this->mCore->list_data('program_payments')->result_array();
            if ($program_payments) {
                $this->response([
                    'status' => true,
                    'data' => $program_payments
                ], 200);
            } else {
                $this->response([
                    'status' => false,
                    'message' => 'No result were found'
                ], 404);
            }
        } else {
            $program_payments = $this->mCore->get_data('program_payments', ['id' => $id])->row_array();
            if ($program_payments) {
                $this->response([
                    'status' => true,
                    'data' => $program_payments
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
            'idr_amount' => $this->post('idr_amount'),
            'usd_amount' => $this->post('usd_amount'),
            'category' => $this->post('category'),
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        );
        $sql = $this->mCore->save_data('program_payments', $data);
        if ($sql) {
            $last_id = $this->mCore->get_lastid('program_payments', 'id');
            $last_data = $this->mCore->get_data('program_payments', ['id' => $last_id])->row_array();
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
            'name' => $this->put('name'),
            'description' => $this->put('description'),
            'start_date' => $this->put('start_date'),
            'end_date' => $this->put('end_date'),
            'order_number' => $this->put('order_number'),
            'idr_amount' => $this->put('idr_amount'),
            'usd_amount' => $this->put('usd_amount'),
            'category' => $this->put('category'),
            'updated_at' => date('Y-m-d H:i:s'),
        );
        $sql = $this->mCore->save_data('program_payments', $data, true, ['id' => $id]);
        if ($sql) {
            $last_data = $this->mCore->get_data('program_payments', ['id' => $id])->row_array();
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
        $sql = $this->mCore->save_data('program_payments', $data, true, ['id' => $id]);
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