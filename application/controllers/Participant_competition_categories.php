<?php

defined('BASEPATH') or exit('No direct script access allowed');

use chriskacerguis\RestServer\RestController;

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, PUT, PATCH, POST, DELETE');
header("Access-Control-Allow-Headers: X-Requested-With");

class Participant_competition_categories extends RestController
{

    function __construct()
    {
        parent::__construct();
    }

    function index_get()
    {
        $participant_id = $this->get('participant_id');
        if ($participant_id == '') {
            $option = array(
                'select' => 'participant_competition_categories.*, competition_categories.category,competition_categories.desc',
                'table' => 'participant_competition_categories',
                'join' => [
                    'competition_categories' => 'participant_competition_categories.competition_category_id = competition_categories.id',
                    'program_categories' => 'competition_categories.program_category_id = program_categories.id'
                ],
            );
            $participant_competition_categories = $this->mCore->join_table($option)->result_array();
            if ($participant_competition_categories) {
                $this->response([
                    'status' => true,
                    'data' => $participant_competition_categories
                ], 200);
            } else {
                $this->response([
                    'status' => false,
                    'message' => 'No result were found'
                ], 404);
            }
        } else {
            $option = array(
                'select' => 'participant_competition_categories.*, competition_categories.category,competition_categories.desc',
                'table' => 'participant_competition_categories',
                'join' => [
                    'competition_categories' => 'participant_competition_categories.competition_category_id = competition_categories.id',
                    'program_categories' => 'competition_categories.program_category_id = program_categories.id'
                ],
                'where' => 'participant_competition_categories.participant_id = ' . $participant_id
            );
            $participant_competition_categories = $this->mCore->join_table($option)->row();
            if ($participant_competition_categories) {
                $this->response([
                    'status' => true,
                    'data' => $participant_competition_categories
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
            'participant_id' => $this->post('participant_id'),
            'competition_category_id' => $this->post('competition_category_id'),
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        );
        $sql = $this->mCore->save_data('participant_competition_categories', array_filter($data));
        if ($sql) {
            $last_id = $this->mCore->get_lastid('participant_competition_categories', 'id');
            $last_data = $this->mCore->get_data('participant_competition_categories', ['id' => $last_id])->row_array();
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
            'competition_category_id' => $this->post('competition_category_id'),
            'updated_at' => date('Y-m-d H:i:s'),
        );
        $sql = $this->mCore->save_data('participant_competition_categories', array_filter($data), true, ['id' => $id]);
        if ($sql) {
            $last_data = $this->mCore->get_data('participant_competition_categories', ['id' => $id])->row_array();
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
        $sql = $this->mCore->save_data('participant_competition_categories', $data, true, ['id' => $id]);
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
