<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

    class Photo_model extends CI_Model {

        public function __construct()
        {
			parent::__construct();
			$this->album_photo_table = 'Album_photos';
			$this->photo_table = 'Photos';
        }

		public function addPhoto($data)
		{
			$this->db->insert($this->photo_table, $data);

			return $this->db->insert_id();
		}

        public function getAll($order_by, $status = 1)
        {
            $this->db->from($this->photo_table);
			$this->db->select('Created, Filename_Large, Filename_Thumbnail, Title');
			$this->db->where('status', $status);
			$this->db->order_by($order_by);

			$query = $this->db->get();
	
			return $query ? $query->result_array() : false;
        }
        
        public function getFilenames()
        {
            $this->db->from($this->photo_table);
			$this->db->select('Filename');
			$this->db->order_by('Created, Filename');

			$query = $this->db->get();
			
			$files = array();
			
			foreach($query->result() as $file)
			{
				array_push($files, $file->Filename);
			}
	
			return $files;
        }
		
		public function getFromAlbum($albumID, $order_by, $status = 1)
        {
            $this->db->from($this->photo_table);
			$this->db->select('Created, Filename_Large, Filename_Thumbnail, Title');
			$this->db->join($this->album_photo_table, $this->album_photo_table.'.Photo_ID = '.$this->photo_table.'.ID');
			$this->db->where($this->album_photo_table.'.Album_ID', $albumID);
			$this->db->where($this->photo_table.'.status', $status);
			$this->db->order_by($order_by);

			$query = $this->db->get();
	
			return $query ? $query->result_array() : false;
        }

		public function getInfo($src)
		{
			$this->db->from($this->photo_table);
			$this->db->select('Title, Description, FileDateTime');
			$this->db->where('Filename_Large', $src);

			$query = $this->db->get();

			return $query->num_rows() > 0 ? $query->row() : false;
		}

        public function getLatest()
        {
			$sql = "SELECT Created, Filename, Title, MAX(Created) AS Created_Max FROM ".$this->photo_table." HAVING MAX(Created) ORDER BY Filename ";
			//$this->db->select('Created, Filename, Title');
			//$this->db->select_max('Created', 'Created_Max');
			//$this->db->Having('Created', 'MAX(Created)');
			//$this->db->order_by('Filename');

			$query = $this->db->query($sql);
	
			$photos = array();

			foreach ($query->result_array() as $row)
			{
				$b = array();
				foreach($row as $key => $value)
				{
					$b[$key] = $value;
				}
				$photos[] = $b;
				unset($b);
			}

			return $photos;
        }

    }

?>