(function(){ // Import GET Vars
	
   document.$_GET = [];
   var urlHalves = String(document.location).split('?');
   if(urlHalves[1]){
      var urlVars = urlHalves[1].split('&');
      for(var i=0; i<=(urlVars.length); i++){
         if(urlVars[i]){
            var urlVarPair = urlVars[i].split('=');
            document.$_GET[urlVarPair[0]] = urlVarPair[1];
         }
      }
   }
})();