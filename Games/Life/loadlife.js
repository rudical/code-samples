
const FPS = 5;
var board1 = new board(16,16);
var cycles = 0;
var url;
gameStart = 0;
var gameboard;
var interval;
var selectGame = 1;
//var selectState = 1;
$(document).ready(function(){
	init(); // Run stuff when document is ready.
});



function init() { 
	
	$('#start').toggle(function() {
	  	gameStart = 1;
		$('#start').text("Pause Game");
		$('#game').text(gameStart);
		interval = setInterval(draw, 1000 / FPS);
	}, function() {
	 	gameStart = 0;
		clearInterval(interval);
		$('#start').text("Start Game");
		$('#game').text(gameStart);
	});
	$('#clear').click(function() {
		if (gameStart == 1) { 
			$('#start').click();
		}
		board1.newGrid();
		board1.updateGrid();
		cycles = 0;
		$('#cycle').text("Cycles: 0");

	});
	$('#next').click(function() {
		draw();
	});
	$('#showUrl').click(function() {
		urlState();
	});
	
	$("#type1").attr('checked', true);
	$("input[name='gametype']").change(function(){
		selectGame =  parseInt($("input[name='gametype']:checked").val());
		switch(selectGame) {
			case 2:
				
				$("input[name='celltype']").show();
				$("#cell1").show();
				$("#cell2").show();
				$("#cell1").text("New Cells");
				$("#cell2").text("Old Cells");
				break;
				
			case 3:
				
				$("input[name='celltype']").show();
				$("#cell1").show();
				$("#cell2").show();
				$("#cell1").text("Type 1");
				$("#cell2").text("Type 2");
				break;
			default:
				
				$("#type1").attr('checked', true);
				$("input[name='celltype']").hide();
				$("#cell1").hide();
				$("#cell2").hide();
			
		}
		urlState();
		
	});
	$("input[name='celltype']").change(function(){
		var selectState =  $("input[name='celltype']:checked").val();
		var selectColour =  board1.getStateColour(selectState);
		$("#"+selectColour).attr('checked', true);
		$("#state").text(selectColour);
		urlState();
	});
	$("input[name='cellcolour']").change(function(){
		var selectState =  $("input[name='celltype']:checked").val();
		var selectColour =  $("input[name='cellcolour']:checked").val();
		board1.setStateColour(selectState,selectColour);
		board1.updateGrid();
		urlState();
		
	});

	gameboard = $('#gameboard')[0].getContext("2d");	
	board1.newGrid();
	getInit();
	
	$('#gameboard').bind('click',function(event) {
		posx = event.pageX - $('#gameboard').offset().left;
		posy = event.pageY - $('#gameboard').offset().top;
		gridx = Math.floor(posx/25)+1;
		gridy = Math.floor(posy/25)+1;
		var setStateCell = 0;
		if (gameStart == 0) {
			if (board1.getCell(gridx,gridy).getState() != 0) {
				board1.setGridCell(gridx,gridy,0,0);
			}
			else {
				setStateCell = $("input[name='celltype']:checked").val();
				if (setStateCell == 1) {
					board1.setGridCell(gridx,gridy,setStateCell,0);
				}
				else {
					board1.setGridCell(gridx,gridy,setStateCell,2);
				}
				
			}
			board1.updateGrid();
		}
		urlState();
	});

} 
function getInit() {
	var i,j;
	var s = "";// ="";
	var sint;
	var g = document.$_GET['g'];
	var c1 = document.$_GET['c1'];
	var c2 = document.$_GET['c2'];
	if ( typeof(c1) == 'undefined' || c1 == "") {
		c1 = 'blue';
	}
	if ( typeof(c2) == 'undefined' || c2 == "") {
		c2 = 'red';
	}
	$("#"+c1).attr('checked', true);
	$("#game1").attr('checked', true);
	$("input[name='celltype']").hide();

	$("#cell1").hide();
	$("#cell2").hide();
	if (typeof(g) != 'undefined' && g != "") {
		selectGame = parseInt(g);
		
		if (selectGame == 2) {
			$("#game2").attr('checked', true);
			$("input[name='celltype']").show();
			$("#cell1").show();
			$("#cell2").show();
			$("#cell1").text("New Cells");
			$("#cell2").text("Old Cells");
		}
		else if(selectGame == 3) {
			$("#game3").attr('checked', true);
			$("input[name='celltype']").show();
			$("#cell1").show();
			$("#cell2").show();
			$("#cell1").text("Type 1");
			$("#cell2").text("Type 2");
		}
		else {
			$("#game1").attr('checked', true);
			$("#type1").attr('checked', true);
			$("input[name='celltype']").hide();
	
			$("#cell1").hide();
			$("#cell2").hide();
		}
		
	}
	else {
		selectGame = 1;
		$("#game1").attr('checked', true);
		$("#type1").attr('checked', true);
		$("input[name='celltype']").hide();

		$("#cell1").hide();
		$("#cell2").hide();
	}
	//document.write(c1);
	
	board1.setStateColour(1,c1);
	board1.setStateColour(2,c2);
	for (i = 1; i<= 16; i++) {
		for (j = 1; j<= 16; j++) {
			s = document.$_GET['x'+i+'y'+j];
			if (typeof(s) == "undefined") {
				sint = 0;
			}
			else {
				sint = parseInt(s);
			}
			
			if(sint != 0) {
				if (sint == 2 && g == 2) {
					board1.setGridCell(i,j,sint,2);
				}
				else {
					board1.setGridCell(i,j,sint,0);
				}
			}
		}
	}
	board1.updateGrid();
}
function draw() {
	  board1.iterateGame(selectGame);
	  board1.updateGrid();
	  cycles++;
	  $('#cycle').text("Cycles: " + cycles);
}
function urlState() {
	url = (String(document.location).split('?'))[0];
	var getvar = board1.getGridState();
	var urlstr = url+"?g="+selectGame+"&c1="+board1.getStateColour(1)+"&c2="+board1.getStateColour(2)+getvar;
	$('#url').text(urlstr);
	$('#url').attr('href',urlstr);
}
