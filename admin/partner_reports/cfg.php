<?php
include_once dirname(dirname(__FILE__)) . '/menu/menu.php';

//echo "<pre>";
//print_r($_SESSION);
//echo "</pre>";
$curr_user = $_SESSION['user_id'];



function before_view() {
	//echo("before");
}

function before_edit() {
	//echo("before");
}

// $use_client_id = true;

$group_by_array = array(
						'imp_date' 				=> 'Imp Date', 
						'imp_hour' 				=> 'Hour', 
						'publisher_id' 			=> 'Publisher',
						'tag_id'				=> 'Tag',
						'country' 				=> 'Country',
						'device_id' 			=> 'Operating System',
						'advertiser_id' 		=> 'Advertiser', 
						'product_id' 			=> 'Product',
						'sub_id1' 				=> 'Sub ID1',
                        'smart_link_id' 		=> 'Smart Link',
                        'sl_rule_id'			=> 'Smart Link Rule',
                        'group_id'				=> 'Group',
                        'sub_id2' 				=> 'Sub ID2',								
                        'block_types'			=> 'Block Reason',
                        'redirect'              => 'Click Type',
						'city' 					=> 'City',
                        'conn_type'				=> 'Connection Type',
						'asn'					=> 'Carrier',
                        'os_version'			=> 'OS Version',
						'param1' 				=> 'Param1',
						'param2' 				=> 'Param2',
						'param3' 				=> 'Param3',
						'param4' 				=> 'Param4',
						/*'creative_id'			=> 'Creative',*/
						);
						
if (isset($_SESSION['user_type']) and $_SESSION['user_type'] > 1)
{
	$allowed_fields = explode(",",$_SESSION['allowed_fields']);
	//echo "\n\nALLOWED FIELDS:\n";
	//print_r($allowed_fields);
	foreach ($group_by_array as $k=>$v)
	{
		if (!in_array($k, $allowed_fields))
		{
			unset($group_by_array[$k]);
		}
	}	
}
else
{
	$allowed_fields = array('imp_date','imp_hour','publisher_id','tag_id','country','city',
							'asn','device_id','os_version','sub_id1','sub_id2','advertiser_id','product_id',
							'smart_link_id','conn_type','creative_id','sl_rule_id',
							'param1','param2','param3','param4',
	                        'block_types','redirect','group_id',
							'imps','clicks','ctr','convs','real_convs','cr',
							'income','real_income','epi','real_epi','epc',
							'real_epc','unique_users','timelag','cost_on_imps'
							);
}

//echo "\n\ngroup_by_array:\n";
//print_r($group_by_array);
						//die();

	if (true)
	{
		$fields["conv_date_ind"]["name"] = "Use Conversion Date<br>For Convs";
		$fields["conv_date_ind"]["type"] = 'text';
		$fields["conv_date_ind"]["input_name"] = 'conv_date_ind';
		$fields["conv_date_ind"]["checkbox"] = array( 0 => 0,1=> 1);
		$fields["conv_date_ind"]["filter"]["defualt"] = 1;
		if (in_array($curr_user,array(1,7,10))) {$fields["conv_date_ind"]["filter"]["defualt"] = 0;}
		$fields["conv_date_ind"]["show_id"] = false;
	
	}	



	if (true)
	{
		$fields["use_cache"]["name"] = "Use Cached Stats";
		$fields["use_cache"]["type"] = 'text';
		$fields["use_cache"]["input_name"] = 'use_cache';
		$fields["use_cache"]["checkbox"] = array( 0 => 0,1=> 1);
		$fields["use_cache"]["filter"]["defualt"] = 1;
		// if (in_array($curr_user,array(1,7,10))) {$fields["use_cache"]["filter"]["defualt"] = 0;}
		$fields["use_cache"]["show_id"] = false;
	
	}	


					

$fields["group_by"]["name"] = "<b>Group</b>";
$fields["group_by"]["type"] = 'text';
$fields["group_by"]["input_name"] = 'group_by';
$fields["group_by"]["requierd"] = 1;
$fields["group_by"]["drop_down"] = $group_by_array;
$fields["group_by"]["filter"]["defualt"] = 'date';
$fields["group_by"]["show_id"] = false;
$fields["group_by"]["new_filter_row"] = true;


if (isset($group_by_array["imp_date"]))
{
	$fields["imp_date"]["name"] = "Imp Date";
	$fields["imp_date"]["type"] = 'text';
	$fields["imp_date"]["input_name"] = 'imp_date';
	$fields["imp_date"]["requierd"] = 1;
	$fields["imp_date"]["db_field"] = 'imp_date';
	//$fields["imp_date"]["drop_down"] = $report_type_array;
	$fields["imp_date"]["filter"]["date"] = true;
}

if (isset($group_by_array["imp_hour"]))
{
	$fields["imp_hour"]["name"] = "Hour";
	$fields["imp_hour"]["type"] = 'text';
	$fields["imp_hour"]["filter_type"] = 'number';
	$fields["imp_hour"]["input_name"] = 'imp_hour';
	$fields["imp_hour"]["requierd"] = 1;
	$fields["imp_hour"]["db_field"] = 'imp_hour';
	$fields["imp_hour"]["drop_down"] = array(0 => 0, 1 => 1, 2 => 2, 3 => 3, 4 => 4, 5 => 5, 6 => 6, 7 => 7, 8 => 8, 9 => 9, 10 => 10, 11 => 11, 12 => 12, 13 => 13, 14 => 14, 15 => 15, 16 => 16, 17 => 17, 18 => 18, 19 => 19, 20 => 20, 21 => 21, 22 => 22, 23 => 23);
	$fields["imp_hour"]["filter"]["defualt"] = -1;
	$fields["imp_hour"]["show_id"] = false;
}







// publisher account - handle allowed publishers
if (isset($_SESSION['user_type']) and $_SESSION['user_type'] == 2)
{
	$income_title = "Income";
	// select all publishers with this user
	$publisher_sql_str = "SELECT id,title FROM publishers where allowed_user is not null and find_in_set({$curr_user},allowed_user) > 0";
}
else
{
	$income_title = "Cost";	
	// select all publishers with this user
	$publisher_sql_str = "select id,title from publishers ";
	if (in_array($_SESSION['user_id'],array(1,7,10))){} else 
	{
	    $publisher_sql_str = "select id,title from publishers where id not in (4417)";
	}
}
$publishers_array = fetch_id_title_for_view($conn,$publisher_sql_str);
if (isset($_SESSION['user_type']) and $_SESSION['user_type'] == 2)
{
	$publisher_sql_restriction = " publisher_id in (-1";
	foreach ($publishers_array as $pub_id => $title)
	{
		$publisher_sql_restriction = $publisher_sql_restriction . "," .$pub_id;
	}
	$publisher_sql_restriction = $publisher_sql_restriction . ") ";
}
else
{
	$publisher_sql_restriction = " 1=1 ";
}

if (in_array($_SESSION['user_id'],array(1,7,10))){}
else
{
    $publisher_sql_restriction = "{$publisher_sql_restriction} AND publisher_id not in (4417) ";
}

// show field?
if (isset($group_by_array["publisher_id"]))
{
	$fields["publisher_id"]["name"] = "Publisher";
	$fields["publisher_id"]["type"] = 'text';
	$fields["publisher_id"]["filter_type"] = 'number';
	$fields["publisher_id"]["input_name"] = 'publisher_id';
	$fields["publisher_id"]["requierd"] = 1;
	$fields["publisher_id"]["db_field"] = 'publisher_id';
	$fields["publisher_id"]["drop_down"] = $publishers_array;
	$fields["publisher_id"]["filter"]["defualt"] = -1;
	$fields["publisher_id"]["show_id"] = true;
}

if (isset($group_by_array["tag_id"]))
{
	$fields["tag_id"]["name"] = "Tag";
	$fields["tag_id"]["type"] = 'text';
	$fields["tag_id"]["filter_type"] = 'number';
	$fields["tag_id"]["input_name"] = 'tag_id';
	$fields["tag_id"]["requierd"] = 1;
	$fields["tag_id"]["db_field"] = 'tag_id';
	$fields["tag_id"]["filter"]["defualt"] = -1;
	$fields["tag_id"]["show_id"] = true;
	if (in_array($_SESSION['user_id'],array(1,7,10)))
	{
	    $fields["tag_id"]["drop_down"] = fetch_id_title_for_view($conn, "SELECT id, title FROM tags WHERE status = 1");
	}
	else
	{
	    $fields["tag_id"]["drop_down"] = fetch_id_title_for_view($conn, "SELECT id, title FROM tags WHERE id not in (138,150,158,161) and status = 1");
	}
}

if (isset($group_by_array["country"]))
{
	$fields["country"]["name"] = "Country";
	$fields["country"]["type"] = 'text';
	$fields["country"]["input_name"] = 'country';
	$fields["country"]["requierd"] = 1;
	$fields["country"]["db_field"] = 'country';
	$fields["country"]["drop_down"] = fetch_id_title_for_view($conn,"select country as id,title from countries");
	$fields["country"]["filter"]["defualt"] = -1;
	$fields["country"]["show_id"] = true;
}

if (isset($group_by_array["device_id"]))
{
	$fields["device_id"]["name"] = "Operating System";
	$fields["device_id"]["type"] = 'text';
	$fields["device_id"]["input_name"] = 'device_id';
	$fields["device_id"]["requierd"] = 1;
	$fields["device_id"]["db_field"] = 'device_id';
	$fields["device_id"]["drop_down"] = fetch_id_title_for_view($conn,"select basic_type as id,basic_type as title from devices");
	$fields["device_id"]["filter"]["defualt"] = -1;
	$fields["device_id"]["show_id"] = false;
}

// Advertiser Account - Handle Allowed Advertisers 
if (isset($_SESSION['user_type']) and $_SESSION['user_type'] == 3)
{
	// select all publishers with this user
	$advertiser_sql_str = "SELECT id,title FROM advertisers where allowed_user is not null and find_in_set({$curr_user},allowed_user) > 0";
}
else
{
	// select all publishers with this user
	$advertiser_sql_str = "select id,title from advertisers";
}
$advertisers_array = fetch_id_title_for_view($conn,$advertiser_sql_str);
if (isset($_SESSION['user_type']) and $_SESSION['user_type'] == 3)
{
	$advertiser_sql_restriction = " advertiser_id in (-1";
	foreach ($advertisers_array as $adv_id => $title)
	{
		$advertiser_sql_restriction = $advertiser_sql_restriction . "," .$adv_id;
	}
	$advertiser_sql_restriction = $advertiser_sql_restriction . ") ";
}
else
{
	$advertiser_sql_restriction = " 1=1 ";
}

if (isset($group_by_array["advertiser_id"]))
{
	$fields["advertiser_id"]["name"] = "Advertiser";
	$fields["advertiser_id"]["type"] = 'text';
	$fields["advertiser_id"]["filter_type"] = 'number';
	$fields["advertiser_id"]["input_name"] = 'advertiser_id';
	$fields["advertiser_id"]["requierd"] = 1;
	$fields["advertiser_id"]["db_field"] = 'advertiser_id';
	$fields["advertiser_id"]["drop_down"] = $advertisers_array;
	$fields["advertiser_id"]["filter"]["defualt"] = -1;
	//$fields["advertiser_id"]["new_filter_row"] = true;
}





if (false and isset($group_by_array["product_id"]))
{
	$fields["product_id"]["name"] 		 	= "Product";
	$fields["product_id"]["type"]         	= 'text';
	$fields["product_id"]["input_name"]   	= 'product_id';
	$fields["product_id"]["filter_type"] = 'number';
	$fields["product_id"]["requierd"]     	= 0;
	$fields["product_id"]["db_field"]     	= 'product_id';
	$fields["product_id"]["datalist"] 	 	= "SELECT id, concat(id,char(32,58,58,32),title) as title FROM products where concat(id,char(32,58,58,32),title) like {user_input} LIMIT 10";
	$fields["product_id"]["datalist_view"]["query"]	= "SELECT id, title, STATUS FROM products WHERE 1=1 ";
	$fields["product_id"]['show_id'] = true;
	$fields["product_id"]['filter']['text_search'] = 'List';
	$fields["product_id"]["link_url"] 			= "/admin/products/cfg.php?mod=edit&id={id}";
}

if (true and isset($group_by_array["product_id"]))
{
	$fields["product_id"]["name"] 		 	= "Product";
	$fields["product_id"]["type"]         	= 'text';
	$fields["product_id"]["input_name"]   	= 'product_id';
	$fields["product_id"]["filter_type"] = 'number';
	$fields["product_id"]["requierd"]     	= 0;
	$fields["product_id"]["db_field"]     	= 'product_id';
	$fields["product_id"]["datalist"] 	 	= "SELECT id, concat(id,char(32,58,58,32),advertiser_id, char(35),adv_offer_id,char(35),title) as title FROM products where concat(id,char(32,58,58,32),advertiser_id, char(35),adv_offer_id,char(35),title) like {user_input} LIMIT 10";
	$fields["product_id"]["datalist_view"]["query"]	= "SELECT id, concat(advertiser_id, char(35),adv_offer_id,char(35),title) as title, STATUS FROM products WHERE 1=1 ";
	$fields["product_id"]['show_id'] = true;
	$fields["product_id"]['filter']['text_search'] = 'List';
	$fields["product_id"]["link_url"] 			= "/admin/products/cfg.php?mod=edit&id={id}";
}



if (isset($group_by_array["sub_id1"]))
{
	$fields["sub_id1"]["name"] = "Sub ID1";
	$fields["sub_id1"]["type"] = 'text';
	$fields["sub_id1"]["input_name"] = 'sub_id1';
	$fields["sub_id1"]["requierd"] = 1;
	$fields["sub_id1"]["db_field"] = 'sub_id1';
	$fields["sub_id1"]['filter']['text_search'] = "Exact";
	//$fields["sub_id1"]["new_filter_row"] = true;
}



if (isset($group_by_array["smart_link_id"]))
{
	$fields["smart_link_id"]["name"] = "Smart Link";
	$fields["smart_link_id"]["type"] = 'text';
	$fields["smart_link_id"]["filter_type"] = 'number';
	$fields["smart_link_id"]["input_name"] = 'smart_link_id';
	$fields["smart_link_id"]["requierd"] = 1;
	$fields["smart_link_id"]["db_field"] = 'smart_link_id';
	$fields["smart_link_id"]["filter"]["defualt"] = -1;
	
	$sl_query = "";
	if (isset($_SESSION['user_type']) and $_SESSION['user_type'] == 2)
	{
	    $sl_query = "SELECT id,  title FROM smart_links WHERE id in ({$_SESSION['smart_link_id']}) ";
	}
	else
	{
	    $sl_query = "SELECT id,  title FROM smart_links";
	}
	
	
	
	if (in_array($_SESSION['user_id'],array(1,7,10))) {} else
	{
	    $sl_query = "{$sl_query} AND id not in (84,81) ";
	}
	
	$sl_query = "{$sl_query} ORDER BY id";
	$fields["smart_link_id"]["drop_down"] = fetch_id_title_for_view($conn,$sl_query);

}


if (isset($group_by_array["sl_rule_id"])) 
{
	$fields["sl_rule_id"]["name"] 		 	= "Smart Link Rule";
	$fields["sl_rule_id"]["type"]         	= 'text';
	$fields["sl_rule_id"]["input_name"]   	= 'sl_rule_id';
	$fields["sl_rule_id"]["filter_type"] = 'number';
	$fields["sl_rule_id"]["db_field"]     	= 'sl_rule_id';
	$fields["sl_rule_id"]['show_id'] = true;
	$fields["sl_rule_id"]['filter']['text_search'] = 'List';
	$fields["sl_rule_id"]["post_view_title_update"] = true;
	$fields["sl_rule_id"]["link_url"] 			= "/admin/smart_link_rules/cfg.php?mod=edit&id={id}";
	if (isset($_SESSION['user_type']) and $_SESSION['user_type'] == 2)
	{
		$fields["sl_rule_id"]["datalist"] 	 	= "SELECT id, concat(id,char(32,58,58,32),title) as title FROM smart_link_rules where smart_link_id in ({$_SESSION['smart_link_id']}) AND concat(id,char(32,58,58,32),title) like {user_input} LIMIT 10";
		$fields["sl_rule_id"]["datalist_view"]["query"]	= "SELECT id, title, STATUS FROM smart_link_rules WHERE smart_link_id in ({$_SESSION['smart_link_id']})";		
		$fields["sl_rule_id"]["name"] = "Offer";
		$fields["group_by"]["drop_down"]['sl_rule_id'] = 'Offer';
		// $group_by_array['sl_rule_id'] = 'Offer';
	}
	else
	{
		$fields["sl_rule_id"]["datalist"] 	 	= "SELECT id, concat(id,char(32,58,58,32),title) as title FROM smart_link_rules where concat(id,char(32,58,58,32),title) like {user_input} LIMIT 10";
		$fields["sl_rule_id"]["datalist_view"]["query"]	= "SELECT id, title, STATUS FROM smart_link_rules";
	}
}

if (isset($group_by_array["group_id"]))
{
    $fields["group_id"]["name"] = "Group";
    $fields["group_id"]["type"] = 'text';
    $fields["group_id"]["filter_type"] = 'number';
    $fields["group_id"]["input_name"] = 'group_id';
    $fields["group_id"]["requierd"] = 1;
    $fields["group_id"]["db_field"] = 'group_id';
    $fields["group_id"]['filter']['number'] = true;
    $fields["group_id"]["filter"]["defualt"] = -1;
    $fields["group_id"]["show_id"] = true;
    $fields["group_id"]["link_url"] = "/admin/slr_groups/cfg.php?mod=edit&id={id}";
    if (in_array($_SESSION['user_id'],array(1,7,10))) 
    {
        $fields["group_id"]["drop_down"] = fetch_id_title_for_view($conn,"SELECT id,  title FROM slr_groups ORDER BY id");
    } 
    else
    {
        $fields["group_id"]["drop_down"] = fetch_id_title_for_view($conn,"SELECT id,  title FROM slr_groups WHERE id not in (911,912,913,914,915,916,917,918,919,920,
                                                                 921,922,923,924,925,926,927,928,929,930,
                                                                 931,932,933,934,935,937,
                                                                 1027,1041,1042,1048,1049,1050,1053,1054) ORDER BY id");
    }
}

if (isset($group_by_array["sub_id2"]))
{
    $fields["sub_id2"]["name"] = "Sub ID2";
    $fields["sub_id2"]["type"] = 'text';
    $fields["sub_id2"]["input_name"] = 'sub_id2';
    $fields["sub_id2"]["requierd"] = 1;
    $fields["sub_id2"]["db_field"] = 'sub_id2';
    $fields["sub_id2"]['filter']['text_search'] = "Exact";
    $fields["sub_id2"]['div_filter_2'] = true;
}

if (isset($group_by_array["block_types"]))
{
    $fields["block_types"]["name"] = "Block Reasons";
    $fields["block_types"]["type"] = 'text';
    $fields["block_types"]["input_name"] = 'block_types';
    $fields["block_types"]["requierd"] = 1;
    $fields["block_types"]["db_field"] = 'block_types';
    $fields["block_types"]["edit_on_view"] = 1;
    $fields["block_types"]["filter"]["defualt"] = -1;
    if (in_array($_SESSION['user_id'],array(1,7,10)))
    {
        $fields["block_types"]["drop_down"] = fetch_id_title_for_view($conn,"select id as id,title as title from block_types");
    }
    else
    {
        $fields["block_types"]["drop_down"] = fetch_id_title_for_view($conn,"select id as id,title as title from block_types where s2s_error = 0");
    }
}

if (isset($group_by_array["redirect"]))
{
    $fields["redirect"]["name"] = "Click Type";
    $fields["redirect"]["type"] = 'text';
    $fields["redirect"]["input_name"] = 'redirect';
    $fields["redirect"]["requierd"] = 1;
    $fields["redirect"]["db_field"] = 'redirect';
    $fields["redirect"]["filter_type"] = 'number';
    if (in_array($_SESSION['user_id'],array(1,7,10)))
    {
        $fields["redirect"]["drop_down"] = array(
                                                0 => 'Unknown',
                                                1 => 'Pixels',
                                                2 => 'Redirect (force redirect or pop traffic)',
                                                3 => 'User Initiated Redirect (Real Click)',
                                                4 => 'S2S Clicks'
                                                );
    }
    else 
    {
        $fields["redirect"]["drop_down"] = array(
                0 => 'Unknown',
                1 => 'Pixels',
                2 => 'Redirect (force redirect or pop traffic)',
                3 => 'User Initiated Redirect (Real Click)'
        );
    }
    $fields["redirect"]["filter"]["defualt"] = -1;
}


if (isset($group_by_array["city"]))
{
	$fields["city"]["name"] = "City";
	$fields["city"]["type"] = 'text';
	$fields["city"]["input_name"] = 'city';
	$fields["city"]["requierd"] = 1;
	$fields["city"]["db_field"] = 'city';
	$fields["city"]['filter']['text_search'] = "Exact";
}

if (isset($group_by_array["conn_type"]))
{
    $fields["conn_type"]["name"] = "Connection Type";
    $fields["conn_type"]["type"] = 'text';
    $fields["conn_type"]["filter_type"] = 'number';
    $fields["conn_type"]["input_name"] = 'conn_type';
    $fields["conn_type"]["requierd"] = 1;
    $fields["conn_type"]["db_field"] = 'conn_type';
    $fields["conn_type"]["filter"]["defualt"] = -1;
    $fields["conn_type"]["drop_down"] = fetch_id_title_for_view($conn,"SELECT id,  title FROM connection_types ORDER BY id");
}

if (isset($group_by_array["asn"]))
{
	$fields["asn"]["name"] = "Carrier";
	$fields["asn"]["type"] = 'text';
	$fields["asn"]["filter_type"] = 'number';
	$fields["asn"]["input_name"] = 'asn';
	$fields["asn"]["requierd"] = 1;
	$fields["asn"]["db_field"] = 'asn'; 
	$fields["asn"]['filter']['text_search'] = "List";
	$fields["asn"]["id_translate"] = fetch_id_title_for_view($conn,"select id,title from asn");
	//$fields["asn"]["filter"]["defualt"] = 'All';
	$fields["asn"]["show_id"] = true;
	//$fields["asn"]['drop_down']['no_filter'] = true;
}

if (isset($group_by_array["os_version"]))
{
    $fields["os_version"]["name"] = "OS Version";
    $fields["os_version"]["type"] = 'text';
    $fields["os_version"]["input_name"] = 'os_version';
    $fields["os_version"]["requierd"] = 1;
    $fields["os_version"]["db_field"] = 'os_version';
    $fields["os_version"]['filter']['text_search'] = "Exact";
}


/*
if (true)
{
	$fields["export_excel_only"]["name"] = "Export To Excel";
	$fields["export_excel_only"]["input_name"] = 'export_excel_only';
	$fields["export_excel_only"]["requierd"] = 1;
	$fields["export_excel_only"]["drop_down"] = array("0"=>"Do Not Export","1"=>"Export");
	$fields["export_excel_only"]["filter"]["defualt"] = 0;

}
*/

if (isset($group_by_array["param1"]))
{
	$fields["param1"]["name"] = "Param1";
	$fields["param1"]["type"] = 'text';
	$fields["param1"]["input_name"] = 'param1';
	$fields["param1"]["requierd"] = 1;
	$fields["param1"]["db_field"] = 'param1';
	$fields["param1"]['filter']['text_search'] = "Exact";
	$fields["param1"]['new_filter_row'] = true;
}

if (isset($group_by_array["param2"]))
{
	$fields["param2"]["name"] = "Param2";
	$fields["param2"]["type"] = 'text';
	$fields["param2"]["input_name"] = 'param2';
	$fields["param2"]["requierd"] = 1;
	$fields["param2"]["db_field"] = 'param2';
	$fields["param2"]['filter']['text_search'] = "Exact";
}
if (isset($group_by_array["param3"]))
{
	$fields["param3"]["name"] = "Param3";
	$fields["param3"]["type"] = 'text';
	$fields["param3"]["input_name"] = 'param3';
	$fields["param3"]["requierd"] = 1;
	$fields["param3"]["db_field"] = 'param3';
	$fields["param3"]['filter']['text_search'] = "Exact";
}
if (isset($group_by_array["param4"]))
{
	$fields["param4"]["name"] = "Param4";
	$fields["param4"]["type"] = 'text';
	$fields["param4"]["input_name"] = 'param4';
	$fields["param4"]["requierd"] = 1;
	$fields["param4"]["db_field"] = 'param4';
	$fields["param4"]['filter']['text_search'] = "Exact";
}


/*

if (isset($group_by_array["creative_id"]))
{
	$fields["creative_id"]["name"] = "Creative";
	$fields["creative_id"]["type"] = 'text';
	$fields["creative_id"]["filter_type"] = 'number';
	$fields["creative_id"]["input_name"] = 'creative_id';
	$fields["creative_id"]["requierd"] = 1;
	$fields["creative_id"]["db_field"] = 'creative_id';
	$fields["creative_id"]["filter"]["defualt"] = -1;
	$fields["creative_id"]["drop_down"] = fetch_id_title_for_view($conn,"SELECT id, title FROM creatives ORDER BY id");		
}

*/






if (true or isset($_SESSION['user_type']) and $_SESSION['user_type'] == 0)
{
	$filter_measures_drop_down_array = array();
	$filter_measures_drop_down_array['none'] = 'None';
	if (in_array("imps",$allowed_fields)) 	$filter_measures_drop_down_array['imps'] = 'Imps';
	if (in_array("clicks",$allowed_fields)) $filter_measures_drop_down_array['clicks'] = 'Clicks';
	if (in_array("convs",$allowed_fields)) 	$filter_measures_drop_down_array['convs'] = 'Convs';
	//if (in_array("cr",$allowed_fields)) 	$filter_measures_drop_down_array['cr'] = 'CR';
	if (in_array("income",$allowed_fields)) $filter_measures_drop_down_array['income'] = 'Income';
	//if (in_array("epc",$allowed_fields)) 	$filter_measures_drop_down_array['epc'] = 'EPC';
	
	
	$fields["filter_measure"]["name"] = "Filter Measure";
	$fields["filter_measure"]["type"] = 'text';
	$fields["filter_measure"]["input_name"] = 'filter_measure';
	$fields["filter_measure"]["drop_down"] = $filter_measures_drop_down_array;
	$fields["filter_measure"]["filter"]["defualt"] = 'none';
	$fields["filter_measure"]["show_id"] = false;
	$fields["filter_measure"]["having"] = true;
	//$fields["filter_measure"]["new_filter_row"] = true;
	//$fields["filter_measure"]['div_filter_2'] = true;

	

	
	$fields["filter_type"]["name"] = "Filter Type";
	$fields["filter_type"]["type"] = 'text';
	$fields["filter_type"]["input_name"] = 'filter_type';
	$fields["filter_type"]["drop_down"] = array( 0 => "None",
													1 => '>',
													2 => '<'
													);
	$fields["filter_type"]["filter"]["defualt"] = 0;
	$fields["filter_type"]["show_id"] = false;
	$fields["filter_type"]["having"] = true;

	$fields["filter_value"]["name"] = "Filter Value";
	$fields["filter_value"]["type"] = 'text';
	$fields["filter_value"]["input_name"] = 'filter_value';
	$fields["filter_value"]['filter']['text_search'] = "Exact";
	$fields["filter_value"]["having"] = true;


	$fields["discrepency_data_ind"]["name"] = "Include Discrepency<br>Data";
	$fields["discrepency_data_ind"]["type"] = 'text';
	$fields["discrepency_data_ind"]["input_name"] = 'discrepency_data_ind';
	$fields["discrepency_data_ind"]["drop_down"] = array( 0 => "No",1=> "Yes", 2=> "Only Discrepency Data");
	$fields["discrepency_data_ind"]["filter"]["defualt"] = 0;
	$fields["discrepency_data_ind"]["show_id"] = false;
	$fields["discrepency_data_ind"]["new_filter_row"] = true;
	
	
	if (false)
	{
		$fields["conv_date_ind"]["name"] = "Use Conversion Date<br>For Convs";
		$fields["conv_date_ind"]["type"] = 'text';
		$fields["conv_date_ind"]["input_name"] = 'conv_date_ind';
		$fields["conv_date_ind"]["drop_down"] = array( 0 => "No",1=> "Yes");
		$fields["conv_date_ind"]["filter"]["defualt"] = 1;
		if (in_array($curr_user,array(1,7,10))) {$fields["conv_date_ind"]["filter"]["defualt"] = 0;}
		$fields["conv_date_ind"]["show_id"] = false;
	}
}

if (in_array("imps",$allowed_fields))
{
	$fields["imps"]["name"] = "Imps";
	$fields["imps"]["type"] = 'text';
	$fields["imps"]["input_name"] = 'imps';
	$fields["imps"]["requierd"] = 1;
	$fields["imps"]["db_field"] = 'imps';
	$fields["imps"]["edit_on_view"] = 1;
	$fields["imps"]["after_decimal"] = 0;
}

if (in_array("unique_users",$allowed_fields))
{
	$fields["unique_users"]["name"] = "Unique Users";
	$fields["unique_users"]["type"] = 'text';
	$fields["unique_users"]["input_name"] = 'unique_users';
	$fields["unique_users"]["requierd"] = 1;
	$fields["unique_users"]["db_field"] = 'unique_users';
	$fields["unique_users"]["after_decimal"] = 0;
}

if (in_array("clicks",$allowed_fields))
{
	$fields["clicks"]["name"] = "Clicks";
	$fields["clicks"]["type"] = 'text';
	$fields["clicks"]["input_name"] = 'clicks';
	$fields["clicks"]["requierd"] = 1;
	$fields["clicks"]["db_field"] = 'clicks';
	$fields["clicks"]["edit_on_view"] = 1;
	$fields["clicks"]["after_decimal"] = 0;
}

if (in_array("ctr",$allowed_fields))
{
	$fields["ctr"]["name"] = "CTR%";
	$fields["ctr"]["type"] = 'text';
	$fields["ctr"]["input_name"] = 'ctr';
	$fields["ctr"]["requierd"] = 1;
	$fields["ctr"]["db_field"] = 'ctr';
	$fields["ctr"]["edit_on_view"] = 1;
}

if (in_array("convs",$allowed_fields))
{
	$fields["convs"]["name"] = "Convs";
	$fields["convs"]["type"] = 'text';
	$fields["convs"]["input_name"] = 'convs';
	$fields["convs"]["requierd"] = 1;
	$fields["convs"]["db_field"] = 'convs';
	$fields["convs"]["edit_on_view"] = 1;
	$fields["convs"]["after_decimal"] = 0;
}

if (in_array("real_convs",$allowed_fields))
{
	$fields["real_convs"]["name"] = "Real Convs";
	$fields["real_convs"]["type"] = 'text';
	$fields["real_convs"]["input_name"] = 'real_convs';
	$fields["real_convs"]["requierd"] = 1;
	$fields["real_convs"]["db_field"] = 'real_convs';
	$fields["real_convs"]["edit_on_view"] = 1;
	$fields["real_convs"]["after_decimal"] = 0;
}

if (in_array("cr",$allowed_fields))
{
	$fields["cr"]["name"] = "CR%";
	$fields["cr"]["type"] = 'text';
	$fields["cr"]["input_name"] = 'cr';
	$fields["cr"]["requierd"] = 1;
	$fields["cr"]["db_field"] = 'cr';
	$fields["cr"]["edit_on_view"] = 1;
	$fields["cr"]["after_decimal"] = 8;
}

if (in_array("income",$allowed_fields))
{
	$fields["income"]["name"] = $income_title;
	$fields["income"]["type"] = 'text';
	$fields["income"]["input_name"] = 'income';
	$fields["income"]["requierd"] = 1;
	$fields["income"]["db_field"] = 'income';
	$fields["income"]["edit_on_view"] = 1;
	$fields["income"]["after_decimal"] = 2;
}

if (in_array("cost_on_imps",$allowed_fields))
{
    $fields["cost_on_imps"]["name"] = 'Cost (CPM Based)';
    $fields["cost_on_imps"]["type"] = 'text';
    $fields["cost_on_imps"]["input_name"] = 'cost_on_imps';
    $fields["cost_on_imps"]["requierd"] = 1;
    $fields["cost_on_imps"]["db_field"] = 'cost_on_imps';
    $fields["cost_on_imps"]["edit_on_view"] = 1;
    $fields["cost_on_imps"]["after_decimal"] = 2;
}

if (in_array("real_income",$allowed_fields))
{
	$fields["real_income"]["name"] = "Revenue";
	$fields["real_income"]["type"] = 'text';
	$fields["real_income"]["input_name"] = 'real_income';
	$fields["real_income"]["requierd"] = 1;
	$fields["real_income"]["db_field"] = 'real_income';
	$fields["real_income"]["edit_on_view"] = 1;
	$fields["real_income"]["after_decimal"] = 2;
}


if (in_array("epi",$allowed_fields))
{
	$fields["epi"]["name"] = "ECPM";
	$fields["epi"]["type"] = 'text';
	$fields["epi"]["input_name"] = 'epi';
	$fields["epi"]["requierd"] = 1;
	$fields["epi"]["db_field"] = 'epi';
	$fields["epi"]["edit_on_view"] = 1;
	$fields["epi"]["after_decimal"] = 8;
}

if (in_array("real_epi",$allowed_fields))
{
	$fields["real_epi"]["name"] = "Real ECPM";
	$fields["real_epi"]["type"] = 'text';
	$fields["real_epi"]["input_name"] = 'real_epi';
	$fields["real_epi"]["requierd"] = 1;
	$fields["real_epi"]["db_field"] = 'real_epi';
	$fields["real_epi"]["edit_on_view"] = 1;
	$fields["real_epi"]["after_decimal"] = 8;
}

if (in_array("epc",$allowed_fields))
{
	$fields["epc"]["name"] = "EPC";
	$fields["epc"]["type"] = 'text';
	$fields["epc"]["input_name"] = 'epc';
	$fields["epc"]["requierd"] = 1;
	$fields["epc"]["db_field"] = 'epc';
	$fields["epc"]["edit_on_view"] = 1;
	$fields["epc"]["after_decimal"] = 8;
}

if (in_array("real_epc",$allowed_fields))
{
	$fields["real_epc"]["name"] = "Real EPC";
	$fields["real_epc"]["type"] = 'text';
	$fields["real_epc"]["input_name"] = 'real_epc';
	$fields["real_epc"]["requierd"] = 1;
	$fields["real_epc"]["db_field"] = 'real_epc';
	$fields["real_epc"]["edit_on_view"] = 1;
	$fields["real_epc"]["after_decimal"] = 8;
}

if (in_array("timelag",$allowed_fields))
{
	$fields["timelag"]["name"] = "Timelag";
	$fields["timelag"]["type"] = 'text';
	$fields["timelag"]["input_name"] = 'timelag';
	$fields["timelag"]["requierd"] = 1;
	$fields["timelag"]["db_field"] = 'timelag';
	$fields["timelag"]["edit_on_view"] = 1;
	$fields["timelag"]["after_decimal"] = 0;
}

$title_to_display = "Partner Report";
$multi_actions_array = array();
$multi_actions_array["generate_list"] 			= 'Generate List';
$multi_actions_array["export_csv"] 				= 'Export To Csv';
$multi_actions_array["create_slr_list"] 		= 'Smart Link Rules List';
$multi_actions_array["create_product_list"] 	= 'Product List';
$src_table = 'reports';

function after_view() {
	//echo "after";
}

function after_edit() {

}
?>

