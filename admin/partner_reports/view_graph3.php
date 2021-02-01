<?php

$allow_buffer = true;
if ($allow_buffer)
{
	ob_start();
}


include_once dirname(dirname(__FILE__)). '/config.php';

if ($allow_buffer)
{
	$redis = get_redis();
}

if (isset($_GET['dev']))
{
	$dev_num = 2;
	echo "<pre class='dev{$dev_num}'>";
	echo "\nGlobal\n";
	
	echo "GET\n";
	print_r($_GET);
	
	
	echo "POST\n";
	print_r($_POST);	
	
	echo "\n\n"; 
	echo "SERVER\n";
	print_r($_SERVER);
	echo "</pre>";   
	
	
	ini_set('display_errors', 1);
	ini_set('display_startup_errors', 1);
	error_reporting(E_ALL);	
}



include_once 'cfg.php';

$bq_client = get_bq_conn();



$curr_url = $_SERVER['REQUEST_URI'];

if (isset($_GET['use_cache']) and $_GET['use_cache'] == 1 and $allow_buffer)
{
	if ($curr_content = $redis->get($curr_url))
	{
		ob_end_clean(); 
		echo $curr_content;
		
		echo   "<script>
					data_processed_ph = document.getElementById('data_processed');
					data_processed_ph.innerHTML = '<div style=\"color: red;font-weight: bold;\">Results Are From Cache, Might need to be refreshed</div>'
				</script>";
		
		echo ("<script>console.log('Found From Redis')</script>");
		return;
	}
}





//die();
$replace_url = preg_replace('#(group_by)(.*?)\&#', 'group_by=#####&', $curr_url);
$drill_pos = strpos($replace_url, "&drill");
if ($drill_pos > 0)
{
	$replace_url = substr($replace_url, 0, $drill_pos);
}

if (explode(".",$_SERVER['HTTP_HOST'])[0] == 'partner')
{
	$is_partner = true;
}
else
{
	$is_partner = false;
}

?>
<html>
<head>
<script type='text/javascript' src='/admin/menu/jquery-ui.min.js'></script> 
<script type='text/javascript' src='/admin/menu/tablesorter-master/dist/js/jquery.tablesorter.min.js'></script>
<script type='text/javascript' src='/admin/menu/tablesorter-master/dist/js/jquery.tablesorter.widgets.min.js'></script> 
<script type='text/javascript' src='/admin/menu/tablesorter-master/dist/js/extras/jquery.dragtable.mod.min.js'></script> 


</head>
<form class='form-container' method='get'>
<div class='form-title'><h2>Reports</h2></div>
<div class='tab'>
<button type='button' class='tablinks active' oncontextmenu=\"javascript:window.open('/admin/partner_reports/view_graph2.php', '_blank');return false;\" onclick=\"javascript:window.location.replace('/admin/partner_reports/view_graph.php');\">Partner</button>
<?php
if (!$is_partner) { echo   "<button type='button' class='tablinks' oncontextmenu=\"javascript:window.open('/admin/convs_report/view_graph.php', '_blank');return false;\" onclick=\"javascript:window.location.replace('/admin/convs_report/view_graph.php');\">Convs</button>"; }
if (!$is_partner) { echo   "<button type='button' class='tablinks' oncontextmenu=\"javascript:window.open('/admin/pivot_report/view.php', '_blank');return false;\" onclick=\"javascript:window.location.replace('/admin/pivot_report/view.php');\">Pivot</button>"; }
if (!$is_partner) { echo   "<button type='button' class='tablinks' oncontextmenu=\"javascript:window.open('/admin/redash_reports/cfg.php?mod=view', '_blank');return false;\" onclick=\"javascript:window.location.replace('/admin/redash_reports/cfg.php?mod=view');\">Redash</button>"; }
if (!$is_partner) { echo   "<button type='button' class='tablinks' oncontextmenu=\"javascript:window.open('/admin/raw_convs/cfg.php', '_blank');return false;\" onclick=\"javascript:window.location.replace('/admin/raw_convs/view.php');\">Raw Convs</button>"; }
echo 
"</div>";

if ($is_partner)
{
echo 
"<script>
window.setTimeout(function(){ hide_show_side_nav(); }, 10);
</script>";
}

require_once 'filter.php';

if (function_exists("before_view"))
{
	before_view();
}


echo "<div id='please_wait' align='center' valign='center'><img class='please_wait'></img></div>";     


//print_r($_GET['y1']);
//die();
//die();

?>

<!--  <div id="graph_div" hidden style="opacity: 0; border-top: 1px solid #FF942B; border-left: 1px solid #FF942B; border-right: 1px solid #FF942B;"> -->
<div id="graph_div" hidden class="data-filter" style="opacity: 0;" >
<table style="width:100%; margin-top:20px;"><tr>
<td><div>
First Y Axis:
<div class="white-field-edit" style="overflow: auto;" >
<?php
if (in_array("imps",$allowed_fields))			echo '<div style="padding-bottom: 4px;"><input type="checkbox" id="y1[]_imps" 	name="y1[]" value="imps" 	><label for="y1[]_imps" style="font-size:13.333px">Imps</label></div>';
if (in_array("unique_users",$allowed_fields))	echo '<div style="padding-bottom: 4px;"><input type="checkbox" id="y1[]_uu" 		name="y1[]" value="uu" 		><label for="y1[]_uu" style="font-size:13.333px">Unique Users</label></div>';
if (in_array("clicks",$allowed_fields)) 		echo '<div style="padding-bottom: 4px;"><input type="checkbox" id="y1[]_clicks" 	name="y1[]" value="clicks" 	><label for="y1[]_clicks" style="font-size:13.333px">Clicks</label></div>';
if (in_array("ctr",$allowed_fields)) 			echo '<div style="padding-bottom: 4px;"><input type="checkbox" id="y1[]_ctr" 		name="y1[]" value="ctr" 	><label for="y1[]_ctr" style="font-size:13.333px">CTR%</label></div>';
if (in_array("convs",$allowed_fields)) 			echo '<div style="padding-bottom: 4px;"><input type="checkbox" id="y1[]_convs" 	name="y1[]" value="convs" 	><label for="y1[]_convs" style="font-size:13.333px">Convs</label></div>';
if (in_array("cr",$allowed_fields)) 			echo '<div style="padding-bottom: 4px;"><input type="checkbox" id="y1[]_cr" 		name="y1[]" value="cr" 		><label for="y1[]_cr" style="font-size:13.333px">CR%</label></div>';
if (in_array("cost_on_imps",$allowed_fields)) 	echo '<div style="padding-bottom: 4px;"><input type="checkbox" id="y1[]_cost_on_imps" 	name="y1[]" value="cost_on_imps" 	><label for="y1[]_cost_on_imps" style="font-size:13.333px">Cost (CPM Based)</label></div>';
if (in_array("income",$allowed_fields)) 		echo '<div style="padding-bottom: 4px;"><input type="checkbox" id="y1[]_cost" 	name="y1[]" value="cost" 	><label for="y1[]_cost" style="font-size:13.333px">Cost</label></div>';
if (in_array("real_income",$allowed_fields)) 	echo '<div style="padding-bottom: 4px;"><input type="checkbox" id="y1[]_revenue" 	name="y1[]" value="revenue" ><label for="y1[]_revenue" style="font-size:13.333px">Revenue</label></div>';
if (in_array("epi",$allowed_fields)) 			echo '<div style="padding-bottom: 4px;"><input type="checkbox" id="y1[]_ecpm" 	name="y1[]" value="ecpm" 	><label for="y1[]_ecpm" style="font-size:13.333px">ECPM</label></div>';
if (in_array("real_epi",$allowed_fields)) 		echo '<div style="padding-bottom: 4px;"><input type="checkbox" id="y1[]_recpm" 	name="y1[]" value="recpm" 	><label for="y1[]_recpm" style="font-size:13.333px">Real ECPM</label></div>';
if (in_array("epc",$allowed_fields)) 			echo '<div style="padding-bottom: 4px;"><input type="checkbox" id="y1[]_epc" 		name="y1[]" value="epc" 	><label for="y1[]_epc" style="font-size:13.333px">EPC</label></div>';
if (in_array("real_epc",$allowed_fields)) 		echo '<div style="padding-bottom: 4px;"><input type="checkbox" id="y1[]_repc" 	name="y1[]" value="repc" 	><label for="y1[]_repc" style="font-size:13.333px">Real EPC</label></div>';
if (in_array("timelag",$allowed_fields)) 		echo '<div style="padding-bottom: 4px;"><input type="checkbox" id="y1[]_timelag" 	name="y1[]" value="timelag" ><label for="y1[]_timelag" style="font-size:13.333px">Timelag</label></div>';
?>
</div>
</div></td>
<td><div id="curve_chart" style="height: 500px;"></div></td>
<td><div>
Second Y Axis:
<div class="white-field-edit" style="overflow: auto;" >
<?php
if (in_array("imps",$allowed_fields))			echo '<div style="padding-bottom: 4px;"><input type="checkbox" id="y2[]_imps" 	name="y2[]" value="imps" 	><label for="y2[]_imps" style="font-size:13.333px">Imps</label></div>';
if (in_array("unique_users",$allowed_fields))	echo '<div style="padding-bottom: 4px;"><input type="checkbox" id="y2[]_uu" 		name="y2[]" value="uu" 		><label for="y2[]_uu" style="font-size:13.333px">Unique Users</label></div>';
if (in_array("clicks",$allowed_fields)) 		echo '<div style="padding-bottom: 4px;"><input type="checkbox" id="y2[]_clicks" 	name="y2[]" value="clicks" 	><label for="y2[]_clicks" style="font-size:13.333px">Clicks</label></div>';
if (in_array("ctr",$allowed_fields)) 			echo '<div style="padding-bottom: 4px;"><input type="checkbox" id="y2[]_ctr" 		name="y2[]" value="ctr" 	><label for="y2[]_ctr" style="font-size:13.333px">CTR%</label></div>';
if (in_array("convs",$allowed_fields)) 			echo '<div style="padding-bottom: 4px;"><input type="checkbox" id="y2[]_convs" 	name="y2[]" value="convs" 	><label for="y2[]_convs" style="font-size:13.333px">Convs</label></div>';
if (in_array("cr",$allowed_fields)) 			echo '<div style="padding-bottom: 4px;"><input type="checkbox" id="y2[]_cr" 		name="y2[]" value="cr" 		><label for="y2[]_cr" style="font-size:13.333px">CR%</label></div>';
if (in_array("cost_on_imps",$allowed_fields)) 	echo '<div style="padding-bottom: 4px;"><input type="checkbox" id="y2[]_cost_on_imps" 	name="y2[]" value="cost_on_imps" 	><label for="y2[]_cost_on_imps" style="font-size:13.333px">Cost (CPM Based)</label></div>';
if (in_array("income",$allowed_fields)) 		echo '<div style="padding-bottom: 4px;"><input type="checkbox" id="y2[]_cost" 	name="y2[]" value="cost" 	><label for="y2[]_cost" style="font-size:13.333px">Cost</label></div>';
if (in_array("real_income",$allowed_fields)) 	echo '<div style="padding-bottom: 4px;"><input type="checkbox" id="y2[]_revenue" 	name="y2[]" value="revenue" ><label for="y2[]_revenue" style="font-size:13.333px">Revenue</label></div>';
if (in_array("epi",$allowed_fields)) 			echo '<div style="padding-bottom: 4px;"><input type="checkbox" id="y2[]_ecpm" 	name="y2[]" value="ecpm" 	><label for="y2[]_ecpm" style="font-size:13.333px">ECPM</label></div>';
if (in_array("real_epi",$allowed_fields)) 		echo '<div style="padding-bottom: 4px;"><input type="checkbox" id="y2[]_recpm" 	name="y2[]" value="recpm" 	><label for="y2[]_recpm" style="font-size:13.333px">Real ECPM</label></div>';
if (in_array("epc",$allowed_fields)) 			echo '<div style="padding-bottom: 4px;"><input type="checkbox" id="y2[]_epc" 		name="y2[]" value="epc" 	><label for="y2[]_epc" style="font-size:13.333px">EPC</label></div>';
if (in_array("real_epc",$allowed_fields)) 		echo '<div style="padding-bottom: 4px;"><input type="checkbox" id="y2[]_repc" 	name="y2[]" value="repc" 	><label for="y2[]_repc" style="font-size:13.333px">Real EPC</label></div>';
if (in_array("timelag",$allowed_fields)) 		echo '<div style="padding-bottom: 4px;"><input type="checkbox" id="y2[]_timelag" 	name="y2[]" value="timelag" ><label for="y2[]_timelag" style="font-size:13.333px">Timelag</label></div>';
?>
</div>
</div></td>
</tr>
<tr><td colspan="3" align="center">
<br><button type="button" onclick="refresh_graph()" style="padding: 5px 50px; margin-bottom : 15px;">Refresh Graph</button><br>
</td></tr></table>
</div>
<br><br>
<div id="pre_table_container" class="data-filter" style="opacity: 1;">
<table>
<tr><td style="padding-right: 30px;"><font style="font: normal 12px/150% Arial, Helvetica, sans-serif;">Total Rows: <label id="upper_row_count"></label></font></td></tr>
</table>
<div id="datagrid" class="datagrid" style="filter:alpha; opacity:0;">
<?php
$max_str_len = 75;
$export_excel_only = (isset($_GET['export_excel_only']) and $_GET['export_excel_only'] == 'Export To CSV') ? true : false;	
if ($export_excel_only)
{
	ob_start();
}
else
{	
    echo "<center><div style='z-index: 500; position: absolute; margin-left: 10cm;' id='col_fix_please_wait' hidden><img class='please_wait' style='opacity :0.70; width: 100px;' ></div></center>";
    echo '<table class="sortable tablesorter" id="main_view_table">';
}        

$old_products = "";
if (isset($_GET['group_by']))
{
	$group_by = $_GET['group_by'];
}
else 
{
	$group_by = 'imp_date';
}

$displayed_field = array();
$displayed_field = $allowed_fields;
array_push($displayed_field, $group_by);


$group_by_header = $group_by;

if (isset($query) == false)
{
	// $where = str_replace("client_id =","stats_lite.client_id =",$where);				
    
	if ($tz == 0)
	{
	    $imp_date_start = $_GET['imp_date_start'];
	    $imp_date_end = $_GET['imp_date_end'];
	}
	else 
	{
	    
	    $imp_date_start = $tz_start_date_hour;
	    if(date("Y-m-d", strtotime($tz_end_date_hour.'+0 hours')) > date("Y-m-d")) 
	    {
	        $imp_date_end = date("Y-m-d");
	    }
	    else 
	    {
	        $imp_date_end = date("Y-m-d", strtotime($tz_end_date_hour.'+0 hours'));
	    }
	    
	    
	    if ($group_by == 'imp_date' and $tz > 0)
	    {
	        $group_by_header = "date(DATE_ADD(TIMESTAMP(imp_date), imp_hour + {$tz_clean}, 'HOUR')) as imp_date";
	    }
	    
	    if ($group_by == 'imp_hour' and $tz > 0)
	    {
	        $group_by_header = "if(imp_hour + {$tz_clean} >= 24, imp_hour + {$tz_clean} - 24,imp_hour + {$tz_clean}) as imp_hour";
	    }
	    
	    if ($group_by == 'imp_date' and $tz < 0)
	    {
	        $group_by_header = "date(DATE_ADD(TIMESTAMP(imp_date), imp_hour - {$tz_clean}, 'HOUR')) as imp_date";
	    }
	    
	    if ($group_by == 'imp_hour' and $tz < 0)
	    {
	        $group_by_header = "if(imp_hour - {$tz_clean} < 0, 24 + imp_hour - {$tz_clean},imp_hour - {$tz_clean}) as imp_hour";
	    }

	}
	
	
	if ($group_by == 'block_types')
	{
	    $group_by_header = "ifnull(block_types,'0') as block_types";
	}

	$from_clause = generate_bq_table_list("stats.stats",$imp_date_start,$imp_date_end);
	$conn->query('SET SESSION group_concat_max_len = 1000000');
	// add the publishers table
	$pub_table = fetch_id_title_for_view($conn,"SELECT 1 as id, group_concat('(SELECT ' ,id, ' AS  id,', postback_percentage ,' as postback_percentage)') as title FROM publishers")[1];
	//echo($pub_table);
	
	// Timelag field in BQ only from 2018-07-01	  
	if ($imp_date_start < "2018-07-01")
	{
		$timelag_in_table = "0";
	}
	else
	{
		$timelag_in_table = "AVG(timelag)";
	}
	
	$conv_date_ind = false;
	if (isset($_GET['conv_date_ind']) and $_GET['conv_date_ind'] == 1)
	{
	    $conv_date_ind = true;
	    //$_GET['dev'] = 1;
	}

	if (!$conv_date_ind)
	{
	$query = "	SELECT {$group_by_header},
    				   SUM(ifnull(imps,0)) as imps,
					   SUM(ifnull(unique_users,0)) as unique_users,
    				   SUM(ifnull(clicks,0)) as clicks,
    				   round((100 * SUM(ifnull(clicks,0)) / greatest(SUM(ifnull(imps,0)),1)),3) as ctr,
					   SUM(ifnull(convs * if(imp_date < '2020-07-05', 1, if(imp_date < '2020-07-05', 1, ind2)),0)) as convs,
					   SUM(ifnull(convs,0)) as real_convs,
    				   round((100 * SUM(ifnull(convs,0)) / greatest(SUM(ifnull(clicks,0)),1)),8) as cr,
    				   round(SUM(ifnull(cost,0)),5)   cost_on_imps,
    				   round(SUM(ifnull(income,0) * if(imp_date < '2020-07-05', 1, ind2) * (pub.postback_percentage/100)),2) as income,
     				   round(SUM(ifnull(income,0)),2) as real_income,
    				   round((1000 * SUM(ifnull(income,0) * (pub.postback_percentage/100)) / greatest(SUM(ifnull(imps,0)),1)),8) as epi,
    				   round((1000 * SUM(ifnull(income,0)) / greatest(SUM(ifnull(imps,0)),1)),8) as real_epi,
    				   round((1000 * SUM(ifnull(income,0) * (pub.postback_percentage/100)) / greatest(SUM(ifnull(clicks,0)),1)),8) as epc,
    				   round((1000 * SUM(ifnull(income,0)) / greatest(SUM(ifnull(clicks,0)),1)),8) as real_epc,
					   {$timelag_in_table} as timelag,
    			FROM (SELECT * FROM {$from_clause}) as stats_lite
    			LEFT JOIN 
    			(SELECT * 
    			FROM {$pub_table}
				) as pub
    			ON stats_lite.publisher_id = pub.id     			
    			WHERE {$where}
    			AND {$publisher_sql_restriction} AND {$advertiser_sql_restriction}
    			GROUP BY {$group_by}
				{$having}
    			ORDER BY {$group_by}";
	}
	else 
	{
	    $query = "	SELECT {$group_by_header},
    				   SUM(ifnull(imps,0)) as imps,
					   SUM(ifnull(unique_users,0)) as unique_users,
    				   SUM(ifnull(clicks,0)) as clicks,
    				   round((100 * SUM(ifnull(clicks,0)) / greatest(SUM(ifnull(imps,0)),1)),3) as ctr,
    				   0 as convs,
					   0 as real_convs,
    				   0 as cr,
    				   round(SUM(ifnull(cost,0)),5)   cost_on_imps,
    				   0 as income,
     				   0 as real_income,
    				   0 as epi,
    				   0 as real_epi,
    				   0 as epc,
    				   0 as real_epc,
					   0 as timelag,
    			FROM (SELECT * FROM {$from_clause}) as stats_lite
    			WHERE {$where}
    			AND {$publisher_sql_restriction} AND {$advertiser_sql_restriction}
    			GROUP BY {$group_by}
				{$having}
    			ORDER BY {$group_by}";
				
		
				

		$raw_convs_query = "
		SELECT 
		 {$group_by_header},
		 count(*) as real_convs,
		 SUM(if(imp_date < '2020-07-05', 1, reported_to_publisher)) as convs,
		 sum(if(payout = 0 or payout is null, api_payout, payout) * (postback_percentage/100) * if(imp_date < '2020-07-05', 1, reported_to_publisher)) as income,
		 sum(if(payout = 0 or payout is null, api_payout, payout)) as real_income,
		 sum(payout) as income_raw,
		 sum(api_payout) as income_pr,
		 avg(timediff_secs)/60 as timelag
			FROM (
			SELECT
					date(conversion_time) AS conv_date,
					hour(conversion_time) AS conv_hour,
					date(click_time) AS click_date,
					hour(click_time) AS click_hour,
					publisher_id,
					tag_id,
					c.country,
					device_id,
					c.advertiser_id,
					link_id AS smart_link_id,
					product_id,
					payout,
					(ifnull(p.revenue_per_conversion,0)+ ifnull(p.revenue_per_action,0)) AS api_payout,
					group_id,
					sub_id1,
					sl_rule_id AS sl_rule_id,
					'0' as block_types,
					reported_to_publisher,
					0 AS timediff_secs,
					pub.postback_percentage
			FROM	conversions AS c
			LEFT JOIN products AS p
			ON p.id = c.product_id
			LEFT JOIN publishers as pub ON (pub.id = c.publisher_id)
			) AS stats_lite
				                   WHERE {$where}
                       AND {$publisher_sql_restriction} AND {$advertiser_sql_restriction}
	                   GROUP BY {$group_by}
				       {$convs_having}
				       ORDER BY {$group_by}
					 ";


		  		       
		 // Replacement because diffrent table is used:
		 $raw_convs_query = str_replace("imp_date", "conv_date", $raw_convs_query);
		 $raw_convs_query = str_replace("imp_hour", "conv_hour", $raw_convs_query);
		 // $raw_convs_query = str_replace("stats_lite.client_id = 1 AND", "", $raw_convs_query);
	}
			    			
    			    				      
}

//echo $raw_convs_query;
//die();
// $_GET['dev'] = 1;
if (isset($_GET['dev']))
{
	$dev_num += 1;
	echo "<pre class='dev{$dev_num}'>";
	print_r($_GET);
	echo "<br><br>Main Query: {$query}<br>";
	
	if (isset($_GET['conv_date_ind']) and $_GET['conv_date_ind'] == 1)
	{
	    echo "<br><br>Convs Query:<br>".$raw_convs_query;
	}
	echo "</pre>"; 
	echo "<script>document.getElementById('please_wait').hidden = true;document.getElementsByClassName('datagrid')[0].style.opacity = 1;</script>";
}


if (!isset($_GET['imp_date_start']))
{
    ?>
    <script>
    document.getElementById('please_wait').hidden = true;
	if (typeof onload_fuctions !== 'undefined')
	{
    	for (i=0; i < onload_fuctions.length; i++)
    	{
    		onload_fuctions[i]();
    	}
	}
    </script>
    <?php 
	
 	die();
 }
 
if (isset($_GET['stop']))
{
	echo "<script>document.getElementById('please_wait').hidden = true;</script>";
 	die();
 } 
 

//$displayed_field = array($group_by,'imps','clicks','ctr','convs','cr','income','real_income','epi','real_epi','epc','real_epc');
// $displayed_field = explode(",",$group_by);






		
if ($conv_date_ind)
{
    // This part happens before:
    // $queryJobConfig = $bq_client->query($convs_query,$config_array);
    try
    {
        $ConvsQueryResults = $conn->query($raw_convs_query);
        $convs_stats_array = array();
        $group_by_convs = str_replace("imp_date", "conv_date", $group_by);
        $group_by_convs = str_replace("imp_hour", "conv_hour", $group_by_convs);
        foreach ($ConvsQueryResults as $ConvRow)
        {
			
            if ($group_by_convs == 'aaaa')
            {
                
                
            }
            else 
            {
                $convs_stats_array[$ConvRow[$group_by_convs]] = $ConvRow;
            }
        }
        
        //echo "\nconvs_stats_array<pre>\n";
        //print_r($convs_stats_array);
        // die();
    }
    catch(Exception $e)
    {
        echo "<pre><font color='black'>";
        $error_message = $e->getMessage();
        echo 'Error Occured: ' .$error_message;
        echo "<script>document.getElementById('please_wait').hidden = true;document.getElementsByClassName('datagrid')[0].style.opacity = 1;</script>";
        die();
    }
    
}
		
$config_array = array();
$config_array['configuration']['query']['useLegacySql'] = true;
$config_array['configuration']['query']['useQueryCache'] = true;
		$options = [
					'maxResults' => 10000,
					'startIndex' => 0
					];		
		
		
echo "<script>console.log(`Query:\n{$query}`);</script>";
$queryJobConfig = $bq_client->query($query,$config_array);
try
{
	$queryResults = $bq_client->runQuery($queryJobConfig,$options);
}
catch(Exception $e) 
{
  echo "<pre><font color='black'>";
  $error_message = $e->getMessage();
  echo 'Error Occured: ' .$error_message;
  echo "<script>document.getElementById('please_wait').hidden = true;document.getElementsByClassName('datagrid')[0].style.opacity = 1;</script>";
  die();
}	
$info = $queryResults->info();

$count_rows = $info["totalRows"];
//echo "<font color='#9f5e1f'>Total Rows: {$count_rows} </font><br>";
$consol_log_array["kind"] = $info["kind"]; 
$consol_log_array["etag"] = $info["etag"];
$consol_log_array["totalBytesProcessed"] = $info["totalBytesProcessed"];
$consol_log_array["jobComplete"] = $info["jobComplete"];
$consol_log_array["cacheHit"] = $info["cacheHit"];


$consol_log_json = json_encode($consol_log_array);


echo "<thead>";
echo "<tr>";
echo "<th class='fixed_col_header' style='z-index: 150;'><input type='checkbox' class='header_checkbox' onchange='change_col(this)'><br>All<input type='checkbox' value='all_rows' name='id_checkbox_all' onchange='select_deselect_all(this.checked)'></th>";
// echo "<th class='fixed_col_header' style='z-index: 150;'><input type='checkbox' class='header_checkbox' onchange='change_col(this)'><br></div>ID</th>";
$is_first = true;
foreach ($info['schema']['fields'] as $bq_field)
{
	$field = $fields[$bq_field['name']];
	if (!isset($field['hidden']) and in_array($field['db_field'], $displayed_field))
	{
		if ($export_excel_only)
		{
			echo str_replace(",",";",$field['name']) .",";
		}
		elseif ($is_first)
		{
			echo "<th class='fixed_col_header' style='z-index: 150;'><input type='checkbox' class='header_checkbox' onchange='change_col(this)'><br>{$field['name']}</th>";
			$is_first = false;
		}
		else
		{
			echo "<th class='drag-enable fixed_col_header' style='z-index: 150;'><input type='checkbox' class='header_checkbox' onchange='change_col(this)'><br>{$field['name']}</th>";
		}
	}
} 

if (!$export_excel_only)
{
	echo "<th class='drag-enable fixed_col_header' style='z-index: 150;'><input type='checkbox' class='header_checkbox' onchange='change_col(this)'><br>Drill</th>";
	echo "</tr></thead><tbody>";
}
else
{
	echo "\n";	
}
$sum_array = array();
$curr_row = 0;
foreach ($queryResults as $row) 
{
    if (isset($convs_stats_array[$row[$group_by]]))
    {

		if (isset($_GET['dev']))
		{
			$dev_num += 1;
			echo "<pre class='dev{$dev_num}'>";
			echo "<br><br>convs_stats_array:<br>";
			print_r($convs_stats_array[$row[$group_by]]);

			echo "</pre>"; 
		}


        $relevant_convs_row = $convs_stats_array[$row[$group_by]];
        $row['convs'] = $relevant_convs_row['convs'];
		$row['real_convs'] = $relevant_convs_row['real_convs'];
        
        // round((100 * SUM(ifnull(convs,0)) / greatest(SUM(ifnull(clicks,0)),1)),8) as cr
        $row['cr'] = 100 * $row['convs'] / max($row['clicks'],1);
        
        // round(SUM(ifnull(income,0) * (pub.postback_percentage/100)),2) as income,
        $row['income'] = $relevant_convs_row['income'];
        
        // round(SUM(ifnull(income,0)),2) as real_income,
        $row['real_income'] = $relevant_convs_row['real_income'];
        
        // round((1000 * SUM(ifnull(income,0) * (pub.postback_percentage/100)) / greatest(SUM(ifnull(imps,0)),1)),3) as epi,
        $row['epi'] = 1000 * $row['income'] / max($row['imps'],1);
        
        // round((1000 * SUM(ifnull(income,0)) / greatest(SUM(ifnull(imps,0)),1)),3) as real_epi,
        $row['real_epi'] = 1000 * $row['real_income'] / max($row['imps'],1);
        
        // round((1000 * SUM(ifnull(income,0) * (pub.postback_percentage/100)) / greatest(SUM(ifnull(clicks,0)),1)),3) as epc,
        $row['epc'] = 1000 * $row['income'] / max($row['clicks'],1);
        
        // round((1000 * SUM(ifnull(income,0)) / greatest(SUM(ifnull(clicks,0)),1)),3) as real_epc,
        $row['real_epc'] = 1000 * $row['real_income'] / max($row['clicks'],1);
        
        $row['timelag'] = $relevant_convs_row['timelag'];
    }
    elseif ($convs_having != "") // Need To filter this row - cause not matching any conversion data
    {
        // check if need to remove row based on convs
        continue;
    }
    
    
    if (isset($row['imps']) and $row['imps'] > 0 and isset($row['cost_on_imps']) and $row['cost_on_imps'] > 0)
    {
        $cpm_based_cost =  ($row['cost_on_imps'] / ($row['imps'] / 1000));
        if ($cpm_based_cost >= 0.001 and $cpm_based_cost <= 10)
        {
            // No Conversion is needed
        }
        else 
        {
            $row['cost_on_imps'] = $row['cost_on_imps'] / 1000;
        }
    }

	//echo "<script>var start_row = Date.now();console.log('start row Time:' + Date.now());</script>";
	$enable_drill = true;
	if (!$export_excel_only)
	{
    	echo "<tr>";
		$curr_row += 1;	
		$row_id = 'checkbox_' . $row[$group_by];
		echo "<td><input type='checkbox' value='' class='id_checkbox' id ='{$row_id}' onchange='select_row(this)'></td>";
	}
	foreach ($row as $row_field=>$row_field_value)
    {
		$field = $fields[$row_field];
		if (true and $row_field == 'ctr' and in_array($field['db_field'], $displayed_field))
		{
			if (isset($row['imps']) and $row['imps'] > 0)
			{
				echo "<td>".number_format($row_field_value,2). "% </td>";	
			}
			else
			{
				echo "<td></td>";
			}
			continue;
		}
		
	//	echo "<script>console.log('Field {$row_field}: ' + Date.now());</script>";
		// if (!isset($fields[$bq_field['name']])) { continue;}
    	
		// Sum() fields
    	if (in_array($field['db_field'], array('imps','clicks','convs','real_convs','income','real_income','unique_users','cost_on_imps')))
    	{
			if (isset($sum_array[$field['db_field']]))
			{
				$sum_array[$field['db_field']] = $sum_array[$field['db_field']] + $row[$field['db_field']]; 
			}
			else
			{
				$sum_array[$field['db_field']] = $row[$field['db_field']]; 
			}
    	}
		
		// Sum the AVG() fields
		if ($field['db_field'] == 'timelag')
    	{
			if (isset($sum_array[$field['db_field']]))
			{
				$sum_array[$field['db_field']] += $row[$field['db_field']] * $row['convs']; 
			}
			else
			{
				$sum_array[$field['db_field']] = $row[$field['db_field']] * $row['convs'];  
			}
    		
    	}
		
		
		// Select where to use tooltip
    	if (isset($group_by_array[$field['db_field']]))
		{
    		$show_tooltip = true;
    	}
		else
		{
			$show_tooltip = false;
		}
		
		
    	if (isset($field["drop_down"]) and in_array($field['db_field'], $displayed_field) and $group_by == 'block_types')
		{
			$curr_value = $row[$field['db_field']];
			if (strpos($curr_value,",") !== false)
			{
    			$enable_drill = false;
			}
			
			$curr_array = $field["drop_down"];
			
			if ($enable_drill)
			{
				if (isset($field['show_id']) and $field['show_id'] == false)
				{
					if ($export_excel_only)
					{
						echo str_replace(",",";",$curr_array[$row[$field['db_field']]]).",";
					}
					else
					{
						if ($show_tooltip)
						{
							echo "<td><div class='tooltip'>".substr($curr_array[$row[$field['db_field']]],0,$max_str_len)."<span class='tooltiptext'>".$curr_array[$row[$field['db_field']]]."</span></div></td>";
						}
						else
						{
							echo "<td>".substr($curr_array[$row[$field['db_field']]],0,$max_str_len)."</td>";
						}
					}
				}
				else
				{
					if ($export_excel_only)
					{
						echo $row[$field['db_field']]." :: ".str_replace(",",";",$curr_array[$row[$field['db_field']]]).",";
					}
					else
					{
						if ($show_tooltip)
						{
							echo "<td><div class='tooltip'>".$row[$field['db_field']]." :: ".substr($curr_array[$row[$field['db_field']]],0,$max_str_len)."<span class='tooltiptext'>".$curr_array[$row[$field['db_field']]]."</span></div></td>";
						}
						else
						{
							echo "<td>".$row[$field['db_field']]." :: ".substr($curr_array[$row[$field['db_field']]],0,$max_str_len)."</td>";
						}
					}	
				}
			}
			else 
			{
				$curr_block_types = split(",",$curr_value);
				$blocks_string = "";
				foreach ($curr_block_types as $block_type)
				{
					$blocks_string = $blocks_string . $curr_array[$block_type].",";
				}
				$blocks_string = ltrim(rtrim($blocks_string,","));
				if (isset($field['show_id']) and $field['show_id'] == false)
				{
					if ($export_excel_only)
					{
						echo str_replace(",",";",$blocks_string).",";
					}
					else
					{
						echo "<td>".$blocks_string."</td>";
					}
				}
				else
				{
					if ($export_excel_only)
					{
						echo $row[$field['db_field']]." :: ".str_replace(",",";",$blocks_string).",";
					}
					else
					{
						echo "<td>".$row[$field['db_field']]." :: ".$blocks_string."</td>";						
					}
				}
			}
		}
    	elseif (isset($field["drop_down"]) and in_array($field['db_field'], $displayed_field))
		{
			$curr_array = $field["drop_down"];
			if (isset($field['show_id']) and $field['show_id'] == false)
			{
				if ($export_excel_only)
				{
					echo str_replace(",",";",$curr_array[$row[$field['db_field']]]).",";
				}	
				else
				{
					if ($show_tooltip)
					{
						echo "<td><div class='tooltip'>".substr($curr_array[$row[$field['db_field']]],0,$max_str_len)."<span class='tooltiptext'>".$curr_array[$row[$field['db_field']]]."</span></div></td>";
					}
					else
					{
						echo "<td>".substr($curr_array[$row[$field['db_field']]],0,$max_str_len)."</td>";					
					}
				}			
			}
			else
			{
				if ($export_excel_only)
				{
					echo $row[$field['db_field']]." :: ".str_replace(",",";",$curr_array[$row[$field['db_field']]]).",";
				}
				else
				{
					// $show_tooltip = false;
					if (isset($curr_array[$row[$field['db_field']]]))
					{
						if ($show_tooltip)
						{
							echo "<td><div class='tooltip'>".$row[$field['db_field']]." :: ".substr($curr_array[$row[$field['db_field']]],0,$max_str_len)."<span class='tooltiptext'>".$curr_array[$row[$field['db_field']]]."</span></div></td>";
						}
						else
						{
							//echo "Test2";
							echo "<td>".$row[$field['db_field']]." :: ".substr($curr_array[$row[$field['db_field']]],0,$max_str_len)."</td>";					
							// echo "<td>".$row[$field['db_field']]."</td>";					
						}
					}
					else
					{
						echo "<td>".$row[$field['db_field']]." :: ... </td>";
					}
				}
				if 	(false and $field['db_field'] == 'product_id' and !isset($curr_array[$row[$field['db_field']]]))
				{
					$old_products = "{$old_products}{$row[$field['db_field']]},";
				}
			}
		}
		elseif (isset($field["datalist_view"]))
		{
			if (isset($field['link_url']))
			{
				$pre = "<a href='".str_replace("{id}",$row[$field['db_field']],$field['link_url'])."'>";
				$post = "</a>";
			}
			else
			{
				$pre = "";
				$post = "";
				
			}
			if (!isset($field['post_view_title_update']) or (isset($field['post_view_title_update']) and $field['post_view_title_update']))
			{
			    echo "<td>{$row[$field['db_field']]}</td>";
			    if (!isset($ids_post_view_title_update)) {$ids_post_view_title_update = array();}
			    array_push($ids_post_view_title_update,$row[$field['db_field']]);
			}
			else
			{
    			if (!isset($fields[$row_field]["datalist_view"]["result"]))
    			{
    				$fields[$row_field]["datalist_view"]["result"] = fetch_id_title_for_view($conn,$field["datalist_view"]["query"]);
    			}
    			
    			if (isset($field['show_id']) and !$field['show_id'])
    			{
    				echo "<td>{$pre}".$fields[$row_field]["datalist_view"]["result"][$row[$field['db_field']]]."{$post}</td>";
    			}
    			else
    			{
    				echo "<td>{$pre}".$row[$field['db_field']]." {$post}:: ".$fields[$row_field]["datalist_view"]["result"][$row[$field['db_field']]]."</td>";
    			}
			}
		}
		elseif ($field['type'] == 'text' and in_array($field['db_field'], $displayed_field))
        {
			if (isset($field["id_translate"]) and sizeof($field["id_translate"]) > 0)
			{
				$display_value = "{$row[$field['db_field']]} :: ". $field["id_translate"][$row[$field['db_field']]];
			}
			else
			{
				$display_value = $row[$field['db_field']];
			}
			
        	if ($export_excel_only)
			{
				echo str_replace(",",";",$row[$field['db_field']]). ",";
			}
			else
			{
				if ($show_tooltip)
				{
					echo "<td><div class='tooltip'>".substr($display_value,0,$max_str_len)."<span class='tooltiptext'>".$display_value."</span></div></td>";
				}
				else
				{
					// if measures - add , 
					if (isset($field["after_decimal"]))
					{
						if ($field['input_name'] == 'timelag')
						{
							$display_value = number_format($display_value,$field["after_decimal"]);
							echo "<td minute-val={$display_value}>".$display_value. "</td>";
						}
						else
						{
							echo "<td>".number_format($display_value,$field["after_decimal"]). "</td>";	
						}
					}
					else
					{
						echo "<td>".substr($display_value,0,$max_str_len). "</td>";	
					}
				}
			}
        }
        else
        {
                
        } 
    }
	
	if ($group_by == 'imp_date')
	{
		$new_url = preg_replace('#(imp_date_start)(.*?)\&#', 'imp_date_start='.$row[$group_by].'&', $replace_url);
		$new_url = preg_replace('#(imp_date_end)(.*?)\&#', 'imp_date_end='.$row[$group_by].'&', $new_url);
	}
	else
	{
		$new_url = preg_replace('#('.$group_by.')(.*?)\&#', $group_by.'='.$row[$group_by].'&', $replace_url);
	}
	$new_url = str_replace("\\","",$new_url);
	if ($enable_drill and !$export_excel_only)
	{
		//echo "<td class='no_csv'><select id='drill' name='drill' onchange='drill_down(\"$new_url\",this)'>";
		echo "<td class='no_csv'><select id='drill' onchange='drill_down(\"$new_url\",this)'>";
		echo "<option value='-1' disabled selected> Select A Value </option>";
		echo "<option value='-2' style='color:blue;font-weight: bold;'> Pivot </option>";
		foreach ($group_by_array as $k=>$v)
		{
			echo "<option value='{$k}'> $v </option>";
		}
		echo "</select></td>";
	}
	elseif (!$export_excel_only)
	{
		//echo "<td class='no_csv'><select id='drill' name='drill' disabled>";
		echo "<td class='no_csv'><select id='drill' disabled>";
		echo "<option value='-1'> Disabled </option>";
		echo "</select></td>";
	}

	if($export_excel_only)
	{
		echo "\n";
	}
	else
	{
		echo "</tr>";
	}
	
    //$row=mysqli_fetch_array($result,MYSQLI_ASSOC);
}


    // Now Check if need to query $ids_post_view_title_update to update the titles
    if (isset($ids_post_view_title_update) and sizeof($ids_post_view_title_update) > 0)
    {
        $ids_post_view_title_update_str = implode(",",$ids_post_view_title_update);
        if (isset($fields[$group_by]['drop_down']))
        {
            
        }
        elseif (isset($fields[$group_by]['datalist_view']['query']))
        {
            $fields[$group_by]["datalist_view"]["query"] = "{$fields[$group_by]["datalist_view"]["query"]} AND id in ({$ids_post_view_title_update_str})";
			$fields[$group_by]["datalist_view"]["query"] = str_replace("(,","(-1,",$fields[$group_by]["datalist_view"]["query"]);
			$fields[$group_by]["datalist_view"]["result"] = fetch_id_title_for_view($conn,$fields[$group_by]["datalist_view"]["query"]);
            $post_view_title_update_json = json_encode($fields[$group_by]["datalist_view"]["result"]);
        }
    }

	$sum_array['ctr'] 			= ($sum_array['imps'] == 0 ) ? 0 : round(100 * $sum_array['clicks'] / $sum_array['imps'],2) . "% ";
	$sum_array['cr']  			= ($sum_array['clicks'] == 0 )? 0 : round(100 * $sum_array['convs'] / $sum_array['clicks'],3); 
	$sum_array['epi'] 			= ($sum_array['imps'] == 0 )? 0 : round(1000 * $sum_array['income'] / $sum_array['imps'],3);
	$sum_array['real_epi'] 		= ($sum_array['imps'] == 0 )? 0 : round(1000 * $sum_array['real_income'] / $sum_array['imps'],3);
	$sum_array['epc'] 			= ($sum_array['clicks'] == 0 )? 0 : round(1000 * $sum_array['income'] / $sum_array['clicks'],3);
	$sum_array['real_epc'] 		= ($sum_array['clicks'] == 0 )? 0 : round(1000 * $sum_array['real_income'] / $sum_array['clicks'],3);
	$sum_array['cost_on_imps'] 	= round($sum_array['cost_on_imps'],3);
//echo "<thead style='display:block'><tr>";
	$table_footer_html = "</tbody><tfoot><tr><td><input type='checkbox' value='' name='id_checkbox'></td><td>Total</td>";
	if (in_array("imps",$allowed_fields))
	{
		$table_footer_html = $table_footer_html ."<td>".number_format($sum_array['imps'],0)."</td>";
	}
	if (in_array("unique_users",$allowed_fields))
	{
		$table_footer_html = $table_footer_html ."<td>".number_format($sum_array['unique_users'],0)."</td>";
	}
	if (in_array("clicks",$allowed_fields))
	{
		$table_footer_html = $table_footer_html ."<td>".number_format($sum_array['clicks'],0)."</td>";
	}
	if (in_array("ctr",$allowed_fields))
	{
		$table_footer_html = $table_footer_html ."<td>{$sum_array['ctr']}</td>";
	}
	if (in_array("convs",$allowed_fields))
	{
		$table_footer_html = $table_footer_html ."<td>".number_format($sum_array['convs'],0)."</td>";
	}
	if (in_array("real_convs",$allowed_fields))
	{
		$table_footer_html = $table_footer_html ."<td>".number_format($sum_array['real_convs'],0)."</td>";
	}
	if (in_array("cr",$allowed_fields))
	{
		$table_footer_html = $table_footer_html ."<td>{$sum_array['cr']}</td>";
	}
	if (in_array("cost_on_imps",$allowed_fields))
	{
	    $table_footer_html = $table_footer_html ."<td>{$sum_array['cost_on_imps']}</td>";
	}
	if (in_array("income",$allowed_fields))
	{
		$table_footer_html = $table_footer_html ."<td>{$sum_array['income']}</td>";
	}
	if (in_array("real_income",$allowed_fields))
	{
		$table_footer_html = $table_footer_html ."<td>{$sum_array['real_income']}</td>";
	}
	if (in_array("epi",$allowed_fields))
	{
		$table_footer_html = $table_footer_html ."<td>{$sum_array['epi']}</td>";
	}
	if (in_array("real_epi",$allowed_fields))
	{
		$table_footer_html = $table_footer_html ."<td>{$sum_array['real_epi']}</td>";
	}	
	if (in_array("epc",$allowed_fields))
	{
		$table_footer_html = $table_footer_html ."<td>{$sum_array['epc']}</td>";
	}	
	if (in_array("real_epc",$allowed_fields))
	{
		$table_footer_html = $table_footer_html ."<td>{$sum_array['real_epc']}</td>";
	}
	if (in_array("timelag",$allowed_fields))
	{
		$display_total_timelag = round($sum_array['timelag']/$sum_array["convs"],2);
		$table_footer_html = $table_footer_html ."<td>{$display_total_timelag}</td>";
	}
	
	
	$table_footer_html = $table_footer_html ."<td> </td></tr></tfoot>";
	if (!$export_excel_only)
	{
		echo $table_footer_html;
		echo "</table>";
	}
	else 
	{
		$table_footer_html = str_replace("</tbody><tfoot><tr><td><input type='checkbox' value='' name='id_checkbox'></td><td>","",$table_footer_html);
		$table_footer_html = str_replace("<td> </td></tr></tfoot>","",$table_footer_html);
		$table_footer_html = str_replace("<td>","",$table_footer_html);
		$table_footer_html = str_replace("</td>",",",$table_footer_html);
		//echo $table_footer_html;
		$out = ob_get_contents();
		ob_end_clean();
		file_put_contents(dirname(__FILE__)."/report.csv", $out);
		echo "<script>window.open('download_csv.php','_blank');</script>";
		echo "<script>document.getElementById('please_wait').hidden = true;</script>";
						
	}
	//echo "<script>console.log('{$consol_log_json}')</script>";

          if (false)
		  {
		  	$out = ob_get_contents();
			ob_end_clean();
			file_put_contents(dirname(__FILE__)."/excel.log", $out);
			 
		  }
          ?>
        </div>
 	<div>
	 	<?php
	 	$old_products = rtrim(ltrim($old_products,","),",");
  		if ($old_products != '')
  		{
  			$old_products_array = fetch_id_title_for_view($conn, "SELECT id, title FROM products_old WHERE id in ({$old_products})");
			$old_products_json=json_encode($old_products_array);
			
//  			print_r($old_products_array);
			?>
			<script>
				var products_array = <?php echo $old_products_json ?>;
				main_table_rows = document.getElementsByClassName('sortable')[0].children[1].children
				for (i=0; i<main_table_rows.length; i++) 
				{
					curr_row = main_table_rows[i];
					curr_product_id_title = curr_row.children[1].innerText;
					var curr_title = curr_product_id_title.split(" ::");
					if  (curr_title[1] == "")
					{
						old_product_id = parseInt(curr_title[0]);
						curr_row.children[1].innerText = old_product_id+ " :: " + products_array[old_product_id] + " - Inactive Product";
						curr_row.children[1].style.color = 'red'
					}
				}
				
			</script>
			<?php
  		}
		else
		{
		//	echo "there are no old products";
		}
		
	 	echo "<br>Actions:<br>";
			echo '<select id="multi_actions">';
			echo '<option value="-1">Please Select An Action:</option>';
			foreach ($multi_actions_array as $k=>$v)
			{
			    if ($k != 'create_slr_list' and $k != 'create_product_list')
			    {
			        echo "<option value='{$k}'>{$v}</option>";
			    }
			}
			
			if (true)
			{
			    // create a list
			    if (isset($_GET['sl_rule_id']) and $_GET['sl_rule_id'] != '')
			    {
			        echo "<option value='create_slr_list'>Smart Link Rule List</option>";
			    }
			    
			    if (isset($_GET['product_id']) and $_GET['product_id'] != '')
			    {
			        echo "<option value='create_product_list'>Product List</option>";
			    }
			}
			
			echo '</select>';
			
			echo '   <button type="button" onclick="multiAction()">Perform Action</button>';
	 	
	 	
	 	
	 	if (function_exists("after_view"))
		{

			if ($consol_log_array['cacheHit'])
			{
				$cache = 'Yes';
			}
			else
			{
				$cache = 'No';
			}
			
			if (isset($_SERVER['SERVER_NAME']) and strpos($_SERVER['SERVER_NAME'],'.partner.'))
			{
				// partner - do not show our cost!
			}
			else
			{
				echo "\n\n<pre id='data_processed'><font size='1' color='grey'>";
				$readable_data_processed = round(($consol_log_array['totalBytesProcessed'] / 1048576),2);
				$query_cost = round(($readable_data_processed * 5 / 1048576), 4);
				echo "Data Proccessed : {$readable_data_processed}MB   Cost: {$query_cost}$  	Is Cache: {$cache}";
				echo "</font></pre>";
			}
			after_view();
		}
		
		$def_y1 = array();
		if (in_array("imps",$allowed_fields)) 	array_push($def_y1, 'imps');
		if (in_array("clicks",$allowed_fields)) array_push($def_y1, 'clicks');
		$y1 = isset($_GET['y1']) ? $_GET['y1'] : $def_y1;
		
		$def_y2 = array();
		if (in_array("convs",$allowed_fields)) 	array_push($def_y2, 'convs');

		$y2 = isset($_GET['y2']) ? $_GET['y2'] : $def_y2;
	 	?>
 	</div>       
        
        <div>
        </form>
		
		
		
		 
</html>

<script>
var cols_array 				= <?php echo json_encode($cols_array); ?>;
var rows 					= document.querySelectorAll('.sortable>tbody>tr')
var checkbox_header_row 	= document.querySelector('.sortable>thead>tr');;
var main_header_row 		= document.querySelector('.sortable>thead>tr');;
var filter_cols_by_dc = 	[];


function select_deselect_all(is_selected)
{
	var checkboxes = document.getElementsByClassName('id_checkbox');
	for(var i = 0; i< checkboxes.length; i++)
	{
		checkboxes[i].checked = is_selected
		select_row(checkboxes[i])
	}
}	

function drill_down(url, curr_object)
{
	var curr_selected = curr_object.value;
	if (curr_selected == -1) // None
	{
		console.log(url);
		console.log(curr_selected);
	}
	else if (curr_selected == -2) // pivot drill
	{
		console.log(url);
		console.log(curr_selected);
		redirect = url.replace("partner_reports/view_graph2","pivot_report/view");
		redirect = redirect.replace("&group_by=#####","");
		redirect = redirect + "&rows_limit=100000&order_measure=clicks";
		window.open(redirect);
	}	
	else
	{
		var redirect =  url.replace("#####",curr_selected);
		window.open(redirect);
	}
}


function multiAction()
{
	var multi_action = document.getElementById("multi_actions").value
	if (multi_action == "-1")
	{
		alert("Please Select An Action");
	}
	else if (multi_action == 'export_csv')
	{
		export_view("general_report");
	}
	else if (multi_action == 'generate_list')
	{
		var checkedBoxes = document.querySelectorAll('input[class=id_checkbox]:checked');
		if (checkedBoxes.length <= 0)
		{
			alert("No Item Was Selected");
		} 
		else
		{
			var id_list = ""
			for (i = 0; i < checkedBoxes.length; i++) 
			{ 
				id_list += checkedBoxes[i].id.replace("checkbox_","") + ","
			}
			id_list = id_list.substring(0, id_list.length - 1);
							 
			console.log("IDs: " + id_list);
			prompt("Copy to clipboard: Ctrl+C, Enter", id_list);
		}
	}	
	else if (multi_action == 'create_slr_list')
	{
		var slr_id = "<?php echo $_GET['sl_rule_id']; ?>";
		var list_type = "<?php echo $group_by; ?>";
		var checkedBoxes = document.querySelectorAll('input[class=id_checkbox]:checked');
		if (checkedBoxes.length <= 0)
		{
			alert("No Item Was Selected");
		} 
		else
		{
			var id_list = ""
			for (i = 0; i < checkedBoxes.length; i++) 
			{ 
				id_list += checkedBoxes[i].id.replace("checkbox_","") + ","
			}
			id_list = id_list.substring(0, id_list.length - 1);
							 
			console.log("Performing Action " + multi_action + " On IDs: " + id_list);
			pop_url = "/admin/partner_reports/create_slr_list.php?slr_id="+slr_id+"&list_type="+list_type+"&prepare=1";
			pop_properties = "height=600,width=900,left=300,top=100,resizable=yes,scrollbars=yes,toolbar=yes,menubar=no,location=no,directories=no,status=yes";
			//popupWindow = window.open( pop_url, "Create List",pop_properties);
			var form = document.createElement("form");
			form.setAttribute("method", "post");
			form.setAttribute("action", pop_url);

			// setting form target to a window named 'formresult'
			form.setAttribute("target", "formresult");
			var hiddenField = document.createElement("input");              
			hiddenField.setAttribute("name", "id_list");
			hiddenField.setAttribute("value", id_list);
			form.appendChild(hiddenField);
			document.body.appendChild(form);

			// creating the 'formresult' window with custom features prior to submitting the form
			window.open('test.html', 'formresult', pop_properties);

			form.submit();
			//alert("wait")
			setTimeout(function(){form.parentNode.removeChild(form);}, 100);

		}
	}
	else if (multi_action == 'create_product_list')
	{
		var product_id = "<?php echo $_GET['product_id']; ?>";
		var list_type = "<?php echo $group_by; ?>";
		var checkedBoxes = document.querySelectorAll('input[class=id_checkbox]:checked');
		if (checkedBoxes.length <= 0)
		{
			alert("No Item Was Selected");
		} 
		else
		{
			var id_list = ""
			for (i = 0; i < checkedBoxes.length; i++) 
			{ 
				id_list += checkedBoxes[i].id.replace("checkbox_","") + ","
			}
			id_list = id_list.substring(0, id_list.length - 1);
							 
			console.log("Performing Action " + multi_action + " On IDs: " + id_list);
			pop_url = "/admin/partner_reports/create_product_list.php?product_id="+product_id+"&list_type="+list_type+"&prepare=1";
			pop_properties = "height=600,width=900,left=300,top=100,resizable=yes,scrollbars=yes,toolbar=yes,menubar=no,location=no,directories=no,status=yes";
			//popupWindow = window.open( pop_url, "Create List",pop_properties);
			var form = document.createElement("form");
			form.setAttribute("method", "post");
			form.setAttribute("action", pop_url);

			// setting form target to a window named 'formresult'
			form.setAttribute("target", "formresult");
			var hiddenField = document.createElement("input");              
			hiddenField.setAttribute("name", "id_list");
			hiddenField.setAttribute("value", id_list);
			form.appendChild(hiddenField);
			document.body.appendChild(form);

			// creating the 'formresult' window with custom features prior to submitting the form
			window.open('test.html', 'formresult', pop_properties);

			form.submit();
			//alert("wait")
			setTimeout(function(){form.parentNode.removeChild(form);}, 100);

		}
	}
	else
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
				id_list += checkedBoxes[i].id.replace("checkbox_","") + ","
			}
			id_list = id_list.substring(0, id_list.length - 1);
							 
			console.log("Performing Action " + multi_action + " On IDs: " + id_list);
			$.post( "multi_actions.php?multi_action="+multi_action,{id_list: id_list, src_table : '<?php echo $src_table; ?>' })
				.done(function( data ) {
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
						alert(alert_text);
						location.reload();
					}
					else
					{
						alert("Error Occured " + data.error)
					}    	
		    		console.log(data);
		  			});
		}
	}
}


function timelag_format_change(element, index_in_table)
{
	console.log(element.value);
	console.log(index_in_table);
	table_rows = document.getElementsByClassName("SORTABLE")[0].children[1].children;
	switch(element.value)
	{
		case "minutes":
			for (let table_row of table_rows) 
			{
				curr_min_value = table_row.children[index_in_table].getAttribute("minute-val")
				table_row.children[index_in_table].textContent = curr_min_value;
			}
			break;
		case "hours":
			for (let table_row of table_rows) 
			{
				curr_min_value = table_row.children[index_in_table].getAttribute("minute-val")
				table_row.children[index_in_table].textContent = convertMinsToHrsMins(curr_min_value);
			}
			break;
		case "days":
			for (let table_row of table_rows) 
			{
				curr_min_value = table_row.children[index_in_table].getAttribute("minute-val")
				table_row.children[index_in_table].textContent = convertMinsToDaysHrsMins(curr_min_value);
			}
			break;
	}
}

function convertMinsToHrsMins(minutes) 
{
  var h = Math.floor(minutes / 60);
  var m = minutes % 60;
  h = h < 10 ? '0' + h : h;
  m = m < 10 ? '0' + m : m;
  return h + ':' + m;
}

function convertMinsToDaysHrsMins(minutes) 
{
  var d = Math.floor(minutes / 1440);
  var minutes_left = minutes - d*1440;
  var h = Math.floor(minutes_left / 60);
  var m = minutes_left % 60;
  h = h < 10 ? '0' + h : h;
  m = m < 10 ? '0' + m : m;
  return d + '\u00A0\u00A0\u00A0' + h + ':' + m;
}


var post_view_title_update_json = <?php echo isset($post_view_title_update_json) ? $post_view_title_update_json : "{'empty':'empty'}" ; ?>;

window.onload = function()
	{
		upper_row_count.innerText="<?php echo $count_rows; ?>";
		
		
		$(function() {
			  $("#main_view_table")
								.dragtable({
    // *** new dragtable mod options ***
    // this option MUST match the tablesorter selectorSort option!
    sortClass: '.sorter',
    // this function is called after everything has been updated
    tablesorterComplete: function(table) { 
									console.log('tablesorterComplete'); 
									fix_filter_js();
									},

    // *** original dragtable settings (non-default) ***
    dragaccept: '.drag-enable',  // class name of draggable cols -> default null = all columns draggable

    // *** original dragtable settings (default values) ***
    revert: false,               // smooth revert
    dragHandle: '.table-handle', // handle for moving cols, if not exists the whole 'th' is the handle
    maxMovingRows: 40,           // 1 -> only header. 40 row should be enough, the rest is usually not in the viewport
    excludeFooter: false,        // excludes the footer row(s) while moving other columns. Make sense if there is a footer with a colspan. */
    onlyHeaderThreshold: 100,    // TODO:  not implemented yet, switch automatically between entire col moving / only header moving
    persistState: null,          // url or function -> plug in your custom persistState function right here. function call is persistState(originalTable)
    restoreState: null,          // JSON-Object or function:  some kind of experimental aka Quick-Hack TODO: do it better
    exact: true,                 // removes pixels, so that the overlay table width fits exactly the original table width
    clickDelay: 10,              // ms to wait before rendering sortable list and delegating click event
    containment: null,           // @see http://api.jqueryui.com/sortable/#option-containment, use it if you want to move in 2 dimesnions (together with axis: null)
    cursor: 'move',              // @see http://api.jqueryui.com/sortable/#option-cursor
    cursorAt: false,             // @see http://api.jqueryui.com/sortable/#option-cursorAt
    distance: 0,                 // @see http://api.jqueryui.com/sortable/#option-distance, for immediate feedback use "0"
    tolerance: 'pointer',        // @see http://api.jqueryui.com/sortable/#option-tolerance
    axis: 'x',                   // @see http://api.jqueryui.com/sortable/#option-axis, Only vertical moving is allowed. Use 'x' or null. Use this in conjunction with the 'containment' setting
    beforeStart: $.noop,         // returning FALSE will stop the execution chain.
    beforeMoving: $.noop,
    beforeReorganize: $.noop,
    beforeStop: $.noop
  })
  .tablesorter({ selectorSort: '.sorter' ,widgets: ['filter'], 
		widgetOptions: {filter_columnFilters : true}
		});
		});		
		/*
		var th_cols = document.getElementsByTagName("TH")
		for (var i = 0; i < th_cols.length; i++) 
		{
			if (th_cols[i].textContent == "Timelag")
			{ // ronen
				th_cols[i].innerHTML = 'Timelag<br><select onchange="timelag_format_change(this, '+i+')"><option value="minutes">Minutes</option><option value="hours">Hours</option><option value="days">Days</option></select>';
			}

		}
		*/

		//fix_first_row();
		window.setTimeout(function(){ change_opacity(please_wait, 0.75);} ,100);
		window.setTimeout(function(){ change_opacity(please_wait, 0.5);} ,200);
		window.setTimeout(function(){ change_opacity(please_wait, 0.25);} ,300);
		window.setTimeout(function(){ document.getElementById('please_wait').hidden = true;} ,400);
		
		
		window.setTimeout(function(){ change_opacity(datagrid, 0.25);} ,400);
		window.setTimeout(function(){ change_opacity(datagrid, 0.5);} ,500);
		window.setTimeout(function(){ change_opacity(datagrid, 0.75);} ,600);
		window.setTimeout(function(){ change_opacity(datagrid, 1);} ,700);
		
		graph_div.hidden = false;
		window.setTimeout(function(){ change_opacity(graph_div, 0.25);} ,400);
		window.setTimeout(function(){ change_opacity(graph_div, 0.5);} ,500);
		window.setTimeout(function(){ change_opacity(graph_div, 0.75);} ,600);
		window.setTimeout(function(){ change_opacity(graph_div, 1);} ,700);
		
		// fix the checkbox
		fix_checkboxes();
		fix_view_title();
		refresh_graph();

	

		if (typeof onload_fuctions !== 'undefined')
		{
	    	for (i=0; i < onload_fuctions.length; i++)
	    	{
	    		onload_fuctions[i]();
	    	}
		}
		
		setTimeout(fix_filter_js,100);
	};		


function fix_view_title()
{
	if (post_view_title_update_json.empty != undefined && post_view_title_update_json.empty == 'empty')
	{
		console.log("no need to fix_view_title post view creation")
	}
	else
	{
		console.log("fix_view_title:")
//		console.log(post_view_title_update_json);
		link_url = "<?php echo isset($fields[$group_by]['link_url']) ? $fields[$group_by]['link_url'] : "";?>";
		show_id =  "<?php echo (isset($field['show_id']) and !$field['show_id']) ? false : true;?>";
		table_rows = main_view_table.querySelector('tbody').children;
		for (i=0, size = table_rows.length; i < size;	i++)
		{
			search_id = table_rows[i].children[1].innerText;
			if (post_view_title_update_json[search_id] != undefined)
			{
				title = post_view_title_update_json[search_id];
				if  (link_url != "")
				{
					var pre = "<a href='"+ link_url.replace('{id}',search_id)+"'>";
					var post = "</a>";
				}
				else
				{
					var pre = "";					
					var post = "";
				}


				if (show_id)
				{
    				table_rows[i].children[1].innerHTML = pre+search_id+post+"::"+title
				}
				else
				{
					table_rows[i].children[1].innerHTML = pre+title+post
				}
			}
		}
	}
}
	
function fix_checkboxes()
{
	var y1_check = <?php echo json_encode($y1);?>;

	for (i=0; i< y1_check.length; i++)
	{
		document.getElementById("y1[]_"+y1_check[i]).checked = true;
	}
	
	
	var y2_check = <?php echo json_encode($y2);?>;
	for (i=0; i< y2_check.length; i++)
	{
		document.getElementById("y2[]_"+y2_check[i]).checked = true;
	}
}	





function change_opacity(element, opacity_value)
{
	element.style.opacity = opacity_value;	
}

</script>



    <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>



<script type="text/javascript">




      google.charts.load('current', {'packages':['line']});
      google.charts.setOnLoadCallback();

      function drawChart(params) 
	  {
		var table 	= document.querySelector('table.sortable');
		var header = Array.from(table.querySelectorAll('th'))

		var body 	= table.tBodies[0];
		var matrix = [];
		
		// Map Coulmn Name To Index In The HTML
		var colNameIndex = {};
		var t1 = ' ';
		var t2 = '\u25B4';
		header.forEach((val,i) => colNameIndex[val.textContent.replace(t1 + t2,'')] = i   );

		colNameIndex['Timelag'] = colNameIndex['TimelagMinutesHoursDays'];
		
		//console.log(colNameIndex);
		if (true)
		{	// Create Dynamicaly
			var colName2Pos = new Object();
			for (j=0; j< params.length + 1; j++) 
			{
				if (j == 0)
				{
					matrix[0] = [header[1].innerText]
				}
				else
				{
					matrix[0].push(params[j - 1][0])
					colName2Pos[params[j - 1][0]] = j;
				}
			}
			//console.log(colName2Pos);
			
			var checkedBoxes = document.querySelectorAll('input[class=id_checkbox]:checked');
			var all_rows = true;
			if (checkedBoxes.length <= 0)
			{
				
			}
			else
			{
				all_rows = false;
			}
			'input[class=id_checkbox]:checked'
			var curr_mat_row = 0;
			for (i=0; i< body.childElementCount; i++) 
			{
				if (all_rows || body.children[i].querySelector('input[class=id_checkbox]:checked'))
				{
					curr_mat_row = curr_mat_row + 1;
					matrix[curr_mat_row] = [body.children[i].children[1].innerText]
					for (j=0; j< params.length; j++) 
					{
						colIndex = colNameIndex[params[j][0]]
					//	console.log(params[j][0])
					//	console.log(colNameIndex);
					//	console.log(colIndex);
					//	console.log(curr_mat_row);
						matrix[curr_mat_row].push(Number(body.children[i].children[colIndex].innerText.replace(/\,/g,'')))
					}
					
					if (curr_mat_row == 30)
					{
						
					}
				}
				
			}	
		}		
		else
		{	// Create Manualy
			matrix[0] = [
						header[1].innerText,
	//					'Imps', 
	//					'Unique Users',
						'Clicks',
	//					'%CTR',
						'CONVS',
	//					'CR',
	//					'Cost',
	//					'Revenue',
	//					'ECPM',
	//					'Real ECPM',
	//					'EPC',
	//					'Real EPC',
	//					'Timelag'
						]
						
			for (i=0; i< body.childElementCount; i++) 
			{
				matrix[i+1] = 	[
								body.children[i].children[1].innerText , // Group Title
					//			Number(body.children[i].children[2].innerText.replace(/\,/g,'')) ,  // Imps
					//			Number(body.children[i].children[3].innerText.replace(/\,/g,'')) ,  // Unique Users
								Number(body.children[i].children[4].innerText.replace(/\,/g,'')) ,  // Clicks
					//			Number(body.children[i].children[5].innerText.replace(/\,/g,'')) ,  // %CTR
								Number(body.children[i].children[6].innerText.replace(/\,/g,'')) ,  // CONVS
					//			Number(body.children[i].children[7].innerText.replace(/\,/g,'')) ,  // %CR
					//			Number(body.children[i].children[8].innerText.replace(/\,/g,'')) ,  // Cost
					//			Number(body.children[i].children[9].innerText.replace(/\,/g,'')) ,  // Revenue
					//			Number(body.children[i].children[10].innerText.replace(/\,/g,'')),  // ECPM
					//			Number(body.children[i].children[11].innerText.replace(/\,/g,'')),  // Real ECPM
					//			Number(body.children[i].children[12].innerText.replace(/\,/g,'')),  // EPC
					//			Number(body.children[i].children[13].innerText.replace(/\,/g,'')),  // Real EPC
					//			Number(body.children[i].children[14].innerText.replace(/\,/g,''))   // Timelag
								]
												
			}	
			
		}
		
		//console.log(matrix);
      
		var data = google.visualization.arrayToDataTable(matrix);
		
		
		var series = new Object();
		var axes  = new Object();
		axes.y = {};
		axes.y.y1 = {};
		axes.y.y2 = {};
		for (j=0; j< params.length; j++) 
		{
			var curr_series = colName2Pos[params[j][0]] -1;
			series[curr_series] = new Object();
			series[curr_series]['axis'] = params[j][2];
			
			if (axes.y[params[j][2]]['label'] == undefined)
			{
				axes.y[params[j][2]]['label'] = params[j][0];
			}
			else
			{
				axes.y[params[j][2]]['label'] = axes.y[params[j][2]]['label'] + "," + params[j][0];
			}
		}
		
		// console.log(series);
		//console.log(axes);
        var options = {
			chart: {
						title: 'Report By ' + "<?php echo $group_by_array[$_GET['group_by']]; ?>"
						// subtitle: 'in millions of dollars (USD)'
					},

            curveType: 'function',
			legend: { position: 'top' },
			selectionMode: 'multiple',
			series : series,	
			axes: axes
		}
		//console.log(options)

	   
		var chart = new google.charts.Line(document.getElementById('curve_chart'));
	    chart.draw(data, google.charts.Line.convertOptions(options));
      }
	  
	  
	  
	  function refresh_graph()
	  {
		  resize_graph();
		  
		  var params = [];
		  y1 = document.getElementsByName("y1[]");
		  for(i=0;i< y1.length;i++)
		  {
			if (y1[i].checked)
			{
				params.push([y1[i].labels[0].innerText,1,'y1']);
			}
		  } 
		  
		  
		  y2 = document.getElementsByName("y2[]");
		  for(i=0;i< y1.length;i++)
		  {
			if (y2[i].checked)
			{
				params.push([y2[i].labels[0].innerText,1,'y2']);
			}
		  } 
		  //console.log(params);
		  
		 /* var params = [
						['Clicks', 4,'y2'],
						['Imps', 2,'y2'] ,
						['Convs', 6,'y1']
						]
						*/
		  params.sort(compareColumn);
		  drawChart(params);
	  }
	  
	   
	  function compareColumn(a, b) 
	  {
		  if (a[2] === b[2]) 
		  {
			return 0;
		  }
		  else
		  {
			return (a[2] < b[2]) ? -1 : 1;
		  }
	  }
	  
	  
	  function  resize_graph()
	  {
		graph_div = document.getElementById("graph_div");
		curve_chart  = document.getElementById("curve_chart");
		cc_width = graph_div.offsetWidth	- (2 * 150);
		curve_chart.style.width = cc_width - 50;
	  }
	  

	  function change_col(element)
	  {
	  	// col_fix_please_wait.style.marginLeft = element.parentElement.getBoundingClientRect().x;
		col_fix_please_wait.style.marginLeft = element.closest('th').getBoundingClientRect().x
	  	col_fix_please_wait.hidden = false;

	  	setTimeout(function()
	  	{ 
	  		if 	(element.checked)
	  		{
	  			fix_col(element);
	  		}
	  		else
	  		{
	  			unfix_col(element);
	  		}

	  		col_fix_please_wait.hidden = true;		 
	  	}, 100);
	  }


	  function fix_col(element) 
	  {
		  header_col = element.closest('th')
		  cell_index = +header_col.dataset.column;
		  // cell_index = element.parentElement.parentElement.cellIndex;
	  	
	  	var sum_offsetWidth = 0;
	  	var last_offsetLeft = 0;
	  	for (var j=0; j < cell_index; j++)
	  	{
			if (checkbox_header_row.children[j].querySelector('input[class=header_checkbox]:checked'))
			{
				sum_offsetWidth 	= sum_offsetWidth + rows[0].children[j].offsetWidth;
			}
	  	}
	   
		header_col.style.left = sum_offsetWidth; 
		header_col.style.zIndex = 180;
	  	
	  	
	  	for (var i=0; i < rows.length; i++)
	  	{
	  		rows[i].children[cell_index].style.left = sum_offsetWidth;
	  		rows[i].children[cell_index].style.position = "sticky";
	  		rows[i].children[cell_index].style.boxShadow = "1px 0px #f9e0c8";
	  	}

	  	// Recuresive for any other selected col - if a more advanced col was selected
	  	// ---------------------------------------------------------------------------
	  	for (var j=cell_index+1; j < checkbox_header_row.childElementCount -1; j++)
	  	{
			if (t = checkbox_header_row.children[j].querySelector('input[class=header_checkbox]:checked'))
			{
				fix_col(t);
				break;
			}
	  	} 
	  }


	  function unfix_col(element)
	  {
	  	// cell_index = element.parentElement.parentElement.cellIndex;
		header_col = element.closest('th')
		//cell_index = +header_col.getAttribute('data-column');
		cell_index = +header_col.dataset.column;
	  	
		
		//element.parentElement.parentElement.style.left = "";
	  	//element.parentElement.parentElement.style.zIndex = 160;

	  	header_col.style.left = "";
	  	header_col.style.zIndex = 100;
	  	
	  	for (var i=0; i < rows.length; i++)
	  	{
	  		rows[i].children[cell_index].style.left = "";
	  		rows[i].children[cell_index].style.position = "";
	  		rows[i].children[cell_index].style.boxShadow = "";
	  	}

	  	// Recuresive for any other selected col - if a more advanced col was selected
	  	// ---------------------------------------------------------------------------
	  	for (var j=cell_index+1; j < checkbox_header_row.childElementCount; j++)
	  	{
			if (t = checkbox_header_row.children[j].querySelector('input[class=header_checkbox]:checked'))
			{ 
				fix_col(t);
				break;
			}
	  	} 
	  }
	  	  
	  function select_row(element)
	  {
		let row = element.closest('tr')
	  	if (element.checked)
	  	{
	  		row.classList.add('selected_row');
	  	}
	  	else
	  	{
	  		row.classList.remove('selected_row');
	  	}
	  }

function fix_filter_js()
{
	// debugger;
	src_filter_row = document.getElementsByClassName("tablesorter-filter-row")[0];
	filter_cols = Array.from(src_filter_row.children)
	// filter_cols_by_dc = filter_cols.reduce((accumulator, value)=> { accumulator[value.firstElementChild.getAttribute("data-column")] = value.firstElementChild; return accumulator; },{});
	filter_cols_by_dc = filter_cols.reduce((accumulator, value)=> { accumulator[value.firstElementChild.dataset.column] = value.firstElementChild; return accumulator; },{});
	console.log("filter_cols_by_dc:");
	console.log(filter_cols_by_dc);
	let has_filters = false;

	for (let i of main_header_row.children[1].children)
	{
		if (i.classList.contains("tablesorter-filter")) 
		{
			has_filters = true;
			break;
		}
	}
 
	if (!has_filters)
	{
		for(current_col_i = 0; current_col_i < main_header_row.childElementCount; current_col_i++) 
		{
			if (current_col_i == 0)
			{
				continue;
			}
			current_col_header = main_header_row.children[current_col_i];
			
			if (current_col_header.innerText.search('Drill') >= 0) { continue; }

			//current_col_data_column = current_col_header.getAttribute("data-column");
			current_col_data_column = current_col_header.dataset.column;
			

			let new_filter_ph = document.createElement("INPUT");
			new_filter_ph.setAttribute("type", "search");
			new_filter_ph.setAttribute("class", 'tablesorter-filter');
			new_filter_ph.setAttribute("placeholder", 'Search');
			let new_br = document.createElement("br");
			current_col_header.appendChild(new_br);
			current_col_header.appendChild(new_filter_ph);

			new_filter_ph.addEventListener('input', function(e) 
			{
				let curr_filter = this;
				//let current_col_data_column = curr_filter.parentElement.getAttribute('data-column');
				let current_col_data_column = curr_filter.parentElement.dataset.column;
				filter_cols_by_dc[current_col_data_column].value = curr_filter.value;
				filter_cols_by_dc[current_col_data_column].dispatchEvent(new Event('change'));
			})
			
		}
	}
}

	  
    </script>
	
<?php
echo ("<script>console.log('Not Found From Redis')</script>");

if ($allow_buffer)
{
	$curr_content = ob_get_contents();  	
	ob_end_clean();  
	echo $curr_content;
	
	$redis->set($curr_url, $curr_content, 86400);
	
}

?>