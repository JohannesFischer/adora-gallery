<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

    class User_model extends CI_Model {

        public function __construct()
        {
			parent::__construct();
			$this->user_table = 'Users';
        }
		
		public function getUser()
		{
			$this->db->select();
			$this->db->from($this->user_table);
			$this->db->order_by('username');

			$query = $this->db->get();
			
			return $query->result();
		}
		
		public function login($username, $password)
		{
			$this->db->select('Email, Icon, Last_Login, Username');
			$this->db->from($this->user_table);
			$this->db->where('username', $username);
			$this->db->where('password', $password);

			$query = $this->db->get();

			if($query->num_rows() == 1)
			{
				return $query->row_array();
			}

			return false;
		}

    }

?>