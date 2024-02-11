<?php

defined('BASEPATH') or exit('No direct script access allowed');

use chriskacerguis\RestServer\RestController;

class web_setting_home extends RestController
{

    function __construct()
    {
        parent::__construct();
    }

    function index_get()
    {
        $id = $this->get('id');
        if ($id == '') {
            $web_setting_home = $this->mCore->list_data('web_setting_home')->result_array();
            if ($web_setting_home) {
                $this->response($web_setting_home, 200);
            } else {
                $this->response([
                    'status' => false,
                    'message' => 'No result were found'
                ], 404);
            }
        } else {
            $web_setting_home = $this->mCore->get_data('web_setting_home', ['id' => $id])->result_array();
            if ($web_setting_home) {
                $this->response($web_setting_home, 200);
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
            'banner1_img_url' => $this->post('banner1_img_url'),
            'banner1_title' => $this->post('banner1_title'),
            'banner1_description' => $this->post('banner1_description'),
            'banner1_date' => $this->post('banner1_date'),
            'banner2_img_url' => $this->post('banner2_img_url'),
            'banner2_title' => $this->post('banner2_title'),
            'banner2_description' => $this->post('banner2_description'),
            'banner2_date' => $this->post('banner2_date'),
            'summary' => $this->post('summary'),
            'reason' => $this->post('reason'),
            'agenda' => $this->post('agenda'),
            'introduction' => $this->post('introduction'),
            'created_at' => date('Y-m-d H:i:s'),
        );
        $sql = $this->mCore->save_data('web_setting_home', $data);
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
            'banner1_img_url' => $this->post('banner1_img_url'),
            'banner1_title' => $this->post('banner1_title'),
            'banner1_description' => $this->post('banner1_description'),
            'banner1_date' => $this->post('banner1_date'),
            'banner2_img_url' => $this->post('banner2_img_url'),
            'banner2_title' => $this->post('banner2_title'),
            'banner2_description' => $this->post('banner2_description'),
            'banner2_date' => $this->post('banner2_date'),
            'summary' => $this->post('summary'),
            'reason' => $this->post('reason'),
            'agenda' => $this->post('agenda'),
            'introduction' => $this->post('introduction'),
            'updated_at' => date('Y-m-d H:i:s'),
        );
        $sql = $this->mCore->save_data('web_setting_home', $data, true, ['id' => $id]);
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
        $sql = $this->mCore->save_data('web_setting_home', $data, true, ['id' => $id]);
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