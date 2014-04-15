dojo.provide("custom.charting.themes.availabilityPie");
dojo.require("dojox.gfx.gradutils");
dojo.require("dojox.charting.Theme");
(function() {
	var dc = dojox.charting, themes = dc.themes, Theme = dc.Theme, g = Theme.generateGradient,

	/* fill settings for gradation */
	defaultFill = {
		type : "linear",
		space : "shape",
		x1 : 0,
		y1 : 0,
		x2 : 0,
		y2 : 100
	};

	custom.charting.themes.availabilityPie = new dojox.charting.Theme({

		chart : {
			fill : "#fff",
			pageStyle : {
				backgroundColor : "#fff",
				color : "#fff",

			}
		},

		/* plotarea definition */
		plotarea : {
			fill : "#fff"
		},

		axis : {
			stroke : { // the axis itself
				color : "#fff",
				width : 3
			},
			tick : { // used as a foundation for all ticks
				color : "#fff",
				position : "center",
				font : "normal normal normal 7pt Helvetica, Arial, sans-serif", // labels
																				// on
																				// axis
				fontColor : "black" // color of labels
			}
		},

		/* series definition */

		series : {
			stroke : {
				width : 0,
				color : "#fff"
			},
			outline : null,
			font : "normal normal normal 8pt Helvetica, Arial, sans-serif",
			fontColor : "#333"
		},

		/* marker definition */
		marker : {
			stroke : {
				width : 1.25,
				color : "#fff"
			},
			outline : {
				width : 1.25,
				color : "#fff"
			},
			font : "normal normal normal 8pt Helvetica, Arial, sans-serif",
			fontColor : "#fff"
		},

		/* series theme with gradations! */
		// light => dark
		// from above: g = dojox.charting.Theme.generateGradient
		// defaultFill object holds all of our gradation settings
		seriesThemes : [ {
			fill : "#9cff9c"
		}, {
			fill : "#ffffb5"
		}, {
			fill : "#ffce9c"
		}, {
			fill : "#ffefef"
		}

		],
	});
	return custom.charting.themes.bandwidthGraph;
})();
