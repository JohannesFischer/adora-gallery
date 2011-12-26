<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Admin extends CI_Controller {

    public $data = array();

	public function __construct()
	{
		parent::__construct();

		$this->config->load('gallery', true);
        $this->lang->load('basic', 'english');
        $this->load->library('content');
		$this->load->model('photo_model');

		$Loggedin = $this->session->userdata('loggedin');
		
		if($Loggedin && !$this->_isAdmin())
		{
			redirect('gallery');
		}

		$this->content->addCSSFiles(array(
			'reset.css',
			'basic.css',
			'forms.css',
			'layout.css',
			'admin.css',
            'upload.css'
		));

		$jsFolder = base_url().'resources/js/';
		$jsFiles = array(
			$jsFolder.'third-party/mootools-core-1.4.0-full-nocompat-yc.js',
			$jsFolder.'third-party/mootools-more-1.4.0.1.js',
            $jsFolder.'third-party/Request.File.js',
            $jsFolder.'third-party/Form.MultipleFileInput.js',
            $jsFolder.'third-party/Form.Upload.js', 
			$jsFolder.'third-party/md5.js',
			$jsFolder.'BlendIn.js',
			$jsFolder.'Photos.js',
			$jsFolder.'Admin.js',
            $jsFolder.'init.js'
		);

        $this->_addData(array(
			'CSS' => $this->content->getCSS(),
			'currentPage' => '',
			'GalleryLinkText' => $this->lang->line('gallery_link_text'),
			'ImageFolder' => $this->config->item('image_dir', 'gallery'),
			'JS' => $jsFiles,
			'Loggedin' => $Loggedin,
			'LoginForm' => $Loggedin ? '' : $this->content->getLoginForm(),
			'Meta_robots' => 'noindex, nofollow',
			'Tabs' => array(
				'galleries' => 'Galleries',
				'update' => 'Add images',
				'settings' => 'Settings',				
				'user' => 'User'
			),
            'PageTitle' => 'Adora Gallery Admin'
        ));
	}
    
	/**
	 * PRIVATE FUNCTIONS
	 */
	
    private function _addData($key, $value = '')
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
	
	private function _getGalleries()
	{
		$this->load->model('album_model');
		
		return $this->album_model->getAlbums();
	}
	
    private function _isAdmin()
    {
        return $this->session->userdata('role') == 'Admin';
    }
    
	/**
	 * PUBLIC FUNCTIONS
	 */
	
	public function galleries()
	{
	     $this->_addData(array(
			'currentPage' => 'galleries',
            'Galleries' => $this->_getGalleries()
        ));

		$this->index('includes/admin_galleries');
	}
	
	public function update()
	{
	     $this->_addData(array(
			'currentPage' => 'update',
			'Text' => $this->lang->line('new_image')
        ));

		$this->index('includes/admin_update');
	}
	
	public function user()
	{
		$this->load->model('user_model');

		$this->_addData(array(
			'addUser' => $this->lang->line('add_user'),
			'currentPage' => 'user',
			'IconFolder' => $this->config->item('user_icon_folder', 'gallery'),
			'User' => $this->user_model->getUser()
		));

		$this->index('includes/admin_user');
	}
	
	public function index($view = 'includes/admin_galleries')
	{
		if($this->data['Loggedin'] && $this->_isAdmin())
		{
			$views = array(
				'web/includes/head',
				'web/admin.php',
				'web/'.$view,
				'web/includes/admin_footer'
			);
		}
		else
		{
			$views = array(
				'web/includes/head',
				'web/includes/login_form',
				'web/includes/admin_footer'
			);
		}

		$this->content->view($views, $this->data);
	}
}

?>