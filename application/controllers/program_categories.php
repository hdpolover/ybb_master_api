<?php

defined('BASEPATH') or exit('No direct script access allowed');

use chriskacerguis\RestServer\RestController;

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, PUT, PATCH, POST, DELETE');
header("Access-Control-Allow-Headers: X-Requested-With");

class Program_categories extends RestController
{

    function __construct()
    {
        parent::__construct();
    }

    function index_get()
    {
        $id = $this->get('id');
        if ($id == '') {
            $program_categories = $this->mCore->list_data('program_categories')->result_array();
            if ($program_categories) {
                $this->response($program_categories, 200);
            } else {
                $this->response([
                    'status' => false,
                    'message' => 'No result were found'
                ], 404);
            }
        } else {
            $program_categories = $this->mCore->get_data('program_categories', ['id' => $id])->result_array();
            if ($program_categories) {
                $this->response($program_categories, 200);
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
            'description' => $this->post('description'),
            'web_url' => $this->post('web_url'),
            'contact' => $this->post('contact'),
            'location' => $this->post('location'),
            'email' => $this->post('email'),
            'instagram' => $this->post('instagram'),
            'titktok' => $this->post('titktok'),
            'youtube' => $this->post('youtube'),
            'telegram' => $this->post('telegram'),
            'created_at' => date('Y-m-d H:i:s'),
        );
        $sql = $this->mCore->save_data('program_categories', $data);
        if ($sql) {
            $this->response($data, 200);
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
            'description' => $this->post('description'),
            'web_url' => $this->post('web_url'),
            'contact' => $this->post('contact'),
            'location' => $this->post('location'),
            'email' => $this->post('email'),
            'instagram' => $this->post('instagram'),
            'titktok' => $this->post('titktok'),
            'youtube' => $this->post('youtube'),
            'telegram' => $this->post('telegram'),
            'updated_at' => date('Y-m-d H:i:s'),
        );
        $sql = $this->mCore->save_data('program_categories', $data, true, ['id' => $id]);
        if ($sql) {
            $this->response($data, 200);
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
        $sql = $this->mCore->save_data('program_categories', $data, true, ['id' => $id]);
        if ($sql) {
            $this->response($data, 200);
        } else {
            $this->response([
                'status' => false,
                'message' => 'Sorry, failed to delete'
            ], 404);
        }
    }
}
?>