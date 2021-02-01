<?php
include_once '/var/www/html/admin/config.php';

$edit_url = $_SERVER['PHP_SELF']."?mod=edit";
if (isset($_GET['dev']))
{
	$dev_num = 1;
	echo "<pre class='dev{$dev_num}'>";
	echo "\nGlobal\n";
	
	echo "GET\n";
	print_r($_GET);
	
	
	echo "POST\n";
	print_r($_POST);	
	
	echo "\n\n"; 
	echo "SERVER\n";
	print_r($_SERVER);
	echo "</pre>";   
}

?>
<html>
<head>

<script id='script_table2csv' defer src='/admin/table2CSV.js'></script>

<script id='script_jquery_ui' defer type="text/javascript" src="/admin/menu/jquery-ui.min.js"></script> 

<script id='script_jquery_tablesorter' defer type="text/javascript" src="/admin/menu/tablesorter-master/dist/js/jquery.tablesorter.min.js"></script> 

<script id='script_jquery_tablesorter_widgets' defer type="text/javascript" src="/admin/menu/tablesorter-master/dist/js/jquery.tablesorter.widgets.min.js"></script> 

<script id='script_jquery_tablesorter_dragtable' defer type="text/javascript" src="/admin/menu/tablesorter-master/dist/js/extras/jquery.dragtable.mod.min.js"></script> 

</head>


<?php 

if (isset($form_method) and strtolower($form_method) == 'post')
{
	$_GET = array();
	$_GET = $_POST;
	?>

	<script id="script_submit_process">
	var cfg_fields = <?php echo json_encode($fields); ?>;
	
	function process(event) 
	{
		//event.preventDefault();
		event.target.action = '<?php echo $_SERVER['PHP_SELF']; ?>';
		var formData = new FormData(event.target);
		var asString = new URLSearchParams(formData).toString();
		var vars = asString.split('&');
		var uri_params = "";
		for (var i=0; i < vars.length; i++) 
		{
			var pair = vars[i].split('=');
			var remove_from_uri = false;
			if (cfg_fields[pair[0]] != undefined)
			{
				if (cfg_fields[pair[0]].remove_from_uri != undefined)
				{
					if (cfg_fields[pair[0]].remove_from_uri)
					{
						remove_from_uri = true;
					}
				}
			}
			if (!remove_from_uri)
			{
				uri_params = uri_params + pair[0] + "=" + decodeURIComponent(pair[1]) + "&";
			}
		}
		uri_params = uri_params.substring(0, uri_params.length - 1);
		console.log(uri_params)
		event.target.action += '?' + uri_params;

	}
	</script>	
	
	
	<?php
	
	echo "<form class='form-container' method='post' onsubmit='return process(event)'>";
}
else
{
	echo "<form class='form-container' method='get'>";
}
echo "<div class='form-title'><h2>{$title_to_display}</h2></div>";
if  (isset($tabs))
{
    echo "<div class='tab'>";
    foreach ($tabs as $tab)
    {
        echo "<button type='button' class='{$tab['class']}' oncontextmenu='javascript:window.open(\"{$tab['url']}\", \"_blank\");return false;' onclick='javascript:window.location.replace(\"{$tab['url']}\");'>{$tab['title']}</button>";
    }
    echo "</div>";
}

if (file_exists('filter.php'))
{
	// Use Local Filter
	include_once 'filter.php';
}
else
{
	// Use General Filter
	include_once '/var/www/html/admin/filter.php';
}



if (function_exists("before_view"))
{
	before_view();
}

$export_excel_only = (isset($_GET['export_excel_only']) and $_GET['export_excel_only'] == 'Export To CSV') ? true : false;	
if ($export_excel_only)
{
	ob_start();
}
else
{
	echo "<div id='please_wait' align='center' valign='center'><img class='please_wait'></img></div>
	<div id='pre_table_container' class='data-filter' style='filter:alpha; opacity:0;'>
	<table class='tab_total_add tablesorter'>
	<tr><td>Total Rows: <label id='upper_row_count'></label></td>
		<td>Edit On View:</td>
		<td><div id='show_edit_on_view_toggle_div' class='center' hidden><input type='checkbox' id='cbx' style='display:none' onchange='edit_on_view_cbx_change(this.checked)'/><label for='cbx' class='toggle'><span>edit</span></label></div></td>
		<td><a href='{$edit_url}'>Add New</a></td></tr>
	</table><div class='datagrid' style='filter:alpha; opacity:1;'>";     																									   
}
$temp = array();
array_push($temp, 'id');
foreach ($fields as $field_name=>$field_data)
{
    if (isset($field_data['db_field']))
	{
		array_push($temp, $field_data['db_field']);
	}
}
$sql_select_fields = implode(",",$temp);


if (isset($query) == false)
{
    $query = "SELECT {$sql_select_fields} FROM {$src_table} WHERE {$where} ";      
}
else 
{
	$query = str_replace('##where##', $where, $query);
	//echo $query;
	// die();
}

$offset = (isset($_GET['offset']) and $_GET['offset'] > 0) ? $_GET['offset'] : 0;
$limit = (isset($_GET['limit']) and $_GET['limit'] > 0) ? $_GET['limit'] : 500;
$order_type = (isset($_GET['order_type']) and $_GET['order_type'] != '')  ? $_GET['order_type'] : 'ASC';
$order_by = (isset($_GET['order_by']) and $_GET['order_by'] != '') ? $_GET['order_by'] : 'id';
$query = "{$query} ORDER BY {$order_by} {$order_type} LIMIT {$limit} offset {$offset}";



// Add More Fields - by group
if (isset($_GET['slr_group']) and $_GET['slr_group'] != '-1')
{
	$query = str_replace("WHERE","WHERE id in (SELECT product_id From smart_link_rules where group_id = {$_GET['slr_group']}) AND",$query);
}

// Add More Fields - by group
if (isset($_GET['price_range_from']) and $_GET['price_range_from'])
{
	$query = str_replace("WHERE","WHERE (GREATEST(ifnull(revenue_per_conversion,0),ifnull(revenue_per_action,0)) >= {$_GET['price_range_from']}) AND",$query);
	
	//$query = str_replace("WHERE","WHERE id in (SELECT product_id From smart_link_rules where group_id = {$_GET['slr_group']}) AND",$query);
}

// Add More Fields - by group
if (isset($_GET['price_range_to']) and $_GET['price_range_to'])
{
	$query = str_replace("WHERE","WHERE (GREATEST(ifnull(revenue_per_conversion,0),ifnull(revenue_per_action,0)) <= {$_GET['price_range_to']}) AND",$query);
}

if (true)
{
    try 
    {
        $sql_total_count = "SELECT count(*) as total_rows". substr($query, stripos($query,"FROM") -1,(stripos($query,"ORDER BY")-stripos($query,"FROM")));
        $result = $conn->query($sql_total_count);
        $total_rows = number_format(mysqli_fetch_array($result,MYSQLI_ASSOC)['total_rows']);
    } 
    catch (Exception $e) 
    {
        $total_rows = -1;
    }
}


unset($fields['offset'],$fields['limit'],$fields['order_type'],$fields['order_by'], $fields['slr_group'],$fields['price_range_to'],$fields['price_range_from']);
if (isset($_GET['dev']))
{
	$dev_num += 1;
	echo "<pre class='dev{$dev_num}'>";
	echo $query . "\n";
	echo "</pre>"; 
}
$clean_get = $_GET;
unset($clean_get['mod']);
unset($clean_get['iframe']);
//print_r($clean_get);
if (sizeof($clean_get) == 0)
{
	echo "<script>filter_button1.click()</script>";

	if (isset($default_off))
	{
	?>
		<script>
		var cols_array = [];
		document.getElementById('please_wait').hidden = true;
		pre_table_container.style.opacity = 1;
		if (typeof onload_fuctions !== 'undefined')
		{
			for (i=0; i < onload_fuctions.length; i++)
			{
				onload_fuctions[i]();
			}
		}
		</script>
		<?php 
		die();
	}
}
if (!$export_excel_only) { echo "<script>console.log(`Query:\n{$query}`);</script>"; }
$result = $conn->query($query);
$count_rows = $result->num_rows;						
$last_result_index = $offset+$count_rows;

$row=mysqli_fetch_array($result,MYSQLI_ASSOC);
$columns_array = array();

// echo "<div style='z-index: 500; position: absolute;top: 0; left: 0;' id='col_fix_please_wait' hidden><img class='please_wait' style='width: 100%; opacity :0.70' ></div>";
if ($export_excel_only)
{
	echo "ID,";
	
	foreach ($fields as $field)
	{
		if (!isset($field['hidden']))
		{
			echo str_replace(",",";",$field['name']) .",";
		}
	}
}
else
{
	echo "<center><div style='z-index: 500; position: absolute; margin-left: 10cm;' id='col_fix_please_wait' hidden><img class='please_wait' style='opacity :0.70; width: 100px;' ></div></center>";
	echo '<table class="sortable tablesorter" id="main_view_table">';
	echo "<thead>";
    echo "<tr>";
	
	if (isset($multi_actions_array))
	{
		echo "<th class='fixed_col_header' style='z-index: 150;'><input type='checkbox' class='header_checkbox' onchange='change_col(this)'><br>All<input type='checkbox' value='all_rows' name='id_checkbox_all' onchange='select_deselect_all(this.checked)'></th>";
	}

	echo "<th class='fixed_col_header' style='z-index: 150;'><input type='checkbox' class='header_checkbox' onchange='change_col(this)'><br></div>ID</th>";
	$cols_array[0] = "checkbox";
	$cols_array[1] = "ID";
	foreach ($fields as $field)
	{
		if (!isset($field['hidden']))
		{
			echo "<th class='drag-enable fixed_col_header' style='z-index: 150;'><input type='checkbox' class='header_checkbox' onchange='change_col(this)'><br>{$field['name']}</th>";
			array_push($cols_array, $field['db_field']);
		}
	}
	echo "</tr>";
	echo "</thead>";
	
	$activate_table_footer = false;
	if ($activate_table_footer)
	{
		echo "<tfoot><tr>";
		echo "<th>CHECKBOX</th>";
		echo "<th>ID</th>";
		foreach ($fields as $field)
		{
			if (!isset($field['hidden']))
			{
				echo "<th>{$field['name']}</th>";
			}
		}
		echo "</tr></tfoot>";
	}

	echo "<tbody>";
}


$show_edit_on_view_toggle = false;
while ($row != NULL) 
{
	if ($export_excel_only)
	{
		echo "\n{$row['id']},";
	}
	else
	{
		echo "<tr>";
		if (isset($multi_actions_array))
		{
			echo "<td><input type='checkbox' value='{$row['id']}' name='id_checkbox' onchange='select_row(this)'></td>";
		}
		if (isset($edit_url))
		{
			$edit_id_url = $edit_url ."&id=".$row['id'];
			$edit_id_url = str_replace(".php&", ".php?", $edit_id_url);
			echo "<td><a href='{$edit_id_url}'>".$row['id']."</td>";
		}
		else
		{
			echo "<td>".$row['id']. "</td>";
		} 
	}
	
	
    foreach ($fields as $field)
    {
        $classes = array();
        if (isset($field['edit_on_view']))
        {
            array_push($classes, 'edit_on_view');
            $show_edit_on_view_toggle = true;
        }

        
        if (sizeof($classes) > 0)
        {
            $class ="class='".implode(" ",$classes)."'";
            
        }
        else 
        {
            $class ="";
        }
        

		if (isset($field['hidden']))
		{
			continue;
		}

		if (isset($field["link_url"]) and $row[$field['db_field']] and !$export_excel_only)
		{
			$td_link_url = str_replace("{id}",$row[$field['db_field']],$field["link_url"]);
			$pre_href = "<a href='{$td_link_url}' target='_blank'>";
			$post_href = "</a>";
		}
		else
		{
			$pre_href = "";
			$post_href = "";
		}	
		
    	if (isset($field["drop_down_multi"]))
		{
			$curr_array = $field["drop_down"];
			if (isset($field['show_id']) and !$field['show_id'])
			{
				$value_array = explode(",",$row[$field['db_field']]);
				$values_selected = "";
				foreach ($value_array as $val_selected)
				{
					$values_selected = $values_selected.$curr_array[$val_selected].",<br>";
				}
				$values_selected = trim($values_selected,',<br>');
				if (!$export_excel_only){	echo "<td {$class}>".$values_selected."</td>"; } else { echo str_replace(",",";",$values_selected).","; }
			}
			else
			{
				$value_array = explode(",",$row[$field['db_field']]);
				$values_selected = "";
				foreach ($value_array as $val_selected)
				{
					$values_selected = $values_selected.$val_selected." :: ".$curr_array[$val_selected].",<br>";
				}
				$values_selected = trim($values_selected,',<br>');
	 
				if (!$export_excel_only){	echo "<td {$class}>".$values_selected."</td>"; } else { echo str_replace(",",";",$values_selected).","; }
	 
			}
		}
		elseif (isset($field["datalist_view"]))
		{
		    if (!isset($field['post_view_title_update']) or (isset($field['post_view_title_update']) and $field['post_view_title_update']))
		    {
		        if (!$export_excel_only) { echo "<td>{$row[$field['db_field']]}</td>"; } else { echo "{$row[$field['db_field']]}#{$field['db_field']},"; }
		        if (!isset($fields[$field['db_field']]['datalist_view']['ids_post_view_title_update'])) {$fields[$field['db_field']]['datalist_view']['ids_post_view_title_update'] = array();}
		        if (isset($row[$field['db_field']]) and strlen($row[$field['db_field']]) > 0)
		        {
		            array_push($fields[$field['db_field']]['datalist_view']['ids_post_view_title_update'],$row[$field['db_field']]);
		        }
		    }
		    else
		    {
    			if (!isset($fields[$array_field_name]["datalist_view"]["result"]))
    			{
    				$fields[$array_field_name]["datalist_view"]["result"] = fetch_id_title_for_view($conn,$field["datalist_view"]["query"]);
    			}
    			
				if (!$export_excel_only)
				{
					if (isset($field['show_id']) and !$field['show_id'])
					{
						echo "<td {$class}>{$pre_href}".$fields[$array_field_name]["datalist_view"]["result"][$row[$field['db_field']]]."{$post_href}</td>";
					}
					else
					{
						echo "<td {$class}>{$pre_href}".$row[$field['db_field']]." :: ".$fields[$array_field_name]["datalist_view"]["result"][$row[$field['db_field']]]."{$post}</td>";
					}
				}
				else
				{
					echo $row[$field['db_field']]." :: ".str_replace(",",";",$fields[$array_field_name]["datalist_view"]["result"][$row[$field['db_field']]]).",";
				}
		    }
		}
		elseif (isset($field["selectbox"]))
		{
			$curr_array = $field["selectbox"];
			if (isset($field['show_id']) and !$field['show_id'])
			{
				$value_array = explode(",",$row[$field['db_field']]);
				$values_selected = "";
				foreach ($value_array as $val_selected)
				{
					$values_selected = $values_selected.$curr_array[$val_selected].",<br>";
				}
				$values_selected = trim($values_selected,',<br>');
				if (!$export_excel_only) { echo "<td {$class}>".$values_selected."</td>"; } else {echo str_replace(",",";",$values_selected).",";}
			}
			else
			{
				$value_array = explode(",",$row[$field['db_field']]);
				$values_selected = "";
				foreach ($value_array as $val_selected)
				{
					$values_selected = $values_selected.$val_selected." :: ".$curr_array[$val_selected].",<br>";
				}
				$values_selected = trim($values_selected,',<br>');
	 
				if (!$export_excel_only) { echo "<td {$class}>".$values_selected."</td>"; } else {echo str_replace(",",";",$values_selected).",";}
	 
			}
		}
    	elseif (isset($field["drop_down"]))
		{
			if ($field['db_field'] == 'country')
			{
				if ($export_excel_only) { echo $row[$field['db_field']]. ","; } else {	echo "<td style='max-width: 200px;' {$class}>".$row[$field['db_field']]. "</td>";	}
			}
			else
			{
				$curr_array = $field["drop_down"];
				if (isset($field['show_id']) and !$field['show_id'])
				{																															
					if ($export_excel_only) { echo str_replace(",",";",$curr_array[$row[$field['db_field']]]).","; } else {echo "<td {$class}>{$pre_href}".$curr_array[$row[$field['db_field']]]."{$post_href}</td>";}
				}
				else
				{
					if ($export_excel_only) { echo $row[$field['db_field']]." :: ".str_replace(",",";",$curr_array[$row[$field['db_field']]]).","; } else { echo "<td {$class}>{$pre_href}".$row[$field['db_field']]." :: ".$curr_array[$row[$field['db_field']]]."{$post_href}</td>"; }
				}
			}
		} 
		elseif ($field['type'] == 'text')
        {
            if (!$export_excel_only) { echo "<td {$class}>{$pre_href}".$row[$field['db_field']]. "{$post_href}</td>"; } else { echo str_replace(",",";",$row[$field['db_field']]). ","; }
        }
		elseif ($field['type'] == 'textarea')
        {
			// Before New Change
			$short_text = substr(strip_tags($row[$field['db_field']]),-501,500);
			if ($export_excel_only) { echo preg_replace( "/\r|\n/", "", str_replace(",",";",$short_text)). ","; } else { echo "<td {$class}>" . $short_text. "</td>";    }
        } 
		else
		{
			
		}
    }
    if (!$export_excel_only) { echo "</tr>"; }
    $row=mysqli_fetch_array($result,MYSQLI_ASSOC);
	}
   
   
   	if ($export_excel_only)
	{
		$out = ob_get_contents();
		ob_end_clean();

	}
	else
	{
		echo "</tbody><tfoot></tfoot></table>";
	}
   
   
   // Now Check if need to query $ids_post_view_title_update to update the titles
   foreach ($fields as $field)
   {
       if (isset($field['datalist_view']['ids_post_view_title_update']) and sizeof($field['datalist_view']['ids_post_view_title_update']) > 0)
       {
           //die('here');
           $ids_post_view_title_update_str = implode(",",$field['datalist_view']['ids_post_view_title_update']);
           if (isset($field['drop_down']))
           {
               
           }
           elseif (isset($field['datalist_view']['query']))
           {
               $fields[$field['db_field']]["datalist_view"]["query"] = "{$field["datalist_view"]["query"]} AND id in ({$ids_post_view_title_update_str})";
               $fields[$field['db_field']]["datalist_view"]["result"] = fetch_id_title_for_view($conn,$fields[$field['db_field']]["datalist_view"]["query"]);
			   if ($export_excel_only)
			   {
				   foreach ($fields[$field['db_field']]["datalist_view"]["result"] as $k=> $v)
				   {
					   $v = str_replace(",",";",$v);
					   $out = str_replace("{$k}#{$field['db_field']}","{$k} :: {$v}",$out);
				   }
			   }
			   else
			   {
					$fields[$field['db_field']]["datalist_view"]["post_view_title_update_json"] = json_encode($fields[$field['db_field']]["datalist_view"]["result"]);
					if (!isset($post_view_title_cols_array)) {$post_view_title_cols_array = array();}
					array_push($post_view_title_cols_array, $field['db_field']);
			   }
           }
       }
   }   
   
   
   if ($export_excel_only)
	{
		//die();
		//$out = ob_get_contents();
		//ob_end_clean();
		file_put_contents(dirname(__FILE__)."/report.csv", $out);
		if (file_exists('download_csv.php'))
		{
			echo "<script>window.open('download_csv.php?table={$src_table}','_blank');</script>";
		}
		else
		{
			echo "<script>window.open('/admin/download_csv.php?table={$src_table}','_blank');</script>";
		}
		echo "<script>document.getElementById('please_wait').hidden = true;</script>";
		die();
	}   
   
   echo "</div><div>";
   echo '<div style="padding-top: 5;"><center><div style="display: inline-flex;"><button class="submit-button" type="button" onclick="previous_page();" style="padding: 5;font-size: 11;"><</button><div class="paging">'.$offset.' - '. $last_result_index .'</div><button class="submit-button" type="button" onclick="next_page();" style="padding: 5;font-size: 11;">></button></div></center></div>';
   
	 	// Multi Actions
	 	if (isset($multi_actions_array))
		{
			echo "<font><br>Actions:<br></font>";
			echo '<select id="multi_actions">';
			echo '<option value="-1">Please Select An Action:</option>';
			foreach ($multi_actions_array as $k=>$v)
			{
				echo "<option value='{$k}'>{$v}</option>";
			}
			echo '</select>';
			
			// Select Local Or Global multi_action Scripts
			$multi_action_file = file_exists("multi_actions.php") ? "multi_actions.php" : "/admin/multi_actions.php";
		
			echo '   <button type="button" onclick="multiAction()">Perform Action</button>';
			
			?>
			<script id="script_multi_action">
				function select_deselect_all(is_selected)
				{
					var checkboxes = document.getElementsByName('id_checkbox');

					for(var i = 0; i< checkboxes.length; i++)
					{
						checkboxes[i].checked = is_selected
						select_row(checkboxes[i])
					}
				}
			
				function multiAction()
				{
					var multi_action = document.getElementById("multi_actions").value
					
					if (multi_action == "-1")
					{
						alert("Please Select An Action");
					}
					else if (multi_action == 'export_csv')
					{
						export_view("<?php echo $src_table; ?>");
					}
					else if (multi_action == 'intable_input')
					{
						
						table_name = "<?php echo $src_table; ?>";  
						//console.log(table_name);

						rows_to_update = document.querySelectorAll(".intable_input");
						params = {};
						for (var i = 0; i < rows_to_update.length; i++)
						{
							var curr_item = rows_to_update[i];
							var split_id = curr_item.id.split("_");
							row_id = split_id[1];
							col_name = cols_array[split_id[2]];
							params[row_id] = ( typeof params[row_id] != 'undefined') ? params[row_id] : {}
							params[row_id][col_name] = curr_item.value;
						}
						console.log(params);
						$.post( "/admin/jquery/jq_post.php?form=intable_input",{src_table : table_name ,params : JSON.stringify(params)})
					  	.done(function( data ) {
								console.log(data)
					  			data= JSON.parse(data)
    							if (data.sucssess == true)
        						{
        							alert("Update Sucsseeded\nPlease Refresh The Page");
        							// history.go(-1);
        						}
    							else
    							{
    								alert("Error Occured " + data.error)
    							}
    							
					  	})
							
																								
					}
					else if (multi_action == 'generate_list')
					{
						debugger;
						var checkedBoxes = document.querySelectorAll('input[name=id_checkbox]:checked');
						if (checkedBoxes.length <= 0)
						{
							alert("No Item Was Selected");
						} 
						else
						{
							list_col_index = 0;
							
							
							var headerCheckedBoxes = document.querySelectorAll('input[class=header_checkbox]:checked')[0];
							if (headerCheckedBoxes)
							{
								// debugger;
								// list_col_index = +headerCheckedBoxes.parentElement.parentElement.parentElement.getAttribute('data-column');
								//list_col_index = +headerCheckedBoxes.closest('[data-column]').getAttribute('data-column');
								list_col_index = +headerCheckedBoxes.closest('[data-column]').dataset.column;
							}
							
							
							
							var id_list = ""
							if (list_col_index == 0)
							{
								for (i = 0; i < checkedBoxes.length; i++) 
								{
									id_list += checkedBoxes[i].value + ",";
								}
								id_list = id_list.substring(0, id_list.length - 1);
							}
							else
							{
								temp = [];
								for (i = 0; i < checkedBoxes.length; i++)
								{
									if (checkedBoxes[i].parentNode.parentNode.children[list_col_index].children[0] == undefined)
									{
										temp.push(checkedBoxes[i].parentNode.parentNode.children[list_col_index].innerText)
									}
									else if (checkedBoxes[i].parentNode.parentNode.children[list_col_index].children[0].href == undefined)
									{
										console.log("Unknown Line 495");
									}
									else
									{
										
										link = new URL(checkedBoxes[i].parentNode.parentNode.children[list_col_index].children[0].href);
										temp.push(link.searchParams.get("id"))
									}
								}
								unique_set = Array.from(new Set(temp));
								id_list = unique_set.join();
							}
							

							console.log("IDs: " + id_list);
							prompt("Copy to clipboard: Ctrl+C, Enter", id_list);
						}
					}
					else
					{
						var checkedBoxes = document.querySelectorAll('input[name=id_checkbox]:checked');
						if (checkedBoxes.length <= 0)
						{
							alert("No Item Was Selected");
						} 
						else
						{
							var id_list = ""
							for (i = 0; i < checkedBoxes.length; i++) 
							{ 
				    			id_list += checkedBoxes[i].value + ",";
							}
							id_list = id_list.substring(0, id_list.length - 1);
							 
							var path = "";
							console.log("Performing Action " + multi_action + " On IDs: " + id_list);
							$.post( "<?php echo $multi_action_file ?>?multi_action="+multi_action,{id_list: id_list, src_table : '<?php echo $src_table; ?>' ,form_data : $('form').serializeArray(), edit_url: '<?php echo $edit_url; ?>'})
							  	.done(function( data ) {
									console.log(data)
							  	data= JSON.parse(data)
		    					if (data.sucssess == true)
								{
									alert_text = "Succeeded\n";
									if (data.hasOwnProperty('rows_inserted'))
									{
										alert_text = alert_text + "New Rows: " + data.rows_inserted + "\n"
									}
									if (data.hasOwnProperty('ids_list'))
									{
										alert_text = alert_text + "IDs: " + data.ids_list
									}
									if (data.hasOwnProperty('pop_url'))
									{
										alert_text = alert_text + "pop_url: " + data.pop_url
										
										if (data.hasOwnProperty('post'))
										{
											var form = document.createElement("form");
											form.setAttribute("method", "post");
											form.setAttribute("action", data.pop_url);
											form.setAttribute("target", data.pop_title);
											var hiddenField = document.createElement("input");              
											hiddenField.setAttribute("name", "id_list");
											hiddenField.setAttribute("value", data.id_list);
											form.appendChild(hiddenField);
											document.body.appendChild(form);
										}
										
										
										popupWindow = window.open( data.pop_url, data.pop_title,data.pop_properties);
										if (data.hasOwnProperty('post'))
										{
											form.submit();
											setTimeout(function(){form.parentNode.removeChild(form);}, 100);
										}
									}
									
									
									if (data.hasOwnProperty('no_reload'))
									{
									}
									else
									{
										alert(alert_text);
										location.reload();
									}
								}
								else
								{
									alert("Error Occured " + data.error)
								}    	
		    						console.log(data);
		  						});
						}
					}
				}
			</script>
			
			<?php
		}
		
	 	if (function_exists("after_view"))
		{
			after_view();
		}
		

?>

</div></div></form>
</html>

<?php
if (isset($show_edit_on_view_toggle) and $show_edit_on_view_toggle)
{
    $edit_only_fields = true;
    echo "<div id='hidden_form' hidden>";
    if (file_exists("edit.php"))
    {
        include "edit.php";
    }
    else
    {
        include "/var/www/html/admin/edit.php";
    }
    echo "</div>";
    ?>
    <script id="script_edit_on_view">
    edit_template_form = document.getElementsByClassName("data-main-div")[0]
    var template_edit_fields = {};
    temp_fields = edit_template_form.getElementsByClassName("form-field-edit")
    for (var i = 0; i < temp_fields.length; i++)
    {
        template_edit_fields[temp_fields[i].name] = temp_fields[i];
    }
    delete temp_fields;
    delete edit_template_form;
    hidden_form.remove();
    </script>
    <?php 
}
?>


<script defer id="script_main_view_fix">
var cols_array 				= <?php echo json_encode($cols_array); ?>;
var rows 					= document.querySelectorAll('.sortable>tbody>tr')
var checkbox_header_row 	= document.querySelector('.sortable>thead>tr');;
var main_header_row 		= document.querySelector('.sortable>thead>tr');;
var filter_cols_by_dc = 	[];



function main_view_fix()
{
	if (true)
	{
		
		upper_row_count.innerText="<?php echo "{$total_rows}"; ?>";
		
		
		$(function() {
			  $("#main_view_table")
								.dragtable({
    // *** new dragtable mod options ***
    // this option MUST match the tablesorter selectorSort option!
    sortClass: '.sorter',
    // this function is called after everything has been updated
    tablesorterComplete: function(table) { 
									console.log('tablesorterComplete'); 
									fix_filter_js();
									},

    // *** original dragtable settings (non-default) ***
    dragaccept: '.drag-enable',  // class name of draggable cols -> default null = all columns draggable

    // *** original dragtable settings (default values) ***
    revert: false,               // smooth revert
    dragHandle: '.table-handle', // handle for moving cols, if not exists the whole 'th' is the handle
    maxMovingRows: 40,           // 1 -> only header. 40 row should be enough, the rest is usually not in the viewport
    excludeFooter: false,        // excludes the footer row(s) while moving other columns. Make sense if there is a footer with a colspan. */
    onlyHeaderThreshold: 100,    // TODO:  not implemented yet, switch automatically between entire col moving / only header moving
    persistState: null,          // url or function -> plug in your custom persistState function right here. function call is persistState(originalTable)
    restoreState: null,          // JSON-Object or function:  some kind of experimental aka Quick-Hack TODO: do it better
    exact: true,                 // removes pixels, so that the overlay table width fits exactly the original table width
    clickDelay: 10,              // ms to wait before rendering sortable list and delegating click event
    containment: null,           // @see http://api.jqueryui.com/sortable/#option-containment, use it if you want to move in 2 dimesnions (together with axis: null)
    cursor: 'move',              // @see http://api.jqueryui.com/sortable/#option-cursor
    cursorAt: false,             // @see http://api.jqueryui.com/sortable/#option-cursorAt
    distance: 0,                 // @see http://api.jqueryui.com/sortable/#option-distance, for immediate feedback use "0"
    tolerance: 'pointer',        // @see http://api.jqueryui.com/sortable/#option-tolerance
    axis: 'x',                   // @see http://api.jqueryui.com/sortable/#option-axis, Only vertical moving is allowed. Use 'x' or null. Use this in conjunction with the 'containment' setting
    beforeStart: $.noop,         // returning FALSE will stop the execution chain.
    beforeMoving: $.noop,
    beforeReorganize: $.noop,
    beforeStop: $.noop
  })
  .tablesorter({ selectorSort: '.sorter' ,widgets: ['filter'], 
		widgetOptions: {filter_columnFilters : true}
		});
		});	
		
		
		
		please_wait = document.getElementById('please_wait');
		window.setTimeout(function(){ change_opacity(please_wait, 0.75);} ,100);
		window.setTimeout(function(){ change_opacity(please_wait, 0.5);} ,200);
		window.setTimeout(function(){ change_opacity(please_wait, 0.25);} ,300);
		window.setTimeout(function(){ document.getElementById('please_wait').hidden = true;} ,400);
		
		
		window.setTimeout(function(){ change_opacity(pre_table_container, 0.25);} ,400);
		window.setTimeout(function(){ change_opacity(pre_table_container, 0.5);} ,500);
		window.setTimeout(function(){ change_opacity(pre_table_container, 0.75);} ,600);
		window.setTimeout(function(){ change_opacity(pre_table_container, 1);} ,700);
	}
	else
	{ 
		edit_url = "<?php echo $edit_url; ?>"
		total_row_size = 50;
		document.getElementById('please_wait').hidden = true;
		pre_table_container.style.opacity = 1;
		datagrid.style.height = "500px";
		
		if ('<?php echo $_GET['dev']; ?>' == '1')
		{}
		else
		{
		datagrid.innerHTML = '<center><h3>No Rows Found</h3><button type="button" onclick="window.open(edit_url, \'_blank\')" style="padding: 20px;padding-right: 30px;padding-left: 30px;">Add New</button></center>'
		}
	}
	
	// CFG Defenition for unload js functions
	if (typeof onload_view === "function")
	{
		onload_view();
	}

	
	if ("<?php echo $show_edit_on_view_toggle; ?>" == 1)
	{
		show_edit_on_view_toggle_div.hidden = false;
	}

	fix_view_title();

	if (typeof onload_fuctions !== 'undefined')
	{
    	for (i=0; i < onload_fuctions.length; i++)
    	{
    		onload_fuctions[i]();
    	}
	}
	
	//setTimeout(fix_filter_js,100);
	// fix_filter_js();
}

main_view_fix();

// This is needed and used onle if a field of type datalist_view is defined
window.onload = function()
{
	console.log('window loaded');
	fix_filter_js();
}


	
function fix_view_title()
{
	console.log('fix view title');
	var fix_view_title_cols = <?php echo json_encode($post_view_title_cols_array); ?>;
	if (fix_view_title_cols != undefined) {
	for (i=0; i < fix_view_title_cols.length; i++)
	{
		curr_field_name = fix_view_title_cols[i];
		curr_field_cfg = cfg_fields[curr_field_name];
		curr_field_index = cols_array.indexOf(curr_field_name)
    	if (curr_field_cfg.datalist_view.result == undefined) 
    	{
//    		console.log("no need to fix_view_title post view creation")
    	}
    	else
    	{
			//debugger;
  //  		console.log("fix_view_title")
    		post_view_title_update_json = curr_field_cfg.datalist_view.result;
    		if (curr_field_cfg.link_url == undefined)
    		{
    			link_url = "";
    		}
    		else
    		{
    			link_url = curr_field_cfg.link_url;
    		}
    
    		if (curr_field_cfg.show_id != undefined & curr_field_cfg.show_id)
    		{
    			show_id = true;
    		}
    		else
    		{
    			show_id = false;
    		}
    		
    		for (j=0, size = main_view_table.children[1].children.length; j < size;	j++)
    		{
    			search_id = main_view_table.children[1].children[j].children[curr_field_index].innerText;
    			if (post_view_title_update_json[search_id] != undefined)
    			{
    				title = post_view_title_update_json[search_id];
    				if  (link_url != "")
    				{
    					var pre = "<a href='"+ link_url.replace('{id}',search_id)+"'>";
    					var post = "</a>";
    				}
    				else
    				{
    					var pre = "";					
    					var post = "";
    				}
    
    
    				if (show_id)
    				{
    					main_view_table.children[1].children[j].children[curr_field_index].innerHTML = pre+search_id+post+" :: "+title
    				}
    				else
    				{
    					main_view_table.children[1].children[j].children[curr_field_index].innerHTML = pre+title+post
    				}
    			}
    		}
    	}
	}
	}
}


function change_col(element)
{
	col_fix_please_wait.style.marginLeft = element.closest('th').getBoundingClientRect().x
	col_fix_please_wait.hidden = false;

	setTimeout(function()
	{ 
		if 	(element.checked)
		{
			fix_col(element);
		}
		else
		{
			unfix_col(element);
		}

		col_fix_please_wait.hidden = true;		 
	}, 100);
}


function fix_col(element) 
{
	header_col = element.closest('th')
	cell_index = +header_col.dataset.column;

	
	var sum_offsetWidth = 0;
	var last_offsetLeft = 0;
	
	for (var j=0; j < cell_index; j++)
	{
		if (true)
		{
			if (checkbox_header_row.children[j].querySelector('input[class=header_checkbox]:checked'))
			{
				sum_offsetWidth 	= sum_offsetWidth + rows[0].children[j].offsetWidth;
			}
		}
	}

	header_col.style.left = sum_offsetWidth; 
	header_col.style.zIndex = 170;

	for (var i=0; i < rows.length; i++)
	{
		rows[i].children[cell_index].style.left = sum_offsetWidth;
		rows[i].children[cell_index].style.position = "sticky";
		rows[i].children[cell_index].style.boxShadow = "1px 0px #3f587226";
	}

	// Recuresive for any other selected col - if a more advanced col was selected
	// ---------------------------------------------------------------------------
	for (var j=cell_index+1; j < checkbox_header_row.childElementCount; j++)
	{
		if (t = checkbox_header_row.children[j].querySelector('input[class=header_checkbox]:checked'))
		{
			fix_col(t);
			break;
		}
	} 
}


function unfix_col(element)
{
	header_col = element.closest('th')
	//cell_index = +header_col.getAttribute('data-column');
	cell_index = +header_col.dataset.column;
	

	header_col.style.left = "";
	header_col.style.zIndex = 100;
	
	for (var i=0; i < rows.length; i++)
	{
		rows[i].children[cell_index].style.left = "";
		rows[i].children[cell_index].style.position = "";
		rows[i].children[cell_index].style.boxShadow = "";
	}

	// Recuresive for any other selected col - if a more advanced col was selected
	// ---------------------------------------------------------------------------
	for (var j=cell_index+1; j < checkbox_header_row.childElementCount; j++)
	{
		if (t = checkbox_header_row.children[j].querySelector('input[class=header_checkbox]:checked'))
		{ 
			fix_col(t);
			break;
		}
	} 
}



function change_opacity(element, opacity_value)
{
	element.style.opacity = opacity_value;	
}	



function edit_on_view_cbx_change(is_checked)
{
	var append_action_to_select = true;
	for (var i = 0; i < multi_actions.childElementCount; i++)
	{
		if (multi_actions[i] == "intable_input")
		{
			append_action_to_select = false;
			break;
		}  	
	}

	if (append_action_to_select)
	{
		multi_actions.innerHTML = multi_actions.innerHTML + '<option value="intable_input">Submit Changes</option>';
	}

	// add menu item
	if (is_checked)
	{
		$("td.edit_on_view").click(function()

				{
					if (!this.querySelector('.intable_input'))
					{
						var row_id = this.parentElement.children[1].innerText
						new_field = $(template_edit_fields[cols_array[this.cellIndex]]).clone()[0];
						new_field.name = new_field.name+"_edit_on_v";
						new_field.className = "intable_input"
						new_field.id = "cell_"+row_id+"_"+this.cellIndex;

						if (new_field.type == "select-one")
						{
							for (var i = 0; i < new_field.childElementCount; i++) 
							{ 
								  if (this.innerText == new_field.children[i].text)
								  {
									  new_field.value = new_field.children[i].value;
									  break;
								  }
							}
						}
						else
						{
							new_field.value = this.innerText;
						}
												
						this.innerHTML = "";
						this.append(new_field);
					}
				});
	}
	else
	{
		$("td.edit_on_view").unbind( "click" );
	}
		
}




function previous_page()
{
	var url_string = window.location.href;
	var url = new URL(url_string);
	var offset = url.searchParams.get("offset");
	var limit  = url.searchParams.get("limit");
	if (offset == undefined)
	{
		alert("First Results");
	}
	else
	{
		old_offset = offset;
		offset = Number(offset);
		limit = Number(limit);
		offset = offset - limit;
		if (offset < 0)
		{
			alert("First Results");
		}
		else
		{
			url.href = url.href.replace("offset="+old_offset,"offset="+offset)
			window.location.replace(url.href);
		}
	}
	
}

function next_page()
{
	var url_string = window.location.href;
	var url = new URL(url_string);
	var offset  = url.searchParams.get("offset");
	var limit 	= url.searchParams.get("limit");
	if (offset == undefined)
	{
		url.href = url.href + "&offset=500";
		window.location.replace(url.href);
	}
	else
	{
		old_offset = offset;
		offset = Number(offset);
		limit = Number(limit);
		offset = offset + limit;
		url.href = url.href.replace("offset="+old_offset,"offset="+offset)
		//console.log(url.href);
		window.location.replace(url.href);
	}
}

function select_row(element)
{
	let row = element.closest('tr')
	if (element.checked)
	{
		row.classList.add('selected_row');
	}
	else
	{
		row.classList.remove('selected_row');
	}
}



function fix_filter_js()
{
	console.log('fix_filter_js');
	src_filter_row = document.getElementsByClassName("tablesorter-filter-row")[0];
	filter_cols = Array.from(src_filter_row.children)
	filter_cols_by_dc = filter_cols.reduce((accumulator, value)=> { accumulator[value.firstElementChild.dataset.column] = value.firstElementChild; return accumulator; },{});
	// console.log("filter_cols_by_dc:");
	// console.log(filter_cols_by_dc);
	let has_filters = false;

	for (let i of main_header_row.children[1].children)
	{
		if (i.classList.contains("tablesorter-filter")) 
		{
			has_filters = true;
			break;
		}
	}
 
	if (!has_filters)
	{
		for(current_col_i = 0; current_col_i < main_header_row.childElementCount; current_col_i++) 
		{
			if (current_col_i == 0)
			{
				continue;
			}
			current_col_header = main_header_row.children[current_col_i];
			current_col_data_column = current_col_header.dataset.column;
			

			let new_filter_ph = document.createElement("INPUT");
			new_filter_ph.setAttribute("type", "search");
			new_filter_ph.setAttribute("class", 'tablesorter-filter');
			new_filter_ph.setAttribute("placeholder", 'Search');
			let new_br = document.createElement("br");
			current_col_header.appendChild(new_br);
			current_col_header.appendChild(new_filter_ph);

			new_filter_ph.addEventListener('input', function(e) 
			{
				let curr_filter = this;
				let current_col_data_column = curr_filter.parentElement.dataset.column;
				filter_cols_by_dc[current_col_data_column].value = curr_filter.value;
				filter_cols_by_dc[current_col_data_column].dispatchEvent(new Event('change'));
			})
			
		}
	}
}
	  
</script>


