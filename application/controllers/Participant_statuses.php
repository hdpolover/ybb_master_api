<?php

defined('BASEPATH') or exit('No direct script access allowed');

use chriskacerguis\RestServer\RestController;

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST');
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
            $participant_statuses = $this->mCore->get_data('participant_statuses', ['is_active' => 1])->result_array();
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
            $participant_statuses = $this->mCore->get_data('participant_statuses', ['id' => $id, 'is_active' => 1])->row();
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

        $participant_statuses = $this->mCore->get_data('participant_statuses', ['participant_id' => $participant_id, 'is_active' => 1])->result_array();
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

    function check_status_get()
    {
        $sql = $this->mCore->query_data("SELECT participants.id, participants.user_id, participants.full_name, participant_statuses.general_status 
        FROM participants LEFT JOIN participant_statuses ON participants.id = participant_statuses.participant_id AND participant_statuses.is_active = 1
        WHERE general_status IS NULL AND participants.is_active = 1")->result_array();

        if ($sql) {
            foreach ($sql as $row) {

                // participant statues
                $participant_statuses = array(
                    'participant_id' => $row['id'],
                    'general_status' => 0,
                    'form_status' => 0,
                    'document_status' => 0,
                    'payment_status' => 0,
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s'),
                );
                $this->mCore->save_data('participant_statuses', $participant_statuses);
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

    // function participant_status_post()
    // {
    //     $form_status = $this->post('form_status');
    //     $general_status = $this->post('general_status');
    //     if ($form_status == 2) {
    //         $sql = $this->mCore->query_data('UPDATE participant_statuses SET general_status = ' . $general_status);
    //         if ($sql) {
    //             $this->response([
    //                 'status' => true,
    //                 'data' => 'Yeay, success'
    //             ], 200);
    //         } else {
    //             $this->response([
    //                 'status' => false,
    //                 'message' => 'Sorry, failed to update'
    //             ], 404);
    //         }
    //     } else {
    //         $this->response([
    //             'status' => false,
    //             'message' => 'Sorry, not found participant statuses'
    //         ], 404);
    //     }
    // }

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

    //UPDATE STATUS
    function update_status_post($id)
    {
        $name_col = $this->post('name');
        $val_col = $this->post('value');

        $sql = $this->mCore->save_data('participant_statuses', [$name_col => $val_col], true, ['participant_id' => $id]);
        if ($sql) {
            $last_data = $this->mCore->get_data('participant_statuses', ['participant_id' => $id])->row_array();
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
