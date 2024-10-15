<?php

defined('BASEPATH') or exit('No direct script access allowed');

use chriskacerguis\RestServer\RestController;

class Web_setting_home extends RestController
{

    function __construct()
    {
        parent::__construct();
    }

    function index_get()
    {
        $program_id = $this->get('program_id');
        if ($program_id == '') {
            $web_setting_home = $this->mCore->get_data('web_setting_home', ['is_active' => 1])->result_array();
            if ($web_setting_home) {
                $this->response([
                    'status' => true,
                    'data' => $web_setting_home
                ], 200);
            } else {
                $this->response([
                    'status' => false,
                    'message' => 'No result were found'
                ], 404);
            }
        } else {
            $web_setting_home = $this->mCore->get_data('web_setting_home', ['program_id' => $program_id, 'is_active' => 1])->result();
            if ($web_setting_home) {
                $this->response([
                    'status' => true,
                    'data' => $web_setting_home
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
            'page_name' => $this->post('page_name'),
            'menu_path' => $this->post('menu_path'),
            'banner1_img_url' => NULL,
            'banner1_mobile_img_url' => NULL,
            'banner1_title' => $this->post('banner1_title'),
            'banner1_description' => $this->post('banner1_description'),
            'banner1_date' => $this->post('banner1_date'),
            'banner2_img_url' => NULL,
            'banner2_mobile_img_url' => NULL,
            'banner2_title' => $this->post('banner2_title'),
            'banner2_description' => $this->post('banner2_description'),
            'banner2_date' => $this->post('banner2_date'),
            'summary' => $this->post('summary'),
            'reason' => $this->post('reason'),
            'agenda' => $this->post('agenda'),
            'introduction' => $this->post('introduction'),
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        );
        $sql = $this->mCore->save_data('web_setting_home', array_filter($data));
        if ($sql) {
            $last_id = $this->mCore->get_lastid('web_setting_home', 'id');
            if (!empty($_FILES['banner1_img_url']['name'])) {
                $upload_file = $this->upload_banner1('banner1_img_url', $last_id);
                if ($upload_file['status'] == 0) {
                    $this->response([
                        'status' => false,
                        'message' => $upload_file['message']
                    ], 404);
                }
            }
            if (!empty($_FILES['banner1_mobile_img_url']['name'])) {
                $upload_file = $this->upload_banner1_mobile('banner1_mobile_img_url', $last_id);
                if ($upload_file['status'] == 0) {
                    $this->response([
                        'status' => false,
                        'message' => $upload_file['message']
                    ], 404);
                }
            }
            if (!empty($_FILES['banner2_img_url']['name'])) {
                $upload_file = $this->upload_banner2('banner2_img_url', $last_id);
                if ($upload_file['status'] == 0) {
                    $this->response([
                        'status' => false,
                        'message' => $upload_file['message']
                    ], 404);
                }
            }
            if (!empty($_FILES['banner2_mobile_img_url']['name'])) {
                $upload_file = $this->upload_banner2_mobile('banner2_mobile_img_url', $last_id);
                if ($upload_file['status'] == 0) {
                    $this->response([
                        'status' => false,
                        'message' => $upload_file['message']
                    ], 404);
                }
            }
            $last_data = $this->mCore->get_data('web_setting_home', ['id' => $last_id])->row_array();
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
            'page_name' => $this->post('page_name'),
            'menu_path' => $this->post('menu_path'),
            'banner1_title' => $this->post('banner1_title'),
            'banner1_description' => $this->post('banner1_description'),
            'banner1_date' => $this->post('banner1_date'),
            'banner2_title' => $this->post('banner2_title'),
            'banner2_description' => $this->post('banner2_description'),
            'banner2_date' => $this->post('banner2_date'),
            'summary' => $this->post('summary'),
            'reason' => $this->post('reason'),
            'agenda' => $this->post('agenda'),
            'introduction' => $this->post('introduction'),
            'updated_at' => date('Y-m-d H:i:s'),
        );
        $sql = $this->mCore->save_data('web_setting_home', array_filter($data), true, ['id' => $id]);
        if ($sql) {
            $last_data = $this->mCore->get_data('web_setting_home', ['id' => $id])->row_array();
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
        $sql = $this->mCore->save_data('web_setting_home', $data, true, ['id' => $id]);
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

    // UPLOAD BANNER 1
    private function upload_banner1($banner1_img_url, $id)
    {

        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Methods: GET, PUT, PATCH, POST, DELETE');
        header("Access-Control-Allow-Headers: X-Requested-With");
        
        $this->load->library('ftp');

        $data = $this->mCore->get_data('web_setting_home', 'id = ' . $id)->row_array();
        if ($data['banner1_img_url'] != '') {
            $exp = (explode('/', $data['banner1_img_url']));
            $temp_img = end($exp);

            //FTP configuration
            $ftp_config['hostname'] = config_item('hostname_upload');
            $ftp_config['username'] = config_item('username_upload');
            $ftp_config['password'] = config_item('password_upload');
            $ftp_config['port'] = config_item('port_upload');
            $ftp_config['debug'] = TRUE;

            $this->ftp->connect($ftp_config);

            $this->ftp->delete_file('web-setting-home/' . $data['program_id'] . '/' . $temp_img);

            $this->ftp->close();
        }

        $config['upload_path'] = './uploads';
        $config['allowed_types'] = 'gif|jpg|png|jpeg';
        $config['max_size'] = 5000;
        $config['file_name'] = time();

        $this->load->library('upload', $config);
        $this->upload->initialize($config);
        if ($this->upload->do_upload($banner1_img_url)) {

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

            if ($this->ftp->list_files('web-setting-home/' . $data['program_id'] . '/') == FALSE) {
                $this->ftp->mkdir('web-setting-home/' . $data['program_id'] . '/', DIR_WRITE_MODE);
            }

            $destination = 'web-setting-home/' . $data['program_id'] . '/' . $fileName;

            $this->ftp->upload($source, $destination);

            $this->ftp->close();

            //Delete file from local server
            @unlink($source);

            $sql = $this->mCore->save_data('web_setting_home', ['banner1_img_url ' => config_item('dir_upload') . 'web-setting-home/' . $data['program_id'] . '/' . $fileName], true, array('id' => $id));

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

    // UPLOAD BANNER 1 DIRECT
    public function do_upload_banner1_post()
    {
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Methods: GET, PUT, PATCH, POST, DELETE');
        header("Access-Control-Allow-Headers: X-Requested-With");        

        $this->load->library('ftp');

        $id = $this->post('id');

        $data = $this->mCore->get_data('web_setting_home', 'id = ' . $id)->row_array();
        if ($data['banner1_img_url'] != '') {
            $exp = (explode('/', $data['banner1_img_url']));
            $temp_img = end($exp);

            //FTP configuration
            $ftp_config['hostname'] = config_item('hostname_upload');
            $ftp_config['username'] = config_item('username_upload');
            $ftp_config['password'] = config_item('password_upload');
            $ftp_config['port'] = config_item('port_upload');
            $ftp_config['debug'] = TRUE;

            $this->ftp->connect($ftp_config);

            $this->ftp->delete_file('web-setting-home/' . $data['program_id'] . '/' . $temp_img);

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

            if ($this->ftp->list_files('web-setting-home/' . $data['program_id'] . '/') == FALSE) {
                $this->ftp->mkdir('web-setting-home/' . $data['program_id'] . '/', DIR_WRITE_MODE);
            }

            $destination = 'web-setting-home/' . $data['program_id'] . '/' . $fileName;

            $this->ftp->upload($source, $destination);

            $this->ftp->close();

            //Delete file from local server
            @unlink($source);

            $sql = $this->mCore->save_data('web_setting_home', ['banner1_img_url ' => config_item('dir_upload') . 'web-setting-home/' . $data['program_id'] . '/' . $fileName], true, array('id' => $id));

            if ($sql) {
                $this->response([
                    'status' => true,
                    'message' => 'Banner saved successfully'
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

    // UPLOAD BANNER MOBILE 1
    private function upload_banner1_mobile($banner1_mobile_img_url, $id)
    {

        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Methods: GET, PUT, PATCH, POST, DELETE');
        header("Access-Control-Allow-Headers: X-Requested-With");
        
        $this->load->library('ftp');

        $data = $this->mCore->get_data('web_setting_home', 'id = ' . $id)->row_array();
        if ($data['banner1_mobile_img_url'] != '') {
            $exp = (explode('/', $data['banner1_mobile_img_url']));
            $temp_img = end($exp);

            //FTP configuration
            $ftp_config['hostname'] = config_item('hostname_upload');
            $ftp_config['username'] = config_item('username_upload');
            $ftp_config['password'] = config_item('password_upload');
            $ftp_config['port'] = config_item('port_upload');
            $ftp_config['debug'] = TRUE;

            $this->ftp->connect($ftp_config);

            $this->ftp->delete_file('web-setting-home/' . $data['program_id'] . '/' . $temp_img);

            $this->ftp->close();
        }

        $config['upload_path'] = './uploads';
        $config['allowed_types'] = 'gif|jpg|png|jpeg';
        $config['max_size'] = 5000;
        $config['file_name'] = time();

        $this->load->library('upload', $config);
        $this->upload->initialize($config);
        if ($this->upload->do_upload($banner1_mobile_img_url)) {

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

            if ($this->ftp->list_files('web-setting-home/' . $data['program_id'] . '/') == FALSE) {
                $this->ftp->mkdir('web-setting-home/' . $data['program_id'] . '/', DIR_WRITE_MODE);
            }

            $destination = 'web-setting-home/' . $data['program_id'] . '/' . $fileName;

            $this->ftp->upload($source, $destination);

            $this->ftp->close();

            //Delete file from local server
            @unlink($source);

            $sql = $this->mCore->save_data('web_setting_home', ['banner1_mobile_img_url ' => config_item('dir_upload') . 'web-setting-home/' . $data['program_id'] . '/' . $fileName], true, array('id' => $id));

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

    // UPLOAD BANNER MOBILE 1 DIRECT
    public function do_upload_banner1_mobile_post()
    {

        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Methods: GET, PUT, PATCH, POST, DELETE');
        header("Access-Control-Allow-Headers: X-Requested-With");        

        $this->load->library('ftp');

        $id = $this->post('id');

        $data = $this->mCore->get_data('web_setting_home', 'id = ' . $id)->row_array();
        if ($data['banner1_mobile_img_url'] != '') {
            $exp = (explode('/', $data['banner1_mobile_img_url']));
            $temp_img = end($exp);

            //FTP configuration
            $ftp_config['hostname'] = config_item('hostname_upload');
            $ftp_config['username'] = config_item('username_upload');
            $ftp_config['password'] = config_item('password_upload');
            $ftp_config['port'] = config_item('port_upload');
            $ftp_config['debug'] = TRUE;

            $this->ftp->connect($ftp_config);

            $this->ftp->delete_file('web-setting-home/' . $data['program_id'] . '/' . $temp_img);

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

            if ($this->ftp->list_files('web-setting-home/' . $data['program_id'] . '/') == FALSE) {
                $this->ftp->mkdir('web-setting-home/' . $data['program_id'] . '/', DIR_WRITE_MODE);
            }

            $destination = 'web-setting-home/' . $data['program_id'] . '/' . $fileName;

            $this->ftp->upload($source, $destination);

            $this->ftp->close();

            //Delete file from local server
            @unlink($source);

            $sql = $this->mCore->save_data('web_setting_home', ['banner1_mobile_img_url ' => config_item('dir_upload') . 'web-setting-home/' . $data['program_id'] . '/' . $fileName], true, array('id' => $id));

            if ($sql) {
                $this->response([
                    'status' => true,
                    'message' => 'Banner mobile saved successfully'
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

    // UPLOAD BANNER 2
    private function upload_banner2($banner2_img_url, $id)
    {

        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Methods: GET, PUT, PATCH, POST, DELETE');
        header("Access-Control-Allow-Headers: X-Requested-With");        

        $this->load->library('ftp');

        $data = $this->mCore->get_data('web_setting_home', 'id = ' . $id)->row_array();
        if ($data['banner2_img_url'] != '') {
            $exp = (explode('/', $data['banner2_img_url']));
            $temp_img = end($exp);

            //FTP configuration
            $ftp_config['hostname'] = config_item('hostname_upload');
            $ftp_config['username'] = config_item('username_upload');
            $ftp_config['password'] = config_item('password_upload');
            $ftp_config['port'] = config_item('port_upload');
            $ftp_config['debug'] = TRUE;

            $this->ftp->connect($ftp_config);

            $this->ftp->delete_file('web-setting-home/' . $data['program_id'] . '/' . $temp_img);

            $this->ftp->close();
        }

        $config['upload_path'] = './uploads';
        $config['allowed_types'] = 'gif|jpg|png|jpeg';
        $config['max_size'] = 5000;
        $config['file_name'] = time();

        $this->load->library('upload', $config);
        $this->upload->initialize($config);
        if ($this->upload->do_upload($banner2_img_url)) {

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

            if ($this->ftp->list_files('web-setting-home/' . $data['program_id'] . '/') == FALSE) {
                $this->ftp->mkdir('web-setting-home/' . $data['program_id'] . '/', DIR_WRITE_MODE);
            }

            $destination = 'web-setting-home/' . $data['program_id'] . '/' . $fileName;

            $this->ftp->upload($source, $destination);

            $this->ftp->close();

            //Delete file from local server
            @unlink($source);

            $sql = $this->mCore->save_data('web_setting_home', ['banner2_img_url ' => config_item('dir_upload') . 'web-setting-home/' . $data['program_id'] . '/' . $fileName], true, array('id' => $id));

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

    // UPLOAD BANNER 2 DIRECT
    public function do_upload_banner2_post()
    {
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Methods: GET, PUT, PATCH, POST, DELETE');
        header("Access-Control-Allow-Headers: X-Requested-With");        

        $this->load->library('ftp');

        $id = $this->post('id');

        $data = $this->mCore->get_data('web_setting_home', 'id = ' . $id)->row_array();
        if ($data['banner2_img_url'] != '') {
            $exp = (explode('/', $data['banner2_img_url']));
            $temp_img = end($exp);

            //FTP configuration
            $ftp_config['hostname'] = config_item('hostname_upload');
            $ftp_config['username'] = config_item('username_upload');
            $ftp_config['password'] = config_item('password_upload');
            $ftp_config['port'] = config_item('port_upload');
            $ftp_config['debug'] = TRUE;

            $this->ftp->connect($ftp_config);

            $this->ftp->delete_file('web-setting-home/' . $data['program_id'] . '/' . $temp_img);

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

            if ($this->ftp->list_files('web-setting-home/' . $data['program_id'] . '/') == FALSE) {
                $this->ftp->mkdir('web-setting-home/' . $data['program_id'] . '/', DIR_WRITE_MODE);
            }

            $destination = 'web-setting-home/' . $data['program_id'] . '/' . $fileName;

            $this->ftp->upload($source, $destination);

            $this->ftp->close();

            //Delete file from local server
            @unlink($source);

            $sql = $this->mCore->save_data('web_setting_home', ['banner2_img_url ' => config_item('dir_upload') . 'web-setting-home/' . $data['program_id'] . '/' . $fileName], true, array('id' => $id));

            if ($sql) {
                $this->response([
                    'status' => true,
                    'message' => 'Banner saved successfully'
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

    // UPLOAD BANNER MOBILE 2
    private function upload_banner2_mobile($banner2_mobile_img_url, $id)
    {

        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Methods: GET, PUT, PATCH, POST, DELETE');
        header("Access-Control-Allow-Headers: X-Requested-With");        

        $this->load->library('ftp');

        $data = $this->mCore->get_data('web_setting_home', 'id = ' . $id)->row_array();
        if ($data['banner2_mobile_img_url'] != '') {
            $exp = (explode('/', $data['banner2_mobile_img_url']));
            $temp_img = end($exp);

            //FTP configuration
            $ftp_config['hostname'] = config_item('hostname_upload');
            $ftp_config['username'] = config_item('username_upload');
            $ftp_config['password'] = config_item('password_upload');
            $ftp_config['port'] = config_item('port_upload');
            $ftp_config['debug'] = TRUE;

            $this->ftp->connect($ftp_config);

            $this->ftp->delete_file('web-setting-home/' . $data['program_id'] . '/' . $temp_img);

            $this->ftp->close();
        }

        $config['upload_path'] = './uploads';
        $config['allowed_types'] = 'gif|jpg|png|jpeg';
        $config['max_size'] = 5000;
        $config['file_name'] = time();

        $this->load->library('upload', $config);
        $this->upload->initialize($config);
        if ($this->upload->do_upload($banner2_mobile_img_url)) {

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

            if ($this->ftp->list_files('web-setting-home/' . $data['program_id'] . '/') == FALSE) {
                $this->ftp->mkdir('web-setting-home/' . $data['program_id'] . '/', DIR_WRITE_MODE);
            }

            $destination = 'web-setting-home/' . $data['program_id'] . '/' . $fileName;

            $this->ftp->upload($source, $destination);

            $this->ftp->close();

            //Delete file from local server
            @unlink($source);

            $sql = $this->mCore->save_data('web_setting_home', ['banner2_mobile_img_url ' => config_item('dir_upload') . 'web-setting-home/' . $data['program_id'] . '/' . $fileName], true, array('id' => $id));

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

    // UPLOAD BANNER MOBILE 2 DIRECT
    public function do_upload_banner2_mobile_post()
    {
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Methods: GET, PUT, PATCH, POST, DELETE');
        header("Access-Control-Allow-Headers: X-Requested-With");        

        $this->load->library('ftp');

        $id = $this->post('id');

        $data = $this->mCore->get_data('web_setting_home', 'id = ' . $id)->row_array();
        if ($data['banner2_mobile_img_url'] != '') {
            $exp = (explode('/', $data['banner2_mobile_img_url']));
            $temp_img = end($exp);

            //FTP configuration
            $ftp_config['hostname'] = config_item('hostname_upload');
            $ftp_config['username'] = config_item('username_upload');
            $ftp_config['password'] = config_item('password_upload');
            $ftp_config['port'] = config_item('port_upload');
            $ftp_config['debug'] = TRUE;

            $this->ftp->connect($ftp_config);

            $this->ftp->delete_file('web-setting-home/' . $data['program_id'] . '/' . $temp_img);

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

            if ($this->ftp->list_files('web-setting-home/' . $data['program_id'] . '/') == FALSE) {
                $this->ftp->mkdir('web-setting-home/' . $data['program_id'] . '/', DIR_WRITE_MODE);
            }

            $destination = 'web-setting-home/' . $data['program_id'] . '/' . $fileName;

            $this->ftp->upload($source, $destination);

            $this->ftp->close();

            //Delete file from local server
            @unlink($source);

            $sql = $this->mCore->save_data('web_setting_home', ['banner2_mobile_img_url ' => config_item('dir_upload') . 'web-setting-home/' . $data['program_id'] . '/' . $fileName], true, array('id' => $id));

            if ($sql) {
                $this->response([
                    'status' => true,
                    'message' => 'Banner mobile saved successfully'
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
