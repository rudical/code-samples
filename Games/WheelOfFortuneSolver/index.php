<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<LINK href="style.css" rel="stylesheet" type="text/css">
<script type="text/javascript" src="killkeystrokes.js"></script>
<script type="text/javascript" src="jquery-1.4.js"></script>
<script type="text/javascript" src="wheel.js"></script>
<title>Wheel of Fortune Solver</title>
</head>
<body>
<script type="text/javascript" language="JavaScript">
	var AjaxOn=true;//false;
	
	$(document).ready(function() {
		$(".words").hide();
	
		$(".board_letter").keydown(function(e) {
			showword(e.keyCode,getIdNum($(".board_letter:focus")));
		});
	
		$(".board_letter").blur(function () {
			$(this).removeClass('OnFocus');
		});
		$(".board_letter").focus(function () {
			$(this).addClass('OnFocus');
		});
		$(".board_letter").click(function () {
		  //alert(getIdNum(this));
		  //selectLetter(getIdNum(this));
		});
		$(".board_letter").dblclick(function () {
			toggleLetter(getIdNum(this));
		});
		$(".words").change(function () {
			 fillWord($(this).val(), getIdNum(this));
		});

	});
	
	<?php 
	function ShowCell($i) {
		return '	<td  tabindex="'.$i.'" class="board_letter" id="td_letter_'.$i.'" name="'.$i.'" >
	<span id="span_letter_'.$i.'"></span>
	<input type="hidden" id="letter_'.$i.'" value="" />
		</td>';
	}
	?>
</script>
<div id="wheel_bg">
    <div id="board_content">
        <div id="wheel_board_div">
            <table  id="wheel_board">
              <tr>
                <td id="td_top_left"></td>
            <?php for ($i=1; $i <= 12; $i++) {
                echo ShowCell($i);
            }?>
                <td id="td_top_right"></td>
              </tr>
              <tr>
                <?php 
            for ($i=13; $i <= 26; $i++) {
                echo ShowCell($i);
            }?>
              </tr>
              <tr>
                <?php 
            for ($i=27; $i <= 40; $i++) {
                echo ShowCell($i); 
                }?>
              </tr>
              <tr>
                <td id="td_bottom_left"></td>
                <?php 
            for ($i=41; $i <= 52; $i++) {
                echo ShowCell($i);
            }?>
                <td id="td_bottom_right"></td>
              </tr>
            </table>
        </div>
        
        <div id="sideinfo">
          
          <div id="instructions">
          	<span>
          		Use can use a "." to turn a square white.
                 
            </span>
          </div>
          <div id="no_in_puzzle">
          		<span>Letters not in puzzle.</span>
          		<input id="no_str" name="no_letter" type="text" size="10" maxlength="200" value="" onkeyup="parseLetters();" /><br />
    				
          </div>
          <div id="other_options">
          	<INPUT TYPE="button" VALUE="Clear Board" onClick='clearAll()' id="clearButton">
          </div>
          
        </div>
        <div class="clear"></div>
        </div>
  		<div class="clear"></div>
        <div align="center"  id="list_words_container"><div id="list_words" width=100%">
        <?php 
        for ($i=1; $i <= 20; $i++) {
         // echo '<div class="words" id="word_'.$i.'"></div>';
          echo '<select class="words" id="word_'.$i.'" name="'.$i.'" size="20"></select>';
         }
    
         ?>
         </div>
        </div>
</div>  

</body>
</html>