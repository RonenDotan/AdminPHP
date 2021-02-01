<?php
$multi_action 	= $_GET['multi_action'];

require_once '/var/www/html/admin/config.php';
if (isset($conn) and !isset($GLOBALS["conn"]))
{
	$GLOBALS["conn"] = $conn;
}

 switch($multi_action) 
 {
 	case "duplicate":
		duplicate();
		break;
 	case "enable":
		enable_disable(1);
		break;
 	case "disable":
		enable_disable(0);
		break;
	case "multi_edit":
	    multi_edit();
		break;		
	case "placeholder2":
		placeholder2();
		break;				
	case "placeholder3":
		placeholder3();
		break;					
	default:
		$ret_array = array();
		$ret_array['sucssess'] 	= False;
		$ret_array['error'] 	= "Action Not Defined";
		$ret_array['output'] 		= "";
		echo json_encode($ret_array);
		die();
 }
 
 
 function duplicate()
 {
 	ob_start();
	$ret_array = array();
	$ret_array['sucssess'] 		= true;
	$ret_array['error'] = "No Error";
 	$id_list 					= $_POST['id_list'];
	$src_table 					= $_POST['src_table'];
 	$conn 						= $GLOBALS["conn"];
	echo __FUNCTION__ ."\n".str_repeat("-", strlen(__FUNCTION__)+3)."\n" ;
 	echo "Performing On IDs: {$id_list} \n";
	
	$sql_columns = "SELECT GROUP_CONCAT(column_name) AS columns
					FROM information_schema.`COLUMNS`
					WHERE table_name = '{$src_table}' AND extra != 'auto_increment'
					";		
					
	$result = $conn->query($sql_columns);
	$columns=mysqli_fetch_array($result,MYSQLI_ASSOC)['columns'];
	
	$result = $conn->query("SELECT max(id) as max_id_pre FROM {$src_table}");
	$max_id_pre=mysqli_fetch_array($result,MYSQLI_ASSOC)['max_id_pre'];
	
	$columns_handle_title_copy = str_replace("title","concat(title,'-copy') as title",$columns);
	$sql_insert = "INSERT INTO {$src_table} ({$columns})
				   SELECT {$columns_handle_title_copy}
				   FROM {$src_table}
				   WHERE id in ({$id_list})";
	echo "\n\n". $sql_insert;
	$result = $conn->query($sql_insert);
	if($result)
	{
		$new_ids_sql = "SELECT group_concat(id) as new_ids
						FROM {$src_table}
						WHERE id > {$max_id_pre}";
		$result = $conn->query($new_ids_sql);
		$new_ids=mysqli_fetch_array($result,MYSQLI_ASSOC)['new_ids'];
		$new_ids_array = explode(",",$new_ids);
		$new_ids_count = sizeof($new_ids_array);
		if ($new_ids_count > 0)
		{
			Echo "Inserted {$new_ids_count}";
			$ret_array['rows_inserted']	= $new_ids_count;
			$ret_array['ids_list'] 		= $new_ids;
			
		}
		else
		{
			echo "Error - No ID Inserted";
			$ret_array['error'] = "Error - No ID Inserted";
			$ret_array['sucssess'] 		= false;
		}
											
	}
	else
	{
		echo "Error";
		$ret_array['error'] = "Error " .mysqli_error($conn);
		$ret_array['sucssess'] 		= false;
	}
		
	$out = ob_get_contents();
	ob_end_clean();	   					
	
	
	$ret_array['output'] 		= $out;
	echo json_encode($ret_array);
 }
 
 
 function enable_disable($is_enable)
 {
 	ob_start();
	$ret_array = array();
	$ret_array['sucssess'] 		= true;
	$ret_array['error'] = "No Error";
 	$id_list 					= $_POST['id_list'];
	$src_table 					= $_POST['src_table'];
 	$conn 						= $GLOBALS["conn"];
	echo __FUNCTION__ ."\n".str_repeat("-", strlen(__FUNCTION__)+3)."\n" ;
 	echo "Performing On IDs: {$id_list} \n";
	
	$sql_columns = "SELECT column_name as qq
					FROM information_schema.`COLUMNS`
					WHERE table_name = '{$src_table}' AND find_in_set(column_name,'status,enable,enabled') > 0
					";		
					
	$result = $conn->query($sql_columns);
	$column_name=mysqli_fetch_array($result,MYSQLI_ASSOC)['qq'];
	
	//$result = $conn->query("SELECT max(id) as max_id_pre FROM {$src_table}");
	//$max_id_pre=mysqli_fetch_array($result,MYSQLI_ASSOC)['max_id_pre'];
	
	$sql_update = "UPDATE {$src_table}
				   SET {$column_name} = {$is_enable}
				   WHERE id in ({$id_list})";
	echo "\n\n". $sql_update;
	$result = $conn->query($sql_update);
	if($result)
	{
		
	}
	else
	{
		echo "Error - No Update";
		$ret_array['error'] = "Error " .mysqli_error($conn);
		$ret_array['sucssess'] 		= false;
	}
	
		
	$out = ob_get_contents();
	ob_end_clean();	   					
	
	
	$ret_array['output'] 		= $out;
	echo json_encode($ret_array);
 }

 
 
 function multi_edit()
 {
     ob_start();
     $ret_array = array();
     $ret_array['sucssess'] 		= true;
     $ret_array['error'] = "No Error";
     $id_list  = $_POST['id_list'];
     $edit_url = $_POST['edit_url'];
     //$conn 						= $GLOBALS["conn"];
     echo __FUNCTION__ ."\n".str_repeat("-", strlen(__FUNCTION__)+3)."\n" ;
     echo "Performing On IDs: {$id_list} \n";
     $out = ob_get_contents();
     ob_end_clean();
     
     
     
     $ret_array['output'] 		= $out;
     // $ret_array['pop_url'] 		= str_replace("multi_actions.php","cfg.php","{$_SERVER['SCRIPT_NAME']}?mod=edit&multi_ids={$id_list}");
     $ret_array['pop_url']          = "{$edit_url}&multi_ids={$id_list}";
     $ret_array['no_reload']		= "1";
     $ret_array['pop_title']		= "Multi Edit {$title_to_display}";
     $ret_array['pop_properties']= "height=600,width=900,left=300,top=100,resizable=yes,scrollbars=yes,toolbar=yes,menubar=no,location=no,directories=no,status=yes";
     
     
     echo json_encode($ret_array);
 }
 
 
function placeholder2()
{
	ob_start();
	$ret_array = array();
	$ret_array['sucssess'] 		= true;
	$ret_array['error'] = "No Error";
	echo __FUNCTION__ ."\n".str_repeat("-", strlen(__FUNCTION__)+3)."\n" ;
	
	$out = ob_get_contents();
	ob_end_clean();	   					
	
	
	$ret_array['output'] 		= $out;
	echo json_encode($ret_array);
}
 
function placeholder3()
{
	ob_start();
	$ret_array = array();
	$ret_array['sucssess'] 		= true;
	$ret_array['error'] = "No Error";
	echo __FUNCTION__ ."\n".str_repeat("-", strlen(__FUNCTION__)+3)."\n" ;
	
	$out = ob_get_contents();
	ob_end_clean();	   					
	
	
	$ret_array['output'] 		= $out;
	echo json_encode($ret_array);
}
  
?>