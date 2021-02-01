<?php

// ob_start();
// echo "<pre>";
 
// ini_set('display_errors', 1);
// ini_set('display_startup_errors', 1);
// error_reporting(E_ALL);


$show_db_status = false;

if (isset($_GET))
{
	foreach ($_GET as $k => $arg)
	{
		$$k = $arg;
	}
}

// Get Command Line Args
if (isset($argv[1]))
{
	foreach ($argv as $k => $arg)
	{
		if ($k > 0)
		{
			$temp_array = explode("=",$arg);
			$q = $temp_array[0];
                        $$q = $temp_array[1];
		} 
	}
}



require_once dirname(dirname(dirname(dirname(__FILE__)))) . "/general/general.php";
$cron_log_row_id = start_cornjob();
$advertiser_offers_log = array();


$dev_mode = false;

try
{
	$conn = get_db_conn();
	$res = $conn->query("SELECT * FROM table");
	$data = fetch_all_assoc($res,'id');
	// print_r($data);

	foreach ($data as $data_item)
	{
		
		
		// code
	}
					
	if (!$conn->query($sql_insert)) 
	{
		echo "\n\nERROR!!!!!!!\n". $conn->error;
		throw new Exception($conn->error);
	}
	else 
	{
		echo "\n\nEnded Succesfully. Affected rows: ". $conn->affected_rows;
	}

	//echo "\n\n\n\n===============================\n=              END            =\n===============================\n";
	$cronjob_status = 1;
}
catch(Exception $e) 
{ 
  echo '\n\nException: Message: ' .$e->getMessage();
  $cronjob_status = 0;
  $show_db_status = true;
}

finish_cornjob($cron_log_row_id,$cronjob_status,$db_output);
?>