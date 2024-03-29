<?php

defined('BASEPATH') or exit('No direct script access allowed');

use chriskacerguis\RestServer\RestController;

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, PUT, PATCH, POST, DELETE');
header("Access-Control-Allow-Headers: X-Requested-With");

class Web_setting_about extends RestController
{

    function __construct()
    {
        parent::__construct();
    }

    function index_get()
    {
        $id = $this->get('id');
        if ($id == '') {
            $web_setting_about = $this->mCore->list_data('web_setting_about')->result_array();
            if ($web_setting_about) {
                $this->response($web_setting_about, 200);
            } else {
                $this->response([
                    'status' => false,
                    'message' => 'No result were found'
                ], 404);
            }
        } else {
            $web_setting_about = $this->mCore->get_data('web_setting_about', ['id' => $id])->result_array();
            if ($web_setting_about) {
                $this->response($web_setting_about, 200);
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
            'page_name' => $this->post('page_name'),
            'menu_path' => $this->post('menu_path'),
            'about_ybb' => $this->post('about_ybb'),
            'about_program' => $this->post('about_program'),
            'why_program' => $this->post('why_program'),
            'what_program' => $this->post('what_program'),
            'ybb_video_url' => $this->post('ybb_video_url'),
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        );
        $sql = $this->mCore->save_data('web_setting_about', $data);
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
            'page_name' => $this->post('page_name'),
            'menu_path' => $this->post('menu_path'),
            'about_ybb' => $this->post('about_ybb'),
            'about_program' => $this->post('about_program'),
            'why_program' => $this->post('why_program'),
            'what_program' => $this->post('what_program'),
            'ybb_video_url' => $this->post('ybb_video_url'),
            'updated_at' => date('Y-m-d H:i:s'),
        );
        $sql = $this->mCore->save_data('web_setting_about', $data, true, ['id' => $id]);
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
        $sql = $this->mCore->save_data('web_setting_about', $data, true, ['id' => $id]);
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