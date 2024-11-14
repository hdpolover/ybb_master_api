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
			'email' => 'ronoroakid@gmail.com',
			'phone' => '081823',
			'participant_id' => '4',
			'program_id' => '3',
			'program_payment_id' => '1',
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
			'currency' => 'IDR',
			'gross_amount' => $this->input->post('price'),
			'email' => $this->input->post('email'),
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
		// dari midtrans
		$result = json_decode($this->input->post('result_data'), true);

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
			'where' => 'midtrans_payment.order_id = ' . $result['order_id'],
		);

		$data = $this->mCore->join_table($option)->row_array();

		// TABEL PEMBANTU
		$upd = array(
			'payment_type' => $result['payment_type'],
			'transaction_time' => $result['transaction_time'],
			'status_code' => $result['status_code'],
			'transaction_status' => $result['transaction_status'],
			'transaction_id' => $result['transaction_id'],
			'finish_redirect_url' => $result['finish_redirect_url'],
			'updated_at' => date('Y-m-d H:i:s', strtotime($result['transaction_time'])),
		);
		
		if($result['payment_type'] == 'bank'){
			$upd['bank'] = $result['va_numbers'][0]['bank'];
			$upd['va_number'] = $result['va_numbers'][0]['va_number'];
			$upd['pdf_url'] = $result['pdf_url'];
		}

		$this->mCore->save_data('midtrans_payment', $upd, true, ['order_id' => $result['order_id']]);

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
														<small>ORDER</small> #' . $data['order_id'] . '<br />
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
												' . $data['currency'] . ' ' . number_format($data['gross_amount']) . '
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
                                            ' . $data['currency'] . ' ' . number_format($data['gross_amount']) . '
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
												<strong>' . $data['currency'] . ' ' . number_format($data['gross_amount']) . '</strong>
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
																' . ucfirst(str_replace('_', ' ',$data['payment_type'])) . '<br />
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
				'id' => $data['order_id'],
				'date' => date('D, d M Y H:i:s', strtotime($data['updated_at'])),
				'currency' => $data['currency'],
				'amount' => number_format($data['gross_amount']),
				'app' => 'Midtrans'
			);

			$this->load->view("success_pay", $data_view);
		} else {
			$this->load->view("error_pay");
		}
	}
}
