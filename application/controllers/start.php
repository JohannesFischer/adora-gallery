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
		$this->load->model('photos');

		$photos = $this->photos->getAll();

		return $photos;
	}
	
	public function index()
	{
        $this->content->view(array('start', 'includes/footer'), $this->data);
	}
}

?>