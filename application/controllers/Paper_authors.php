<?php

defined('BASEPATH') or exit('No direct script access allowed');

use chriskacerguis\RestServer\RestController;

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST');
header("Access-Control-Allow-Headers: X-Requested-With");

class Paper_authors extends RestController
{

    function __construct()
    {
        parent::__construct();
    }

    function index_get()
    {
        $id = $this->get('id');
        if ($id == '') {
            $paper_authors = $this->mCore->get_data('paper_authors', ['is_active' => 1])->result_array();
            if ($paper_authors) {
                $this->response([
                    'status' => true,
                    'data' => $paper_authors
                ], 200);
            } else {
                $this->response([
                    'status' => false,
                    'message' => 'No result were found'
                ], 404);
            }
        } else {
            $paper_authors = $this->mCore->get_data('paper_authors', ['id' => $id, 'is_active' => 1])->row_array();
            if ($paper_authors) {
                $this->response([
                    'status' => true,
                    'data' => $paper_authors
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
        $paper_authors = $this->mCore->get_data('paper_authors', ['paper_detail_id' => $paper_detail_id, 'is_active' => 1])->result_array();
        if ($paper_authors) {
            $this->response([
                'status' => true,
                'data' => $paper_authors
            ], 200);
        } else {
            $this->response([
                'status' => false,
                'message' => 'No result were found'
            ], 404);
        }
    }

    function participant_get()
    {
        $participant_id = $this->get('participant_id');
        $paper_authors = $this->mCore->get_data('paper_authors', ['participant_id' => $participant_id, 'is_active' => 1])->result_array();
        if ($paper_authors) {
            $this->response([
                'status' => true,
                'data' => $paper_authors
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
            'participant_id' => $this->post('participant_id'),
            'paper_detail_id' => $this->post('paper_detail_id'),
            'name' => $this->post('name'),
            'institution' => $this->post('institution'),
            'email' => $this->post('email'),
            'is_participant' => $this->post('is_participant'),
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        );
        $sql = $this->mCore->save_data('paper_authors', array_filter($data));
        if ($sql) {
            $last_id = $this->mCore->get_lastid('paper_authors', 'id');
            $last_data = $this->mCore->get_data('paper_authors', ['id' => $last_id])->row_array();
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
            'institution' => $this->post('institution'),
            'email' => $this->post('email'),
            'is_participant' => $this->post('is_participant'),
            'updated_at' => date('Y-m-d H:i:s'),
        );
        $sql = $this->mCore->save_data('paper_authors', array_filter($data), true, ['id' => $id]);
        if ($sql) {
            $last_data = $this->mCore->get_data('paper_authors', ['id' => $id])->row_array();
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
        $sql = $this->mCore->save_data('paper_authors', $data, true, ['id' => $id]);
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
