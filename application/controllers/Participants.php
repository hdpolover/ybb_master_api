<?php

defined('BASEPATH') or exit('No direct script access allowed');

use chriskacerguis\RestServer\RestController;

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, PUT, PATCH, POST, DELETE');
header("Access-Control-Allow-Headers: X-Requested-With");

class Participants extends RestController
{
    public function __construct()
    {
        parent::__construct();
    }

    public function index_get()
    {
        $id = $this->get('id');
        if ($id == '') {
            $option = array(
                'select' => 'participants.*, a.general_status, a.form_status, a.document_status, a.payment_status, b.email',
                'table' => 'participants',
                'join' => [
                    'participant_statuses a' => 'a.participant_id = participants.id AND a.is_active = 1',
                    'users b' => 'b.id = participants.user_id AND b.is_active = 1',
                ],
                'where' => ['participants.is_active = 1'],
                'order' => ['participants.id' => 'ASC']
            );
            $participants = $this->mCore->join_table($option)->result_array();
            if ($participants) {
                $this->response([
                    'status' => true,
                    'data' => $participants,
                ], 200);
            } else {
                $this->response([
                    'status' => false,
                    'message' => 'No result were found',
                ], 404);
            }
        } else {
            $option = array(
                'select' => 'participants.*, a.general_status, a.form_status, a.document_status, a.payment_status, b.email',
                'table' => 'participants',
                'join' => [
                    'participant_statuses a' => 'a.participant_id = participants.id AND a.is_active = 1',
                    'users b' => 'b.id = participants.user_id AND b.is_active = 1',
                ],
                'where' => 'participants.id = ' . $id . ' AND participants.is_active = 1',
            );
            $participants = $this->mCore->join_table($option)->row_array();
            if ($participants) {
                $this->response([
                    'status' => true,
                    'data' => $participants,
                ], 200);
            } else {
                $this->response([
                    'status' => false,
                    'message' => 'No result were found',
                ], 404);
            }
        }
    }

    //LIST AMBASSADOR
    public function participant_user_get()
    {
        $user_id = $this->get('user_id');

        $option = array(
            'select' => 'participants.*, a.general_status, a.form_status, a.document_status, a.payment_status, b.email',
            'table' => 'participants',
            'join' => [
                'participant_statuses a' => 'a.participant_id = participants.id AND a.is_active = 1',
                'users b' => 'b.id = participants.user_id AND b.is_active = 1',
            ],
            'where' => 'participants.user_id = ' . $user_id . ' AND participants.is_active = 1',
        );
        $participants = $this->mCore->join_table($option)->result_array();
        if ($participants) {
            $this->response([
                'status' => true,
                'data' => $participants,
            ], 200);
        } else {
            $this->response([
                'status' => false,
                'message' => 'No result were found',
            ], 404);
        }
    }

    //LIST PROGRAM
    public function participant_program_get()
    {
        $program_id = $this->get('program_id');

        $option = array(
            'select' => 'participants.*, a.general_status, a.form_status, a.document_status, a.payment_status, b.email',
            'table' => 'participants',
            'join' => [
                'participant_statuses a' => 'a.participant_id = participants.id AND a.is_active = 1',
                'users b' => 'b.id = participants.user_id AND b.is_active = 1',
            ],
            'where' => 'participants.program_id = ' . $program_id . ' AND participants.is_active = 1',
        );
        $participants = $this->mCore->join_table($option)->result_array();
        if ($participants) {
            $this->response([
                'status' => true,
                'data' => $participants,
            ], 200);
        } else {
            $this->response([
                'status' => false,
                'message' => 'No result were found',
            ], 404);
        }
    }

    //LIST AMBASSADOR
    public function list_ambassador_get()
    {
        $ref_code = $this->get('ref_code');
        $option = array(
            'select' => 'participants.*, a.general_status, a.form_status, a.document_status, a.payment_status, b.email',
            'table' => 'participants',
            'join' => [
                'participant_statuses a' => 'a.participant_id = participants.id AND a.is_active = 1',
                'users b' => 'b.id = participants.user_id AND b.is_active = 1',
            ],
            'where' => 'participants.ref_code_ambassador = "' . $ref_code . '" AND participants.is_active = 1',
        );
        $participants = $this->mCore->join_table($option)->result_array();
        if ($participants) {
            $this->response([
                'status' => true,
                'data' => $participants,
            ], 200);
        } else {
            $this->response([
                'status' => false,
                'message' => 'No result were found',
            ], 404);
        }
    }

    //CHECK AMBASSADOR
    public function validate_ref_code_post()
    {
        $ref_code = $this->post('ref_code');
        $ambassadors = $this->mCore->get_data('ambassadors', ['ref_code' => $ref_code])->row();
        if ($ambassadors) {
            $this->response([
                'status' => true,
                'data' => $ambassadors,
            ], 200);
        } else {
            $this->response([
                'status' => false,
                'message' => 'The referral code you entered is invalid. Please try again.',
            ], 404);
        }
    }

    //SIMPAN DATA
    public function save_post()
    {
        $data = array(
            'user_id' => $this->post('user_id'),
            'account_id' => uniqid($this->post('user_id')),
            'full_name' => $this->post('full_name'),
            'birthdate' => $this->post('birthdate'),
            'ref_code_ambassador' => $this->post('ref_code_ambassador'),
            'program_id' => $this->post('program_id'),
            'gender' => $this->post('gender'),
            'origin_address' => $this->post('origin_address'),
            'current_address' => $this->post('current_address'),
            'nationality' => $this->post('nationality'),
            'occupation' => $this->post('occupation'),
            'institution' => $this->post('institution'),
            'organizations' => $this->post('organizations'),
            'country_code' => $this->post('country_code'),
            'phone_number' => $this->post('phone_number'),
            'picture_url' => null,
            'instagram_account' => $this->post('instagram_account'),
            'emergency_account' => $this->post('emergency_account'),
            'contact_relation' => $this->post('contact_relation'),
            'disease_history' => $this->post('disease_history'),
            'tshirt_size' => $this->post('tshirt_size'),
            'category' => $this->post('category'),
            'experiences' => $this->post('experiences'),
            'achievements' => $this->post('achievements'),
            'resume_url' => null,
            'knowledge_source' => $this->post('knowledge_source'),
            'source_account_name' => $this->post('source_account_name'),
            'twibbon_link' => $this->post('twibbon_link'),
            'requirement_link' => $this->post('requirement_link'),
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        );
        $sql = $this->mCore->save_data('participants', array_filter($data));
        if ($sql) {
            $last_id = $this->mCore->get_lastid('participants', 'id');
            if (!empty($_FILES['picture_url']['name'])) {
                $upload_file = $this->upload_picture('picture_url', $last_id);
                if ($upload_file['status'] == 0) {
                    $this->response([
                        'status' => false,
                        'message' => $upload_file['message'],
                    ], 404);
                }
            }
            if (!empty($_FILES['resume_url']['name'])) {
                $upload_file = $this->upload_resume('resume_url', $last_id);
                if ($upload_file['status'] == 0) {
                    $this->response([
                        'status' => false,
                        'message' => $upload_file['message'],
                    ], 404);
                }
            }
            $last_data = $this->mCore->get_data('participants', ['id' => $last_id])->row_array();
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

    // SIGNIN
    public function signin_post()
    {
        $sql = $this->mCore->do_signin_participant($this->post('email'), $this->post('password'), $this->post('program_category_id'));

        if ($sql['status']) {
            $this->response([
                'status' => true,
                'data' => $sql['data'],
            ], 200);
        } else {
            $this->response([
                'status' => false,
                'message' => $sql['data'],
            ], 404);
        }
    }

    //UPDATE DATA
    public function update_post($id)
    {
        $data = array(
            'full_name' => $this->post('full_name'),
            'birthdate' => $this->post('birthdate'),
            'ref_code_ambassador' => $this->post('ref_code_ambassador'),
            'program_id' => $this->post('program_id'),
            'gender' => $this->post('gender'),
            'origin_address' => $this->post('origin_address'),
            'current_address' => $this->post('current_address'),
            'nationality' => $this->post('nationality'),
            'occupation' => $this->post('occupation'),
            'institution' => $this->post('institution'),
            'organizations' => $this->post('organizations'),
            'country_code' => $this->post('country_code'),
            'phone_number' => $this->post('phone_number'),
            'instagram_account' => $this->post('instagram_account'),
            'emergency_account' => $this->post('emergency_account'),
            'contact_relation' => $this->post('contact_relation'),
            'disease_history' => $this->post('disease_history'),
            'tshirt_size' => $this->post('tshirt_size'),
            'category' => $this->post('category'),
            'experiences' => $this->post('experiences'),
            'achievements' => $this->post('achievements'),
            'knowledge_source' => $this->post('knowledge_source'),
            'source_account_name' => $this->post('source_account_name'),
            'twibbon_link' => $this->post('twibbon_link'),
            'requirement_link' => $this->post('requirement_link'),
            'updated_at' => date('Y-m-d H:i:s'),
        );
        $sql = $this->mCore->save_data('participants', array_filter($data), true, ['id' => $id]);
        if ($sql) {
            if (!empty($_FILES['picture_url']['name'])) {
                $upload_file = $this->upload_picture('picture_url', $id);
                if ($upload_file['status'] == 0) {
                    $this->response([
                        'status' => false,
                        'message' => $upload_file['message'],
                    ], 404);
                }
            }
            if (!empty($_FILES['resume_url']['name'])) {
                $upload_file = $this->upload_resume('resume_url', $id);
                if ($upload_file['status'] == 0) {
                    $this->response([
                        'status' => false,
                        'message' => $upload_file['message'],
                    ], 404);
                }
            }
            $last_data = $this->mCore->get_data('participants', ['id' => $id])->row_array();
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
        $sql = $this->mCore->save_data('participants', $data, true, ['id' => $id]);
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

    // UPLOAD PICTURE
    private function upload_picture($picture_url, $id)
    {

        $this->load->library('ftp');

        $data = $this->mCore->get_data('participants', 'id = ' . $id)->row_array();
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

            $this->ftp->delete_file('participants/' . $data['program_id'] . '/' . $data['user_id'] . '/' . $temp_img);

            $this->ftp->close();
        }

        $config['upload_path'] = './uploads';
        $config['allowed_types'] = 'gif|jpg|png|jpeg';
        $config['max_size'] = 5000;
        $config['file_name'] = time();

        $this->load->library('upload', $config);
        $this->upload->initialize($config);
        if ($this->upload->do_upload($picture_url)) {

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

            if ($this->ftp->list_files('participants/' . $data['program_id'] . '/') == false) {
                // $this->ftp->mkdir('participants/' . $data['program_id'] . '/' . $data['user_id'] . '/', DIR_WRITE_MODE);
                $this->ftp->mkdir('participants/' . $data['program_id'] . '/', DIR_WRITE_MODE, true);
                // $this->ftp->mkdir('participants/' . $data['program_id'] . '/' . $data['user_id'] . '/', DIR_WRITE_MODE, true);
            }

            if ($this->ftp->list_files('participants/' . $data['program_id'] . '/' . $data['user_id'] . '/') == false) {
                // $this->ftp->mkdir('participants/' . $data['program_id'] . '/' . $data['user_id'] . '/', DIR_WRITE_MODE);
                // $this->ftp->mkdir('participants/' . $data['program_id'] . '/', DIR_WRITE_MODE, true);
                $this->ftp->mkdir('participants/' . $data['program_id'] . '/' . $data['user_id'] . '/', DIR_WRITE_MODE, true);
            }


            $destination = 'participants/' . $data['program_id'] . '/' . $data['user_id'] . '/' . $fileName;

            $this->ftp->upload($source, $destination);

            $this->ftp->close();

            //Delete file from local server
            @unlink($source);

            $sql = $this->mCore->save_data('participants', ['picture_url' => config_item('dir_upload') . 'participants/' . $data['program_id'] . '/' . $data['user_id'] . '/' . $fileName], true, array('id' => $id));

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

    // UPLOAD PICTURE DIRECT
    public function do_upload_picture_post()
    {

        $this->load->library('ftp');

        $id = $this->post('id');

        $data = $this->mCore->get_data('participants', 'id = ' . $id)->row_array();
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

            $this->ftp->delete_file('participants/' . $data['program_id'] . '/' . $data['user_id'] . '/' . $temp_img);

            $this->ftp->close();
        }

        $config['upload_path'] = './uploads';
        $config['allowed_types'] = 'gif|jpg|png|jpeg';
        $config['max_size'] = 5000;
        $config['file_name'] = time();

        $this->load->library('upload', $config);
        $this->upload->initialize($config);
        if ($this->upload->do_upload("picture")) {

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

            if ($this->ftp->list_files('participants/' . $data['program_id'] . '/' . $data['user_id'] . '/') == false) {
                // $this->ftp->mkdir('participants/' . $data['program_id'] . '/' . $data['user_id'] . '/', DIR_WRITE_MODE);
                $this->ftp->mkdir('participants/' . $data['program_id'] . '/', DIR_WRITE_MODE, true);
                $this->ftp->mkdir('participants/' . $data['program_id'] . '/' . $data['user_id'] . '/', DIR_WRITE_MODE, true);
            }

            $destination = 'participants/' . $data['program_id'] . '/' . $data['user_id'] . '/' . $fileName;

            $this->ftp->upload($source, $destination);

            $this->ftp->close();

            //Delete file from local server
            @unlink($source);

            $sql = $this->mCore->save_data('participants', ['picture_url' => config_item('dir_upload') . 'participants/' . $data['program_id'] . '/' . $data['user_id'] . '/' . $fileName], true, array('id' => $id));

            if ($sql) {
                $this->response([
                    'status' => true,
                    'message' => 'Picture saved successfully',
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

    // UPLOAD RESUME
    public function upload_resume($resume_url, $id)
    {

        $this->load->library('ftp');

        $data = $this->mCore->get_data('participants', 'id = ' . $id)->row_array();
        if ($data['resume_url'] != '') {
            $exp = (explode('/', $data['resume_url']));
            $temp_img = end($exp);

            //FTP configuration
            $ftp_config['hostname'] = config_item('hostname_upload');
            $ftp_config['username'] = config_item('username_upload');
            $ftp_config['password'] = config_item('password_upload');
            $ftp_config['port'] = config_item('port_upload');
            $ftp_config['debug'] = true;

            $this->ftp->connect($ftp_config);

            $this->ftp->delete_file('participants/' . $data['program_id'] . '/' . $data['user_id'] . '/' . $temp_img);

            $this->ftp->close();
        }

        $config['upload_path'] = './uploads';
        $config['allowed_types'] = '*';
        $config['max_size'] = 5000;
        $config['file_name'] = time();

        $this->load->library('upload', $config);
        $this->upload->initialize($config);
        if ($this->upload->do_upload($resume_url)) {

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

            if ($this->ftp->list_files('participants/' . $data['program_id'] . '/' . $data['user_id'] . '/') == false) {
                $this->ftp->mkdir('participants/' . $data['program_id'] . '/', DIR_WRITE_MODE, true);
                $this->ftp->mkdir('participants/' . $data['program_id'] . '/' . $data['user_id'] . '/', DIR_WRITE_MODE, true);
            }

            $destination = 'participants/' . $data['program_id'] . '/' . $data['user_id'] . '/' . $fileName;

            $this->ftp->upload($source, $destination);

            $this->ftp->close();

            //Delete file from local server
            @unlink($source);

            $sql = $this->mCore->save_data('participants', ['resume_url' => config_item('dir_upload') . 'participants/' . $data['program_id'] . '/' . $data['user_id'] . '/' . $fileName], true, array('id' => $id));

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
