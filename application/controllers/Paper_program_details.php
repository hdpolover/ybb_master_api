<?php

defined('BASEPATH') or exit('No direct script access allowed');

use chriskacerguis\RestServer\RestController;

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST');
header("Access-Control-Allow-Headers: X-Requested-With");

class Paper_program_details extends RestController
{

    function __construct()
    {
        parent::__construct();
    }

    function index_get()
    {
        $id = $this->get('id');
        if ($id == '') {
            $paper_program_details = $this->mCore->get_data('paper_program_details', ['is_active' => 1])->result_array();
            if ($paper_program_details) {
                $this->response([
                    'status' => true,
                    'data' => $paper_program_details
                ], 200);
            } else {
                $this->response([
                    'status' => false,
                    'message' => 'No result were found'
                ], 404);
            }
        } else {
            $paper_program_details = $this->mCore->get_data('paper_program_details', ['id' => $id, 'is_active' => 1])->row_array();
            if ($paper_program_details) {
                $this->response([
                    'status' => true,
                    'data' => $paper_program_details
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
        $paper_program_details = $this->mCore->get_data('paper_program_details', ['program_id' => $program_id, 'is_active' => 1])->result_array();
        if ($paper_program_details) {
            $this->response([
                'status' => true,
                'data' => $paper_program_details
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
            'topics' => $this->post('topics'),
            'topic_img_url' => NULL,
            'paper_format' => $this->post('paper_format'),
            'committees' => $this->post('committees'),
            'committee_img_url' => NULL,
            'books' => $this->post('books'),
            'timeline' => $this->post('timeline'),
            'contact_us' => $this->post('contact_us'),
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        );
        $sql = $this->mCore->save_data('paper_program_details', array_filter($data));
        if ($sql) {
            $last_id = $this->mCore->get_lastid('paper_program_details', 'id');
            if (!empty($_FILES['topic_img_url']['name'])) {
                $upload_file = $this->upload_image_topic('topic_img_url', $last_id);
                if ($upload_file['status'] == 0) {
                    $this->response([
                        'status' => false,
                        'message' => $upload_file['message']
                    ], 404);
                }
            }
            if (!empty($_FILES['committee_img_url']['name'])) {
                $upload_file = $this->upload_image_committee('committee_img_url', $last_id);
                if ($upload_file['status'] == 0) {
                    $this->response([
                        'status' => false,
                        'message' => $upload_file['message']
                    ], 404);
                }
            }
            $last_data = $this->mCore->get_data('paper_program_details', ['id' => $last_id])->row_array();
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
            'topics' => $this->post('topics'),
            'topic_img_url' => NULL,
            'paper_format' => $this->post('paper_format'),
            'committees' => $this->post('committees'),
            'committee_img_url' => NULL,
            'books' => $this->post('books'),
            'timeline' => $this->post('timeline'),
            'contact_us' => $this->post('contact_us'),
            'updated_at' => date('Y-m-d H:i:s'),
        );
        $sql = $this->mCore->save_data('paper_program_details', array_filter($data), true, ['id' => $id]);
        if ($sql) {
            if (!empty($_FILES['topic_img_url']['name'])) {
                $upload_file = $this->upload_image_topic('topic_img_url', $id);
                if ($upload_file['status'] == 0) {
                    $this->response([
                        'status' => false,
                        'message' => $upload_file['message']
                    ], 404);
                }
            }
            if (!empty($_FILES['committee_img_url']['name'])) {
                $upload_file = $this->upload_image_committee('committee_img_url', $id);
                if ($upload_file['status'] == 0) {
                    $this->response([
                        'status' => false,
                        'message' => $upload_file['message']
                    ], 404);
                }
            }
            $last_data = $this->mCore->get_data('paper_program_details', ['id' => $id])->row_array();
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
        $sql = $this->mCore->save_data('paper_program_details', $data, true, ['id' => $id]);
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

    // UPLOAD IMAGE
    public function upload_image_topic($topic_img_url, $id)
    {
        $this->load->library('ftp');

        $data = $this->mCore->get_data('paper_program_details', 'id = ' . $id)->row_array();
        if ($data['topic_img_url'] != '') {
            $exp = (explode('/', $data['topic_img_url']));
            $temp_img = end($exp);

            //FTP configuration
            $ftp_config['hostname'] = config_item('hostname_upload');
            $ftp_config['username'] = config_item('username_upload');
            $ftp_config['password'] = config_item('password_upload');
            $ftp_config['port'] = config_item('port_upload');
            $ftp_config['debug'] = TRUE;

            $this->ftp->connect($ftp_config);

            $this->ftp->delete_file('program/' . $data['program_id'] . '/others/' . $temp_img);

            $this->ftp->close();
        }

        $config['upload_path'] = './uploads';
        $config['allowed_types'] = 'gif|jpg|png|jpeg';
        $config['max_size'] = 5000;
        $config['file_name'] = 'topic_'.time();

        $this->load->library('upload', $config);
        $this->upload->initialize($config);
        if ($this->upload->do_upload($topic_img_url)) {

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

            if ($this->ftp->list_files('program/' . $data['program_id'] . '/others/') == FALSE) {
                $this->ftp->mkdir('program/' . $data['program_id'] . '/others/', DIR_WRITE_MODE, true);
            }

            $destination = 'program/' . $data['program_id'] . '/others/' . $fileName;

            $this->ftp->upload($source, $destination);

            $this->ftp->close();

            //Delete file from local server
            @unlink($source);

            $sql = $this->mCore->save_data('paper_program_details', ['topic_img_url' => config_item('dir_upload') . 'program/' . $data['program_id'] . '/others/' . $fileName], true, array('id' => $id));

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
    
    // UPLOAD IMAGE
    public function upload_image_committee($committee_img_url, $id)
    {
        $this->load->library('ftp');

        $data = $this->mCore->get_data('paper_program_details', 'id = ' . $id)->row_array();
        if ($data['committee_img_url'] != '') {
            $exp = (explode('/', $data['committee_img_url']));
            $temp_img = end($exp);

            //FTP configuration
            $ftp_config['hostname'] = config_item('hostname_upload');
            $ftp_config['username'] = config_item('username_upload');
            $ftp_config['password'] = config_item('password_upload');
            $ftp_config['port'] = config_item('port_upload');
            $ftp_config['debug'] = TRUE;

            $this->ftp->connect($ftp_config);

            $this->ftp->delete_file('program/' . $data['program_id'] . '/others/' . $temp_img);

            $this->ftp->close();
        }

        $config['upload_path'] = './uploads';
        $config['allowed_types'] = 'gif|jpg|png|jpeg';
        $config['max_size'] = 5000;
        $config['file_name'] = 'committee_'.time();

        $this->load->library('upload', $config);
        $this->upload->initialize($config);
        if ($this->upload->do_upload($committee_img_url)) {

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

            if ($this->ftp->list_files('program/' . $data['program_id'] . '/others/') == FALSE) {
                $this->ftp->mkdir('program/' . $data['program_id'] . '/others/', DIR_WRITE_MODE, true);
            }

            $destination = 'program/' . $data['program_id'] . '/others/' . $fileName;

            $this->ftp->upload($source, $destination);

            $this->ftp->close();

            //Delete file from local server
            @unlink($source);

            $sql = $this->mCore->save_data('paper_program_details', ['committee_img_url' =>  config_item('dir_upload') . 'program/' . $data['program_id'] . '/others/' . $fileName], true, array('id' => $id));

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
}
