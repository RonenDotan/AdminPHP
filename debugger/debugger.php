<?php

global $local_vars;
$local_vars = array();
// $command Global Variable initilaize
$Commands = array();
$Commands['next'] = 'next';
$Commands['breakpoint'] = 'breakpoint';
$Commands['abort'] = 'abort';
$Commands['pause'] = 'pause';
$Commands['finish'] = 'finish';
$Commands['go-to'] = 'go-to';
foreach (get_defined_functions()['internal'] as $func_name)
{
    $Commands[$func_name] = $func_name;
}
$_SERVER["NEXT"] = true;
$_SERVER['DEBUG'] = true;
$_SERVER['PAUSE'] = true;
$_SERVER["GO-TO"]["FILE"] = "";
$_SERVER["GO-TO"]["LINE"] = 10000;

function apache_note($str1, $str2)
{
    echo "\napache_note: {$str1} - {$str2} \n";
}

function debugger_autocomplete($Input, $index)
{
    global $Commands;
    // echo "\nAUTO ::: " . $Input . "\n";
    // print_r(readline_info());
   // echo "\n\n$Input";
    $Matches = array();
    foreach(array_keys($Commands) as $Command)
        if(stripos($Command, $Input) === 0)
            $Matches[] = $Command;
            
            // Prevent Segfault
            if($Matches == false)
                $Matches[] = '';
                
                return $Matches;
}

readline_completion_function('debugger_autocomplete');


function getallheaders()
{
    
}


/* ---------------------------------------------------------------------------------------------------------------
 * debugger_point
 * --------------
 * 
 * used for setting stop point on script run.
 * 
 * parameters:
 * -----------
 *      note            - Text To Print. Usually pass the calling script name and line number.
 *      is_breakpoint   - If this point is a breakpoint or just a "next" psoition.
 *                        A Breakpoint debugger point will be reached on both "next" and "breakpoint" commands
 *                        A Next debugger point will be reached only is the next command was issued
 * ---------------------------------------------------------------------------------------------------------------*/
function debugger_point($note = "", $is_breakpoint = false, $local_vars = null)
{
    if (isset($local_vars) and sizeof($local_vars) > 0)
    {
        echo "\n\nhas local_vars, to see type: print_r(\$local_vars);\n";
    }
    // Check Conditions
    if ($_SERVER["DEBUG"] and 
                            (
                                $_SERVER["NEXT"] or 
                                ($is_breakpoint and $_SERVER["BREAKPOINT"]) or
                                ($_SERVER["GO-TO"]["FILE"] != "" and $_SERVER["GO-TO"]["LINE"] < 10000)
                             )
        )
    {
        extract($GLOBALS,EXTR_REFS);
        $bt = array();
        $bt = debug_backtrace()[0];
        // echo $bt['file'] .":" .$bt['line']."\n";
        echo "\033[33m{$bt['file']}:{$bt['line']}\033[0m\n";
        if ($note != "")
        {
            echo "\033[33m{$note}\033[0m\n";
            // \033[32m   text    \033[0m
        }
        $curr_short_file_name = basename($bt['file'],".php");
        $curr_line_number = $bt['line'];
        // $Commands[$short_file_name] = $short_file_name;
        if ( $_SERVER["GO-TO"]["FILE"] != "") 
        {
            // Must Be Go To Line
            if  (
                $curr_short_file_name == $_SERVER["GO-TO"]["FILE"] and
                $curr_line_number >=  $_SERVER["GO-TO"]["LINE"]
                )
            {
                // stop
            }
            else 
            {
                // Skip this breakpoint
                return;
            }
        }
        $bt = array();
        
        while (true)
        {
            //echo  $output;
           // ob_start();
            foreach (array_keys(get_defined_vars()) as $var_name)
            {
                $Commands[$var_name] = $var_name;
            }
            
            $params = array();
            $params = get_defined_vars();
            unset($params['note']);
            unset($params['is_breakpoint']);
            unset($params['GLOBALS']);
            unset($params['Commands']);
            unset($params['func_name']);
            // unset($params['file']);
            unset($params['bt']);
            //unset($params['short_file_name']);
            unset($params['var_name']);
            unset($params['line']); 
            unset($params['params']);
            file_put_contents("debuger_raw_output.dat",json_encode($params));
            unset($params);
            
            echo ("\033[36m");
			$line = readline("php>");
            $line = ltrim(rtrim($line));
            readline_add_history($line);
            if ($line == 'next')
            {
                echo "Next Row...\n\033[0m";
                $_SERVER["BREAKPOINT"] = true;
                $_SERVER["NEXT"] = true;
                $_SERVER["GO-TO"]["FILE"] = "";
                $_SERVER["GO-TO"]["LINE"] = 10000;
                break;
            }
            elseif ($line == 'breakpoint')
            {
                echo "Running Until Next Breakpoint...\n\033[0m";
                $_SERVER["BREAKPOINT"] = true;
                $_SERVER["NEXT"] = false;
                $_SERVER["GO-TO"]["FILE"] = "";
                $_SERVER["GO-TO"]["LINE"] = 10000;
                break;
            }
            elseif (strpos($line, "go-to") !== false)
            {
                $arguments = explode(" ",$line);
                foreach ($arguments as $k=>$arg)
                {
                    if ($arg == "-l")
                    {
                        $dest_line = $arguments[$k+1];
                    }
                    
                    if ($arg == "-f")
                    {
                        $dest_file = $arguments[$k+1];
                    }
                }
                
                if (isset($dest_line) and isset($dest_file))
                {
                    echo "Running To File {$dest_file} Line {$dest_line}...\n\033[0m";
                    $_SERVER["BREAKPOINT"] = false;
                    $_SERVER["NEXT"] = false;
                    $_SERVER["GO-TO"]["FILE"] = $dest_file;
                    $_SERVER["GO-TO"]["LINE"] = $dest_line;
                    break;
                }
                else
                {
                    // must supply...
                    echo "Please Use The GoTo With -l line_number -f file_name (base name only, no .php)";
                }
            }
            elseif ($line == 'abort')
            {
                die("\n\nAbort\n\n\033[0m");
            }
            elseif ($line == 'pause')
            {
                echo "Finish And Pause...\n";
                $_SERVER["DEBUG"] = false;
                $_SERVER["BREAKPOINT"] = false;
                $_SERVER["NEXT"] = false;
                $_SERVER["PAUSE"] = true;
                $_SERVER["GO-TO"]["FILE"] = "";
                $_SERVER["GO-TO"]["LINE"] = 10000;
                echo "\033[0m";
                break;
            }
            elseif ($line == 'finish')
            {
                echo "Finish\n";
                echo "\033[0m";
                $_SERVER["DEBUG"] = false;
                $_SERVER["BREAKPOINT"] = false;
                $_SERVER["NEXT"] = false;
                $_SERVER["PAUSE"] = false;
                $_SERVER["GO-TO"]["FILE"] = "";
                $_SERVER["GO-TO"]["LINE"] = 10000;
                break;
            } 
            else
            {
                try
                {
                    eval($line);
                    echo "\n";
                }
                catch (Throwable $t)
                {
                    echo $t->getMessage() ."\n";
                }
            }
        }
    }
}               






function create_debugger_script($file)
{
    // for special breakpoint, replace $_SERVER["next"] == 1 with the requested condition
    $nextrow_text = '/*DEBUGER*/     while ($_SERVER["DEBUG"] == 1 and $_SERVER["next"] == 1){$Commands = get_defined_vars();$line = readline(\'php>\');$line = ltrim(rtrim($line));readline_add_history($line);if ($line == \'next\'){echo "NextRow\n";$_SERVER["next"] = 1;break;}elseif ($line == \'breakpoint\'){echo "Breakpoint\n";$_SERVER["next"] = 0;break;}elseif ($line == \'abort\'){die(\'AbortDebug\');}elseif ($line == \'pause\'){echo "FinishAndPause\n";$_SERVER["DEBUG"] = 0; $_SERVER["PAUSE"] = 1;}elseif ($line == \'finish\'){echo "Finish\n";$_SERVER["DEBUG"] = 0;} else{eval($line);echo "\n";}}';
    $str=file_get_contents($file);
    $str=str_replace(";", ";    " . $nextrow_text,$str);
    $str=str_replace("<?php", "<?php    {$nextrow_text}",$str);
    $debuger_script_path = str_replace(".php","_debuger.php",$file);
    file_put_contents($debuger_script_path, $str);
    
}



function debuger_help()
{
    echo "\033[32mPossible Command:\n";
    echo "next          - next line(If enabled in script)\n";
    echo "breakpoint    - jump to next breakpoint (If enabled in script)\n";
    echo "go-to         - jump to a line in a file. Usage: GoTo -l 199 -f network_api\n";
    echo "abort         - kill the script immediately\n";
    echo "pause         - run all the script and then pause\n";
    echo "finish        - run all the script and finish\n";
    echo "------------------------------------------------------------------------------------------------------\n";
    echo "In Function Debugger Points Parameters:\n";
    echo "---------------------------------------\n";
    echo "In The Calling Function:\n";
    echo "\$local_vars = array();\n";
    echo "\$var_names = array_keys(get_defined_vars());\n";
    echo "foreach (\$var_names as \$var_name)    { \$local_vars[\$var_name] =".'& $$var_name;'."    }\n";
    echo "debugger_point('from calling function',true,\$local_vars);\n\n";
    echo "In The Breakpoint Pause, set \$local_vars['param'] = 3;\n\n\033[0m";
}

echo "\033[32m\n\n======================================================================================================\n";
echo "=                                                Debuger                                             =\n";
echo "======================================================================================================\n\033[0m";


debuger_help();
debugger_point();

// declare(ticks=1);

/*DEBUGER*/     // echo __FILE__." : ". __LINE__ . "\n"; while ($_SERVER["DEBUG"] == 1 and $_SERVER["next"] == 1){$Commands = get_defined_vars();$line = readline('php>');$line = ltrim(rtrim($line));readline_add_history($line);if ($line == 'next'){echo "NextRow\n";$_SERVER["next"] = 1;break;}elseif ($line == 'breakpoint'){echo "Breakpoint\n";$_SERVER["next"] = 0;break;}elseif ($line == 'abort'){die('AbortDebug');}elseif ($line == 'pause'){echo "FinishAndPause\n";$_SERVER["DEBUG"] = 0; $_SERVER["PAUSE"] = 1;}elseif ($line == 'finish'){echo "Finish\n";$_SERVER["DEBUG"] = 0;} else{eval($line);echo "\n";}}
?>


