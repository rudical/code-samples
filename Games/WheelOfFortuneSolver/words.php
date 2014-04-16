
<?php 
$no_letters=htmlentities($_GET['no_letters']);
$word=htmlentities($_GET['word']);

if (preg_match('/[^A-Z.\']+/', $word) || preg_match('/[^A-Z.]+/', $no_letters) || empty($word)) exit(0);
$no_letters = str_replace ( "." , "" , $no_letters);
if (!empty($no_letters)) {
	$no_letter_arr = preg_split('//', $no_letters, -1, PREG_SPLIT_NO_EMPTY);
	$no_letters = "";
	foreach($no_letter_arr as $char) {
		$char = strtoupper($char);
		if (strpos($word,$char) === false){
			$no_letters .= ' AND words NOT LIKE "%'.$char.'%"';
		}
		
	}
}

//if (!empty($no_letters)) $no_letters=preg_replace('/(\w)/i', ' AND words NOT LIKE "%$1%"', strtoupper($no_letters));

if (strpos($word,"'") == false) {
	$no_letters .= ' AND words NOT LIKE "%'."\\\'".'%"'; 
}
else {
	$word=str_replace('\'',"\\'",$word);
}
$regex = '^'.$word.'$';
$con = mysql_connect("127.0.0.1","company","password");
if (!$con) die('Could not connect: ' . mysql_error());
mysql_query("SET NAMES 'utf8'");
mysql_select_db("rocksmith", $con);

$query = 'SELECT DISTINCT * FROM words_bak WHERE words REGEXP "'.$regex.'" '.$no_letters. ' ORDER BY freq DESC';
//echo '<span class="pick_word">'.$query.'</span><br />';
echo '<option class="original_word" value="'.$word.'" >'. str_replace ( "." , "_" , $word).'</option>';
$result = mysql_query($query);
//if (mysql_num_rows($result) == 0) {
//	echo '<option class="word_not_found" value=".'.$word.'" >'.str_repeat('?',strlen($word)).'</option>';

//}
//else {
if (mysql_num_rows($result) > 0) {
  while($row = mysql_fetch_array($result)){
	echo '<option class="pick_word" value="'.$row['words'].'" >'.$row['words'].'</option>';
	
  }
}
mysql_close($con);

?>
