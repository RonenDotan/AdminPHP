<?php

$search_list_id = true;
$show_export_excel_only = true;
$new_admin_type = true;
$use_client_id = true;
$mod = isset($_GET['mod']) ? $_GET['mod'] : 'view';

if (true)
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
	    include_once dirname(dirname(__FILE__)). '/menu/menu.php';
	}
	else
	{
	    echo "<LINK href='{$theme_path}' rel='stylesheet' type='text/css'>";
	}
}
 


// Use to display fields and order them
if ($mod == 'view')
{
    $fields["title"]=array();
    $fields["enabled"] = array();
    $fields["advertiser_platform_url"] = array();
    $fields["username"] = array();
    $fields["password"] = array();
    $fields["add_to_url"] = array();
    $fields["clicks_cap"] = array();
    $fields["click_cap_timeframe"] = array();
    $fields["pixel_ttl"] = array();
    $fields["allowed_serving_method"] = array();
    $fields["offers_platform"] = array();
    $fields["notes"] = array();
    $fields["password"] = array();
php -a
}


$fields["title"]["name"]          = 'Title';
$fields["title"]["type"]          = 'text';
$fields["title"]["input_name"]    = 'title';
$fields["title"]["requierd"]      = 1;
$fields["title"]["db_field"]      = 'title'; 
$fields["title"]['filter']['text_search'] = "Approximate";
$fields["title"]["edit_on_view"]  = true;
$fields["title"]["fixed_col"]     = true;
$fields["title"]["edit_tab"]      = 'general';


$fields["advertiser_platform_url"]["name"] = "Advertiser Platform URL";
$fields["advertiser_platform_url"]["type"]             = 'text';
$fields["advertiser_platform_url"]["input_name"]   = 'advertiser_platform_url';
$fields["advertiser_platform_url"]["requierd"]     = 0;
$fields["advertiser_platform_url"]["db_field"]     = 'advertiser_platform_url';
$fields["advertiser_platform_url"]["edit_on_view"]  = true;
$fields["advertiser_platform_url"]["link_url"]      = "{id}";


if (true)
{
$fields["notes"]["name"] = "Notes";
$fields["notes"]["type"]             = 'textarea';
$fields["notes"]["input_name"]   = 'notes';
$fields["notes"]["requierd"]     = 0;
$fields["notes"]["db_field"]     = 'notes';
$fields["notes"]["edit_on_view"]  = true;
$fields["notes"]["edit_tab"]      = 'general';
}

$fields["enabled"]["name"]          = 'Status';
$fields["enabled"]["type"]          = 'text';
$fields["enabled"]["input_name"]    = 'enabled';
$fields["enabled"]["requierd"]      = 1;
$fields["enabled"]["db_field"]      = 'enabled'; 
$fields["enabled"]["drop_down"] = array(1=>'Active', 0=>'Not Active');
$fields["enabled"]["show_id"] = false;
$fields["enabled"]["filter"]["defualt"] = 1;
$fields["enabled"]["edit_on_view"]  = true;


$fields["allowed_serving_method"]["name"] = "Allowed Serving Method";
$fields["allowed_serving_method"]["type"]             = 'text';
$fields["allowed_serving_method"]["input_name"]   = 'allowed_serving_method';
$fields["allowed_serving_method"]["requierd"]     = 1;
$fields["allowed_serving_method"]["db_field"]     = 'allowed_serving_method';
$fields["allowed_serving_method"]["drop_down"] = fetch_id_title_for_view($conn,"SELECT id, title FROM table where status = 1 ");;
$fields["allowed_serving_method"]["show_id"] = true;
$fields["allowed_serving_method"]["defualt"] = 1;
$fields["allowed_serving_method"]["edit_on_view"]  = true;
$fields["allowed_serving_method"]["edit_tab"]  = 'serving_info';


if ($mod != 'view')
{
if (isset($_SESSION['user_id']) and $_SESSION['user_id'] = 10)
{
	// Filter by user
}


$fields["json_field"]["name"] = "Json Field";
$fields["json_field"]["type"]             = 'text';
$fields["json_field"]["query"]        = '';
$fields["json_field"]["input_name"]   = 'json_field';
$fields["json_field"]["requierd"]     = 0;
$fields["json_field"]["db_field"]     = 'json_field';
$fields["json_field"]["json_viewer"]     = true;
$fields["json_field"]["edit_on_view"]  = true;


if ($mod != 'view')
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
$fields["group_by_fields"]["edit_tab"]  = 'general';
}

$multi_actions_array = array();
$multi_actions_array["duplicate"] = 'Duplicate';
$multi_actions_array["enable"] = 'Enable';
$multi_actions_array["disable"] = 'Disable';
$multi_actions_array['multi_edit'] = 'Multi Edit';
$multi_actions_array["generate_list"] 			    = 'Generate List';
$multi_actions_array["export_csv"] 	= 'Export To Csv';

function before_view()
{

}

function after_edit()
{
}

$edit_tabs = array
(
      array('class'=>'tablinks active', 'name' => 'general','title' => 'General'),
      array('class'=>'tablinks',  'name' => 'contact','title' => 'Contact'),
      array('class'=>'tablinks',  'name' => 'serving_info','title' => 'Serving Behaviour'),
      array('class'=>'tablinks',  'name' => 'api','title' => 'API')
); 

$src_table = 'table_name';
$edit_url = "table_name";
$title_to_display = 'Table Name';
$submit_content = "Save";



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
