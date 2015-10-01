
var Config = {

	controller : {
		
		globalEvents : {
			
			"global:sendMessage" : function(event, context){
				console.log("send");
			}

		},

		states : {

			"passive:selecting.attack.start" : {},
			
			"passive:selecting.attack.target" : {},

			"active:selecting.attack.start" : {
				
				"region.mouse.over" : function(event, context){
					var region = event.data.model;
					context.mouseOverRegion = region;
				},

				"region.mouse.out" : function(event, context){
					context.mouseOverRegion = null;
				},

				"region.mouse.click" : function(event, context){
					var region = event.data.model;
					context.move.start = region;
					return "active:selecting.attack.target";
				}

			},
			
			"active:selecting.attack.target" : {
				
				"region.mouse.over" : function(event, context){
					var region = event.data.model;
					if(region !== context.move.start){
						context.mouseOverRegion = region;
					}
				},

				"region.mouse.out" : function(event, context){
					context.mouseOverRegion = null;
				},

				"region.mouse.click" : function(event, context){
					var region = event.data.model;
					if(region === context.move.start){
						context.move.start = null;
						return "active:selecting.attack.start";
					} else {
						context.move.end = region;
						context.move.type = "attack";
						return "active:confirm.attack";
					}
				}
			},
			
			"active:confirm.attack" : {
				
				"region.mouse.click" : function(event, context){
					var region = event.data.model;
					if(region === context.move.end){
						context.move.end = null;
						context.move.type = null;
						return "active:selecting.attack.target";
					}
				}
				
			}

		}
	
	},
	
	view : {
		map : {
			defaultMode : 'owner',
			width : 800,
			height : 500,
			containerId : "game-map",
			
			regions : {
				nameLabels : {
					fontFamily: 'Garamond',
					fontSize: 18,
					padding: 5,
					offsetX: 5
					//offsetY: 15
				},
				troopLabels : {
					fontFamily: 'Calibri',
					fontSize: 25,
					padding: 5,
					fontStyle: "100",
					width: 40,
					offsetY : -5,
					cornerRadius: 20,
					align : 'center'
					//offsetY: 15
				}
			},
			
			pointers : {
				stroke : "",
				strokeWidth : 0,
				speed : 0.5,
				fillLinearGradientColorStops: [0, 'rgba(0,0,0,0)', 0.05, 'rgba(0,0,0,0)', 0.4, '#222', 1, '#222']
			}
		},
		
		themes : {
			"red" : {
				fill : ["rgba(200,22,22,0.75)", "rgba(255,88,88,0.875)", "rgba(255,88,88,1)"],
				stroke : ["", "", "rgba(127,44,44,1)"],
				strokeWidth : [1, 3, 3],
				text : ["rgba(0,0,0,0.20)", "rgba(127,44,44,1)", "rgba(190,66,66,1)"],
				troops : {
					stroke : ["rgba(87,22,22,1)", "rgba(87,22,22,1)", "rgba(87,22,22,1)"],
					strokeWidth : [2,2,2],
					color : ["rgba(87,22,22,1)", "rgba(87,22,22,1)", "rgba(87,22,22,1)"],
					fill : ["rgba(127,44,44,0.5)", "rgba(127,44,44,0.5)", "rgba(200,88,88,0.75)"]
				}
			},

			"blue" : {
				fill : ["rgba(88,255,88,0.75)", "rgba(88,255,88,0.875)", "rgba(88,255,88,1)"],
				stroke : ["", "", "rgba(44,127,44,1)"],
				strokeWidth : [1, 3, 3],
				text : ["rgba(0,0,0,0.20)", "rgba(44,127,44,1)", "rgba(66,190,66,1)"],
				troops : {
					stroke : ["rgba(22,87,22,1)", "rgba(22,87,22,1)", "rgba(22,87,22,1)"],
					strokeWidth : [2,2,2],
					color : ["rgba(22,87,22,1)", "rgba(22,87,22,1)", "rgba(22,87,22,1)"],
					fill : ["rgba(44,127,44,0.5)", "rgba(44,127,44,0.5)", "rgba(88,200,88,0.75)"]
				}
			},

			"green" : {
				fill : ["rgba(88,88,255,0.75)", "rgba(88,88,255,0.875)", "rgba(88,88,255,1)"],
				stroke : ["", "", "rgba(44,44,127,1)"],
				strokeWidth : [1, 3, 3],
				text : ["rgba(0,0,0,0.20)", "rgba(44,44,127,1)", "rgba(66,66,190,1)"],
				troops : {
					stroke : ["rgba(22,22,87,1)", "rgba(22,22,87,1)", "rgba(22,22,87,1)"],
					strokeWidth : [2,2,2],
					color : ["rgba(22,22,87,1)", "rgba(22,22,87,1)", "rgba(22,22,87,1)"],
					fill : ["rgba(44,44,127,0.5)", "rgba(44,44,127,0.5)", "rgba(88,88,200,0.75)"]
				}
			}
		}
	}
	
};