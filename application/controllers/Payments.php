<?php

defined('BASEPATH') or exit('No direct script access allowed');

use chriskacerguis\RestServer\RestController;

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, PUT, PATCH, POST, DELETE');
header("Access-Control-Allow-Headers: X-Requested-With");

class payments extends RestController
{

    function __construct()
    {
        parent::__construct();
    }

    function index_get()
    {
        $id = $this->get('id');
        if ($id == '') {
            $payments = $this->mCore->list_data('payments')->result_array();
            if ($payments) {
                $this->response([
                    'status' => true,
                    'data' => $payments
                ], 200);
            } else {
                $this->response([
                    'status' => false,
                    'message' => 'No result were found'
                ], 404);
            }
        } else {
            $payments = $this->mCore->get_data('payments', ['id' => $id])->row();
            if ($payments) {
                $this->response([
                    'status' => true,
                    'data' => $payments
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
        $participant_id = $this->get('participant_id');

        $payments = $this->mCore->get_data('payments', ['participant_id' => $participant_id])->result_array();
        if ($payments) {
            $this->response([
                'status' => true,
                'data' => $payments
            ], 200);
        } else {
            $this->response([
                'status' => false,
                'message' => 'No result were found'
            ], 404);
        }
    }

    function list_payment_get()
    {
        $program_payment_id = $this->get('program_payment_id');

        $payments = $this->mCore->get_data('payments', ['program_payment_id' => $program_payment_id])->result_array();
        if ($payments) {
            $this->response([
                'status' => true,
                'data' => $payments
            ], 200);
        } else {
            $this->response([
                'status' => false,
                'message' => 'No result were found'
            ], 404);
        }
    }

    function list_method_get()
    {
        $payment_method_id = $this->get('payment_method_id');

        $payments = $this->mCore->get_data('payments', ['payment_method_id' => $payment_method_id])->result_array();
        if ($payments) {
            $this->response([
                'status' => true,
                'data' => $payments
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
            'participant_id' => $this->post('participant_id'),
            'program_payment_id' => $this->post('program_payment_id'),
            'payment_method_id' => $this->post('payment_method_id'),
            'status' => $this->post('status'),
            'proof_url' => NULL,
            'account_name' => $this->post('account_name'),
            'amount' => $this->post('amount'),
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        );
        $sql = $this->mCore->save_data('payments', array_filter($data));
        if ($sql) {
            $last_id = $this->mCore->get_lastid('payments', 'id');
            if (!empty($_FILES['proof_url']['name'])) {
                $upload_file = $this->upload_image('proof_url', $last_id);
                if ($upload_file['status'] == 0) {
                    $this->response([
                        'status' => false,
                        'message' => $upload_file['message']
                    ], 404);
                }
            }
            $last_data = $this->mCore->get_data('payments', ['id' => $last_id])->row_array();
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
            'participant_id' => $this->post('participant_id'),
            'program_payment_id' => $this->post('program_payment_id'),
            'payment_method_id' => $this->post('payment_method_id'),
            'status' => $this->post('status'),
            'proof_url' => NULL,
            'account_name' => $this->post('account_name'),
            'amount' => $this->post('amount'),
            'updated_at' => date('Y-m-d H:i:s'),
        );
        $sql = $this->mCore->save_data('payments', array_filter($data), true, ['id' => $id]);
        if ($sql) {
            if (!empty($_FILES['proof_url']['name'])) {
                $upload_file = $this->upload_image('proof_url', $id);
                if ($upload_file['status'] == 0) {
                    $this->response([
                        'status' => false,
                        'message' => $upload_file['message']
                    ], 404);
                }
            }
            $last_data = $this->mCore->get_data('payments', ['id' => $id])->row_array();
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
        $sql = $this->mCore->save_data('payments', $data, true, ['id' => $id]);
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
    public function upload_image($proof_url, $id)
    {
        $this->load->library('ftp');

        $opt = array(
            'select' => 'payments.*, payment_methods.program_id',
            'table' => 'payments',
            'join' => ['payment_methods' => 'payments.payment_method_id = payment_methods.id'],
            'where' => 'payments.id = ' . $id
        );

        $data = $this->mCore->join_table($opt)->row_array();

        if ($data['proof_url'] != '') {
            $exp = (explode('/', $data['proof_url']));
            $temp_img = end($exp);

            //FTP configuration
            $ftp_config['hostname'] = config_item('hostname_upload');
            $ftp_config['username'] = config_item('username_upload');
            $ftp_config['password'] = config_item('password_upload');
            $ftp_config['port'] = config_item('port_upload');
            $ftp_config['debug'] = TRUE;

            $this->ftp->connect($ftp_config);

            $this->ftp->delete_file('payments/' . $data['program_id'] . '/' . $data['program_payment_id'] . '/' . $temp_img);

            $this->ftp->close();
        }

        $config['upload_path'] = './uploads';
        $config['allowed_types'] = '*';
        $config['max_size'] = 5000;
        $config['file_name'] = time();

        $this->load->library('upload', $config);
        $this->upload->initialize($config);
        if ($this->upload->do_upload($proof_url)) {

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

            if ($this->ftp->list_files('payments/' . $data['program_id'] . '/' . $data['program_payment_id'] . '/') == FALSE) {
                $this->ftp->mkdir('payments/' . $data['program_id'] . '/', DIR_WRITE_MODE, true);
                $this->ftp->mkdir('payments/' . $data['program_id'] . '/' . $data['program_payment_id'] . '/', DIR_WRITE_MODE, true);
            }

            $destination = 'payments/' . $data['program_id'] . '/' . $data['program_payment_id'] . '/' . $fileName;

            $this->ftp->upload($source, $destination);

            $this->ftp->close();

            //Delete file from local server
            @unlink($source);

            $sql = $this->mCore->save_data('payments', ['proof_url' => config_item('dir_upload') . 'payments/' . $data['program_id'] . '/' . $data['program_payment_id'] . '/' . $fileName], true, array('id' => $id));

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
