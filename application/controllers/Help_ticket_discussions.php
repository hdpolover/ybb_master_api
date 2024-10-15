<?php

defined('BASEPATH') or exit('No direct script access allowed');

use chriskacerguis\RestServer\RestController;

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST');
header("Access-Control-Allow-Headers: X-Requested-With");

class Help_ticket_discussions extends RestController
{

    public function __construct()
    {
        parent::__construct();
    }

    public function index_get()
    {
        $id = $this->get('id');
        if ($id == '') {
            $help_ticket_discussions = $this->mCore->get_data('help_ticket_discussions', ['is_active' => 1])->result_array();
            if ($help_ticket_discussions) {
                $this->response([
                    'status' => true,
                    'data' => $help_ticket_discussions,
                ], 200);
            } else {
                $this->response([
                    'status' => false,
                    'message' => 'No result were found',
                ], 404);
            }
        } else {
            $help_ticket_discussions = $this->mCore->get_data('help_ticket_discussions', ['id' => $id, 'is_active' => 1])->row_array();
            if ($help_ticket_discussions) {
                $this->response([
                    'status' => true,
                    'data' => $help_ticket_discussions,
                ], 200);
            } else {
                $this->response([
                    'status' => false,
                    'message' => 'No result were found',
                ], 404);
            }
        }
    }

    public function list_get()
    {
        $help_ticket_id = $this->get('help_ticket_id');

        $help_ticket_discussions = $this->mCore->get_data('help_ticket_discussions', ['help_ticket_id' => $help_ticket_id, 'is_active' => 1])->result_array();
        if ($help_ticket_discussions) {
            $this->response([
                'status' => true,
                'data' => $help_ticket_discussions,
            ], 200);
        } else {
            $this->response([
                'status' => false,
                'message' => 'No result were found',
            ], 404);
        }
    }

    public function list_participant_get()
    {
        $participant_id = $this->get('participant_id');

        $help_ticket_discussions = $this->mCore->get_data('help_ticket_discussions', ['participant_id' => $participant_id, 'is_active' => 1])->result_array();
        if ($help_ticket_discussions) {
            $this->response([
                'status' => true,
                'data' => $help_ticket_discussions,
            ], 200);
        } else {
            $this->response([
                'status' => false,
                'message' => 'No result were found',
            ], 404);
        }
    }

    public function list_admin_get()
    {
        $admin_id = $this->get('admin_id');

        $help_ticket_discussions = $this->mCore->get_data('help_ticket_discussions', ['admin_id' => $admin_id, 'is_active' => 1])->result_array();
        if ($help_ticket_discussions) {
            $this->response([
                'status' => true,
                'data' => $help_ticket_discussions,
            ], 200);
        } else {
            $this->response([
                'status' => false,
                'message' => 'No result were found',
            ], 404);
        }
    }

    //SIMPAN DATA
    public function save_post()
    {
        $data = array(
            'help_ticket_id' => $this->post('help_ticket_id'),
            'message' => $this->post('message'),
            'participant_id' => $this->post('participant_id'),
            'admin_id' => $this->post('admin_id'),
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        );
        $sql = $this->mCore->save_data('help_ticket_discussions', array_filter($data));
        if ($sql) {
            $last_id = $this->mCore->get_lastid('help_ticket_discussions', 'id');
            $last_data = $this->mCore->get_data('help_ticket_discussions', ['id' => $last_id])->row_array();
            $this->response([
                'status' => true,
                'data' => $last_data,
            ], 200);
        } else {
            $this->response([
                'status' => false,
                'message' => 'Sorry, failed to save',
            ], 404);
        }
    }

    //UPDATE DATA
    public function update_post($id)
    {
        $data = array(
            'help_ticket_id' => $this->post('help_ticket_id'),
            'message' => $this->post('message'),
            'participant_id' => $this->post('participant_id'),
            'admin_id' => $this->post('admin_id'),
            'updated_at' => date('Y-m-d H:i:s'),
        );
        $sql = $this->mCore->save_data('help_ticket_discussions', array_filter($data), true, ['id' => $id]);
        if ($sql) {
            $last_data = $this->mCore->get_data('help_ticket_discussions', ['id' => $id])->row_array();
            $this->response([
                'status' => true,
                'data' => $last_data,
            ], 200);
        } else {
            $this->response([
                'status' => false,
                'message' => 'Sorry, failed to update',
            ], 404);
        }
    }

    //DELETE DATA
    public function delete_get()
    {
        $id = $this->get('id');
        $data = array(
            'is_active' => 0,
            'is_deleted' => 1,
            // 'updated_at' => date('Y-m-d H:i:s')
        );
        $sql = $this->mCore->save_data('help_ticket_discussions', $data, true, ['id' => $id]);
        if ($sql) {
            $this->response([
                'status' => true,
                'message' => 'Data deleted successfully',
            ], 200);
        } else {
            $this->response([
                'status' => false,
                'message' => 'Sorry, failed to delete',
            ], 404);
        }
    }
}
