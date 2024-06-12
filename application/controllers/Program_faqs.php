<?php

defined('BASEPATH') or exit('No direct script access allowed');

use chriskacerguis\RestServer\RestController;

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, PUT, PATCH, POST, DELETE');
header("Access-Control-Allow-Headers: X-Requested-With");

class Program_faqs extends RestController
{

    function __construct()
    {
        parent::__construct();
    }

    function index_get()
    {
        $id = $this->get('id');
        if ($id == '') {
            $program_faqs = $this->mCore->get_data('program_faqs', ['is_active' => 1])->result_array();
            if ($program_faqs) {
                $this->response([
                    'status' => true,
                    'data' => $program_faqs
                ], 200);
            } else {
                $this->response([
                    'status' => false,
                    'message' => 'No result were found'
                ], 404);
            }
        } else {
            $program_faqs = $this->mCore->get_data('program_faqs', ['id' => $id, 'is_active' => 1])->row_array();
            if ($program_faqs) {
                $this->response([
                    'status' => true,
                    'data' => $program_faqs
                ], 200);
            } else {
                $this->response([
                    'status' => false,
                    'message' => 'No result were found'
                ], 404);
            }
        }
    }

    //LIST PROGRAM
    function list_program_get()
    {
        $program_id = $this->get('program_id');
        $program_faqs = $this->mCore->get_data('program_faqs', ['program_id' => $program_id, 'is_active' => 1])->result_array();
        if ($program_faqs) {
            $this->response([
                'status' => true,
                'data' => $program_faqs
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
            'question' => $this->post('question'),
            'answer' => $this->post('answer'),
            'faq_category' => $this->post('faq_category'),
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        );
        $sql = $this->mCore->save_data('program_faqs', array_filter($data));
        if ($sql) {
            $last_id = $this->mCore->get_lastid('program_faqs', 'id');
            $last_data = $this->mCore->get_data('program_faqs', ['id' => $last_id])->row_array();
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
            'question' => $this->post('question'),
            'answer' => $this->post('answer'),
            'faq_category' => $this->post('faq_category'),
            'updated_at' => date('Y-m-d H:i:s'),
        );
        $sql = $this->mCore->save_data('program_faqs', array_filter($data), true, ['id' => $id]);
        if ($sql) {
            $last_data = $this->mCore->get_data('program_faqs', ['id' => $id])->row_array();
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
        $sql = $this->mCore->save_data('program_faqs', $data, true, ['id' => $id]);
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
