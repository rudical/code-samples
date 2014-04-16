<?php

header("Content-type: text/plain");

$html = "";

$file="list2.htm";
if ($fp = fopen($file, 'r')) {
	//if ($fp = fopen('wiki_words/1-1000.htm', 'r')) {
	   $content = '';
	   // keep reading until there's nothing left
	   while ($line = fread($fp, 1024)) {
		  $content .= $line;
	   }

	$html = $html.$content;
	

} else {
   exit("error");
}


		
$dom = new domDocument;
$dom->loadHTML($html);
$dom->preserveWhiteSpace = false;
$tables = $dom->getElementsByTagName('table');

$rows = $tables->item(0)->getElementsByTagName('tr');

$myFile = "/tmp/rudie/mysql.sql";
$fh = fopen($myFile, 'w') or die("can't open file");





$con = mysql_connect("localhost","company","password");
if (!$con) die('Could not connect: ' . mysql_error());
mysql_select_db("rocksmith", $con);
flush();
foreach ($rows as $row) {
	$cols = $row->getElementsByTagName('td');
	
	$query = 'SELECT DISTINCT * FROM words WHERE words = \''.mysql_escape_string(strtoupper( $cols->item(1)->nodeValue)).'\'';
	$result = mysql_query($query);
	if (mysql_num_rows($result) == 0) {
		$query = "INSERT INTO words (words, freq) VALUES ('".mysql_real_escape_string(strtoupper($cols->item(1)->nodeValue))."', '".(61000-($cols->item(0)->nodeValue))."')";
		fwrite($fh, $query.";\n");
		echo $query."\n";
		flush();
	}
}
fclose($fh);

mysql_close($con);



?>


