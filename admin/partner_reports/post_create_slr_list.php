<?php

ob_start();

//print_r($_GET);
//print_r($_POST);

$slr_id 		= $_GET['slr_id'];
$list_type_id 	= $_GET['list_type_id'];
$behavior 		= $_GET['behavior'];
$id_list 		= $_POST['id_list'];
$ids_array		= explode(",",$id_list);
$count_rows 	= sizeof($ids_array);
$ret_array 		= array();


try
{
	require_once  dirname(dirname(dirname(__FILE__))). '/general/general.php';  
	$conn = get_db_conn();
	if (!isset($conn)or $conn->connect_error) 
	{
		$ret_array['error'] = "Error: Connection failed:" .$conn->connect_error;
		throw new Exception();
	}

	$list_for_slr = "SELECT list1, list2,list3,list4,list5
						FROM smart_link_rules
						WHERE id = {$slr_id} and status = 1";
	$ret_array['sql'][0] = $list_for_slr;
	$result = $conn->query($list_for_slr);
	$lists_for_slrs=mysqli_fetch_array($result,MYSQLI_ASSOC);
	//print_r($lists_for_slrs);
	if (sizeof($lists_for_slrs) == 0)
	{
		$ret_array['error'] = ": Smart Link Rule " . $slr_id . " -  No Longer Active";
		throw new Exception();
	}
	
	$list_id = 0;
	$empty_list_number = "";
	foreach ($lists_for_slrs as  $list_number=>$curr_list_id)
	{
	    if ($curr_list_id != "" and $curr_list_id != -1)
		{
			$check_list = "	SELECT *
							FROM lists
							WHERE id = {$curr_list_id} AND STATUS = 1 AND behavior = {$behavior} AND list_type_id = {$list_type_id}";
			
			$ret_array['sql'][1] = $check_list;
			$result = $conn->query($check_list);
			if (mysqli_fetch_array($result,MYSQLI_ASSOC))
			{
				// Suitable list was found, append to it
				$list_id = $curr_list_id;
				break;
			}
		}
		elseif ($empty_list_number == "")
		{
			$empty_list_number = $list_number;
		}
	}
	
	if ($list_id == 0)
	{
		// create a new list for this smart_link_rule, use $empty_list_number
		$black_white_list = ($behavior == 0) ? "BL" : "WL";
		$create_list_sql = "INSERT INTO `lists` (`list_type_id`, `behavior`, `product_id`, `title`, `status`, `client_id`, `creation_type`) VALUES ( {$list_type_id}, {$behavior}, NULL, '{$slr_id}_{$list_type_id}_{$black_white_list}', 1, 1, 1);";
		$ret_array['sql'][2] = $create_list_sql;
		if ($conn->query($create_list_sql) === TRUE) 
		{
			$list_id = $conn->insert_id;
			//echo "New record created successfully. Last inserted ID is: " . $list_id;
		} 
		else 
		{
		//	echo "Error: " . $create_list_sql . "<br>" . $conn->error;
			$ret_array['error'] = "Error: " . $create_list_sql . "<br>" . $conn->error;
			throw new Exception();
		}
		
		
		
		// now update the smart link rule to use this list
		$update_new_list_to_sl = "UPDATE smart_link_rules SET {$empty_list_number}='{$list_id}' WHERE id={$slr_id};";
		$ret_array['sql'][3] = $update_new_list_to_sl;
		if ($conn->query($update_new_list_to_sl) === TRUE) 
		{
			//$list_id = $conn->insert_id;
			//echo "New record created successfully. Last inserted ID is: " . $list_id;
		} 
		else 
		{
			$ret_array['error'] = "Error: " . $update_new_list_to_sl . "<br>" . $conn->error;
			throw new Exception();
		}
	}
	
				
	if ($list_id != 0 and $count_rows > 0)
	{
		$insert_rows = 0;
		$sql_insert = "INSERT INTO `list_items` (`list_id`, `item_string`, `status`) VALUES "; 
		foreach ($ids_array as $curr_item_string)
		{
			$insert_rows += 1;	
			$sql_insert = "{$sql_insert}\n ('{$list_id}', '{$curr_item_string}' ,1),";						
		}
		$sql_insert = rtrim($sql_insert, ",") . "\nON DUPLICATE KEY UPDATE status = 1";
		$ret_array['sql'][4] = $sql_insert;
		if ($conn->query($sql_insert) === TRUE) 
		{
			//$list_id = $conn->insert_id;
			//echo "New record created successfully. Last inserted ID is: " . $list_id;
		} 
		else 
		{
			$ret_array['error'] = "Error: " . $sql_insert . "<br>" . $conn->error;
			throw new Exception();
		}
	}
	else
	{
		$ret_array['error'] = "Error: list_id: " . $list_id . "<br>count_rows:" . $count_rows;
		throw new Exception();
	}
	$ret_array['sucssess'] = 1;
	$ret_array['rows_inserted'] = $count_rows;
	
	
}
catch(Exception $e) 
{
	$ret_array['sucssess'] = 0;

}

$out = ob_get_contents();
ob_end_clean();	   					

echo json_encode($ret_array);
?>