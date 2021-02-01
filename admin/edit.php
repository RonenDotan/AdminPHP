<?php
include_once '/var/www/html/admin/config.php';

?>
 
<script> 
function count_checkbox(checkbox_name)
{
	var count = document.querySelectorAll('input[name="'+checkbox_name+'"][type="checkbox"]:checked').length;
	counter_id = checkbox_name.replace("[]",'') + "_count";
	
	if (count == 0)
	{
		document.getElementById(counter_id).innerText = "No Item Selected";
	}
	else if (count == 1)
	{
		document.getElementById(counter_id).innerText = "1 Item Selected";
	}
	else
	{
		document.getElementById(counter_id).innerText = count + " Items Selected";
	}
}
</script> 
 

<form class="form-container" method="post">

<?php

$id = $_GET['id'];
if (isset($_GET['id']))
{
    $title_to_display = $title_to_display . " - {$id}";
}
elseif (isset($_GET['multi_ids']) and strlen($_GET['multi_ids']) > 0)
{
    $title_to_display = "Multi Edit ". $title_to_display . " {$_GET['multi_ids']}";
}
else 
{
    $title_to_display = "Add New To {$title_to_display}";
}
$included_json_viewer = false;



echo "<div class='form-title'><h2>{$title_to_display}</h2></div>";

if  (isset($edit_tabs))
{
	echo "<div id='edit_tabs_div' class='tab'>";
	foreach ($edit_tabs as $tab)
	{
		echo "<button type='button' class='{$tab['class']}' id='{$tab['name']}' oncontextmenu='' onclick='tab_click(this);'>{$tab['title']}</button>";
	}
	echo "</div>";
}
		
echo "<div class='data-main-div'>";


if (isset($id))
{

    if (isset($query) == false)
    {
        $query = "SELECT * FROM {$src_table} WHERE 1=1 AND ";      
    }
	else 
	{
		if(!isset($where))
		{
			$where = "1=1";
		}
		$query = str_replace('##where##', $where, $query) . " AND ";	
	}
    $query = $query. " id = {$id}"; 

	$result = $conn->query($query);


    $row=mysqli_fetch_array($result,MYSQLI_ASSOC);


	if(isset($_GET['dev']))
	{
		echo "<pre>"; 
		echo "\nGlobal\n";
		echo "query : $query\nResult:\n";
		print_r($row);
		echo "</pre>";
	}


    $id_for_insert_values = "id,";
    $id = "{$id},";
}
else
{
    $id_for_insert_values = "";
    $id = "";
}

if (function_exists("before_edit"))
{
    if (isset($_GET['mod']) and $_GET['mod'] == 'edit')
    {
        before_edit();
    }
}

   echo '<table>';
    echo "<tr>";

    

   foreach ($fields as $field)
   {
	   if (!isset($edit_tabs))
	   {
		  $use_tab = 'general show';
	   }
	   else
	   {
		   if (isset($field['edit_tab']))
		   {
			   $use_tab = $field['edit_tab'];
			   if($use_tab == 'general') 
			   {
				   $use_tab = 'general show';
			   }
			   else
			   {
				   $use_tab = "{$use_tab} hide";
			   }
		   }
		   elseif (isset($use_tab))
		   {
			   // $use_tab  - unchanged form last field
		   }
		   else
		   {
			   $use_tab = 'general show';
		   }
	   }
	   
		$attributes = "";
   		if (isset($field['requierd']) and $field['requierd'] == 1)
		{
			$attributes = "required";	
		}
			
		if (isset($field["readonly"]) and $field["readonly"] == true)
		{
			$attributes = $attributes. ' readonly ';  		
		}
		
		if (isset($field["id"]) and $field["id"])
		{
			$attributes = $attributes. " id='{$field['input_name']}' "; 
		}															   
		if (!isset($field['only_view']))
		{

			   $name = $field['name'];
			   $type = $field['type'];
			   $input_name = $field['input_name'];
			   if (isset($row[$field['db_field']]))
			   {
			   		$value = $row[$field['db_field']];
			   }
			   elseif (isset($_GET[$input_name]))
			   {
			   		$value = $_GET[$input_name];
			   }
			   elseif (isset($field['filter']['defualt']))
			   {
			   		$value = $field['filter']['defualt'];
			   }												 
			   else
			   {
			   		$value = -1;
			   }


			   if (isset($field['hidden']))
			   {

					echo "<input type='hidden' name='{$field['input_name']}' value='{$_GET[$field['input_name']]}' /> ";
			   }
			   elseif (isset($field['drop_down']))
				{
					echo "<tr class='{$use_tab}'><td><div class='form-title'>{$field['name']}: </div></td><td><select class='form-field-edit' name='{$field['input_name']}' {$attributes}>";
					echo "<option value=''>Please Select A Value</option>";
					foreach ($field['drop_down'] as $k => $v)
					{
						$selected = "";
						if ($value==$k) 
						{
							//$where = $where . " AND {$field['db_field']} = {$k}";
							$selected = "selected = 'true'";
						}
						
						if (isset($field['show_id']) and !$field['show_id'])
						{
							echo "<option value='{$k}' {$selected}>{$v}</option>";
						}
						else
						{
							echo "<option value='{$k}' {$selected}>{$k} :: {$v}</option>";
						}
						
					}
					echo "</select>";
					if (isset($field["link_url"]))
					{			
						$link_url = str_replace("{id}",$value,$field["link_url"]);
						echo "<button type='button' onclick='window.open(\"{$link_url}\", \"_blank\");'>Navigate</button>";
					}
					echo "</td></tr>";
					
				}
				elseif (isset($field['selectbox']))
				{
					if (true)
					{
						echo "<tr class='{$use_tab}'><td><div class='form-title'>{$field['name']}: <font size=1><div id='{$field['input_name']}_count'>0</div></font></div></td><td style='border: 1px solid border-radius: 10px;'><div class='form-field-edit' name='{$field['input_name']}' style='overflow: auto; height:100px;width:685px' {$attributes}>"; 
						$curr_values = explode(",",$value);						
					}
 
					
					foreach ($field['selectbox'] as $k => $v)
					{
						$checked = "";
						if (in_array($k,$curr_values))
						{
							$checked = "checked";
						}
						echo "<div style='padding-bottom: 4px;'>";
						echo "<input type='checkbox' id='{$field['input_name']}[]_{$k}' name='{$field['input_name']}[]' value='{$k}' {$checked} onclick='count_checkbox(\"{$field['input_name']}[]\");'>";
						if (isset($field['show_id']) and !$field['show_id'])
						{
							echo  "<label for='{$field['input_name']}[]_{$k}' style='font-size:13.333px'>{$v}</label>";
						}
						else
						{
							echo  "<label for='{$field['input_name']}[]_{$k}' style='font-size:13.333px'>{$k} :: {$v}</label>";
						}
						echo "</div>";
					}
					echo "</div></td></tr>";
					echo "<script>count_checkbox(\"{$field['input_name']}[]\")</script>";
				}
				
			   elseif ($type == 'textarea')
			   {
					if ($value == -1)
					{
						$value = '';
					}
					echo "<tr class='{$use_tab}'><td><div class='form-title'>{$name} : </div></td><td><textarea rows='30' class='form-field-edit' name='{$input_name}'> {$value}</textarea></td></tr>";
			   }
			   elseif (isset($field["datalist"]) and $field["datalist"])
				{
					// Get Label
					$query_for_label_base = $field["datalist"];
					$input_name = $field['input_name'];
					
					if ($value)
					{
						
						$sql = explode("where", $field["datalist"])[0]. "where id = {$value}" ;
						$res = $conn->query($sql);
						$label_row = $res->fetch_assoc();
						$label = $label_row['title'];
						
					}
					else
					{
						$label = "";
					}
					echo "<tr class = '{$use_tab}'><td><font>{$field['name']}:</font><br><font size=1>(Auto Complete)</font></td><td><div style='position: relative;'><input  list='{$input_name}_datalist' class='form-field-edit' name='{$input_name}' id='{$input_name}_display_only' value='{$label}' {$attributes} onkeyup='datalist_keyup(this, \"{$field['datalist']}\")' onchange='datalist_change(this)' onblur='datalist_blur(this)'><div style='z-index: 10; position: absolute;top: 0; left: 0;' id='{$input_name}_please_wait' hidden><img class='please_wait' style='width: 100%; opacity :0.70' ></div>";
					
					if (isset($field["link_url"]))
					{			
						$link_url = str_replace("{id}",$value,$field["link_url"]);
						echo "<button type='button' onclick='window.open(\"{$link_url}\", \"_blank\");'>Navigate</button>";
					}
					
					echo "</div></td></div>";
					echo "<datalist id='{$input_name}_datalist'></datalist>";
					echo "<input type='hidden' name='{$input_name}' id='{$input_name}' value='{$value}'></tr>";
					
				}
			   else
				{
	   				if ($value == -1)
					{
						$value = '';
					}
					
					echo "<tr class='{$use_tab}'><td><div class='form-title'>{$name} : </div></td><td><input  class='form-field-edit' type='{$type}' name='{$input_name}' value='{$value}' {$attributes}/>";
					if (isset($field["link_url"]))
					{			
						$link_url = str_replace("{id}",$value,$field["link_url"]);
						echo "<button type='button' onclick='window.open(\"{$link_url}\", \"_blank\");'>Navigate</button>";
					}
					echo "</td></tr>";
				}
		}
		if (isset($field['json_viewer']) and $field['json_viewer'])
		{
			
			echo "<tr class='{$use_tab}'><td></td><td>";
			echo "<button type='button' id='{$field['input_name']}_json_btn' name='{$field['input_name']}_json_btn' onclick='json_format(this)'>Format JSON</button>";
			echo "&nbsp;&nbsp;<label><input type='checkbox' id='{$field['input_name']}_collapsed'>Collapse nodes</label>";
			echo "&nbsp;&nbsp;<label><input type='checkbox' id='{$field['input_name']}_with-quotes'>Keys with quotes</label>";
			echo "</tr></td>";
			echo "<tr class='{$use_tab}'><td colspan='2'>";
			echo "<div><pre id='{$field['input_name']}-json-renderer' class='pre-json'></pre></div>";
			echo "</tr></td>";
			if (!$included_json_viewer)
			{
				$included_json_viewer = true;
				
				?>
				<script src="/admin/json-viewer/jquery.json-viewer.js"></script>
				<link href="/admin/json-viewer/jquery.json-viewer.css" type="text/css" rel="stylesheet">
				
				<script>
					function json_format(element)
					{
						var base_element = element.name.replace("_json_btn", "");
						var input_src = document.getElementsByName(base_element)[0].value;
						try 
						{
							var input = eval('(' + input_src + ')');
							//console.log(input);
						}
						catch (error) 
						{
						  return alert("Cannot eval JSON: " + error);
						}
						
						options = 	{
									collapsed: $('#'+base_element+'_collapsed').is(':checked'),
									withQuotes: $('#'+base_element+'_with-quotes').is(':checked')
									};
						$('#'+base_element+'-json-renderer').jsonViewer(input, options);
					}
				</script>
				<?php
			}
		}
	}
	

   ?>

  </table>
  
  <center><input class="submit-button" type="submit" name="submit" class="submit" value="<?php echo $submit_content; ?>"></center>
  </div>
</form>

<?php

if (isset($edit_only_fields) and $edit_only_fields)
{ /* end here */ }
else
{

if($_POST['submit'])
{
    foreach ($_POST as $p_n => $p_v)
    {
        if (!isset($fields[$p_n]['selectbox']) and !isset($fields[$p_n]['drop_down']))
        {
            $_POST[$p_n] = ltrim(rtrim($p_v));
        }
    }
    
    if (isset($sql_insert) == false)
    {
        $columns = "";
        $values = "";
	    $duplicate = "ON DUPLICATE KEY UPDATE";

		if (isset($use_client_id) and $use_client_id)
		{
			$columns = $columns . 'client_id,';
	        $values = $values ."'".$_SESSION['client_id']."',";
		}

		if (!isset($_GET['multi_ids']))
		{
            foreach ($fields as $field)
            {
           		if (isset($field['db_field']) and !isset($field['only_view']))
    			{
    				$columns = $columns . $field['db_field'] . ',';
    	            
    				if (isset($field['drop_down_multi']) and $field['drop_down_multi'])
    				{
    					$multi_str = "";
    					foreach ($_POST[$field['input_name']] as $selected_option)
    					{
    						$multi_str = $multi_str . "{$selected_option},";
    					}
    					$multi_str = trim($multi_str,',');
    					$values = $values ."'".$multi_str."',"; 
    				}
    				elseif (isset($field['selectbox']))
    				{
    					if (isset($field["requierd"]) and $field["requierd"] and sizeof($_POST[$field['input_name']]) == 0)
    					{
    						throw new Exception("Please Select Values In {$field['input_name']}");
    					}
    					$multi_str = implode(",",$_POST[$field['input_name']]);
    					$values = $values ."'".$multi_str."',";
    				}
    				else 
    				{
    					$values = $values ."'".$_POST[$field['input_name']]."',";
    				} 				
    
    	            $duplicate = "{$duplicate} {$field['db_field']} = values({$field['db_field']}) ,"; 
    			}
            }
            $columns = trim($columns,',');
            $values = trim($values,',');
            $duplicate = trim($duplicate,',');
            
            
            $sql_insert = "insert into {$src_table} ({$id_for_insert_values} {$columns})
                            VALUES ($id {$values})
                            {$duplicate}";
		}
		elseif (strlen($_GET['multi_ids']) > 0)
		{
		    $sql_insert = "Update {$src_table} SET ";
		    // Multi Action Edit
		    foreach ($fields as $field)
		    {
//              echo "\n\n{$field['db_field']}:";
		        if (!in_array($field["type"],array('text','textarea'))) {continue;}
//		        echo "398;";
		        if (isset($field['multi_edit']) and !$field['multi_edit']) {continue;}
		        if (!isset($field['db_field'])) {continue;}
		        $col = $field['db_field'];
//		        echo "401;";
		        
		        if (isset($field['selectbox']))
		        {
		            $val = implode(",",$_POST[$field['input_name']]);
		        }
		        elseif (!isset($_POST[$field['input_name']])) 
		        {
		            continue;
		        }
		        else 
		        {
		            $val = $_POST[$field['input_name']];
		        }
//		        echo "412;";
		        if ($val == -3 or ltrim(rtrim($val)) == 'Do Not Change') {continue;}
		        
//		        echo "414;";
		        // Now - passed all test, add to update statment.
		        $sql_insert = "{$sql_insert} {$col} = '{$val}',";
		        //echo $sql_insert;
		        
		    }
		    $sql_insert = rtrim($sql_insert, ","). "  WHERE id in ({$_GET['multi_ids']})";
		    // echo $sql_insert;
		    
		}

		if(isset($_GET['dev']))
		{
			echo "<pre>"; 
			echo "sql_insert : {$sql_insert}";
			echo "</pre>";
			die('No insert on dev');
		}
    }   

   submit_refresh($sql_insert,$conn);

}
}

function submit_refresh($sql_query,$conn)
{

    $ret_val = $conn->query($sql_query);

    $ins_id = mysqli_insert_id($conn);
    if ($ret_val== 1)   
    {
        if (isset($_GET['id']))
        {
			echo "<script>history.go(-1);</script>";
        }
        elseif (isset($ins_id) and $ins_id > 0)
        {
            ?>
             <script type="text/javascript">
                url = document.URL+"&id=<?php echo $ins_id;?>";
				var url = url.replace(".php&",".php?");
                location.href = url;
             </script>
             <?php
        }
        else
        {
            echo '<script type="text/javascript">location.href = document.URL;</script>';
        }
    }
    else 
    {
        echo "error occured while trying to update";
    }
}
 

if (function_exists("after_edit"))
{
    if (isset($_GET['mod']) and $_GET['mod'] == 'edit')
    {
        after_edit();
    }
}



?>


<script>
var cfg_fields = <?php echo json_encode($fields); ?>;
window.onload = function()
{
	
	if (typeof onload_edit === "function")
	{
		onload_edit();
	}

	var vars = {};
	var parts = window.location.href.replace(/[?&]+([^=&]+)=([^&]*)/gi, function(m,key,value) {	        vars[key] = value;	    });
	if (vars['multi_ids'] != undefined) 
	{
		multi_edit();
	}


	if (typeof onload_fuctions !== 'undefined')
	{
    	for (i=0; i < onload_fuctions.length; i++)
    	{
    		onload_fuctions[i]();
    	}
	}
}


function multi_edit()
{
	edit_fields = document.getElementsByClassName('form-field-edit');

	for (var i = 0; i < edit_fields.length; i++) 
		{
			field_name = edit_fields[i].getAttribute("name")
			if (cfg_fields[field_name].multi_edit != undefined & !cfg_fields[field_name].multi_edit) 
			{
				console.log(field_name + " hide")
				edit_fields[i].parentElement.parentElement.hidden = true;
			} 
			else 
			{
    			switch(edit_fields[i].type) 
    			{
    				case 'text':
    					edit_fields[i].value = "Do Not Change"
    					if (document.getElementById(field_name) != undefined) {document.getElementById(field_name).value = '-3';}
    						break;
    				case 'textarea': 
    					edit_fields[i].value = "Do Not Change"
    						break;
    				case 'select-one':
    					DoNotChangeOption = document.createElement("option");
    					DoNotChangeOption.text = "Do Not Change";
    					DoNotChangeOption.value = -3
    					edit_fields[i].options.add(DoNotChangeOption, 0);
    					edit_fields[i].options[0].selected = true;
    						break;
    				case undefined: 
    					if (edit_fields[i].children[0].children[0].type == 'checkbox') // This is a selectbox
    					{
    						for(var j=0; j < edit_fields[i].children.length; j++) {edit_fields[i].children[j].children[0].checked = false;}
    						el = document.createElement( 'html' );
    						el.innerHTML = '<div style="padding-bottom: 4px;"><input type="checkbox" id="'+field_name+'[]_-3" name="'+field_name+'[]" value="-3" onclick="count_checkbox(&quot;'+field_name+'[]&quot;);"><label for="'+field_name+'[]_-3" style="font-size:13.333px">Do Not Change</label></div>';
    						DoNotChangeSelectbox = el.getElementsByTagName('div')[0];
    						DoNotChangeSelectbox.children[0].checked = true;
    						edit_fields[i].insertBefore(DoNotChangeSelectbox,edit_fields[i].firstChild);
    					}
    					else
    					{
    						edit_fields[i].parentElement.parentElement.hidden = true;
    					}
    					break;
    				default: // not supported
    					edit_fields[i].parentElement.parentElement.hidden = true;
    					
    			}
			}
		}
	
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
			//console.log('sdfdf');
			dl.innerHTML += '<option value="Type At Least 3 Letters/Numbers"></option>';
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
    	var selected_value = hidden_field.value;
    	var found = false;
    	if (element.value != 'Type At Least 3 Letters/Numbers')
    	{
    		var list_length = dl.children.length
    		for (var i = 0; i < list_length; i++) 
    		{
    			if (dl.children[i].value == element.value)
    			{
    				selected_value = dl.children[i].getAttribute("data-value");
    				found = true;
    			}
    		}
    	}
    		
    	if (found)
    	{
    		hidden_field.value = selected_value;
    	}
    	else
    	{
    		hidden_field.value = "";
    		element.value = "";
    	}
	}
}

function datalist_blur(element)
{
	if (false)
	{
    	dl = document.getElementById(element.id.replace("_display_only","_datalist"));
    	hidden_field = document.getElementById(element.id.replace("_display_only",""));
    	var selected_value = hidden_field.value;
    	var found = false;
    	if (element.value != 'Type At Least 3 Letters/Numbers')
    	{
    		var list_length = dl.children.length
    		for (var i = 0; i < list_length; i++) 
    		{
    			if (dl.children[i].value == element.value)
    			{
    				selected_value = dl.children[i].getAttribute("data-value");
    				found = true;
    			}
    		}
    	}
    		
    	if (found)
    	{
    		hidden_field.value = selected_value;
    	}
    	else
    	{
    		hidden_field.value = "";
    		element.value = "";
    	}
	}
	else
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



function tab_click(button)
{
	click_button_id = button.id;
	
	$(document).ready(function () 
	{
		$('.show').addClass('hide'); 
		$('.show').removeClass('show'); 
		$('button.active').removeClass('active'); 
		
		console.log(button);
		$('.'+ button.id).addClass('show'); 
		$('#'+ button.id).addClass('active'); 
		$('.'+ button.id).removeClass('hide'); 
	});
	
}

</script>


