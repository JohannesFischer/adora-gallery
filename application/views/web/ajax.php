<?php
	if(isset($json) && $json == true)
	{
		header('Content-type: application/json');
	}	
	if(isset($response))
	{
		echo $response;
	}
?>
