<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class page extends CI_Controller {

	public function index()
	{
		$this->load->view('welcome_message');
	}

	public function error()
	{
		$data = array(
            'icon'    => 'fas fa-fw text-primary fa-bug',
			'title' => '404 Not Found'
		);

		$this->load->view('errors/index', $data);

	}
}