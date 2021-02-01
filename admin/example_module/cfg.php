<script>
if (typeof onload_fuctions === 'undefined') 
{
	var onload_fuctions = [];
}


function cfg_onload()
{
	
}

onload_fuctions.push(cfg_onload);

function add_button(netapi_pub_data)
{
	tbody = document.getElementsByTagName('tbody')[0];
	var new_row = document.createElement('tr');
	new_row.classList.add("network");
	new_row.classList.add("hide");
	netapi_pub_data = netapi_pub_data.replaceAll('###','\n');
	row_html = '<td><div class="form-title">API Doc: </div></td><td><textarea disabled rows="60" class="form-field-edit">'+netapi_pub_data+' </textarea></td>';
	new_row.innerHTML = row_html;
	tbody.appendChild(new_row);
	
	
	old_submit = document.getElementsByName('submit')[0];
	new_button = '<button type="button" onclick="create_network_publisher()">Network Publisher</button>';
	old_submit.parentElement.innerHTML = old_submit.parentElement.innerHTML + new_button
	
	
	

}

function create_network_publisher()
{
			$.post( "/admin/jquery/jq_post.php?form=create_network_publisher&id=<?php echo $_GET['id']; ?>&dev=<?php echo isset($_GET['dev']) ? $_GET['dev'] : 0; ?>")
  				.done(function( data ) 
				{
    				console.log(data);
					data = JSON.parse(data);
					if (data.sucssess)
					{
						var CreationOutputDiv = document.createElement("div"); 
						var beautified_output = data.row.pub_data.replace(/###/g,"\n");
						var beautified_output_html = data.row.pub_data.replace(/###/g,"<br>");
						CreationOutputDiv.innerHTML = "<pre style='color: black; background-color: white;'>"+beautified_output_html+"</pre>";
						form_con = document.getElementsByClassName('form-container')[0]
						form_con.appendChild(CreationOutputDiv);
						prompt("Please Copy And Send To Publisher", beautified_output);
					}
					else
					{
						alert("Error, See Console Log");
						alert(data.error);
					}
  				});
				
				
}

</script>
<?php
// le-sha
$show_export_excel_only = true;
// $form_method = 'post';
$new_admin_type = true;
$mod = isset($_GET['mod']) ? $_GET['mod'] : 'view';
if ($new_admin_type)
{
	session_start();
	require_once  dirname(dirname(dirname(__FILE__))). '/general/general.php';  
	$theme_path = $_SESSION["theme_path"];
	$conn = get_db_conn();
	if (!isset($conn)or $conn->connect_error) 
	{
		die("Connection failed: " . $conn->connect_error);
	}

	if (!isset($_GET['iframe']) or $_GET['iframe'] == 0)
	{
		include_once dirname(dirname(__FILE__)). '/menu/menu_2.php';
		//include_once dirname(dirname(__FILE__)). '/menu/menu.php';
	}
	else 
	{
	    echo "<LINK href='{$theme_path}' rel='stylesheet' type='text/css'>";
	}
}
$use_client_id = true;

function before_view()
{
	//echo("before");
}

function before_edit()
{
	//echo("before");
}

$fields["title"]["name"]          = 'Title';
$fields["title"]["type"]          = 'text';
$fields["title"]["input_name"]    = 'title';
$fields["title"]["requierd"]      = 1;
$fields["title"]["db_field"]      = 'title'; 
$fields["title"]['filter']['text_search'] = 'Approximate';
$fields["title"]['remove_from_uri'] = true;
$fields["title"]["edit_on_view"]  = true;
$fields["title"]["fixed_col"]       = true;

$fields["domain_id"]["name"] = "Domain";
$fields["domain_id"]["type"]          = 'text';
$fields["domain_id"]["input_name"]    = 'domain_id';
$fields["domain_id"]["db_field"]      = 'domain_id';
if ($mod == 'view')
{
    $fields["domain_id"]["drop_down"] = fetch_id_title_for_view($conn, "SELECT id, serving_location AS title
                                                                    FROM serving_domains AS sd
                                                                    WHERE sd.status = 1 AND sd.type = 'serving'");
}
elseif (isset($_GET['id']))
{
        $fields["domain_id"]["drop_down"] = fetch_id_title_for_view($conn, "SELECT id, serving_location AS title
                                                                            FROM serving_domains AS sd
                                                                            WHERE sd.status = 1 AND sd.type = 'serving' AND 
                                                                            (NOT EXISTS (
                                                                            SELECT 1
                                                                            FROM publishers AS p
                                                                            WHERE p.domain_id = sd.id) 
                                                                            OR 
                                                                            id IN (
                                                                            SELECT domain_id
                                                                            FROM publishers
                                                                            WHERE id = {$_GET['id']}))");
}
else
{
        $fields["domain_id"]["drop_down"] = fetch_id_title_for_view($conn, "SELECT id, serving_location AS title
                                                                            FROM serving_domains AS sd
                                                                            WHERE sd.status = 1 AND sd.type = 'serving' AND
                                                                            NOT EXISTS (
                                                                            SELECT 1
                                                                            FROM publishers AS p
                                                                            WHERE p.domain_id = sd.id)");

}
$fields["domain_id"]["filter"]["defualt"] = -1;
$fields["domain_id"]["link_url"] = "/admin/domains/cfg.php?mod=edit&id={id}";
$fields["domain_id"]["edit_on_view"]  = false;


$fields["publisher_platform_url"]["name"] = "Publisher Platform URL";
$fields["publisher_platform_url"]["type"]             = 'text';
$fields["publisher_platform_url"]["input_name"]   = 'publisher_platform_url';
$fields["publisher_platform_url"]["db_field"]     = 'publisher_platform_url';
$fields["publisher_platform_url"]["edit_on_view"]  = true;
$fields["publisher_platform_url"]["link_url"]      = "{id}";

$fields["username"]["name"] = "Username";
$fields["username"]["type"]          = 'text';
$fields["username"]["input_name"]   = 'username';
$fields["username"]["db_field"]     = 'username';
$fields["username"]["edit_on_view"]  = true;

$fields["password"]["name"] = "Password";
$fields["password"]["type"]             = 'text';
$fields["password"]["input_name"]   = 'password';
$fields["password"]["db_field"]     = 'password';
$fields["password"]["edit_on_view"]  = true;

$fields["contact_name"]["name"] = "Contact Name";
$fields["contact_name"]["type"]             = 'text';
$fields["contact_name"]["input_name"]   = 'contact_name';
$fields["contact_name"]["db_field"]     = 'contact_name';
$fields["contact_name"]["edit_on_view"]  = true;


$fields["contact_email"]["name"] = "Contact Email";
$fields["contact_email"]["type"]             = 'text';
$fields["contact_email"]["input_name"]   = 'contact_email';
$fields["contact_email"]["db_field"]     = 'contact_email';
$fields["contact_email"]["edit_on_view"]  = true;

$fields["contact_mobile"]["name"] = "Contact Mobile";
$fields["contact_mobile"]["type"]             = 'text';
$fields["contact_mobile"]["input_name"]   = 'contact_mobile';
$fields["contact_mobile"]["db_field"]     = 'contact_mobile';
$fields["contact_mobile"]["edit_on_view"]  = true;


$fields["contact_skype"]["name"] = "Contact Skype";
$fields["contact_skype"]["type"]             = 'text';
$fields["contact_skype"]["input_name"]   = 'contact_skype';
$fields["contact_skype"]["db_field"]     = 'contact_skype';
$fields["contact_skype"]["edit_on_view"]  = true;

$fields["notes"]["name"] = "Notes";
$fields["notes"]["type"]             = 'textarea';
$fields["notes"]["input_name"]   = 'notes';
$fields["notes"]["db_field"]     = 'notes';
$fields["notes"]["edit_on_view"]  = true;

if ($mod == 'edit')
{
$fields["group_by_fields"]["name"] 			= "Stats Group By Fields <br><b>(Expensive! Use Caution)</b>";
$fields["group_by_fields"]["type"]          	= 'text';
$fields["group_by_fields"]["input_name"]    	= 'group_by_fields';
$fields["group_by_fields"]["db_field"]      	= 'group_by_fields';
$fields["group_by_fields"]["selectbox"] = array(
                                                'city'          => 'City',
                                                'sub_id1'       => 'Sub ID1',
                                                'sub_id2'       => 'Sub ID2',
                                                'param1'        => 'Param1',
                                                'param2'        => 'Param2',
                                                'param3'        => 'Param3',
                                                'param4'        => 'Param4',
                                                'asn'           => 'ASN (Carrier)',
                                                'os_version'    => 'OS Version',
                                                'conn_type'     => 'Connection Type'
                                                );
$fields["group_by_fields"]['show_id'] 			= false;
}

$fields["postback_url"]["name"] = "Report URL";
$fields["postback_url"]["type"]             = 'text';
$fields["postback_url"]["input_name"]   = 'postback_url';
$fields["postback_url"]["db_field"]     = 'postback_url';
$fields["postback_url"]["edit_on_view"]  = true;
$fields["postback_url"]["edit_tab"]  = 'postback';

$fields["postback_percentage"]["name"] = "Report Income%";
$fields["postback_percentage"]["type"]             = 'text';
$fields["postback_percentage"]["input_name"]   = 'postback_percentage';
$fields["postback_percentage"]["db_field"]     = 'postback_percentage';
$fields["postback_percentage"]["defualt"] = 0;
$fields["postback_percentage"]["edit_on_view"]  = true;


if (in_array($_SESSION['user_id'],array(1,7,10)))
{
	$fields["postback_scrub_percentage"]["name"] = "Hide Conversion%";
	$fields["postback_scrub_percentage"]["type"]             = 'text';
	$fields["postback_scrub_percentage"]["input_name"]   = 'postback_scrub_percentage';
	$fields["postback_scrub_percentage"]["db_field"]     = 'postback_scrub_percentage';
	$fields["postback_scrub_percentage"]["defualt"] = 0;
	$fields["postback_scrub_percentage"]["edit_on_view"]  = true;
}



$fields["is_pb_wget"]["name"] = "PostBack Method";
$fields["is_pb_wget"]["type"]          = 'text';
$fields["is_pb_wget"]["input_name"]    = 'is_pb_wget';
$fields["is_pb_wget"]["db_field"]      = 'is_pb_wget';
$fields["is_pb_wget"]["drop_down"] = array(0 => 'Regular (curl)', 1=> 'Custom (wget)');
$fields["is_pb_wget"]['show_id'] 			= false;



$fields["allowed_user"]["name"] = "User";
$fields["allowed_user"]["type"]          = 'text';
$fields["allowed_user"]["input_name"]    = 'allowed_user';
$fields["allowed_user"]["db_field"]      = 'allowed_user';
$fields["allowed_user"]["selectbox"] = fetch_id_title_for_view($conn, "SELECT id, name as title FROM users WHERE status = 1");
$fields["allowed_user"]["show_id"] = true;
$fields["allowed_user"]["edit_on_view"]  = false;
$fields["allowed_user"]["edit_tab"]  = 'network';


$fields["network_tag"]["name"] = "Network Tag";
$fields["network_tag"]["type"]          = 'text';
$fields["network_tag"]["input_name"]    = 'network_tag';
$fields["network_tag"]["db_field"]      = 'network_tag';
$fields["network_tag"]["filter"]["defualt"] = -1;
$fields["network_tag"]["link_url"] = "/admin/tags/cfg.php?mod=edit&id={id}";
$fields["network_tag"]["edit_on_view"]  = true;
if (in_array($_SESSION['user_id'],array(1,7,10)))
{
    $fields["network_tag"]["drop_down"] = fetch_id_title_for_view($conn, "SELECT id, title FROM tags WHERE status = 1");
}
else
{
    $fields["network_tag"]["drop_down"] = fetch_id_title_for_view($conn, "SELECT id, title FROM tags WHERE id not in (138,150,158,161) and status = 1");
}


$fields["network_api_password"]["name"] = "Network API Password";
$fields["network_api_password"]["type"]          = 'text';
$fields["network_api_password"]["input_name"]    = 'network_api_password';
$fields["network_api_password"]["db_field"]      = 'network_api_password';
$fields["network_api_password"]["edit_on_view"]  = true;


$fields["is_affise"]["name"] = "ALAN? (affise-like api network)";
$fields["is_affise"]["type"]          = 'text';
$fields["is_affise"]["input_name"]    = 'is_affise';
$fields["is_affise"]["db_field"]      = 'is_affise';
$fields["is_affise"]["drop_down"] = array(0=>'No', 1=>'Yes');
$fields["is_affise"]["edit_on_view"]  = true;
$fields["is_affise"]['show_id'] 			= false;

if ($mod == 'edit')
{
if (in_array($_SESSION['user_id'],array(1,7,10)))
{
$fields["s2st3_publisher_spec"]["name"] = "S2ST3 Publisher Spec";
$fields["s2st3_publisher_spec"]["type"]          = 'textarea';
$fields["s2st3_publisher_spec"]["input_name"]    = 's2st3_publisher_spec';
$fields["s2st3_publisher_spec"]["db_field"]      = 's2st3_publisher_spec';
$fields["s2st3_publisher_spec"]["edit_on_view"]  = true;
$fields["s2st3_publisher_spec"]["json_viewer"]     = true;
$fields["s2st3_publisher_spec"]["edit_on_view"]  = true;
$fields["s2st3_publisher_spec"]["edit_tab"]  = 's2s';
}
}


$multi_actions_array = array();
$multi_actions_array["duplicate"] = 'Duplicate';
$multi_actions_array["export_csv"] 				= 'Export To Csv';
$multi_actions_array["multi_edit"] = 'Multi Edit';
//$multi_actions_array["disable"] = 'Disable';

function after_view()
{
	?>
<script>
	// document.getElementById("filter").style.visibility = "hidden";
</script>
<?php
}

function after_edit()
{
	$pub_id = isset($_GET['id']) ? $_GET['id'] : 0;
	
	$sql_netapi = "select CONCAT('Basic API:###',basic_api, '###------------------------------------------------############',
				  'Affise Based API:###',affise_based_api_basic,'###------------------------------------------------############',
				  'For Publishers Using Affise:###',publishers_using_affise_api) AS pub_data, 1 as ind
				  FROM (
					select p.id,concat('Admin: https://partner.le-sha.com###',
						 'User: ', u.name, '###', 
						 u.note, '###',
						 'Api Documentation: https://partner.le-sha.com/admin/api_doc/doc.php?iframe=1&u=',md5(u.id),'###'
						 'API KEY: ', network_api_password,'###'
						 ) as basic_api, 
						 concat(	'Admin: https://partner.le-sha.com###',
		 						 	'User: ', u.name, '###', 
						 			 u.note, '######',
						 			'API Instructions:###',
						 			'similar to affise api.###',
						 			'1) Use this URL: https://api-lesha.affisse.com/###',
									'2) Use This API Key:		',network_api_password, '###',
									' 	 Use These Macros:###',
									'			scidu	     -	Your Click ID###',
									'			sub_id1	  -	Your sub-ID###',
									'			idfa	     -	iOS Device ID###',
									'			androidid  -	Android ID###',
									'			app_name_s -	App Name#########'																																				
									' See API Documentation: https://partner.le-sha.com/admin/api_doc/doc.php?iframe=1&u=',md5(u.id)) AS affise_based_api_basic,
						 concat(	'Admin: https://partner.le-sha.com###',
		 						 	'User: ', u.name, '###', 
						 			 u.note, '######',
						 			'API Instructions (for Affise publishers using CPAPI):###',									
									' 1) Add demand source named TrafficMizer.###',
									' 2) Type Title: le-sha###',
									' 3) Fill The API Key: ',network_api_password, '###',
									' 4) Fill In sysid: 1###',
									' 5) Fill In scidu: {clickid}###',
									' 6) Fill In sub_id1: {pid}_{Your sub-ID}###',
									' 7) Fill In idfa: {iOS Device ID param}###',
									' 8) Fill In androidid: {Android ID param}###',
									' 9) Fill In app_name_s: {App Name param}###',
									' #########',
									' ALEX: ###',
									' ----- ###',
									' To Test The API, Check This URL - sould return offers in a json format: ###',
									' https://api.le-sha.com/network_api?api-key=',network_api_password,'&sysid=1&limit=10&PAGE=1 ###') as publishers_using_affise_api
				from publishers as p
				join users as u
				on (p.allowed_user = u.id)
				where p.id = {$pub_id} ) AS qq";
				
				
				//$sql_netapi = "SELECT * FROM publishers";
				$conn = isset($conn) ? $conn : get_db_conn();
				if ($res_netapi = $conn->query($sql_netapi))
				{
					$row_netapi=mysqli_fetch_array($res_netapi,MYSQLI_ASSOC);
					$netapi_pub_data = $row_netapi['pub_data'];
				}
				else
				{
					$netapi_pub_data = "";
				}
				
	
	echo "<script>add_button('{$netapi_pub_data}');</script>";

}

$src_table = 'publishers';
$edit_url = "publishers";
$title_to_display = 'Publishers';
//$fields = array($title, $advertiser_id,$revenue_per_conversion,$tracking_url,$preview_url,$product_type,$create_date,$last_check_date,$clicks_cap,$imps_cap,$convs_cap,$budget_cap,$budget_time_interval,$extract_type,$status,$rate);
$submit_content = "Save";

$edit_tabs = array
(
      array('class'=>'tablinks active', 'name' => 'general','title' => 'General'),
      array('class'=>'tablinks',  'name' => 'postback','title' => 'Post Back Info'),
      array('class'=>'tablinks',  'name' => 'network','title' => 'Network Info'),
      array('class'=>'tablinks',  'name' => 's2s','title' => 'S2S Spec')
); 

// Take From Local Or General Mod
if (file_exists("{$mod}.php"))
{
	include "{$mod}.php";
}
else
{
	include "/var/www/html/admin/{$mod}.php";
}





?>

