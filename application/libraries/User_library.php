<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

    class User_library {

        private $CI;

        public function __construct()
        {
            $this->CI =& get_instance();
        }

		public function isAdmin()
		{
			return $this->CI->session->userdata('role') == 'Admin';
		}

	}
?>