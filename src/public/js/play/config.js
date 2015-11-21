
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
			
			"view.mode.owner" : function(){
				View.setMode('owner');
			},
			
			"view.mode.continent" : function(){
				View.setMode('continent');
			},
			
			"message.send" : function(context, event){
				proxy.send("new.chat.message", event.data);
			},
			
			"new.chat.message" : function(context, event){
				context.newChatMessage = event.data;
			},
			
			"user.connected" : function(context, event){
				var user = event.data.user;
				log(Lang.get("user.online", {
					"name" : user.name
				}));
				user.isonline = true;
			},
			
			"user.disconnected" : function(context, event){
				var user = event.data.user;
				log(Lang.get("user.offline", {
					"name" : user.name
				}));
				user.isonline = false;
			}
			
		},
		
		initServerEvent : function (data) {
			Model.digest(data);

			View = new ViewInstance(Controller);

			var map = new Map(Model, Config.view.map, Controller.getContext());
			View.addComponent(map).as("Map");

			var sideBar = new SideBar(Model, Config.view.map, Controller.getContext());
			View.addComponent(sideBar).as("SideBar");

			Controller.switchToState(Model.roundphase);
		},
		
		serverEvents : {
			
			"phase.troopgain" : function(context){
				Model.activePlayer = context.ativePlayer;
				Model.roundphase = context.roundPhase;
				Model.roundphasedata = JSON.parse(context.roundphasedata);
				Model.activePlayer.newtroops = context.newTroops;
				
				if(context.newCard && context.newCardOwner){
					context.newCard.cardowner = context.newCardOwner;
					context.newCardOwner.cards.push(context.newCard);
				}
				delete context.roundPhase;
				delete context.newCard;
				delete context.newCardOwner;
				
				context.nextPhase = "troopdeployment";
				return "troopgain";
			},
			
			"cards.traded" : function(context){
				
				Model.activePlayer.newtroops = context.newTroops;
				Model.roundphasedata = context.roundphasedata;
				for(var i in context.selectedCards){
					var tradedCard = context.selectedCards[i];
					var index = Model.activePlayer.cards.indexOf(tradedCard);
					Model.activePlayer.cards.splice(index, 1);
					tradedCard.cardOwner = null;
				}
				delete context.selectedCards;
				
			},
			
			"phase.troopdeployment" : function(context){
				
				Model.roundphase = context.roundPhase;
				Model.roundphasedata = null;
				return "troopdeployment";
				
			},
			
			"unit.deployed" : function(context){
				context.region.troops = context.newRegionTroops;
				context.player.newtroops = context.newPlayerTroops;
				if(context.player.newtroops <= 0){
					context.nextPhase = "attack";
					return "troopdeployment.finish";
				}
			},
			
			"phase.attack" : function(context){
				context.mouseOverRegion = null;
				Model.roundphase = context.roundPhase;
				return "attack";
			},
			
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
				};
			},
			
			"attack.victory" : function(context){
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
					
					log(context.moveStart.owner.name + " conquered " + context.moveEnd.label + " from " + context.moveEnd.owner.name);
					
					context.moveEnd.owner = context.moveStart.owner;
					context.moveEnd.troops++;
					context.moveStart.troops--;
					context.moveType = "troopshift";
				};
				
				return "attack.troopshift";
			},
			
			"attack.troopshift.result" : function(context){
				context.moveEnd.troops = context.moveEndTroops;
				context.moveStart.troops = context.moveStartTroops;
				
				delete context.shiftTroops;
				context.moveEnd = null;
				context.moveStart = null;
				context.moveType = null;
				
				return "attack";
			},
			
			"phase.troopshift" : function(context){
				context.mouseOverRegion = null;
				Model.roundphase = context.roundPhase;
				return "troopshift";
			},
			
			"troopshift.result" : function(context){
				context.moveEnd.troops = context.moveEndTroops;
				context.moveStart.troops = context.moveStartTroops;
				
				delete context.shiftTroops;
				context.moveEnd = null;
				context.moveStart = null;
				context.moveType = null;
				
				return "phase.finish";
			}

		},

		states : {
			
			"troopgain" : {
				
				onEnter : function(context){
					context.nextPhase = "troopdeployment";
					if(Utils.Type.isString(Model.roundphasedata)){
						Model.roundphasedata = JSON.parse(Model.roundphasedata);
					}
				},
			
				"regioncard.clicked" : function(context, event){
					if(!context.selectedCards){
						context.selectedCards = [];
					}
					var card = event.data;
					var cardIndex = context.selectedCards.indexOf(card);
					if(cardIndex > -1){
						context.selectedCards.splice(cardIndex, 1);
					} else {
						context.selectedCards.push(card);
					}
				},
				
				"button.tradecards.clicked" : function(context, event){
					if(context.selectedCards && context.selectedCards.length == 3){
						proxy.send("trade.cards");
					}
				},
			
				"button.nextphase.clicked" : function(context, event){
					delete context.nextPhase;
					proxy.send("troopgain.finish");
				}
				
			},
			
			"troopdeployment" : {
				
				onEnter : function(context){
					if(!Model.me.newtroops > 0){
						context.nextPhase = "attack";
					}
				},
			
				"region.mouse.click" : function(context, event){
					var region = event.data.model;
					if(Model.me === region.owner){
						context.region = region;
						proxy.send("deploy.unit");
					}
				},
				
				"region.mouse.over" : function(context, event){
					var region = event.data.model;
					if(Model.me === region.owner){
						context.mouseOverRegion = region;
					}
				},

				"region.mouse.out" : function(context, event){
					context.mouseOverRegion = null;
				}
				
			},
			
			"troopdeployment.finish" : {
			
				"button.nextphase.clicked" : function(context, event){
					proxy.send("troopdeployment.finish");
				}
				
			},

			"attack" : {
				
				onEnter : function(context){
					context.nextPhase = "troopshift";
				},
				
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
					if(Model.me === region.owner && region.troops > 1){
						context.moveStart = region;
						return "attack.select.end";
					}
				},
				
				"button.nextphase.clicked" : function(context, event){
					proxy.send("attack.finish");
				}

			},
			
			"attack.select.end" : {
				
				onEnter : function(context){
					delete context.nextPhase;
				},
				
				"region.mouse.over" : function(context, event){
					var region = event.data.model;
					var isNeighborRegion = context.moveStart.neighbors.indexOf(region) > -1;
					if(region !== context.moveStart && Model.me !== region.owner && isNeighborRegion){
						context.mouseOverRegion = region;
					}
				},

				"region.mouse.out" : function(context, event){
					context.mouseOverRegion = null;
				},

				"region.mouse.click" : function(context, event){
					context.mouseOverRegion = null;
					var region = event.data.model;
					var isNeighborRegion = context.moveStart.neighbors.indexOf(region) > -1;
					if(region === context.moveStart){
						context.moveStart = null;
						return "attack";
					} else if (Model.me !== region.owner && isNeighborRegion){
						context.moveEnd = region;
						context.moveType = "attack";
						return "attack.confirm";
					}
				}
			},
			
			"attack.confirm" : {
				
				onEnter : function(context){
					delete context.nextPhase;
				},
				
				"button.attack.confirm.clicked" : function(context, event){
					context.attackResult = "waiting";
					var attackorTroops = Math.min(context.moveStart.troops - 1, 3);
					var defenderTroops = Math.min(context.moveEnd.troops, 2);
					context.attackTroops = [attackorTroops, defenderTroops];
					proxy.send("attack.confirm");
				},
				
				"button.attack.cancel.clicked" : function(context, event){
					context.moveEnd = null;
					context.moveStart = null;
					context.moveType = null;
					context.mouseOverRegion = null;
					return "attack";
				}
				
			},
			
			"attack.troopshift" : {
				
				onEnter : function(context){
					delete context.nextPhase;
				},
				
				"button.troopshift.plus.clicked" : function(context, event){
					if(context.shiftTroops === undefined){
						context.shiftTroops = 0;
					}
					var endRegion = context.moveEnd;
					var startRegion = context.moveStart;
					if(startRegion.troops > 1){
						startRegion.troops--;
						endRegion.troops++;
						context.shiftTroops++;
					}
				},
				
				"button.troopshift.minus.clicked" : function(context, event){
					if(context.shiftTroops === undefined){
						context.shiftTroops = 0;
					}
					var endRegion = context.moveEnd;
					var startRegion = context.moveStart;
					if(endRegion.troops > 1){
						startRegion.troops++;
						endRegion.troops--;
						context.shiftTroops--;
					}
				},
				
				"button.troopshift.confirm.clicked" : function(context, event){
					if(context.shiftTroops === undefined){
						context.shiftTroops = 0;
					}
					proxy.send("attack.troopshift.confirm");
				}
				
			},
			
			"attack.finish" : {
			
				"button.nextphase.clicked" : function(context, event){
					delete context.nextPhase;
					proxy.send("attack.finish");
				}
				
			},
			
			"troopshift" : {
				
				onEnter : function(context){
					context.nextPhase = "troopgain";
				},
				
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
					if(Model.me === region.owner && region.troops > 1){
						context.moveStart = region;
						return "troopshift.select.end";
					}
				},
				
				"button.nextphase.clicked" : function(context, event){
					proxy.send("phase.finish");
				}
			
			},
			
			"troopshift.select.end" : {
				
				onEnter : function(context){
					delete context.nextPhase;
				},
				
				"region.mouse.over" : function(context, event){
					var region = event.data.model;
					var isNeighborRegion = context.moveStart.neighbors.indexOf(region) > -1;
					if(region !== context.moveStart && Model.me === region.owner && isNeighborRegion){
						context.mouseOverRegion = region;
					}
				},

				"region.mouse.out" : function(context, event){
					context.mouseOverRegion = null;
				},

				"region.mouse.click" : function(context, event){
					context.mouseOverRegion = null;
					var region = event.data.model;
					var isNeighborRegion = context.moveStart.neighbors.indexOf(region) > -1;
					if(region === context.moveStart){
						context.moveStart = null;
						return "troopshift";
					} else if (Model.me === region.owner && isNeighborRegion){
						context.moveEnd = region;
						context.moveType = "troopshift";
						return "troopshift.confirm";
					}
				}
			},
			
			"troopshift.confirm" : {
				
				onEnter : function(context){
					delete context.nextPhase;
				},
				
				"button.troopshift.plus.clicked" : function(context, event){
					if(context.shiftTroops === undefined){
						context.shiftTroops = 0;
					}
					var endRegion = context.moveEnd;
					var startRegion = context.moveStart;
					if(startRegion.troops > 1){
						startRegion.troops--;
						endRegion.troops++;
						context.shiftTroops++;
					}
				},
				
				"button.troopshift.minus.clicked" : function(context, event){
					if(context.shiftTroops === undefined){
						context.shiftTroops = 0;
					}
					var endRegion = context.moveEnd;
					var startRegion = context.moveStart;
					if(endRegion.troops > 1){
						startRegion.troops++;
						endRegion.troops--;
						context.shiftTroops--;
					}
				},
				
				"button.troopshift.confirm.clicked" : function(context, event){
					proxy.send("troopshift.confirm");
				}
			
			},
			
			"phase.finish" : {
				
				onEnter : function(context){
					context.nextPhase = "troopgain";
				},
				
				"button.nextphase.clicked" : function(context, event){
					proxy.send("phase.finish");
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
					attackshift : [0, 'rgba(0,200,0,0)', 0.05, 'rgba(0,200,0,0)', 0.4, 'rgb(0,200,0)', 1, 'rgb(0,200,0)'],
					troopshift : [0, 'rgba(0,200,0,0)', 0.05, 'rgba(0,200,0,0)', 0.4, 'rgb(0,200,0)', 1, 'rgb(0,200,0)']
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

			"green" : {
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

			"blue" : {
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