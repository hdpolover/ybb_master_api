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
    
    function list_get()
    {
        $program_id = $this->get('program_id');
        $paper_reviewers = $this->mCore->get_data('paper_reviewers', ['program_id' => $program_id, 'is_active' => 1])->result_array();
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
    
    function signin_get()
    {
        $email = $this->get('email');
        $password = $this->get('password');
        $paper_reviewers = $this->mCore->get_data('paper_reviewers', ['email' => $email, 'password' => $password, 'is_active' => 1])->result_array();
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
            'paper_topic_id' => $this->post('paper_topic_id'),
            'program_id' => $this->post('program_id'),
            'name' => $this->post('name'),
            'email' => $this->post('email'),
            'institution' => $this->post('institution'),
            'password' => $this->post('password'),
            'topic_access' => $this->post('topic_access'),
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
            'paper_topic_id' => $this->post('paper_topic_id'),
            'program_id' => $this->post('program_id'),
            'name' => $this->post('name'),
            'email' => $this->post('email'),
            'institution' => $this->post('institution'),
            'password' => $this->post('password'),
            'topic_access' => $this->post('topic_access'),
            'updated_at' => date('Y-m-d H:i:s'),
        );
        $sql = $this->mCore->save_data('paper_reviewers', array_filter($data), true, ['id' => $id]);
        if ($sql) {
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
