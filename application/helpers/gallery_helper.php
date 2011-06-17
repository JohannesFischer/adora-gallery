<?php

	function is_exif_available()
	{
		$loaded_ext = get_loaded_extensions();
		return in_array('exif', $loaded_ext);	
	}

?>