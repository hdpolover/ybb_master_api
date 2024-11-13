<?php if (! defined('BASEPATH')) exit('No direct script access allowed');

class Snap extends CI_Controller
{

	/**
	 * Index Page for this controller.
	 *
	 * Maps to the following URL
	 * 		http://example.com/index.php/welcome
	 *	- or -  
	 * 		http://example.com/index.php/welcome/index
	 *	- or -
	 * Since this controller is set as the default controller in 
	 * config/routes.php, it's displayed at http://example.com/
	 *
	 * So any other public methods not prefixed with an underscore will
	 * map to /index.php/welcome/<method_name>
	 * @see http://codeigniter.com/user_guide/general/urls.html
	 */


	public function __construct()
	{
		// iki dev		
		parent::__construct();
		$params = array('server_key' => config_item('server_key'), 'production' => false);
		$this->load->library('midtrans');
		$this->midtrans->config($params);
		$this->load->helper('url');
	}

	public function pay_midtrans()
	{
		$data = [
			'id' => 'a1',
			'price' => '100000',
			'description' => 'PEMBAYARAN',
			'name' => 'IVAL AKUDEWE',
			'email' => 'AKUDEWE@GMAIL.COM',
			'phone' => '081823',
			'participant_id' => '4',
			'payment_id' => '10000',
			'program_id' => '3',
			'program_payment_id' => '7',
			'payment_method_id' => '7',
		];

		$this->load->view('checkout_snap', $data);
	}

	public function token()
	{
		$id = time() . rand(0, 100);
		// Required
		$transaction_details = array(
			'order_id' => $id,
			'gross_amount' => $this->input->post('price'), // no decimal allowed for creditcard
		);

		// Optional

		$item1_details = array(
			'id' => $this->input->post('id'),
			'price' => $this->input->post('price'),
			'quantity' => 1,
			'name' => $this->input->post('description')
		);

		// Optional
		$item_details = array($item1_details);

		// Optional
		$customer_details = array(
			'first_name'    => $this->input->post('name'),
			'email'         => $this->input->post('email'),
			'phone'         => $this->input->post('phone'),
		);

		// Data yang akan dikirim untuk request redirect_url.
		$credit_card['secure'] = true;
		//ser save_card true to enable oneclick or 2click
		//$credit_card['save_card'] = true;

		$time = time();
		$custom_expiry = array(
			'start_time' => date("Y-m-d H:i:s O", $time),
			'unit' => 'hour',
			'duration'  => 1
		);

		$transaction_data = array(
			'transaction_details' => $transaction_details,
			'item_details'       => $item_details,
			'customer_details'   => $customer_details,
			'credit_card'        => $credit_card,
			'expiry'             => $custom_expiry
		);

		error_log(json_encode($transaction_data));
		// insert
		// tabel payment
		$data_payment = array(
			'participant_id' => $this->input->post('participant_id'),
			'program_payment_id' => $this->input->post('program_payment_id'),
			'payment_method_id' => $this->input->post('payment_method_id'),
			'status' => 1,
			'account_name' => $this->input->post('name'),
			'amount' => $this->input->post('price'),
			'source_name' => $this->input->post('email'),
			'created_at' => date('Y-m-d H:i:s'),
			'updated_at' => date('Y-m-d H:i:s'),
		);

		$sql = $this->mCore->save_data('payments', $data_payment);
		if (!$sql) {
			echo "<BR><BR>";
			print_r($this->db->error());
			echo "<BR><BR>";
			die();
		}
		$last_id = $this->mCore->get_lastid('payments', 'id');

		$tambahan = $custom_expiry['duration'] . ' ' . $custom_expiry['unit'];
		$data_midtrans = array(
			'participant_id' => $this->input->post('participant_id'),
			'payment_id' => $last_id,
			'program_id' => $this->input->post('program_id'),
			'description' => $this->input->post('description'),
			'gross_amount' => $this->input->post('price'),
			'order_id' => $id,
			'expired_at' => date('Y-m-d H:i:s', strtotime($custom_expiry['start_time'] . '+' . $tambahan)),
			'created_at' => date('Y-m-d H:i:s'),
			'updated_at' => date('Y-m-d H:i:s'),
		);

		$sql = $this->mCore->save_data('midtrans_payment', $data_midtrans);
		if (!$sql) {
			echo "<BR><BR>";
			print_r($this->db->error());
			echo "<BR><BR>";
			die();
		}

		$snapToken = $this->midtrans->getSnapToken($transaction_data);

		error_log($snapToken);
		echo $snapToken;
	}

	public function finish()
	{
		$result = json_decode($this->input->post('result_data'), true);

		// tabel payment
		$data_payment = array(
			'participant_id' => $this->input->post('participant_id'),
			'program_payment_id' => $this->input->post('program_payment_id'),
			'payment_method_id' => $this->input->post('payment_method_id'),
			'status' => 1,
			'account_name' => $this->input->post('account_name'),
			'amount' => $this->input->post('amount'),
			'source_name' => $result['payer_email'],
			'created_at' => date('Y-m-d H:i:s'),
			'updated_at' => date('Y-m-d H:i:s'),
		);

		$this->mCore->save_data('payments', $data_payment);

		$last_id = $this->mCore->get_lastid('payments', 'id');

		$data_midtrans = array(
			'participant_id' => $this->input->post('participant_id'),
			'payment_id' => $last_id,
			'program_id' => $this->input->post('program_id'),
			'gross_amount' => $result['gross_amount'],
			'payment_type' => $result['payment_type'],
			'transaction_time' => $result['transaction_time'],
			'status_code' => $result['status_code'],
			'transaction_status' => $result['transaction_status'],
			'order_id' => $result['order_id'],
			'transaction_id' => $result['transaction_id'],
			'bank' => $result['va_numbers'][0]['bank'],
			'va_number' => $result['va_numbers'][0]['va_number'],
			'pdf_url' => $result['pdf_url'],
			'finish_redirect_url' => $result['finish_redirect_url'],
		);

		$sql = $this->mCore->save_data('midtrans_payment', $data_midtrans);

		if ($sql) {
			echo "SUKSES";
		} else {
			echo "GAGAL";
		}
	}
}
