<html>  
  <head>  
  <LINK href="style.css" rel="stylesheet" type="text/css">

   <script type="application/javascript">  
 function draw() {  
   var canvas = document.getElementById("canvas");  
   var ctx = canvas.getContext("2d");  
   var block_width=40;
   var block_height=60;
   ctx.lineWidth   = 4;
   ctx.fillStyle   = 'rgb(0,0,0)';
   ctx.fillStyle = "rgb(70,200,0)";
   //first row
   for (i=1; i<=2; i++){
	// spacing = block_width+ctx.lineWidth;
    // ctx.fillRect ((spacing*i), 5, (spacing*i)+spacing, 5+block_height);  
    // ctx.strokeRect((spacing*i), 5, (spacing*i)+spacing, 5+block_height);
   }
   
        ctx.fillRect (40, 5, 84, 5+block_height);  
     ctx.strokeRect(40, 5, 84, 5+block_height);
	 
	      ctx.fillRect (84, 5, 128, 5+block_height);  
     ctx.strokeRect(84, 5, 128, 5+block_height);
 }  
   </script>  
  </head>  
  <body onload="draw()">  
    <canvas id="canvas" width="800" height="300"></canvas>  
  </body>  
 </html> 