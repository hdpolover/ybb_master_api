<?php

defined('BASEPATH') or exit('No direct script access allowed');

use chriskacerguis\RestServer\RestController;

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, PUT, PATCH, POST, DELETE');
header("Access-Control-Allow-Headers: X-Requested-With");

class Program_certificates extends RestController
{

    function __construct()
    {
        parent::__construct();
    }

    function index_get()
    {
        $id = $this->get('id');
        if ($id == '') {
            $program_certificates = $this->mCore->get_data('program_certificates', ['is_active' => 1])->result_array();
            if ($program_certificates) {
                $this->response([
                    'status' => true,
                    'data' => $program_certificates
                ], 200);
            } else {
                $this->response([
                    'status' => false,
                    'message' => 'No result were found'
                ], 404);
            }
        } else {
            $program_certificates = $this->mCore->get_data('program_certificates', ['id' => $id, 'is_active' => 1])->row_array();
            if ($program_certificates) {
                $this->response([
                    'status' => true,
                    'data' => $program_certificates
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

        $program_certificates = $this->mCore->get_data('program_certificates', ['program_id' => $program_id, 'is_active' => 1])->result_array();
        if ($program_certificates) {
            $this->response([
                'status' => true,
                'data' => $program_certificates
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
        $data = array(
            'title' => $this->post('title'),
            'description' => $this->post('description'),
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        );
        $sql = $this->mCore->save_data('program_certificates', array_filter($data));
        if ($sql) {
            $last_id = $this->mCore->get_lastid('program_certificates', 'id');
            if (!empty($_FILES['template_url']['name'])) {
                $upload_file = $this->upload_certificate('template_url', $last_id);
                if ($upload_file['status'] == 0) {
                    $this->response([
                        'status' => false,
                        'message' => $upload_file['message']
                    ], 404);
                }
            }
            $last_data = $this->mCore->get_data('program_certificates', ['id' => $last_id])->row_array();
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
            'title' => $this->post('title'),
            'description' => $this->post('description'),
            'updated_at' => date('Y-m-d H:i:s'),
        );
        $sql = $this->mCore->save_data('program_certificates', array_filter($data), true, ['id' => $id]);
        if ($sql) {
            if (!empty($_FILES['template_url']['name'])) {
                $upload_file = $this->upload_certificate('template_url', $id);
                if ($upload_file['status'] == 0) {
                    $this->response([
                        'status' => false,
                        'message' => $upload_file['message']
                    ], 404);
                }
            }
            $last_data = $this->mCore->get_data('program_certificates', ['id' => $id])->row_array();
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
        $sql = $this->mCore->save_data('program_certificates', $data, true, ['id' => $id]);
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

    //UPLOAD
    public function upload_certificate($certificate_url, $id)
    {

        $this->load->library('ftp');

        $data = $this->mCore->get_data('program_certificates', 'id = ' . $id)->row_array();
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

            $this->ftp->delete_file('programs/' . $data['program_id'] . '/certificates/' . $temp_img);

            $this->ftp->close();
        }

        $config['upload_path'] = './uploads';
        $config['allowed_types'] = '*';
        $config['max_size'] = 5000;
        $config['file_name'] = time();

        $this->load->library('upload', $config);
        $this->upload->initialize($config);
        if ($this->upload->do_upload($certificate_url)) {

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

            if ($this->ftp->list_files('programs/' . $data['program_id'] . '/certificates/') == FALSE) {
                $this->ftp->mkdir('programs/' . $data['program_id'] . '/certificates/', DIR_WRITE_MODE);
            }

            $destination = 'programs/' . $data['program_id'] . '/certificates/' . $fileName;

            $this->ftp->upload($source, $destination);

            $this->ftp->close();

            //Delete file from local server
            @unlink($source);

            $sql = $this->mCore->save_data('program_certificates', ['template_url' => config_item('dir_upload') . 'programs/' . $data['program_id'] . '/certificates/' . $fileName], true, array('id' => $id));

            if ($sql) {
                $data['status'] = 1;
                $data['message'] = 'Certificates saved successfully';
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

    // UPLOAD CERTIFICATES
    public function do_upload_certificate_post()
    {

        $this->load->library('ftp');

        $id = $this->post('id');
        $program_id = $this->post('program_id');

        $data = $this->mCore->get_data('program_certificates', 'id = ' . $id)->row_array();
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

            $this->ftp->delete_file('program_certificates/' . $program_id . '/' . $temp_img);

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

            if ($this->ftp->list_files('program_certificates/' . $program_id . '/') == FALSE) {
                $this->ftp->mkdir('program_certificates/' . $program_id . '/', DIR_WRITE_MODE);
            }

            $destination = 'program_certificates/' . $program_id . '/' . $fileName;

            $this->ftp->upload($source, $destination);

            $this->ftp->close();

            //Delete file from local server
            @unlink($source);

            $sql = $this->mCore->save_data('program_certificates', ['template_url' => config_item('dir_upload') . 'program_certificates/' . $program_id . '/' . $fileName], true, array('id' => $id));

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

    public function addTextImage_get()
    {
        $nama = "RONALD";
        $file_name = "email_new.png";
        try {
            // Create a new SimpleImage object
            $image = new \claviska\SimpleImage();

            $image
                ->fromFile('assets/img/email.png')                     // load image.jpg
                ->autoOrient()                              // adjust orientation based on exif data
                ->text(
                    $nama,
                    array(
                        'fontFile' => realpath('font.ttf'),
                        'size' => 20,
                        'anchor' => 'top',
                        'xOffset' => 330,
                        'yOffset' => 213,
                    )
                )
                ->toFile('assets/img/' . $file_name, 'image/png');
            //->toScreen();                               // output to the screen

            return $file_name;
            // And much more! 💪
        } catch (Exception $err) {
            // Handle errors
            echo $err->getMessage();
        }
    }
}
