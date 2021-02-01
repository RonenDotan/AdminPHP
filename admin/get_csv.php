<?php
	$csv_value = isset($_POST['csv_text']) ? $_POST['csv_text'] : 'Empty';
	$csv_name  = isset($_POST['csv_name']) ? $_POST['csv_name']."_".date("Ymd_Hi") : date("Ymd_Hi");	
	header("Content-type: application/octet-stream");
	header("Content-Disposition: attachment; filename=\"{$csv_name}.csv\"");
	$data=str_replace('&#9652;",',"",str_replace("#%~%#","\n",$csv_value));
	$data=str_replace('▴",','',$data);
	echo '"'.$data; 
?>