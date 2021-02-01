<?php
// die();
if (!isset($_SERVER["DEBUG"]))
{
    $_SERVER["DEBUG"] = 0;
}

if (!isset($_SERVER["PAUSE"]))
{
    $_SERVER["PAUSE"] = 0;
}

if(!isset($db_output))
{
	$db_output = 'none';
}

if (function_exists('debugger_point'))
{
} 
elseif ($_SERVER["DEBUG"] == 0) 
{
    function debugger_point($note = "", $is_breakpoint = false) {}
}
	
function get_redis()
{
	$redis = new Redis();
	$redis->connect('127.0.0.1', 6379);
	$redis->auth('pilpilon');
	$redis->select(0);
    return $redis;
}


function get_db_conn()
{
    $conn = new mysqli("localhost","user","password","scheme");
    // Check conn ection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
    return $conn;
}

function fetch_all_assoc($result, $key = NULL)
{
	$ret_array = array();
	$index = -1;
	if (isset($result) and !empty ($result))
	{
		while ($row = $result->fetch_assoc())
		{
			if(is_null($key))
			{
				$index += 1;
			}  
			else
			{
				$index = $row[$key];	
			}
			
			foreach ($row as $k=>$v)
			{
				$ret_array[$index][$k] = $v;
			}
		}
	}
	return($ret_array);
}


function fetch_all_assoc_multi_values_key($result, $key = NULL)
{
	$ret_array = array();
	$index = -1;
	if (isset($result) and !empty ($result))
	{
		while ($row = $result->fetch_assoc())
		{
			if(is_null($key))
			{
				$index += 1;
			}  
			else
			{
				$index = $row[$key];	
			}
			if(!isset($ret_array[$index]))
			{
				$ret_array[$index] = array();
			}
			array_push($ret_array[$index],$row);
		}
	}
	return($ret_array);
}

function fetch_id_title_for_view($conn,$sql)
{
	$ret_array = array();
	$result = $conn->query($sql);
	$row=mysqli_fetch_array($result,MYSQLI_ASSOC);
	while ($row != NULL) 
	{
		$ret_array[$row['id']] = $row['title'];
		$row=mysqli_fetch_array($result,MYSQLI_ASSOC);
	}
	return($ret_array);
}

function check_blacklist_range_ip($ip_to_check)
{
	$ip = ip2long($ip_to_check);
	
	$redis = get_redis();
    $high = $redis->hGet('ip_range_bl_size', 'size') - 1;
    $low = 0;
	while ($high >= $low)
	{
			//echo "\n\n\n\nnew iteration:\n";
            $probe = floor(($high + $low) / 2);
            $row = $redis->hMGet('ip_range_bl##'.$probe, array('id', '_ip0', '_ip1'));
			//print_r($row);
	        if ($row['_ip0'] > $ip)
            {
                $high = $probe - 1;
			}
			else if ($row['_ip1'] < $ip) 
			{
                $low = $probe + 1;
            } 
            else
			{
				$ret_value = $row['id'];
				break;
            }
    }
	if (!isset($ret_value))
	{
		$ret_value = null;
	}
	
	return $ret_value;
}



function get_bq_conn()
{
	// https://github.com/GoogleCloudPlatform/google-cloud-php-bigquery
	// https://googlecloudplatform.github.io/google-cloud-php/#/docs/google-cloud/v0.49.0/bigquery/table
	require 'bq_conn.php'; 
	$parameters = array(
						'projectId' 		=> 'your_project-number',
						'keyFilePath'		=> '/var/www/html/general/adserver-41b51f319c90.json'
						);
    									
	return (src_get_bq_conn($parameters));
}  

function generate_raw_range($prefix, $start_datetime, $end_datetime)
{
	$start_datetime = str_replace(' ', 'T',$start_datetime);
	$end_datetime = str_replace(' ', 'T',$end_datetime);
	$imp_date_start_p = explode('T',$start_datetime);
	$imp_date_start = $imp_date_start_p[0];
	$imp_hour_start = explode(':',$imp_date_start_p[1])[0];
	$imp_date_end_p = explode('T',$end_datetime);
	$imp_date_end = $imp_date_end_p[0];
	$imp_hour_end = explode(':',$imp_date_end_p[1])[0];
	$tables = generate_bq_table_list($prefix, $imp_date_start, $imp_date_end, false);
	
	$table_array = explode(",",$tables);
	$table_query = "";
	foreach ($table_array as $k=>$table_date)
	{
		$table_date = str_replace(']','',str_replace('[','',$table_date));
		$start_hour = 0;
		$end_hour = 23;
		if ($k == 0)
		{
			$start_hour = 0+$imp_hour_start;
		}
		
		if ($k == (sizeof($table_array)-1))
		{
			$end_hour = 0+$imp_hour_end;
		}
		
		for ($i = $start_hour; $i <= $end_hour; $i++)
		{
			if ($i < 10)
			{
				$table_query = "{$table_query},[{$table_date}_0{$i}]";
			}
			else
			{
				$table_query = "{$table_query},[{$table_date}_{$i}]";
			}
		}
	}
	
	return ltrim(rtrim($table_query, ","),",");
}

function generate_bq_table_list($prefix, $start_date, $end_date, $is_new = true)
{
    if (!$is_new)
    {
    	$min_data_date = new DateTime('2018-06-11');
    	$begin = new DateTime($start_date);
    	
    	if ($begin < $min_data_date)  
    	{
    		$begin = $min_data_date;
    	}
    	
    	$end = new DateTime($end_date);
    	if ($end < $min_data_date)  
    	{
    		$end = $min_data_date;
    	}
    
    	$interval = DateInterval::createFromDateString('1 day');
    	$period = new DatePeriod($begin, $interval, $end);
    	
    	$ret_val = "";
    	foreach ( $period as $dt )
    	{
    	  $ret_val = $ret_val . "[".$prefix.$dt->format( "Ymd") ."],";
    	}
    	//$ret_val = rtrim($ret_val,",");
    	$ret_val = $ret_val . "[".$prefix.$end->format( "Ymd") ."]";
    	return($ret_val);
    }
    else 
    {
        //$bq_conn = get_bq_conn();
        global $bq_client;
        $prefix_parts = explode(".",$prefix);
        
        $min_data_date = new DateTime('2018-06-11');
        $begin = new DateTime($start_date);
        
        if ($begin < $min_data_date)
        {
            $begin = $min_data_date;
        }
        
        $end = new DateTime($end_date);
        if ($end < $min_data_date)
        {
            $end = $min_data_date;
        }
        
        $interval = DateInterval::createFromDateString('1 day');
        $period = new DatePeriod($begin, $interval, $end);
        
        $search_table_ids = array();
        foreach ( $period as $dt )
        {
            // $ret_val = $ret_val . "[".$prefix.$dt->format( "Ymd") ."],";
            array_push($search_table_ids, "'".$prefix_parts[1].$dt->format( "Ymd")."'");
        }
        array_push($search_table_ids, "'".$prefix_parts[1].$end->format( "Ymd")."'");
        // $ret_val = $ret_val . "[".$prefix.$end->format( "Ymd") ."]";
        $search_table_ids = implode(",", $search_table_ids);
        $bq_sql_get_existing_tables = " select group_concat(concat('[{$prefix_parts[0]}.',table_id,']')) as tables_str
                                        from   {$prefix_parts[0]}.__TABLES__
                                        where  table_id in ({$search_table_ids})";
        
        // echo $bq_sql_get_existing_tables;
        $queryJobConfig = $bq_client->query(
            $bq_sql_get_existing_tables,
            ['configuration' => ['query' => ['useLegacySql' => true]]]
            );
        $queryResultsExistingTables = $bq_client->runQuery($queryJobConfig);
        $ret_val = "";
        foreach ($queryResultsExistingTables as $row)
        {
            $ret_val = $row['tables_str'];
        }
        
        return($ret_val);
    }
}


function start_cornjob()
{
    extract(get_server_ips());
    if (isset($_SERVER["DEBUG"]) and $_SERVER["DEBUG"] == 1) {return 0;}
    if (isset($_SERVER["PAUSE"]) and $_SERVER["PAUSE"] == 1) {return 0;}
	ob_start();
	
	ini_set('display_errors', 1);
	ini_set('display_startup_errors', 1);
	error_reporting(E_ALL);
	
	$date = date('Y-m-d');
	$hour = date('H');
	$cronjob_status = 0;							
	$conn = get_db_conn();
	
	$script_path = $_SERVER['SCRIPT_FILENAME'];
	$script_name = basename($script_path);

	// Find The Process Id By Script Name
	if (true)
	{
		$res = $conn->query("SELECT id, title FROM cronjobs_processes WHERE title LIKE '%{$script_name}%'");
		$row = $res->fetch_assoc();
		$cronjobs_processes_id = $row['id'];
	}
	
	$script_name_id = "{$cronjobs_processes_id} - {$script_name}";
	echo "<pre>\n\n";
	echo $script_name_id . "\n" . str_repeat("-",strlen($script_name_id)) ."\n";
	echo "Start Time: ". date('Y-m-d H:i:s') ."\n\n";
	$GLOBALS['start_cron_run'] = date('Y-m-d H:i:s');
	
	
	$cronjob_sql = "INSERT INTO `cron_logs` (`cronjob_id`, `date`, `hour`, `text`, `status`,`awaiting_action`,`server_ip`) 
					VALUES ('{$cronjobs_processes_id}', '{$date}', {$hour}, 'Process Did Not Finish', {$cronjob_status}, 1, '{$external_server_ip}');";

	$res = $conn->query($cronjob_sql);
	$cron_log_row_id = $conn->insert_id;
	
	echo "\nCron Log Row ID : {$cron_log_row_id}\n";
	echo "Start Memory Usage: " . memory_get_usage() . "\n";
	echo "Start Memory Peak Usage: " . memory_get_peak_usage(). "\n\n";
	echo "Parameters:\n";
	if (isset($_GET)) {echo "GET:\n".print_r($_GET);}
	if (isset($argv[1])) {echo "Command Line Params:\n".print_r($argv);}
	return($cron_log_row_id);
}


function finish_cornjob($cron_log_row_id, $cronjob_status,$db_output = "short",$preserve_log_file = false,$append_to_log_file_name = "" , $run_title = null)
{
    if (isset($_SERVER["DEBUG"]) and $_SERVER["DEBUG"] == 1) {return 0;}
    if (isset($_SERVER["PAUSE"]) and $_SERVER["PAUSE"] == 1) {return 0;}
    global $external_server_ip;
    $conn = get_db_conn();
    $date = date('Y-m-d');
    $hour = date('H');
    
    // Find The Process Id By Script Name
    if (true)
    {
        $script_path = $_SERVER['SCRIPT_FILENAME'];
        $script_name = basename($script_path);
        
        $res = $conn->query("SELECT id, title FROM cronjobs_processes WHERE title LIKE '%{$script_name}%'");
        $row = $res->fetch_assoc();
        $cronjobs_processes_id = $row['id'];
    }
    
    echo "\n\nEnd Memory Usage: " . memory_get_usage() . "\n";
    echo "End Memory Peak Usage: " . memory_get_peak_usage(). "\n\n";
    echo "Finish Time: ". date('Y-m-d H:i:s') ."\n\n";
    
    $start_run 	= $GLOBALS['start_cron_run'];
    unset($GLOBALS['start_cron_run']);
    $end_run 	= date('Y-m-d H:i:s');
    
    $duration = strtotime($end_run) - strtotime($start_run);
    echo "Cronjob Duration: ". $duration ."\n\n";
    if ($cronjob_status == 1)
    {
        echo "\n";
        echo "===========================================\n";
        echo "=            ENDED SUCCESSFULLY           =\n";
        echo "===========================================\n";
    }
    
    
    $out = ob_get_contents();
    ob_end_clean();
    
    global $advertiser_offers_log;
    if (isset($advertiser_offers_log) and sizeof($advertiser_offers_log) > 0)
    {
        $out = $out . "\n\n\nAdvertiser Offer Log:\n" .  print_r($advertiser_offers_log, true);
    }
    
    $out_sql = "";
    if ($db_output != "short" or $cronjob_status == 0)
    {
        $out_sql = str_replace('"','\"',str_replace("'", "\'", $out));
    }
    elseif (isset($advertiser_offers_log) and sizeof($advertiser_offers_log) > 0)
    {
        $out_sql = $out_sql . "\n\n\nAdvertiser Offer Log:\n" .  print_r($advertiser_offers_log, true);
    }
    
    
    $awaiting_action = ($cronjob_status == 1) ? 0 : 1;
    $cronjob_sql = "REPLACE INTO `cron_logs` (`id`,`cronjob_id`, `date`, `hour`, `text`, `status`,`awaiting_action`,`duration`,`run_title`,`server_ip`)
					VALUES ('{$cron_log_row_id}','{$cronjobs_processes_id}', '{$date}', {$hour}, '{$out_sql}', {$cronjob_status}, {$awaiting_action},{$duration}, '{$run_title}','{$external_server_ip}');";
    
    if (!$res = $conn->query($cronjob_sql))
    {
		$out_cleansed_sql = $string = str_replace("'","",str_replace('"', '', $out_sql)); // Removes special chars.
		$cronjob_sql = "REPLACE INTO `cron_logs` (`id`,`cronjob_id`, `date`, `hour`, `text`, `status`,`awaiting_action`,`duration`,`run_title`,`server_ip`)
						VALUES ('{$cron_log_row_id}','{$cronjobs_processes_id}', '{$date}', {$hour}, '{$out_cleansed_sql}', {$cronjob_status}, {$awaiting_action},{$duration}, '{$run_title}','{$external_server_ip}');";
		
		if (!$res = $conn->query($cronjob_sql))
		{
			$out_cleansed_sql = clean_string($out_sql);
			$cronjob_sql = "REPLACE INTO `cron_logs` (`id`,`cronjob_id`, `date`, `hour`, `text`, `status`,`awaiting_action`,`duration`,`run_title`,`server_ip`)
						VALUES ('{$cron_log_row_id}','{$cronjobs_processes_id}', '{$date}', {$hour}, '{$out_cleansed_sql}', {$cronjob_status}, {$awaiting_action},{$duration}, '{$run_title}','{$external_server_ip}');";

			if (!$res = $conn->query($cronjob_sql))
			{
				$out = $out . "\n\n\nCrontab Insert Error {$conn->error}";
				$cronjob_sql = "REPLACE INTO `cron_logs` (`id`,`cronjob_id`, `date`, `hour`, `text`, `status`,`awaiting_action`,`duration`,`run_title`,`server_ip`)
							VALUES ('{$cron_log_row_id}','{$cronjobs_processes_id}', '{$date}', {$hour}, 'Cannot Write Output, See LOG File \n\n\nCrontab Insert Error', {$cronjob_status}, {$awaiting_action},{$duration},'{$run_title}','{$external_server_ip}');";
			
				$res = $conn->query($cronjob_sql);
			}

		}
	}
    

    $script_log_path = str_replace(".php",".log",$script_path);
    
    if ($preserve_log_file)
    {
        $curr_ts = date('Ymd_h:i');
        $script_log_path = str_replace(".log","{$curr_ts}.log",$script_log_path);
    }
    
    if ($append_to_log_file_name != "")
    {
        $script_log_path = str_replace(".log","_{$append_to_log_file_name}.log",$script_log_path);
    }
    
    file_put_contents($script_log_path, $out);
    if (isset($_GET['output']) and $_GET['output'] == 'both')
    {
        echo $out;
    }
   
    
}



function loop_fix_json($obj,&$fixed_array)
{
    if (is_array($obj))
    {
        foreach ($obj as $k=>$x) 
        {
            loop_fix_json($x,$fixed_array[$k]);
        }
    }
    else
    {
        // do something
        $fixed_array = str_replace("'","",str_replace('"','',$obj));
    }
}


function convert_weird_chars(&$array)
{
    array_walk($array,
        function (&$value)
        {
            if ($value)
            {
                $value = mb_convert_encoding($value ,'UTF-8', 'UTF-8');
            }
    });
}

function search_for_any($needles, $haystack, $case_sensetive = true)
{
	if ($case_sensetive)
	{
		foreach($needles as $needle)
		{
			if (strpos($haystack, $needle) !== false) 
			{
				return true;
			}
		}
		return false;
	}
	else
	{
		foreach($needles as $needle)
		{
			if (stripos($haystack, $needle) !== false) 
			{
				return true;
			}
		}
		return false;
	}
}


function clean_string($string)
{
    $string = str_replace('<br>', 'LINEBREAK', $string); // Replaces all linebreaks (1).
    $string = str_replace('\n', 'LINEBREAK', $string); // Replaces all linebreaks (2).
    $string = str_replace(' ', '-', $string); // Replaces all spaces with hyphens.
    $string = preg_replace('/[^A-Za-z0-9\-]/', '', $string); // Removes special chars.
        
    return str_replace("LINEBREAK","\n",str_replace("-", " ", preg_replace('/-+/', '-', $string))); // Replaces multiple hyphens with single one.
}


function get_server_ips()
{
    global $external_server_ip, $internal_server_ip, $real_external_server_ip;
    $external_server_ip     =  exec("curl ifconfig.me");
//    $internal_server_ip     = exec("/sbin/ifconfig eth1 | grep 'inet addr' | cut -d ':' -f 2 | cut -d ' ' -f 1");
    $internal_server_ip     = exec("ip addr show eth1 | grep 'inet \b' | head -n 1 | awk '{print $2}' | cut -d/ -f1");
//    $real_external_server_ip= exec("/sbin/ifconfig eth0 | grep 'inet addr' | cut -d ':' -f 2 | cut -d ' ' -f 1");
    $real_external_server_ip     = exec("ip addr show eth0 | grep 'inet \b' | head -n 1 | awk '{print $2}' | cut -d/ -f1");
    return(array('external_server_ip' => $external_server_ip, 'real_external_server_ip' => $real_external_server_ip, 'internal_server_ip' => $internal_server_ip));
}

function get_ip_address(){
    foreach (array('HTTP_CLIENT_IP', 'HTTP_CF_CONNECTING_IP','HTTP_X_FORWARDED_FOR', 'HTTP_X_FORWARDED', 'HTTP_X_CLUSTER_CLIENT_IP', 'HTTP_FORWARDED_FOR', 'HTTP_FORWARDED', 'REMOTE_ADDR') as $key){
        if (array_key_exists($key, $_SERVER) === true){
            foreach (explode(',', $_SERVER[$key]) as $ip){
                $ip = trim($ip); // just to be safe

                if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE) !== false){
  //                echo "generalout";
                    return $ip;
                }
            }
        }
    }
}


?>