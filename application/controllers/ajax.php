<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Ajax extends CI_Controller {

	private $Loggedin;

	public function __construct()
	{
		parent::__construct();

		if (!$this->input->isAjax())
		{
			return;
		}

		$this->config->load('gallery', true);

		$this->Loggedin = $this->session->userdata('loggedin');
	}

	/**
	 * Private
	 */
	
	private function _isAdmin()
	{
		return $this->session->userdata('role') == 'Admin';
	}

	/**
	 * Gallery
	 */
	
	public function getAlbums()
	{
		$this->load->model('album_model');

		$albums = $this->album_model->getAlbumDetails();

		$data = array(
			'Albums' => $albums,
			'ImageFolder' => $this->config->item('image_dir_resampled', 'gallery')
		);

		$this->index('ajax/box_albums', $data);
	}
	
	public function getHelp()
	{
		$this->index('ajax/box_help');
	}
	
	public function getInfo()
	{
		$this->load->model('photo_model');

		$src = $this->input->post('src');

		if($src)
		{
			$info = $this->photo_model->getInfo($src);

			$date_format = $this->config->item('date_format', 'gallery');

			$data = array(
				'Comments' => 0,
				'Date' => date($date_format, $info->FileDateTime),
				'Description' => $info->Description,
				'Title' => $info->Title
			);

			$this->index('ajax/box_info', $data);
		}
	}
	
	public function setAlbumID()
	{
		$albumID = $this->input->post('id');
		
		$this->session->set_userdata(array('albumID', $albumID));
	}
	
	/**
	 * Login
	 */
	
	public function login()
	{
		$this->load->library('login_library');

		$password = $this->input->post('password');
		$username = $this->input->post('username');

		$data = $this->login_library->login($username, $password);	
		
		$this->index('ajax', array(
			'json' => true,
			'response' => json_encode($data)
		));
	}
	
	public function logout()
	{
		return $this->session->sess_destroy();
	}
	
	/**
	 * Admin
	 */
	
	public function addGallery()
	{
		if(!$this->_isAdmin() || !$this->Loggedin)
		{
			return false;
		}
		
		$title = $this->input->post('title');
		
		if($title)
		{
			$this->load->model('album_model');
			
			$this->album_model->createAlbum($title);
		}
	}
	
	public function addPhoto()
	{
		if(!$this->_isAdmin() || !$this->Loggedin)
		{
			return false;
		}

		$this->load->model(array('album_model', 'photo_model'));

		$data = json_decode($this->input->post('data'), true);
		$data['Status'] = 1;

		$filename = $data['Filename'];
		$source_image = $this->config->item('image_folder', 'gallery').$data['Filename'];

		$this->load->library('image_lib');

		// Rotate Image
		if($data['Orientation'] > 1)
		{
			// TODO put this into a helper since its used twice
			if($exif_data['Orientation'] == 6)
			{
				$rotation_angle = 270;
			}
			else if($exif_data['Orientation'] == 8)
			{
				$rotation_angle = 90;
			}

			$config = array(
				'image_library' => 'gd2',
				'new_image' => $this->config->item('image_folder_resampled', 'gallery').$filename,
				'rotation_angle' => $rotation_angle,
				'source_image' => $source_image
			);

			$this->image_lib->initialize($config); 

			$rotate = $this->image_lib->rotate();
			
			if(!$rotate)
			{
				echo $this->image_lib->display_errors();
			}
			
			$source_image = $this->config->item('image_folder_resampled', 'gallery').$filename;
			
			$this->image_lib->clear();
		}

		// Resize Image
		$size = array(
			'large' => array(
				'height' => 600,
				'width' => 900
			),
			'thumbnail' => array(
				'height' => 100,
				'width' => 100
			)
		);

		$config = array(
			'create_thumb' => true,
			'image_library' => 'gd2',
			'maintain_ratio' => true,
			'new_image' => $this->config->item('image_folder_resampled', 'gallery').$filename,
			'source_image' => $source_image
		);

		$config_large = array(
			'height' => $size['large']['height'],
			'thumb_marker' => '_large',
			'width' => $size['large']['width']
		);

		$this->image_lib->initialize(array_merge($config, $config_large)); 

		$resize_large = $this->image_lib->resize();

		if(!$resize_large)
		{
			echo $this->image_lib->display_errors();
		}

		$this->image_lib->clear();

		$config_thumbnail = array(
			'height' => $size['thumbnail']['height'],
			'thumb_marker' => '_thumbnail',
			'width' => $size['thumbnail']['width']
		);

		$this->image_lib->initialize(array_merge($config, $config_thumbnail)); 

		$resize_thumbnail = $this->image_lib->resize();

		if(!$resize_thumbnail)
		{
			echo $this->image_lib->display_errors();
		}

		$fn = explode('.', $data['Filename']);

		$data['Filename_Large'] = $fn[0].$config_large['thumb_marker'].'.'.$fn[1];
		$data['Filename_Thumbnail'] = $fn[0].$config_thumbnail['thumb_marker'].'.'.$fn[1];

		$i = -1;
		$keys = array_keys($data);

		unset($data['Orientation']);

		$data['Created'] = date('Y-m-d h:m:i');

		// store Album ID
		$album_id = $data['Album'];
		unset($data['Album']);

		$photo_id = $this->photo_model->addPhoto($data);
		
		// get photo ID
		$this->album_model->addPhotoToGallery($album_id, $photo_id);

		$this->index('ajax');
	}

	public function editUser()
	{
		if(!$this->_isAdmin() || !$this->Loggedin)
		{
			return false;
		}

		$this->load->helper(array('file','html'));
		$this->load->model('user_model');

		$user_id = $this->input->post('id');

		if($user_id)
		{
			$data = $this->user_model->getUserDetails($user_id);

			$buffer = array();

			$icons = get_filenames($this->config->item('user_icon_folder', 'gallery'));

			foreach($icons as $icon)
			{
				$buffer[] = $this->config->item('user_icon_folder', 'gallery').$icon;
			}

			$data['Icons'] = $buffer;
			unset($buffer);

			$this->index('ajax/admin_form_user', $data);
		}
	}
	
	public function getAjaxView()
	{
		if(!$this->_isAdmin() || !$this->Loggedin)
		{
			return false;
		}
		
		$view = $this->input->post('view');

		if($view)
		{
			$this->index('ajax/'.$view);
		}
	}
	
	public function getGalleries()
	{
		if(!$this->_isAdmin() || !$this->Loggedin)
		{
			return false;
		}

		$this->load->model('album_model');
		
		$this->index('ajax/admin_galleries', $data = array('Galleries' => $this->album_model->getAlbums()));
	}
	
	public function getImageForm()
	{
		if(!$this->_isAdmin() || !$this->Loggedin)
		{
			return false;
		}

		$this->load->helper('html');
		$this->load->model('album_model');

		$filename = $this->input->post('file');
		$fn = explode('.', $filename);
		$source_image = $this->config->item('image_folder', 'gallery').$filename;
		$thumb_marker = '_preview';
		
		// todo error handling
		$exif_data = exif_read_data($this->config->item('image_folder', 'gallery').$filename);

		if(!file_exists($this->config->item('image_folder_resampled', 'gallery').$fn[0].$thumb_marker.'.'.$fn[1]))
		{
			$this->load->library('image_lib');

			$config = array(
				'image_library' => 'gd2',
				'source_image' => $source_image
			);

			if($exif_data['Orientation'] > 1)
			{
				if($exif_data['Orientation'] == 6)
				{
					$rotation_angle = 270;
				}
				else if($exif_data['Orientation'] == 8)
				{
					$rotation_angle = 90;
				}
				
				$config_rotate = array(
					'new_image' => $this->config->item('image_folder_resampled', 'gallery').$filename,
					'rotation_angle' => $rotation_angle
				);

				$this->image_lib->initialize(array_merge($config, $config_rotate));
				
				if(!$this->image_lib->rotate())
				{
					echo $this->image_lib->display_errors();
				}

				$this->image_lib->clear();

				$source_image = $this->config->item('image_folder_resampled', 'gallery').$filename;
			}
			
			$config_thumbnail = array(
				'create_thumb' => true,
				'height' => 250,
				'maintain_ratio' => true,
				'new_image' => $this->config->item('image_folder_resampled', 'gallery').$filename,
				'source_image' => $source_image,
				'thumb_marker' => '_preview',
				'width' => 250
			);
	
			$this->image_lib->initialize(array_merge($config, $config_thumbnail)); 
	
			$resize = $this->image_lib->resize();
	
			if(!$resize)
			{
				$this->image_lib->display_errors();
			}
		}

		$albums = $this->album_model->getAlbums();

		$data = array(
			'Albums' => $albums,
			'exif' => $exif_data,
			'file' => $this->config->item('image_dir_resampled', 'gallery').$fn[0].$thumb_marker.'.'.$fn[1],
			'source_file' => $filename
		);

		$this->index('includes/admin_update_form', $data);
	}
	
	public function deleteImage()
	{
		if(!$this->_isAdmin() || !$this->Loggedin)
		{
			return false;
		}

		$file = $this->input->post('filename');
		
		if($file)
		{
			// delete original image
			unlink($this->config->item('image_folder', 'gallery').$file);
			// delete preview thumbnail
			$file_parts = explode('.', $file);
			unlink($this->config->item('image_folder_resampled', 'gallery').$file_parts[0].$this->config->item('preview_marker', 'gallery').'.'.$file_parts[1]);
		}
	}
	
	public function updateUser()
	{
		if(!$this->_isAdmin() || !$this->Loggedin)
		{
			return false;
		}

		$this->load->model('user_model');

		$data = json_decode($this->input->post('data'), true);

		$this->user_model->update($data, $data['ID']);
	}
	
	public function index($view, $data = array())
	{
		$view = 'web/'.$view;
		$this->load->view($view, $data);
	}

}

?>