<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title>Ouiji Board</title>
<meta name="viewport" content="width=device-width,user-scalable=no" />
<script src="jquery.1.3.2.min.js" type="text/javascript"></script>
<script src="pointer.js" type="text/javascript"></script>
<script src="board.js" type="text/javascript"></script>

<LINK href="quija.css" rel="stylesheet" type="text/css">

<script type="text/javascript">


$(document).ready(function() {
	document.body.setAttribute("orient", 'portait');
	init();
	
	if (window.DeviceMotionEvent==undefined) {
		document.getElementById("no").style.display="block";
		document.getElementById("yes").style.display="none";
		$("body").css("background-color","white");
		$("body").css("background-image","url()");
	
	} else {
		window.ondevicemotion = function(event) {
			ax = ((event.accelerationIncludingGravity.x-.15) * ghostxdir) + ghosty;
			ay = ((event.accelerationIncludingGravity.y+.15) * ghostydir) + ghostx;
		}
		window.ondeviceorientation = function(event) {
			//event.alpha
			//event.beta
			//event.gamma
		}
		
	
		setInterval(function() {
			newvy = vy + -(ay);
			newvx = vx + ax
	
			
			if (Math.abs(newvy) < maxspeed && Math.abs(newvy) > minspeed) {
				vy = newvy
			}
			else if (Math.abs(newvy) > maxspeed){
				vy = (newvy/Math.abs(newvy))*maxspeed
			}
			else {
				vy = 0;
			}
			
			if (Math.abs(newvx) < maxspeed && Math.abs(newvx) > minspeed) {
				vx = newvx
			}
			else if (Math.abs(newvx) > maxspeed){
				vx = (newvx/Math.abs(newvx))*maxspeed
			}
			else {
				vx = 0;
			}
	
			getAngle(ax,-ay);
	
			var board = document.getElementById("body");
			y = parseInt(y + vy );
			x = parseInt(x + vx );
			//$("#rot").html("x: "+x+" y:"+y);
			if (x<-450) { 
				x =-450; 
				vx = 0; 
			}
			if (y<-368) { 
				y =-368; 
				vy = 0; 
			}
			if (x>450-document.documentElement.clientWidth) { 
				x = 450-document.documentElement.clientWidth; 
				vx = 0; 
			}
			if (y>(368)-document.documentElement.clientHeight) { 
				y = (368)-document.documentElement.clientHeight; 
				vy = 0; 
			}
	
			$("body").css("background-position", ((-1)*(x+360))+'px '+((-1)*(y+250))+'px');
			
		}, delay);
	} 
});
</script>
</head>

<body>
<div id="content">
    <div id="yes">
 		<div id="rot"></div>
		<canvas id="pointer" width="342" height="342"></canvas>
    </div>
    <div id="no">
    	Your browser does not support Device Orientation and Motion API. Try this sample with iPhone, iPod or iPad with iOS 4.2+.    
    </div>
</div>
</body>
</html>
