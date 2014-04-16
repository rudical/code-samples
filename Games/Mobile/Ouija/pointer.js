const FPS = 20;
var angle = 0;
var centerx=108;
var centery=171;
var ponterx = 65;
var pontery = 0;
var xDirection = 1;
var yDirection = 1;
var image = new Image();
image.src = "./ouija_pointer.png";
var canvas = null;
var context2D = null;

function init(){
	canvas = document.getElementById('pointer');
	context2D = canvas.getContext('2d');
	context2D.save(); 
	setInterval(draw, 1000 / FPS);
}

function draw()
{
	context2D.restore();
	context2D.clearRect(0,0, 342, 342);
	rotate(angle);
}
function rotate(p_deg) {
	context2D.save();
	context2D.translate(centerx + ponterx, centery + pontery); 
	context2D.rotate(p_deg * Math.PI / 180);
	context2D.drawImage(image, (-centerx), (-centery));
	context2D.restore();
};
