<?php

defined('BASEPATH') or exit('No direct script access allowed');

use chriskacerguis\RestServer\RestController;

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST');
header("Access-Control-Allow-Headers: X-Requested-With");

class Program_document_settings extends RestController
{

    function __construct()
    {
        parent::__construct();
    }

    function index_get()
    {
        $id = $this->get('id');
        if ($id == '') {
            $program_document_settings = $this->mCore->get_data('program_document_settings', ['is_active' => 1])->result_array();
            if ($program_document_settings) {
                $this->response([
                    'status' => true,
                    'data' => $program_document_settings
                ], 200);
            } else {
                $this->response([
                    'status' => false,
                    'message' => 'No result were found'
                ], 404);
            }
        } else {
            $program_document_settings = $this->mCore->get_data('program_document_settings', ['id' => $id, 'is_active' => 1])->row();
            if ($program_document_settings) {
                $this->response([
                    'status' => true,
                    'data' => $program_document_settings
                ], 200);
            } else {
                $this->response([
                    'status' => false,
                    'message' => 'No result were found'
                ], 404);
            }
        }
    }

    function list_get()
    {
        $program_id = $this->get('program_id');

        $program_document_settings = $this->mCore->get_data('program_document_settings', ['program_id' => $program_id, 'is_active' => 1])->result_array();
        if ($program_document_settings) {
            $this->response([
                'status' => true,
                'data' => $program_document_settings
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
            'name' => $this->post('name'),
            'start_date' => $this->post('start_date'),
            'end_date' => $this->post('end_date'),
            'availability_date' => $this->post('availability_date'),
            'custom_availability' => $this->post('custom_availability'),
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        );
        $sql = $this->mCore->save_data('program_document_settings', array_filter($data));
        if ($sql) {
            $last_id = $this->mCore->get_lastid('program_document_settings', 'id');
            $last_data = $this->mCore->get_data('program_document_settings', ['id' => $last_id])->row_array();
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
            'start_date' => $this->post('start_date'),
            'end_date' => $this->post('end_date'),
            'availability_date' => $this->post('availability_date'),
            'custom_availability' => $this->post('custom_availability'),
            'updated_at' => date('Y-m-d H:i:s'),
        );
        $sql = $this->mCore->save_data('program_document_settings', array_filter($data), true, ['id' => $id]);
        if ($sql) {
            $last_data = $this->mCore->get_data('program_document_settings', ['id' => $id])->row_array();
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
        $sql = $this->mCore->save_data('program_document_settings', $data, true, ['id' => $id]);
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
