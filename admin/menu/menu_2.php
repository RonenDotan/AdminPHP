<?php
// die("maintaince");
// unset($_SESSION);
// echo "\n\n\n\n\n" . $_SESSION['user_id'] . "\n\n\n\n\n";

if (isset($new_admin_type) and $new_admin_type)
{
    echo "<LINK href='{$theme_path}' rel='stylesheet' type='text/css'>";
	// New way - already defined in CFG
	?>
	<script id='script_jquery' src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.1/jquery.min.js"></script>
	<?php
}
else
{ 
	// Old way
	//echo "old type";
			if (session_status() == PHP_SESSION_NONE) 
		{
			session_start();
		}
		$theme_path = $_SESSION["theme_path"];
	echo "<LINK href='{$theme_path}' rel='stylesheet' type='text/css'>";
	require_once  dirname(dirname(dirname(__FILE__))). '/general/general.php';  
	
	


	
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
		  <script type="text/javascript" src="/admin/menu/tablesorter-master/dist/js/jquery.tablesorter.min.js"></script>
		  <script type="text/javascript" src="/admin/menu/tablesorter-master/dist/js/jquery.tablesorter.widgets.js"></script>
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
	echo "<div style='color: red;z-index: 8000; font-size: 18px;margin: 30px 0px 0px 230px;font-weight: bold;text-align: center;width: fit-content;'>You Need To Re-Login, Then Save the Changes again for it to take affect</div>"; 
	echo "<script>window.open('/admin/login/login.php?close=1','Please Re-Login','_blank,directories=0,titlebar=0,toolbar=0,location=0,status=0,menubar=0,scrollbars=no,resizable=no,      width=500,height=700');</script>";																				  
}
else
{
	apache_note('user_id', $_SESSION["user_id"]);
	apache_note('user_name', $_SESSION["user_name"]);
}

?>

      <style>
li {
    float: left;
}



li a, .dropbtn {
    display: inline-block;
    color: #989191;
    padding: 10px 16px;
	text-decoration: none;

}

.form-container
{
	margin-left: 15px;
	margin-top: 15px;
	transition: all 0.4s ;
	
}

li a:hover, .dropdown:hover .dropbtn {
    color : white;
}

li.dropdown {
    display: inline-block;
}

.dropdown-content {
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

.accordion-top
{
	width: fit-content;
	height: fit-content;
	margin: 0px;
	padding: 0px;
	background-color: transparent;
}

.accordion-side
{
	width: fit-content;
    height: fit-content;
    margin: 0px;
    padding: 0px;
    background-color: black;
}

.dropdown-content a:hover {background-color: #fff2bf}

.dropdown:focus .dropdown-content {
    display: block;
}

.accordion-inbutton-container
{
	display: inline-flex;
	width: -webkit-fill-available;
}

.accordion_title
{
	width: 95%;
}

.accordion_indicator
{
	width: 5%;
}


button.accordion {
    background-color: #212529;
    color: #989191;
    cursor: pointer;
    padding: 10px;
    width: 100%;
    text-align: left;
    outline: none;
    transition: all 0.4s;
	border:none;
}



button.accordion.active, button.accordion:hover {
    color: white;

}


.sub-menu
{
	color: #989191;
    text-decoration: none;
    padding: 10px 0px;
	font: 200 13.3333px Arial;
	display: none;
}

.sub-menu.active, .sub-menu:hover {
    color: white;

}

div.panel {
    padding: 0 0 0 20px;
    display: inline-grid;
    background-color: #343a40;
	max-height: 0px;
	width: -webkit-fill-available;
	transition: all 0.4s;
}

div.panel.show {
	max-height: 500px;
}

.div-menu-side
{ 
	position: fixed;
    top: 0;
	margin-top: 15px;
    background: #212529;
	color: #212529;
	height: 100%;
	overflow: auto;
	display: flex;
	width: 215px;
	transition: all 0.4s;
}

img.logo
{
	width: 50px;
    height: 50px;
	content: url(/admin/theme/images/Lesha_logo_s_dark.png);
}

.main-level-link
{
	display: inline-flex;
	width: -webkit-fill-available;
	padding: 0px;
	font: 400 13.3333px Arial;
	text-decoration: none;
	color : #989191;
}

.main-level-link:hover
{
	color : white;
}

.top_nav_container
{
	position: fixed;
    width: 100%;
    z-index: 500;
    background-color: rgb(52, 58, 64);
    transition: all 0.4s;
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
	<div id="top_nav_container" class="top_nav_container" style="top: -50px;">
		<div id="top_nav" style="display:inline-flex; color: white;   width: 100%; height:50px">
			<a href="https://www.le-sha.com/" ><img class="logo"></a>
			<h1 style="width: -webkit-fill-available;padding: 0px 20px;margin: 0px; align-self: center;">Le-Sha Media</h1>
			<div style="align-self: center;">
				<div onclick="accordion_manual(this);" style="padding-right: 20px;">▼<?php echo $_SESSION['user_name']; ?></div>
				<div class="dropdown-content" hidden>
				<a href="" oncontextmenu="return false;"><font>Profile</font></a>
				<a href="/admin/login/login.php" oncontextmenu="javascript:window.open('/admin/login/login.php', '_blank');return false;"><font>Logout</font></a>
				</div>
			</div>
		</div>
		<center style="border-top: 0.1px #212529 solid;"><button class="accordion-top" onclick="hide_show_top_nav()"><img src="/admin/theme/images/enlarge-minimize.PNG" style="    margin: 0px;    padding: 0;    height: 15px;	"></button>	</center>
		
	</div>
		
	
   <div class="form-container-menu" style="display: flex;">
   <div class="div-menu-side" id='side_container' style="left: -195px;;">
   <div id="side_nav" style="height: 100%;width: 200px;border-right: 0.1px #343a40 solid;">
   <?php
	
	$log_entery = "\nUSER=>". $_SESSION['user_id'] ."   URI=>".$_SERVER['REQUEST_URI'] . "   TIME=>".date('Y-m-d h:i');
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
			if (true)
			{
				echo '<button class="accordion"><div class="accordion-inbutton-container"><div class="accordion_title">'.$menu_item['title'].'</div><div class="accordion_indicator">&#x25BC;</div></div></button><div class="panel">';
			}
			else
			{
				echo '<button class="accordion">'.$menu_item['title'].'</button> <div class="panel">';
			}
			foreach ($menu_item['children'] as $menu_item_child)
			{
				if ($menu_item_child['title'] == "Cron Log" and $alert_row['number_of_errors'] > 0)
				{
					echo "<a href='{$menu_item_child['href']}&id={$alert_row['ids_to_inform']}' class='sub-menu'>{$menu_item_child['title']} &nbsp`;<font color='red' size='2'>{$alert_row['number_of_errors']}</font></a>";
				} // $alert_row['ids_to_inform']
				else
				{
					echo "<a href='{$menu_item_child['href']}' class='sub-menu' oncontextmenu='javascript:window.open(\"{$menu_item_child['href']}\", \"_blank\");return false;'>{$menu_item_child['title']}</a>";
				}
				if ($menu_item_child['module'] == $curr_module)
				{
					$allowed = true;
				} 
			}
			echo "</div>";
			// echo "</li>";
		}
		else
		{
			if ($menu_item['title'] == 'Messages')
			{
				if ($alert_row['number_of_errors'] > 0)
				{
					echo "<div style='display: inline-flex; padding: 10px;    width: -webkit-fill-available;'><a class='main-level-link' href='{$menu_item['href']}' oncontextmenu='javascript:window.open(\"{$menu_item['href']}\", \"_blank\");return false;'><div color='red'>Cronjob Logs</div></a><a class='main-level-link' href='{$menu_item['href']}&id={$alert_row['ids_to_inform']}' oncontextmenu='javascript:window.open(\"{$menu_item['href']}&id={$alert_row['ids_to_inform']}\", \"_blank\");return false;'><div color='red' style='color: red; border: red solid 1px;border-radius: 50px;margin-left: 5px;padding: 2px;'>{$alert_row['number_of_errors']}</div></a></div>";
				} // $alert_row['ids_to_inform']
				else
				{
					echo "<div style='padding: 10px;    width: -webkit-fill-available;'><a class='main-level-link' href='{$menu_item['href']}'>Cronjob Logs</a></div>";
				}
			}
			else
			{
				echo "<div style='padding: 10px;    width: -webkit-fill-available;'><a class='main-level-link' href='{$menu_item['href']}' oncontextmenu='javascript:window.open(\"{$menu_item['href']}\", \"_blank\");return false;'>{$menu_item['title']}</a></div>";
			}
			
			if ($menu_item['module'] == $curr_module)
			{
				$allowed = true;
			} 
		}
	}


	
   ?>

   <!-- <li><a href='/admin/publishers/cfg.php'>test n</a></li> -->
   </div>   <!-- side nav end-->
   
   <center style="border-top: 0.1px #212529 solid; align-self: center;"><button class="accordion-side" onclick="hide_show_side_nav()"><img src="/admin/theme/images/enlarge-minimize-side.PNG" style="    margin: 0px;    padding: 0;    height: 15px;	"></button>	</center>
   
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

var acc = document.getElementsByClassName("accordion");
var i;

if (false)
{
	for (i = 0; i < acc.length; i++) 
	{
		acc[i].onclick = function()
		{ 
			for (i = 0; i < acc.length; i++) 
			{
				if (acc[i] != this)
				{
					acc[i].nextElementSibling.style.display = "none";
				}
			}
			
			if (this.nextElementSibling.style.display == "none")
			{
				this.nextElementSibling.style.display = "grid"
			}
			else
			{
				this.nextElementSibling.style.display = "none"
			}
		}
	}
}
else
{
	for (i = 0; i < acc.length; i++) 
	{
		acc[i].onclick = function()
		{ 
			console.log(this);
			for (i = 0; i < acc.length; i++) 
			{
				if (acc[i] != this)
				{
					
					acc[i].getElementsByClassName('accordion_indicator')[0].innerHTML = "&#x25BC;";
					acc[i].nextElementSibling.classList.remove("show")
					var sub_menu_items = acc[i].nextElementSibling.children;
					for (j = 0; j < sub_menu_items.length; j++) 
					{
						sub_menu_items[j].style.display = "none"
					}
				}
			}
			
			var next_sub_menu = this.nextElementSibling;
			var sub_menu_items = next_sub_menu.children;
			if (next_sub_menu.classList.contains("show"))
			{
				next_sub_menu.classList.remove("show")
				this.getElementsByClassName('accordion_indicator')[0].innerHTML = "&#x25BC;";
				for (i = 0; i < sub_menu_items.length; i++) 
				{
					sub_menu_items[i].style.display = "none"
				}
			}
			else
			{
				next_sub_menu.classList.add("show")
				this.getElementsByClassName('accordion_indicator')[0].innerHTML = "&#x25B2;";
				for (i = 0; i < sub_menu_items.length; i++) 
				{
					sub_menu_items[i].style.display = "block"
				}
			}
		}
	}
}


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



	// setTimeout(function(){hide_show_side_nav(); }, 100);



function menu_on_load()
{

}
onload_fuctions.push(menu_on_load);
// check out https://www.w3schools.com/howto/howto_js_navbar_hide_scroll.asp


		function accordion_manual(element)
		{
			element.nextElementSibling.hidden = !element.nextElementSibling.hidden;
		}
		
		function hide_show_top_nav()
		{
			if (true)
			{
				if (top_nav_container.style.top == "-50px")
				{
					// now it is hidden - need to show
					top_nav_container.style.top = "0px";
					side_container.style.marginTop = "65px"
					document.getElementsByClassName('form-container')[0].style.marginTop = "65px"				
				}
				else
				{
					// now it is shown - need to hide.
					top_nav_container.style.top = "-50px";
					side_container.style.marginTop = "15px"
					document.getElementsByClassName('form-container')[0].style.marginTop = "15px"
				}
			}
			
		}
		
		function hide_show_side_nav()
		{
			if (side_container.style.left == "-195px")
			{
				side_container.style.left = "0px";
				document.getElementsByClassName('form-container')[0].style.marginLeft = "215px"				
			}
			else
			{
				side_container.style.left = "-195px"
				document.getElementsByClassName('form-container')[0].style.marginLeft = "15px"
			}
			
		}


</script>