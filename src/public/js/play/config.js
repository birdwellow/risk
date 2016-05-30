
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
			View.addComponentAs(map, "Map");

			var sideBar = new SideBar(Model, Config.view.map, Controller.getContext());
			View.addComponentAs(sideBar, "SideBar");

			Controller.switchToState(Model.roundphase);
		},
		
		serverEvents : {
			
			"phase.troopgain" : function(context){
				Model.activePlayer = context.ativePlayer;
				Model.roundphase = context.roundPhase;
				Model.roundphasedata = JSON.parse(context.roundphasedata);
				Model.activePlayer.newtroops = context.newTroops;
				
				log(Lang.get("phase." + Model.roundphase) + ". " + Lang.get("users.turn", {
					"name" : Model.activePlayer.name,
					"troops" : Model.activePlayer.newtroops
				}));
				
				if(context.newCard && context.newCardOwner){
					context.newCard.cardowner = context.newCardOwner;
					context.newCardOwner.cards.push(context.newCard);
					if(Model.me === context.newCardOwner){
						UI.info(new Card(context.newCard), Lang.get("new.region.card"));
					}
				}
				delete context.roundPhase;
				delete context.newCard;
				delete context.newCardOwner;
				
				context.nextPhase = "troopdeployment";
				return "troopgain";
			},
			
			"cards.traded" : function(context){
				
				var additionalTroops = context.newTroops - Model.activePlayer.newtroops;
				Model.activePlayer.newtroops = context.newTroops;
				Model.roundphasedata = context.roundphasedata;
				for(var i in context.selectedCards){
					var tradedCard = context.selectedCards[i];
					var index = Model.activePlayer.cards.indexOf(tradedCard);
					Model.activePlayer.cards.splice(index, 1);
					tradedCard.cardOwner = null;
				}
				
				log(Lang.get("cards.traded", {
					"name" : Model.activePlayer.name,
					"troops" : additionalTroops
				}));
				
				delete context.selectedCards;
				
			},
			
			"phase.troopdeployment" : function(context){
				
				Model.roundphase = context.roundPhase;
				Model.roundphasedata = null;
				
				log(Lang.get("phase." + Model.roundphase) + ": " + Model.activePlayer.name);
				
				return "troopdeployment";
				
			},
			
			"unit.deployed" : function(context){
				context.region.troops = context.newRegionTroops;
				context.player.newtroops = context.newPlayerTroops;
				if(context.player.newtroops <= 0){
					context.nextPhase = "attack";
					//return "troopdeployment.finish";
				}
				log(Lang.get("unit.deployed", {
					"name" : context.player.name,
					"region" : "region." + context.region.name
				}));
			},
			
			"phase.attack" : function(context){
				context.mouseOverRegion = null;
				Model.roundphase = context.roundPhase;
				
				log(Lang.get("phase." + Model.roundphase) + ": " + Model.activePlayer.name);
				
				return "attack";
			},
			
			"attack.result" : function(context){
				
				Config.controller.utils.enrichResultsWithModels(context.attackResult, context.moveStart, context.moveEnd);
				var result = context.attackResult;
				context.callback = function(){
					for(var i in result){
						var resultPart = result[i];
						var loserRegion = resultPart[3];
						if(Utils.Type.isObject(loserRegion) && loserRegion.troops > 1){
							--loserRegion.troops;
						}
					}
					log(Lang.get("attack.result", {
						"name" : context.moveStart.owner.name,
						"start" : "region." + context.moveStart.name,
						"end" : "region." + context.moveEnd.name
					}));
					if(context.moveStart.troops <= 1){
						context.moveEnd = null;
						context.moveStart = null;
						context.moveType = null;
						Controller.switchToState("attack");
					}
				};
				
				return "attack.confirm";
			},
			
			"attack.victory" : function(context){
				
				Model.roundphasedata = JSON.parse(context.roundphasedata);
				
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
					
					context.moveEnd.owner = context.moveStart.owner;
					var autoShiftTroops = Math.ceil(context.moveStart.troops / 2);
					autoShiftTroops = Math.min(autoShiftTroops, context.moveStart.troops);
					
					context.moveEnd.troops += autoShiftTroops;
					context.moveStart.troops -= autoShiftTroops;
					context.shiftTroops = 0;
					console.log("Shift: " + context.shiftTroops);
					context.moveType = "troopshift";
					
					log(Lang.get("attack.victory", {
						"name" : context.moveStart.owner.name,
						"region" : "region." + context.moveEnd.name,
						"oldowner" : context.moveEnd.owner.name
					}));
					
					if(context.loser){
						var loserIndex = Model.players.indexOf(context.loser);
						Model.players.splice(loserIndex, 1);

						log(Lang.get("attack.loser", {
							"name" : context.loser.name
						}));
						
						if(Model.me === context.loser){
							UI.confirmRedirect(
								"/",
								Lang.get('info.defeated'),
								Lang.get('info.defeated.title')
							);
						}
						delete context.loser;
					}
					
					if(context.winner){
						log(Lang.get("end", {
							"name" : context.winner.name
						}));
						
						if(Model.me === context.winner){
							UI.confirmRedirect(
								"/",
								Lang.get('info.won'),
								Lang.get('info.won.title')
							);
						}
						Controller.switchToState("match.end");
					}
				};
				
				return "attack.troopshift";
			},
			
			"attack.troopshift.result" : function(context){
				var shiftedTroops = Math.abs(context.moveEndTroops - context.moveEnd.troops);
				context.moveEnd.troops = context.moveEndTroops;
				context.moveStart.troops = context.moveStartTroops;
					
				log(Lang.get("attack.troopshift.result", {
					"name" : Model.activePlayer.name,
					"start" : "region." + context.moveStart.name,
					"end" : "region." + context.moveEnd.name,
					"troops" : shiftedTroops
				}));
				
				delete context.shiftTroops;
				context.moveEnd = null;
				context.moveStart = null;
				context.moveType = null;
				
				return "attack";
			},
			
			"phase.troopshift" : function(context){
				context.mouseOverRegion = null;
				Model.roundphase = context.roundPhase;
				
				log(Lang.get("phase." + Model.roundphase) + ": " + Model.activePlayer.name);
				
				return "troopshift";
			},
			
			"troopshift.result" : function(context){
				var shiftedTroops = Math.abs(context.moveEndTroops - context.moveEnd.troops);
				context.moveEnd.troops = context.moveEndTroops;
				context.moveStart.troops = context.moveStartTroops;
					
				log(Lang.get("attack.troopshift.result", {
					"name" : Model.activePlayer.name,
					"start" : "region." + context.moveStart.name,
					"end" : "region." + context.moveEnd.name,
					"troops" : shiftedTroops
				}));
				
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
					
					var myCards = Model.me.cards;
					var differentCardTypes = {};
					for (var index in myCards) {
						var card = myCards[index];
						differentCardTypes["#" + card.cardunittype] = (
								differentCardTypes["#" + card.cardunittype] === undefined ?
								1 :
								differentCardTypes["#" + card.cardunittype] + 1	);
					}
					
					var atLeastThreeCardsOfDifferentType = (Object.keys(differentCardTypes).length >= 3);
					
					var atLeastThreeCardsOfSameType = false;
					for (var key in differentCardTypes) {
						atLeastThreeCardsOfSameType = atLeastThreeCardsOfSameType
								|| differentCardTypes[key] >= 3;
					}
					
					if (atLeastThreeCardsOfDifferentType || atLeastThreeCardsOfSameType) {
						
						UI.confirmAction(function(){
								proxy.send("troopgain.finish");
							},
							Lang.get('warn.continue.withouth.card.trade.text'), 
							Lang.get('warn.continue.withouth.card.trade.title'), 
							Lang.get('warn.continue.withouth.card.trade.cancel'), 
							Lang.get('warn.continue.withouth.card.trade.confirm'),
							"warn");
					} else {
						delete context.nextPhase;
						proxy.send("troopgain.finish");
					}
				}
				
			},
			
			"troopdeployment" : {
				
				onEnter : function(context){
					if(Model.me.newtroops <= 0){
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
				},
			
				"button.nextphase.clicked" : function(context, event){
					delete context.nextPhase;
					proxy.send("troopdeployment.finish");
				}
				
			},
			
			/*
			"troopdeployment.finish" : {
			
				"button.nextphase.clicked" : function(context, event){
					proxy.send("troopdeployment.finish");
				}
				
			},
			*/

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
					var region = event.data.model;
					if(Model.me === region.owner && region.troops > 1){
						context.mouseOverRegion = null;
						context.moveStart = region;
						return "attack.select.end";
					}
				},
				
				"button.nextphase.clicked" : function(context, event){
						console.log(Model.roundphasedata);
					if(Model.roundphasedata && Model.roundphasedata.conqueredregions > 0){
						proxy.send("attack.finish");
					} else {
						UI.confirmAction(function(){
								proxy.send("attack.finish");
							},
							Lang.get('warn.continue.without.attack.text'), 
							Lang.get('warn.continue.without.attack.title'), 
							Lang.get('warn.continue.without.attack.cancel'), 
							Lang.get('warn.continue.without.attack.confirm'),
							"warn");
					}
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
					if(!context.moveStart.troops > 1){
						return;
					}
					proxy.send("attack.confirm");
					return "attack.waiting";
				},
				
				"button.attack.cancel.clicked" : function(context, event){
					context.moveEnd = null;
					context.moveStart = null;
					context.moveType = null;
					context.mouseOverRegion = null;
					return "attack";
				}
				
			},
			
			"attack.waiting" : {
				
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
					console.log("Shifting " + context.shiftTroops);
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
					if(!Model.roundphasedata.shiftedTroops) {
						UI.confirmAction(function(){
								proxy.send("phase.finish");
							},
							Lang.get('warn.continue.withouth.final.shift.text'), 
							Lang.get('warn.continue.withouth.final.shift.title'), 
							Lang.get('warn.continue.withouth.final.shift.cancel'), 
							Lang.get('warn.continue.withouth.final.shift.confirm'),
							"warn");
					} else {
						// Just for the case...
						proxy.send("phase.finish");
					}
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
				
			},
			
			"match.end" : {
				
			}

		}
	
	},
	
	view : {
		map : {
			defaultMode : 'owner',
			width : 1000,
			height : 700,
			containerId : "game-map",
			
			fade : {
				speed : 0.4,
				targetOpacity : 0.25
			},
			
			
			regions : {
				nameLabels : {
					fontFamily: 'Garamond',
					fontSize: 16,
					padding: 5,
					offsetX: 5,
					width: 100,
					align: 'center'
					//offsetY: 15
				},
				troopLabels : {
					fontFamily: 'Calibri',
					fontSize: 20,
					padding: 1,
					fontStyle: "100",
					width: 30,
					offsetY : -5,
					cornerRadius: 15,
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
				fill : ["rgba(200,22,22,0.75)", "rgba(255,88,88,0.875)", "rgba(255,130,130,1)"],
				stroke : ["rgba(0,0,0,0.50)", "#000", "#000"],
				strokeWidth : [1, 3, 3],
				text : ["rgba(0,0,0,0.50)", "rgba(0,0,0,0.75)", "rgba(0,0,0,1)"],
				troops : {
					stroke : ["", "rgba(87,22,22,1)", "rgba(87,22,22,1)"],
					strokeWidth : [2,2,2],
					color : ["rgba(87,22,22,1)", "rgba(87,22,22,1)", "rgba(87,22,22,1)"],
					fill : ["rgba(127,44,44,0.5)", "rgba(127,44,44,0.5)", "rgba(200,88,88,0.75)"]
				}
			},

			"blue" : {
				fill : ["rgba(88,88,255,0.75)", "rgba(150,150,255,0.875)", "rgba(180,180,255,1)"],
				stroke : ["rgba(0,0,0,0.50)", "#000", "#000"],
				strokeWidth : [1, 3, 3],
				text : ["rgba(0,0,0,0.50)", "rgba(0,0,0,0.75)", "rgba(0,0,0,1)"],
				troops : {
					stroke : ["", "rgba(22,22,87,1)", "rgba(22,22,87,1)"],
					strokeWidth : [2,2,2],
					color : ["rgba(22,22,87,1)", "rgba(22,22,87,1)", "rgba(22,22,87,1)"],
					fill : ["rgba(44,44,127,0.5)", "rgba(44,44,127,0.5)", "rgba(88,88,200,0.75)"]
				}
			},

			"green" : {
				fill : ["rgba(44,155,44,0.75)", "rgba(66,200,66,0.875)", "rgba(88,255,88,1)"],
				stroke : ["rgba(0,0,0,0.50)", "#000", "#000"],
				strokeWidth : [1, 3, 3],
				text : ["rgba(0,0,0,0.50)", "rgba(0,0,0,0.75)", "rgba(0,0,0,1)"],
				troops : {
					stroke : ["", "rgba(22,87,22,1)", "rgba(22,87,22,1)"],
					strokeWidth : [2,2,2],
					color : ["rgba(22,87,22,1)", "rgba(22,87,22,1)", "rgba(22,87,22,1)"],
					fill : ["rgba(44,127,44,0.5)", "rgba(44,127,44,0.5)", "rgba(88,200,88,0.75)"]
				}
			},

			"yellow" : {
				fill : ["rgba(200,200,0,0.75)", "rgba(230,230,0,0.875)", "rgba(255,255,0,1)"],
				stroke : ["rgba(0,0,0,0.50)", "#000", "#000"],
				strokeWidth : [1, 3, 3],
				text : ["rgba(0,0,0,0.50)", "rgba(0,0,0,0.75)", "rgba(0,0,0,1)"],
				troops : {
					stroke : ["", "rgba(100,100,0,1)", "rgba(100,100,0,1)"],
					strokeWidth : [2,2,2],
					color : ["rgba(50,50,0,1)", "rgba(50,50,0,1)", "rgba(50,50,0,1)"],
					fill : ["rgba(126,126,0,0.5)", "rgba(126,126,0,0.5)", "rgba(200,200,0,0.75)"]
				}
			},

			"orange" : {
				fill : ["rgba(230,160,0,0.75)", "rgba(255,180,0,0.75)", "rgba(255,180,0,1)"],
				stroke : ["rgba(0,0,0,0.50)", "#000", "#000"],
				strokeWidth : [1, 3, 3],
				text : ["rgba(0,0,0,0.50)", "rgba(0,0,0,0.75)", "rgba(0,0,0,1)"],
				troops : {
					stroke : ["", "rgba(120,80,0,1)", "rgba(120,80,0,1)"],
					strokeWidth : [2,2,2],
					color : ["rgba(70,40,0,1)", "rgba(70,40,0,1)", "rgba(70,40,0,1)"],
					fill : ["rgba(150,100,0,0.5)", "rgba(150,100,0,0.5)", "rgba(220,120,0,0.75)"]
				}
			},

			"brown" : {
				fill : ["rgba(150,90,0,0.75)", "rgba(175,110,0,0.75)", "rgba(200,130,0,1)"],
				stroke : ["rgba(0,0,0,0.50)", "#000", "#000"],
				strokeWidth : [1, 3, 3],
				text : ["rgba(0,0,0,0.50)", "rgba(0,0,0,0.75)", "rgba(0,0,0,1)"],
				troops : {
					stroke : ["", "rgba(120,80,0,1)", "rgba(120,80,0,1)"],
					strokeWidth : [2,2,2],
					color : ["rgba(70,40,0,1)", "rgba(70,40,0,1)", "rgba(70,40,0,1)"],
					fill : ["rgba(150,120,0,0.5)", "rgba(150,120,0,0.5)", "rgba(190,140,0,0.75)"]
				}
			},
			
			"purple" : {
				fill : ["rgba(160,0,135,0.75)", "rgba(180,0,155,0.875)", "rgba(210,0,180,1)"],
				stroke : ["rgba(0,0,0,0.50)", "#000", "#000"],
				strokeWidth : [1, 3, 3],
				text : ["rgba(0,0,0,0.50)", "rgba(0,0,0,0.75)", "rgba(0,0,0,1)"],
				troops : {
					stroke : ["", "rgba(87,0,70,1)", "rgba(87,0,70,1)"],
					strokeWidth : [2,2,2],
					color : ["rgba(50,0,40,1)", "rgba(50,0,40,1)", "rgba(50,0,40,1)"],
					fill : ["rgba(127,0,90,0.5)", "rgba(127,0,90,0.5)", "rgba(200,0,160,0.75)"]
				}
			},
		}
	}
	
};