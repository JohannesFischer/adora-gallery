<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Login extends CI_Controller {


	public function __construct()
	{
		parent::__construct();

		$this->load->library('login_library');

		$password = md5($this->input->post('password'));
		$username = $this->input->post('username');

		$data = $this->login_library->login($username, $password);	

		redirect('gallery');
	}

}
?>