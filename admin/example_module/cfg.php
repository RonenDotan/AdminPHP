<?php

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
	    include_once dirname(dirname(__FILE__)). '/menu/menu.php';
	}
	else
	{
	    echo "<LINK href='{$theme_path}' rel='stylesheet' type='text/css'>";
	}
}
 
$use_client_id = true;

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

$fields["username"]["name"] = "Username";
$fields["username"]["type"]          = 'text';
$fields["username"]["input_name"]   = 'username';
$fields["username"]["requierd"]     = 0;
$fields["username"]["db_field"]     = 'username';
$fields["username"]["edit_on_view"]  = true;


$fields["password"]["name"] = "Password";
$fields["password"]["type"]             = 'text';
$fields["password"]["input_name"]   = 'password';
$fields["password"]["requierd"]     = 0;
$fields["password"]["db_field"]     = 'password';


if ($mod != 'view')
{
    $fields["contact_name"]["name"] = "Contact Name";
    $fields["contact_name"]["type"]             = 'text';
    $fields["contact_name"]["input_name"]   = 'contact_name';
    $fields["contact_name"]["requierd"]     = 0;
    $fields["contact_name"]["db_field"]     = 'contact_name';
    $fields["contact_name"]["edit_on_view"]  = true;
	$fields["contact_name"]["edit_tab"]      = 'contact';
    
    $fields["contact_email"]["name"] = "Contact Email";
    $fields["contact_email"]["type"]             = 'text';
    $fields["contact_email"]["input_name"]   = 'contact_email';
    $fields["contact_email"]["requierd"]     = 0;
    $fields["contact_email"]["db_field"]     = 'contact_email';
    $fields["contact_email"]["edit_on_view"]  = true;
    
    $fields["contact_mobile"]["name"] = "Contact Mobile";
    $fields["contact_mobile"]["type"]             = 'text';
    $fields["contact_mobile"]["input_name"]   = 'contact_mobile';
    $fields["contact_mobile"]["requierd"]     = 0;
    $fields["contact_mobile"]["db_field"]     = 'contact_mobile';
    $fields["contact_mobile"]["edit_on_view"]  = true;
    
    $fields["contact_skype"]["name"] = "Contact Skype";
    $fields["contact_skype"]["type"]             = 'text';
    $fields["contact_skype"]["input_name"]   = 'contact_skype';
    $fields["contact_skype"]["requierd"]     = 0;
    $fields["contact_skype"]["db_field"]     = 'contact_skype';
    $fields["contact_skype"]["edit_on_view"]  = true;
}

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
$fields["allowed_serving_method"]["drop_down"] = fetch_id_title_for_view($conn,"SELECT id, title FROM serving_code where status = 1 and id != -9");;
$fields["allowed_serving_method"]["show_id"] = true;
$fields["allowed_serving_method"]["defualt"] = 1;
$fields["allowed_serving_method"]["edit_on_view"]  = true;
$fields["allowed_serving_method"]["edit_tab"]  = 'serving_info';


if ($mod != 'view')
{
if (isset($_SESSION['user_id']) and $_SESSION['user_id'] = 10)
{
    $fields["api_url"]["name"] = "API URL";
    $fields["api_url"]["type"]             = 'text';
    $fields["api_url"]["input_name"]   = 'api_url';
    $fields["api_url"]["db_field"]     = 'api_url';
    $fields["api_url"]["edit_on_view"]  = false;
	$fields["api_url"]["edit_tab"]  = 'api';
    
    $fields["api_key"]["name"] = "API Key";
    $fields["api_key"]["type"]             = 'text';
    $fields["api_key"]["input_name"]   = 'api_key';
    $fields["api_key"]["db_field"]     = 'api_key';
    $fields["api_key"]["edit_on_view"]  = false;
}
}

if ($mod != 'view')
{
$fields["api_pull_countries"]["name"] = "API Countries";
$fields["api_pull_countries"]["type"]             = 'text';
$fields["api_pull_countries"]["input_name"]   = 'api_pull_countries';
$fields["api_pull_countries"]["db_field"]     = 'api_pull_countries';
$fields["api_pull_countries"]["edit_on_view"]  = true;
$fields["api_pull_countries"]["edit_tab"]  = 'api';
}

if ($mod != 'view')
{
$fields["api_min_cpi"]["name"] = "API Min CPI";
$fields["api_min_cpi"]["type"]             = 'text';
$fields["api_min_cpi"]["input_name"]   = 'api_min_cpi';
$fields["api_min_cpi"]["db_field"]     = 'api_min_cpi';
$fields["api_min_cpi"]["edit_on_view"]  = true;
$fields["api_min_cpi"]["edit_tab"]  = 'api';
}

if ($mod != 'view')
{
$fields["crawler_enabled"]["name"]          = 'Crawler Enabled';
$fields["crawler_enabled"]["type"]          = 'text';
$fields["crawler_enabled"]["input_name"]    = 'crawler_enabled';
$fields["crawler_enabled"]["requierd"]      = 1;
$fields["crawler_enabled"]["db_field"]      = 'crawler_enabled';
$fields["crawler_enabled"]["drop_down"] = array(1=>'Active', 0=>'Not Active');
$fields["crawler_enabled"]["show_id"] = false;
$fields["crawler_enabled"]["filter"]["defualt"] = -1;
$fields["crawler_enabled"]["edit_on_view"]  = true;
$fields["crawler_enabled"]["edit_tab"]  = 'general';

$fields["crawler_conditions"]["name"] = "Crawler Conditions";
$fields["crawler_conditions"]["type"]             = 'text';
$fields["crawler_conditions"]["query"]        = '';
$fields["crawler_conditions"]["input_name"]   = 'crawler_conditions';
$fields["crawler_conditions"]["requierd"]     = 0;
$fields["crawler_conditions"]["db_field"]     = 'crawler_conditions';
$fields["crawler_conditions"]["json_viewer"]     = true;
$fields["crawler_conditions"]["edit_on_view"]  = true;
}

$fields["add_to_url"]["name"] = "URL Parameters";
$fields["add_to_url"]["type"]             = 'text';
$fields["add_to_url"]["input_name"]   = 'add_to_url';
$fields["add_to_url"]["db_field"]     = 'add_to_url';
$fields["add_to_url"]["edit_on_view"]  = true;
$fields["add_to_url"]["edit_tab"]  = 'serving_info';

if ($mod != 'view')
{
$fields["rand_subs_list"]["name"] = "Randomized Sub IDs";
$fields["rand_subs_list"]["type"]             = 'textarea';
$fields["rand_subs_list"]["query"]        = '';
$fields["rand_subs_list"]["input_name"]   = 'rand_subs_list';
$fields["rand_subs_list"]["requierd"]     = 0;
$fields["rand_subs_list"]["db_field"]     = 'rand_subs_list';
$fields["rand_subs_list"]["json_viewer"]     = true;
$fields["rand_subs_list"]["edit_tab"]  = 'serving_info';
}



$fields["pixel_ttl"]["name"]          = 'Repixel Products<br><font size=\'1\'>(for tags with active FingerPrint To Product)</font>';
$fields["pixel_ttl"]["type"]          = 'text';
$fields["pixel_ttl"]["input_name"]    = 'pixel_ttl';
$fields["pixel_ttl"]["requierd"]      = 1;
$fields["pixel_ttl"]["db_field"]      = 'pixel_ttl';
$fields["pixel_ttl"]["location"] = 1;
$fields["pixel_ttl"]["drop_down"] = array(0=>'No TTL', 3600=>'1 Hour', 18000=>'5 Hours', 36000=>'10 Hours', 86400=>'24 Hours',172800=>'2 Days',259200=>'3 Days',432000=>'5 Days',604800=>'7 Days');
$fields["pixel_ttl"]["show_id"] = false;
$fields["pixel_ttl"]["edit_on_view"]  = true;
$fields["pixel_ttl"]["edit_tab"]  = 'serving_info';

$fields["clicks_cap"]["name"]                   = "Clicks Cap";
$fields["clicks_cap"]["type"]                   = 'text';
$fields["clicks_cap"]["input_name"]             = 'clicks_cap';
$fields["clicks_cap"]["db_field"]               = 'clicks_cap';
$fields["clicks_cap"]["edit_on_view"]           = true;

$fields["click_cap_timeframe"]["name"]          = "Clicks Cap TimeFrame";
$fields["click_cap_timeframe"]["type"]          = 'text';
$fields["click_cap_timeframe"]["input_name"]    = 'click_cap_timeframe';
$fields["click_cap_timeframe"]["drop_down"]     = array(0=>'No TTL', 3600=>'1 Hour', 43200=>'12 Hours', 86400=>'24 Hours');
$fields["click_cap_timeframe"]["show_id"]       = false;
$fields["click_cap_timeframe"]["db_field"]      = 'click_cap_timeframe';
$fields["click_cap_timeframe"]["edit_on_view"]  = true;

if ($mod != 'view')
{
$fields["click_balancing_enabled"]["name"]          = "Click Balancing";
$fields["click_balancing_enabled"]["type"]          = 'text';
$fields["click_balancing_enabled"]["input_name"]    = 'click_balancing_enabled';
$fields["click_balancing_enabled"]["drop_down"]     = array(0=>'Disabled', 1=>'Enabled');
$fields["click_balancing_enabled"]["show_id"]       = false;
$fields["click_balancing_enabled"]["db_field"]      = 'click_balancing_enabled';
$fields["click_balancing_enabled"]["edit_on_view"]  = true;
}


$fields["offers_platform"]["name"]          = 'Offers Platform';
$fields["offers_platform"]["type"]          = 'text';
$fields["offers_platform"]["input_name"]    = 'offers_platform';
$fields["offers_platform"]["requierd"]      = 1;
$fields["offers_platform"]["db_field"]      = 'offers_platform';
$fields["offers_platform"]['filter']['text_search'] = "Approximate";
$fields["offers_platform"]["edit_on_view"]  = true;
$fields["offers_platform"]["fixed_col"]       = true;
$fields["offers_platform"]["edit_tab"]  = 'api';

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

$src_table = 'advertisers';
$edit_url = "advertisers";
$title_to_display = 'Advertisers';
//$fields = array($title, $advertiser_id,$revenue_per_conversion,$tracking_url,$preview_url,$product_type,$create_date,$last_check_date,$clicks_cap,$imps_cap,$convs_cap,$budget_cap,$budget_time_interval,$extract_type,$status,$rate);
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
