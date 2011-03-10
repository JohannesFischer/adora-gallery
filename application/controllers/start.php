<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Start extends CI_Controller {

    public $data = array();

	public function __construct()
	{
		parent::__construct();

        $this->lang->load('basic', 'english');
        $this->load->library('content');

		$Loggedin = $this->session->userdata('loggedin');

        $this->addData(array(
			'CSS' => $this->content->getCSS(),
            'Date' => $this->content->getDate(),
			'ImageFolder' => $this->config->item('image_dir_resampled'),
			'JS' => $this->content->getJS(array('infoBubble.js')),
			'Loggedin' => $Loggedin,
			'LoginForm' => $Loggedin ? '' : $this->content->getLoginForm(),
            'PageTitle' => 'Adora Gallery',
			'Photos' => $this->getPhotos()
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
	
	private function getPhotos()
	{
		$this->load->model('photo_model');

		$photos = $this->photo_model->getAll();

		return $photos;
	}
	
	public function index()
	{
		$this->load->view('includes/head', $this->data);
		if($this->data['Loggedin'])
		{
			$this->load->view('start', $this->data);
		}
		else
		{
			$this->load->view('includes/login_form', $this->data);
		}
        $this->load->view('includes/footer', $this->data);
	}

}

?>