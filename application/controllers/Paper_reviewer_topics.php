<?php

defined('BASEPATH') or exit('No direct script access allowed');

use chriskacerguis\RestServer\RestController;

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST');
header("Access-Control-Allow-Headers: X-Requested-With");

class Paper_reviewer_topics extends RestController
{

    function __construct()
    {
        parent::__construct();
    }

    function index_get()
    {
        $id = $this->get('id');
        if ($id == '') {
            $paper_reviewers = $this->mCore->get_data('paper_reviewer_topics', ['is_active' => 1])->result_array();
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
            $paper_reviewers = $this->mCore->get_data('paper_reviewer_topics', ['id' => $id, 'is_active' => 1])->row_array();
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
    
    function list_get()
    {
        $id = $this->get('paper_reviewer_id');
        $paper_reviewers = $this->mCore->get_data('paper_reviewer_topics', ['paper_reviewer_id' => $id, 'is_active' => 1])->result_array();
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

    //SIMPAN DATA
    function save_post()
    {
        $data = array(
            'paper_reviewer_id' => $this->post('paper_reviewer_id'),
            'paper_topic_id' => $this->post('paper_topic_id'),
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        );
        $sql = $this->mCore->save_data('paper_reviewer_topics', array_filter($data));
        if ($sql) {
            $last_id = $this->mCore->get_lastid('paper_reviewer_topics', 'id');
            $last_data = $this->mCore->get_data('paper_reviewer_topics', ['id' => $last_id])->row_array();
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
             'paper_reviewer_id' => $this->post('program_id'),
            'paper_topic_id' => $this->post('paper_topic_id'),
            'updated_at' => date('Y-m-d H:i:s'),
        );
        $sql = $this->mCore->save_data('paper_reviewer_topics', array_filter($data), true, ['id' => $id]);
        if ($sql) {
            $last_data = $this->mCore->get_data('paper_reviewer_topics', ['id' => $id])->row_array();
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
        $sql = $this->mCore->save_data('paper_reviewer_topics', $data, true, ['id' => $id]);
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
