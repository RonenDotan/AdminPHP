<?php
	require '/vendor/autoload.php';
	use Google\Cloud\BigQuery\BigQueryClient;
	//use Google\Cloud\BigQuery\Connection;
	
	function src_get_bq_conn($parameters)
	{
 		$bigQuery = new BigQueryClient($parameters);
	 	return($bigQuery);
	} 	
?>