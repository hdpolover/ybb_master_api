<?php

defined('BASEPATH') or exit('No direct script access allowed');

use chriskacerguis\RestServer\RestController;

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, PUT, PATCH, POST, DELETE');
header("Access-Control-Allow-Headers: X-Requested-With");

class Participant_certificates extends RestController
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
                'select' => 'participant_certificates.*, program_certificates.template_url, program_certificates.title, program_certificates.description',
                'table' => 'participant_certificates',
                'join' => ['program_certificates' => 'participant_certificates.program_certificate_id = program_certificates.id AND program_certificates.is_active = 1'],
                'where' => 'participant_certificates.is_active = 1'
            );
            $participant_certificates = $this->mCore->join_table($option)->result_array();
            if ($participant_certificates) {
                $this->response([
                    'status' => true,
                    'data' => $participant_certificates,
                ], 200);
            } else {
                $this->response([
                    'status' => false,
                    'message' => 'No result were found',
                ], 404);
            }
        } else {
            $option = array(
                'select' => 'participant_certificates.*, program_certificates.template_url, program_certificates.title, program_certificates.description',
                'table' => 'participant_certificates',
                'join' => ['program_certificates' => 'participant_certificates.program_certificate_id = program_certificates.id AND program_certificates.is_active = 1'],
                'where' => 'participant_id = ' . $participant_id . ' AND program_certificates.is_active = 1',
            );
            $participant_certificates = $this->mCore->join_table($option)->result_array();
            if ($participant_certificates) {
                $this->response([
                    'status' => true,
                    'data' => $participant_certificates,
                ], 200);
            } else {
                $this->response([
                    'status' => false,
                    'message' => 'No result were found',
                ], 404);
            }
        }
    }

    function list_get()
    {
        $participant_id = $this->get('participant_id');

        $participant_certificates = $this->mCore->get_data('participant_certificates', ['participant_id' => $participant_id, 'is_active' => 1])->result_array();
        if ($participant_certificates) {
            $this->response([
                'status' => true,
                'data' => $participant_certificates
            ], 200);
        } else {
            $this->response([
                'status' => false,
                'message' => 'No result were found'
            ], 404);
        }
    }

    function list_certificate_get()
    {
        $program_certificate_id = $this->get('program_certificate_id');

        $participant_certificates = $this->mCore->get_data('participant_certificates', ['program_certificate_id' => $program_certificate_id, 'is_active' => 1])->result_array();
        if ($participant_certificates) {
            $this->response([
                'status' => true,
                'data' => $participant_certificates
            ], 200);
        } else {
            $this->response([
                'status' => false,
                'message' => 'No result were found'
            ], 404);
        }
    }

    //SIMPAN DATA
    public function save_post()
    {
        $participant_id = $this->post('participant_id');
        $program_certificate_id = $this->post('program_certificate_id');
        $check_participant = $this->mCore->get_data('participant_certificates', ['participant_id' => $participant_id, 'program_certificate_id' => $program_certificate_id]);
        // exists or not
        if ($check_participant->num_rows() > 0) {
            // update
            $data = array(
                'participant_id' => $this->post('participant_id'),
                'program_certificate_id' => $this->post('program_certificate_id'),
                'valid_until' => $this->post('valid_until'),
                'updated_at' => date('Y-m-d H:i:s'),
            );
            $sql = $this->mCore->save_data('participant_certificates', array_filter($data), true, ['id' => $check_participant->row_array()['id']]);
            $last_data = $this->mCore->get_data('participant_certificates', ['id' => $check_participant->row_array()['id']])->row_array();
        } else {
            // insert
            $data = array(
                'participant_id' => $this->post('participant_id'),
                'program_certificate_id' => $this->post('program_certificate_id'),
                'valid_until' => $this->post('valid_until'),
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            );
            $sql = $this->mCore->save_data('participant_certificates', array_filter($data));
            $last_id = $this->mCore->get_lastid('participant_certificates', 'id');
            $last_data = $this->mCore->get_data('participant_certificates', ['id' => $last_id])->row_array();
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
            'program_certificate_id' => $this->post('program_certificate_id'),
            'valid_until' => $this->post('valid_until'),
            'updated_at' => date('Y-m-d H:i:s'),
        );
        $sql = $this->mCore->save_data('participant_certificates', array_filter($data), true, ['id' => $id]);
        if ($sql) {
            $last_data = $this->mCore->get_data('participant_certificates', ['id' => $id])->row_array();
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
        $sql = $this->mCore->save_data('participant_certificates', $data, true, ['id' => $id]);
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
