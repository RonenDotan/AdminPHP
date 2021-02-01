   <head>
      <!--   <script src="/admin/menu/sorttable.js" ></script> -->

   </head>

<script>

function showHideFilters(element)
{
	
	element.nextElementSibling.hidden = !element.nextElementSibling.hidden;

	if (element.nextElementSibling.hidden)
	{
		element.textContent = element.textContent.replace("Hide","Show").replace("Less","More");
	}
	else
	{
		element.textContent = element.textContent.replace("Show","Hide").replace("More","Less");
	}
}

</script>      
<?php
$where = "1=1";

echo "<button id='filter_button1' type='button' onclick='showHideFilters(this)' style='margin-top: 10px;'>Show Filters</button>";
echo '<div class="data-filter" id="filter" hidden><br>';

$iframe = 0;
if (isset($_GET['iframe']) and $_GET['iframe'] == 1)
{
	$iframe = 1;
}

if (!isset($_GET['mod']) OR $_GET['mod'] == 'view')
{
	$mod = 'view';
}
elseif (isset($_GET['mod']) AND $_GET['mod'] == 'edit')
{
	$mod = 'edit';
}


if (!isset($hide_paging) or (isset($hide_paging) and !$hide_paging))
{
$fields["limit"]["name"]          = 'Reslut Per Page';
$fields["limit"]["type"]          = 'text';
$fields["limit"]["input_name"]    = 'limit';
$fields["limit"]["drop_down"] = array('50'=>'50','100'=>'100','200'=>'200','500' => '500', '1000' => '1000', '2000' => '2000', '5000' => '5000', '10000' => '10000', '15000'=> '15000', '20000' => '20000');
$fields["limit"]["filter"]["defualt"] = '500';
$fields["limit"]["show_id"] = false;
$fields["limit"]['new_filter_row'] = true;
$fields["limit"]["div_filter_2"] = true;

$fields["offset"]["name"]          = 'Offset';
$fields["offset"]["type"]          = 'text';
$fields["offset"]["input_name"]    = 'offset';
$fields["offset"]['filter']['text_search'] = 'Exact';

$order_by_array = array('id' => 'ID');
foreach($fields as $field_name => $field)
{
    $order_by_array[$field_name] = $field['name'];
}
$fields["order_by"]["name"]          = 'Order';
$fields["order_by"]["type"]          = 'text';
$fields["order_by"]["input_name"]    = 'order_by';
$fields["order_by"]["drop_down"] = $order_by_array;
$fields["order_by"]["filter"]["defualt"] = 'id';
$fields["order_by"]["show_id"] = false;

$fields["order_type"]["name"]          = 'Order Type';
$fields["order_type"]["type"]          = 'text';
$fields["order_type"]["input_name"]    = 'order_type';
$fields["order_type"]["drop_down"] = array('asc' => 'Ascending', 'desc' => 'Descending');
$fields["order_type"]["filter"]["defualt"] = 'asc';
$fields["order_type"]["show_id"] = false;
}

if (in_array($_SESSION['user_id'],array(1,7,10)))
{
$fields["free_sql"]["name"]          = 'Free SQL';
$fields["free_sql"]["input_name"]    = 'free_sql';
$fields["free_sql"]["type"]          = 'text';
$fields["free_sql"]["filter"]["defualt"] = '';
$fields["free_sql"]['filter']['text_search'] = 'FreeSQL';
$fields["free_sql"]['new_filter_row'] = true;
$fields["free_sql"]["div_filter_2"] = true;
$fields["free_sql"]['hidden'] = true;
}
echo "<input type='text' name='iframe'  value='{$iframe}'   style='display:none;'>";
echo "<input type='text' name='mod'     value='{$mod}'      style='display:none;'>";


//echo "<pre>";
//print_r($fields);
//die();
if (isset($use_client_id) and $use_client_id and $_SESSION['client_id'] != 0)
{
	$where = $where . " AND client_id = {$_SESSION['client_id']}";
}



// Add ID list search list
if (isset($search_list_id) and !$search_list_id) {}
else
{
	$temp["id"]["name"] = "ID";
	$temp["id"]["type"]             = 'text';
	$temp["id"]["input_name"]   = 'id';
	$temp["id"]["db_field"]     = 'id';
	$temp["id"]['filter']['text_search'] = 'List';
	
	$fields = array_merge($temp, $fields);
}

//echo '<div class="row">';
echo '<table><tr>';
$curr_col_index = 0;
$div_filter_2 = false;
foreach($fields as $kk=>$field)
{
    if (!$div_filter_2 and isset($field["div_filter_2"]) and $field["div_filter_2"])
	{
		//echo "</tr><tr><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td><button id='filter_button' type='button' onclick='showHideFilters()'>More Filters</button></td></tr></table>";
		echo '</tr></table><br>';
		echo "<button id='filter_button' type='button' onclick='showHideFilters(this)'>More Filters</button>";
		$div_filter_2 = true;
		echo '<div id="filter2" style="overflow-x: auto; border: 1px solid grey;" hidden="true"><br>';
		echo '<table><tr>';
		$curr_col_index = 0;
	}
	
	//echo '<div id="filter" style="border: 1px solid black";><br>';

	if (isset($field['new_filter_row']) and ['new_filter_row'] or $curr_col_index == 4)
	{
		echo '</tr>';
		echo '<tr>';
		$curr_col_index = 0;
	}

	if (isset($field['filter']))
	{
		$def = isset($field['filter']['defualt']) ? $field['filter']['defualt'] : "";
		$curr_v = isset($_GET[$field['input_name']]) ? $_GET[$field['input_name']] : $def;
		
		$hide_style = "";
		if (isset($field['filter']['hidden']))
		{
			//echo "<input type='hidden' name='{$field['input_name']}' value='{$_GET[$field['input_name']]}' /> ";
			$hide_style = "style='display:none;'";
		}
		
		
		
		if (isset($field['drop_down']))
		{
			if (isset($field['auto_submit']))
			{
				$auto_submit = 'onchange="this.form.submit()"';
			}
			else
			{
				$auto_submit = '';
			}
			
			if (isset($field['filter']['hidden']))
			{
				echo "<td><div class = 'div-filter' align='right'><select {$hide_style} class='form-field-filter' name='{$field['input_name']}' {$auto_submit}>";
			}
			else
			{
				$require = "";
				if (isset($field["filter"]["enforce_selection"]) and $field["filter"]["enforce_selection"])
				{
					$require = "required";
				}
				$curr_col_index = $curr_col_index + 1;
				if (isset($field["filter"]["drop_down_multi"]))
				{
					$curr_v = array();
					if (isset($field['input_name']))
					{
						foreach ($_GET[$field['input_name']] as $selectedOption)
						{
							array_push($curr_v,$selectedOption);
						}
					}
					
					if (sizeof($curr_v) == 0)
					{
						array_push($curr_v,$field['filter']['defualt']);
					}

					echo "<div class = 'div-filter'><td><font>{$field['name']}:</font></td><td><select multiple class='form-field-filter' name='{$field['input_name']}[]' {$require}>";
				}
				else
				{
					echo "<div class = 'div-filter'><td><font>{$field['name']}:</font></td><td><select {$hide_style} class='form-field-filter' name='{$field['input_name']}' {$auto_submit} {$require}>";
				}					
			}
			if ($field['filter']['defualt'] == -1 or strtolower($field['filter']['defualt']) == "all")
			{
				$selected = "";
				if ($curr_v==$k)  
				{ 
					$selected = "selected = 'true'";
				}
				
				if (isset($field["filter"]["enforce_selection"]) and $field["filter"]["enforce_selection"])
				{
					echo "<option value='' selected disabled>Please select an option...</option>";
				}
				else
				{
					echo "<option value='{$field["filter"]["defualt"]}' {$selected}>All</option>";
				}
			}			
			foreach ($field['drop_down'] as $k => $v)
			{
				$selected = "";
				if ($curr_v==$k or (is_array($curr_v) and in_array($k, $curr_v))) 
				{
				    if ($curr_v != '-1' and isset($field['db_field']))
					{
						if 	(isset($field['filter']['search_list']) AND $field['filter']['search_list'] == 1)
						{
							$where = $where . " AND concat(',',{$field['db_field']},',') LIKE '%,{$k},%'";
						}
						else 
						{
							if (isset($field['filter_type']) and $field['filter_type'] == 'number')
							{
								$where = $where . " AND {$field['db_field']} = {$k}";
							}
							else 
							{
								$where = $where . " AND {$field['db_field']} = '{$k}'";	
							}
								
						}
						
					} 
					$selected = "selected = 'true'";
				}
				if (isset($field['show_id']) and !$field['show_id'])
				{
					echo "<option value='{$k}' {$selected}>{$v}</option>";
				}
				else
				{
					echo "<option value='{$k}' {$selected}>{$k} - {$v}</option>";
				}
				
			}
			echo "</select></td></div>";
										
		}
		elseif (isset($field['filter']['text_search']) and !isset($field["datalist"]))
		{
							
			$input_type =  "input";
			$desc_text = "";
			switch ($field['filter']['text_search'])
			{
				case "Exact":
					$desc_text = "<br><font size=1>(Exact Search)</font>";
					break;
					
				case "List":
					$desc_text = "<br><font size=1>(Comma Seperated)</font>";
					$input_type =  "textarea";
					break;
				
				case "Approx":
					$desc_text = "<br><font size=1>(Approx. Search)</font>";
					break;
				
				case "Approximate":
					$desc_text = "<br><font size=1>(Approx. Search)</font>";
					break;
				case "FreeSQL":
					$desc_text = "<br><font size=1>(SQL Filter)</font>";
					break;
				
				case "Approximate List":
					$desc_text = "<br><font size=1>(Approx. List Search)</font>";
					$input_type =  "textarea";	
					break;					

				default:
					$desc_text = "<br><font size=1>({$field['filter']['text_search']})</font>";
					break;						
			}
			// print_r($field);
			// die();
			
			if ($kk == 'free_sql')
			{
				echo "<div class = 'div-filter'><td><font>{$field['name']}:{$desc_text}</font></td><td colspan='7'><textarea type='text' value='' name='{$field['input_name']}' class='form-field-filter' style='width: -webkit-fill-available;'>{$curr_v}</textarea></td></div>";
				if ($curr_v != '' ) 
				{
					$where = $where . " AND {$curr_v}";
				}
				continue;
			}
			
			
			$curr_col_index = $curr_col_index + 1;
			$def_value = isset($_GET[$field['input_name']]) ? $_GET[$field['input_name']] : "";
			
			
			// echo "<div class = 'div-filter'><td><font>{$field['name']}:{$desc_text}</font></td><td><input type='text' name='{$field['input_name']}' value='{$def_value}' class='form-field-filter'></td></div>";
			if (isset($curr_v) and $curr_v != '' and isset($field['db_field']))
			{
				$curr_v = strtolower($curr_v);
				if ($field['filter']['text_search'] == "Exact")
				{
					$where = $where . " AND lower({$field['db_field']}) = '{$curr_v}'";
				}
				elseif ($field['filter']['text_search'] == "List")
				{
					//$def_value = str_replace(",,",",",str_replace("\r",",",str_replace("\n",",",$def_value)));
					$curr_v = trim(preg_replace('/\s+/', ',', $curr_v));
					$curr_v = "'".str_replace(",","','",$curr_v)."'";
					$where = $where . " AND lower({$field['db_field']}) IN ({$curr_v})";
				}
				elseif ($field['filter']['text_search'] == "Approx" or $field['filter']['text_search'] == "Approximate")
				{
					$where = $where . " AND lower({$field['db_field']}) LIKE '%{$curr_v}%'";
				}
				elseif ($field['filter']['text_search'] == "Approximate List")
				{
					$curr_v = trim(preg_replace('/\s+/', '|', $curr_v));
					$curr_v = str_replace(",","|",$curr_v);
					$where = $where . " AND lower({$field['db_field']}) REGEXP '{$curr_v}'";
				}
				elseif ($field['filter']['text_search'] == "Numerical")
				{
					
					if (isset($field['filter']['position']) and $field['filter']['position'] == 1)
					{
						$equation = "<=";
					}
					else
					{
						$equation = ">=";
					}
					
					if (isset($field['db_field']))
					{
						$col_for_equation = $field['db_field'];
					}
					elseif (isset($field['filter']['custom_db_col']))
					{
						$col_for_equation = $field['filter']['custom_db_col'];
					}
					else
					{
						continue; // can't filter
					}
					
					$where = $where . " AND {$col_for_equation} {$equation} {$curr_v}";
				}
				else
				{
					// Special Case
				}
			}
			
			if ($input_type == 'textarea')
			{
				echo "<div class = 'div-filter'><td><font>{$field['name']}:{$desc_text}</font></td><td><textarea type='text' name='{$field['input_name']}' value='{$def_value}' class='form-field-filter'>{$def_value}</textarea></td></div>";
			}
			else
			{
				echo "<div class = 'div-filter'><td><font>{$field['name']}:{$desc_text}</font></td><td><input type='text' name='{$field['input_name']}' value='{$def_value}' class='form-field-filter'></td></div>";
			}
			// echo "<script>console.log('{$def_value}')</script>";
		}
		elseif (isset($field["datalist"]) and $field["datalist"])
		{
			$curr_col_index = $curr_col_index + 1;
			$query_for_label_base = $field["datalist"];
			$input_name = $field['input_name'];
			
			if ($curr_v)
			{
				$sql = explode("where", $field["datalist"])[0]. "where id = {$curr_v}" ;
				$res = $conn->query($sql);
				$row = $res->fetch_assoc();
				$label = $row['title'];
			}
			else
			{
				$label = "";
			}
			echo "<div class = 'div-filter'><td><font>{$field['name']}:</font><br><font size=1>(Auto Complete)</font></td><td><div style='position: relative;'><input  list='{$input_name}_datalist' class='form-field-filter' id='{$input_name}_display_only' value='{$label}' {$attributes} onkeyup='datalist_keyup(this, \"{$field['datalist']}\")' onchange='datalist_change(this)' onblur='datalist_blur(this)'><div style='z-index: 10; position: absolute;top: 0; left: 0;' id='{$input_name}_please_wait' hidden><img class='please_wait' style='width: 100%; opacity :0.70' ></div></div></td></div>";
			echo "<datalist id='{$input_name}_datalist'></datalist>";
			echo "<input type='hidden' name='{$input_name}' id='{$input_name}' value='{$curr_v}'>";
			if (ltrim(rtrim($curr_v)) != '')
			{
				$where = $where . " AND {$field['db_field']} = {$curr_v} ";
			}
		}
		elseif (isset($field['filter']['number']))
		{
			// print_r($field);
			// die();
			$curr_col_index = $curr_col_index + 1;
			echo "<div class = 'div-filter'><td><font>{$field['name']}:</font></td><td><input type='text' name='{$field['input_name']}' value='{$_GET[$field['input_name']]}' class='form-field-filter'></td></div>";
			if (isset($curr_v) and $curr_v != '')
			{
				$where = $where . " AND {$field['db_field']} = {$curr_v} ";
			}
		}
		/* new part */
		elseif (isset($field['filter']['date']) or isset($field['filter']['date-time']))
		{
			$date_start_this_month 	= date("Y-m-d", strtotime("first day of this month"));
			$date_start_last_month 	= date("Y-m-d", strtotime("first day of previous month"));
			$date_end_last_month 	= date("Y-m-d", strtotime("last day of previous month"));			
			$date_30_days_ego = date('Y-m-d', mktime(0, 0, 0, date("m") , date("d") - 30, date("Y")));
			$date_7_days_ego = date('Y-m-d', mktime(0, 0, 0, date("m") , date("d") - 7, date("Y")));
			$date_3_days_ego = date('Y-m-d', mktime(0, 0, 0, date("m") , date("d") - 3, date("Y")));
			$date_1_day_ego = date('Y-m-d', mktime(0, 0, 0, date("m") , date("d") - 1, date("Y")));
			$date_today = date("Y-m-d");
			$date_10_years_ego = date('Y-m-d', mktime(0, 0, 0, date("m") , date("d") - 3650, date("Y")));
			
			if (isset($field['filter']['defualt']))
			{
				$defualt_timerange = $field['filter']['defualt'];
				switch ($defualt_timerange)
				{
					case 0:
						$default_start_range = $date_today;
						$default_end_range 	 = $date_today;
						break;
					case 1:
						$default_start_range = $date_1_day_ego;
						$default_end_range 	 = $date_1_day_ego;
						break;
					case 2:
						$default_start_range = $date_3_days_ego;
						$default_end_range 	 = $date_today;
						break;
					case 3:
						$default_start_range = $date_7_days_ego;
						$default_end_range 	 = $date_today;
						break;
					case 4: // last 30 days
						$default_start_range = $date_30_days_ego;
						$default_end_range 	 = $date_today;
						break;						
					case 5: // this month
						$default_start_range = $date_start_this_month;
						$default_end_range 	 = $date_today;
						break;	
					case 6: // last month
						$default_start_range = $date_start_last_month;
						$default_end_range 	 = $date_end_last_month;
						break;
					case 7: // Always
					    $default_start_range = $date_10_years_ego;
					    $default_end_range 	 = $date_today;
					    break;
					default:
						$defualt_timerange = 2;
						$default_start_range = $date_3_days_ego;
						$default_end_range 	 = $date_today;
				}
			
				
			}
			else
			{
				$defualt_timerange = 2;
				$default_start_range = $date_3_days_ego;
				$default_end_range 	 = $date_today;
			}
			
			$SelectDateChange_val = isset($_GET["{$field['input_name']}_select"]) ? $_GET["{$field['input_name']}_select"] : "{$defualt_timerange}"; 
			$date_range_options = array(
							0=>'Today',
							1=>'Yesterday',
							2=>'Last 3 Days',
							3=>'Last 7 Days',
							4=>'Last 30 Days',
							5=>'This Month',
							6=>'Last Month',
			                7=>'Always'
							);
							
							

			$date_range_options_str = "";
			foreach ($date_range_options as $id=>$label)
			{
				if ($id == $SelectDateChange_val)
				{
					$date_range_options_str = "{$date_range_options_str}<option value='{$id}' selected>{$label}</option>";
				}
				else
				{
					$date_range_options_str = "{$date_range_options_str}<option value='{$id}'>{$label}</option>";
				}
			}

			
			$curr_col_index = $curr_col_index + 2;
			$start_input_name = $field['input_name'] ."_start";
			$end_input_name = $field['input_name'] ."_end";
			$curr_v_start = isset($_GET[$start_input_name]) ? $_GET[$start_input_name] : $default_start_range;
			$curr_v_end = isset($_GET[$end_input_name]) ? $_GET[$end_input_name] : $default_end_range;
			echo "<div class = 'div-filter'><td style='border: 1px dashed;border-bottom-color: #3f5872; border-top-color: #3f5872; border-left-color: #3f5872; border-right-color: white;'><font>{$field['name']}</font></td><td style='border: 1px dashed;border-bottom-color: #3f5872; border-top-color: #3f5872; border-left-color: white; border-right-color: white;'><select class='form-field-filter' name='{$field[input_name]}_select' onchange='SelectDateChange(this, \"{$start_input_name}\",\"{$end_input_name}\")'>{$date_range_options_str}</select></td><td style='border: 1px dashed;border-bottom-color: #3f5872; border-top-color: #3f5872; border-left-color: white; border-right-color: white;'><font size=2>From</font><input type='date' name='{$start_input_name}' value='{$curr_v_start}' class='form-field-filter'></td><td style='border: 1px dashed;border-bottom-color: #3f5872; border-top-color: #3f5872; border-left-color: white; border-right-color: #3f5872;'><font size=2>to</font><input type='date' name='{$end_input_name}' value='{$curr_v_end}' class='form-field-filter'></td></div>";
			if (isset($curr_v_start) and $curr_v_start != '' and isset($curr_v_end) and $curr_v_end != '')
			{
			    if ($curr_v_start == $date_10_years_ego and $curr_v_end == $date_today)
			    {
			    }
			    else 
			    {
			        if (isset($field['filter']['date-time']))
			        {
			            $curr_v_end = $curr_v_end . " 23:59:59";
			        }
			        $where = $where . " AND {$field['db_field']} BETWEEN '{$curr_v_start}' AND '{$curr_v_end}' ";
			    }
			}
		} 
		elseif (false and isset($field['filter']['date-time'])) // old absolete
		{
			$curr_col_index = $curr_col_index + 2;
			$start_input_name = $field['input_name'] ."_start";
			$end_input_name = $field['input_name'] ."_end";
			$curr_v_start = isset($_GET[$start_input_name]) ? $_GET[$start_input_name] : date('Y-m-d', mktime(0, 0, 0, date("m") , date("d") - 7, date("Y")));
			$curr_v_end = isset($_GET[$end_input_name]) ? $_GET[$end_input_name] : date("Y-m-d");
			echo "<div class = 'div-filter'><td><font>{$field['name']}-From:</font></td><td><input type='date' name='{$start_input_name}' value='{$curr_v_start}' class='form-field-filter'></td><td><font>To:</font></td><td><input type='date' name='{$end_input_name}' value='{$curr_v_end}' class='form-field-filter'></td></div>";
			if (isset($curr_v_start) and $curr_v_start != '' and isset($curr_v_end) and $curr_v_end != '')
			{
			    if ($curr_v_start == $date_10_years_ego and $curr_v_end == $date_today)
			    {
			    }
			    else
			    {
			        $curr_v_end = $curr_v_end . " 23:59:59";
			        $where = $where . " AND {$field['db_field']} BETWEEN '{$curr_v_start}' AND '{$curr_v_end}' ";
			    }
			}
		}
		elseif (isset($field['hidden']))
		{
			echo "<input type='hidden' name='{$field['input_name']}' value='{$_GET[$field['input_name']]}' /> ";
		}
		elseif (isset($field['selectbox']))
		{
			$curr_values = array();
			foreach ($_GET[$field['input_name']] as $selectedOption)
			{
				if ($selectedOption != -1)
				{
					array_push($curr_values,$selectedOption);
				}
			}
			echo "<div class = 'div-filter'><td><font>{$field['name']}:</font></td><td style='border: 1px solid border-radius: 10px;'><div class='form-field-filter' style='overflow: auto; height:200px;'>";
			

			$checked = "";
			if (in_array(-1,$curr_values))
			{
				$checked = "checked";
			}
			echo "<div style='padding-bottom: 4px;'>";
			echo "<input type='checkbox' id='{$field['input_name']}[]_-1' name='{$field['input_name']}[]' value='-1' {$checked} onchange='filter_select_deselect_all(this)'>";
			echo  "<label for='{$field['input_name']}[]_-1' style='font-size:13.333px'>ALL</label>";
			echo "</div>";
			foreach ($field['selectbox'] as $k=>$v)
			{
				$checked = "";
				if (in_array($k,$curr_values))
				{
					$checked = "checked";
				}
				echo "<div style='padding-bottom: 4px;'>";
				echo "<input type='checkbox' id='{$field['input_name']}[]_{$k}' name='{$field['input_name']}[]' value='{$k}' {$checked} onchange='filter_turn_off_all(this)'>";
				echo  "<label for='{$field['input_name']}[]_{$k}' style='font-size:13.333px'>{$v}</label>";
				echo "</div>";
			}
			echo "</div></td></div>";
			
			if (isset($field['db_field']) and sizeof($curr_values) > 0)
			{
				if ($field['type'] == 'number')
				{
					$where = $where . " AND {$field['db_field']} in (".implode(",",$curr_values).")";
				}
				elseif ($field['type'] == 'text')
				{
					$where = $where . " AND {$field['db_field']} in ('".implode("','",$curr_values)."')";
				}
			}
		}
	}
}

// Extra Patch
if (in_array($_SESSION['user_id'],array(1,7,10)))
{}
else
{
    if (isset($src_table) and strlen($src_table) > 3)
    {
        if ($src_table == "tags")
        {
            $where = $where . " AND id not in (138,150,158,161)";
        }
        
        if ($src_table == "slr_groups")
        {
            $where = $where . " AND id not in (911,912,913,914,915,916,917,918,919,920,
                                                                 921,922,923,924,925,926,927,928,929,930,
                                                                 931,932,933,934,935,937,
                                                                 1027,1041,1042,1048,1049,1050,1053,1054)";
        }
        
        if ($src_table == "publishers")
        {
            $where = $where . " AND id not in (4417,4428,4435)";
        }
        
        if ($src_table == "smart_links")
        {
            $where = $where . " AND id not in (84,81)";
        }
        
    } 
    else {die('Error, SRC table not spec');}
    

    foreach ($fields as $field_key=>$field)
    {
        // tags
        if (in_array($field['db_field'],array("tag_id","network_tag")))
        {
            $where = $where . " AND {$field['db_field']} not in (138,150,158,161)";
        }
          
        
        // groups
        if (in_array($field['db_field'],array("group_id","click_balancing")))
        {
            $where = $where . " AND {$field['db_field']} not in (911,912,913,914,915,916,917,918,919,920,
                                                                 921,922,923,924,925,926,927,928,929,930,
                                                                 931,932,933,934,935,937,
                                                                 1027,1041,1042,1048,1049,1050,1053,1054)";
        }

        
         
        // publishers
        if (in_array($field['db_field'],array("publisher_id", "default_publisher")))
        {
            $where = $where . " AND {$field['db_field']} not in (4417,4428,4435)";
        }
        
        
        
        // Smart links
        if (in_array($field['db_field'],array("smart_link", "smart_link_id")))
        {
            $where = $where . " AND {$field['db_field']} not in (84,81)";
        }

        
        // optimization rules - by action type id
        if (in_array($field['db_field'],array("action_type_id")))
        {
            $where = $where . " AND {$field['db_field']} not in (3,13,14)";
        }
        
    }
}

echo '</tr></table>';
if ($div_filter_2)
{
	echo '</div>';
}



if (isset($_GET['dev']))
{
	$dev_num += 1;
	echo "<pre class='dev{$dev_num}'>";
	print_r($_GET);
	echo $where;
	echo "</pre></div>";
}



// Remove The ID from list of fields:
if (isset($search_list_id) and !$search_list_id) {}
else
{
	unset($fields['id']);
}



echo "<br><br><br>";
echo "<center><input class='submit-button' type='submit' class='submit' value='Filter' />";
if (isset($show_export_excel_only) and !$show_export_excel_only) {} 
else
{ 
	echo "&nbsp&nbsp&nbsp&nbsp<input class='submit-button' type='submit' class='submit' value='Export To CSV' name='export_excel_only' />"; 
}
echo "</center><br><br></div><br>";
?>

<script>
function filter_select_deselect_all(element)
{
	var is_selected = element.checked;
	var checkboxes = document.getElementsByName(element.name);

	for(var i = 0; i< checkboxes.length; i++)
	{
		checkboxes[i].checked = is_selected;
	}
}

function SelectDateChange(element, start_input_name, end_input_name)
{
	start_input = document.getElementsByName(start_input_name)[0];
	end_input 	= document.getElementsByName(end_input_name)[0]

	switch(element.value)
	{
		case "0": 	// Today
			start_input.value 	= 	'<?php echo $date_today; ?>';
			end_input.value 	=	'<?php echo $date_today; ?>';
			break;
		case "1": 	// Yesterday
			start_input.value 	= 	'<?php echo $date_1_day_ego; ?>';
			end_input.value 	=	'<?php echo $date_1_day_ego; ?>';
			break;			
		case "2":	// Last 3 Days
			start_input.value 	= 	'<?php echo $date_3_days_ego; ?>';
			end_input.value 	=	'<?php echo $date_today; ?>';
			break;
		case "3": // Last 7 Days
			start_input.value 	= 	'<?php echo $date_7_days_ego; ?>';
			end_input.value 	=	'<?php echo $date_today; ?>';
			break;
		case "4": // Last 30 days
			start_input.value 	= 	'<?php echo $date_30_days_ego; ?>';
			end_input.value 	=	'<?php echo $date_today; ?>';
			break;
		case "5": // This Month
			start_input.value 	= 	'<?php echo $date_start_this_month; ?>';
			end_input.value 	=	'<?php echo $date_today; ?>';
			break;
		case "6": // Last Month
			start_input.value 	= 	'<?php echo $date_start_last_month; ?>';
			end_input.value 	=	'<?php echo $date_end_last_month ; ?>';
			break;		
		case "7": // Last Month
			start_input.value 	= 	'<?php echo $date_10_years_ego; ?>';
			end_input.value 	=	'<?php echo $date_today ; ?>';
			break;			
		default:
			alert("ERROR IN FILTER IN SelectDateChange FUNCTION");
	}
}

function filter_turn_off_all(element)
{
	var all_checkbox = document.getElementById(element.name+"_-1");
	all_checkbox.checked = false;
}





function datalist_keyup(element, query)
{
	console.log("keyup");
	if (false) 
	{ 
        // Revert Changes
		
    }
	else
	{
		dl = document.getElementById(element.id.replace("_display_only","_datalist"));
		please_wait = document.getElementById(element.id.replace("_display_only","_please_wait"));
		dl.innerHTML = "";	
		if (element.value.length < 3)
		{
		}
		else
		{
			query = query.replace('{user_input}', '\"percentage'+element.value+'percentage\"')
			
			if (Number(element.value) > 0) 
			{
				// query = query + ' OR id LIKE "'+element.value+'%"'
			}
			 console.log(query)
			 please_wait.hidden = false;
			$.post( "/admin/jquery/jq_post.php","query="+query)
				.done(function( data ) 
					{
						var data = JSON.parse(data);
												
						if (data == "Not Found")	
						{
							console.log("Not Found");
						}
						else
						{
							for (var i in data)
							{
								dl.innerHTML += '<option data-value="'+i+'" value="'+data[i]+'">'+i+'</option>';
							}
						}
						please_wait.hidden = true;
					});
		}
	}
	
}

function datalist_change(element)
{
	console.log("change");
	if (false)
	{
		dl = document.getElementById(element.id.replace("_display_only","_datalist"));
		hidden_field = document.getElementById(element.id.replace("_display_only",""));
		var list_length = dl.children.length
		var selected_value = hidden_field.value;
		var found = false;
		for (var i = 0; i < list_length; i++) 
		{
			if (dl.children[i].value == element.value)
			{
				selected_value = dl.children[i].getAttribute("data-value");
				found = true;
			}
		}
		
		if (found)
		{
			hidden_field.value = selected_value;
		}
		else
		{
			hidden_field.value = "";
		}
	}
}


function datalist_blur(element)
{
	console.log("blur");
	if (true)
	{
		dl = document.getElementById(element.id.replace("_display_only","_datalist"));
		hidden_field = document.getElementById(element.id.replace("_display_only",""));
		var list_length = dl.children.length
		var selected_value = hidden_field.value;
		var found = false;
		for (var i = 0; i < list_length; i++) 
		{
			if (dl.children[i].value == element.value)
			{
				selected_value = dl.children[i].getAttribute("data-value");
				found = true;
			}
		}
		
		if (found)
		{
			hidden_field.value = selected_value;
		}
		else
		{
			hidden_field.value = "";
		}
	}
}
</script>