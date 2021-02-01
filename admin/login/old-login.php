<?php

if ($_SERVER['HTTP_HOST'] == "www.admin.cerberusdm.com")
{
	header('Location: http://www.admin.cerberusdigitalmedia.com');
}

session_start();
require_once  dirname(dirname(dirname(__FILE__))). '/general/general.php';  

//$login_screen = 1;
//include_once dirname(dirname(__FILE__)). '/config.php';


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


?>

   <head>
      <!--<!--<LINK href="/admin/theme/edit.css" rel="stylesheet" type="text/css">-->
      <LINK href="<?php echo $theme_path;?>" rel="stylesheet" type="text/css">
      <!--<script src="http://www.kryogenix.org/code/browser/sorttable/sorttable.js" ></script>-->
      <script src="/admin/menu/sorttable.js" ></script>
      <style>
.form-container {
     width:70%;
   }
   .submit-button {
   padding: 8.5px 0px;
   margin:10px 20px 5px 0;
   width:275px;
   }
img.logo {
	padding: 5% 5% 7% 7%;
	width:250;
	height:250;
}
.form-field {
   min-width:280px;
}

</style>
   </head>


<?php

if($_POST['submit'])
{
    //$login_screen = 1;


    $name = $_POST['name'];
    $password = $_POST['password'];

    
    //$query = "SELECT id as user_id,name as user_name,customer_id FROM users where name = '{$name}' and password = md5('$password')";
	$query = "
SELECT u.id as user_id, u.name AS user_name, u.client_id AS client_id, c.title AS client_name, u.privileges, u.user_type, c.theme_path,
u.allowed_fields as allowed_fields
FROM users as u
LEFT JOIN clients as c
on (u.client_id = c.id and u.`status` = c.`status`) 
where u.status = 1 and u.name = '{$name}' AND u.password = MD5('$password')";
//die($query);
    $result = $conn->query($query); 
    $row=mysqli_fetch_array($result,MYSQLI_ASSOC);
//	print_r($row);
//	die();
    $user_id = $row['user_id'];
	$user_type = $row['user_type'];
	$user_name = $row['user_name'];
	$client_id = $row['client_id'];
	$client_name = $row['client_name'];
	$theme_path = $row['theme_path'];
	$allowed_fields = $row['allowed_fields'];
	
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
	//print_r($menu_array);
    //session_start();
    if (isset($user_id))
    {
        $_SESSION["user_id"] = $user_id;
		$_SESSION["user_name"] = $user_name;
		$_SESSION["client_id"] = $client_id;
		$_SESSION["client_name"] = $client_name;
		$_SESSION["menu_array"] = $menu_array;
		$_SESSION["user_type"] = $user_type;
		$_SESSION["device_type"] = get_device(); 
		
		
		$network_data_sql = "
							SELECT p.id as pub_id,t.id as tag_id,sl.id as smart_link_id
							FROM publishers as p
							left join tags  as t on (p.network_tag = t.id)
							left join smart_links as sl on (t.smart_link_id = sl.id)
							WHERE allowed_user IS NOT NULL AND FIND_IN_SET({$user_id},allowed_user) > 0
							";
		$result = $conn->query($network_data_sql);
	//	print_r($result);
	//	die();
		if ($row=mysqli_fetch_array($result,MYSQLI_ASSOC))
		{
			$_SESSION["pub_id"] = $row['pub_id'];
			$_SESSION["tag_id"] = $row['tag_id'];
			$_SESSION["smart_link_id"] = $row['smart_link_id'];
		}
		
		
		//$_SESSION["theme_path"] = $theme_path;
		if ($user_type > 1)
		{
			//$_SESSION["show_imps"]    = $row['show_imps'];
			//$_SESSION["show_clicks"]  = $row['show_clicks'];
			//$_SESSION["show_convs"]   = $row['show_convs'];
			//$_SESSION["show_revenue"] = $row['show_revenue'];
			$_SESSION["allowed_fields"] = $allowed_fields;
			header('Location: /admin/partner_reports/view.php');
		}
		
	
		
		$insert_query = "INSERT INTO `user_activity` (`user_id`) VALUES ('{$user_id}');";
		//$conn->query($insert_query); 
		//die($insert_query);
        header('Location: /admin/menu/menu.php'); 
    }
    else
    {
      unset($_SESSION["user_id"]);
	  unset($_SESSION["user_name"]);
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


	
<body style="background-color: #FF942B";>
<center><form class="form-container" method="post">
<div class="data-filter">
<div class="form-title"><h2><?php echo $system_name; ?></h2></div>
<img class="logo"><br><br>
<div class="form-title">User</div>
<input class="form-field" type="text" name="name" /><br><br><br>
<div class="form-title">password</div>
<input class="form-field" type="password" name="password" /><br>
<div class="submit-container">
<center><input class="submit-button" type="submit" value="Submit" name="submit" /><center>

</div></div>
</form></center>
