   <head>
</style>
   </head>

<script>


function showHideFilters(element)
{
	console.log(element)
	
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
echo '<div class="data-filter" id="filter"><br>';


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

echo "<input type='text' name='iframe' value='{$iframe}' style='display:none;'>";
echo "<input type='text' name='mod' value='{$mod}' style='display:none;'>";

//echo "<pre>";
//print_r($fields);
//die();




// Add ID list search list
if (isset($search_list_id) and $search_list_id)
{
	$temp["id"]["name"] = "ID";
	$temp["id"]["type"]             = 'text';
	$temp["id"]["input_name"]   = 'id';
	$temp["id"]["db_field"]     = 'id';
	$temp["id"]['filter']['text_search'] = 'List';
	
	$fields = array_merge($temp, $fields);
}

//echo '<div class="row">';
echo '<table width="100%"><tr>';
$curr_col_index = 0;
$div_filter_2 = false;

$having = "";
$convs_having = "";

if (isset($_GET['conv_date_ind']) and $_GET['conv_date_ind'] == 1)
{
    $conv_table_measures = array('convs','income');
}
else 
{
    $conv_table_measures = array();
}

 
foreach ($fields as $field_name=>$field)
{
	$def = isset($field['filter']['defualt']) ? $field['filter']['defualt'] : "";
	$curr_v = isset($_GET[$field['input_name']]) ? $_GET[$field['input_name']] : $def;
	switch ($field_name)
	{
		case "filter_measure":
			if ($curr_v != 'none')
			{
			    $filter_measure = $curr_v;
			    if(in_array($filter_measure,$conv_table_measures)) {$convs_having = "HAVING {$curr_v} ";} else {$having = "HAVING {$curr_v} ";}
				break;
			}
			else
			{
				break 2;
			}
		case "filter_type":
			if ($curr_v != 'none')
			{
				$sign = ($curr_v == 1) ?  ">" : "<";
				if(in_array($filter_measure,$conv_table_measures)) {$convs_having = "{$convs_having} {$sign}";} else {$having = "{$having} {$sign}";}
				break;
			}
			else
			{
				break 2;
			}
		case "filter_value":
		{
			if ($curr_v != 'none')
			{
			    if(in_array($filter_measure,$conv_table_measures)) {$convs_having = "{$convs_having} {$curr_v}";} else {$having = "{$having} {$curr_v}";}
				break;
			}
			else
			{
				break 2;
			}	
		}
		default:
	}
}


// Discrepency check - please replace to ind1 usage.
$discrepency_data_ind = isset($_GET['discrepency_data_ind']) ? $_GET['discrepency_data_ind'] : 0;
switch ($discrepency_data_ind)
{
    case 0:
        $where = $where . " AND ifnull(tag_id,0) != 1 AND ifnull(product_id,0) != 1 ";
        // $where = $where . " AND ind1 = 0 ";
        break;
    case 1:
        // Include all
        break;
    case 2:
        $where = $where . " AND (tag_id = 1 OR product_id = 1) ";
        // $where = $where . " AND ind1 = 1 ";
        break;
}

//echo $having;


foreach($fields as $field)
{
	if (isset($field["div_filter_2"]) and $field["div_filter_2"])
	{
	   // echo "<br>Field: {$field['input_name']}<br>";
		//echo "</tr><tr><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td><button id='filter_button' type='button' onclick='showHideFilters()'>More Filters</button></td></tr></table>";
		echo '</tr></table><br>';
		echo "<button id='filter_button' type='button' onclick='showHideFilters(this)'>More Filters</button>";
		$div_filter_2 = true;
		echo '<div id="filter2" style="border: 1px dotted #3f5872c4"; hidden="true"><br>';
		echo '<table><tr>';
		$curr_col_index = 0;
	}
/*	if ($field["input_name"] == 'group_by')
	{
		$curr_col_index = 0;
	}
	*/
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
		
		/*
		if (isset())
		<input type="checkbox" value="" name="id_checkbox">
		*/
		if (isset($field['checkbox']))
		{
			
			
			$value_if_not_checked = $fields["conv_date_ind"]["checkbox"][0];
			$value_if_checked = $fields["conv_date_ind"]["checkbox"][1];
			if (isset($_GET[$field['input_name']]) and $_GET[$field['input_name']] == $value_if_checked)
			{
				$is_checked = 'checked';
			}
			elseif(!isset($_GET['imp_date_start']) and isset($field["filter"]["defualt"]) and $field["filter"]["defualt"] == $value_if_checked)
			{
				$is_checked = 'checked';
			}
			else
			{
				$_GET[$field['input_name']] = $value_if_not_checked;
				$is_checked = '';
			}
			
			echo "<script>console.log('checkbox value: {$_GET[$field['input_name']]}')</script>";
			echo "<td><font>{$field['name']}:</font></td><td><input type='checkbox' style='width: fit-content;' class='form-field-filter' name='{$field['input_name']}' value='{$value_if_checked}' {$is_checked}>";
		}	
		elseif (isset($field['drop_down']))
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
				echo "<td><select {$hide_style} class='form-field-filter' name='{$field['input_name']}' {$auto_submit}>";
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

					echo "<td><font>{$field['name']}:</font></td><td><select multiple class='form-field-filter' name='{$field['input_name']}[]' {$require}>";
				}
				else
				{
					echo "<td><font>{$field['name']}:</font></td><td><select {$hide_style} class='form-field-filter' name='{$field['input_name']}' {$auto_submit} {$require}>";
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
				    if (isset($field['db_field']) and $field['db_field'] == 'imp_hour')
				    {
				        $hour = $k;
				    }
				    else if (isset($field['db_field']))
					{
						if 	(isset($field['filter']['search_list']) AND $field['filter']['search_list'] == 1)
						{
							$where = $where . " AND concat(',',{$field['db_field']},',') LIKE '%,{$k},%'";
						}
						else 
						{
							if (isset($field['filter_type']) and $field['filter_type'] == 'number')
							{
								$where = $where . " AND ifnull({$field['db_field']},0) = {$k}";
							}
							else 
							{
								$where = $where . " AND ifnull({$field['db_field']},'0') = '{$k}'";	
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
			echo "</select></td>";
										
		}
		elseif (isset($field['filter']['text_search']) and !isset($field["datalist"]))
		{
							
							
			$desc_text = "";
			switch ($field['filter']['text_search'])
			{
				case "Exact":
					$desc_text = "<br><font size=1>(Exact Search)</font>";
					break;
					
				case "List":
					$desc_text = "<br><font size=1>(Comma Seperated)</font>";
					break;
					
				default:
					$desc_text = "<br><font size=1>(Approx. Search)</font>";
					break;						
			}
			// print_r($field);
			// die();
			$curr_col_index = $curr_col_index + 1;
			$def_value = isset($_GET[$field['input_name']]) ? $_GET[$field['input_name']] : "";
			echo "<td><font>{$field['name']}:{$desc_text}</font></td><td><input type='text' name='{$field['input_name']}' value='{$def_value}' class='form-field-filter'></td>";
			if (isset($curr_v) and $curr_v != '' and isset($field['db_field']))
			{
				if ($field['filter']['text_search'] == "Exact")
				{
					$where = $where . " AND {$field['db_field']} = '{$curr_v}'";
				}
				elseif ($field['filter']['text_search'] == "List")
				{
					$where = $where . " AND {$field['db_field']} IN ({$curr_v})";
				}
				else
				{
					$where = $where . " AND {$field['db_field']} LIKE '%{$curr_v}%'";
				}
			}
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
			//$k = $$k_name;
			//$label = $$label_name;
			if (!isset($attributes)) { $attributes = "";}
			echo "<td><font>{$field['name']}:</font><br><font size=1>(Auto Complete)</font></td><td><div style='position: relative;'><input  list='{$input_name}_datalist' class='form-field-filter' id='{$input_name}_display_only' value='{$label}' {$attributes} onkeyup='datalist_keyup(this, \"{$field['datalist']}\")' onchange='datalist_change(this)' onblur='datalist_blur(this)'><div style='z-index: 10; position: absolute;top: 0; left: 0;' id='{$input_name}_please_wait' hidden><img class='please_wait' style='width: 100%; opacity :0.70' ></div></div></td>";
			echo "<datalist id='{$input_name}_datalist'></datalist>";
			echo "<input type='hidden' name='{$input_name}' id='{$input_name}' value='{$curr_v}'>";
			if (ltrim(rtrim($curr_v)) != '')
			{
				$where = $where . " AND {$field['db_field']} = {$curr_v} ";
			}
		}
		elseif ($field["db_field"] == 'imp_xxhour')
		{
		    // print_r($field);
		    $curr_col_index = $curr_col_index + 1;
		    echo "<td><font>{$field['name']}:</font></td><td><input type='text' name='{$field['input_name']}' value='{$_GET[$field['input_name']]}' class='form-field-filter'></td>";
		    if (isset($curr_v) and $curr_v != '' and isset($field['db_field']))
		    {
		        $hour = $curr_v;
		    }
		}
		elseif (isset($field['filter']['number']))
		{
			// print_r($field);
			// die();
			$curr_col_index = $curr_col_index + 1;
			echo "<td><font>{$field['name']}:</font></td><td><input type='text' name='{$field['input_name']}' value='{$_GET[$field['input_name']]}' class='form-field-filter'></td>";
			if (isset($curr_v) and $curr_v != '' and isset($field['db_field']))
			{
				$where = $where . " AND {$field['db_field']} = {$curr_v} ";
			}
		}
		/* new part */
		elseif (isset($field['filter']['date']))
		{
			$date_start_this_month 	= date("Y-m-d", strtotime("first day of this month"));
			$date_start_last_month 	= date("Y-m-d", strtotime("first day of previous month"));
			$date_end_last_month 	= date("Y-m-d", strtotime("last day of previous month"));			
			$date_30_days_ego = date('Y-m-d', mktime(0, 0, 0, date("m") , date("d") - 30, date("Y")));
			$date_7_days_ego = date('Y-m-d', mktime(0, 0, 0, date("m") , date("d") - 7, date("Y")));
			$date_3_days_ego = date('Y-m-d', mktime(0, 0, 0, date("m") , date("d") - 3, date("Y")));
			$date_1_day_ego = date('Y-m-d', mktime(0, 0, 0, date("m") , date("d") - 1, date("Y")));
			$date_today = date("Y-m-d");
			
			
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
							6=>'Last Month'							
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

			
			$curr_col_index = $curr_col_index + 3;
			$start_input_name = $field['input_name'] ."_start";
			$end_input_name = $field['input_name'] ."_end";
			$curr_v_start = isset($_GET[$start_input_name]) ? $_GET[$start_input_name] : $default_start_range;
			$curr_v_end = isset($_GET[$end_input_name]) ? $_GET[$end_input_name] : $default_end_range;
			$tz = isset($_GET['tz']) ? $_GET['tz'] : '0';
			$tz_array = array(  
			    '-12'=>'(GMT-12:00) International Date Line West',
			    '-11'=>'(GMT-11:00) Midway Island, Samoa',
			    '-10'=>'(GMT-10:00) Hawaii',
			    '-9'=>'(GMT-09:00) Alaska',
			    '-8'=>'(GMT-08:00) Pacific Time (US & Canada)',
			    '-7'=>'(GMT-07:00) Mountain Time (US & Canada)',
			    '-6'=>'(GMT-06:00) Central Time (US & Canada)',
			    '-5'=>'(GMT-05:00) Eastern Time (US & Canada)',
			    '-4'=>'(GMT-04:00) Atlantic Time (Canada)',
			    '-3'=>'(GMT-03:00) Brasilia, Buenos Aires, Georgetown',
			    '-2'=>'(GMT-02:00) Mid-Atlantic',
			    '-1'=>'(GMT-01:00) Cape Verde Is.',
			    '0' =>'(GMT+00:00) Greenwich Mean Time : Dublin, Edinburgh, Lisbon, London',
			    '1' =>'(GMT+01:00) Berlin, Rome, Brussels, Madrid, Paris, Budapest',
			    '1' =>'(GMT+01:00) Belgrade, Bratislava, Budapest, Ljubljana, Prague, Warsaw',
			    '2' =>'(GMT+02:00) Jerusalem, Athens, Kyiv, Istanbul, Sofia, Cairo ',
			    '3' =>'(GMT+03:00) Moscow, St. Petersburg, Volgograd',
			    '4' =>'(GMT+04:00) Abu Dhabi, Muscat',
			    '5' =>'(GMT+05:00) Islamabad, Karachi, Tashkent',
			    '6' =>'(GMT+06:00) Almaty, Novosibirsk, Astana, Dhaka',
			    '7' =>'(GMT+07:00) Bangkok, Hanoi, Jakarta',
			    '8' =>'(GMT+08:00) Beijing, Singapore, Perth, Hong Kong',
			    '9' =>'(GMT+09:00) Tokyo, Seoul',
			    '10'=>'(GMT+10:00) Brisbane, Canberra, Melbourne, Sydney',
			    '11'=>'(GMT+11:00) Magadan, Solomon Is., New Caledonia',
			    '12'=>'(GMT+12:00) Auckland, Wellington, Fiji, Kamchatka, Marshall Is.'
			);
			
			echo "<td style='border: 1px dashed;border-bottom-color: #3f5872c4; border-top-color: #3f5872c4; border-left-color: #3f5872c4; border-right-color: white;'><font>{$field['name']}</font></td><td style='border: 1px dashed;border-bottom-color: #3f5872c4; border-top-color: #3f5872c4; border-left-color: white; border-right-color: white;'><select class='form-field-filter' name=\'{$field['input_name']}_select\' onchange='SelectDateChange(this, \"{$start_input_name}\",\"{$end_input_name}\")'>{$date_range_options_str}</select></td><td style='border: 1px dashed;border-bottom-color: #3f5872c4; border-top-color: #3f5872c4; border-left-color: white; border-right-color: white;'><font size=2>From</font><input type='date' name='{$start_input_name}' value='{$curr_v_start}' class='form-field-filter'></td><td style='border: 1px dashed;border-bottom-color: #3f5872c4; border-top-color: #3f5872c4; border-left-color: white; border-right-color: white;'><font size=2>to</font><input type='date' name='{$end_input_name}' value='{$curr_v_end}' class='form-field-filter'></td>";
			echo "<td style='border: 1px dashed;border-bottom-color: #3f5872c4; border-top-color: #3f5872c4; border-left-color: white; border-right-color: white;'><font>TimeZone</font></td><td style='border: 1px dashed;border-bottom-color: #3f5872c4; border-top-color: #3f5872c4; border-left-color: white; border-right-color: #3f5872c4;'><select class='form-field-filter' name='tz'>";
			foreach ($tz_array as $id=>$text)
			{
			    if ($id == $tz)
			    {
			        echo "<option value= {$id} selected>{$text}</option>";
			    }
			    else 
			    {
			         echo "<option value= {$id}>{$text}</option>";
			    }
			}
			echo "</select></td>";
			
			
			/*
			if (isset($curr_v_start) and $curr_v_start != '' and isset($curr_v_end) and $curr_v_end != '')
			{
			    
			    if ($tz == 0)
			    {
				    $where = $where . " AND {$field['db_field']} BETWEEN '{$curr_v_start}' AND '{$curr_v_end}' ";
			    }
			    elseif ($tz > 0)
			    {
			        
			    }
			    elseif ($tz < 0)
			    {
			        
			    }
			}
			*/
			
		} 
		elseif (isset($field['filter']['date-time']))
		{
			$curr_col_index = $curr_col_index + 2;
			$start_input_name = $field['input_name'] ."_start";
			$end_input_name = $field['input_name'] ."_end";
			$curr_v_start = isset($_GET[$start_input_name]) ? $_GET[$start_input_name] : date('Y-m-d', mktime(0, 0, 0, date("m") , date("d") - 7, date("Y")));
			$curr_v_end = isset($_GET[$end_input_name]) ? $_GET[$end_input_name] : date("Y-m-d");
			echo "<td><font>{$field['name']}-From:</font></td><td><input type='date' name='{$start_input_name}' value='{$curr_v_start}' class='form-field-filter'></td><td><font>To:</font></td><td><input type='date' name='{$end_input_name}' value='{$curr_v_end}' class='form-field-filter'></td>";
			if (isset($curr_v_start) and $curr_v_start != '' and isset($curr_v_end) and $curr_v_end != '')
			{
				$curr_v_end = $curr_v_end . " 23:59:59";
				$where = $where . " AND {$field['db_field']} BETWEEN '{$curr_v_start}' AND '{$curr_v_end}' ";
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
			echo "<td><font>{$field['name']}:</font></td><td style='border: 1px solid border-radius: 10px;'><div class='form-field-filter' style='overflow: auto; height:200px;'>";
			

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
			echo "</div></td>";
			
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
echo '</tr></table>';
if ($div_filter_2)
{
	echo '</div>';
}



if (isset($_GET['dev']))
{
	//echo "<pre>";
	print_r($_GET);
	//echo "</pre>";
}



// Remove The ID from list of fields:
if (isset($search_list_id) and $search_list_id)
{
	unset($fields['id']);
}


// TimeZone Fix
if (isset($curr_v_start) and $curr_v_start != '' and isset($curr_v_end) and $curr_v_end != '')
{
}
else 
{
    die ('Date Error');
}
    
if ($tz == 0)
{
    $where = $where . " AND imp_date BETWEEN '{$curr_v_start}' AND '{$curr_v_end}' ";
    if (isset($hour)) {$where = $where . " AND imp_hour = {$hour}";}
}
elseif ($tz > 0)
{   // Jerusalem
    $tz_clean = str_replace("+","",$tz) + 0;
    $curr_v_end  = $curr_v_end . " 23:59:59";
    $tz_start_date_hour =  date("Y-m-d H:i:s", strtotime($curr_v_start . "-".$tz_clean.' hours'));
    $tz_end_date_hour   =  date("Y-m-d H:i:s", strtotime($curr_v_end . "-".$tz_clean.' hours'));
    $where = $where . " AND DATE_ADD(TIMESTAMP(imp_date), imp_hour, 'HOUR') >= '{$tz_start_date_hour}' 
                        AND DATE_ADD(TIMESTAMP(imp_date), imp_hour, 'HOUR') <= '{$tz_end_date_hour}' ";
    if (isset($hour)) 
    {
        $hour = $hour - $tz_clean >= 0 ? $hour - $tz_clean : 24 + $hour - $tz_clean;
        $where = $where . " AND imp_hour = {$hour}";
    }
}
elseif ($tz < 0)
{
    // LA
    $curr_v_end  = $curr_v_end . " 23:59:59";
    $tz_clean = str_replace("-","",$tz) + 0;
    $tz_start_date_hour =  date("Y-m-d H:i:s", strtotime($curr_v_start . "+".$tz_clean.' hours'));
    $tz_end_date_hour   =  date("Y-m-d H:i:s", strtotime($curr_v_end . "+".$tz_clean.' hours'));
    $where = $where . " AND DATE_ADD(TIMESTAMP(imp_date), imp_hour, 'HOUR') >= '{$tz_start_date_hour}' 
                        AND DATE_ADD(TIMESTAMP(imp_date), imp_hour, 'HOUR') <= '{$tz_end_date_hour}' ";
    if (isset($hour))
    {
       // echo "ddd";
        $hour = $hour + $tz_clean <= 23 ? $hour + $tz_clean : $hour + $tz_clean - 24;
        $where = $where . " AND imp_hour = {$hour}";
    }
   
}

//echo $where;



?>
<br><br><br>
<center><input class="submit-button" type="submit" class="submit" value='Filter' />&nbsp&nbsp&nbsp&nbsp<input class="submit-button" type="submit" class="submit" value='Export To CSV' name='export_excel_only' /></center>
<br><br> 
</div>

<br>


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
						console.log(data)
						
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