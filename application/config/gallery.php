<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/*
|--------------------------------------------------------------------------
| Photo Gallery Settings
|--------------------------------------------------------------------------
|
| Global settings for the Photo Gallery
|
*/

$config['user_icon_folder'] = 'resources/images/icons/user-icons/';
$config['image_dir'] = 'photos/';
$config['image_dir_resampled'] = 'photos/_resampled/';
$config['image_folder'] = '/opt/lampp/htdocs/scripts/adora-gallery/photos/';
$config['image_folder_resampled'] = $config['image_folder'].'_resampled/';
$config['root_dir'] = '';
$config['thumbnail_dir'] = '_resampled/';

$config['thumb_marker']	= '_thumbnail';

$config['date_format'] = 'd-m-Y';

?>