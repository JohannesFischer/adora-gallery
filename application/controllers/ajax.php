<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Ajax extends CI_Controller {

	public function __construct()
	{
		parent::__construct();

		$Loggedin = $this->session->userdata('loggedin');
	}
	
	/**
	 * Login
	 */
	
	public function login()
	{
		$this->lang->load('basic', 'english');

		$usercode = $this->input->post('usercode');

		$data = array(
			'icon' => base_url().'resources/images/icons/7_48x48.png',
			'loggedin' => true,
			'username' => 'johannes'
		);

		$data['username'] = str_replace('%u', $data['username'], $this->lang->line('login_welcome'));

		$this->session->set_userdata($data);

		$this->index(array(
			'json' => true,
			'response' => json_encode($data)
		));
	}
	
	/**
	 * Update
	 */
	
	public function addPhoto()
	{
		$this->load->model('photos');

		$data = json_decode($this->input->post('data'), true);
		
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

		$filename = $data['Filename'];

		$config = array(
			'create_thumb' => true,
			'image_library' => 'gd2',
			'maintain_ratio' => true,
			'new_image' => $this->config->item('image_folder_resampled').$filename,
			'source_image' => $this->config->item('image_folder').$data['Filename']
		);

		$config_large = array(
			'height' => $size['large']['height'],
			'thumb_marker' => '_large',
			'width' => $size['large']['width']
		);

		$this->load->library('image_lib', array_merge($config, $config_large)); 

		$resize_large = $this->image_lib->resize();

		if(!$resize_large)
		{
			$this->image_lib->display_errors();
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
			$this->image_lib->display_errors();
		}

		$fn = explode('.', $data['Filename']);

		$data['Filename_Large'] = $fn[0].$config_large['thumb_marker'].'.'.$fn[1];
		$data['Filename_Thumbnail'] = $fn[0].$config_thumbnail['thumb_marker'].'.'.$fn[1];

		$this->photos->addPhoto($data);

		$this->index();
	}
	
	public function index($data = array())
	{
		$this->load->view('ajax', $data);
	}
}

?>