<?php

defined('BASEPATH') or exit('No direct script access allowed');

use chriskacerguis\RestServer\RestController;

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, PUT, PATCH, POST, DELETE');
header("Access-Control-Allow-Headers: X-Requested-With");

class Participant_statuses extends RestController
{

    function __construct()
    {
        parent::__construct();
    }

    function index_get()
    {
        $id = $this->get('id');
        if ($id == '') {
            $participant_statuses = $this->mCore->list_data('participant_statuses')->result_array();
            if ($participant_statuses) {
                $this->response([
                    'status' => true,
                    'data' => $participant_statuses
                ], 200);
            } else {
                $this->response([
                    'status' => false,
                    'message' => 'No result were found'
                ], 404);
            }
        } else {
            $participant_statuses = $this->mCore->get_data('participant_statuses', ['id' => $id])->row();
            if ($participant_statuses) {
                $this->response([
                    'status' => true,
                    'data' => $participant_statuses
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
        $participant_id = $this->get('participant_id');

        $participant_statuses = $this->mCore->get_data('participant_statuses', ['participant_id' => $participant_id])->result_array();
        if ($participant_statuses) {
            $this->response([
                'status' => true,
                'data' => $participant_statuses
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
            'general_status' => $this->post('general_status'),
            'form_status' => $this->post('form_status'),
            'document_status' => $this->post('document_status'),
            'payment_status' => $this->post('payment_status'),
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        );
        $sql = $this->mCore->save_data('participant_statuses', array_filter($data));
        if ($sql) {
            $last_id = $this->mCore->get_lastid('participant_statuses', 'id');
            $last_data = $this->mCore->get_data('participant_statuses', ['id' => $last_id])->row_array();
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
            'participant_id' => $this->post('participant_id'),
            'general_status' => $this->post('general_status'),
            'form_status' => $this->post('form_status'),
            'document_status' => $this->post('document_status'),
            'payment_status' => $this->post('payment_status'),
            'updated_at' => date('Y-m-d H:i:s'),
        );
        $sql = $this->mCore->save_data('participant_statuses', array_filter($data), true, ['id' => $id]);
        if ($sql) {
            $last_data = $this->mCore->get_data('participant_statuses', ['id' => $id])->row_array();
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
        $sql = $this->mCore->save_data('participant_statuses', $data, true, ['id' => $id]);
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
