<?php

class Upload extends CI_Controller {

	function __construct()
	{
		parent::__construct();
		$this->config->load('gallery', true);
	}

	function index()
	{
	}

	function do_upload()
	{
		$config = array(
            'allowed_types' => 'jpeg|jpg|png',
            'upload_path' => $this->config->item('image_folder', 'gallery')
        );

        #print_r($_FILES['url']['name']);

        for($i = 0; $i < count($_FILES['url']['name']); $i++)
        {
            //echo $this->config->item('image_folder', 'gallery').$_FILES['url']['name'][$i];
            move_uploaded_file($_FILES['url']['tmp_name'][$i], $this->config->item('image_folder', 'gallery').$_FILES['url']['name'][$i]);
        }
	}
}
?>