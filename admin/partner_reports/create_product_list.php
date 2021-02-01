<?php

//include_once dirname(dirname(__FILE__)). '/config.php';
//include_once dirname(dirname(__FILE__)). '/menu/menu.php';

	session_start();
	require_once  dirname(dirname(dirname(__FILE__))). '/general/general.php';  
	$theme_path = $_SESSION["theme_path"];
	$conn = get_db_conn();
	if (!isset($conn)or $conn->connect_error) 
	{
		die("Connection failed: " . $conn->connect_error);
	}

	?>
	<script src="http://ajax.googleapis.com/ajax/libs/jquery/3.1.1/jquery.min.js"></script>
	<script src="/admin/menu/sorttable.js" ></script>
	<script src='/admin/table2CSV.js'></script>
	<?php
	echo "<LINK href='{$theme_path}' rel='stylesheet' type='text/css'>";

?>

<!DOCTYPE html>
<html>
<head>
<!--<LINK href="/admin/theme/edit.css" rel="stylesheet" type="text/css">-->
</head>

<?php

$is_admin_user = false;
if (strpos("@".strtolower($_SESSION['user_name']),"admin") > 0)
{
    $is_admin_user = true;
}

$product_id	= isset($_GET["product_id"]) 		? $_GET["product_id"] 		: '';
$list_type	= isset($_GET["list_type"]) 	? $_GET["list_type"]	: '';
$behavior 	= isset($_GET["behavior"]) 	? $_GET["behavior"]	: 0;
$id_list 	= isset($_POST["id_list"]) 		? $_POST["id_list"]		: '';
$ids_array	= explode(",",$id_list);
$count_rows = sizeof($ids_array);

?>	
	
<body>
<form class="form-container" method="post">
  <input name='id_list' 		value=<?php echo $id_list; ?>  hidden >
  <div class="form-title"><h2>Smart Link Rule List</h2></div>
  <div class="data-main-div">
  <table>	
  <tr><td><div class="form-title">Product 			: </div></td><td><input  class="form-field-edit" 				name='product_id' 		value=<?php echo $product_id ?>  								  ></td></tr>
  
<?php

	echo "<tr><td><div class='form-title'>List Type : </div></td><td><select class='form-field-edit' name='list_type' required>";
	
	$query = "SELECT id, field_name, title FROM list_types"; 
	$result = $conn->query($query);
	$row=mysqli_fetch_array($result,MYSQLI_ASSOC);
	echo "<option value='' >Please Select A Value</option>";
	while ($row != NULL) 
	{
		$selected = "";
		if($list_type == $row["field_name"])
		{
			$selected = "selected";
			
		}
		echo "<option value='{$row["field_name"]}' ind_val='{$row["id"]}' {$selected}>{$row["title"]}</option>";
		$row=mysqli_fetch_array($result,MYSQLI_ASSOC);
	}
	echo "</select></td></tr>";
	
	
	echo "<tr><td><div class='form-title'>behavior : </div></td><td><select class='form-field-edit' name='behavior' required>";
	echo "<option value=''>Please Select A Value</option>";
	foreach (array(0 => 'Black List', 1 => 'White List') as $v=>$title)
	{
		$selected = "";
		if($behavior == $v)
		{
			$selected = "selected";
		}
		echo "<option value='{$v}' {$selected}>{$title}</option>";
	}
	echo "</select></td></tr>";	
	?>
</table>
	<br>
	<div id='please_wait' align='center' valign='center'><img class='please_wait'></img></div>
	<div class="datagrid" style="filter:alpha; opacity:0;">
	<table class="sortable">    
	
	<?php
	// echo "<font class='form-filter-field-label'>Total Rows: {$count_rows} </font><br>";
	
	echo "<thead style='display:block'><tr>";
   	echo "<th><input type='checkbox' value='all_rows' name='id_checkbox_all' onchange='select_deselect_all(this.checked)'></th>";
    echo "<th>{$list_type}</th>";
	echo "</tr></thead><tbody style='display:block; overflow:auto; height:400px;'>";
	
	foreach ($ids_array as $id)
	{
		echo "<tr>";
		echo "<td><input type='checkbox' value='{$id}' name='id_checkbox'></td>";
		echo "<td>{$id}</td>";
		echo "</tr>";
	}
	
	   echo "</tbody><tfoot></tfoot></table>";
  //  echo "<font class='form-filter-field-label'>Total Rows: {$count_rows} </font><br>";
   echo "</div>";
	?>
	
	<br><br>
  <center><button class="submit-button" type="button" class="submit" onclick="apply()">Submit</button></center>
  </div>
</form> 

</body>

</html>


<script>
window.onload = function()
{
	table 			= document.getElementsByClassName('sortable')[0];
	thead 			= table.children[0];
	theadrow 		= thead.children[0];
	tbody 			= table.children[1];
	tbodyrow 		= tbody.children[0];
	datagrid 		= document.getElementsByClassName("datagrid")[0];
	full_dg_width 	= datagrid.offsetWidth;
	curr_row_width 	= theadrow.offsetWidth;
	
	if (tbodyrow != null)
	{
		tbody.style.width = "20000px";
		var total_row_size = 0;
		for (i = 0; i < theadrow.childElementCount; i++) 
		{
			
			var max_width = Math.max(theadrow.children[i].offsetWidth,tbodyrow.children[i].offsetWidth);
			tbodyrow.children[i].style.width = max_width - 20;
			theadrow.children[i].style.width = max_width - 20;
			total_row_size += max_width;
		}
		
		
		total_row_size += 30;
		tbody.style.width = total_row_size;
		//console.log(total_row_size);
		tbody.style.height = Math.min(tbodyrow.offsetHeight*tbody.childElementCount, 400);
		
		please_wait = document.getElementById('please_wait')
		
		// fill datagrid
		
		if (false)
		{
			tbody.style.width = full_dg_width - 20;
			for (i = 0; i < theadrow.childElementCount; i++) 
			{
				var new_size = full_dg_width * theadrow.children[i].offsetWidth / curr_row_width - 20;
				console.log(new_size);
				theadrow.children[i].style.width = new_size;
				tbodyrow.children[i].style.width = new_size;
			}
		}
		enlarge_screen_size_table_fix();
		
		window.setTimeout(function(){ change_opacity(please_wait, 0.75);} ,100);
		window.setTimeout(function(){ change_opacity(please_wait, 0.5);} ,200);
		window.setTimeout(function(){ change_opacity(please_wait, 0.25);} ,300);
		window.setTimeout(function(){ document.getElementById('please_wait').hidden = true;} ,400);
		
		
		window.setTimeout(function(){ change_opacity(datagrid, 0.25);} ,400);
		window.setTimeout(function(){ change_opacity(datagrid, 0.5);} ,500);
		window.setTimeout(function(){ change_opacity(datagrid, 0.75);} ,600);
		window.setTimeout(function(){ change_opacity(datagrid, 1);} ,700);
	}
	else
	{
		total_row_size = 50;
		document.getElementById('please_wait').hidden = true;
		
	}
	
	// CFG Defenition for unload js functions
	if (typeof onload_view === "function")
	{
		onload_view();
	}

}

window.onresize = function(){enlarge_screen_size_table_fix()};

function enlarge_screen_size_table_fix()
{
	console.log("enlarge_screen_size_table_fix")
	table 			= document.getElementsByClassName('sortable')[0];
	thead 			= table.children[0];
	theadrow 		= thead.children[0];
	tbody 			= table.children[1];
	tbodyrow 		= tbody.children[0];
	datagrid 		= document.getElementsByClassName("datagrid")[0];
	full_dg_width 	= datagrid.offsetWidth;
	curr_row_width 	= theadrow.offsetWidth;
	
	if (full_dg_width > curr_row_width)
	{
		tbody.style.width = full_dg_width - 20;
		for (i = 0; i < theadrow.childElementCount; i++) 
		{
			var new_size = full_dg_width * theadrow.children[i].offsetWidth / curr_row_width - 20;
			//console.log(new_size);
			theadrow.children[i].style.width = new_size;
			tbodyrow.children[i].style.width = new_size;
		}
	}
}

function change_opacity(element, opacity_value)
{
	element.style.opacity = opacity_value;	
}	

function select_deselect_all(is_selected)
{
	var checkboxes = document.getElementsByName('id_checkbox');

	for(var i = 0; i< checkboxes.length; i++)
	{
		checkboxes[i].checked = is_selected
	}
}


function apply()
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
		// console.log(id_list);
		 
		 
		 
		 
		 var product_id 	= document.getElementsByName("product_id")[0].value
		 var list_type 		= document.getElementsByName("list_type")[0];
		 var list_type_id 	= list_type.children[list_type.selectedIndex].getAttribute("ind_val")
		 var behavior 		= document.getElementsByName("behavior")[0].value
		 
		if (product_id == "")
		{
			alert ("Please Select A Product");
			return
		}
		
		if (list_type.value == "")
		{
			alert ("Please Select A List Type");
			return
		}
		
		if (behavior == "")
		{
			alert ("Please Select Black List \ White List");
			return
		}
		
		if (list_type_id == undefined)
		{
			alert ("Please Select Known list_type");
			return
		}
		 
		 
		//console.log("slr_id:" + slr_id);;
		//console.log("list_type_id:" + list_type_id);
		//console.log("behavior:" + behavior);
		//console.log("id_list:" + id_list);
		
		$.post( "post_create_product_list.php?product_id="+product_id+"&list_type_id="+list_type_id+"&behavior="+behavior,{id_list: id_list})
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
					popupWindow = window.open( data.pop_url, data.pop_title,data.pop_properties);
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
	</script>