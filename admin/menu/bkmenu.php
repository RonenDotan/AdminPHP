<?php
//die("maintaince");

if (isset($new_admin_type) and $new_admin_type)
{
	// New way - already defined in CFG
}
else
{ 
	// Old way
	//echo "old type";
	session_start();

	require_once  dirname(dirname(dirname(__FILE__))). '/general/general.php';  



	$theme_path = $_SESSION["theme_path"];
	$conn = get_db_conn();
	if (!isset($conn)or $conn->connect_error) 
	{
		die("Connection failed: " . $conn->connect_error);
	}



	//ini_set('display_errors', 1);
	//ini_set('display_startup_errors', 1);
	//error_reporting(E_ALL);

	?>
	 <html>
	   <head>
		  <LINK href="<?php echo $theme_path; ?>" rel="stylesheet" type="text/css">
		  <script src="http://ajax.googleapis.com/ajax/libs/jquery/3.1.1/jquery.min.js"></script>
		  <script src="/admin/menu/sorttable.js" ></script>
		  <script src='/admin/table2CSV.js'></script>
	<?php
}

if (isset($_GET['sess']))
{
	print_r($_SESSION);
}
if (!isset($_SESSION["user_id"]))
{
	header('Location: /admin/login/login.php'); 
}
?>

      <style>
ul {
    list-style-type: none;
    margin: 0;
    padding: 0;
    -webkit-border-radius: 8px;
    -moz-border-radius: 8px;
    border-radius: 8px;
    overflow: hidden;
    background-color: white;
}

li {
    float: left;
}

li a, .dropbtn {
    display: inline-block;
    color: black;
    font-weight: 550;
    text-align: center;
    padding: 10px 16px;

}

li a:hover, .dropdown:hover .dropbtn {
    background-color: #0066ff;
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
	z-index:	100;
}

.dropdown-content a {
    color: black;
    padding: 12px 16px;
    text-decoration: none;
    display: block;
    text-align: left;
	z-index:	100;
}

.dropdown-content a:hover {background-color: #f1f1f1}

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
   <div class="form-container-menu">
   <img class="logo"><br><br>
  <ul>
   <?php
    
	//echo "USER:". $_SESSION['user_id'];
	//die('ttt');
	$log_entery = "\nUSER=>". $_SESSION['user_id'] ."   URI=>".$_SERVER['REQUEST_URI'] . "   TIME=>".date('Y-m-d h:i');
	/*$log_entery = json_encode(array(
								'user'=>$_SESSION['user_id'],
								'uri' =>$_SERVER['REQUEST_URI'], 
								'time'=>date('Y-m-d h:i')));
		*/						
	
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
	
	$menu_array = $_SESSION['menu_array'];
	//echo "<pre>"; print_r($menu_array);
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
					echo "<a href='{$menu_item_child['href']}&id={$alert_row['ids_to_inform']}'><font color='red'>{$menu_item_child['title']} &nbsp;</font><font color='red' size='2'>{$alert_row['number_of_errors']}</font></a>";
				} // $alert_row['ids_to_inform']
				else
				{
					echo "<a href='{$menu_item_child['href']}'>{$menu_item_child['title']}</a>";
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
					echo "<li><a href='{$menu_item['href']}&id={$alert_row['ids_to_inform']}'><font color='red'>{$menu_item['title']}&#9993;</font><font color='red' size='2'>{$alert_row['number_of_errors']}</font></a></li>";
				} // $alert_row['ids_to_inform']
				else
				{
					echo "<li><a href='{$menu_item['href']}'>{$menu_item['title']}&#9993;</a></li>";
				}
			}
			else
			{
				echo "<li><a href='{$menu_item['href']}'>{$menu_item['title']}</a></li>";
			}
			
			if ($menu_item['module'] == $curr_module)
			{
				$allowed = true;
			} 
		}
	}
	
	// Check If not in cronjob errors screen. if not - check errors.
	/*
	if (strtok($_SERVER["REQUEST_URI"],'?') != '/admin/cron_logs/cfg.php')
	{   
		if ($alert_row['number_of_errors'] > 0)
		{
			
			if ($_SESSION["device_type"] == 'Desktop') //Show Errors Only On Desktop
			{
			?>
			<script>
				alert("Cronjob Errors Occured");
	   			popup("/admin/cron_logs/cfg.php?mod=view&iframe=1&cronjob_id=-1&timestamp_start=<?php echo $alert_row['start_date'];?>&timestamp_end=<?php echo $alert_row['end_date'];?>&awaiting_action=1");
	   		</script>
	   		<?php
			}
		}
	}
	*/
	
   ?>

   <!-- <li><a href='/admin/publishers/cfg.php'>test n</a></li> -->
   </ul>
   
   <?php
   	if (!$allowed)
	{
		die('<br><br><h1><font color="red">Not Allowed!</font></h1>');
	}
   
   ?>
   

</html>