<?php
	
	$filename = $_POST['fileName'];	//get unique filename from client
			
	$handle = fopen($filename,"r");	//open file for reading
	$returnval = "";				//initialize return value
	while (!feof($handle)) {		
		$returnval .= fgets($handle);	//read from file intil EOF
	}
	fclose($handle);	//close file
	unlink($filename);	//delete file
	
	header("content-type: text/xml");
	echo $returnval;
	exit;
	
?>