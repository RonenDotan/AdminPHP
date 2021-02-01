<style>
.dummy{
    border: 1px solid #f2e3d2;
}

<?php



if (true)
{
if (isset($_GET['iframe']) and $_GET['iframe'] == 1)
{
		echo "
	body {
    background-color: #edf0f5;
	    margin: 0px;
		color: #3f5872;

		}
	";	
}
else
{
	echo "
	body {
    background-color: #edf0f5;
	    margin: 0px;
		    color: #3f5872;

		}
	";	
} 
}

?>

pre, xmp, plaintext, listing {
    font-family: "Helvetica Neue", Helvetica, sans-serif;
}

.tab {
  overflow: hidden;
  border-radius: 4px;
  border-left: 1px inset;
}
.tab button {
  background-color: #edf0f5;
  float: left;
  border-radius: 4px;
  border: none;
  border-top: 1px inset;
  border-right: 1px outset;
  cursor: pointer;
  padding: 6px 10px;
  font-size: 17px;
  color: #3f5872;
  
}

/* Change background color of buttons on hover */
.tab button:hover {
  background-color: #fff2bf;
}

/* Create an active/current tablink class */
.tab button.active {
  background-color: white;
  border-left: 1px outset;
  border-right: 1px outset;
}

.show
{
	display: table-row;
}

.hide
{
	display: none;
}

.form-container {
   background: #edf0f5;
   font-family: "Helvetica Neue", Helvetica, sans-serif;
   text-decoration: none;
   min-width:300px;
   padding:20px;
   width: -webkit-fill-available;

   }
   
   .form-container-menu {
   background: #edf0f5;
   font-family: "Helvetica Neue", Helvetica, sans-serif;
   text-decoration: none;
   min-width:300px;
   }   
   
      .div-menu {
   background: white;

   }  
   

   
.data-filter
{
	border: 1px #e4e5e7;
	background-color: white;
	border-radius: 4px;
	padding: 1%;
	overflow-x: auto;
	border-width: 1px;
	border-style: solid;
}    
.form-field-edit {
   border: 1px solid #3f5872;
   border-radius: 4px;
   padding:8px;
   margin-bottom:10px;
   min-width:280px;
   width:700px;
   color: inherit;
}
.data-main-div
{
	border: 1px solid #3f587226;
	background-color: white;
	border-radius: 4px;
	padding: 1%;
	overflow-x: auto;
	border-width: 1px;
	border-style: solid;
	width: fit-content;
}

.form-field:focus {
   background: #fff;
   color: black;
   }
 .form-field-edit:focus { 
   background: #fff;
   color: black;
   } 
.form-container h2 {
   text-shadow: none;
   font-size:18px;
   margin: 0 0 10px 0;
   font-weight:bold;
   text-align:left;

    }
.form-title {
   margin-bottom: 10px;
   }
.submit-container {
   margin:8px 0;
   text-align:right;
   }

   
   .submit-button {
   border: 1px solid #3f5872;
   background: -webkit-linear-gradient(top, #3f58729c, #3f5872);
   border-radius: 4px;
   color: white;
   font-weight: bold;
   padding: 8.5px 200px;
   font-size: 14px;
   }
.submit-button:hover {
   border: 1px solid #3f5872;
   background: -webkit-linear-gradient(top, #3f5872, #3f58729c );
   border-radius: 4px;
   color: white;
   font-weight: bold;
   padding: 8.5px 200px;
   font-size: 14px;
   }
.submit-button:active {
   background: red;
   color: white;
   }
   
      .submit-button-edit {
   border: 1px solid #3f5872;
   background: -webkit-linear-gradient(top, #3f58729c, #3f5872);
   border-radius: 4px;
   color: white;
   font-weight: bold;
   padding: 8.5px 200px;
   font-size: 14px;
   margin:	50px;
   margin-left: 200px;
   }
.submit-button-edit:hover {
   border: 1px solid #3f5872;
   background: -webkit-linear-gradient(top, #3f5872, #3f58729c );
   border-radius: 4px;
   color: white;
   font-weight: bold;
   padding: 8.5px 200px;
   font-size: 14px;
   }
.submit-button-edit:active {
   border: 1px solid #3f5872;
   background: red;
   border-radius: 4px;
   color: white;
   font-weight: bold;
   padding: 8.5px 200px;
   font-size: 14px;
   }

      .white-field-edit {
   border: 1px solid #555;
   -webkit-border-radius: 4px;
   -moz-border-radius: 4px;
   border-radius: 4px;
   color: #555;
   -webkit-box-shadow: rgba(255,255,255,0.4) 0 1px 0, inset rgba(000,000,000,0.7) 0 0px 0px;
   -moz-box-shadow: rgba(255,255,255,0.4) 0 1px 0, inset rgba(000,000,000,0.7) 0 0px 0px;
   box-shadow: rgba(255,255,255,0.4) 0 1px 0, inset rgba(000,000,000,0.7) 0 0px 0px;
   padding:8px;
   margin-bottom:10px;
   width:150px;
}
   
.datagrid table {
 border-collapse: collapse;
 text-align: left;
 width: 100%;
 }

 .datagrid {
font: normal 12px/150% Arial, Helvetica, sans-serif;
 background: #fff;
 overflow: auto;
 border: 1px #3f587226;
 border-radius: 4px;
 border-style: solid;
 max-height:500px;
 }

.datagrid table td, .datagrid table th {
 padding: 3px 10px;
 }

.datagrid table thead th {
position: sticky;
top: 0px;
background-color:#e3e7ed;
 font-size: 15px;
 font-weight: bold;
 border-bottom-style : double;
  z-index:100;
  box-shadow: 1px 0px;
  border-left: 1px solid;
  vertical-align: top;
  
 }
 
 .datagrid table thead th:hover {
	 background-color:#e1eef4;
 }


.datagrid table tbody td {
 border-left: 1px solid #3f587226;
font-size: 12px;
border-bottom: 1px solid #3f587226;
font-weight: normal;
 }

.datagrid table tbody td:first-child {
 border-left: none;
 }

.datagrid table tbody tr:last-child td {
 border-bottom: none;
 }


 .datagrid table tfoot td div{
 padding: 2px;
 }
 
  .datagrid tfoot {
 background: #edf0f5;
 font-size: 13px;
 border-top-style: solid;
 border-top-width: 0.5px;
 }

.datagrid table tfoot td ul {
 margin: 0;
 padding:0;
 list-style: none;
 text-align: right;
 }

.datagrid table tfoot  li {
 display: inline;
 }

.datagrid table tfoot ul.active, .datagrid table tfoot ul a:hover {
 text-decoration: none;
border-color: red;
 color: #FFFFFF;
 background: none;
 background-color:#36752D;
}

.datagrid table input[type=text]{
    background: none;
    border: thin;
 color: red;
 border-left: 0px solid #D9CFB8;
font-size: 12px;
border-bottom: 1px solid #E1EEF4;
border-bottom-color: red;
font-weight: normal;
 }
 
 .datagrid table input[type=submit]{
   border: 1px solid #447314;
   background: #6aa436;
   background: -webkit-gradient(linear, left top, left bottom, from(#8dc059), to(#6aa436));
   background: -webkit-linear-gradient(top, #8dc059, #6aa436);
   background: -moz-linear-gradient(top, #8dc059, #6aa436);
   background: -ms-linear-gradient(top, #8dc059, #6aa436);
   background: -o-linear-gradient(top, #8dc059, #6aa436);
   background-image: -ms-linear-gradient(top, #8dc059 0%, #6aa436 100%);
   -webkit-border-radius: 4px;
   -moz-border-radius: 4px;
   border-radius: 4px;
   -webkit-box-shadow: rgba(255,255,255,0.4) 0 1px 0, inset rgba(255,255,255,0.4) 0 1px 0;
   -moz-box-shadow: rgba(255,255,255,0.4) 0 1px 0, inset rgba(255,255,255,0.4) 0 1px 0;
   box-shadow: rgba(255,255,255,0.4) 0 1px 0, inset rgba(255,255,255,0.4) 0 1px 0;
   text-shadow: none;
   color: #31540c;
   font-family: "Helvetica Neue", Helvetica, sans-serif;
   font-size: 15px;
   text-decoration: none;
   }
 .datagrid table input[type=submit]:hover {
   border: 1px solid #447314;
   text-shadow: none;
   background: #6aa436;
   background: -webkit-gradient(linear, left top, left bottom, from(#8dc059), to(#6aa436));
   background: -webkit-linear-gradient(top, #8dc059, #6aa436);
   background: -moz-linear-gradient(top, #8dc059, #6aa436);
   background: -ms-linear-gradient(top, #8dc059, #6aa436);
   background: -o-linear-gradient(top, #8dc059, #6aa436);
   background-image: -ms-linear-gradient(top, #8dc059 0%, #6aa436 100%);
   color: #fff;
   }
.datagrid table input[type=submit]:active {
   text-shadow: none;
   border: 1px solid #447314;
   background: #8dc059;
   background: -webkit-gradient(linear, left top, left bottom, from(#6aa436), to(#6aa436));
   background: -webkit-linear-gradient(top, #6aa436, #8dc059);
   background: -moz-linear-gradient(top, #6aa436, #8dc059);
   background: -ms-linear-gradient(top, #6aa436, #8dc059);
   background: -o-linear-gradient(top, #6aa436, #8dc059);
   background-image: -ms-linear-gradient(top, #6aa436 0%, #8dc059 100%);
   color: #fff;
   }

div.dhtmlx_window_active, div.dhx_modal_cover_dv {
 position: fixed !important;
 }
 
 table.sortable th:not(.sorttable_sorted):not(.sorttable_sorted_reverse):not(.sorttable_nosort):after { 
     
}

table.sortable tbody tr:nth-child(2n) td {
  background: #edf0f5;
}



table.sortable tbody tr:nth-child(2n+1) td {
  background: white;
}

table {
	width: -webkit-fill-available;
}


table.sortable tbody tr:hover td {
 background: #fff2bf;
 }
 
 table.sortable tbody tr.selected_row td {
 background: #d5e0f1;
 }
 
img.logo {
	
	width: 100px;
  	height: 100px;
	
	content:url(/admin/theme/images/Lesha_logo_s.png);
}


font
{

}

img.mag_glass {
	width: 30px;
  	height: 30px;
	content:url(/admin/theme/images/magnifying-glass.jpg);
}

img.please_wait {
	content:url(/admin/theme/images/tenor.gif);
	width: 50%;
}

.form-filter-field-label
{

}

.form-field-filter {
   border: 1px solid #3f5872;
   border-radius: 4px;
   padding:8px;
   width: 200px;
   margin: 5px;
   color: inherit;
  } 
  
  
  .form-field-filter-bk {
   border: 1px solid #3f5872;
   border-radius: 4px;
   padding:8px;
   margin-bottom:10px;
   margin-right:30px;
   width: 200px;
  } 
 
.form-field-filter:focus {
   background: #fff;
   color: black;
   }
 .form-field-filter:focus { 
   background: #fff;
   color: black;
   } 

/*
.div-filter{
	overflow: auto;
	display: inline;
	margin: 10%;
	color:white;
	width: 10%;
	}  
*/



/* tooltip */

/* Tooltip container */
.tooltip {
    position: relative;
    display: inline-block;
}

/* Tooltip text */
.tooltip .tooltiptext {
    visibility: hidden;
    background-color: #555;
    color: #fff;
    text-align: center;
    padding: 5px 5px;
    border-radius: 6px;

    /* Position the tooltip text */
    position: absolute;
    z-index: 1;
    bottom: 125%;
    left: 50%;
    margin-left: -60px;

    /* Fade in tooltip */
    opacity: 0;
    transition: opacity 0.3s;
}

/* Tooltip arrow */
.tooltip .tooltiptext::after {
    content: "";
    position: absolute;
    top: 100%;
    left: 50%;
    margin-left: -5px;
    border-width: 5px;
    border-style: solid;
    border-color: #555 transparent transparent transparent;
}

/* Show the tooltip text when you mouse over the tooltip container */
.tooltip:hover .tooltiptext {
    visibility: visible;
    opacity: 1;
}

/* end tooltip */


.toggle {
  position: relative;
  display: block;
  width: 40px;
  height: 20px;
  cursor: pointer;
  -webkit-tap-highlight-color: transparent;
  transform: translate3d(0, 0, 0);
}
.toggle:before {
  content: "";
  position: relative;
  top: 3px;
  left: 3px;
  width: 34px;
  height: 14px;
  display: block;
  background: #9A9999;
  border-radius: 8px;
  transition: background 0.2s ease;
}
.toggle span {
  position: absolute;
  top: 0;
  left: 0;
  width: 20px;
  height: 20px;
  display: block;
  background: white;
  border-radius: 10px;
  box-shadow: 0 3px 8px rgba(154, 153, 153, 0.5);
  transition: all 0.2s ease;
}
.toggle span:before {
  content: "";
  position: absolute;
  display: block;
  margin: -18px;
  width: 56px;
  height: 56px;
  background: rgba(79, 46, 220, 0.5);
  border-radius: 50%;
  transform: scale(0);
  opacity: 1;
  pointer-events: none;
}

.paging
{
	border: #658ab2 solid;color: #3f5872;
	border-width: 0.5px 0px;
	font-size: 13;
	padding: 3px 5px 0px 5px;
	margin: 1 -1 1 -1;
	background-color: #edf0f5;
}

#cbx:checked + .toggle:before {
  background: #658ab2;
}
#cbx:checked + .toggle span {
  background: #3f5872;
  transform: translateX(20px);
  transition: all 0.2s cubic-bezier(0.8, 0.4, 0.3, 1.25), background 0.15s ease;
  box-shadow: 0 3px 8px rgba(79, 46, 220, 0.2);
}
#cbx:checked + .toggle span:before {
  transform: scale(1);
  opacity: 0;
  transition: all 0.4s ease;
}

.dev1
{
	color: black;
    background: lightyellow;
    position: absolute;
    top: 1000;
	left: 30;
	width: 90%;
	height: 1500px;
	padding: 10px;
	overflow: auto;
	font-size: medium;
	
}

.dev2
{
	color: black;
    background: lightyellow;
    position: absolute;
    top: 2600;
	left: 30;
	width: 90%;
	height: 500px;
	padding: 10px;
	overflow: auto;
	font-size: medium;
}

.dev3
{
	color: black;
    background: lightyellow;
    position: absolute;
    top: 3200;
	left: 30;
	width: 90%;
	height: 500px;
	padding: 10px;
	overflow: auto;
	font-size: medium;
}

.dev4
{
	color: black;
    background: lightyellow;
    position: absolute;
    top: 3800;
	left: 30;
	width: 90%;
	height: 500px;
	padding: 10px;
	overflow: auto;
	font-size: medium;
}

.dev5
{
	color: black;
    background: lightyellow;
    position: absolute;
    top: 4400;
	left: 30;
	width: 90%;
	height: 500px;
	padding: 10px;
	overflow: auto;
	font-size: medium;
}

.dev6
{
	color: black;
    background: lightyellow;
    position: absolute;
    top: 5000;
	left: 30;
	width: 90%;
	height: 500px;
	padding: 10px;
	overflow: auto;
	font-size: medium;
}

table.tab_total_add
{
	width: fit-content;
}

table.tab_total_add td
{
	padding-right: 30px;
	font: normal 12px/150% Arial, Helvetica, sans-serif;
	
}









/*
 * dragtable
 * @Version 2.0.14 MOD
 * default css
 */
.dragtable-sortable {
	list-style-type: none;
	margin: 0;
	padding: 0;
	-moz-user-select: none;
	z-index: 10;
}
.dragtable-sortable li {
	margin: 0;
	padding: 0;
	float: left;
	font-size: 1em;
}
.dragtable-sortable table {
	margin-top: 0;
}
.dragtable-sortable th, .dragtable-sortable td {
	border-left: 0px;
}
.dragtable-sortable li:first-child th, .dragtable-sortable li:first-child td {
	border-left: 1px solid #CCC;
}
.dragtable-handle-selected {
	/* table-handle class while actively dragging a column */
}
.ui-sortable-helper {
	opacity: 0.7;
	filter: alpha(opacity=70);
}
.ui-sortable-placeholder {
	-moz-box-shadow: 4px 5px 4px rgba(0,0,0,0.2) inset;
	-webkit-box-shadow: 4px 5px 4px rgba(0,0,0,0.2) inset;
	box-shadow: 4px 5px 4px rgba(0,0,0,0.2) inset;
	border-bottom: 1px solid rgba(0,0,0,0.2);
	border-top: 1px solid rgba(0,0,0,0.2);
	visibility: visible !important;
	/* change the background color here to match the tablesorter theme */
	background: #EFEFEF;
}
.ui-sortable-placeholder * {
	opacity: 0.0;
	visibility: hidden;
}
.table-handle, .table-handle-disabled {
	/* background-image: url(images/dragtable-handle.png); */
	/* background-image: url('data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAIAAAANAQMAAAC5Li2yAAAABlBMVEUAAAAzMzPI8eYgAAAAAnRSTlMAzORBQ6MAAAAOSURBVAjXYwABByyYAQAQWgFBLN2RnwAAAABJRU5ErkJggg=='); */
	background-image: url(data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHdpZHRoPSIyIiBoZWlnaHQ9IjEzIj48cmVjdCBzdHlsZT0iZmlsbDojMzMzO2ZpbGwtb3BhY2l0eTouODsiIHdpZHRoPSIxIiBoZWlnaHQ9IjEiIHg9IjEiIHk9IjIiLz4JPHJlY3Qgc3R5bGU9ImZpbGw6IzMzMztmaWxsLW9wYWNpdHk6Ljg7IiB3aWR0aD0iMSIgaGVpZ2h0PSIxIiB4PSIxIiB5PSI0Ii8+CTxyZWN0IHN0eWxlPSJmaWxsOiMzMzM7ZmlsbC1vcGFjaXR5Oi44OyIgd2lkdGg9IjEiIGhlaWdodD0iMSIgeD0iMSIgeT0iNiIvPjxyZWN0IHN0eWxlPSJmaWxsOiMzMzM7ZmlsbC1vcGFjaXR5Oi44OyIgd2lkdGg9IjEiIGhlaWdodD0iMSIgeD0iMSIgeT0iOCIvPjxyZWN0IHN0eWxlPSJmaWxsOiMzMzM7ZmlsbC1vcGFjaXR5Oi44OyIgd2lkdGg9IjEiIGhlaWdodD0iMSIgeD0iMSIgeT0iMTAiLz48L3N2Zz4=);
	background-repeat: repeat-x;
	height: 13px;
	margin: -4px -10px -0px -10px;
	cursor: move;
}
.table-handle-disabled {
	opacity: 0;
	cursor: not-allowed;
}
.dragtable-sortable table {
	margin-bottom: 0;
}

.tablesorter .filtered {
    display: none;
}


.tablesorter-filter {
  width: 90%;
  height: inherit;
  background-color: #fff;
  border: 1px solid #bbb;
  color: #333;
  -webkit-box-sizing: border-box;
  -moz-box-sizing: border-box;
  box-sizing: border-box;
  -webkit-transition: height 0.1s ease;
  -moz-transition: height 0.1s ease;
  -o-transition: height 0.1s ease;
  transition: height 0.1s ease;
  position: absolute;
  bottom: 2;
  left: 2;
}


.tablesorter-filter-row {
	display: none;
}

.tablesorter-filter-row {
	display: none;
}

.div-seperator
{
	margin: -2px -10px 0px -10px;
    border-top: 0.2px #80808052 solid;
}


caption {
	/* override bootstrap adding 8px to the top & bottom of the caption */
	padding: 0;
}
.ui-sortable-placeholder {
	/* change placeholder (seen while dragging) background color */
	background: #ddd;
}
div.table-handle-disabled {
	/* optional red background color indicating a disabled drag handle */
	background-color: rgba(255,128,128,0.5);
	/* opacity set to zero for disabled handles in the dragtable.mod.css file */
	opacity: 0.7;
}
/* fix cursor */
.tablesorter-blue .tablesorter-header {
	cursor: default;
}
.sorter {
	cursor: pointer;
}



</style>