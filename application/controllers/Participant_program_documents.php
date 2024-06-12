<?php

defined('BASEPATH') or exit('No direct script access allowed');

use chriskacerguis\RestServer\RestController;

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, PUT, PATCH, POST, DELETE');
header("Access-Control-Allow-Headers: X-Requested-With");

class Participant_program_documents extends RestController
{

    public function __construct()
    {
        parent::__construct();
    }

    public function index_get()
    {
        $id = $this->get('id');
        if ($id == '') {
            $participant_program_documents = $this->mCore->get_data('participant_program_documents', ['is_active' => 1])->result_array();
            if ($participant_program_documents) {
                $this->response([
                    'status' => true,
                    'data' => $participant_program_documents,
                ], 200);
            } else {
                $this->response([
                    'status' => false,
                    'message' => 'No result were found',
                ], 404);
            }
        } else {
            $participant_program_documents = $this->mCore->get_data('participant_program_documents', ['id' => $id, 'is_active' => 1])->row();
            if ($participant_program_documents) {
                $this->response([
                    'status' => true,
                    'data' => $participant_program_documents,
                ], 200);
            } else {
                $this->response([
                    'status' => false,
                    'message' => 'No result were found',
                ], 404);
            }
        }
    }

    public function list_part_get()
    {
        $participant_id = $this->get('participant_id');

        $participant_program_documents = $this->mCore->get_data('participant_program_documents', ['participant_id' => $participant_id, 'is_active' => 1])->result_array();
        if ($participant_program_documents) {
            $this->response([
                'status' => true,
                'data' => $participant_program_documents,
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
        $participant_id = $this->post('participant_id');
        $program_document_id = $this->post('program_document_id');
        $check_program_document = $this->mCore->get_data('participant_program_documents', ['participant_id' => $participant_id, 'program_document_id' => $program_document_id]);
        // exists or not
        if ($check_program_document->num_rows() > 0) {
            // update
            $data = array(
                'participant_id' => $this->post('participant_id'),
                'program_document_id' => $this->post('program_document_id'),
                'file_url' => null,
                'updated_at' => date('Y-m-d H:i:s'),
            );
            $sql = $this->mCore->save_data('participant_program_documents', array_filter($data), true, ['id' => $check_program_document->row_array()['id']]);
            $last_data = $this->mCore->get_data('participant_program_documents', ['id' => $check_program_document->row_array()['id']])->row_array();
        } else {
            // insert
            $data = array(
                'participant_id' => $this->post('participant_id'),
                'program_document_id' => $this->post('program_document_id'),
                'file_url' => null,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            );
            $sql = $this->mCore->save_data('participant_program_documents', array_filter($data));
            $last_id = $this->mCore->get_lastid('participant_program_documents', 'id');
            $last_data = $this->mCore->get_data('participant_program_documents', ['id' => $last_id])->row_array();
        }
        if ($sql) {
            if (!empty($_FILES['file_url']['name'])) {
                $upload_file = $this->upload_file('file_url', $last_id);
                if ($upload_file['status'] == 0) {
                    $this->response([
                        'status' => false,
                        'message' => $upload_file['message'],
                    ], 404);
                }
            }
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
            'program_document_id' => $this->post('program_document_id'),
            'file_url' => null,
            'updated_at' => date('Y-m-d H:i:s'),
        );
        $sql = $this->mCore->save_data('participant_program_documents', array_filter($data), true, ['id' => $id]);
        if ($sql) {
            if (!empty($_FILES['file_url']['name'])) {
                $upload_file = $this->upload_file('file_url', $id);
                if ($upload_file['status'] == 0) {
                    $this->response([
                        'status' => false,
                        'message' => $upload_file['message'],
                    ], 404);
                }
            }
            $last_data = $this->mCore->get_data('participant_program_documents', ['id' => $id])->row_array();
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
        $sql = $this->mCore->save_data('participant_program_documents', $data, true, ['id' => $id]);
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

    // UPLOAD FILE
    public function upload_file($file_url, $id)
    {

        $this->load->library('ftp');

        $data = $this->mCore->get_data('participant_program_documents', 'id = ' . $id)->row_array();
        if ($data['file_url'] != '') {
            $exp = (explode('/', $data['file_url']));
            $temp_img = end($exp);

            //FTP configuration
            $ftp_config['hostname'] = config_item('hostname_upload');
            $ftp_config['username'] = config_item('username_upload');
            $ftp_config['password'] = config_item('password_upload');
            $ftp_config['port'] = config_item('port_upload');
            $ftp_config['debug'] = true;

            $this->ftp->connect($ftp_config);

            $this->ftp->delete_file('participant_program_documents/' . $id . '/' . $temp_img);

            $this->ftp->close();
        }

        $config['upload_path'] = './uploads';
        $config['allowed_types'] = 'gif|jpg|png|jpeg';
        $config['max_size'] = 5000;
        $config['file_name'] = time();

        $this->load->library('upload', $config);
        $this->upload->initialize($config);
        if ($this->upload->do_upload($file_url)) {

            $upload_data = $this->upload->data();
            $fileName = $upload_data['file_name'];

            $source = './uploads/' . $fileName;

            //FTP configuration
            $ftp_config['hostname'] = config_item('hostname_upload');
            $ftp_config['username'] = config_item('username_upload');
            $ftp_config['password'] = config_item('password_upload');
            $ftp_config['port'] = config_item('port_upload');
            $ftp_config['debug'] = true;

            $this->ftp->connect($ftp_config);

            if ($this->ftp->list_files('participant_program_documents/' . $id . '/') == false) {
                $this->ftp->mkdir('participant_program_documents/' . $id . '/', DIR_WRITE_MODE);
            }

            $destination = 'participant_program_documents/' . $id . '/' . $fileName;

            $this->ftp->upload($source, $destination);

            $this->ftp->close();

            //Delete file from local server
            @unlink($source);

            $sql = $this->mCore->save_data('participant_program_documents', ['file_url' => config_item('dir_upload') . 'participant_program_documents/' . $id . '/' . $fileName], true, array('id' => $id));

            if ($sql) {
                $data['status'] = 1;
                $data['message'] = 'Image saved successfully';
            } else {
                $data['status'] = 0;
                $data['message'] = 'Sorry, failed to update';
            }
        } else {
            $data['status'] = 0;
            $data['message'] = $this->upload->display_errors();
        }

        return $data;
    }

    // UPLOAD FILE DIRECT
    public function do_upload_file_post()
    {

        $this->load->library('ftp');

        $id = $this->post('id');

        $data = $this->mCore->get_data('participant_program_documents', 'id = ' . $id)->row_array();
        if ($data['file_url'] != '') {
            $exp = (explode('/', $data['file_url']));
            $temp_img = end($exp);

            //FTP configuration
            $ftp_config['hostname'] = config_item('hostname_upload');
            $ftp_config['username'] = config_item('username_upload');
            $ftp_config['password'] = config_item('password_upload');
            $ftp_config['port'] = config_item('port_upload');
            $ftp_config['debug'] = true;

            $this->ftp->connect($ftp_config);

            $this->ftp->delete_file('participant_program_documents/' . $id . '/' . $temp_img);

            $this->ftp->close();
        }

        $config['upload_path'] = './uploads';
        $config['allowed_types'] = 'gif|jpg|png|jpeg';
        $config['max_size'] = 5000;
        $config['file_name'] = time();

        $this->load->library('upload', $config);
        $this->upload->initialize($config);
        if ($this->upload->do_upload("logo")) {

            $upload_data = $this->upload->data();
            $fileName = $upload_data['file_name'];

            $source = './uploads/' . $fileName;

            //FTP configuration
            $ftp_config['hostname'] = config_item('hostname_upload');
            $ftp_config['username'] = config_item('username_upload');
            $ftp_config['password'] = config_item('password_upload');
            $ftp_config['port'] = config_item('port_upload');
            $ftp_config['debug'] = true;

            $this->ftp->connect($ftp_config);

            if ($this->ftp->list_files('participant_program_documents/' . $id . '/') == false) {
                $this->ftp->mkdir('participant_program_documents/' . $id . '/', DIR_WRITE_MODE);
            }

            $destination = 'participant_program_documents/' . $id . '/' . $fileName;

            $this->ftp->upload($source, $destination);

            $this->ftp->close();

            //Delete file from local server
            @unlink($source);

            $sql = $this->mCore->save_data('participant_program_documents', ['file_url' => config_item('dir_upload') . 'participant_program_documents/' . $id . '/' . $fileName], true, array('id' => $id));

            if ($sql) {
                $this->response([
                    'status' => true,
                    'message' => 'Image saved successfully',
                ], 200);
            } else {
                $this->response([
                    'status' => false,
                    'message' => 'Sorry, failed to update',
                ], 404);
            }
        } else {
            $this->response([
                'status' => false,
                'message' => $this->upload->display_errors(),
            ], 404);
        }
    }
}
