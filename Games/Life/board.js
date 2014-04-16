(
	function () {
		function board(h,w) {
			this.height = h;
			this.width = w;
			this.grid;
			this.stateColour = new Array();
			this.stateColour[0] = "white";
			this.stateColour[1] = "blue";
			this.stateColour[2] = "red";
			
		}
		board.prototype.getHeight = function (){
			return this.height; 
		}
		board.prototype.setHeight = function (newheight){
			this.height = newheight; 
		}
		board.prototype.getWidth = function (){
			return this.width; 
		}
		board.prototype.setWidth = function (newwidth){
			this.width = newwidth; 
		}
		board.prototype.getGrid = function (){
			return this.grid; 
		}
		board.prototype.getCell = function (x,y){
			return this.grid[y-1][x-1]; 
		}
		board.prototype.newGrid = function (){
			var i = 0;
			var j = 0;
			this.grid = new Array(this.height - 1);
			for ( i = 0; i< this.height; i++){
				this.grid[i] = new Array(this.width - 1);
				for ( j = 0; j < this.width; j++){
					this.grid[i][j] = new cell(j,i,0,this);
					this.grid[i][j].drawCell();
	
				}
			}		
		}

		board.prototype.setStateColour = function(state, colour){
			this.stateColour[state] = colour;			
		}
		board.prototype.getStateColour = function(state){
			return this.stateColour[state];			
		}
		board.prototype.updateGrid = function (){
			var i = 0;
			var j = 0;
			for ( i = 0; i< this.height; i++){
				for ( j = 0; j < this.width; j++){
					this.grid[i][j].drawCell();
				}
			}		
		}
		board.prototype.setGridCell = function (x,y,status,age){
			this.grid[y-1][x-1].setState(status);	
			this.grid[y-1][x-1].setAge(age);
			
		}
		board.prototype.getGridCellState = function (x,y){
			return this.grid[y-1][x-1].getState();	
		}
		board.prototype.getGridState = function (){
			var i = 0;
			var j = 0;
			var s = 0;
			var stateString = "";
			for ( i = 0; i< this.height; i++){
				for ( j = 0; j < this.width; j++){
					s = this.grid[i][j].getState();
					if (s != 0) {
						stateString = stateString + '&x'+(j+1)+'y'+(i+1)+'='+s;
					}
	
				}
			}
			return stateString;
		}
		board.prototype.countNeighbours = function(x,y){
			var i = 0;
			var j = 0;
			var stateCount = new Array();
			stateCount [0] = 0;
			stateCount [1] = 0;
			stateCount [2] = 0;
			/*
			for (i=x-2; i<=x; i++) {
				if (i>=0 && i<= this.width -1) {
					for (j=y-2; j<=y; j++) {
						if (j >= 0 && j <= this.height-1) {
							if (!(i == x-1 && j == y-1)) {
								stateCount [this.grid[j][i].getState()]++;
	
							}
						}
					}
				}
			}
			*/
			var tempi = -1;
			var tempj = -1;
			for (i=x-2; i<=x; i++) {
				for (j=y-2; j<=y; j++) {
					if (!(i == x-1 && j == y-1)) {
							if (i==-1 && j == -1) {
								stateCount [this.grid[15][15].getState()]++;
							}
							else if (i==16 && j == -1) {
								stateCount [this.grid[15][0].getState()]++;
							}
							else if (i==-1 && j == 16) {
								stateCount [this.grid[0][15].getState()]++;
							}
							else if (i==16 && j == 16) {
								stateCount [this.grid[0][0].getState()]++;
							}
							else if (i == -1) {
								stateCount [this.grid[j][15].getState()]++;
							}
							else if (j == -1) {
								stateCount [this.grid[15][i].getState()]++;
							}
							else if (i==16) {
								stateCount [this.grid[j][0].getState()]++;
							}
							else if (j == 16) {
								stateCount [this.grid[0][i].getState()]++;
							}
							else {
								stateCount [this.grid[j][i].getState()]++;
							}
	
					}	
				}
			}
			return stateCount;
		}
		
		board.prototype.iterateGame = function(game) {
			var i = 0;
			var j = 0;	
			var neighbours = 0;
			var neighbours2 = 0;
			var totNeighbours = 0;
			var newgrid = new Array(this.height - 1);
			for ( i = 0; i< this.height; i++){
				newgrid[i] = new Array(this.width - 1);
				for ( j = 0; j < this.width; j++){
					newgrid[i][j] = this.grid[i][j].copy();
					if (game == 1){
						neighbours = this.countNeighbours(j+1,i+1)[1];
						if (neighbours < 2 || neighbours > 3) {
							newgrid[i][j].setState(0);
						}
						else if (neighbours == 3) {
							newgrid[i][j].setState(1);
						}
						else {
							newgrid[i][j].setState(this.grid[i][j].getState());
						}
					}
					else if (game == 2){
						
						neighbours = this.countNeighbours(j+1,i+1)[1];
						neighbours2 = this.countNeighbours(j+1,i+1)[2];
						totNeighbours = (neighbours + neighbours2);
						//newgrid[i][j].setState(this.grid[i][j].getState());
						
						if (newgrid[i][j].getAge() >= 2){
							newgrid[i][j].setState(2);
						}
						if (totNeighbours < 2 || totNeighbours > 3 || newgrid[i][j].getAge() >= 4) {
							newgrid[i][j].setState(0);
						}
						else if (totNeighbours == 3) {
							if (newgrid[i][j].getState() == 0) {
								newgrid[i][j].setState(1);
							}
						}
						else {
						}
					}
					else {
						neighbours = this.countNeighbours(j+1,i+1)[1];
						neighbours2 = this.countNeighbours(j+1,i+1)[2];						
						if (neighbours == 3 && neighbours2 == 0) {
							newgrid[i][j].setState(1);
						}
						else if(neighbours == 0 && neighbours2 == 3) {
							newgrid[i][j].setState(2);
						}
						else if (newgrid[i][j].getState() == 1 &&(neighbours < 2 || neighbours > 3)){
							newgrid[i][j].setState(0);
						}
						else if (newgrid[i][j].getState() == 2 &&(neighbours2 < 2 || neighbours2 > 3)){
							newgrid[i][j].setState(0);
						}
						else if (newgrid[i][j].getState() == 1 && neighbours2 >= 2){
							newgrid[i][j].setState(0);
						}
						else if (newgrid[i][j].getState() == 2 && neighbours >= 2){
							newgrid[i][j].setState(0);
						}
	
	
	
					}
					newgrid[i][j].incrementAge();
				}
			}
			
			this.grid = newgrid;
		}		
		window.board = board;	
	}
)();