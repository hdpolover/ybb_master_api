<?php

defined('BASEPATH') or exit('No direct script access allowed');

use chriskacerguis\RestServer\RestController;

class Program_documents extends RestController
{

    function __construct()
    {
        parent::__construct();
    }

    function index_get()
    {
        $id = $this->get('id');
        if ($id == '') {
            $program_documents = $this->mCore->get_data('program_documents', ['is_active' => 1])->result_array();
            if ($program_documents) {
                $this->response([
                    'status' => true,
                    'data' => $program_documents
                ], 200);
            } else {
                $this->response([
                    'status' => false,
                    'message' => 'No result were found'
                ], 404);
            }
        } else {
            $program_documents = $this->mCore->get_data('program_documents', ['id' => $id, 'is_active' => 1])->row();
            if ($program_documents) {
                $this->response([
                    'status' => true,
                    'data' => $program_documents
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
        $program_id = $this->get('program_id');

        $program_documents = $this->mCore->get_data('program_documents', ['program_id' => $program_id, 'is_active' => 1])->result_array();
        if ($program_documents) {
            $this->response([
                'status' => true,
                'data' => $program_documents
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
        $id = $this->get('id');
        $option = array(
            'select' => 'participant_statuses.general_status',
            'table' => 'participants',
            'join' => ['participant_statuses' => 'participants.id = participant_statuses.participant_id'],
            'where' => 'participants.id = ' . $id,
        );

        $participant = $this->mCore->join_table($option)->row_array();
        $program_documents = $this->mCore->get_data('program_documents', ['visibility <=' => $participant['general_status']])->result_array();

        if ($program_documents) {
            $this->response([
                'status' => true,
                'data' => $program_documents
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
            'program_id' => $this->post('program_id'),
            'name' => $this->post('name'),
            'file_url' => NULL,
            'drive_url' => $this->post('drive_url'),
            'desc' => $this->post('desc'),
            'is_upload' => $this->post('is_upload'),
            'visibility' => $this->post('visibility'),
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        );
        $sql = $this->mCore->save_data('program_documents', array_filter($data));
        if ($sql) {
            $last_id = $this->mCore->get_lastid('program_documents', 'id');
            if (!empty($_FILES['file_url']['name'])) {
                $upload_file = $this->upload_file('file_url', $last_id);
                if ($upload_file['status'] == 0) {
                    $this->response([
                        'status' => false,
                        'message' => $upload_file['message']
                    ], 404);
                }
            }
            $last_data = $this->mCore->get_data('program_documents', ['id' => $last_id])->row_array();
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
            'file_url' => NULL,
            'drive_url' => $this->post('drive_url'),
            'desc' => $this->post('desc'),
            'is_upload' => $this->post('is_upload'),
            'visibility' => $this->post('visibility'),
            'updated_at' => date('Y-m-d H:i:s'),
        );
        $sql = $this->mCore->save_data('program_documents', array_filter($data), true, ['id' => $id]);
        if ($sql) {
            if (!empty($_FILES['file_url']['name'])) {
                $upload_file = $this->upload_file('file_url', $id);
                if ($upload_file['status'] == 0) {
                    $this->response([
                        'status' => false,
                        'message' => $upload_file['message']
                    ], 404);
                }
            }
            $last_data = $this->mCore->get_data('program_documents', ['id' => $id])->row_array();
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
        $sql = $this->mCore->save_data('program_documents', $data, true, ['id' => $id]);
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

    // UPLOAD FILE
    public function upload_file($file_url, $id)
    {

        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Methods: GET, PUT, PATCH, POST, DELETE');
        header("Access-Control-Allow-Headers: X-Requested-With");

        $this->load->library('ftp');

        $data = $this->mCore->get_data('program_documents', 'id = ' . $id)->row_array();
        if ($data['file_url'] != '') {
            $exp = (explode('/', $data['file_url']));
            $temp_img = end($exp);

            //FTP configuration
            $ftp_config['hostname'] = config_item('hostname_upload');
            $ftp_config['username'] = config_item('username_upload');
            $ftp_config['password'] = config_item('password_upload');
            $ftp_config['port'] = config_item('port_upload');
            $ftp_config['debug'] = TRUE;

            $this->ftp->connect($ftp_config);

            $this->ftp->delete_file('programs/' . $data['program_id'] . '/documents/' . $temp_img);

            $this->ftp->close();
        }

        $config['upload_path'] = './uploads';
        $config['allowed_types'] = '*';
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
            $ftp_config['debug'] = TRUE;

            $this->ftp->connect($ftp_config);

            if ($this->ftp->list_files('programs/' . $data['program_id'] . '/documents/') == FALSE) {
                $this->ftp->mkdir('programs/' . $data['program_id'] . '/documents/', DIR_WRITE_MODE);
            }

            $destination = 'programs/' . $data['program_id'] . '/documents/' . $fileName;

            $this->ftp->upload($source, $destination);

            $this->ftp->close();

            //Delete file from local server
            @unlink($source);

            $sql = $this->mCore->save_data('program_documents', ['file_url' => config_item('dir_upload') . 'programs/' . $data['program_id'] . '/documents/' . $fileName], true, array('id' => $id));

            if ($sql) {
                $data['status'] = 1;
                $data['message'] = 'Document saved successfully';
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

    // DIRECT UPLOAD FILE AGREEMENT
    public function agreement_letter_upload_post()
    {

        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Methods: GET, PUT, PATCH, POST, DELETE');
        header("Access-Control-Allow-Headers: X-Requested-With");

        $this->load->library('ftp');

        $id = $this->post('participant_id');

        $sql_check = $this->mCore->get_data('participant_agreement_letters', 'participant_id = ' . $id);
        $update = 0;
        if ($sql_check->num_rows() > 0) {
            $update = 1;
            $data = $sql_check->row_array();
            if ($data['file_link'] != '') {
                $exp = (explode('/', $data['file_link']));
                $temp_img = end($exp);

                //FTP configuration
                $ftp_config['hostname'] = config_item('hostname_upload');
                $ftp_config['username'] = config_item('username_upload');
                $ftp_config['password'] = config_item('password_upload');
                $ftp_config['port'] = config_item('port_upload');
                $ftp_config['debug'] = TRUE;

                $this->ftp->connect($ftp_config);

                $this->ftp->delete_file('agreement_letter/' . $id . '/' . $temp_img);

                $this->ftp->close();
            }
        }
        $config['upload_path'] = './uploads';
        $config['allowed_types'] = '*';
        $config['max_size'] = 5000;
        $config['file_name'] = time();

        $this->load->library('upload', $config);
        $this->upload->initialize($config);
        if ($this->upload->do_upload("file")) {

            $upload_data = $this->upload->data();
            $fileName = $upload_data['file_name'];

            $source = './uploads/' . $fileName;

            //FTP configuration
            $ftp_config['hostname'] = config_item('hostname_upload');
            $ftp_config['username'] = config_item('username_upload');
            $ftp_config['password'] = config_item('password_upload');
            $ftp_config['port'] = config_item('port_upload');
            $ftp_config['debug'] = TRUE;

            $this->ftp->connect($ftp_config);

            if ($this->ftp->list_files('agreement_letter/' . $id . '/') == FALSE) {
                $this->ftp->mkdir('agreement_letter/' . $id . '/', DIR_WRITE_MODE);
            }

            $destination = 'agreement_letter/' . $id . '/' . $fileName;

            $this->ftp->upload($source, $destination);

            $this->ftp->close();

            //Delete file from local server
            @unlink($source);

            if ($update) {
                $sql = $this->mCore->save_data('participant_agreement_letters', ['file_link' => config_item('dir_upload') . 'agreement_letter/' . $id . '/' . $fileName, 'updated_at' => date('Y-m-d H:i:s')], true, array('participant_id' => $id));
            } else {
                $sql = $this->mCore->save_data('participant_agreement_letters', ['participant_id' => $id, 'file_link' => config_item('dir_upload') . 'agreement_letter/' . $id . '/' . $fileName, 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')]);
            }

            if ($sql) {
                $last_data = $this->mCore->get_data('participant_agreement_letters', ['participant_id' => $id])->row_array();
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
        } else {
            $this->response([
                'status' => false,
                'message' => $this->upload->display_errors()
            ], 404);
        }
    }

    public function agreement_letter_get()
    {
        $id = $this->get('participant_id');
        if ($id == '') {
            $agreement_letter = $this->mCore->get_data('participant_agreement_letters', ['is_active' => 1])->result_array();
            if ($agreement_letter) {
                $this->response([
                    'status' => true,
                    'data' => $agreement_letter
                ], 200);
            } else {
                $this->response([
                    'status' => false,
                    'message' => 'No result were found'
                ], 404);
            }
        } else {
            $agreement_letter = $this->mCore->get_data('participant_agreement_letters', ['participant_id' => $id, 'is_active' => 1])->row();
            if ($agreement_letter) {
                $this->response([
                    'status' => true,
                    'data' => $agreement_letter
                ], 200);
            } else {
                $this->response([
                    'status' => false,
                    'message' => 'No result were found'
                ], 404);
            }
        }
    }
}
