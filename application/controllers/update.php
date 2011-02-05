<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Update extends CI_Controller {

    public $data = array();

	public function __construct()
	{
		parent::__construct();

        $this->lang->load('basic', 'english');
        $this->load->library('content');
		$this->load->model('photos');

		$Loggedin = $this->session->userdata('loggedin');

        $this->addData(array(
			'CSS' => $this->content->getCSS(),
            'Files' => $this->getNewPhotos(),
			'ImageFolder' => $this->config->item('image_dir'),
			'Loggedin' => $Loggedin,
			'LoginForm' => $Loggedin ? '' : $this->content->getLoginForm(),
            'PageTitle' => 'Update Photos'
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
    
    public function getNewPhotos()
    {
		$this->config->load('gallery', true);

        $this->load->helper('file');

        $photos = $this->photos->getFilenames();

        $files = get_dir_file_info($this->config->item('image_dir'), true);
		//var_dump($files);

        $new_photos = array();

		$i = 0;
	
        foreach($files as $file)
        {
			//$this->config->item('thumb_marker', 'gallery')
			$fn = $file['name'];

            if(!in_array($fn, $photos))
            {
				$new_photos[$i]['exif'] = exif_read_data($this->config->item('image_folder').$fn);
                $new_photos[$i]['filename'] = $fn;
				$i++;
            }
        }
        
        return $new_photos;
    }
	
	public function index()
	{
		$this->content->view('update', $this->data);
	}
}

?>