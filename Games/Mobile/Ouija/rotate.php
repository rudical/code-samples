<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
	"http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">

<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="fr" lang="fr">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<title>rotate()</title>
	
	<style type="text/css" media="screen">
	canvas { 
		border: 1px solid black;

	}
	
	</style>


<script type="text/javascript">
const FPS = 20;
var angle = 0;
var centerx=108;//108;
var centery=171;
var x = 63;
var y = 0;
var xDirection = 1;
var yDirection = 1;
var image = new Image();
image.src = "./ouija_pointer.png";
var canvas = null;
var context2D = null;

window.onload = init;

function init()
{
	canvas = document.getElementById('canvas');
	context2D = canvas.getContext('2d');
	//context2D.translate(centerx, centery);
	context2D.save(); 
	//context2D.drawImage(image, -centerx, -centery);
	//context2D.fillRect(0,0,-2,2);

	
	





//draw();
	setInterval(draw, 1000 / FPS);
}

function draw()
{
    if (angle >= 360) angle = 0;
	angle += 20;
	context2D.restore();
	//context2D.translate(-centerx , -centery);
	context2D.clearRect(0,0, 342, 342);
	//context2D.restore();
	rotate(angle);
	
}
function rotate(p_deg) {
	context2D.save();
	context2D.translate(centerx + x, centery + y); 
	context2D.rotate(p_deg * Math.PI / 180);
	context2D.drawImage(image, (-centerx), (-centery));
	context2D.fillRect(0,0,-2,2);
	context2D.restore();
};



</script>

</head>

<body>

<p>

	<canvas id="canvas" width="342" height="342"></canvas>
</p>


</body>
</html>



