
Object.prototype.clone = function () {var o = new Object(); for (var property in this) {o[property] = typeof (this[property]) == 'object' ? this[property].clone() : this[property]} return o}
Array.prototype.clone = function () {var a = new Array(); for (var property in this) {a[property] = typeof (this[property]) == 'object' ? this[property].clone() : this[property]} return a}

var Words=new Array;
var Exclude="";



function parse_sen(str) {
  str = str.replace(/([^a-zA-Z.\'\"\?\s])/g, "");
  str = str.replace(/([\"\?])/g, " ");
  str = str.replace(/([\s]+)/g, " ");
  str = str.replace(/([\s])$/g, "");
  str = str.replace(/^([\s]*)/g, "");
  str = str.toUpperCase();
  arr=new Array();
  if (str != ""){
    arr=str.split(" ");
  }
  return arr;
}
function parse_ex(str) {
  str = str.replace(/([^a-zA-Z\s])/g, "");
  str = str.toUpperCase();
  return str;
}

function getIdNum(el){
 return $(el).attr('name');
}

function do_ajax(div_num, str, ex_str) {
   var getXHR = function() {
   try {
      http = new XMLHttpRequest;
	  getXHR = function() {
	    return new XMLHttpRequest;
	  };
    }
    catch(e) {
      var msxml = [
        'MSXML2.XMLHTTP.3.0',
        'MSXML2.XMLHTTP',
        'Microsoft.XMLHTTP'
      ];
      for (var i=0, len = msxml.length; i < len; ++i) {
        try {
          http = new ActiveXObject(msxml[i]);
          getXHR = function() {
            return new ActiveXObject(msxml[i]);
          };
          break;
        }
        catch(e) {}
      }
    }
    return http;
  }
  
  var get_file = "words.php?word="+str+"&no_letters="+ex_str;
  //alert(get_file);
  var http = getXHR();
  http.open('GET',get_file, true);
  http.onreadystatechange = function () { 
    if (http.readyState == 4 && http.status == 200) {
 	  $("#word_"+div_num).html(http.responseText);
	}
  };
  http.send(null);
}
function clearAll() {
  var i=0;
  for (i=1; i<=52; i++) {
	OnLetter(i, 2);
 }
 $("#no_str").val("");
 parseLetters();
}

function parse_excluded_words(num_arry, words_arr, ex_glob_letters){
	var i=0;
	var j=0;
	var ex_letters = '';
	var ex_words = new Array(num_arry);
	for (i=0; i<num_arry; i++){
		ex_words[i]='';
	}
	for (i=0; i<num_arry; i++){
		ex_letters = words_arr[i];
		
		for (j=0; j<num_arry; j++) {
			if (j != i) {
				
				ex_words[j] = ex_words[j] + ex_letters;
				
			}
		}
		
		ex_words[i] = parse_ex(ex_words[i] + ex_glob_letters);
		
	}

	return ex_words;
}
function get_lists(str) {
  ex_str = $("#no_str").val();
  var i = 0;
  var oldWords=Words.clone();
  var newWords=parse_sen(str).clone();

  var oldExclude=Exclude;
  var newExclude=parse_ex(ex_str);
  var excludeOtherLetters;
  var newWords_length=newWords.length;
  var oldWords_length=oldWords.length;
  
  var ExcludeWords=new Array;
  ExcludeWords = parse_excluded_words(newWords_length,newWords,newExclude);
  
  
 // if (newExclude != oldExclude) {
	for (i=0; i < newWords_length; i++) {
		//alert(parse_ex(ExcludeWords[i]));
		do_ajax(i+1,newWords[i],parse_ex(ExcludeWords[i]));
		//do_ajax(i+1,newWords[i], newExclude);
    }
 // }
 /* else {
	  for (i=0; i < newWords_length; i++) {
		  if (i < oldWords_length ) {
			  if (newWords[i] != oldWords[i]) {  
				  do_ajax(i+1,newWords[i], newExclude);
			  }
		  }
		  else {
			do_ajax(i+1,newWords[i], newExclude);
		 }
	  }
  }*/
  for (i=1; i<=20; i++) {
	  if (i<=newWords_length) {
		$("#word_"+i).show();
	  }
	  else {
		$("#word_"+i).hide();
	  }
  }
  Words=newWords.clone();
  Exclude=newExclude;
}

function checkEnds(id) {
	var i = parseInt(id);
	if (i == 12 || i == 26 || i == 40) {
		return " ";
	}
	return "";
}

function showword(keyCode,letter_id){
  //  var keyCode = event.keyCode;
    var letter_char; 
	var newdir=0;
	if (keyCode >= 65 && keyCode <= 90) {
		letter_char = String.fromCharCode(keyCode);
		$("#letter_"+letter_id).val(letter_char + checkEnds(letter_id));
		$("#span_letter_"+letter_id).text(letter_char);
		OnLetter(letter_id, 1);
		newdir = moveFocus('right',letter_id);
		$("#td_letter_"+letter_id).removeClass('OnFocus');
		$("#td_letter_"+newdir).addClass('OnFocus');
		$("#td_letter_"+newdir).focus();
	}
	//delete
	else if (keyCode == 46) {
		OnLetter(letter_id, 2);
	}
	//backspace
	else if (keyCode == 8) {
		OnLetter(letter_id, 2);
		newdir = moveFocus('left',letter_id);
		$("#td_letter_"+letter_id).removeClass('OnFocus');
		$("#td_letter_"+newdir).addClass('OnFocus');
		$("#td_letter_"+newdir).focus();
	}
	//space
	else if (keyCode == 32) {
		OnLetter(letter_id, 2);
		newdir = moveFocus('right',letter_id);
		$("#td_letter_"+letter_id).removeClass('OnFocus');
		$("#td_letter_"+newdir).addClass('OnFocus');
		$("#td_letter_"+newdir).focus();
	}
	//up
	else if (keyCode == 38) {
		newdir = moveFocus('up',letter_id);
		$("#td_letter_"+letter_id).removeClass('OnFocus');
		$("#td_letter_"+newdir).addClass('OnFocus');
		$("#td_letter_"+newdir).focus();
	}
	//down
	else if (keyCode == 40) {
		newdir = moveFocus('down',letter_id);
		$("#td_letter_"+letter_id).removeClass('OnFocus');
		$("#td_letter_"+newdir).addClass('OnFocus');
		$("#td_letter_"+newdir).focus();
	}
	//left
	else if (keyCode == 37) {
		newdir = moveFocus('left',letter_id);
		$("#td_letter_"+letter_id).removeClass('OnFocus');
		$("#td_letter_"+newdir).addClass('OnFocus');
		$("#td_letter_"+newdir).focus();
	}
	//right
	else if (keyCode == 39) {
		newdir = moveFocus('right',letter_id);
		$("#td_letter_"+letter_id).removeClass('OnFocus');
		$("#td_letter_"+newdir).addClass('OnFocus');
		$("#td_letter_"+newdir).focus();
	}
	//esc
	else if (keyCode == 27) {
	}
	//tab
	else if (keyCode == 9){
	}
	//.
	else if (keyCode == 190){
		$("#letter_"+letter_id).val("."+ checkEnds(letter_id));
		$("#span_letter_"+letter_id).text("");
		OnLetter(letter_id, 1);
		newdir = moveFocus('right',letter_id);
		$("#td_letter_"+letter_id).removeClass('OnFocus');
		$("#td_letter_"+newdir).addClass('OnFocus');
		$("#td_letter_"+newdir).focus();
	}
	//-
	else if (keyCode == 109){

		$("#span_letter_"+letter_id).text("-");
		OnLetter(letter_id, 1);
		newdir = moveFocus('right',letter_id);
		$("#td_letter_"+letter_id).removeClass('OnFocus');
		$("#td_letter_"+newdir).addClass('OnFocus');
		$("#td_letter_"+newdir).focus();
	}
	//'
	else if (keyCode == 222){
		$("#letter_"+letter_id).val("'"+ checkEnds(letter_id));
		$("#span_letter_"+letter_id).text("'");
		OnLetter(letter_id, 1);
		newdir = moveFocus('right',letter_id);
		$("#td_letter_"+letter_id).removeClass('OnFocus');
		$("#td_letter_"+newdir).addClass('OnFocus');
		$("#td_letter_"+newdir).focus();
	}
	if ( !AjaxOn ) {
		if (event.keyCode == 13) parseLetters();
	}
	else {
		parseLetters();
	}
}

function parseLetters() {
var i = 0;
	var str_of_letters = "";
	var str_of_letter = ""
	for (i = 1; i <= 52; i++) {
	  str_of_letter = $("#letter_"+i).val();
	  if (str_of_letter == "") {
		  str_of_letters = str_of_letters + " ";
	  }
	  else {
		  str_of_letters = str_of_letters + str_of_letter;
	  }
	  
	}

	get_lists(str_of_letters);
}

function OnLetter(letter_id, on_cell){

	letter = $("#letter_"+letter_id).val();
	if (on_cell == 1) {
		
		$("#td_letter_"+letter_id).css('backgroundColor', 'white');
	}
	else if (on_cell == 2) {
	   	$("#letter_"+letter_id).val("");
		$("#td_letter_"+letter_id).css('backgroundColor', 'green');
		$("#span_letter_"+letter_id).text("");
	}

}

function toggleLetter(letter_id){

	letter = $("#letter_"+letter_id).val();
	if (letter == "") {
		$("#letter_"+letter_id).val(".");
		$("#td_letter_"+letter_id).css('backgroundColor', 'white');
	}
	else {
		$("#letter_"+letter_id).val("");
		$("#td_letter_"+letter_id).css('backgroundColor', 'green');
		$("#span_letter_"+letter_id).text("");
	}
	var i =0;

}


function moveFocus(dir, id) {
	var newdir=0
	var i = parseInt(id);
	switch (dir){
		case 'up':
			if ( i >= 1 && i <= 12)  {
				newdir = 40 + i;
			}
			else if ((i >= 14 && i <= 25) || (i >= 41 && i <= 52 )){
				newdir = i-13;
			}
			else if (i >= 27 && i <= 40) {
				newdir = i-14;
			}
			else if (i == 13 || i == 26) {
			  newdir = i+14;
			}
			break;
		case 'down':
			if (( i >= 1 && i <= 12) || (i >= 28 && i <= 39)) {
				newdir = i+13;
			}
			else if (i >= 13 && i <= 26){
				newdir = i+14;
			}
			else if (i >= 41 && i <= 52) {
				newdir = i-40;
			}
			else if (i == 27 || i == 40) {
			  newdir = i-14;
			}
			break;
		case 'right':
			if (i >= 52) {
				newdir = 1;
			}
			else {
				newdir = i+1;
			}
			break;
		case 'left':
			if (i <= 1) {
				newdir = 52;
			}
			else {
				newdir = i-1;
			}
			break;
	}
	return newdir;
}

function fillWord(str, id) {
	var i = 0;
	id = parseInt(id);
    var pos=new Array;
	var char;
	var w=0;
	var nextWord = false;
	for (i=1; i <= 52; i++) {
		//letter = $("#span_letter_"+i).text();
		letter = $("#letter_"+i).val();
		if ($("#td_letter_"+i).css("backgroundColor") != "white") {
		  letter = " ";
		}

		if ((nextWord==false) && (letter.match(/[A-Z\-\'\.][\s]*/) )) {
	
			pos[w]=i;
			w++;
			nextWord=true;
		}
		else if (letter.match(/[\s]+/)) {
			nextWord=false;
		}
	}

	for (i=0; i < str.length; i++) {
		char = str.charAt(i);
	  	$("#letter_"+(pos[(id-1)] + i)).val(char);
		//if (char=".") char="";
		if (char == ".") {
		  $("#span_letter_"+(pos[(id-1)] + i)).text("");
		}
		else {
			$("#span_letter_"+(pos[(id-1)] + i)).text(char);
		}
	}
	parseLetters();
}