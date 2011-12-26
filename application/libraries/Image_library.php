<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Image_library {

    private $CI;

    public function __construct()
    {
        $this->CI =& get_instance();
        $this->CI->config->load('gallery', true);
        $this->CI->load->library('image_lib');
    }

    public function getDateTime($exif_data)
    {
        // DateTime YYYY:MM:DD HH:ii:ss
        if($exif_data['DateTime'])
        {
            //preg_match($exif_data['DateTime'], '/[0-9:]*/', $matches);
            $date_time = explode(' ', $exif_data['DateTime']);
            return str_replace(':', '-', $date_time[0]).' '.$date_time[1];
        }
        return false;
    }
    
    public function getNewImages()
    {
        $this->CI->load->helper(array('file', 'gallery'));
        $this->CI->load->model('photo_model');

        $exif_availabale = is_exif_available();
        $files = get_dir_file_info($this->CI->config->item('image_dir', 'gallery'), true);
        $i = 0;
        $images = array();
        $photos = $this->CI->photo_model->getFilenames();

        foreach($files as $file)
        {
            $fn = $file['name'];

            if(!in_array($fn, $photos) && preg_match('/\.jpg/i', $fn))
            {
                if($exif_availabale)
                {
                    $images[$i]['exif'] = exif_read_data($this->CI->config->item('image_folder', 'gallery').$fn);
                }
                $images[$i]['filename'] = $fn;
                $i++;
            }
        }

        return $images;
    }

    private function getRotationAngle($orientation)
    {
        $image_library = strtolower($this->CI->config->item('image_library', 'gallery'));
        $rotation_angle = 0;

        if($orientation == 6)
        {
            $rotation_angle = $image_library == 'imagemagick' ? 90 : 270;
        }
        else if($orientation == 8)
        {
            $rotation_angle = $image_library == 'imagemagick' ? 270 : 90;
        }

        return $rotation_angle;
    }
    
    public function rotateImage($filename, $source_image, $orientation)
    {
        $rotation_angle = $this->getRotationAngle($orientation);

        $config = array(
            'image_library' => $this->CI->config->item('image_library', 'gallery'),
            'library_path' => $this->CI->config->item('library_path', 'gallery'),
            'new_image' => $this->CI->config->item('image_folder_resampled', 'gallery').$filename,
            'rotation_angle' => $rotation_angle,
            'source_image' => $source_image
        );

        $this->CI->image_lib->initialize($config); 

        $rotate = $this->CI->image_lib->rotate();
        
        if(!$rotate)
        {
            echo $this->CI->image_lib->display_errors();

            return false;
        }
        
        $source_image = $this->CI->config->item('image_folder_resampled', 'gallery').$filename;
        
        $this->CI->image_lib->clear();
        
        return $source_image;
    }

}