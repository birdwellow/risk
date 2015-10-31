
var Config = {

	controller : {
		
		utils : {
			
			enrichResultsWithModels : function(result, startRegion, endRegion){
				
				for(var i in result){
					if(result[i][0]){
						if(result[i][0] === "win"){
							result[i][3] = endRegion;
						} if(result[i][0] === "lose"){
							result[i][3] = startRegion;
						}
					}
				}
				
			}
			
		},
		
		globalEvents : {
			
			"message.send" : function(context){
				console.log("send");
			},
			
			"message.receive" : function(context){
				console.log("received");
			},
			
		},
		
		serverEvents : {
			
			"attack.result" : function(context){
				Config.controller.utils.enrichResultsWithModels(context.attackResult, context.moveStart, context.moveEnd);
				var result = context.attackResult;
				context.callback = function(){
					for(var i in result){
						var resultPart = result[i];
						var loserRegion = resultPart[3];
						if(Utils.Type.isObject(loserRegion) && loserRegion.troops){
							--loserRegion.troops;
						}
					}
				}
			},
			
			"attack.victory" : function(context){
				Config.controller.utils.enrichResultsWithModels(context.attackResult, context.moveStart, context.moveEnd);
				context.callback = function(){
					for(var i in result){
						var resultPart = result[i];
						var loserRegion = resultPart[3];
						if(Utils.Type.isObject(loserRegion) && loserRegion.troops){
							--loserRegion.troops;
						}
					}
					context.moveEnd.owner = context.moveStart.owner;
					context.moveType = "shift";
				}
			}

		},

		states : {

			"selecting.attack.start" : {
				
				"region.mouse.over" : function(context, event){
					var region = event.data.model;
					if(Model.me === region.owner){
						context.mouseOverRegion = region;
					}
				},

				"region.mouse.out" : function(context, event){
					context.mouseOverRegion = null;
				},

				"region.mouse.click" : function(context, event){
					context.mouseOverRegion = null;
					var region = event.data.model;
					if(Model.me === region.owner){
						context.moveStart = region;
						return "selecting.attack.target";
					}
				}

			},
			
			"selecting.attack.target" : {
				
				"region.mouse.over" : function(context, event){
					var region = event.data.model;
					if(region !== context.moveStart && Model.me !== region.owner){
						context.mouseOverRegion = region;
					}
				},

				"region.mouse.out" : function(context, event){
					context.mouseOverRegion = null;
				},

				"region.mouse.click" : function(context, event){
					context.mouseOverRegion = null;
					var region = event.data.model;
					if(region === context.moveStart){
						context.moveStart = null;
						return "selecting.attack.start";
					} else if (Model.me !== region.owner){
						context.moveEnd = region;
						context.moveType = "attack";
						return "confirm.attack";
					}
				}
			},
			
			"confirm.attack" : {
				
				"attackConfirmButton.clicked" : function(context, event){
					context.attackResult = "waiting";
					var attackorTroops = Math.min(context.moveStart.troops - 1, 3);
					var defenderTroops = Math.min(context.moveEnd.troops, 2);
					context.attackTroops = [attackorTroops, defenderTroops];
					console.log(context.attackTroops);
					proxy.send("attack.confirm");
				},
				
				"attackCancelButton.clicked" : function(context, event){
					context.moveEnd = null;
					context.moveStart = null;
					context.moveType = null;
					context.mouseOverRegion = null;
					return "selecting.attack.start";
				}
				
			}

		}
	
	},
	
	view : {
		map : {
			defaultMode : 'owner',
			width : 1000,
			height : 500,
			containerId : "game-map",
			
			fade : {
				speed : 0.25,
				targetOpacity : 0.25
			},
			
			
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
				fillLinearGradientColorStops: {
					attack : [0, 'rgba(255,255,0,0)', 0.25, '#ff0', 0.75, '#f00', 1, '#f00'],
					troopshift : [0, 'rgba(0,0,0,0)', 0.05, 'rgba(0,0,0,0)', 0.4, '#222', 1, '#222']
				}
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