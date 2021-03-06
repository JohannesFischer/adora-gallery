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
		$this->load->library('user_library');
		$this->load->model('album_model');

		$albums = $this->album_model->getAlbumDetails();
		$a = array();

		// check permissions
		foreach($albums as $album)
		{
			if($album['IsPublic'] || $this->user_library->isAdmin())
			{
				array_push($a, $album);	
			}
			else
			{
				$permitted = $this->album_model->getAlbumPermission($album['ID'], $this->session->userdata('user_id'));

				if($permitted)
				{
					array_push($a, $album);		
				}
			}	
		}	

		$data = array(
			'Albums' => $a,
			'ImageFolder' => $this->config->item('image_dir_resampled', 'gallery')
		);

		$this->index('ajax/box_albums', $data);
	}
	
	public function getHelp()
	{
		$this->index('ajax/box_help');
	}
    
    public function getNewImages()
    {
        $this->load->library('image_library');

        $data = array(
            'Files' => $this->image_library->getNewImages()
        );

        $this->index('ajax/admin_update_images', $data);
    }
	
	public function getInfo()
	{
		$this->load->model('photo_model');

		$src = $this->input->post('src');

		if($src)
		{
			$info = $this->photo_model->getInfo($src);

			//$date_format = $this->config->item('date_format', 'gallery');
			$date = explode(' ', $info->FileDateTime);

			$data = array(
				'Comments' => 0,
				'Date' => $date[0],
				'Description' => $info->Description,
				'Title' => $info->Title
			);

			$this->index('ajax/box_info', $data);
		}
	}
	
	public function setAlbumID()
	{
		$albumID = $this->input->post('id');
		
		$this->session->set_userdata(array('albumID' => $albumID));
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

		$this->load->library('image_library');
		$this->load->model(array('album_model', 'photo_model'));

		$post_data = json_decode($this->input->post('data'), true);
		$post_data['Status'] = 1;

		$filename = $post_data['Filename'];
		$source_image = $this->config->item('image_folder', 'gallery').$post_data['Filename'];

		$this->load->library('image_lib');

		// Rotate Image
		if($post_data['Orientation'] > 1)
		{
			$source_image = $this->image_library->rotateImage($filename, $source_image, $post_data['Orientation']);
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
			'image_library' => $this->config->item('image_library', 'gallery'),
			'library_path' => $this->config->item('library_path', 'gallery'),
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

		$fn = explode('.', $post_data['Filename']);

		$post_data['Filename_Large'] = $fn[0].$config_large['thumb_marker'].'.'.$fn[1];
		$post_data['Filename_Thumbnail'] = $fn[0].$config_thumbnail['thumb_marker'].'.'.$fn[1];

		$i = -1;
		$keys = array_keys($post_data);

		unset($post_data['Orientation']);

		$post_data['Created'] = date('Y-m-d h:m:i');

		// store Album ID
		$album_id = $post_data['Album'];
		unset($post_data['Album']);

		$photo_id = $this->photo_model->addPhoto($post_data);
		
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

		//$this->load->library('image_library');

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
		$this->load->library('image_library');
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
				'image_library' => $this->config->item('image_library', 'gallery'),
				'library_path' => $this->config->item('library_path', 'gallery'),
				'source_image' => $source_image
			);

            if(isset($exif_data['Orientation']))
            {
                if($exif_data['Orientation'] > 1)
                {
                    $source_image = $this->image_library->rotateImage($filename, $source_image, $exif_data['Orientation']);
                }
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
			'fileDateTime' => $this->image_library->getDateTime($exif_data),
			'file' => $this->config->item('image_dir_resampled', 'gallery').$fn[0].$thumb_marker.'.'.$fn[1],
			'orientation' => $exif_data['Orientation'],
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