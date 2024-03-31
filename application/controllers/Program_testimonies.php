<?php

defined('BASEPATH') or exit('No direct script access allowed');

use chriskacerguis\RestServer\RestController;

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, PUT, PATCH, POST, DELETE');
header("Access-Control-Allow-Headers: X-Requested-With");

class Program_testimonies extends RestController
{

    function __construct()
    {
        parent::__construct();
    }

    function index_get()
    {
        $id = $this->get('id');
        if ($id == '') {
            $program_testimonies = $this->mCore->list_data('program_testimonies')->result_array();
            if ($program_testimonies) {
                $this->response([
                    'status' => true,
                    'data' => $program_testimonies
                ], 200);
            } else {
                $this->response([
                    'status' => false,
                    'message' => 'No result were found'
                ], 404);
            }
        } else {
            $program_testimonies = $this->mCore->get_data('program_testimonies', ['id' => $id])->row_array();
            if ($program_testimonies) {
                $this->response([
                    'status' => true,
                    'data' => $program_testimonies
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
            'program_id' => $this->post('program_id'),
            'person_name' => $this->post('person_name'),
            'testimony' => $this->post('testimony'),
            'occupation' => $this->post('occupation'),
            'img_url' => NULL,
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        );
        $sql = $this->mCore->save_data('program_testimonies', $data);
        if ($sql) {
            $last_id = $this->mCore->get_lastid('program_testimonies', 'id');
            $last_data = $this->mCore->get_data('program_testimonies', ['id' => $last_id])->row_array();
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
    function update_put()
    {
        $id = $this->put('id');
        $data = array(
            'person_name' => $this->post('person_name'),
            'testimony' => $this->post('testimony'),
            'occupation' => $this->post('occupation'),
            'updated_at' => date('Y-m-d H:i:s'),
        );
        $sql = $this->mCore->save_data('program_testimonies', $data, true, ['id' => $id]);
        if ($sql) {
            $last_data = $this->mCore->get_data('program_testimonies', ['id' => $id])->row_array();
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
        $sql = $this->mCore->save_data('program_testimonies', $data, true, ['id' => $id]);
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
    
    public function do_upload_image_post()
    {

        $this->load->library('ftp');

        $id = $this->post('id');

        $data = $this->mCore->get_data('program_testimonies', 'id = ' . $id)->row_array();
        if ($data['img_url'] != '') {
            $exp = (explode('/', $data['img_url']));
            $temp_img = end($exp);

            //FTP configuration
            $ftp_config['hostname'] = config_item('hostname_upload');
            $ftp_config['username'] = config_item('username_upload');
            $ftp_config['password'] = config_item('password_upload');
            $ftp_config['port'] = config_item('port_upload');
            $ftp_config['debug'] = TRUE;

            $this->ftp->connect($ftp_config);

            $this->ftp->delete_file('testimonies/' . $data['program_id'] . '/'. $temp_img);

            $this->ftp->close();
        }

        $config['upload_path'] = './uploads';
        $config['allowed_types'] = 'gif|jpg|png|jpeg';
        $config['max_size'] = 5000;
        $config['file_name'] = time();

        $this->load->library('upload', $config);
        $this->upload->initialize($config);
        if ($this->upload->do_upload("image")) {

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

            if ($this->ftp->list_files('testimonies/' . $data['program_id'] . '/') == FALSE) {
                $this->ftp->mkdir('testimonies/' . $data['program_id'] . '/', DIR_WRITE_MODE);
            }

            $destination = 'testimonies/' . $data['program_id'] . '/' . $fileName;

            $this->ftp->upload($source, $destination);

            $this->ftp->close();

            //Delete file from local server
            @unlink($source);

            $sql = $this->mCore->save_data('program_testimonies', ['img_url' => config_item('dir_upload') . 'testimonies/' . $data['program_id'] . '/' . $fileName], true, array('id' => $id));

            if ($sql) {
                $this->response([
                    'status' => true,
                    'message' => 'Image saved successfully'
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
}
?>