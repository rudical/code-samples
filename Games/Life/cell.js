(
	function () {

		function cell(x,y,s,p){
			if (!s) {
				s = 0;
			}
			this.state = s;
			this.xcord = x;
			this.ycord = y;
			this.parent = p;
			this.age = 0;
		}
		cell.prototype.getState = function () {
			return this.state;
		}
		cell.prototype.setState = function (newstate) {
			this.state=newstate;
		}
		cell.prototype.setXCord = function (x) {
			this.xcord = x;
		}
		cell.prototype.getXCord = function () {
			return this.xcord;
		}
		cell.prototype.setYCord = function (y) {
			this.ycord = y;
		}
		cell.prototype.getYCord = function () {
			return this.ycord;
		}
		cell.prototype.setParent = function (p) {
			this.parent = p;
		}
		cell.prototype.getParent = function () {
			return this.parent;
		}
		cell.prototype.setAge = function (a) {
			this.age = a;
		}
		cell.prototype.incrementAge = function () {
			if(this.state == 0) {
				this.age = 0;
			}
			else {
				this.age++;
			}
		}
		cell.prototype.getAge = function () {
			return this.age;
		}
		cell.prototype.copy = function () {
			newCell = new cell(this.xcord, this.ycord, this.state, this.parent);
			newCell.setAge(this.age);
			return newCell;
		}
		cell.prototype.decodeColour = function(colour) {
	
				switch (colour) {
            	case 'red':
                	return "rgb(255,0,0)";
				case 'orange':
					return "rgb(255,165,0)";
				case 'yellow':
					return "rgb(255,255,0)";
                case 'green':
                	return "rgb(0,255,0)";
            	case 'blue':
                	return "rgb(0,0,255)";
				case 'purple':
					return "rgb(160,32,240)";
                case 'black':
                	return "rgb(0,0,0)";
				case 'white':
                	return "rgb(255,255,255)";
				case 'grey':
                	return "rgb(100,100,100)";
                default:
                	return "rgb(0,0,0)";
            }
		}
		cell.prototype.drawCell = function () {

			gameboard.strokeStyle = this.decodeColour('grey');
			if (this.state == 1){
				gameboard.fillStyle = this.decodeColour(this.parent.getStateColour(1));
			}
			else if (this.state == 2){
				gameboard.fillStyle =  this.decodeColour(this.parent.getStateColour(2));
			}
			else {
				gameboard.fillStyle =  this.decodeColour(this.parent.getStateColour(0));
			}
			
			gameboard.fillRect (this.xcord*25, this.ycord*25, (this.xcord*25)+25, (this.ycord*25)+25);
			gameboard.strokeRect (this.xcord*25, this.ycord*25, (this.xcord*25)+25, (this.ycord*25)+25);
			
		}
		window.cell = cell;	
	}
)();