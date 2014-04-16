<?php
phpinfo();
exit();
header("Content-type: text/plain");
$page=1;
//$page=$page+1000;
$html = "";
/*
for ($page=1; $page<=9001; $page=$page+1000) {
	//exit ('wiki_words/'.$page.'-'.($page+1000).'.htm');
	$page2=$page+999;
	 $file="wiki_words/$page-$page2.htm";
	if ($fp = fopen($file, 'r')) {
		//if ($fp = fopen('wiki_words/1-1000.htm', 'r')) {
		   $content = '';
		   // keep reading until there's nothing left
		   while ($line = fread($fp, 1024)) {
			  $content .= $line;
		   }
		
		$findme    = "<table>\n<tbody><tr>\n<td><b>rank</b></td>\n<td><b>word</b></td>\n<td><b>count</b></td>\n</tr>\n";
		$pos = strlen($findme)+stripos($content, $findme);
		$content = substr ( $content , $pos );

		$findme    = "</tbody>";
		$pos2 = stripos($content, $findme);
		$content = substr ( $content , 0, $pos2 );
		$html = $html.$content;
		

	} else {
	   exit("error");
	}
}
*/


for ($page=30001; $page<=40000; $page=$page+2000) {
	//exit ('wiki_words/'.$page.'-'.($page+1000).'.htm');
	$page2=$page+1999;
	 $file="wiki_words/$page-$page2.htm";
	if ($fp = fopen($file, 'r')) {
		//if ($fp = fopen('wiki_words/1-1000.htm', 'r')) {
		   $content = '';
		   // keep reading until there's nothing left
		   while ($line = fread($fp, 1024)) {
			  $content .= $line;
		   }
		
		$findme    = "<table>\n<tbody><tr>\n<td><b>rank</b></td>\n<td><b>word</b></td>\n<td><b>count</b></td>\n</tr>\n";
		$pos = strlen($findme)+stripos($content, $findme);
		$content = substr ( $content , $pos );

		$findme    = "</tbody>";
		$pos2 = stripos($content, $findme);
		$content = substr ( $content , 0, $pos2 );
		$html = $html.$content;
		
	
	} else {
	   exit("error");
	}

}


$html="<html><table>$html</table></html>";
		
 $dom = new domDocument;

/*** load the html into the object ***/
$dom->loadHTML($html);

/*** discard white space ***/
$dom->preserveWhiteSpace = false;

/*** the table by its tag name ***/
$tables = $dom->getElementsByTagName('table');

/*** get all rows from the table ***/
$rows = $tables->item(0)->getElementsByTagName('tr');



$con = mysql_connect("localhost","company","password");
if (!$con) die('Could not connect: ' . mysql_error());
mysql_select_db("rocksmith", $con);
/*** loop over the table rows ***/
foreach ($rows as $row)
{
	/*** get each column by tag name ***/
	$cols = $row->getElementsByTagName('td');
	/*** echo the values ***/
	$query = "INSERT INTO words_bak (words, freq) VALUES ('".mysql_real_escape_string($cols->item(1)->nodeValue)."', '".(40001-$cols->item(0)->nodeValue)."')";
	//mysql_query($query);
	echo "$query;\n";

}






 

mysql_close($con);

/*
DROP TABLE `words_bak`;
CREATE TABLE `rocksmith`.`words_bak` (
`words` VARCHAR( 255 ) NOT NULL ,
`freq` INT NOT NULL DEFAULT '0'
) ENGINE = MYISAM ;
*/

?>


