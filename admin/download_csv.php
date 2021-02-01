<?php
//$file = basename($_GET['file']);
$file = "report.csv";

if(!$file)
{ // file does not exist
    die('file not found');
} 
else 
{
	$file_dest_name = "report";
	if (isset($_GET['table']) and strlen($_GET['table']) > 2)
	{
		$file_dest_name = $_GET['table'];
	}
	$file_dest_name = $file_dest_name . "-" .date("Y-m-d_H") . ".csv";
    header("Cache-Control: public");
    header("Content-Description: File Transfer");
    header("Content-Disposition: attachment; filename={$file_dest_name}");
	//header("Content-Disposition: attachment; filename=ronen.csv");
    header("Content-Type: application/zip");
    header("Content-Transfer-Encoding: binary");

    // read the file from disk
    readfile($file);
}
?>