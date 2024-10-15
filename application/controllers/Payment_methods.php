<?php

defined('BASEPATH') or exit('No direct script access allowed');

use chriskacerguis\RestServer\RestController;

class Payment_methods extends RestController
{

    public function __construct()
    {
        parent::__construct();
    }

    public function index_get()
    {
        $id = $this->get('id');
        if ($id == '') {
            $payment_methods = $this->mCore->get_data('payment_methods', ['is_active' => 1])->result_array();
            if ($payment_methods) {
                $this->response([
                    'status' => true,
                    'data' => $payment_methods,
                ], 200);
            } else {
                $this->response([
                    'status' => false,
                    'message' => 'No result were found',
                ], 404);
            }
        } else {
            $payment_methods = $this->mCore->get_data('payment_methods', ['id' => $id, 'is_active' => 1])->row_array();
            if ($payment_methods) {
                $this->response([
                    'status' => true,
                    'data' => $payment_methods,
                ], 200);
            } else {
                $this->response([
                    'status' => false,
                    'message' => 'No result were found',
                ], 404);
            }
        }
    }

    public function list_get()
    {
        $program_id = $this->get('program_id');

        $payment_methods = $this->mCore->get_data('payment_methods', ['program_id' => $program_id, 'is_active' => 1])->result_array();
        if ($payment_methods) {
            $this->response([
                'status' => true,
                'data' => $payment_methods,
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
        $data = array(
            'program_id' => $this->post('program_id'),
            'name' => $this->post('name'),
            'description' => $this->post('description'),
            'type' => $this->post('type'),
            'img_url' => null,
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        );
        $sql = $this->mCore->save_data('payment_methods', array_filter($data));
        if ($sql) {
            $last_id = $this->mCore->get_lastid('payment_methods', 'id');
            if (!empty($_FILES['img_url']['name'])) {
                $upload_file = $this->upload_image('img_url', $last_id);
                if ($upload_file['status'] == 0) {
                    $this->response([
                        'status' => false,
                        'message' => $upload_file['message'],
                    ], 404);
                }
            }
            $last_data = $this->mCore->get_data('payment_methods', ['id' => $last_id])->row_array();
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
            'name' => $this->post('name'),
            'description' => $this->post('description'),
            'type' => $this->post('type'),
            'img_url' => null,
            'updated_at' => date('Y-m-d H:i:s'),
        );
        $sql = $this->mCore->save_data('payment_methods', array_filter($data), true, ['id' => $id]);
        if ($sql) {
            if (!empty($_FILES['img_url']['name'])) {
                $upload_file = $this->upload_image('img_url', $id);
                if ($upload_file['status'] == 0) {
                    $this->response([
                        'status' => false,
                        'message' => $upload_file['message'],
                    ], 404);
                }
            }
            $last_data = $this->mCore->get_data('payment_methods', ['id' => $id])->row_array();
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
        $sql = $this->mCore->save_data('payment_methods', $data, true, ['id' => $id]);
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

    // UPLOAD IMAGE
    public function upload_image($img_url, $id)
    {
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Methods: GET, PUT, PATCH, POST, DELETE');
        header("Access-Control-Allow-Headers: X-Requested-With");
        
        $this->load->library('ftp');

        $data = $this->mCore->get_data('payment_methods', 'id = ' . $id)->row_array();
        if ($data['img_url'] != '') {
            $exp = (explode('/', $data['img_url']));
            $temp_img = end($exp);

            //FTP configuration
            $ftp_config['hostname'] = config_item('hostname_upload');
            $ftp_config['username'] = config_item('username_upload');
            $ftp_config['password'] = config_item('password_upload');
            $ftp_config['port'] = config_item('port_upload');
            $ftp_config['debug'] = true;

            $this->ftp->connect($ftp_config);

            $this->ftp->delete_file('payment-methods/' . $temp_img);

            $this->ftp->close();
        }

        $config['upload_path'] = './uploads';
        $config['allowed_types'] = 'gif|jpg|png|jpeg';
        $config['max_size'] = 5000;
        $config['file_name'] = time();

        $this->load->library('upload', $config);
        $this->upload->initialize($config);
        if ($this->upload->do_upload($img_url)) {

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

            $destination = 'payment-methods/' . $fileName;

            $this->ftp->upload($source, $destination);

            $this->ftp->close();

            //Delete file from local server
            @unlink($source);

            $sql = $this->mCore->save_data('payment_methods', ['img_url' => config_item('dir_upload') . 'payment-methods/' . $fileName], true, array('id' => $id));

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

    // UPLOAD IMAGE DIRECT
    public function do_upload_image_post()
    {
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Methods: GET, PUT, PATCH, POST, DELETE');
        header("Access-Control-Allow-Headers: X-Requested-With");
        
        $this->load->library('ftp');

        $id = $this->post('id');

        $data = $this->mCore->get_data('payment_methods', 'id = ' . $id)->row_array();
        if ($data['img_url'] != '') {
            $exp = (explode('/', $data['img_url']));
            $temp_img = end($exp);

            //FTP configuration
            $ftp_config['hostname'] = config_item('hostname_upload');
            $ftp_config['username'] = config_item('username_upload');
            $ftp_config['password'] = config_item('password_upload');
            $ftp_config['port'] = config_item('port_upload');
            $ftp_config['debug'] = true;

            $this->ftp->connect($ftp_config);

            $this->ftp->delete_file('payment-methods/' . $temp_img);

            $this->ftp->close();
        }

        $config['upload_path'] = './uploads';
        $config['allowed_types'] = 'gif|jpg|png|jpeg';
        $config['max_size'] = 5000;
        $config['file_name'] = $id;

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
            $ftp_config['debug'] = true;

            $this->ftp->connect($ftp_config);

            $destination = 'payment-methods/' . $fileName;

            $this->ftp->upload($source, $destination);

            $this->ftp->close();

            //Delete file from local server
            @unlink($source);

            $sql = $this->mCore->save_data('payment_methods', ['img_url' => config_item('dir_upload') . 'payment-methods/' . $fileName], true, array('id' => $id));

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
