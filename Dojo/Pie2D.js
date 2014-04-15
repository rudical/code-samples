dojo.require('dojox.charting.Chart2D');
dojo.require('dojox.charting.widget.Chart2D');
dojo.require('dojox.charting.themes.Julie');
dojo.provide("custom.charting.Pie2D");
dojo.require("dojox.charting.widget.Legend");
dojo.require("custom.charting.themes.availabilityPie");
dojo.declare("custom.charting.Pie2D",null,
{
	constructor : function(url, id,legendContent) 
	{
		this.url = url;
		this.id = id;
		this.legendContent = typeof legendContent !== 'undefined' ? legendContent : "legend";
		this.pieChart = new dojox.charting.Chart2D(this.id);
		this.pieChart
			 .setTheme(custom.charting.themes.availabilityPie)
		     .addPlot("default", {
		        type: 'Pie',
		        radius: 70,
		        omitLabels: true,

		    });
		this.postData();
		
	},
	renderDataForChart : function(url)
	{
		url = typeof url !== 'undefined' ? url : this.url;
		var object = this;
		status = true;
		var standby = new dojox.widget.Standby({
	        target: "availabilityChart"
	    });
	    document.body.appendChild(standby.domNode);
	    standby.startup();
	    standby.show(); 
		dojo.xhrGet(
		{
		     url: url,
		     handleAs:"json",
		     load: function(result) 
		     {
		    	 standby.hide(); 
		    	 object.postData();
		     },
		    error: function(error)
		    {
		    	standby.hide(); 
		    	console.error(error);
		    }
		});
	},
	postData : function() 
	{
		var legend = this.legendContent;
		var pieChart = this.pieChart;
		var url = this.url;
		dojo.xhrGet({
		    url: url,
		    handleAs:"json",
		    load: function(result) {
				 pieChart.addSeries("Series A", result);
				 new dojox.charting.action2d.MoveSlice(pieChart, "default");
				 new dojox.charting.action2d.Highlight(pieChart, "default");
				 new dojox.charting.action2d.Tooltip(pieChart, "default");
				 pieChart.render();
				 console.log(pieChart);
				 var l = new dojox.charting.widget.Legend({chart: pieChart, outline: false, swatchSize: 25}, legend);
				 console.log(l);
		    },
		    error: function(error)
		    {
		      targetNode.innerHTML = "An unexpected error occurred: " + error;
		    }
		});
	},
	updateData: function()
	{
		var url = this.url;
		var pieChart = this.pieChart;
		dojo.xhrGet({
		    url: url,
		    handleAs:"json",
		    load: function(result) {
		    	pieChart.updateSeries("Series A", result);
				pieChart.render();
		    },
		    error: function(error)
		    {
		      targetNode.innerHTML = "An unexpected error occurred: " + error;
		    }
		});
		
	}
});


