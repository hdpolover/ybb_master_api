<?php

defined('BASEPATH') or exit('No direct script access allowed');

use chriskacerguis\RestServer\RestController;

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST');
header("Access-Control-Allow-Headers: X-Requested-With");

class Papers extends RestController
{

    function __construct()
    {
        parent::__construct();
    }

    function index_get()
    {
        $id = $this->get('id');
        if ($id == '') {
            $papers = $this->mCore->get_data('papers', ['is_active' => 1])->result_array();
            if ($papers) {
                $this->response([
                    'status' => true,
                    'data' => $papers
                ], 200);
            } else {
                $this->response([
                    'status' => false,
                    'message' => 'No result were found'
                ], 404);
            }
        } else {
            $papers = $this->mCore->get_data('papers', ['id' => $id, 'is_active' => 1])->row_array();
            if ($papers) {
                $this->response([
                    'status' => true,
                    'data' => $papers
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
        $paper_topic_id = $this->get('paper_topic_id');
        $papers = $this->mCore->get_data('papers', ['paper_topic_id' => $paper_topic_id, 'is_active' => 1])->result_array();
        if ($papers) {
            $this->response([
                'status' => true,
                'data' => $papers
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
            'paper_url' => NULL,
            'desc' => $this->post('desc'),
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        );
        $sql = $this->mCore->save_data('papers', array_filter($data));
        if ($sql) {
            $last_id = $this->mCore->get_lastid('papers', 'id');
            if (!empty($_FILES['paper_url']['name'])) {
                $upload_file = $this->upload_paper('paper_url', $last_id);
                if ($upload_file['status'] == 0) {
                    $this->response([
                        'status' => false,
                        'message' => $upload_file['message']
                    ], 404);
                }
            }
            $last_data = $this->mCore->get_data('papers', ['id' => $last_id])->row_array();
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
            'paper_url' => NULL,
            'desc' => $this->post('desc'),
            'updated_at' => date('Y-m-d H:i:s'),
        );
        $sql = $this->mCore->save_data('papers', array_filter($data), true, ['id' => $id]);
        if ($sql) {
            if (!empty($_FILES['paper_url']['name'])) {
                $upload_file = $this->upload_paper('paper_url', $id);
                if ($upload_file['status'] == 0) {
                    $this->response([
                        'status' => false,
                        'message' => $upload_file['message'],
                    ], 404);
                }
            }
            $last_data = $this->mCore->get_data('papers', ['id' => $id])->row_array();
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
        $sql = $this->mCore->save_data('papers', $data, true, ['id' => $id]);
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

    // UPLOAD PAPER
    public function upload_paper($paper_url, $id)
    {
        $this->load->library('ftp');

        $opt = [
            'select' => 'papers.*, programs.id',
            'table' => 'papers',
            'join' => [
                ['paper_details' => 'papers.id = paper_details.paper_id'],
                ['programs' => 'paper_details.program_id = programs.id'],
            ],
            'where' => ['papers.id' => $id]
        ];
        $data = $this->mCore->join_table($opt)->row_array();

        if ($data['paper_url'] != '') {
            $exp = (explode('/', $data['paper_url']));
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
        $config['allowed_types'] = '*';
        $config['max_size'] = 5000;
        $config['file_name'] = 'papers_'.time();

        $this->load->library('upload', $config);
        $this->upload->initialize($config);
        if ($this->upload->do_upload($paper_url)) {

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

            $sql = $this->mCore->save_data('papers', ['paper_url' => config_item('dir_upload') . 'program/' . $data['program_id'] . '/others/' . $fileName], true, array('id' => $id));

            if ($sql) {
                $data['status'] = 1;
                $data['message'] = 'Paper saved successfully';
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
