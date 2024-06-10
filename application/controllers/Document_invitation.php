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

    //SIMPAN DATA
    function save_post()
    {
        $data = array(
            'program_document_id' => $this->post('program_document_id'),
            'content' => $this->post('content'),
            'sincerely' => $this->post('sincerely'),
            'sign_url' => NULL,
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        );
        $sql = $this->mCore->save_data('document_invitation', array_filter($data));
        if ($sql) {
            $last_id = $this->mCore->get_lastid('document_invitation', 'id');
            if (!empty($_FILES['sign_url']['name'])) {
                $upload_file = $this->upload_sign('sign_url', $last_id);
                if ($upload_file['status'] == 0) {
                    $this->response([
                        'status' => false,
                        'message' => $upload_file['message'],
                    ], 404);
                }
            }
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
            'sincerely' => $this->post('sincerely'),
            'updated_at' => date('Y-m-d H:i:s'),
        );
        $sql = $this->mCore->save_data('document_invitation', array_filter($data), true, ['id' => $id]);
        if ($sql) {
            if (!empty($_FILES['sign_url']['name'])) {
                $upload_file = $this->upload_sign('sign_url', $id);
                if ($upload_file['status'] == 0) {
                    $this->response([
                        'status' => false,
                        'message' => $upload_file['message'],
                    ], 404);
                }
            }
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

    // UPLOAD SIGN
    private function upload_sign($sign_url, $id)
    {

        $this->load->library('ftp');

        $data = $this->mCore->get_data('document_invitation', 'id = ' . $id)->row_array();
        if ($data['picture_url'] != '') {
            $exp = (explode('/', $data['picture_url']));
            $temp_img = end($exp);

            //FTP configuration
            $ftp_config['hostname'] = config_item('hostname_upload');
            $ftp_config['username'] = config_item('username_upload');
            $ftp_config['password'] = config_item('password_upload');
            $ftp_config['port'] = config_item('port_upload');
            $ftp_config['debug'] = true;

            $this->ftp->connect($ftp_config);

            $this->ftp->delete_file('document_invitation/' . $data['program_document_id'] . '/' . $temp_img);

            $this->ftp->close();
        }

        $config['upload_path'] = './uploads';
        $config['allowed_types'] = 'gif|jpg|png|jpeg';
        $config['max_size'] = 5000;
        $config['file_name'] = time();

        $this->load->library('upload', $config);
        $this->upload->initialize($config);
        if ($this->upload->do_upload($sign_url)) {

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

            if ($this->ftp->list_files('document_invitation/' . $data['program_document_id'] . '/') == false) {
                $this->ftp->mkdir('document_invitation/' . $data['program_document_id'] . '/', DIR_WRITE_MODE, true);
            }

            $destination = 'document_invitation/' . $data['program_document_id'] . '/' . $fileName;

            $this->ftp->upload($source, $destination);

            $this->ftp->close();

            //Delete file from local server
            @unlink($source);

            $sql = $this->mCore->save_data('document_invitation', ['sign_url' => config_item('dir_upload') . 'document_invitation/' . $data['program_document_id'] . '/' . $fileName], true, array('id' => $id));

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

    function generate_pdf_get()
    {
        $participant_id = $this->get('id');

        $option = array(
            'select' => 'participants.id, participants.full_name, participants.gender, users.email, 
                programs.name, programs.logo_url, program_documents.name, program_documents.file_url, 
                program_documents.drive_url,program_documents.is_upload, program_categories.name name_categories,
                program_categories.tagline, program_categories.logo_url logo_categories, program_categories.web_url web_categories, 
                program_categories.email email_categories,program_categories.contact contact_categories,
                document_invitation.content, document_invitation.sincerely, document_invitation.sign_url',
            'table' => 'participants',
            'join' => [
                'users' => 'participants.user_id = users.id',
                'programs' => 'participants.program_id = programs.id',
                'program_categories' => 'programs.program_category_id = program_categories.id',
                'program_documents' => 'programs.id = program_documents.program_id',
                'document_invitation' => 'program_documents.id = document_invitation.program_document_id',
            ],
            'where' => 'participants.id = ' . $participant_id . ' AND program_documents.id = 8',
        );
        $data = $this->mCore->join_table($option)->row_array();

        // print_r($data);
        // die();
        $parts = explode(" ", $data['full_name']);
        $this->load->library('pdf');
        $this->pdf->set_paper('A4', 'potrait');
        $this->pdf->filename = "Document Invitation - " . strtoupper($parts[0]) . ".pdf";
        $this->pdf->load_view('pdf/document_invitation', $data);
    }
}
