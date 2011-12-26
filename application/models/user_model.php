<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

    class User_model extends CI_Model {

        public function __construct()
        {
			parent::__construct();
			$this->user_table = 'User';
        }
		
		public function getUser()
		{
			$this->db->select();
			$this->db->from($this->user_table);
			$this->db->order_by('username');

			$query = $this->db->get();
			
			return $query->result();
		}
		
		public function getUserDetails($id)
		{
			$this->db->select();
			$this->db->from($this->user_table);
			$this->db->where('ID', $id);

			$query = $this->db->get();
			
			return $query->row_array();
		}
		
		public function login($username, $password)
		{
			$this->db->select('Email, Icon, ID, Last_Login, Login_Text, Role, Username');
			$this->db->from($this->user_table);
			$this->db->where('username', $username);
			$this->db->where('password', $password);

			$query = $this->db->get();

			if($query->num_rows() == 1)
			{
				$this->updateLastLogin();
				return $query->row();
			}

			return false;
		}

		public function update($data, $id)
		{
			return $this->db->update($this->user_table, $data, array('id' => $id));
		}

		public function updateLastLogin()
		{
			$this->db->update($this->user_table, array('Last_Login' => date('Y-m-d h:i:s')));
		}

    }

?>