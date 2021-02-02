<?php

if (!isset($_GET['pwd']))
{
    die("Please Supply A Password: GET[pwd]");
}

if ($_GET['pwd'] != 'pilpilon')
{
    die("Password Inncorect");
}


?>

<html>
<script src="http://ajax.googleapis.com/ajax/libs/jquery/3.1.1/jquery.min.js"></script>	
<script src="/admin/json-viewer/jquery.json-viewer.js"></script>
<link href="/admin/json-viewer/jquery.json-viewer.css" type="text/css" rel="stylesheet">
<h1 style="color: white;" >Debugger Output</h1>
<h3 style="color: white;" id="headline3">Debugger Output</h3>
<body style="background-image: url(https://i.imgur.com/3m1LWaC.png);">
<div id="formatted_output">
<!--  <div name="event_div"><textarea type="text" name="result"></textarea><label><input type='checkbox' id='collapsed' >Collapse nodes</label><label><input type='checkbox' id='with-quotes'>Keys with quotes</label><div size><pre name= 'json-renderer' class='pre-json'></pre></div></div>
-->
</div>

</body>
</html>

<script>
var all_json_data = [];
var headline_set = false;
var source = new EventSource("debugger_sse_agent.php");
source.onmessage = function(event) 
{
	num_of_events = document.getElementsByName("event_div").length
	
 	if(num_of_events == 0 || all_json_data[num_of_events-1] != event.data)
 	{
 	 	all_json_data[num_of_events] = event.data
 		formatted_output.innerHTML += "<div name='event_div'><label id='title-"+num_of_events+"' style='background-color: #d83a3a;'></label><br><textarea type='text' name='result' id='result-"+num_of_events+"' style='width: 100%; height: 15%;'></textarea><div><pre name= 'json-renderer' class='pre-json' style='background-color: white;'></pre></div></div><br><br><br>";
        
        
        // loop over all saved data
        for (var i = 0; i < all_json_data.length; i++) 
        {
        	document.getElementById("result-"+i).value = all_json_data[i];
        	var curr_jv = document.getElementsByName("json-renderer")[i];
        	input = JSON.parse(JSON.parse(all_json_data[i]));
        	if (headline_set == false)
        	{
        		headline3.innerText = input.file;
        		headline_set = true;
        	}
        	document.getElementById("title-"+i).innerText = input.curr_short_file_name + ":" + input.curr_line_number;
			options = 	{
					collapsed: true,
					withQuotes: false
					};
			
        	$(curr_jv).jsonViewer(input,options);
        }
	}
};



function json_format(element)
{
	var base_element = element.name.replace("_json_btn", "");
	var input_src = document.getElementsByName(base_element)[0].value;
	input_src = input_src.replace(/(\r\n\t|\n|\r\t)/gm,"");								
	try 
	{
		var input = eval('(' + input_src + ')');
	}
	catch (error) 
	{
	return alert("Cannot eval JSON: " + error);
	}
	
	options = 	{
				collapsed: $('#'+base_element+'_collapsed').is(':checked'),
				withQuotes: $('#'+base_element+'_with-quotes').is(':checked')
				};
	$('#'+base_element+'-json-renderer').jsonViewer(input, options);
}


</script>