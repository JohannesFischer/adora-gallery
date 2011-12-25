<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

    class Album_model extends CI_Model {

		private $album_table;
		private $album_photos_table;

        public function __construct()
        {
			parent::__construct();
			$this->album_table = 'Albums';
			$this->album_photos_table = 'Album_photos';
			$this->photos_table = 'Photos';
        }
		
		public function addPhotoToGallery($album_id, $photo_id)
		{
			// TODO get max_sort
			return $this->db->insert($this->album_photos_table, array(
				'Album_ID' => $album_id,
				'Photo_ID' => $photo_id,
				'Sort' => 0
			));
		}
		
		public function createAlbum($title)
		{
			return $this->db->insert($this->album_table, array('Title' => $title, 'OrderBy' => 'default'));
		}
		
		public function getAlbumDetails()
		{
			$this->db->select($this->album_table.'.ID, '.$this->album_table.'.Title, '.$this->photos_table.'.Filename_Thumbnail');
			$this->db->select('COUNT('.$this->album_table.'.ID) AS Photos');
			$this->db->from($this->album_table);
			$this->db->join($this->album_photos_table, $this->album_table.'.ID = '.$this->album_photos_table.'.Album_ID');
			$this->db->join($this->photos_table, $this->album_photos_table.'.Photo_ID = '.$this->photos_table.'.ID');
			$this->db->group_by($this->album_table.'.ID');
			$this->db->order_by('Title, '.$this->photos_table.'.FileDateTime ASC');

			$query = $this->db->get();

			return $query ? $query->result_array() : false;
		}
		
		public function getAlbumOrder($albumID)
		{
			$this->db->select('OrderBy');
			$this->db->from($this->album_table);
			$this->db->where('ID', $albumID);

			$query = $this->db->get();

			$row = $query->row();

			return $row ? $row->OrderBy : false;
		}
		
		public function getAlbums()
		{
			$this->db->select();
			$this->db->from($this->album_table);
			$this->db->order_by('Title');

			$query = $this->db->get();

			return $query ? $query->result_array() : false;
		}
		
		public function getLatestAlbum()
		{
			$this->db->select('ID');
			$this->db->from($this->album_table);
			$this->db->order_by('Created ASC');

			$query = $this->db->get();

			$row = $query->row();

			return $row ? $row->ID : false;
		}

    }

?>