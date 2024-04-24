<?php

defined('BASEPATH') or exit('No direct script access allowed');

use chriskacerguis\RestServer\RestController;

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, PUT, PATCH, POST, DELETE');
header("Access-Control-Allow-Headers: X-Requested-With");

class Participant_subthemes extends RestController
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
                'select' => 'participant_subthemes.*, programs.name, programs.logo_url, programs.description, program_subthemes.name, program_subthemes.desc',
                'table' => 'participant_subthemes',
                'join' => [
                    'program_subthemes' => 'participant_subthemes.program_subtheme_id = program_subthemes.id',
                    'programs' => 'program_subthemes.program_id = programs.id',
                ],
            );
            $participant_subthemes = $this->mCore->join_table($option)->result_array();
            if ($participant_subthemes) {
                $this->response([
                    'status' => true,
                    'data' => $participant_subthemes,
                ], 200);
            } else {
                $this->response([
                    'status' => false,
                    'message' => 'No result were found',
                ], 404);
            }
        } else {
            $option = array(
                'select' => 'participant_subthemes.*, programs.name, programs.logo_url, programs.description, program_subthemes.name, program_subthemes.desc',
                'table' => 'participant_subthemes',
                'join' => [
                    'program_subthemes' => 'participant_subthemes.program_subtheme_id = program_subthemes.id',
                    'programs' => 'program_subthemes.program_id = programs.id',
                ],
                'where' => 'participant_id = ' . $participant_id,
            );
            $participant_subthemes = $this->mCore->join_table($option)->row();
            if ($participant_subthemes) {
                $this->response([
                    'status' => true,
                    'data' => $participant_subthemes,
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
        // $program_subtheme_id = $this->post('program_subtheme_id');
        $check_subtheme = $this->mCore->get_data('participant_subthemes', ['participant_id' => $participant_id]);
        // exists or not
        if ($check_subtheme->num_rows() > 0) {
            // update
            $data = array(
                'program_subtheme_id' => $this->post('program_subtheme_id'),
                'participant_id' => $this->post('participant_id'),
                'updated_at' => date('Y-m-d H:i:s'),
            );
            $sql = $this->mCore->save_data('participant_subthemes', array_filter($data), true, ['id' => $check_subtheme->row_array()['id']]);
            $last_data = $this->mCore->get_data('participant_subthemes', ['id' => $check_subtheme->row_array()['id']])->row_array();
        } else {
            // insert
            $data = array(
                'program_subtheme_id' => $this->post('program_subtheme_id'),
                'participant_id' => $this->post('participant_id'),
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            );
            $sql = $this->mCore->save_data('participant_subthemes', array_filter($data));
            $last_id = $this->mCore->get_lastid('participant_subthemes', 'id');
            $last_data = $this->mCore->get_data('participant_subthemes', ['id' => $last_id])->row_array();
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
            'program_subtheme_id' => $this->post('program_subtheme_id'),
            'participant_id' => $this->post('participant_id'),
            'updated_at' => date('Y-m-d H:i:s'),
        );
        $sql = $this->mCore->save_data('participant_subthemes', array_filter($data), true, ['id' => $id]);
        if ($sql) {
            $last_data = $this->mCore->get_data('participant_subthemes', ['id' => $id])->row_array();
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
        $sql = $this->mCore->save_data('participant_subthemes', $data, true, ['id' => $id]);
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
