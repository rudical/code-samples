<?php
$con = mysql_connect("localhost","company","password");
if (!$con) die('Could not connect: ' . mysql_error());
mysql_query("SET NAMES 'utf8'");
mysql_select_db("rocksmith", $con);


$myFile = "/tmp/rudie/mysql.sql";
$fh = fopen($myFile, 'w') or die("can't open file");


fwrite($fh, 'INSERT INTO extra_words (extra_words) VALUES (\'RUDIE\');'."<BR>");

echo "START<br>";
	flush();
$query = 'SELECT DISTINCT * FROM words_bak';
$result = mysql_query($query);
//if (mysql_num_rows($result) > 0) {
while($row = mysql_fetch_array($result)){
	$query2 = 'SELECT * FROM tmp_words WHERE words_tmp = \''.mysql_escape_string($row['words']).'\'';
	echo $query2." ---- ".mysql_num_rows(mysql_query($query2))."<BR>";
	exit();
	
	//if (mysql_num_rows(mysql_query($query2)) == 0) {
	//	echo 'INSERT INTO extra_words (extra_words) VALUES (\''.mysql_escape_string($row['words_tmp']).'\');'."\n"."<BR>";
	//	flush();
	//	fwrite($fh, 'INSERT INTO extra_words (extra_words) VALUES (\''.mysql_escape_string($row['words_tmp']).'\');'."\n");
	//}
}

fclose($fh);
echo "DONE";
mysql_close($con);
?>