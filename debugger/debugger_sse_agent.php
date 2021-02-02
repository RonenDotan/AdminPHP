<?php
header('Content-Type: text/event-stream');
header('Cache-Control: no-cache');

$fa = fopen("debuger_raw_output.dat", "r");
$content = fread($fa,filesize("debuger_raw_output.dat"));
fclose($fa);

$data = json_encode($content,true);
echo 'data: ' . $data . "\n\n";
 
ob_flush();
flush();
?>