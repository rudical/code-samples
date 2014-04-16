// Position Variables
var x = -161;
var y = -200;

// Speed - Velocity
var vx = 0;
var vy = 0;

// Acceleration
var ax = 0;
var ay = 0;


var angle = 0;

var delay = 10;
var vMultiplier = 0.1;
var maxspeed = 2;
var minspeed = .2;

var arot=0;
var rotv = 0;
var rot_t = 0;
var newvy = 0;
var newvx = 0;

var ghostx=0;
var ghosty=0;
var ghostxdir =1;
var ghostydir =1;
var ghostrot = 1;


function getAngle(dx,dy) {
		if (dx == 0 && dy >0) {
			angle=90;
		}
		else if (dx ==0 && dy < 0) {
			angle=-90;
		}
		else if (dy == 0 && dx > 0) {
			angle=0;
		}
		else if (dy == 0 && dx < 0) {
			angle = 180;
		}
		else if (dx > 0 && dy > 0) {
			angle =(180/Math.PI) *  Math.atan(dy/dx);
		}
		else if (dx < 0 && dy > 0) {
			angle = 180-((180/Math.PI) * Math.atan(dy/(-dx)));
		}
		else if (dx > 0 && dy < 0) {
			angle = -((180/Math.PI) * Math.atan((-dy)/dx));
		}
		else if (dx < 0 && dy < 0) {
			angle = -180+((180/Math.PI) * Math.atan((-dy)/(-dx)));
		}
		angle -= 90;
}
function init()
{
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
}


