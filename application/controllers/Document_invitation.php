<?php

defined('BASEPATH') or exit('No direct script access allowed');

use chriskacerguis\RestServer\RestController;

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, PUT, PATCH, POST, DELETE');
header("Access-Control-Allow-Headers: X-Requested-With");

class Document_invitation extends RestController
{

    function __construct()
    {
        parent::__construct();
    }

    function index_get()
    {
        $id = $this->get('id');
        if ($id == '') {
            $document_invitation = $this->mCore->list_data('document_invitation')->result_array();
            if ($document_invitation) {
                $this->response([
                    'status' => true,
                    'data' => $document_invitation
                ], 200);
            } else {
                $this->response([
                    'status' => false,
                    'message' => 'No result were found'
                ], 404);
            }
        } else {
            $document_invitation = $this->mCore->get_data('document_invitation', ['id' => $id])->row();
            if ($document_invitation) {
                $this->response([
                    'status' => true,
                    'data' => $document_invitation
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
        $program_document_id = $this->get('program_document_id');

        $document_invitation = $this->mCore->get_data('document_invitation', ['program_document_id' => $program_document_id])->result_array();
        if ($document_invitation) {
            $this->response([
                'status' => true,
                'data' => $document_invitation
            ], 200);
        } else {
            $this->response([
                'status' => false,
                'message' => 'No result were found'
            ], 404);
        }
    }

    function check_status_get()
    {
        $sql = $this->mCore->query_data("SELECT participants.id, participants.user_id, participants.full_name, document_invitation.general_status 
        FROM participants LEFT JOIN document_invitation ON participants.id = document_invitation.participant_id
        WHERE general_status IS NULL")->result_array();

        if ($sql) {
            foreach ($sql as $row) {

                // participant statues
                $document_invitation = array(
                    'participant_id' => $row['id'],
                    'general_status' => 0,
                    'form_status' => 0,
                    'document_status' => 0,
                    'payment_status' => 0,
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s'),
                );
                $this->mCore->save_data('document_invitation', $document_invitation);
            }
            $this->response([
                'status' => true,
                'data' => 'Yeay, success'
            ], 200);
        }
        $this->response([
            'status' => false,
            'message' => 'Sorry, not found participant statuses'
        ], 404);
    }

    //SIMPAN DATA
    function save_post()
    {
        $data = array(
            'program_document_id' => $this->post('program_document_id'),
            'content' => $this->post('content'),
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        );
        $sql = $this->mCore->save_data('document_invitation', array_filter($data));
        if ($sql) {
            $last_id = $this->mCore->get_lastid('document_invitation', 'id');
            $last_data = $this->mCore->get_data('document_invitation', ['id' => $last_id])->row_array();
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
            'program_document_id' => $this->post('program_document_id'),
            'content' => $this->post('content'),
            'updated_at' => date('Y-m-d H:i:s'),
        );
        $sql = $this->mCore->save_data('document_invitation', array_filter($data), true, ['id' => $id]);
        if ($sql) {
            $last_data = $this->mCore->get_data('document_invitation', ['id' => $id])->row_array();
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
        $sql = $this->mCore->save_data('document_invitation', $data, true, ['id' => $id]);
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
