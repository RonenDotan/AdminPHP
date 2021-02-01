<?php

$form = $_GET['form'];
require_once dirname(dirname(__FILE__)). '/config.php';

/*
  
  THIS SECTION IS FOR TEST PURPOSES
  ==================================
  
 
  ob_start();
  echo "<pre>\n\n";
  echo "FORM: {$form}     TABLE: {$table}\n";
  echo "POST:\n";
  print_r($_POST);
  $out = ob_get_contents();
  ob_end_clean();
  file_put_contents(dirname(__FILE__)."/test.log", $out);
  
 */
 
 switch($form) 
 {
 	case "smart_link_rules_tree":
		smart_link_rules_tree_post($conn);
		break;
 	case "rules_tree":
 	    rules_tree();
 	    break;
 	case "tags":
		//smart_link_rules_tree_post($conn);
		$smart_link_id = $_GET['smart_link_id'];
		get_smart_link_properties($conn,$smart_link_id);
		//echo "AAAAA";
		break;
 	case "intable_input":
 	    $params_array = json_decode($_POST['params'],true);
 	    $return = array();
 	    $return['sucssess'] = true;
 	    $return['sql'] = array();
 	    foreach ($params_array as $row_id => $row_data)
 	    {
 	        $sql = "";
 	        $cols = "(id,". implode(",",array_keys($row_data)) . ")";
 	        $values = "({$row_id},'" . implode("','",array_values ($row_data)) . "')";
 	        $temp = array();
 	        foreach (array_keys($row_data) as $col_name)
 	        {
 	            array_push($temp,"{$col_name} = values($col_name)");
 	        }
 	        $duplicate = implode(",",$temp);
 	        $sql = $sql ."  INSERT INTO {$_POST['src_table']} {$cols} VALUES {$values} ON DUPLICATE KEY UPDATE {$duplicate};";
 	        array_push($return['sql'],$sql);
 	        if (!$res = $conn->query($sql))
 	        {
 	            $return['sucssess'] = false;
 	            $return['error'] = $conn->error;
 	            echo json_encode($return);
 	            break;
 	        }
 	    }
 	    echo json_encode($return);
 	    break;
 	case "click_distribution_editor":
 	    // Sorting The clicks_distribution
 	    $src_array = json_decode($_POST['clicks_distribution'],true);
 	    $temp_array = array();
 	    foreach ($src_array as $data)
 	    {
 	        $temp_array[$data['order']] = $data;
 	    }
 	    
 	    ksort($temp_array);
 	    $output_array = array();
 	    foreach ($temp_array as $data)
 	    {
 	        array_push($output_array, $data);
 	    }
 	    $clicks_distribution_sorted = json_encode($output_array);
 	    // End Sort
 	    
 	    if (isset($_POST['tag_id']) and $_POST['tag_id'] > 0)
 	    {
 	      $sql = "UPDATE tags set clicks_distribution='{$clicks_distribution_sorted}' where id={$_POST['tag_id']}";
 	    }
 	    elseif (isset($_POST['smart_link_id']) and $_POST['smart_link_id'] > 0)
 	    {
 	        $sql = "UPDATE smart_links set clicks_distribution='{$clicks_distribution_sorted}' where id={$_POST['smart_link_id']}";
 	    }
 	    $return = array();
 	    $return['sucssess'] = true;
 	    if (!$res = $conn->query($sql))
 	    {
 	        $return['sucssess'] = false;
 	        $return['error'] = $conn->error;
 	    }
 	    echo json_encode($return);
 	    break;

 	case "s2s_device_id_selection_editor":
 	    // Sorting The clicks_distribution
 	    if (false)
 	    {
     	    $src_array = json_decode($_POST['json_categories'],true);
     	    $temp_array = array();
     	    foreach ($src_array as $data)
     	    {
     	        $temp_array[$data['order']] = $data;
     	    }
     	    
     	    ksort($temp_array);
     	    $output_array = array();
     	    foreach ($temp_array as $data)
     	    {
     	        array_push($output_array, $data);
     	    }
     	    $clicks_distribution_sorted = json_encode($output_array);
     	    // End Sort
 	    }
 	    
 	    
 	    if (isset($_POST['slr_group']) and $_POST['slr_group'] > 0)
 	    {
 	        $sql = "UPDATE slr_groups set s2s_device_id_selection='{$_POST['json_categories']}' where id={$_POST['slr_group']}";
 	    }
 	    elseif (isset($_POST['sl_rule_id']) and $_POST['sl_rule_id'] > 0)
 	    {
 	        $sql = "UPDATE smart_link_rules set s2s_device_id_selection='{$_POST['json_categories']}' where id={$_POST['sl_rule_id']}";
 	    }
 	    $return = array();
 	    $return['sucssess'] = true;
 	    if (!$res = $conn->query($sql))
 	    {
 	        $return['sucssess'] = false;
 	        $return['error'] = $conn->error;
 	    }
 	    echo json_encode($return);
 	    break;
 	case "create_network_publisher":
 	    // Sorting The clicks_distribution
 	  	$return = array();
 	    $return['sucssess'] = true;
		$return = create_network_publisher($conn, $_GET['id']);
 	    echo json_encode($return);
 	    break;		
	default:
		return_id_title_by_query($conn);
 }
 
 
 
 
 
 function get_smart_link_properties($conn,$smart_link_id)
 {
 	$sql = "SELECT * FROM smart_links WHERE id = {$smart_link_id}";
	if (!$res = $conn->query($sql))
	{ 
		$error = true;
		echo "\n\n{$conn->error}\n\n";
	}
	$row = $res->fetch_assoc();
	echo json_encode($row);
 }
 
 function smart_link_rules_tree_post($conn)
 {
 	  $table = $_GET['table'];
	  
	  // Test To A File
 	  //ob_start();
	  //echo "<pre>\n\n";
	  //echo "Function smart_link_rules_tree_post()\n";
      //echo "TABLE: {$table}\n";
      //echo "POST:\n";
      //print_r($_POST);
	  
	  $recs_to_update = $_POST;
	  foreach  ($recs_to_update as $k => $v)
	  {
	  	$key = split("_",$k);
		$sql = "UPDATE {$table} SET {$key[0]} = '{$v}' WHERE id={$key[1]}";
		//echo "SQL: $sql \n";
		//$conn->query($sql);
		if (!mysqli_query($conn,$sql))
  		{
  			echo "====================ERROR===================\n";
  			echo("Error description: " . mysqli_error($conn));
			die("\n===========================================\n");
  		}
	  }
	  echo "OK";
      //$out = ob_get_contents();
      //ob_end_clean();
      
      
     // file_put_contents(dirname(__FILE__)."/test.log", $out);
 }
 
 
 function return_id_title_by_query($conn)
 {
	// echo $_POST['query'];
	if (isset($_POST['query']) and strlen($_POST['query']) > 0)
	{
		$query = str_replace("percentage","%",$_POST['query']);
		$ret_val = fetch_id_title_for_view($conn, $query);
	}
	if (sizeof($ret_val) > 0)
	{
		echo json_encode($ret_val);
	}
	else
	{
		echo json_encode("Not Found");
	}
 }
 
 
 
 function rules_tree()
 {
     global $conn;
     try 
     {
         $update_array = json_decode($_POST['update_array'],true);
         $ret_array = array();
         
         if (json_last_error() != JSON_ERROR_NONE)
         {
             $ret_array['error'] = "json Error";
             throw new Exception();
         }
         
         $update_sql = "INSERT INTO smart_link_rules (id,weight) VALUES ";
         foreach ($update_array as $uprdate_row)
         {
             $update_sql = "{$update_sql}\n({$uprdate_row['id']},{$uprdate_row['weight']}),";
         }
         $update_sql = rtrim($update_sql,",") . "\nON DUPLICATE KEY UPDATE weight=values(weight)";
         
         if (!mysqli_query($conn,$update_sql))
         {
             $ret_array['error'] = mysqli_error($conn);
             throw new Exception();
         }
         
         $ret_array['succeeded'] = true;
     } 
     catch (Exception $e) 
     {
         $ret_array['succeeded'] = false;
     }
     
     
     echo json_encode($ret_array);
 }
 

function create_network_publisher($conn,$publisher_id)
{
	$return = array();
 	$return['sucssess'] = true;
	
	$dev = false;
	if (isset($_GET['dev']) and $_GET['dev'] == 1)
	{
		$dev = true;
	}
	
	
	//$return['error'] = "DEV: {$_GET['dev']}";
	//return($return);
	
	try
	{
		$return['sucssess'] = true;
		$sql = "select title, network_api_password , allowed_user, network_tag from publishers where id = {$publisher_id}";
		if (!$res = $conn->query($sql))
		{ 
			throw new Exception($conn->error);
		}
		$p_row = $res->fetch_assoc();
		$title = $p_row['title'];
		
		if (isset($p_row['network_api_password']) and strlen($p_row['network_api_password']) >= 2)
		{
			// Wil set a new password
		}
		
		if (isset($p_row['allowed_user']) and strlen($p_row['allowed_user']) > 2)
		{
			// throw new Exception("User Already Set - Please Check Manualy");
			$user_id = $p_row['allowed_user'];
		}
		
		if (isset($p_row['network_tag']) and strlen($p_row['network_tag']) >= 2)
		{
			// throw new Exception("Tag Already Set - Please Check Manualy");
			$sql = "SELECT * FROM connections_for_redirect where c_tag_id = {$p_row['network_tag']} and c_publisher_id = {$publisher_id}";
			if (!$res = $conn->query($sql))
			{ 
				throw new Exception($conn->error);
			}
			$row = $res->fetch_assoc();
			
			$slr_groups_id = $row['c_group'];
			$smart_link_id = $row['c_smart_link_id'];
			$tag_id = $row['c_tag_id'];

		}
		
		
		


		// Slr Group
		if (isset($slr_groups_id))
		{
			$sql = "REPLACE INTO `slr_groups` (`id`,`title`, 									`description`						,`status`, `clicks_cap`	, `click_cap_timeframe`,`type`) 
					VALUES 						 ({$slr_groups_id},'{$title} - Redirect CPI', '{$title} - Redirect CPI', 1		, 0				, 0						  ,2);";
			
		}
		else
		{
			$sql = "INSERT INTO `slr_groups` (`title`, 									`description`						,`status`, `clicks_cap`	, `click_cap_timeframe`,`type`) 
					VALUES 						 ('{$title} - Redirect CPI', '{$title} - Redirect CPI', 1		, 0				, 0						  ,2);";
		}
		
		if (!$res = $conn->query($sql))
		{ 
			throw new Exception($sql ."\n\n". $conn->error);
		}
		
		if (!isset($slr_groups_id)) { $slr_groups_id = $conn->insert_id; }
		
		
		
		
		
		// Smart Links
		if (isset($smart_link_id))
		{
			$sql = "REPLACE INTO `smart_links` (`id`,`title`, 	`clicks_distribution`																				,`redirect_method`, `default_url`,`size`	, `width`, `height`	, `creative_id`, `status`,  `fraud_type_id`, `num_pixels`, `view_id_type`, `click_id_type`, `write_stats_ratio`, `ip_list_id`, `fraudhunt_req`, `use_blacklist_ip`, `use_blacklist_ip_range`, `use_blacklist_referrer`, `p0f`, `use_blacklist_subid`, `check_idfa_deviceid`, `max_mind_detection_type`, `extended_device_detection_type`, `save_additional_data`, `conncetion_type_handling`, `use_rabbit`, `auto_add_ip_blacklist`,`cpm`, `redis_ui_product_ttl`, `debug_mode`, `client_id`) VALUES 
						({$smart_link_id},'{$title} - Feed Smart Link', '[{\"order\":\"redirect\",\"clicks\":1,\"groups\":[{\"group\":{$slr_groups_id},\"weight\":3}]}]' 	,2						,			'-1'	,'0x0'	,	 0		,	0			,		12			,		1	, 			2, 					1, 				1, 				1, 				1.00000,						0, 				0, 				0, 								0, 						0, 						0, 			0, 							0, 						'1,2,3', 							0, 										0, 							-1, 								2, 				0, 				0.00000, 			0, 					0, 				1);";
		}
		else
		{
			$sql = "INSERT INTO `smart_links` (`title`, 	`clicks_distribution`																				,`redirect_method`, `default_url`,`size`	, `width`, `height`	, `creative_id`, `status`,  `fraud_type_id`, `num_pixels`, `view_id_type`, `click_id_type`, `write_stats_ratio`, `ip_list_id`, `fraudhunt_req`, `use_blacklist_ip`, `use_blacklist_ip_range`, `use_blacklist_referrer`, `p0f`, `use_blacklist_subid`, `check_idfa_deviceid`, `max_mind_detection_type`, `extended_device_detection_type`, `save_additional_data`, `conncetion_type_handling`, `use_rabbit`, `auto_add_ip_blacklist`,`cpm`, `redis_ui_product_ttl`, `debug_mode`, `client_id`) VALUES 
						('{$title} - Feed Smart Link', '[{\"order\":\"redirect\",\"clicks\":1,\"groups\":[{\"group\":{$slr_groups_id},\"weight\":3}]}]' 	,2						,			'-1'	,'0x0'	,	 0		,	0			,		12			,		1	, 			2, 					1, 				1, 				1, 				1.00000,						0, 				0, 				0, 								0, 						0, 						0, 			0, 							0, 						'1,2,3', 							0, 										0, 							-1, 								2, 				0, 				0.00000, 			0, 					0, 				1);";
		}
		if (!$res = $conn->query($sql))
		{ 
			throw new Exception($conn->error);
		}
		if (!isset($smart_link_id)) { $smart_link_id = $conn->insert_id; }
		
		
		// Tags
		if (isset($tag_id))
		{
			$sql = "REPLACE INTO `tags` (`id`,`title`, 				`smart_link_id`, `default_publisher`,`redirect_method`, `default_url`, `width`, `height`, `creative_id`, `fraud_type_id`, `status`, `write_stats_ratio`, `ip_list_id`, `fraudhunt_req`, `use_blacklist_ip`, `use_blacklist_ip_range`, `use_blacklist_referrer`, `p0f`, `use_blacklist_subid`, `check_idfa_deviceid`, `max_mind_detection_type`, `extended_device_detection_type`, `save_additional_data`, `conncetion_type_handling`, `use_rabbit`, `auto_add_ip_blacklist`, 		`cpm`, `allowed_serving_code`, `redis_ui_product_ttl`, `debug_mode`, `client_id`, `clicks_distribution`, `click_balancing`) VALUES 
					({$tag_id},'{$title} - Feed Tag', '{$smart_link_id}', {$publisher_id},				'-9', 				'-9', 			'-9', 	'-9', 		'12', 			'-9', 			'1',	'1.00000', 				'-9', 				'-9', 			'-9', 					'-9', 							'-9', 					'-9', 		'-9', 						'-9',						 '-9', 									'-9', 								'-9', 							'-9', 				'-9', 							'-9', 		'0.00000',	 '-9', 								'-9',						'0', 			'1', 				'', 								'0');";			
		}
		else
		{
			$sql = "INSERT INTO `tags` (`title`, 				`smart_link_id`, `default_publisher`,`redirect_method`, `default_url`, `width`, `height`, `creative_id`, `fraud_type_id`, `status`, `write_stats_ratio`, `ip_list_id`, `fraudhunt_req`, `use_blacklist_ip`, `use_blacklist_ip_range`, `use_blacklist_referrer`, `p0f`, `use_blacklist_subid`, `check_idfa_deviceid`, `max_mind_detection_type`, `extended_device_detection_type`, `save_additional_data`, `conncetion_type_handling`, `use_rabbit`, `auto_add_ip_blacklist`, 		`cpm`, `allowed_serving_code`, `redis_ui_product_ttl`, `debug_mode`, `client_id`, `clicks_distribution`, `click_balancing`) VALUES 
					('{$title} - Feed Tag', '{$smart_link_id}', {$publisher_id},				'-9', 				'-9', 			'-9', 	'-9', 		'12', 			'-9', 			'1',		'1.00000', 				'-9', 				'-9', 			'-9', 					'-9', 							'-9', 					'-9', 		'-9', 						'-9',						 '-9', 									'-9', 								'-9', 							'-9', 				'-9', 							'-9', 		'0.00000',	 '-9', 								'-9',						'0', 			'1', 				'', 								'0');";
		}
		if (!$res = $conn->query($sql))
		{ 
			throw new Exception($conn->error);
		}
		if (!isset($tag_id)) { $tag_id = $conn->insert_id; }


		// Users
		$no_space_title = str_replace(" ","",$title);
		if (isset($user_id))
		{
			$sql = "REPLACE INTO `users` (`id`, `client_id`, `name`, `password`, `note`, `status`, `privileges`, `user_type`, `allowed_fields`) VALUES 
										({$user_id},1, '{$no_space_title}-CPI', 'c408b3146ed415666bb8b5055c798a42',   'pwd: a8e0afccd9', 1, '37,61,63', 2, 'imp_date,imp_hour,country,device_id,sub_id1,sub_id2,sl_rule_id,imps,clicks,convs,income,epi');";			
		}
		else
		{
			$sql = "INSERT INTO `users` (`client_id`, `name`, `password`, `note`, `status`, `privileges`, `user_type`, `allowed_fields`) VALUES 
										(1, '{$no_space_title}-CPI', 'c408b3146ed415666bb8b5055c798a42',   'pwd: a8e0afccd9', 1, '37,61,63', 2, 'imp_date,imp_hour,country,device_id,sub_id1,sub_id2,sl_rule_id,imps,clicks,convs,income,epi');";
		}
		
		if (!$res = $conn->query($sql))
		{ 
			throw new Exception($conn->error);
		}
		if (!isset($user_id)) { $user_id = $conn->insert_id; }
		



		
		
		// Smart Links Rules
		$sql = "INSERT INTO `smart_link_rules` (`smart_link_id`	,`group_id`	, `country`	, `device`, `product_id`,`title`, `description`, `advertiser_id`, `weight`, `status`, `is_cookie`, `is_redirect`,`app_name_ids`, `smart_link_subgroups_list_types`, `client_id`, `clicks_cap`, `click_cap_timeframe`) VALUES 
										({$smart_link_id}	,{$slr_groups_id}	, 'WW'		, 'iOS - phone', 421859, 'Intergation Test', 'Intergation Test', 20, 1, 1, 0, 1, NULL, '18', 1, -9, -9),
										({$smart_link_id}	, {$slr_groups_id}, 'WW'		, 'Android - phone', 421859, 'Intergation Test', 'Intergation Test', 20, 1, 1, 0, 1,  NULL, '18', 1, -9, -9);";
		if (!$res = $conn->query($sql))
		{ 
			throw new Exception($conn->error);
		}
		
		$sql = "select id
				from smart_link_rules
				where smart_link_id = {$smart_link_id}";
		if (!$res = $conn->query($sql))
		{ 
			throw new Exception($conn->error);
		}

		$sql = 'INSERT INTO `smart_link_rules_subgroups` (`smart_link_rule_id`, `list_type`, `priority`, `sub_group`, `weight`, `status`, `creation_type`, `client_id`) VALUES ';
		while ($row = $res->fetch_assoc())
		{
			$sql = "{$sql}\n({$row['id']}, 18, 1.00000, '{$row['id']}', 10000, 1, 2, 1),";
		}
		$sql = rtrim($sql, ",");
		if (!$res = $conn->query($sql))
		{ 
			throw new Exception($conn->error);
		}
		
		
		
		
		
		// Publisher Update
		$sql = "update publishers set allowed_user = {$user_id}, network_tag = {$tag_id}, network_api_password = md5(now()) where id = {$publisher_id}";
		if (!$res = $conn->query($sql))
		{ 
			throw new Exception($conn->error);
		}
		
		
		
		
		
		// Output To Screen
		$sql = "select p.id,concat('Admin: https://partner.le-sha.com\n',
						 'User: ', u.name, '\n', 
						 u.note, '\n',
						 'Api Documentation: https://partner.le-sha.com/admin/api_doc/doc.php?iframe=1&u=',md5(u.id),'\n'
						 'API KEY: ', network_api_password,'\n'
						 ) as pub_data, 
						 concat(	'Admin: https://partner.le-sha.com\n',
		 						 	'User: ', u.name, '\n', 
						 			 u.note, '\n\n',
						 			'API Instructions:\n',
						 			'1) Click on Demand source > New Demand Source:\n',
						 			'2) Use Search to choose Affise network type:\n',
									'3) Fill in the fields with API credentials: \n' ,
									' 			Base URL: 	https://api-lesha.affisse.com/\n',
									' 			API Key:		',network_api_password, '\n',
									' 	 Use These Macros:\n',
									'			sub1	-	Your Click ID\n',
									'			sub2	-	Your sub-ID\n',
									'			sub3	-	iOS Device ID\n',
									'			sub4	-	Android ID\n',
									'			sub5	-	App Name\n\n\n'																																				
									' See API Documentation: https://partner.le-sha.com/admin/api_doc/doc.php?iframe=1&u=',md5(u.id),'\n'
									 ) as pub_data_affise
				from publishers as p
				join users as u
				on (p.allowed_user = u.id)
				where p.id = {$publisher_id}";
				
		$sql = "
				select CONCAT('Basic API:###',basic_api, '###------------------------------------------------############',
				  'Affise Based API:###',affise_based_api_basic,'###------------------------------------------------############',
				  'For Publishers Using Affise:###',publishers_using_affise_api) AS pub_data
				  FROM (
					select p.id,concat('Admin: https://partner.le-sha.com###',
						 'User: ', u.name, '###', 
						 u.note, '###',
						 'Api Documentation: https://partner.le-sha.com/admin/api_doc/doc.php?iframe=1&u=',md5(u.id),'###'
						 'API KEY: ', network_api_password,'###'
						 ) as basic_api, 
						 concat(	'Admin: https://partner.le-sha.com###',
		 						 	'User: ', u.name, '###', 
						 			 u.note, '######',
						 			'API Instructions:###',
						 			'similar to affise api.###',
						 			'1) Use this URL: https://api-lesha.affisse.com/###',
									'2) Use This API Key:		',network_api_password, '###',
									' 	 Use These Macros:###',
									'			scidu	     -	Your Click ID###',
									'			sub_id1	  -	Your sub-ID###',
									'			idfa	     -	iOS Device ID###',
									'			androidid  -	Android ID###',
									'			app_name_s -	App Name#########'																																				
									' See API Documentation: https://partner.le-sha.com/admin/api_doc/doc.php?iframe=1&u=',md5(u.id)) AS affise_based_api_basic,
						 concat(	'Admin: https://partner.le-sha.com###',
		 						 	'User: ', u.name, '###', 
						 			 u.note, '######',
						 			'API Instructions (for Affise publishers using CPAPI):###',									
									' 1) Add demand source named TrafficMizer.###',
									' 2) Type Title: le-sha###',
									' 3) Fill The API Key: ',network_api_password, '###',
									' 4) Fill In sysid: 1###',
									' 5) Fill In scidu: {clickid}###',
									' 6) Fill In sub_id1: {pid}_{Your sub-ID}###',
									' 7) Fill In idfa: {iOS Device ID param}###',
									' 8) Fill In androidid: {Android ID param}###',
									' 9) Fill In app_name_s: {App Name param}###',
									' #########',
									' ALEX: ###',
									' ----- ###',
									' To Test The API, Check This URL - sould return offers in a json format: ###',
									' https://api.le-sha.com/network_api?api-key=',network_api_password,'&sysid=1&limit=10&PAGE=1 ###') as publishers_using_affise_api
				from publishers as p
				join users as u
				on (p.allowed_user = u.id)
				where p.id = {$publisher_id} ) AS qq";				
				

		if (!$res = $conn->query($sql))
		{ 
			throw new Exception($conn->error);
		}
		$row = $res->fetch_assoc();
		$return['row'] = $row;
		
	}
	catch(Exception $e) 
	{
	  $return['sucssess'] = false;
	  $return['error'] = $e->getMessage();
	}
	
	return($return);
}

?>