<?php

defined('BASEPATH') or exit('No direct script access allowed');

use chriskacerguis\RestServer\RestController;

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, PUT, PATCH, POST, DELETE');
header("Access-Control-Allow-Headers: X-Requested-With");

class Programs extends RestController
{

    function __construct()
    {
        parent::__construct();
    }

    function index_get()
    {
        $id = $this->get('id');
        if ($id == '') {
            $programs = $this->mCore->list_data('programs')->result_array();
            if ($programs) {
                $this->response([
                    'status' => true,
                    'data' => $programs
                ], 200);
            } else {
                $this->response([
                    'status' => false,
                    'message' => 'No result were found'
                ], 404);
            }
        } else {
            $programs = $this->mCore->get_data('programs', ['id' => $id])->row_array();
            if ($programs) {
                $this->response([
                    'status' => true,
                    'data' => $programs
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
            'program_category_id' => $this->post('program_category_id'),
            'name' => $this->post('name'),
            'logo_url' => NULL,
            'description' => $this->post('description'),
            'guideline' => $this->post('guideline'),
            'twibbon' => $this->post('twibbon'),
            'start_date' => $this->post('start_date'),
            'end_date' => $this->post('end_date'),
            'registration_video_url' => $this->post('registration_video_url'),
            'sponsor_canva_url' => $this->post('sponsor_canva_url'),
            'theme' => $this->post('theme'),
            'sub_themes' => $this->post('sub_themes'),
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        );
        $sql = $this->mCore->save_data('programs', $data);
        if ($sql) {
            $last_id = $this->mCore->get_lastid('programs', 'id');
            $last_data = $this->mCore->get_data('programs', ['id' => $last_id])->row_array();
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
            'program_category_id' => $this->post('program_category_id'),
            'name' => $this->post('name'),
            'description' => $this->post('description'),
            'guideline' => $this->post('guideline'),
            'twibbon' => $this->post('twibbon'),
            'start_date' => $this->post('start_date'),
            'end_date' => $this->post('end_date'),
            'registration_video_url' => $this->post('registration_video_url'),
            'sponsor_canva_url' => $this->post('sponsor_canva_url'),
            'updated_at' => date('Y-m-d H:i:s'),
        );
        $sql = $this->mCore->save_data('programs', $data, true, ['id' => $id]);
        if ($sql) {
            $last_data = $this->mCore->get_data('programs', ['id' => $id])->row_array();
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
        $sql = $this->mCore->save_data('programs', $data, true, ['id' => $id]);
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

    // UPLOAD LOGO
    public function do_upload_logo_post()
    {

        $this->load->library('ftp');

        $id = $this->post('id');

        $data = $this->mCore->get_data('programs', 'id = ' . $id)->row_array();
        if ($data['logo_url'] != '') {
            $exp = (explode('/', $data['logo_url']));
            $temp_img = end($exp);

            //FTP configuration
            $ftp_config['hostname'] = config_item('hostname_upload');
            $ftp_config['username'] = config_item('username_upload');
            $ftp_config['password'] = config_item('password_upload');
            $ftp_config['port'] = config_item('port_upload');
            $ftp_config['debug'] = TRUE;

            $this->ftp->connect($ftp_config);

            $this->ftp->delete_file('programs/' . $id . '/' . $temp_img);

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
            $ftp_config['debug'] = TRUE;

            $this->ftp->connect($ftp_config);

            if ($this->ftp->list_files('programs/' . $id . '/') == FALSE) {
                $this->ftp->mkdir('programs/' . $id . '/', DIR_WRITE_MODE);
            }

            $destination = 'programs/' . $id . '/' . $fileName;

            $this->ftp->upload($source, $destination);

            $this->ftp->close();

            //Delete file from local server
            @unlink($source);

            $sql = $this->mCore->save_data('programs', ['logo_url' => config_item('dir_upload') . 'programs/' . $id . '/' . $fileName], true, array('id' => $id));

            if ($sql) {
                $this->response([
                    'status' => true,
                    'message' => 'Logo saved successfully'
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
