<?php

defined('BASEPATH') or exit('No direct script access allowed');

use chriskacerguis\RestServer\RestController;
use Xendit\Configuration;
use Xendit\Invoice\InvoiceApi;
use Xendit\PaymentMethod\PaymentMethodApi;
use Xendit\PaymentRequest\PaymentRequestApi;


header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST');
header("Access-Control-Allow-Headers: X-Requested-With");

class Payments extends RestController
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
				'select' => 'payments.*, participants.full_name, participants.phone_number, users.email,
				program_payments.program_id, program_payments.name program_payments_name, program_payments.description, 
				program_payments.start_date, program_payments.end_date, program_payments.order_number, program_payments.idr_amount, 
				program_payments.usd_amount, program_payments.category, payment_methods.name payment_methods_name, 
				payment_methods.type, payment_methods.img_url',
				'table' => 'payments',
				'join' => [
					'participants' => 'payments.participant_id = participants.id AND participants.is_active = 1',
					'users' => 'participants.user_id = users.id AND users.is_active = 1',
					'program_payments' => 'payments.program_payment_id = program_payments.id AND program_payments.is_active = 1',
					'payment_methods' => 'payments.payment_method_id = payment_methods.id AND payment_methods.is_active = 1',
				],
				'where' => ['payments.is_active' => 1],
				'order' => ['payments.id' => 'asc']
			);
			$payments = $this->mCore->join_table($option)->result_array();
			if ($payments) {
				$this->response([
					'status' => true,
					'data' => $payments,
				], 200);
			} else {
				$this->response([
					'status' => false,
					'message' => 'No result were found',
				], 404);
			}
		} else {
			$option = array(
				'select' => 'payments.*, participants.full_name, participants.phone_number, users.email,
				program_payments.program_id, program_payments.name program_payments_name, program_payments.description, 
				program_payments.start_date, program_payments.end_date, program_payments.order_number, program_payments.idr_amount, 
				program_payments.usd_amount, program_payments.category, payment_methods.name payment_methods_name, 
				payment_methods.type, payment_methods.img_url',
				'table' => 'payments',
				'join' => [
					'participants' => 'payments.participant_id = participants.id AND participants.is_active = 1',
					'users' => 'participants.user_id = users.id AND users.is_active = 1',
					'program_payments' => 'payments.program_payment_id = program_payments.id AND program_payments.is_active = 1',
					'payment_methods' => 'payments.payment_method_id = payment_methods.id AND payment_methods.is_active = 1',
				],
				'where' => ['payments.id' => $id, 'payments.is_active' => 1],
				'order' => ['payments.id' => 'asc']
			);
			$payments = $this->mCore->join_table($option)->row();
			if ($payments) {
				$this->response([
					'status' => true,
					'data' => $payments,
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
		$participant_id = $this->get('participant_id');

		$option = array(
			'select' => 'payments.*, participants.full_name, participants.phone_number, users.email,
				program_payments.program_id, program_payments.name program_payments_name, program_payments.description, 
				program_payments.start_date, program_payments.end_date, program_payments.order_number, program_payments.idr_amount, 
				program_payments.usd_amount, program_payments.category, payment_methods.name payment_methods_name, 
				payment_methods.type, payment_methods.img_url',
			'table' => 'payments',
			'join' => [
				'participants' => 'payments.participant_id = participants.id AND participants.is_active = 1',
				'users' => 'participants.user_id = users.id AND users.is_active = 1',
				'program_payments' => 'payments.program_payment_id = program_payments.id AND program_payments.is_active = 1',
				// 'payment_methods' => 'payments.payment_method_id = payment_methods.id AND payment_methods.is_active = 1',
				// payment method bisa gak aktif
				'payment_methods' => 'payments.payment_method_id = payment_methods.id',
			],
			'where' => ['payments.participant_id' => $participant_id, 'payments.is_active' => 1],
			'order' => ['payments.id' => 'asc']
		);
		$payments = $this->mCore->join_table($option)->result_array();

		if ($payments) {
			$this->response([
				'status' => true,
				'data' => $payments,
			], 200);
		} else {
			$this->response([
				'status' => false,
				'message' => 'No result were found',
			], 404);
		}
	}

	public function list_payment_get()
	{
		$program_payment_id = $this->get('program_payment_id');

		$option = array(
			'select' => 'payments.*, participants.full_name, participants.phone_number, users.email,
				program_payments.program_id, program_payments.name program_payments_name, program_payments.description, 
				program_payments.start_date, program_payments.end_date, program_payments.order_number, program_payments.idr_amount, 
				program_payments.usd_amount, program_payments.category, payment_methods.name payment_methods_name, 
				payment_methods.type, payment_methods.img_url',
			'table' => 'payments',
			'join' => [
				'participants' => 'payments.participant_id = participants.id AND participants.is_active = 1',
				'users' => 'participants.user_id = users.id AND users.is_active = 1',
				'program_payments' => 'payments.program_payment_id = program_payments.id AND program_payments.is_active = 1',
				'payment_methods' => 'payments.payment_method_id = payment_methods.id AND payment_methods.is_active = 1',
			],
			'where' => ['payments.program_payment_id' => $program_payment_id, 'payments.is_active' => 1],
			'order' => ['payments.id' => 'asc']
		);
		$payments = $this->mCore->join_table($option)->result_array();

		if ($payments) {
			$this->response([
				'status' => true,
				'data' => $payments,
			], 200);
		} else {
			$this->response([
				'status' => false,
				'message' => 'No result were found',
			], 404);
		}
	}

	public function list_method_get()
	{
		$payment_method_id = $this->get('payment_method_id');

		$option = array(
			'select' => 'payments.*, participants.full_name, participants.phone_number, users.email,
				program_payments.program_id, program_payments.name program_payments_name, program_payments.description, 
				program_payments.start_date, program_payments.end_date, program_payments.order_number, program_payments.idr_amount, 
				program_payments.usd_amount, program_payments.category, payment_methods.name payment_methods_name, 
				payment_methods.type, payment_methods.img_url',
			'table' => 'payments',
			'join' => [
				'participants' => 'payments.participant_id = participants.id AND participants.is_active = 1',
				'users' => 'participants.user_id = users.id AND users.is_active = 1',
				'program_payments' => 'payments.program_payment_id = program_payments.id AND program_payments.is_active = 1',
				'payment_methods' => 'payments.payment_method_id = payment_methods.id AND payment_methods.is_active = 1',
			],
			'where' => ['payments.payment_method_id' => $payment_method_id, 'payments.is_active' => 1],
			'order' => ['payments.id' => 'asc']
		);
		$payments = $this->mCore->join_table($option)->result_array();

		if ($payments) {
			$this->response([
				'status' => true,
				'data' => $payments,
			], 200);
		} else {
			$this->response([
				'status' => false,
				'message' => 'No result were found',
			], 404);
		}
	}

	public function payment_program_get()
	{
		$program_id = $this->get('program_id');

		$option = array(
			'select' => 'payments.*, participants.full_name, participants.phone_number, participants.nationality, participants.gender, participants.institution,
				users.email, program_payments.program_id, program_payments.name program_payments_name, program_payments.description, 
				program_payments.start_date, program_payments.end_date, program_payments.order_number, program_payments.idr_amount, 
				program_payments.usd_amount, program_payments.category, payment_methods.name payment_methods_name, 
				payment_methods.type, payment_methods.img_url, xendit_payment.external_id, xendit_payment.status xendit_status, xendit_payment.payment_method xendit_payment_method',
			'table' => 'payments',
			'join' => [
				['participants' => 'payments.participant_id = participants.id AND participants.is_active = 1'],
				['users' => 'participants.user_id = users.id AND users.is_active = 1'],
				['program_payments' => 'payments.program_payment_id = program_payments.id AND program_payments.is_active = 1'],
				['payment_methods' => 'payments.payment_method_id = payment_methods.id AND payment_methods.is_active = 1'],
				['xendit_payment', 'payments.id = xendit_payment.payment_id', 'left'],
			],
			'where' => ['program_payments.program_id' => $program_id, 'payments.is_active' => 1],
			'order' => ['payments.created_at' => 'desc']
		);
		$payments = $this->mCore->join_table($option)->result_array();

		if ($payments) {
			$this->response([
				'status' => true,
				'data' => $payments,
			], 200);
		} else {
			$this->response([
				'status' => false,
				'message' => 'No result were found',
			], 404);
		}
	}

	public function list_payment_xendit_get()
	{

		$program_id = $this->get('program_id');

		$option = array(
			'select' => 'xendit_payment.*, users.full_name, users.email email_user, programs.name, programs.logo_url, program_categories.web_url,
				program_categories.contact,program_categories.email email_program_category, program_payments.name program_payment_name',
			'table' => 'xendit_payment',
			'join' => [
				'payments' => 'payments.id = xendit_payment.payment_id',
				'program_payments' => 'program_payments.id = payments.program_payment_id',
				'payment_methods' => 'payment_methods.id = payments.payment_method_id',
				'participants' => 'participants.id = xendit_payment.participant_id',
				'users' => 'participants.user_id = users.id',
				'programs' => 'xendit_payment.program_id = programs.id',
				'program_categories' => 'programs.program_category_id = program_categories.id',
			],
			'where' => ['xendit_payment.program_id' => $program_id],
			'order' => ['xendit_payment.id' => 'asc']
		);

		$payments = $this->mCore->join_table($option)->result_array();

		if ($payments) {
			$this->response([
				'status' => true,
				'data' => $payments,
			], 200);
		} else {
			$this->response([
				'status' => false,
				'message' => 'No result were found',
			], 404);
		}
	}

	public function payment_xendit_get()
	{
		$id = $this->get('id');

		$option = array(
			'select' => 'xendit_payment.*, users.full_name, users.email email_user, programs.name, programs.logo_url, program_categories.web_url,
				program_categories.contact,program_categories.email email_program_category, program_payments.name program_payment_name',
			'table' => 'xendit_payment',
			'join' => [
				'payments' => 'payments.id = xendit_payment.payment_id',
				'program_payments' => 'program_payments.id = payments.program_payment_id',
				'payment_methods' => 'payment_methods.id = payments.payment_method_id',
				'participants' => 'participants.id = xendit_payment.participant_id',
				'users' => 'participants.user_id = users.id',
				'programs' => 'xendit_payment.program_id = programs.id',
				'program_categories' => 'programs.program_category_id = program_categories.id',
			],
			'where' => ['payments.id' => $id],
			'order' => ['xendit_payment.id' => 'asc']
		);

		$payments = $this->mCore->join_table($option)->row_array();

		if ($payments) {
			$this->response([
				'status' => true,
				'data' => $payments,
			], 200);
		} else {
			$this->response([
				'status' => false,
				'message' => 'No result were found',
			], 404);
		}
	}

	// UPDATE PAYMENT YANG LEBIH DARI SEHARI
	public function update_payments_get()
	{
		$sql = $this->mCore->query_data('UPDATE payments p JOIN payment_methods pm ON p.payment_method_id = pm.id SET p.status = 3 WHERE pm.type = "gateway" AND p.status IN (0, 1) AND p.updated_at < NOW() - INTERVAL 1 DAY');

		if ($sql) {
			$this->response([
				'status' => true,
				'message' => 'Data update successfully',
			], 200);
		} else {
			$this->response([
				'status' => false,
				'message' => 'Sorry, failed to update',
			], 404);
		}
	}

	//SIMPAN DATA
	public function save_post()
	{
		if ($this->post('amount') <= 0) {
			$this->response([
				'status' => false,
				'message' => 'Sorry, nominal must be more than 0',
			], 404);
		}

		$currency = 'USD';
		if ($this->post('amount') >= 1000) {
			$currency = 'IDR';
		}
		$data = array(
			'participant_id' => $this->post('participant_id'),
			'program_payment_id' => $this->post('program_payment_id'),
			'payment_method_id' => $this->post('payment_method_id'),
			'status' => $this->post('status'),
			'proof_url' => null,
			'account_name' => $this->post('account_name'),
			'amount' => $this->post('amount'),
			'currency' => $currency,
			'source_name' => $this->post('source_name'),
			'created_at' => date('Y-m-d H:i:s'),
			'updated_at' => date('Y-m-d H:i:s'),
		);
		$sql = $this->mCore->save_data('payments', array_filter($data));
		if ($sql) {
			$last_id = $this->mCore->get_lastid('payments', 'id');
			if (!empty($_FILES['proof_url']['name'])) {
				$upload_file = $this->upload_image('proof_url', $last_id);
				if ($upload_file['status'] == 0) {
					$this->response([
						'status' => false,
						'message' => $upload_file['message'],
					], 404);
				}
			}
			$last_data = $this->mCore->get_data('payments', ['id' => $last_id])->row_array();
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
		if ($this->post('amount') <= 0) {
			$this->response([
				'status' => false,
				'message' => 'Sorry, nominal must be more than 0',
			], 404);
		}

		$currency = 'USD';
		if ($this->post('amount') >= 1000) {
			$currency = 'IDR';
		}
		$data = array(
			'participant_id' => $this->post('participant_id'),
			'program_payment_id' => $this->post('program_payment_id'),
			'payment_method_id' => $this->post('payment_method_id'),
			'status' => $this->post('status'),
			'proof_url' => null,
			'account_name' => $this->post('account_name'),
			'amount' => $this->post('amount'),
			'currency' => $currency,
			'source_name' => $this->post('source_name'),
			'updated_at' => date('Y-m-d H:i:s'),
		);
		$sql = $this->mCore->save_data('payments', array_filter($data), true, ['id' => $id]);
		if ($sql) {
			if (!empty($_FILES['proof_url']['name'])) {
				$upload_file = $this->upload_image('proof_url', $id);
				if ($upload_file['status'] == 0) {
					$this->response([
						'status' => false,
						'message' => $upload_file['message'],
					], 404);
				}
			}
			$last_data = $this->mCore->get_data('payments', ['id' => $id])->row_array();
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
		$sql = $this->mCore->save_data('payments', $data, true, ['id' => $id]);
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

	// UPDATE STATUS
	public function update_status_post($id)
	{
		$status = $this->post('status');
		$sql = $this->mCore->save_data('payments', ['status' => $status], true, ['id' => $id]);
		if ($sql) {
			$this->response([
				'status' => true,
				'message' => 'Data update successfully',
			], 200);
		} else {
			$this->response([
				'status' => false,
				'message' => 'Sorry, failed to update',
			], 404);
		}
	}

	public function list_method_xendit_filter_get()
	{
		Configuration::setXenditKey(config_item('xendit'));
		$apiInstance = new PaymentMethodApi();
		// $payment_method_id = "pm-1fdaf346-dd2e-4b6c-b938-124c7167a822"; // string
		// $for_user_id = "660ed435c296c084e7badb36"; // string

		try {
			$result = $apiInstance->getPaymentMethodByID();
			print_r($result);
		} catch (\Xendit\XenditSdkException $e) {
			echo 'Exception when calling PaymentMethodApi->getPaymentMethodByID: ', $e->getMessage(), PHP_EOL;
			echo 'Full Error: ', json_encode($e->getFullError()), PHP_EOL;
		}
	}

	public function list_method_xendit_get()
	{
		Configuration::setXenditKey(config_item('xendit'));

		$apiInstance = new PaymentMethodApi();
		$for_user_id = "660ed435c296c084e7badb36"; // string
		$id = array('id_example'); // string[]
		$type = array('type_example'); // string[]
		$status = array(new \Xendit\PaymentMethod\PaymentMethodStatus()); // \PaymentMethod\PaymentMethodStatus[]
		$reusability = new \Xendit\PaymentMethod\PaymentMethodReusability(); // PaymentMethodReusability
		$customer_id = "'customer_id_example'"; // string
		$reference_id = "'reference_id_example'"; // string
		$after_id = "'after_id_example'"; // string
		$before_id = "'before_id_example'"; // string
		$limit = 56; // int

		try {
			$result = $apiInstance->getAllPaymentMethods();
			print_r($result['data']);
		} catch (\Xendit\XenditSdkException $e) {
			echo 'Exception when calling PaymentMethodApi->getAllPaymentMethods: ', $e->getMessage(), PHP_EOL;
			echo 'Full Error: ', json_encode($e->getFullError()), PHP_EOL;
		}
	}

	// list e request
	public function list_request_xendit_get()
	{
		Configuration::setXenditKey(config_item('xendit'));

		$apiInstance = new PaymentRequestApi();
		// $for_user_id = "5f9a3fbd571a1c4068aa40cf"; // string
		// $reference_id = array('reference_id_example'); // string[]
		// $id = array('id_example'); // string[]
		// $customer_id = array('customer_id_example'); // string[]
		// $limit = 56; // int
		// $before_id = "'before_id_example'"; // string
		// $after_id = "'after_id_example'"; // string

		try {
			$result = $apiInstance->getAllPaymentRequests();
			print_r($result['data']);
		} catch (\Xendit\XenditSdkException $e) {
			echo 'Exception when calling PaymentRequestApi->getAllPaymentRequests: ', $e->getMessage(), PHP_EOL;
			echo 'Full Error: ', json_encode($e->getFullError()), PHP_EOL;
		}
	}

	// pembayaran
	public function pay_post()
	{
		Configuration::setXenditKey(config_item('xendit'));

		$external_id = time() . rand(0, 100);
		$apiInstance = new InvoiceApi();
		$create_invoice_request = new Xendit\Invoice\CreateInvoiceRequest([
			'external_id' => $external_id,
			'description' => $this->input->post('description'),
			'payer_email' => $this->input->post('payer_email'),
			'amount' => $this->input->post('amount'),
			'invoice_duration' => 3600,
			// 'currency' => 'IDR',
			'reminder_time' => 1,
			'success_redirect_url' => base_url() . 'Payments/success_pay?external_id=' . $external_id,
			'failure_redirect_url' => base_url() . 'Payments/failure_pay?external_id=' . $external_id,
		]); // \Xendit\Invoice\CreateInvoiceRequest

		try {
			$result = $apiInstance->createInvoice($create_invoice_request);

			// $invoice_callback = new InvoiceCallback([
			//     'id' => $result['id'],
			//     'external_id' => $result['external_id'],
			//     'user_id' => $result['user_id'],
			//     'merchant_name' => 'Xendit',
			//     'amount' => $result['amount'],
			//     'created' => $result['created'],
			//     'updated' => $result['updated'],
			//     'currency' => $result['IDR'],
			// ]);

			// function simulateInvoiceCallback(InvoiceCallback $invoice_callback)
			// {
			//     echo $invoice_callback->getId();
			//     // do things here with the callback
			// }
			// tabel payment
			$data_payment = array(
				'participant_id' => $this->post('participant_id'),
				'program_payment_id' => $this->post('program_payment_id'),
				'payment_method_id' => $this->post('payment_method_id'),
				'status' => 1,
				'account_name' => $this->post('account_name'),
				'amount' => $this->input->post('amount'),
				'source_name' => $result['payer_email'],
				'created_at' => date('Y-m-d H:i:s'),
				'updated_at' => date('Y-m-d H:i:s'),
			);

			$this->mCore->save_data('payments', $data_payment);

			$last_id = $this->mCore->get_lastid('payments', 'id');

			// tabel pembantu
			$data = array(
				'participant_id' => $this->post('participant_id'),
				'payment_id' => $last_id,
				'program_id' => $this->post('program_id'),
				'description' => $result['description'],
				'amount' => $result['amount'],
				'email' => $result['payer_email'],
				'external_id' => $result['external_id'],
				'currency' => $result['currency'],
				'id_xendit' => $result['id'],
				'user_id_xendit' => $result['user_id'],
				'url_xendit' => $result['invoice_url'],
				'status' => $result['status'],
				'merchant_name' => $result['merchant_name'],
				'expired_at' => date_format($result['expiry_date'], 'Y-m-d H:i:s'),
				'created_at' => date_format($result['created'], 'Y-m-d H:i:s'),
				'updated_at' => date_format($result['updated'], 'Y-m-d H:i:s'),
			);

			$this->mCore->save_data('xendit_payment', $data);

			$this->response([
				'status' => true,
				'data' => $result['invoice_url'],
			], 200);
		} catch (\Xendit\XenditSdkException $e) {
			$this->response([
				'status' => false,
				'message' => 'Exception when calling InvoiceApi->createInvoice: ',
				$e->getMessage(),
				PHP_EOL,
			], 404);
			// echo 'Full Error: ', json_encode($e->getFullError()), PHP_EOL;
		}
	}

	// cek
	public function invoice_get()
	{
		$invoice_id = $this->get('id');

		Configuration::setXenditKey(config_item('xendit'));

		$apiInstance = new InvoiceApi();
		if ($invoice_id) {
			try {
				$result = $apiInstance->getInvoiceById($invoice_id);

				$this->response([
					'status' => true,
					'data' => $result,
				], 200);
			} catch (\Xendit\XenditSdkException $e) {
				$this->response([
					'status' => false,
					'message' => 'Exception when calling InvoiceApi->getInvoiceById: ',
					$e->getMessage(),
					PHP_EOL,
				], 404);
				// echo 'Full Error: ', json_encode($e->getFullError()), PHP_EOL;
			}
		} else {
			try {
				$result = $apiInstance->getInvoices();

				$this->response([
					'status' => true,
					'data' => $result,
				], 200);
			} catch (\Xendit\XenditSdkException $e) {
				$this->response([
					'status' => false,
					'message' => 'Exception when calling InvoiceApi->getInvoices: ',
					$e->getMessage(),
					PHP_EOL,
				], 404);
				// echo 'Full Error: ', json_encode($e->getFullError()), PHP_EOL;
			}
		}
	}

	public function success_pay_get()
	{

		$option = array(
			'select' => 'xendit_payment.*, users.full_name, users.email email_user, programs.name, programs.logo_url, program_categories.web_url,
				program_categories.contact,program_categories.email email_program_category, program_payments.name program_payment_name',
			'table' => 'xendit_payment',
			'join' => [
				'payments' => 'payments.id = xendit_payment.payment_id',
				'program_payments' => 'program_payments.id = payments.program_payment_id',
				'payment_methods' => 'payment_methods.id = payments.payment_method_id',
				'participants' => 'participants.id = xendit_payment.participant_id',
				'users' => 'participants.user_id = users.id',
				'programs' => 'xendit_payment.program_id = programs.id',
				'program_categories' => 'programs.program_category_id = program_categories.id',
			],
			'where' => 'xendit_payment.external_id = ' . $this->get('external_id'),
		);
		$data = $this->mCore->join_table($option)->row_array();

		// print_r($data);
		// die();
		// get invoice
		Configuration::setXenditKey(config_item('xendit'));
		$apiInstance = new InvoiceApi();

		$result = $apiInstance->getInvoiceById($data['id_xendit']);
		// print_r($result);

		// TABEL PEMBANTU
		$upd = array(
			'status' => $result['status'],
			'payment_method' => $result['payment_method'],
			'updated_at' => date_format($result['updated'], 'Y-m-d H:i:s'),
		);
		$this->mCore->save_data('xendit_payment', $upd, true, ['id_xendit' => $data['id_xendit']]);

		// diambil lagi
		$data = $this->mCore->join_table($option)->row_array();

		// email
		$config = array(
			'protocol' => 'smtp',
			'smtp_host' => 'ssl://smtp.googlemail.com',
			'smtp_port' => config_item('port_email'),
			'smtp_user' => config_item('user_email'),
			'smtp_pass' => config_item('pass_email'),
			'mailtype' => 'html',
			'charset' => 'iso-8859-1',
			'wordwrap' => true,
		);

		$message = ('
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Order confirmation</title>
<meta name="robots" content="noindex,nofollow" />
<meta name="viewport" content="width=device-width; initial-scale=1.0;" />
<style type="text/css">
	@import url(https://fonts.googleapis.com/css?family=Open+Sans:400,700);

	body {
		margin: 0;
		padding: 0;
		background: #e1e1e1;
	}

	div,
	p,
	a,
	li,
	td {
		-webkit-text-size-adjust: none;
	}

	.ReadMsgBody {
		width: 100%;
		background-color: #ffffff;
	}

	.ExternalClass {
		width: 100%;
		background-color: #ffffff;
	}

	body {
		width: 100%;
		height: 100%;
		background-color: #e1e1e1;
		margin: 0;
		padding: 0;
		-webkit-font-smoothing: antialiased;
	}

	html {
		width: 100%;
	}

	p {
		padding: 0 !important;
		margin-top: 0 !important;
		margin-right: 0 !important;
		margin-bottom: 0 !important;
		margin-left: 0 !important;
	}

	.visibleMobile {
		display: none;
	}

	.hiddenMobile {
		display: block;
	}

	.rotateWm {
		transform: rotate(-45deg);
	}

	@media only screen and (max-width: 600px) {
		body {
			width: auto !important;
		}

		table[class="fullTable"] {
			width: 96% !important;
			clear: both;
		}

		table[class="fullPadding"] {
			width: 85% !important;
			clear: both;
		}

		table[class="col"] {
			width: 45% !important;
		}

		.erase {
			display: none;
		}
	}

	@media only screen and (max-width: 420px) {
		table[class="fullTable"] {
			width: 100% !important;
			clear: both;
		}

		table[class="fullPadding"] {
			width: 85% !important;
			clear: both;
		}

		table[class="col"] {
			width: 100% !important;
			clear: both;
		}

		table[class="col"] td {
			text-align: left !important;
		}

		.erase {
			display: none;
			font-size: 0;
			max-height: 0;
			line-height: 0;
			padding: 0;
		}

		.visibleMobile {
			display: block !important;
		}

		.hiddenMobile {
			display: none !important;
		}
	}

</style>

<!-- Header -->
<table
	width="100%"
	border="0"
	cellpadding="0"
	cellspacing="0"
	align="center"
	class="fullTable"
	bgcolor="#e1e1e1"
>
	<tr>
		<td height="20"></td>
	</tr>
	<tr>
		<td>
			<table
				width="600"
				border="0"
				cellpadding="0"
				cellspacing="0"
				align="center"
				class="fullTable"
				bgcolor="#ffffff"
				style="border-radius: 10px 10px 0 0"
			>
				<tr class="hiddenMobile">
					<td height="40"></td>
				</tr>
				<tr class="visibleMobile">
					<td height="30"></td>
				</tr>
				<tr>
					<td>
						<table
							width="480"
							border="0"
							cellpadding="0"
							cellspacing="0"
							align="center"
							class="fullPadding"
						>
							<tbody>
								<tr>
									<td>
										<table
											width="220"
											border="0"
											cellpadding="0"
											cellspacing="0"
											align="left"
											class="col"
										>
											<tbody>
												<tr>
													<td align="left">
														<img
															src=' . $data['logo_url'] . '
															width="120"
															alt="logo"
															border="0"
														/>
													</td>
												</tr>
												<!-- <tr class="hiddenMobile">
                                                    <td height="40"></td>
                                                </tr> -->
												<tr class="visibleMobile">
													<td height="20"></td>
												</tr>
												<tr>
													<td
														style="
															font-size: 12px;
															color: #5b5b5b;
															font-family: Open Sans, sans-serif;
															line-height: 18px;
															vertical-align: top;
															text-align: left;
														"
													>
														Hello, ' . $data['full_name'] . '
														<br />
														Thank you for participating in our program and for
														your payment
													</td>
												</tr>
											</tbody>
										</table>
										<table
											width="220"
											border="0"
											cellpadding="0"
											cellspacing="0"
											align="right"
											class="col"
										>
											<tbody>
												<tr class="visibleMobile">
													<td height="20"></td>
												</tr>
												<tr>
													<td height="5"></td>
												</tr>
												<tr>
													<td style="text-align: right">
														<span
															style="
																display: inline-block;
																font-size: 21px;
																color: #fff;
																letter-spacing: -1px;
																font-family: Open Sans, sans-serif;
																line-height: 1;
																vertical-align: middle;
																color: #377dff;
															"
															>Receipt</span
														>
														<br>
														<small>via Xendit</small>
													</td>
												</tr>
												<tr></tr>
												<tr class="hiddenMobile">
													<td height="50"></td>
												</tr>
												<tr class="visibleMobile">
													<td height="20"></td>
												</tr>
												<tr>
													<td
														style="
															font-size: 12px;
															color: #5b5b5b;
															font-family: Open Sans, sans-serif;
															line-height: 18px;
															vertical-align: top;
															text-align: right;
														"
													>
														<small>ORDER</small> #' . $data['external_id'] . '<br />
														<small>' . date('D, d M Y H:i:s', strtotime($data['created_at'])) . ' (GMT+0)</small>
													</td>
												</tr>
											</tbody>
										</table>
									</td>
								</tr>
							</tbody>
						</table>
					</td>
				</tr>
			</table>
		</td>
	</tr>
</table>
<!-- /Header -->
<!-- Order Details -->
<table
	width="100%"
	border="0"
	cellpadding="0"
	cellspacing="0"
	align="center"
	class="fullTable"
	bgcolor="#e1e1e1"
>
	<tbody>
		<tr>
			<td>
				<table
					width="600"
					border="0"
					cellpadding="0"
					cellspacing="0"
					align="center"
					class="fullTable"
					bgcolor="#ffffff"
				>
					<tbody>
						<tr></tr>
						<tr class="hiddenMobile">
							<td height="60"></td>
						</tr>
						<tr>
							<td>
								<table
									width="480"
									border="0"
									cellpadding="0"
									cellspacing="0"
									align="center"
									class="fullPadding"
								>
									<tbody>
										<tr>
											<td
												colspan="3"
												style="
													font-size: 14px;
													font-family: Open Sans, sans-serif;
													color: #5b5b5b;
													font-weight: normal;
													line-height: 1;
													vertical-align: top;
													padding: 0 10px 12px 0;
												"
											>
												Payment Details
											</td>
										</tr>
										<tr>
											<th
												style="
													font-size: 12px;
													font-family: Open Sans, sans-serif;
													color: #5b5b5b;
													font-weight: normal;
													line-height: 1;
													vertical-align: top;
													padding: 0 10px 7px 0;
												"
												width="52%"
												align="left"
											>
												Product
											</th>
											<th
												style="
													font-size: 12px;
													font-family: Open Sans, sans-serif;
													color: #5b5b5b;
													font-weight: normal;
													line-height: 1;
													vertical-align: top;
													padding: 0 0 7px;
												"
												align="center"
											>
												Quantity
											</th>
											<th
												style="
													font-size: 12px;
													font-family: Open Sans, sans-serif;
													color: #1e2b33;
													font-weight: normal;
													line-height: 1;
													vertical-align: top;
													padding: 0 0 7px;
												"
												align="right"
											>
												Subtotal
											</th>
										</tr>
										<tr>
											<td
												height="1"
												style="background: #bebebe"
												colspan="4"
											></td>
										</tr>
										<tr>
											<td height="10" colspan="4"></td>
										</tr>
										<tr>
											<td
												style="
													font-size: 12px;
													font-family: Open Sans, sans-serif;
													color: #1e2b33;
													line-height: 18px;
													vertical-align: top;
													padding: 10px 0;
												"
												class="article"
											>
												' . $data['description'] . '
											</td>
											<td
												style="
													font-size: 12px;
													font-family: Open Sans, sans-serif;
													color: #1e2b33;
													line-height: 18px;
													vertical-align: top;
													padding: 10px 0;
												"
												align="center"
											>
												1
											</td>
											<td
												style="
													font-size: 12px;
													font-family: Open Sans, sans-serif;
													color: #1e2b33;
													line-height: 18px;
													vertical-align: top;
													padding: 10px 0;
												"
												align="right"
											>
												' . $data['currency'] . ' ' . number_format($data['amount']) . '
											</td>
										</tr>
										<tr>
											<td
												height="1"
												colspan="4"
												style="border-bottom: 1px solid #e4e4e4"
											></td>
										</tr>
										<tr>
											<td
												height="1"
												colspan="4"
												style="border-bottom: 1px solid #e4e4e4"
											></td>
										</tr>
									</tbody>
								</table>
							</td>
						</tr>
						<tr>
							<td height="20"></td>
						</tr>
					</tbody>
				</table>
			</td>
		</tr>
	</tbody>
</table>
<!-- /Order Details -->
<!-- Total -->
<table
	width="100%"
	border="0"
	cellpadding="0"
	cellspacing="0"
	align="center"
	class="fullTable"
	bgcolor="#e1e1e1"
>
	<tbody>
		<tr>
			<td>
				<table
					width="600"
					border="0"
					cellpadding="0"
					cellspacing="0"
					align="center"
					class="fullTable"
					bgcolor="#ffffff"
				>
					<tbody>
						<tr>
							<td>
								<!-- Table Total -->
								<table
									width="480"
									border="0"
									cellpadding="0"
									cellspacing="0"
									align="center"
									class="fullPadding"
								>
									<tbody>
										<tr>
											<td
												style="
													font-size: 12px;
													font-family: Open Sans, sans-serif;
													color: #646a6e;
													line-height: 22px;
													vertical-align: top;
													text-align: right;
												"
											>
												Subtotal
											</td>
											<td
												style="
													font-size: 12px;
													font-family: Open Sans, sans-serif;
													color: #646a6e;
													line-height: 22px;
													vertical-align: top;
													text-align: right;
													white-space: nowrap;
												"
												width="80"
											>
                                            ' . $data['currency'] . ' ' . number_format($data['amount']) . '
											</td>
										</tr>
										<tr>
											<td
												style="
													font-size: 12px;
													font-family: Open Sans, sans-serif;
													color: #000;
													line-height: 22px;
													vertical-align: top;
													text-align: right;
												"
											>
												<strong>TOTAL</strong>
											</td>
											<td
												style="
													font-size: 12px;
													font-family: Open Sans, sans-serif;
													color: #000;
													line-height: 22px;
													vertical-align: top;
													text-align: right;
												"
											>
												<strong>' . $data['currency'] . ' ' . number_format($data['amount']) . '</strong>
											</td>
										</tr>
									</tbody>
								</table>
								<!-- /Table Total -->
							</td>
						</tr>
					</tbody>
				</table>
			</td>
		</tr>
	</tbody>
</table>
<!-- /Total -->
<!-- Information -->
<table
	width="100%"
	border="0"
	cellpadding="0"
	cellspacing="0"
	align="center"
	class="fullTable"
	bgcolor="#e1e1e1"
>
	<tbody>
		<tr>
			<td>
				<table
					width="600"
					border="0"
					cellpadding="0"
					cellspacing="0"
					align="center"
					class="fullTable"
					bgcolor="#ffffff"
				>
					<tbody>
						<tr></tr>
						<tr class="visibleMobile">
							<td height="40"></td>
						</tr>
						<tr>
							<td>
								<table
									width="480"
									border="0"
									cellpadding="0"
									cellspacing="0"
									align="center"
									class="fullPadding"
								>
									<tbody>
										<tr>
											<td>
												<table
													width="220"
													border="0"
													cellpadding="0"
													cellspacing="0"
													align="left"
													class="col"
												>
													<tbody>
														<tr class="visibleMobile">
															<td height="20"></td>
														</tr>
														<tr>
															<td
																style="
																	font-size: 11px;
																	font-family: Open Sans, sans-serif;
																	color: #5b5b5b;
																	line-height: 1;
																	vertical-align: top;
																"
															>
																<strong>Payment time and method</strong>
															</td>
														</tr>
														<tr>
															<td width="100%" height="10"></td>
														</tr>
														<tr>
															<td
																style="
																	font-size: 12px;
																	font-family: Open Sans, sans-serif;
																	color: #5b5b5b;
																	line-height: 20px;
																	vertical-align: top;
																"
															>
																' . $data['payment_method'] . '<br />
																<span
																	style="color: #5b5b5b"
																	>' . date('D, d M Y H:i:s', strtotime($data['updated_at'])) . ' (GMT+0)</span
																>
															</td>
														</tr>
														<tr>
															<td width="100%" height="10"></td>
														</tr>
														<tr>
															<td
																style="
																	font-size: 18px;
																	font-family: Open Sans, sans-serif;
																	color: #198754;
																	line-height: 20px;
																	vertical-align: top;
																"
															>
																PAID
															</td>
														</tr>
													</tbody>
												</table>
											</td>
										</tr>
									</tbody>
								</table>
							</td>
						</tr>
						<tr class="hiddenMobile">
							<td height="60"></td>
						</tr>
					</tbody>
				</table>
			</td>
		</tr>
	</tbody>
</table>
<!-- /Information -->
<table
	width="100%"
	border="0"
	cellpadding="0"
	cellspacing="0"
	align="center"
	class="fullTable"
	bgcolor="#e1e1e1"
>
	<tr>
		<td>
			<table
				width="600"
				border="0"
				cellpadding="0"
				cellspacing="0"
				align="center"
				class="fullTable"
				bgcolor="#ffffff"
				style="border-radius: 0 0 10px 10px"
			>
				<tr>
					<td>
						<table
							width="480"
							border="0"
							cellpadding="0"
							cellspacing="0"
							align="center"
							class="fullPadding"
						>
							<tbody>
								<tr>
									<td
										style="
											font-size: 10px;
											color: #5b5b5b;
											font-family: Open Sans, sans-serif;
											line-height: 18px;
											vertical-align: top;
											text-align: center;
										"
									>
										<strong>' . $data['name'] . ' - ' . $data['web_url'] . '</strong> -
										' . $data['email_program_category'] . ' (' . $data['contact'] . ')
									</td>
								</tr>
							</tbody>
						</table>
					</td>
				</tr>
				<tr class="spacer">
					<td height="50"></td>
				</tr>
			</table>
		</td>
	</tr>
	<tr>
		<td height="20"></td>
	</tr>
</table>');

		$this->load->library('email', $config);
		$this->email->set_mailtype("html");
		$this->email->set_newline("\r\n");
		$this->email->set_crlf("\r\n");
		$this->email->from(config_item('user_email'));
		$this->email->to($data['email']);
		$this->email->subject('Thank you for participating in ' . $data['name']);
		$this->email->message($message);

		if ($this->email->send()) {
			try {

				// PAYMENT
				$upd_payment = array(
					'status' => 2,
					'updated_at' => date('Y-m-d H:i:s'),
				);
				$this->mCore->save_data('payments', $upd_payment, true, ['id' => $data['payment_id']]);

				$status_pay = 0;
				if ($data['program_payment_name'] == 'Registration Fee (Early Bid)') {
					$status_pay = 1;
				} else if ($data['program_payment_name'] == 'Program Fee Batch 1') {
					$status_pay = 2;
				} else if ($data['program_payment_name'] == 'Program Fee Batch 2') {
					$status_pay = 3;
				}

				// PARTICIPANT STATUS
				$upd_payment = array(
					'payment_status' => $status_pay,
					'updated_at' => date('Y-m-d H:i:s'),
				);
				$this->mCore->save_data('participant_statuses', $upd_payment, true, ['participant_id' => $data['participant_id']]);

				$data_view = array(
					'logo_url' => $data['logo_url'],
					'web_url' => $data['web_url'],
					'id' => $data['external_id'],
					'date' => date('D, d M Y H:i:s', strtotime($data['updated_at'])),
					'currency' => $data['currency'],
					'amount' => number_format($data['amount']),
					'app' => 'Xendit'
				);

				$this->load->view("success_pay", $data_view);

				// $this->response([
				// 	'status' => true,
				// 	'message' => 'Successful payment!',
				// 	'data' => $result,
				// ], 200);
			} catch (\Xendit\XenditSdkException $e) {
				$this->response([
					'status' => false,
					'message' => 'Exception when calling InvoiceApi->getInvoiceById: ',
					$e->getMessage(),
					PHP_EOL,
				], 404);
				// echo 'Full Error: ', json_encode($e->getFullError()), PHP_EOL;
			}
		} else {

			$this->response([
				'status' => false,
				'message' => $this->email->print_debugger(),
			], 404);
			// $this->response([
			//     'status' => false,
			//     'message' => 'Payment error!',
			// ], 404);
		}
	}

	public function failure_pay_get()
	{

		$data = $this->mCore->get_data('payments', ['external_id' => $this->get('external_id')])->row();

		// PAYMENT
		$upd_payment = array(
			'status' => 3,
			'updated_at' => date('Y-m-d H:i:s'),
		);
		$this->mCore->save_data('payments', $upd_payment, true, ['id' => $data['payment_id']]);

		$this->response([
			'status' => false,
			'message' => 'Payment failed!',
		], 404);
	}

	// email manual
	public function invoice_send_post($id)
	{

		$option = array(
			'select' => 'payments.*, program_payments.name program_payment_name, users.full_name, users.email email_user, programs.name, programs.logo_url, payment_methods.name payment_method_name,
				program_categories.web_url,program_categories.contact,program_categories.email email_program_category',
			'table' => 'payments',
			'join' => [
				'program_payments' => 'program_payments.id = payments.program_payment_id',
				'payment_methods' => 'payment_methods.id = payments.payment_method_id',
				'participants' => 'participants.id = payments.participant_id',
				'users' => 'participants.user_id = users.id',
				'programs' => 'participants.program_id = programs.id',
				'program_categories' => 'programs.program_category_id = program_categories.id',
			],
			'where' => 'payments.id = ' . $id,
		);
		$data = $this->mCore->join_table($option)->row_array();

		// print_r($data);
		// die();

		// email
		$config = array(
			'protocol' => 'smtp',
			'smtp_host' => 'ssl://smtp.googlemail.com',
			'smtp_port' => config_item('port_email'),
			'smtp_user' => config_item('user_email'),
			'smtp_pass' => config_item('pass_email'),
			'mailtype' => 'html',
			'charset' => 'iso-8859-1',
			'wordwrap' => true,
		);

		$message = ('
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Order confirmation</title>
<meta name="robots" content="noindex,nofollow" />
<meta name="viewport" content="width=device-width; initial-scale=1.0;" />
<style type="text/css">
	@import url(https://fonts.googleapis.com/css?family=Open+Sans:400,700);

	body {
		margin: 0;
		padding: 0;
		background: #e1e1e1;
	}

	div,
	p,
	a,
	li,
	td {
		-webkit-text-size-adjust: none;
	}

	.ReadMsgBody {
		width: 100%;
		background-color: #ffffff;
	}

	.ExternalClass {
		width: 100%;
		background-color: #ffffff;
	}

	body {
		width: 100%;
		height: 100%;
		background-color: #e1e1e1;
		margin: 0;
		padding: 0;
		-webkit-font-smoothing: antialiased;
	}

	html {
		width: 100%;
	}

	p {
		padding: 0 !important;
		margin-top: 0 !important;
		margin-right: 0 !important;
		margin-bottom: 0 !important;
		margin-left: 0 !important;
	}

	.visibleMobile {
		display: none;
	}

	.hiddenMobile {
		display: block;
	}

	.rotateWm {
		transform: rotate(-45deg);
	}

	@media only screen and (max-width: 600px) {
		body {
			width: auto !important;
		}

		table[class="fullTable"] {
			width: 96% !important;
			clear: both;
		}

		table[class="fullPadding"] {
			width: 85% !important;
			clear: both;
		}

		table[class="col"] {
			width: 45% !important;
		}

		.erase {
			display: none;
		}
	}

	@media only screen and (max-width: 420px) {
		table[class="fullTable"] {
			width: 100% !important;
			clear: both;
		}

		table[class="fullPadding"] {
			width: 85% !important;
			clear: both;
		}

		table[class="col"] {
			width: 100% !important;
			clear: both;
		}

		table[class="col"] td {
			text-align: left !important;
		}

		.erase {
			display: none;
			font-size: 0;
			max-height: 0;
			line-height: 0;
			padding: 0;
		}

		.visibleMobile {
			display: block !important;
		}

		.hiddenMobile {
			display: none !important;
		}
	}

</style>

<!-- Header -->
<table
	width="100%"
	border="0"
	cellpadding="0"
	cellspacing="0"
	align="center"
	class="fullTable"
	bgcolor="#e1e1e1"
>
	<tr>
		<td height="20"></td>
	</tr>
	<tr>
		<td>
			<table
				width="600"
				border="0"
				cellpadding="0"
				cellspacing="0"
				align="center"
				class="fullTable"
				bgcolor="#ffffff"
				style="border-radius: 10px 10px 0 0"
			>
				<tr class="hiddenMobile">
					<td height="40"></td>
				</tr>
				<tr class="visibleMobile">
					<td height="30"></td>
				</tr>
				<tr>
					<td>
						<table
							width="480"
							border="0"
							cellpadding="0"
							cellspacing="0"
							align="center"
							class="fullPadding"
						>
							<tbody>
								<tr>
									<td>
										<table
											width="220"
											border="0"
											cellpadding="0"
											cellspacing="0"
											align="left"
											class="col"
										>
											<tbody>
												<tr>
													<td align="left">
														<img
															src=' . $data['logo_url'] . '
															width="120"
															alt="logo"
															border="0"
														/>
													</td>
												</tr>
												<!-- <tr class="hiddenMobile">
                                                    <td height="40"></td>
                                                </tr> -->
												<tr class="visibleMobile">
													<td height="20"></td>
												</tr>
												<tr>
													<td
														style="
															font-size: 12px;
															color: #5b5b5b;
															font-family: Open Sans, sans-serif;
															line-height: 18px;
															vertical-align: top;
															text-align: left;
														"
													>
														Hello, ' . $data['full_name'] . '
														<br />
														Thank you for participating in our program and for
														your payment
													</td>
												</tr>
											</tbody>
										</table>
										<table
											width="220"
											border="0"
											cellpadding="0"
											cellspacing="0"
											align="right"
											class="col"
										>
											<tbody>
												<tr class="visibleMobile">
													<td height="20"></td>
												</tr>
												<tr>
													<td height="5"></td>
												</tr>
												<tr>
													<td style="text-align: right">
														<span
															style="
																display: inline-block;
																font-size: 21px;
																color: #fff;
																letter-spacing: -1px;
																font-family: Open Sans, sans-serif;
																line-height: 1;
																vertical-align: middle;
																color: #377dff;
															"
															>Receipt</span
														>
													</td>
												</tr>
												<tr></tr>
												<tr class="hiddenMobile">
													<td height="50"></td>
												</tr>
												<tr class="visibleMobile">
													<td height="20"></td>
												</tr>
												<tr>
													<td
														style="
															font-size: 12px;
															color: #5b5b5b;
															font-family: Open Sans, sans-serif;
															line-height: 18px;
															vertical-align: top;
															text-align: right;
														"
													>
														<small>ORDER</small> #' . strtotime($data['created_at']) . $data['id'] . '<br />
														<small>' . date('D, d M Y H:i:s', strtotime($data['created_at'])) . ' (GMT+0)</small>
													</td>
												</tr>
											</tbody>
										</table>
									</td>
								</tr>
							</tbody>
						</table>
					</td>
				</tr>
			</table>
		</td>
	</tr>
</table>
<!-- /Header -->
<!-- Order Details -->
<table
	width="100%"
	border="0"
	cellpadding="0"
	cellspacing="0"
	align="center"
	class="fullTable"
	bgcolor="#e1e1e1"
>
	<tbody>
		<tr>
			<td>
				<table
					width="600"
					border="0"
					cellpadding="0"
					cellspacing="0"
					align="center"
					class="fullTable"
					bgcolor="#ffffff"
				>
					<tbody>
						<tr></tr>
						<tr class="hiddenMobile">
							<td height="60"></td>
						</tr>
						<tr>
							<td>
								<table
									width="480"
									border="0"
									cellpadding="0"
									cellspacing="0"
									align="center"
									class="fullPadding"
								>
									<tbody>
										<tr>
											<td
												colspan="3"
												style="
													font-size: 14px;
													font-family: Open Sans, sans-serif;
													color: #5b5b5b;
													font-weight: normal;
													line-height: 1;
													vertical-align: top;
													padding: 0 10px 12px 0;
												"
											>
												Payment Details
											</td>
										</tr>
										<tr>
											<th
												style="
													font-size: 12px;
													font-family: Open Sans, sans-serif;
													color: #5b5b5b;
													font-weight: normal;
													line-height: 1;
													vertical-align: top;
													padding: 0 10px 7px 0;
												"
												width="52%"
												align="left"
											>
												Product
											</th>
											<th
												style="
													font-size: 12px;
													font-family: Open Sans, sans-serif;
													color: #5b5b5b;
													font-weight: normal;
													line-height: 1;
													vertical-align: top;
													padding: 0 0 7px;
												"
												align="center"
											>
												Quantity
											</th>
											<th
												style="
													font-size: 12px;
													font-family: Open Sans, sans-serif;
													color: #1e2b33;
													font-weight: normal;
													line-height: 1;
													vertical-align: top;
													padding: 0 0 7px;
												"
												align="right"
											>
												Subtotal
											</th>
										</tr>
										<tr>
											<td
												height="1"
												style="background: #bebebe"
												colspan="4"
											></td>
										</tr>
										<tr>
											<td height="10" colspan="4"></td>
										</tr>
										<tr>
											<td
												style="
													font-size: 12px;
													font-family: Open Sans, sans-serif;
													color: #1e2b33;
													line-height: 18px;
													vertical-align: top;
													padding: 10px 0;
												"
												class="article"
											>
												' . $data['program_payment_name'] . '
											</td>
											<td
												style="
													font-size: 12px;
													font-family: Open Sans, sans-serif;
													color: #1e2b33;
													line-height: 18px;
													vertical-align: top;
													padding: 10px 0;
												"
												align="center"
											>
												1
											</td>
											<td
												style="
													font-size: 12px;
													font-family: Open Sans, sans-serif;
													color: #1e2b33;
													line-height: 18px;
													vertical-align: top;
													padding: 10px 0;
												"
												align="right"
											>
												' . $data['currency'] . ' ' . number_format($data['amount']) . '
											</td>
										</tr>
										<tr>
											<td
												height="1"
												colspan="4"
												style="border-bottom: 1px solid #e4e4e4"
											></td>
										</tr>
										<tr>
											<td
												height="1"
												colspan="4"
												style="border-bottom: 1px solid #e4e4e4"
											></td>
										</tr>
									</tbody>
								</table>
							</td>
						</tr>
						<tr>
							<td height="20"></td>
						</tr>
					</tbody>
				</table>
			</td>
		</tr>
	</tbody>
</table>
<!-- /Order Details -->
<!-- Total -->
<table
	width="100%"
	border="0"
	cellpadding="0"
	cellspacing="0"
	align="center"
	class="fullTable"
	bgcolor="#e1e1e1"
>
	<tbody>
		<tr>
			<td>
				<table
					width="600"
					border="0"
					cellpadding="0"
					cellspacing="0"
					align="center"
					class="fullTable"
					bgcolor="#ffffff"
				>
					<tbody>
						<tr>
							<td>
								<!-- Table Total -->
								<table
									width="480"
									border="0"
									cellpadding="0"
									cellspacing="0"
									align="center"
									class="fullPadding"
								>
									<tbody>
										<tr>
											<td
												style="
													font-size: 12px;
													font-family: Open Sans, sans-serif;
													color: #646a6e;
													line-height: 22px;
													vertical-align: top;
													text-align: right;
												"
											>
												Subtotal
											</td>
											<td
												style="
													font-size: 12px;
													font-family: Open Sans, sans-serif;
													color: #646a6e;
													line-height: 22px;
													vertical-align: top;
													text-align: right;
													white-space: nowrap;
												"
												width="80"
											>
                                            ' . $data['currency'] . ' ' . number_format($data['amount']) . '
											</td>
										</tr>
										<tr>
											<td
												style="
													font-size: 12px;
													font-family: Open Sans, sans-serif;
													color: #000;
													line-height: 22px;
													vertical-align: top;
													text-align: right;
												"
											>
												<strong>TOTAL</strong>
											</td>
											<td
												style="
													font-size: 12px;
													font-family: Open Sans, sans-serif;
													color: #000;
													line-height: 22px;
													vertical-align: top;
													text-align: right;
												"
											>
												<strong> ' . $data['currency'] . ' ' . number_format($data['amount']) . '</strong>
											</td>
										</tr>
									</tbody>
								</table>
								<!-- /Table Total -->
							</td>
						</tr>
					</tbody>
				</table>
			</td>
		</tr>
	</tbody>
</table>
<!-- /Total -->
<!-- Information -->
<table
	width="100%"
	border="0"
	cellpadding="0"
	cellspacing="0"
	align="center"
	class="fullTable"
	bgcolor="#e1e1e1"
>
	<tbody>
		<tr>
			<td>
				<table
					width="600"
					border="0"
					cellpadding="0"
					cellspacing="0"
					align="center"
					class="fullTable"
					bgcolor="#ffffff"
				>
					<tbody>
						<tr></tr>
						<tr class="visibleMobile">
							<td height="40"></td>
						</tr>
						<tr>
							<td>
								<table
									width="480"
									border="0"
									cellpadding="0"
									cellspacing="0"
									align="center"
									class="fullPadding"
								>
									<tbody>
										<tr>
											<td>
												<table
													width="220"
													border="0"
													cellpadding="0"
													cellspacing="0"
													align="left"
													class="col"
												>
													<tbody>
														<tr class="visibleMobile">
															<td height="20"></td>
														</tr>
														<tr>
															<td
																style="
																	font-size: 11px;
																	font-family: Open Sans, sans-serif;
																	color: #5b5b5b;
																	line-height: 1;
																	vertical-align: top;
																"
															>
																<strong>Payment time and method</strong>
															</td>
														</tr>
														<tr>
															<td width="100%" height="10"></td>
														</tr>
														<tr>
															<td
																style="
																	font-size: 12px;
																	font-family: Open Sans, sans-serif;
																	color: #5b5b5b;
																	line-height: 20px;
																	vertical-align: top;
																"
															>
																' . $data['payment_method_name'] . '<br />
																<span
																	style="color: #5b5b5b"
																	>' . date('D, d M Y H:i:s', strtotime($data['updated_at'])) . ' (GMT+0)</span
																>
															</td>
														</tr>
														<tr>
															<td width="100%" height="10"></td>
														</tr>
														<tr>
															<td
																style="
																	font-size: 18px;
																	font-family: Open Sans, sans-serif;
																	color: #198754;
																	line-height: 20px;
																	vertical-align: top;
																"
															>
																PAID
															</td>
														</tr>
													</tbody>
												</table>
											</td>
										</tr>
									</tbody>
								</table>
							</td>
						</tr>
						<tr class="hiddenMobile">
							<td height="60"></td>
						</tr>
					</tbody>
				</table>
			</td>
		</tr>
	</tbody>
</table>
<!-- /Information -->
<table
	width="100%"
	border="0"
	cellpadding="0"
	cellspacing="0"
	align="center"
	class="fullTable"
	bgcolor="#e1e1e1"
>
	<tr>
		<td>
			<table
				width="600"
				border="0"
				cellpadding="0"
				cellspacing="0"
				align="center"
				class="fullTable"
				bgcolor="#ffffff"
				style="border-radius: 0 0 10px 10px"
			>
				<tr>
					<td>
						<table
							width="480"
							border="0"
							cellpadding="0"
							cellspacing="0"
							align="center"
							class="fullPadding"
						>
							<tbody>
								<tr>
									<td
										style="
											font-size: 10px;
											color: #5b5b5b;
											font-family: Open Sans, sans-serif;
											line-height: 18px;
											vertical-align: top;
											text-align: center;
										"
									>
										<strong>' . $data['name'] . ' - ' . $data['web_url'] . '</strong> -
										' . $data['email_program_category'] . ' (' . $data['contact'] . ')
									</td>
								</tr>
							</tbody>
						</table>
					</td>
				</tr>
				<tr class="spacer">
					<td height="50"></td>
				</tr>
			</table>
		</td>
	</tr>
	<tr>
		<td height="20"></td>
	</tr>
</table>');

		$this->load->library('email', $config);
		$this->email->set_mailtype("html");
		$this->email->set_newline("\r\n");
		$this->email->set_crlf("\r\n");
		$this->email->from(config_item('user_email'));
		$this->email->to($data['email_user']);
		$this->email->subject('Thank you for participating in ' . $data['name']);
		$this->email->message($message);

		if ($this->email->send()) {
			$this->response([
				'status' => true,
				'message' => 'Email sent successfully!',
			], 200);
		} else {
			$this->response([
				'status' => false,
				'message' => $this->email->print_debugger(),
			], 404);
			// $this->response([
			//     'status' => false,
			//     'message' => 'Payment error!',
			// ], 404);
		}
	}

	//list
	public function xendit_list_get()
	{
		Configuration::setXenditKey(config_item('xendit'));

		$apiInstance = new PaymentMethodApi();
		try {
			$result = $apiInstance->getAllPaymentMethods();
			print_r($result);
		} catch (\Xendit\XenditSdkException $e) {
			echo 'Exception when calling PaymentMethodApi->getAllPaymentMethods: ', $e->getMessage(), PHP_EOL;
			echo 'Full Error: ', json_encode($e->getFullError()), PHP_EOL;
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
			'where' => 'payments.id = ' . $id,
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
			$ftp_config['debug'] = true;

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
			$ftp_config['debug'] = true;

			$this->ftp->connect($ftp_config);

			if ($this->ftp->list_files('payments/' . $data['program_id'] . '/') == false) {
				$this->ftp->mkdir('payments/' . $data['program_id'] . '/', DIR_WRITE_MODE, true);
				// $this->ftp->mkdir('payments/' . $data['program_id'] . '/' . $data['program_payment_id'] . '/', DIR_WRITE_MODE, true);
			}

			if ($this->ftp->list_files('payments/' . $data['program_id'] . '/' . $data['program_payment_id'] . '/') == false) {
				// $this->ftp->mkdir('payments/' . $data['program_id'] . '/', DIR_WRITE_MODE, true);
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

	// MIDTRANS
	public function pay_midtrans_post()
	{
		$data = [
			'id' => $this->post('id'),
			'price' => $this->post('price'),
			'name' => $this->post('name'),
			'description' => $this->post('description'),
			'participant_id' => $this->post('participant_id'),
			'program_id' => $this->post('program_id'),
			'program_payment_id' => $this->post('program_payment_id'),
			'payment_method_id' => $this->post('payment_method_id'),
		];

		$this->load->view('checkout_snap', $data);
	}
	
	public function payment_program_midtrans_get()
	{
		$program_id = $this->get('program_id');

		$option = array(
			'select' => 'payments.*, participants.full_name, participants.phone_number, participants.nationality, participants.gender, participants.institution,
				users.email, program_payments.program_id, program_payments.name program_payments_name, program_payments.description, 
				program_payments.start_date, program_payments.end_date, program_payments.order_number, program_payments.idr_amount, 
				program_payments.usd_amount, program_payments.category, payment_methods.name payment_methods_name, 
				payment_methods.type, payment_methods.img_url, midtrans_payment.order_id, midtrans_payment.transaction_status midtrans_status, midtrans_payment.payment_type midtrans_payment_method',
			'table' => 'payments',
			'join' => [
				['participants' => 'payments.participant_id = participants.id AND participants.is_active = 1'],
				['users' => 'participants.user_id = users.id AND users.is_active = 1'],
				['program_payments' => 'payments.program_payment_id = program_payments.id AND program_payments.is_active = 1'],
				['payment_methods' => 'payments.payment_method_id = payment_methods.id AND payment_methods.is_active = 1'],
				['midtrans_payment', 'payments.id = midtrans_payment.payment_id', 'left'],
			],
			'where' => ['program_payments.program_id' => $program_id, 'payments.is_active' => 1],
			'order' => ['payments.created_at' => 'desc']
		);
		$payments = $this->mCore->join_table($option)->result_array();

		if ($payments) {
			$this->response([
				'status' => true,
				'data' => $payments,
			], 200);
		} else {
			$this->response([
				'status' => false,
				'message' => 'No result were found',
			], 404);
		}
	}

	public function list_payment_midtrans_get()
	{

		$program_id = $this->get('program_id');

		$option = array(
			'select' => 'midtrans_payment.*, users.full_name, users.email email_user, programs.name, programs.logo_url, program_categories.web_url,
				program_categories.contact,program_categories.email email_program_category, program_payments.name program_payment_name',
			'table' => 'midtrans_payment',
			'join' => [
				'payments' => 'payments.id = midtrans_payment.payment_id',
				'program_payments' => 'program_payments.id = payments.program_payment_id',
				'payment_methods' => 'payment_methods.id = payments.payment_method_id',
				'participants' => 'participants.id = midtrans_payment.participant_id',
				'users' => 'participants.user_id = users.id',
				'programs' => 'midtrans_payment.program_id = programs.id',
				'program_categories' => 'programs.program_category_id = program_categories.id',
			],
			'where' => ['midtrans_payment.program_id' => $program_id],
			'order' => ['midtrans_payment.id' => 'asc']
		);

		$payments = $this->mCore->join_table($option)->result_array();

		if ($payments) {
			$this->response([
				'status' => true,
				'data' => $payments,
			], 200);
		} else {
			$this->response([
				'status' => false,
				'message' => 'No result were found',
			], 404);
		}
	}

	public function payment_midtrans_get()
	{
		$id = $this->get('id');

		$option = array(
			'select' => 'midtrans_payment.*, users.full_name, users.email email_user, programs.name, programs.logo_url, program_categories.web_url,
				program_categories.contact,program_categories.email email_program_category, program_payments.name program_payment_name',
			'table' => 'midtrans_payment',
			'join' => [
				'payments' => 'payments.id = midtrans_payment.payment_id',
				'program_payments' => 'program_payments.id = payments.program_payment_id',
				'payment_methods' => 'payment_methods.id = payments.payment_method_id',
				'participants' => 'participants.id = midtrans_payment.participant_id',
				'users' => 'participants.user_id = users.id',
				'programs' => 'midtrans_payment.program_id = programs.id',
				'program_categories' => 'programs.program_category_id = program_categories.id',
			],
			'where' => ['payments.id' => $id],
			'order' => ['midtrans_payment.id' => 'asc']
		);

		$payments = $this->mCore->join_table($option)->row_array();

		if ($payments) {
			$this->response([
				'status' => true,
				'data' => $payments,
			], 200);
		} else {
			$this->response([
				'status' => false,
				'message' => 'No result were found',
			], 404);
		}
	}
}
