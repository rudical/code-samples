(
	function () {

		function logic(b,g,w){
			this.game = g;
			this.board = b;
			this.wrapping = w;
		}

		logic.prototype.iterate = function () {

				
			var i = 0;
			var j = 0;	
			var neighbours = 0;
			var newgrid = new Array(this.height - 1);
			for ( i = 0; i< this.height; i++){
				newgrid[i] = new Array(this.width - 1);
				for ( j = 0; j < this.width; j++){
					
					newgrid[i][j] = this.board[i][j].copy();
					neighbours = this.countNeighbours(j+1,i+1)[1];
					if (neighbours < 2 || neighbours > 3) {
						newgrid[i][j].setState(0);
					}
					else if (neighbours == 3) {
						newgrid[i][j].setState(1);
					}
					else {
						newgrid[i][j].setState(this.getGridCellState(j+1,i+1));
					}
				}
			}
			return newgrid;
		}
		window.logic = logic;	
	}
)();