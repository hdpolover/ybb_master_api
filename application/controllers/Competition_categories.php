<?php

defined('BASEPATH') or exit('No direct script access allowed');

use chriskacerguis\RestServer\RestController;

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, PUT, PATCH, POST, DELETE');
header("Access-Control-Allow-Headers: X-Requested-With");

class Competition_categories extends RestController
{

    function __construct()
    {
        parent::__construct();
    }

    function index_get()
    {
        $id = $this->get('id');
        if ($id == '') {
            $competition_categories = $this->mCore->list_data('competition_categories')->result_array();
            if ($competition_categories) {
                $this->response([
                    'status' => true,
                    'data' => $competition_categories
                ], 200);
            } else {
                $this->response([
                    'status' => false,
                    'message' => 'No result were found'
                ], 404);
            }
        } else {
            $competition_categories = $this->mCore->get_data('competition_categories', ['id' => $id])->row_array();
            if ($competition_categories) {
                $this->response([
                    'status' => true,
                    'data' => $competition_categories
                ], 200);
            } else {
                $this->response([
                    'status' => false,
                    'message' => 'No result were found'
                ], 404);
            }
        }
    }

    //LIST CATEGORY_ID
    function list_get()
    {
        $program_category_id = $this->get('program_category_id');
        $competition_categories = $this->mCore->get_data('competition_categories', ['program_category_id' => $program_category_id])->result_array();
        if ($competition_categories) {
            $this->response([
                'status' => true,
                'data' => $competition_categories
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
            'program_category_id' => $this->post('program_category_id'),
            'category' => $this->post('category'),
            'desc' => $this->post('desc'),
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        );
        $sql = $this->mCore->save_data('competition_categories', array_filter($data));
        if ($sql) {
            $last_id = $this->mCore->get_lastid('competition_categories', 'id');
            $last_data = $this->mCore->get_data('competition_categories', ['id' => $last_id])->row_array();
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
            'category' => $this->post('category'),
            'desc' => $this->post('desc'),
            'updated_at' => date('Y-m-d H:i:s'),
        );
        $sql = $this->mCore->save_data('competition_categories', array_filter($data), true, ['id' => $id]);
        if ($sql) {
            $last_data = $this->mCore->get_data('competition_categories', ['id' => $id])->row_array();
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
        $sql = $this->mCore->save_data('competition_categories', $data, true, ['id' => $id]);
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