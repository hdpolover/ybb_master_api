<?php

defined('BASEPATH') or exit('No direct script access allowed');

use chriskacerguis\RestServer\RestController;
use Ramsey\Uuid\Uuid;

class participants extends RestController
{

    function __construct()
    {
        parent::__construct();
    }

    function index_get()
    {
        $id = $this->get('id');
        if ($id == '') {
            $participants = $this->mCore->list_data('participants')->result_array();
            if ($participants) {
                $this->response($participants, 200);
            } else {
                $this->response([
                    'status' => false,
                    'message' => 'No result were found'
                ], 404);
            }
        } else {
            $participants = $this->mCore->get_data('participants', ['id' => $id])->result_array();
            if ($participants) {
                $this->response($participants, 200);
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
            'user_id' => $this->post('user_id'),
            'account_id' => Uuid::uuid5(Uuid::NAMESPACE_URL, $this->post('user_id')),
            'full_name' => $this->post('full_name'),
            'birthdate' => $this->post('birthdate'),
            'nationality' => $this->post('nationality'),
            'gender' => $this->post('gender'),
            'phone_number' => $this->post('phone_number'),
            'country_code' => $this->post('country_code'),
            'progam_id' => $this->post('progam_id'),
            'created_at' => date('Y-m-d H:i:s'),
        );
        $sql = $this->mCore->save_data('participants', $data);
        if ($sql) {
            $this->response($data, 200);
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
            'full_name' => $this->post('full_name'),
            'birthdate' => $this->post('birthdate'),
            'nationality' => $this->post('nationality'),
            'gender' => $this->post('gender'),
            'phone_number' => $this->post('phone_number'),
            'country_code' => $this->post('country_code'),
            'progam_id' => $this->post('progam_id'),
            'updated_at' => date('Y-m-d H:i:s'),
        );
        $sql = $this->mCore->save_data('participants', $data, true, ['id' => $id]);
        if ($sql) {
            $this->response($data, 200);
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
        $sql = $this->mCore->save_data('participants', $data, true, ['id' => $id]);
        if ($sql) {
            $this->response($data, 200);
        } else {
            $this->response([
                'status' => false,
                'message' => 'Sorry, failed to delete'
            ], 404);
        }
    }
}
?>