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

		$this->content->addCSSFiles(array(
			'reset.css',
			'basic.css',
			'forms.css',
			'layout.css',
			'admin.css'
		));

		$jsFolder = base_url().'resources/js/';
		$jsFiles = array(
			$jsFolder.'third-party/mootools-core-1.3-full-nocompat-yc.js',
			$jsFolder.'third-party/mootools-more.js',
			$jsFolder.'Admin.js'
		);

        $this->addData(array(
			'CSS' => $this->content->getCSS(),
			'currentPage' => '',
			'GalleryLinkText' => $this->lang->line('gallery_link_text'),
			'ImageFolder' => $this->config->item('image_dir', 'gallery'),
			'isAdmin' => $this->session->userdata('role') == 'Admin',
			'JS' => $jsFiles,
			'Loggedin' => $Loggedin,
			'LoginForm' => $Loggedin ? '' : $this->content->getLoginForm(),
			'Tabs' => array(
				'settings',
				'edit',
				'update',
				'user'
			),
            'PageTitle' => 'Adora Gallery Admin'
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
    
    private function getNewPhotos()
    {
        $this->load->helper('file');

        $photos = $this->photo_model->getFilenames();

        $files = get_dir_file_info($this->config->item('image_dir', 'gallery'), true);

        $new_photos = array();

		$i = 0;
	
        foreach($files as $file)
        {
			$fn = $file['name'];

            if(!in_array($fn, $photos) && preg_match('/\.jpg/i', $fn))
            {
				$new_photos[$i]['exif'] = exif_read_data($this->config->item('image_folder', 'gallery').$fn);
                $new_photos[$i]['filename'] = $fn;
				$i++;
            }
        }
        
        return $new_photos;
    }
	
	public function update()
	{
	     $this->addData(array(
			'currentPage' => 'update',
            'Files' => $this->getNewPhotos(),
			'Text' => $this->lang->line('new_image')
        ));

		$this->content->view(array('admin','includes/admin_update', 'includes/admin_footer'), $this->data);
	}
	
	public function user()
	{
		$this->load->model('user_model');

		$this->addData(array(
			'addUser' => $this->lang->line('add_user'),
			'currentPage' => 'user',
			'IconFolder' => $this->config->item('user_icon_folder', 'gallery'),
			'User' => $this->user_model->getUser()
		));

		$this->content->view(array('admin','includes/admin_user', 'includes/admin_footer'), $this->data);
	}
	
	public function index()
	{
		$this->load->view('includes/head', $this->data);
		if($this->data['Loggedin'])
		{
			$this->load->view('admin', $this->data);
		}
		else
		{
			$this->load->view('includes/login_form', $this->data);
		}
        $this->load->view('includes/admin_footer', $this->data);
	}
}

?>