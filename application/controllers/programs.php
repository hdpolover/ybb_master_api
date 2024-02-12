<?php

defined('BASEPATH') or exit('No direct script access allowed');

use chriskacerguis\RestServer\RestController;

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, PUT, PATCH, POST, DELETE');
header("Access-Control-Allow-Headers: X-Requested-With");

class Programs extends RestController
{

    function __construct()
    {
        parent::__construct();
    }

    function index_get()
    {
        $id = $this->get('id');
        if ($id == '') {
            $programs = $this->mCore->list_data('programs')->result_array();
            if ($programs) {
                $this->response($programs, 200);
            } else {
                $this->response([
                    'status' => false,
                    'message' => 'No result were found'
                ], 404);
            }
        } else {
            $programs = $this->mCore->get_data('programs', ['id' => $id])->result_array();
            if ($programs) {
                $this->response($programs, 200);
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
            'program_category_id' => $this->post('program_category_id'),
            'name' => $this->post('name'),
            'logo_url' => $this->post('logo_url'),
            'description' => $this->post('description'),
            'guideline' => $this->post('guideline'),
            'twibbon' => $this->post('twibbon'),
            'start_date' => $this->post('start_date'),
            'end_date' => $this->post('end_date'),
            'registration_video_url' => $this->post('registration_video_url'),
            'sponsor_canva_url' => $this->post('sponsor_canva_url'),
            'created_at' => date('Y-m-d H:i:s'),
        );
        $sql = $this->mCore->save_data('programs', $data);
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
            'program_category_id' => $this->post('program_category_id'),
            'name' => $this->post('name'),
            'logo_url' => $this->post('logo_url'),
            'description' => $this->post('description'),
            'guideline' => $this->post('guideline'),
            'twibbon' => $this->post('twibbon'),
            'start_date' => $this->post('start_date'),
            'end_date' => $this->post('end_date'),
            'registration_video_url' => $this->post('registration_video_url'),
            'sponsor_canva_url' => $this->post('sponsor_canva_url'),
            'updated_at' => date('Y-m-d H:i:s'),
        );
        $sql = $this->mCore->save_data('programs', $data, true, ['id' => $id]);
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
        $sql = $this->mCore->save_data('programs', $data, true, ['id' => $id]);
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