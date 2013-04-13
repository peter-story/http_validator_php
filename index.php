<?php
	require('validator.php');

	$dir_path = 'requests';

	if ( ! file_exists($dir_path) || ! is_dir($dir_path))
		die("Error: no directory by the name of \"$dir_path\" could be found.");

	$handle   = opendir($dir_path);

	if ($handle) {
	    while (false !== ($file = readdir($handle))) {
	    	if (in_array($file, array('.', '..')))
	    		continue;

			$validate = new Validator($dir_path.DIRECTORY_SEPARATOR.$file);

	        if ($validate->execute()) {
	        	echo "$file is valid\n\n";
	        } else {
	        	echo "$file is invalid: ".$validate->get_error_message()."\n\n";
	        }
	    }

	    closedir($handle);
	}