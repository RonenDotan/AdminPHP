jQuery.fn.table2CSV = function(options) 
{
    var options = jQuery.extend({
        separator: ',',
        header: [],
        headerSelector: 'th',
        columnSelector: 'td',
        delivery: 'popup', // popup, value, download
        // filename: 'powered_by_sinri.csv', // filename to download
        transform_gt_lt: true // make &gt; and &lt; to > and <
    },
    options);

    var csvData = [];
    var headerArr = [];
    var el = this;

    console.log("assdsd");
    console.log(this[0].tHead);
    
    //header
    var numCols = options.header.length;
    var tmpRow = []; // construct header avalible array

    if (numCols > 0) 
    {
        for (var i = 0; i < numCols; i++) {
            tmpRow[tmpRow.length] = formatData(options.header[i]);
        }
    } else {
        $(el).filter(':visible').find(options.headerSelector).each(function() 
        {
        	if ($(this).css('display') != 'none' && $(this)[0].classList.contains("no_csv") != true)
            	tmpRow[tmpRow.length] = formatData($(this).html());
        });
    }

    console.log(tmpRow);
    var head_c = tmpRow.filter(function (el) 
    		{
    			return el != "";
    		}
    	);
    console.log(head_c);
    row2CSV(head_c);

    // actual data
    $(el).find('tr').each(function() {
    	if (this.children[0].children[0].checked)
    	{
	        var tmpRow = [];
	        $(this).filter(':visible').find(options.columnSelector).each(function() {
	            if ($(this).css('display') != 'none' && $(this)[0].classList.contains("no_csv") != true) 
				{
					// Fix The Tooltip Issue
					if ($(this.firstElementChild).hasClass("tooltip"))
					{
						var t = $(this.firstElementChild).html();
						var te = t.substring(t.lastIndexOf('"tooltiptext">')+14,t.lastIndexOf('</span>'))
						tmpRow[tmpRow.length] = te;
					}
					else
					{
						tmpRow[tmpRow.length] = formatData($(this).html());
					}
				}
	        });
	        row2CSV(tmpRow);
	    }
    });
    if (options.delivery == 'popup') {
        var mydata = csvData.join('\n');
        if(options.transform_gt_lt){
            mydata=sinri_recover_gt_and_lt(mydata);
        }
        return popup(mydata);
    }
    else if(options.delivery == 'download') {
        var mydata = csvData.join('\n');
        if(options.transform_gt_lt){
            mydata=sinri_recover_gt_and_lt(mydata);
        }
        var url='data:text/csv;charset=utf8,' + encodeURIComponent(mydata);
        window.open(url);
        return true;
    } 
    else {
        var mydata = csvData.join('\n');
        if(options.transform_gt_lt){
            mydata=sinri_recover_gt_and_lt(mydata);
        }
        return mydata;
    }

    function sinri_recover_gt_and_lt(input){
        var regexp=new RegExp(/&gt;/g);
        var input=input.replace(regexp,'>');
        var regexp=new RegExp(/&lt;/g);
        var input=input.replace(regexp,'<');
        return input;
    }

    function row2CSV(tmpRow) {
        var tmp = tmpRow.join('') // to remove any blank rows
        if (tmpRow.length > 0 && tmp != '') {
            var mystr = tmpRow.join(options.separator);
            csvData[csvData.length] = mystr.substring(1) +'#%~%#';
            //csvData[csvData.length] = mystr +'#%~%#';
        }
    }
    function formatData(input) {
        // replace " with “
        var regexp = new RegExp(/["]/g);
        var output = input.replace(regexp, "“");
        //HTML
        var regexp = new RegExp(/\<[^\<]+\>/g);
        var output = output.replace(regexp, "");
        output = output.replace(/&nbsp;/gi,' '); //replace &nbsp;
        if (output == "") return '';
        return '"' + output.trim() + '"';
    }
    function popup(data) {
        var generator = window.open('', 'csv', 'height=400,width=600');
        generator.document.write('<html><head><title>CSV</title>');
        generator.document.write('</head><body >');
        generator.document.write('<textArea cols=70 rows=15 wrap="off" >');
        generator.document.write(data);
        generator.document.write('</textArea>');
        generator.document.write('</body></html>');
        generator.document.close();
        return true;
    }
};








function export_view(src_table)
{
	var csv_text=$("table.sortable").table2CSV({delivery:'value'});
	console.log(csv_text);
	var csv_form = document.createElement("form");
	var csv_text_input = document.createElement("input");
	var csv_name  = document.createElement("input");
	
	csv_form.method = "POST";
	csv_form.id = "csv_form";
	csv_form.action = "/admin/get_csv.php";
	csv_text_input.value=csv_text;
    csv_text_input.name="csv_text";
    csv_form.appendChild(csv_text_input);
    csv_name.value=src_table;
    csv_name.name="csv_name";
    csv_form.appendChild(csv_name);
    csv_form.hidden = true;
    document.body.appendChild(csv_form);
    csv_form.submit();
    document.body.removeChild(csv_form);
}


