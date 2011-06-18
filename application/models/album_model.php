<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

    class Album_model extends CI_Model {

		private $album_table;
		private $album_photos_table;

        public function __construct()
        {
			parent::__construct();
			$this->album_table = 'Albums';
			$this->album_photos_table = 'Album_photos';
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
		
		public function getAlbums()
		{
			$this->db->select();
			$this->db->from($this->album_table);
			$this->db->order_by('Title');

			$query = $this->db->get();

			return $query ? $query->result_array() : false;
		}

    }

?>