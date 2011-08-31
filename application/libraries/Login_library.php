<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

    class Login_library {

        private $CI;

        public function __construct()
        {
            $this->CI =& get_instance();
        }

		public function login($username, $password)
		{
			$this->CI->lang->load('basic', 'english');
			$this->CI->load->model('user_model');
	
			$login = $this->CI->user_model->login($username, $password);

			if($login)
			{
				$data = array(
					'icon' => base_url().$this->CI->config->item('user_icon_folder', 'gallery').$login->Icon,
					'loggedin' => true,
					'role' => $login->Role,
					'username' => $login->Username
				);
		
				$login_text = $login->Login_Text != '' ? $login->Login_Text : $this->lang->line('login_welcome');
	
				$data['username'] = str_replace('%U', $data['username'], $login_text);
		
				$this->CI->session->set_userdata($data);
	
				$this->CI->user_model->updateLastLogin();
			}
			else
			{
				$data = array(
					'error' => 'Invalid login'
				);
			}
	
			return $data;
		}

	}
?>