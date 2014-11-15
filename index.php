<?php

	//index.php	- Should re-route user to main html page
	
	if (!empty($_SERVER['HTTPS']) && ('on' == $_SERVER['HTTPS'])) {
		$uri = 'https://';
	} else {
		$uri = 'http://';
	}
	
	$uri .= $_SERVER['HTTP_HOST'];
	
	header('Location: '.$uri.'INSERT MAIN HTML PAGE HERE');	//<<<<<<<<<<<<<<<<<<<
	
	exit;
	
?>
Something is wrong with the XAMPP installation
