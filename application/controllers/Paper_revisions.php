<?php

defined('BASEPATH') or exit('No direct script access allowed');

use chriskacerguis\RestServer\RestController;

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST');
header("Access-Control-Allow-Headers: X-Requested-With");

class Paper_revisions extends RestController
{

    function __construct()
    {
        parent::__construct();
    }

    function index_get()
    {
        $id = $this->get('id');
        if ($id == '') {
            $paper_revisions = $this->mCore->get_data('paper_revisions', ['is_active' => 1])->result_array();
            if ($paper_revisions) {
                $this->response([
                    'status' => true,
                    'data' => $paper_revisions
                ], 200);
            } else {
                $this->response([
                    'status' => false,
                    'message' => 'No result were found'
                ], 404);
            }
        } else {
            $paper_revisions = $this->mCore->get_data('paper_revisions', ['id' => $id, 'is_active' => 1])->row_array();
            if ($paper_revisions) {
                $this->response([
                    'status' => true,
                    'data' => $paper_revisions
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
        $paper_detail_id = $this->get('paper_detail_id');
        $paper_revisions = $this->mCore->get_data('paper_revisions', ['paper_detail_id' => $paper_detail_id, 'is_active' => 1])->result_array();
        if ($paper_revisions) {
            $this->response([
                'status' => true,
                'data' => $paper_revisions
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
        $paper_detail_id = $this->post('paper_detail_id');
        $check_paper_revisions = $this->mCore->get_data('paper_revisions', ['paper_detail_id' => $paper_detail_id]);
        // exists or not
        if ($check_paper_revisions->num_rows() > 0) {
            // update
            $data = array(
                'comment' => $this->post('comment'),
                'updated_at' => date('Y-m-d H:i:s'),
            );
            $sql = $this->mCore->save_data('paper_revisions', array_filter($data), true, ['id' => $check_paper_revisions->row_array()['id']]);
            $last_data = $this->mCore->get_data('paper_revisions', ['id' => $check_paper_revisions->row_array()['id']])->row_array();
        } else {
            // insert
            $data = array(
                'comment' => $this->post('comment'),
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            );

            $sql = $this->mCore->save_data('paper_revisions', array_filter($data));
            $last_id = $this->mCore->get_lastid('paper_revisions', 'id');
            $last_data = $this->mCore->get_data('paper_revisions', ['id' => $last_id])->row_array();
        }
        if ($sql) {
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

    //DELETE DATA
    function delete_get()
    {
        $id = $this->get('id');
        $data = array(
            'is_active' => 0,
            'is_deleted' => 1
            // 'updated_at' => date('Y-m-d H:i:s')
        );
        $sql = $this->mCore->save_data('paper_revisions', $data, true, ['id' => $id]);
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