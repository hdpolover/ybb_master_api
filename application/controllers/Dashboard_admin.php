<?php

defined('BASEPATH') or exit('No direct script access allowed');

use chriskacerguis\RestServer\RestController;

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST');
header("Access-Control-Allow-Headers: X-Requested-With");

class Dashboard_admin extends RestController
{

    public function __construct()
    {
        parent::__construct();
    }

    public function user_count_by_day_get()
    {
        $program_id = $this->get('program_id');
        if ($program_id) {
            $data = $this->mCore->query_data('SELECT date(users.created_at) tanggal, count(*) jumlah FROM users JOIN participants ON participants.user_id = users.id WHERE program_id = ' . $program_id . ' GROUP BY DATE(users.created_at) order by users.created_at asc')->result_array();
        } else {
            $data = $this->mCore->query_data('SELECT date(users.created_at) tanggal, count(*) jumlah FROM users JOIN participants ON participants.user_id = users.id GROUP BY DATE(users.created_at) order by users.created_at asc')->result_array();
        }
        $this->response([
            'status' => true,
            'data' => $data,
        ], 200);
    }

    public function participant_country_count_get()
    {
        $program_id = $this->get('program_id');
        if ($program_id) {
            $data = $this->mCore->query_data('SELECT nationality,count(*) jumlah FROM participants WHERE program_id = ' . $program_id . ' group by nationality order by jumlah desc')->result_array();
        } else {
            $data = $this->mCore->query_data('SELECT nationality,count(*) jumlah FROM participants group by nationality order by jumlah desc')->result_array();
        }
        $this->response([
            'status' => true,
            'data' => $data,
        ], 200);
    }

    public function all_payment_get()
    {
        $email = $this->get('email');
        $status = $this->get('status');
        $start_date = $this->get('start_date');
        $end_date = $this->get('end_date');
        $program_payment_id = $this->get('program_payment_id');
        $payment_method_id = $this->get('payment_method_id');

        $option = array(
            'select' => 'payments.*, participants.full_name, participants.phone_number, users.email,
            program_payments.name program_payments_name, program_payments.description, program_payments.start_date, program_payments.end_date,
            program_payments.order_number, program_payments.idr_amount, program_payments.usd_amount, program_payments.category,
            payment_methods.name payment_methods_name, payment_methods.type, payment_methods.img_url',
            'table' => 'payments',
            'join' => [
                'participants' => 'payments.participant_id = participants.id',
                'users' => 'participants.user_id = users.id',
                'program_payments' => 'payments.program_payment_id = program_payments.id',
                'payment_methods' => 'payments.payment_method_id = payment_methods.id',
            ],
            'where' => array_filter([
                'users.email' => $email,
                'payments.status' => $status,
                'DATE(payments.created_at) >=' => $start_date,
                'DATE(payments.created_at) <=' => $end_date,
                'payments.program_payment_id' => $program_payment_id,
                'payments.payment_method_id' => $payment_method_id,
            ]),
            'order' => ['payments.id' => 'asc'],
        );

        $data = $this->mCore->join_table($option)->result_array();

        $this->response([
            'status' => true,
            'data' => $data,
        ], 200);
    }

    public function participant_stats_get()
    {

        $param = $this->get('param');
        $program_id = $this->get('program_id');
        if ($program_id) {
            $data = $this->mCore->query_data('SELECT ' . $param . ',count(*) jumlah FROM participants WHERE program_id = '.$program_id.' group by  ' . $param . ' order by jumlah desc')->result_array();
        } else {
            $data = $this->mCore->query_data('SELECT ' . $param . ',count(*) jumlah FROM participants group by  ' . $param . ' order by jumlah desc')->result_array();
        }

        $this->response([
            'status' => true,
            'data' => $data,
        ], 200);
    }
}
