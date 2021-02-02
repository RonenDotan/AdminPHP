<?php 

include("debugger.php");
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

if (isset($argv[1]))
{
    if ($argv[1] == 'help')
    {
        echo "\nPossible Parameters:\n--------------------\n";
        echo "      file=path_to_file/file\n";
        echo "      create_debuger_file=boolean [0 | 1 - defualt]\n";
        die("\n\n");
    }
    
    
    foreach ($argv as $k => $arg)
    {
        if ($k > 0)
        {
            $temp_array = explode("=",$arg);
            $q = $temp_array[0];
            $$q = $temp_array[1];
        }
    }
}


if (!isset($file))
{
// $file = "/var/www/html/crons/redis_update/temp.php";
// $file = "/var/www/html/crons/APIs/personaly/personaly_offers-old.php";
//$file = "/var/www/html/crons/APIs/cygobel/cygobel_api.php";
//$file = "/var/www/html/crons/APIs/Mobio/mobio_new_api.php";
//$file = "/var/www/html/crons/redis_update/local_redis_update_v3.php";
$file = "/var/www/html/crons/redis_update/local_redis_update_v3.php";
//	$file = "/var/www/html/admin/slr_groups/smart_link_rules_tree.php";

//$file = "/var/www/html/scaling/upload_servers_data.php";
// $file = "/var/www/html/crons/publisher_postback/insert_convs_csv.php";
	$file = "/var/www/html/crons/publisher_postback/publisher_postback.php";
}

// $_GET['redis_ip_list'] = "10.132.69.247";

//$_GET['date_from'] = "2018-12-24";
//$_GET['date_to'] = "2018-12-24";
//$_GET['api-key'] = '44f437ced647ec3f40fa0841041871cd';

//$_GET['group_id'] = 72;
//$_GET['date_from']='2018-12-26';
//$_GET['date_to']='2018-12-26';
//$_GET['product_status'] = 1;
//$_GET['status'] = 1;
//declare(ticks=1); 
// register_tick_function('debugger_point');
//ob_start();
include($file);
    

$out = ob_get_contents();
ob_end_clean();
echo $out;

/*DEBUGER*/
echo "Script Ended...\n Paused Untill Finished...\n";
while ($_SERVER["PAUSE"] == 1){ $Commands = get_defined_vars(); $line = readline('php>'); $line = ltrim(rtrim($line)); readline_add_history($line); if ($line == 'next') { echo "NextRow\n"; $_SERVER["PAUSE"] = 0; break; } elseif ($line == 'breakpoint') { echo "NextBreakpoint\n"; $_SERVER["PAUSE"] = 0; break; } elseif ($line == 'abort') { die('AbortDebug'); } elseif ($line == 'pause') { echo "FinishAndPause\n"; $_SERVER["PAUSE"] = 1; } elseif ($line == 'finish') { echo "Finish\n"; $_SERVER["PAUSE"] = 0; } else {try{eval($line); echo "\n";}catch (Throwable $t){echo $t->getMessage() ."\n";}}}


?>
