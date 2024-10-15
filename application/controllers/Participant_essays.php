<?php

defined('BASEPATH') or exit('No direct script access allowed');

use chriskacerguis\RestServer\RestController;

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST');
header("Access-Control-Allow-Headers: X-Requested-With");

class Participant_essays extends RestController
{

    public function __construct()
    {
        parent::__construct();
    }

    public function index_get()
    {
        $participant_id = $this->get('participant_id');
        if ($participant_id == '') {
            $option = array(
                'select' => 'participant_essays.*, program_essays.questions',
                'table' => 'participant_essays',
                'join' => ['program_essays' => 'participant_essays.program_essay_id = program_essays.id AND program_essays.is_active = 1'],
                'where' => 'participant_essays.is_active = 1'
            );
            $participant_essays = $this->mCore->join_table($option)->result_array();
            if ($participant_essays) {
                $this->response([
                    'status' => true,
                    'data' => $participant_essays,
                ], 200);
            } else {
                $this->response([
                    'status' => false,
                    'message' => 'No result were found',
                ], 404);
            }
        } else {
            $option = array(
                'select' => 'participant_essays.*, program_essays.questions',
                'table' => 'participant_essays',
                'join' => ['program_essays' => 'participant_essays.program_essay_id = program_essays.id AND program_essays.is_active = 1'],
                'where' => 'participant_id = ' . $participant_id . ' AND participant_essays.is_active = 1',
            );
            $participant_essays = $this->mCore->join_table($option)->result_array();
            if ($participant_essays) {
                $this->response([
                    'status' => true,
                    'data' => $participant_essays,
                ], 200);
            } else {
                $this->response([
                    'status' => false,
                    'message' => 'No result were found',
                ], 404);
            }
        }
    }

    //SIMPAN DATA
    public function save_post()
    {
        $participant_id = $this->post('participant_id');
        $program_essay_id = $this->post('program_essay_id');
        $check_participant = $this->mCore->get_data('participant_essays', ['participant_id' => $participant_id, 'program_essay_id' => $program_essay_id]);
        // exists or not
        if ($check_participant->num_rows() > 0) {
            // update
            $data = array(
                'participant_id' => $this->post('participant_id'),
                'program_essay_id' => $this->post('program_essay_id'),
                'answer' => $this->post('answer'),
                'updated_at' => date('Y-m-d H:i:s'),
            );
            $sql = $this->mCore->save_data('participant_essays', array_filter($data), true, ['id' => $check_participant->row_array()['id']]);
            $last_data = $this->mCore->get_data('participant_essays', ['id' => $check_participant->row_array()['id']])->row_array();
        } else {
            // insert
            $data = array(
                'participant_id' => $this->post('participant_id'),
                'program_essay_id' => $this->post('program_essay_id'),
                'answer' => $this->post('answer'),
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            );
            $sql = $this->mCore->save_data('participant_essays', array_filter($data));
            $last_id = $this->mCore->get_lastid('participant_essays', 'id');
            $last_data = $this->mCore->get_data('participant_essays', ['id' => $last_id])->row_array();
        }
        if ($sql) {
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
            'participant_id' => $this->post('participant_id'),
            'program_essay_id' => $this->post('program_essay_id'),
            'answer' => $this->post('answer'),
            'updated_at' => date('Y-m-d H:i:s'),
        );
        $sql = $this->mCore->save_data('participant_essays', array_filter($data), true, ['id' => $id]);
        if ($sql) {
            $last_data = $this->mCore->get_data('participant_essays', ['id' => $id])->row_array();
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
        $sql = $this->mCore->save_data('participant_essays', $data, true, ['id' => $id]);
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
