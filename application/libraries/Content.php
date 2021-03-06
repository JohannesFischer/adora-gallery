<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

    class Content {

        private $CI;
		public $cssFiles = array();
		public $jsFiles = array();

        public function __construct()
        {
            $this->CI =& get_instance();
        }
		
        public function addCSSFile($file)
        {
            array_push($this->cssFiles, $file);
        } 
        
		public function addCSSFiles($files)
		{
			foreach($files as $file)
			{
				$this->addCSSFile($file);
			}
		}

		public function getCSS($files = array())
		{
			$this->CI->load->helper('html');

			$html = '';

			foreach($this->cssFiles as $file)
			{
				$html.= link_tag(array(
					'href' => 'resources/css/'.$file,
					'rel' => 'stylesheet',
					'type' => 'text/css'
				));
			}

			return $html;
		}

        public function getDate($date = false)
        {
            $this->CI->lang->load('basic', 'english');

            $day = date('j');
            $month = date('n');
    
            return $month.$this->CI->lang->line('date_month_suffix').$day.$this->CI->lang->line('date_day_suffix');
        }

        public function getLoginForm()
        {
			$this->CI->lang->load('basic', 'english');
            $this->CI->load->helper('form');

			$form = '<div id="LoginForm">';
			$form.= '<p>'.$this->CI->lang->line('login_text').'</p>';
			$form.= form_open(site_url('login'), array(
				'id' => 'Login'
			));
			$form.= form_fieldset();
			$form.= form_label('username');
			$form.= form_input(array(
				'maxlength' => 20,
				'name' => 'username'
			));
			$form.= form_label('password');
			$form.= form_input(array(
				'maxlength' => 20,
				'name' => 'password',
				'type' => 'password'
			));
			$form.= form_submit('login_submit', $this->CI->lang->line('login_button'));
			$form.= form_fieldset_close();
			$form.= form_close();
			$form.= '</div>';

			return $form;
        }

        public function view($view, $data = array())
        {
			if(is_array($view))
			{
				foreach($view as $v)
				{
					$this->CI->load->view($v, $data);		
				}
			}
			else
			{
				$this->CI->load->view($view, $data);
			}
        }

    }

?>