<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Gallery extends CI_Controller {

    public $data = array();
	public $Language = array();

	public function __construct()
	{
		parent::__construct();

		$this->config->load('gallery', true);
        $this->lang->load('basic', 'english');
        $this->load->library('content');
		$this->load->model('album_model');

		$this->loadLanguageItems(array(
			'gallery_title_play_button'
		));

		$Loggedin = $this->session->userdata('loggedin');

		$this->content->addCSSFiles(array(
			'reset.css',
			'basic.css',
			'forms.css',
			'layout.css',
			'infoBubble.css'
		));
		
		$jsFolder = base_url().'resources/js/';
		$jsFiles = array(
			$jsFolder.'third-party/mootools-core-1.4.0-full-nocompat-yc.js',
			$jsFolder.'third-party/mootools-more-1.4.0.1.js',
			$jsFolder.'third-party/md5.js',
			$jsFolder.'BlendIn.js',
			$jsFolder.'infoBubble.js',
			$jsFolder.'Photos.js',
            $jsFolder.'init.js'
		);

		$albumID = $this->session->userdata('albumID');
        var_dump($albumID);
		if(!$albumID)
		{
			$albumID = $this->album_model->getLatestAlbum();
            $this->session->set_userdata(array('albumID' => $albumID));
		}

        $this->addData(array(
			'CSS' => $this->content->getCSS(),
            'Date' => $this->content->getDate(),
			'ImageFolder' => $this->config->item('image_dir_resampled', 'gallery'),
			'JS' => $jsFiles,
			'Language' => $this->Language,
			'Loggedin' => $Loggedin,
			'LoginForm' => $Loggedin ? '' : $this->content->getLoginForm(),
			'Meta_robots' => $this->config->item('meta_robots', 'gallery'),
            'PageTitle' => 'Adora Gallery',
			'Photos' => $this->getPhotos($albumID),
			'RequiresLogin' => $this->config->item('requires_login', 'gallery')
        ));
	}
    
    private function addData($key, $value = '')
    {
        if(is_array($key))
        {
            foreach($key as $k => $v)
            {
                $this->data[$k] = $v;
            }
        }
        else
        {
            $this->data[$key] = $value;
        }
    }
	
	private function loadLanguageItems($keys)
	{
		foreach($keys as $key)
		{
			$this->Language[$key] = $this->lang->line($key);
		}
	}
	
	private function getPhotos($albumID)
	{
		$this->load->model('photo_model');

		$order = $this->album_model->getAlbumOrder($albumID);
		if($order == 'default')
		{
			$oder_by = 'FileDateTime ASC';
		}

		$photos = $this->photo_model->getFromAlbum($albumID, $oder_by, 1);

		return $photos;
	}
	
	public function index()
	{
		$this->load->library('user_agent');

		$folder = $this->agent->is_mobile() ? 'mobile/' : 'web/';

		if($this->data['Loggedin'] || !$this->data['RequiresLogin'])
		{
			$view = $folder.'gallery';
		}
		else
		{
			$view = $folder.'includes/login_form';
		}
		
		if($this->agent->is_mobile())
		{
			$views = array(
				$view
			);
		}
		else
		{
			$views = array(
				'web/includes/head',
				$view,
				'web/includes/footer'
			);
		}

		$this->content->view($views, $this->data);
	}

}

?>