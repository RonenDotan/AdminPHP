<?php
//die("FIX BUG");
unset($_SESSION);
session_start();
$login_screen = 1;
require_once  dirname(dirname(dirname(__FILE__))). '/general/general.php';

$conn = get_db_conn();
if (!isset($conn)or $conn->connect_error)
{
    die("Connection failed: " . $conn->connect_error);
}
$query = "SELECT system_name,theme_path FROM clients WHERE admin_domain = '{$_SERVER['HTTP_HOST']}'";
$result = $conn->query($query); 
$row=mysqli_fetch_array($result,MYSQLI_ASSOC); 
$theme_path = $row['theme_path'];
$system_name = $row['system_name'];
$_SESSION["theme_path"] = $theme_path;
//echo $_SERVER['HTTP_HOST'];
 
// die('\nEND');
?>

   <head>
      <!--<!--<LINK href="/admin/theme/edit.css" rel="stylesheet" type="text/css">-->
      <LINK href="<?php echo $theme_path;?>" rel="stylesheet" type="text/css">
	  <style>
	  img.login-logo {
	padding: 10% 0% 0% 0%;
	width:250px;
	height:250px;

}
.data-main-div {
	display: inline-grid;
}

	  </style>
   </head>


<?php

$_POST['name'] = isset($_GET['name']) ? $_GET['name'] : $_POST['name'];
$_POST['password'] = isset($_GET['password']) ? $_GET['password'] : $_POST['password'];
$name = $_POST['name'];
$password = $_POST['password'];

// $password = isset($_POST['password']) ? $_POST['password'] :     isset($_GET['password']) ? $_GET['password'] : '';
if($_POST['submit'])
{
    $login_screen = 1;

    
    //$query = "SELECT id as user_id,name as user_name,customer_id FROM users where name = '{$name}' and password = md5('$password')";
	$query = "
SELECT u.id as user_id, u.name AS user_name, u.client_id AS client_id, c.title AS client_name, u.privileges, u.user_type, c.theme_path,
u.allowed_fields as allowed_fields
FROM users as u
LEFT JOIN clients as c
on (u.client_id = c.id and u.`status` = c.`status`) 
where u.status = 1 and u.name = '{$name}' AND u.password = MD5('$password')";
// die($query);
    $result = $conn->query($query); 
    $row=mysqli_fetch_array($result,MYSQLI_ASSOC);
	// print_r($row);
	

    $user_id = $row['user_id'];
	$user_type = $row['user_type'];
	$user_name = $row['user_name'];
	$client_id = $row['client_id'];
	$client_name = $row['client_name'];
	$theme_path = $row['theme_path'];
	$allowed_fields = $row['allowed_fields'];
	$privileges = $row['privileges'];
	
	$query = "
	SELECT *
	FROM menu_items
	WHERE (find_in_set(id,'{$row['privileges']}') > 0 or {$client_id} = 0)
	and status=1
	ORDER BY parent_id, order_loc
	";

	$result = $conn->query($query);
	
	$menu_array = array(); 	
	while ($row=mysqli_fetch_array($result,MYSQLI_ASSOC)) 
	{
		$module = str_replace('/admin/','',$row['href']);
		$module = substr($module,0,strpos($module,"/"));
		if (!isset($menu_array[$row['parent_id']]))
		{
		    $menu_array[$row['id']] = array('href' => $row['href'], 'title' => $row['title'], 'module' => $module, 'visible'=> $row['visible']);
		}
		else
		{
		    $menu_array[$row['parent_id']]['children'][$row['id']] = array('href' => $row['href'], 'title' => $row['title'],'module' => $module, 'visible'=> $row['visible']);
		}
	}


    if (isset($user_id))
    {
		$_SESSION["user_id"] = $user_id;
		apache_note('user_id', $user_id);
		$_SESSION["user_name"] = $user_name;
		apache_note('user_name', $user_name);
		$_SESSION["client_id"] = $client_id;
		$_SESSION["client_name"] = $client_name;
		$_SESSION["menu_array"] = $menu_array;
		$_SESSION["user_type"] = $user_type;
		$_SESSION["device_type"] = get_device(); 
		
		
		$network_data_sql = "
							SELECT p.id as pub_id,t.id as tag_id,sl.id as smart_link_id, t.clicks_distribution as tags_clicks_distribution, sl.clicks_distribution  as sl_clicks_distribution
							FROM publishers as p
							left join tags  as t on (p.network_tag = t.id)
							left join smart_links as sl on (t.smart_link_id = sl.id)
							WHERE allowed_user IS NOT NULL AND FIND_IN_SET({$user_id},allowed_user) > 0
							";
		$result = $conn->query($network_data_sql);
		//print_r($result);
	//	die();
	
		
		
		
		if ($row=mysqli_fetch_array($result,MYSQLI_ASSOC))
		{
		    echo "<pre>";
		    //print_r($row);
		    $_SESSION["pub_id"] = $row['pub_id'];
			$_SESSION["tag_id"] = $row['tag_id'];
			$_SESSION["smart_link_id"] = $row['smart_link_id'];
			
			
			$clicks_distribution = json_decode($row['tags_clicks_distribution'],true);
			if (!isset($clicks_distribution[0]))
			{
			    $clicks_distribution = json_decode($row['sl_clicks_distribution'],true);
			}
			//print_r($clicks_distribution);
			//die();
			if (isset($clicks_distribution[0]))
			{
			    foreach ($clicks_distribution as $order)
			    {
			        if ($order['order'] == 'redirect')
			        {
			            $_SESSION["group_id"] = $order['groups'][0]['group'];
			            break;
			        }
			    }
			}
			
			//print_r($_SESSION);
			//die();
		}
		//$_SESSION["device_type"] = get_device(); 	

			//	die('\n\n\nghhhhg');		
		//$_SESSION["theme_path"] = $theme_path;
		
		switch($user_type)
		{
		    case 0: // master user - me, alex.
		        if (in_array(76,explode(",",$privileges)))
		        {
		            $redirect_url = '/admin/dashboard/dashboard.php';
		        }
		        else
		        {
		            $redirect_url = '/admin/menu/menu.php';
		        }
		        break;
		    case 1: // undefined
		        $redirect_url = '/admin/menu/menu.php';
		        break;
		    case 2:
		        $_SESSION["allowed_fields"] = $allowed_fields;
		        $redirect_url = '/admin/partner_reports/view_graph.php';
		        break;
		    case 3:
		        break;
		    case 4:
		        break;
		    default:
		        $redirect_url = '/admin/menu/menu.php';
		}
	
	
		echo "redirect_url : {$redirect_url}\n";
		echo "CLOSE: " . $_GET['close'] ."\n";
		$insert_query = "INSERT INTO `user_activity` (`user_id`) VALUES ('{$user_id}');";
		//$conn->query($insert_query); 
		//die($insert_query);
		if (false or !isset($_GET['close']) or $_GET['close'] != 1)
		{
		    header("Location: {$redirect_url}");
		}
		else 
		{
		    echo 	"<script>
						if (window.opener != undefined)
						{
							window.opener.location.reload(true); 
							window.close();
						}
						else
						{
							window.location.replace('{$redirect_url}');
						}
					</script>";
		}
    }
    else
    {
	  unset($_SESSION["user_id"]);
	  apache_note('user_id', '');
	  unset($_SESSION["user_name"]);
	  apache_note('user_name', '');
	  unset($_SESSION["client_id"]);
	  unset($_SESSION["client_name"]);
	  unset($_SESSION["menu_array"]);
	  unset($_SESSION["user_type"]);
	  unset($_SESSION["theme_path"]);
	  unset($_SESSION["device_type"]);
    }
	

}

function get_device()
{
    require_once  dirname(dirname(dirname(__FILE__))). '/general/mobile_detect.php';
    //    require_once    dirname(__FILE__) . "/mobile_detect.php";
    $detect = new mobile_detect;
    $device = $detect->get_device();
    unset ($detect);
    return ($device);
}
?>

<div class="div-menu" style="background-color: white;">
    <table style="width: 100%;"><tbody><tr><td><a href="https://www.le-sha.com/"><img class="logo" style="padding-left: 20px;"></a></td><td style="width: 90%;"></td></tr></tbody></table>
</div>
<form class="form-container" method="post">
<center>

<div class='data-main-div'>
<img class="logo login-logo"><br><br>
<div class="form-title"><center><h1><?php echo $system_name; ?></h1></center></div>
<table>
<tr><td><div class="form-title">User</div></td><td><input class="form-field-edit" type="text" name="name" value='<?php echo $name; ?>' style="width: 100%;"/></td></tr>
<tr><td><div class="form-title">Password</div></td><td><input class="form-field-edit" type="password" name="password" value='<?php echo $password; ?>' style="width: 100%;"/></td></tr>
</table>
<br>
<input class="submit-button" type="submit" value="Sign In" name="submit" /><tr>
</div>
</center>
</form>
