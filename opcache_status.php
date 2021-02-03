<?php


if (isset($_GET['opcache_reset']) and $_GET['opcache_reset'] == 1)
{
	opcache_reset();
	echo "<h2>OP Cache Was Reseted For This Server...</h2><br><br>";
}
else
{
	echo "<h2>Reset The Cache With:   URL PARAM   opcache_reset=1</h2><br><br>";
}

echo "<h1>OP Cache Status:</h1>\n";
echo "<pre>";
print_r(opcache_get_status());
echo "</pre>";




?>
