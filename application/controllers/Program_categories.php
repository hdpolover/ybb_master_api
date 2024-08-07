<?php

defined('BASEPATH') or exit('No direct script access allowed');

use chriskacerguis\RestServer\RestController;

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, PUT, PATCH, POST, DELETE');
header("Access-Control-Allow-Headers: X-Requested-With");

class Program_categories extends RestController
{

    public function __construct()
    {
        parent::__construct();
    }

    public function index_get()
    {
        $id = $this->get('id');
        if ($id == '') {
            $program_categories = $this->mCore->get_data('program_categories', ['is_active' => 1])->result_array();
            if ($program_categories) {
                $this->response([
                    'status' => true,
                    'data' => $program_categories,
                ], 200);
            } else {
                $this->response([
                    'status' => false,
                    'message' => 'No result were found',
                ], 404);
            }
        } else {
            $program_categories = $this->mCore->get_data('program_categories', ['id' => $id, 'is_active' => 1])->row_array();
            if ($program_categories) {
                $this->response([
                    'status' => true,
                    'data' => $program_categories,
                ], 200);
            } else {
                $this->response([
                    'status' => false,
                    'message' => 'No result were found',
                ], 404);
            }
        }
    }

    public function web_get()
    {
        $url = $this->get('url');

        $program_categories = $this->mCore->get_data('program_categories', ['is_active' => 1])->result_array();
        $cat = array_column($program_categories, 'web_url');

        $is_true = false;
        foreach ($cat as $val) {
            if (strpos($url, $val) !== false) {
                $is_true = true;
                $url_new = $val;
            }
        }

        if ($is_true) {
            $join = [
                'select' => '*',
                'table' => 'program_categories',
                'join' => ['programs' => 'programs.program_category_id = program_categories.id AND programs.is_active = 1'],
                'like' => ['program_categories.web_url' => $url_new],
                'where' => ['programs.is_active' => 1],
                'limit' => 1
            ];
            $web_url = $this->mCore->join_table($join)->row_array();
            if ($web_url) {
                $this->response([
                    'status' => true,
                    'data' => $web_url,
                ], 200);
            } else {
                $this->response([
                    'status' => false,
                    'message' => 'No result were found',
                ], 404);
            }
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
            'name' => $this->post('name'),
            'description' => $this->post('description'),
            'web_url' => $this->post('web_url'),
            'tagline' => $this->post('tagline'),
            'contact' => $this->post('contact'),
            'location' => $this->post('location'),
            'email' => $this->post('email'),
            'instagram' => $this->post('instagram'),
            'titktok' => $this->post('titktok'),
            'youtube' => $this->post('youtube'),
            'telegram' => $this->post('telegram'),
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        );
        $sql = $this->mCore->save_data('program_categories', array_filter($data));
        if ($sql) {
            $last_id = $this->mCore->get_lastid('program_categories', 'id');
            $last_data = $this->mCore->get_data('program_categories', ['id' => $last_id])->row_array();
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
            'web_url' => $this->post('web_url'),
            'tagline' => $this->post('tagline'),
            'contact' => $this->post('contact'),
            'location' => $this->post('location'),
            'email' => $this->post('email'),
            'instagram' => $this->post('instagram'),
            'titktok' => $this->post('titktok'),
            'youtube' => $this->post('youtube'),
            'telegram' => $this->post('telegram'),
            'updated_at' => date('Y-m-d H:i:s'),
        );
        $sql = $this->mCore->save_data('program_categories', array_filter($data), true, ['id' => $id]);
        if ($sql) {
            $last_data = $this->mCore->get_data('program_categories', ['id' => $id])->row_array();
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
        $sql = $this->mCore->save_data('program_categories', $data, true, ['id' => $id]);
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
