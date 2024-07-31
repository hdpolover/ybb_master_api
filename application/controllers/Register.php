<?php

defined('BASEPATH') or exit('No direct script access allowed');


class Register extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
    }

    public function index()
    {
        $code = $this->input->get('code');

        if ($code) {
            // check ambassador

            $opt = array(
                'select' => 'ambassadors.*, programs.program_category_id',
                'table' => 'ambassadors',
                'join' => ['programs' => 'programs.id = ambassadors.program_id'],
                'where' => ['ref_code' => $code],
            );

            $ambassadors = $this->mCore->join_table($opt);

            if ($ambassadors->num_rows() == 0) {
                $data_view = [
                    'title' => 'Error Page',
                    'message' => 'Ambassador code not found!'
                ];
                $this->load->view("errors/page", $data_view);
            } else {
                // jika ada
                $this->load->view("errors/page");
            }
        } else {
            $data_view = [
                'title' => 'Error Page',
                'message' => 'No Parameters!'
            ];
            $this->load->view("errors/page", $data_view);
        }
    }

    public function save(){
        
        // cek data jika sudah terdaftar di program itu
        $opt = array(
            'select' => 'participants.*',
            'table' => 'users',
            'join' => ['participants' => 'users.id = participants.user_id'],
            'where' => ['email' => $this->input->post('email'), 'program_id' => $this->input->post('program_id')],
        );

        $check_data = $this->mCore->join_table($opt)->num_rows();
        if ($check_data) {
            $arr['status'] = false;
            $arr['message'] = 'You are already registered as a participant. Please sign in to continue.';
            echo json_encode($arr);
            exit();
        } else {
            $data = array(
                'full_name' => $this->input->post('full_name'),
                'email' => $this->input->post('email'),
                'password' => md5($this->input->post('password')),
                'program_category_id' =>  $this->input->post('program_category_id'),
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            );
            $sql = $this->mCore->save_data('users', $data);
            if ($sql) {
                $last_id = $this->mCore->get_lastid('users', 'id');

                $participants = array(
                    'user_id' => $last_id,
                    'account_id' => uniqid($last_id),
                    'full_name' => $data['full_name'],
                    'ref_code_ambassador' => $code,
                    'program_id' =>  $this->input->post('program_id'),
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s'),
                );
                $this->mCore->save_data('participants', $participants);
                $last_participant_id = $this->mCore->get_lastid('participants', 'id');

                // participant statues
                $participant_statuses = array(
                    'participant_id' => $last_participant_id,
                    'general_status' => 0,
                    'form_status' => 0,
                    'document_status' => 0,
                    'payment_status' => 0,
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s'),
                );
                $this->mCore->save_data('participant_statuses', $participant_statuses);

                // $last_data = $this->mCore->get_data('users', ['id' => $last_id])->row();

                $arr['status'] = true;
                $arr['message'] = 'Data saved successfully!';
                echo json_encode($arr);
            } else {
                $arr['status'] = false;
                $arr['message'] = 'Sorry, failed to save';
                echo json_encode($arr);
                exit();
            }
        }
    }
}
