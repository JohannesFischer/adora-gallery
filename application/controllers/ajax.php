<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Ajax extends CI_Controller {

	private $Loggedin;
	private $Role;

	public function __construct()
	{
		parent::__construct();

		$this->config->load('gallery', true);

		$this->Loggedin = $this->session->userdata('loggedin');
		$this->Role = $this->session->userdata('role');
	}

	/**
	 * Gallery
	 */
	
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

			$this->index($data, 'ajax/infobox');
		}
	}
	
	/**
	 * Login
	 */
	
	public function login()
	{
		$this->lang->load('basic', 'english');
		$this->load->model('user_model');

		$password = md5($this->input->post('password'));
		$username = $this->input->post('username');

		$login = $this->user_model->login($username, $password);

		if($login)
		{
			$data = array(
				'icon' => base_url().$this->config->item('user_icon_folder', 'gallery').$login->Icon,
				'loggedin' => true,
				'role' => $login->Role,
				'username' => $login->Username
			);
	
			$login_text = $login->Login_Text != '' ? $login->Login_Text : $this->lang->line('login_welcome');

			$data['username'] = str_replace('%U', $data['username'], $login_text);
	
			$this->session->set_userdata($data);

			$this->user_model->updateLastLogin();
		}
		else
		{
			$data = array(
				'error' => 'Invalid login'
			);
		}

		$this->index(array(
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
	
	public function addPhoto()
	{
		$this->load->model('photo_model');

		$data = json_decode($this->input->post('data'), true);

		$filename = $data['Filename'];
		$source_image = $this->config->item('image_folder', 'gallery').$data['Filename'];

		$this->load->library('image_lib');

		// Rotate Image

		if($data['Orientation'] > 1)
		{
			$config = array(
				'image_library' => 'gd2',
				'new_image' => $this->config->item('image_folder_resampled', 'gallery').$filename,
				'rotation_angle' => 270,
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

		$this->photo_model->addPhoto($data);

		$this->index();
	}

	public function editUser()
	{
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

			$this->index($data, 'ajax/admin_user_form');
		}
	}
	
	public function getImageForm()
	{
		$this->load->helper('html');
		
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
				$config_rotate = array(
					'new_image' => $this->config->item('image_folder_resampled', 'gallery').$filename,
					'rotation_angle' => 270
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

		$data = array(
			'exif' => $exif_data,
			'file' => $this->config->item('image_dir_resampled', 'gallery').$fn[0].$thumb_marker.'.'.$fn[1],
			'source_file' => $filename
		);

		$this->load->view('includes/admin_update_form', $data);
	}
	
	public function updateUser()
	{
		$this->load->model('user_model');

		$data = json_decode($this->input->post('data'), true);

		$this->user_model->update($data, $data['ID']);
	}
	
	public function index($data = array(), $view = 'ajax')
	{
		$this->load->view($view, $data);
	}
}

?>