<?php
// die("maintaince");
// unset($_SESSION);
// echo "\n\n\n\n\n" . $_SESSION['user_id'] . "\n\n\n\n\n";
if (true)
{
	include("menu_2.php");
	return;
}


if (isset($new_admin_type) and $new_admin_type)
{
    echo "<LINK href='{$theme_path}' rel='stylesheet' type='text/css'>";
	// New way - already defined in CFG
	?>
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
	<?php
}
else
{ 
	// Old way
	//echo "old type";
	session_start();
	echo "<LINK href='{$theme_path}' rel='stylesheet' type='text/css'>";
	require_once  dirname(dirname(dirname(__FILE__))). '/general/general.php';  



	$theme_path = $_SESSION["theme_path"];
	$conn = get_db_conn();
	if (!isset($conn)or $conn->connect_error) 
	{
		die("Connection failed: " . $conn->connect_error);
	}



	?>
	 <html>
	   <head>
		  <LINK href="<?php echo $theme_path; ?>" rel="stylesheet" type="text/css">
		  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.1/jquery.min.js"></script>
		  <script type="text/javascript" src="/admin/menu/tablesorter-master/js/jquery.tablesorter.js"></script>
		  <script src='/admin/table2CSV.js'></script>
	<?php
}

if (isset($_GET['sess']))
{
	print_r($_SESSION);
}
if (!isset($_SESSION["user_id"]))
{
//	header('Location: /admin/login/login.php'); 
	echo "You Need To Re-Login, Then Save the Changes again for it to take affect "; 
	echo "<script>window.open('/admin/login/login.php?close=1','Please Re-Login','_blank,directories=0,titlebar=0,toolbar=0,location=0,status=0,menubar=0,scrollbars=no,resizable=no,      width=500,height=700');</script>";																				  
}
else
{
	apache_note('user_id', $_SESSION["user_id"]);
	apache_note('user_name', $_SESSION["user_name"]);
}

?>

      <style>
ul {
    list-style-type: none;
    margin: 0;
    padding: 0;
    overflow: hidden;
    background-color: white;
    border-bottom: 1px solid #e4e5e7;
}

li {
    float: left;
}



li a, .dropbtn {
    display: inline-block;
    color: #3f5872;
    text-align: center;
    padding: 10px 16px;

}

li a:hover, .dropdown:hover .dropbtn {
    background-color: #fff2bf;
}

li.dropdown {
    display: inline-block;
}

.dropdown-content {
    display: none;
    position: absolute;
    background-color: white;
    color:black;
    min-width: 160px;
    box-shadow: 0px 8px 16px 0px rgba(0,0,0,0.2);
	z-index:	500;
}

.dropdown-content a {
    color: black;
    padding: 12px 16px;
    text-decoration: none;
    display: block;
    text-align: left;
	z-index:	500;
}

.dropdown-content a:hover {background-color: #fff2bf}

.dropdown:hover .dropdown-content {
    display: block;
}

</style>
      
    </head>

    <script type="text/javascript">
		function popup(url) 
		{
		   popupWindow = window.open( url, 'Clicks',
	       "height=300,width=1300,left=90,top=200,resizable=yes,scrollbars=yes,toolbar=yes,menubar=no,location=no,directories=no,status=yes");
		}
	</script>
	<div><table id='logo_table' hidden class="div-menu" style='border-bottom: 1px solid #e4e5e7;width: 100%;' onmouseover='logo_table_over();' onmouseleave='logo_table_leave();'><tr><td><a href='https://www.le-sha.com/'><img class='logo' style='padding-left: 20px;'></a></td><td style='width: 70%;'></td><td style='text-align-last: right;' class='form-filter-field-label'>
	<li class='dropdown'><data value='1' class='dropbtn'>▼<b><?php echo $_SESSION['user_name']; ?></b></data><div class='dropdown-content'><a href='#'>Profile</a><a href='/admin/login/login.php'>Sign Out</a></div></li>
	</td></tr></table></div>
	<div id="logo_table_hint" onmouseover="logo_table_hint_over();" onmouseleave="logo_table_hint_leave();" style="background: #3f5872;height: 4px;"></div>
	
	
   <div class="form-container-menu">
   <div class="div-menu">
   <?php
    
	//echo "USER:". $_SESSION['user_id'];
	//die('ttt');
	$log_entery = "\nUSER=>". $_SESSION['user_id'] ."   URI=>".$_SERVER['REQUEST_URI'] . "   TIME=>".date('Y-m-d h:i');
	/*$log_entery = json_encode(array(
								'user'=>$_SESSION['user_id'],
								'uri' =>$_SERVER['REQUEST_URI'], 
								'time'=>date('Y-m-d h:i')));
		*/						
	//
	echo   '<ul id="ul_menu" style="border-bottom: 1px solid;">';
    if (true)
	{
    file_put_contents("/var/www/html/admin/menu/menu_log.log",$log_entery,FILE_APPEND);
	}
	
	if (true)
	{
		$parsed_url = parse_url($_SERVER['REQUEST_URI']);
		$path = $parsed_url['path'];
		$file_name = basename($path);
		$module = str_replace("/".$file_name,"",str_replace("/admin/","",$path));
		$qs = $parsed_url['query'];
		$insert_query = "INSERT INTO `user_activity` (`user_id`, `url`, `module`, `file`, `qs`) VALUES ('{$_SESSION['user_id']}','{$path}','{$module}','{$file_name}','{$qs}');";
		//echo $insert_query;
		//die();
		$conn->query($insert_query);
	}
	
    $allowed = false;
	$curr_uri = ltrim(rtrim(parse_url($_SERVER["REQUEST_URI"], PHP_URL_PATH)));
	$curr_module = str_replace('/admin/','',$curr_uri);
	$curr_module =	substr($curr_module,0,strpos($curr_module,"/"));

	
	// Get Errors:
	$query_alerts = "	SELECT COUNT(*) AS number_of_errors, GROUP_CONCAT(c.id) AS ids_to_inform, min(date(date)) as start_date,max(date(date)) as end_date
							FROM cron_logs AS c
							JOIN cronjobs_processes AS t ON c.cronjob_id = t.id
							WHERE awaiting_action = 1 and find_in_set({$_SESSION['user_id']},t.inform_user_ids)";
	$result = $conn->query($query_alerts);
	$alert_row = $result->fetch_assoc();
	 
	//echo "ERRORS:" . $alert_row['number_of_errors'];
	
	
	
	
	
	
	if (ltrim(rtrim('/admin/menu/menu.php')) == $curr_uri)
	{
				$allowed = true;
	}
	
	if (ltrim(rtrim('/admin/menu/menu_2.php')) == $curr_uri)
	{
	    $allowed = true;
	}
	
	$menu_array = $_SESSION['menu_array'];


	foreach ($menu_array as $menu_item)
	{
		if ($menu_item['visible'] == 0)
		{
			if ($menu_item['module'] == $curr_module)
			{
				$allowed = true;
			} 
		}
		elseif ($menu_item['href'] == '')
		{
			echo "<li class='dropdown'>";
			if (false)
			{
				echo "<data  value='1' class='dropbtn'>&#9660;<u><font color='red'>{$menu_item['title']}</font></u></data>";
			}
			else
			{
				echo "<data  value='1' class='dropbtn'>&#9660;<u>{$menu_item['title']}</u></data>";
			}
			echo "<div class='dropdown-content'>";
			foreach ($menu_item['children'] as $menu_item_child)
			{
				if ($menu_item_child['title'] == "Cron Log" and $alert_row['number_of_errors'] > 0)
				{
					echo "<a href='{$menu_item_child['href']}&id={$alert_row['ids_to_inform']}'><font color='red'>{$menu_item_child['title']} &nbsp`;</font><font color='red' size='2'>{$alert_row['number_of_errors']}</font></a>";
				} // $alert_row['ids_to_inform']
				else
				{
					echo "<a href='{$menu_item_child['href']}' oncontextmenu='javascript:window.open(\"{$menu_item_child['href']}\", \"_blank\");return false;'><font>{$menu_item_child['title']}</font></a>";
				}
				if ($menu_item_child['module'] == $curr_module)
				{
					$allowed = true;
				} 
			}
			echo "</div>";
			echo "</li>";
		}
		else
		{
			if ($menu_item['title'] == 'Messages')
			{
				if ($alert_row['number_of_errors'] > 0)
				{
					echo "<li><a href='{$menu_item['href']}&id={$alert_row['ids_to_inform']}' oncontextmenu='javascript:window.open(\"{$menu_item['href']}&id={$alert_row['ids_to_inform']}\", \"_blank\");return false;'><font color='red'>{$menu_item['title']}&#9993;</font><font color='red' size='2'>{$alert_row['number_of_errors']}</font></a></li>";
				} // $alert_row['ids_to_inform']
				else
				{
					echo "<li><a href='{$menu_item['href']}'>{$menu_item['title']}&#9993;</a></li>";
				}
			}
			else
			{
				echo "<li><a href='{$menu_item['href']}' oncontextmenu='javascript:window.open(\"{$menu_item['href']}\", \"_blank\");return false;'>{$menu_item['title']}</a></li>";
			}
			
			if ($menu_item['module'] == $curr_module)
			{
				$allowed = true;
			} 
		}
	}


	
   ?>

   <!-- <li><a href='/admin/publishers/cfg.php'>test n</a></li> -->
   </ul>
   </div>
   <?php
   	if (!$allowed)
	{
		die('<br><br><h1><font color="red">Not Allowed!</font></h1>');
	}
   
   ?>
   

</html>
<script>

var show_logo_table = false;

function logo_table_over()
{
	show_logo_table = true;
}

function logo_table_leave()
{
	show_logo_table = false;
	setTimeout(show_hide_logo_table,1000);
}

function logo_table_hint_over()
{
	show_logo_table = true;
	setTimeout(show_hide_logo_table,1000);
}

function logo_table_hint_leave()
{
	show_logo_table = false;
}

function show_hide_logo_table()
{
	if (show_logo_table & (logo_table.style.display == "none" || logo_table.style.display == "") )
	{
		$("#logo_table").show("slow");
	}
	//  and logo_table.style.display == "table"
	else if (!show_logo_table & logo_table.style.display == "table")
	{
		$("#logo_table").hide("slow");
	}
}


if (typeof onload_fuctions === 'undefined') 
{
	var onload_fuctions = [];
}


function menu_on_load()
{

}
onload_fuctions.push(menu_on_load);
// check out https://www.w3schools.com/howto/howto_js_navbar_hide_scroll.asp

</script>