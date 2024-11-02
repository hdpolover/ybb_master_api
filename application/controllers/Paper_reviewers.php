<?php

defined('BASEPATH') or exit('No direct script access allowed');

use chriskacerguis\RestServer\RestController;

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST');
header("Access-Control-Allow-Headers: X-Requested-With");

class Paper_reviewers extends RestController
{

    function __construct()
    {
        parent::__construct();
    }

    function index_get()
    {
        $id = $this->get('id');
        if ($id == '') {
            $paper_reviewers = $this->mCore->get_data('paper_reviewers', ['is_active' => 1])->result_array();
            if ($paper_reviewers) {
                $this->response([
                    'status' => true,
                    'data' => $paper_reviewers
                ], 200);
            } else {
                $this->response([
                    'status' => false,
                    'message' => 'No result were found'
                ], 404);
            }
        } else {
            $paper_reviewers = $this->mCore->get_data('paper_reviewers', ['id' => $id, 'is_active' => 1])->row_array();
            if ($paper_reviewers) {
                $this->response([
                    'status' => true,
                    'data' => $paper_reviewers
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
            'name' => $this->post('name'),
            'email' => $this->post('email'),
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        );
        $sql = $this->mCore->save_data('paper_reviewers', array_filter($data));
        if ($sql) {
            $last_id = $this->mCore->get_lastid('paper_reviewers', 'id');
            $last_data = $this->mCore->get_data('paper_reviewers', ['id' => $last_id])->row_array();
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
            'email' => $this->post('email'),
            'updated_at' => date('Y-m-d H:i:s'),
        );
        $sql = $this->mCore->save_data('paper_reviewers', array_filter($data), true, ['id' => $id]);
        if ($sql) {
            if (!empty($_FILES['img_url']['name'])) {
                $upload_file = $this->upload_image('img_url', $id);
                if ($upload_file['status'] == 0) {
                    $this->response([
                        'status' => false,
                        'message' => $upload_file['message'],
                    ], 404);
                }
            }
            $last_data = $this->mCore->get_data('paper_reviewers', ['id' => $id])->row_array();
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
        $sql = $this->mCore->save_data('paper_reviewers', $data, true, ['id' => $id]);
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
