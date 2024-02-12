<?php

defined('BASEPATH') or exit('No direct script access allowed');

use chriskacerguis\RestServer\RestController;

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, PUT, PATCH, POST, DELETE');
header("Access-Control-Allow-Headers: X-Requested-With");

class Program_testimonies extends RestController
{

    function __construct()
    {
        parent::__construct();
    }

    function index_get()
    {
        $id = $this->get('id');
        if ($id == '') {
            $program_testimonies = $this->mCore->list_data('program_testimonies')->result_array();
            if ($program_testimonies) {
                $this->response($program_testimonies, 200);
            } else {
                $this->response([
                    'status' => false,
                    'message' => 'No result were found'
                ], 404);
            }
        } else {
            $program_testimonies = $this->mCore->get_data('program_testimonies', ['id' => $id])->result_array();
            if ($program_testimonies) {
                $this->response($program_testimonies, 200);
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
            'person_name' => $this->post('person_name'),
            'testimony' => $this->post('testimony'),
            'occupation' => $this->post('occupation'),
            'img_url' => $this->post('img_url'),
            'created_at' => date('Y-m-d H:i:s'),
        );
        $sql = $this->mCore->save_data('program_testimonies', $data);
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
            'person_name' => $this->post('person_name'),
            'testimony' => $this->post('testimony'),
            'occupation' => $this->post('occupation'),
            'img_url' => $this->post('img_url'),
            'updated_at' => date('Y-m-d H:i:s'),
        );
        $sql = $this->mCore->save_data('program_testimonies', $data, true, ['id' => $id]);
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
        $sql = $this->mCore->save_data('program_testimonies', $data, true, ['id' => $id]);
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