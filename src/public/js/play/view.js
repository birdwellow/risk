var View = {};



function ViewInstance(controller) {
	
	var self = this;
	
	var viewMode = Config.view.map.defaultMode;
	
	var components = [];
	
	return {
		
		getMode : function(){
			return viewMode;
		},
		
		setMode : function(newViewMode){
			viewMode = newViewMode;
			this.update();
			return this;
		},
		
		getContext : function(){
			return controller.getContext();
		},

		addComponent : function(component, name){
			components.push(component);
			component.setParent(this);
			return {
				as : function(name){
					components[name] = component;
					return self;
				}
			};
		},

		getComponent : function(key){
			return components[key];
		},

		fire : function(event){
			controller.listen(event);
		},

		update : function(event){
			for(var key in components){
				var component = components[key];
				if(component.update && typeof component.update === 'function'){
					// event is handed over, e.g. if the component doesn't want
					// to handle events sent by it self
					component.update(event);
				}
			}
		}
	
	};
	
};




function Map(model, config, context){
	
	var MOUSEOUT_STATE = 0;
	var MOUSEOVER_STATE = 1;
	var CLICKED_STATE = 2;
	
	var parent;
	
	var pointerConfig = {};
	
	var pointer = null;
	
	var mapLayer, actionLayer;
	
	var regionPaths = [];
	
	var previousStartRegion, previousEndRegion;
	
	
	
	var self = {
		
		setParent : function(newParent){
			parent = newParent;
		},
		
		getParent : function(){
			return parent;
		},
		
		getMode : function(){
			return parent.getMode();
		},
		
		render : function(){
			
			for(var regionIndex in model.regions){
				var region = model.regions[regionIndex];
				if (context.moveStart === region || context.moveEnd === region){
					clickRegion(region);
				} else if(context.mouseOverRegion === region){
					mouseOverRegion(region);
				} else {
					mouseOutRegion(region);
				}
			}
				
			var move = context.getMove();
			if(move && !pointer){
				var startRegion = move.start;
				var endRegion = move.end;
				if(move.type === "attack"){
					attackMoveFromTo(startRegion, endRegion);
				} else if(move.type === "troopshift"){
					troopshiftMoveFromTo(startRegion, endRegion);
				}
			} else if(!move && pointer){
				endMove();
			} else if(move && pointer){
				if(!pointer.isType(move.type)){
					pointer.setType(move.type);
				}
			}
			
			if(move && context.attackResult === "waiting" && !mapControls.diceRolling() && context.attackTroops && context.attackTroops.length === 2){
				mapControls.diceStart(context.attackTroops[0], context.attackTroops[1]);
			} else if(Utils.Type.isArray(context.attackResult)){
				mapControls.diceEndWith(context.attackResult, context.callback);
				context.attackResult = null;
			}
			
			mapControls.active(context.isClientActive());
			mapControls.mode(context.moveType);
			mapControls.phase(Model.roundphase);
			mapControls.activePlayer(Model.activePlayer);
			mapControls.newTroops(Model.activePlayer.newtroops);
			
		},
		
		update : function(){
			
			this.render();
			
			context.callback = null;

			for(var regionPathKey in regionPaths){
				var regionPath = regionPaths[regionPathKey];
				regionPath.update();
			}
			mapLayer.update();
			actionLayer.update();
			
		},

		fire : function(event){
			parent.fire(event);
		}
	};
	
	
	var kineticStage = new Kinetic.Stage({
		container: config.containerId,
		width: config.width,
		height: config.height
	});
	
	var mapControls = new MapControls("map-controls");
	
	/*kineticStage.on("mousemove", function(e){
		console.log(e.evt.layerX + "|" + e.evt.layerY);
	});*/
	
	function addRegionPath(regionPath){
		regionPaths[regionPath.getIdentifier()] = regionPath;
		regionPath.setParent(self);
		mapLayer.addRegionPath(regionPath);
	}
	
	function getRegionPath (region){
		var search = "id=" + region.id;
		return regionPaths[search];
	}
	
	function mouseOutRegion(region){
		var regionPath = getRegionPath(region);
		regionPath.setState(MOUSEOUT_STATE);
	}

	function mouseOverRegion(region){
		var regionPath = getRegionPath(region);
		regionPath.setState(MOUSEOVER_STATE);
	}

	function clickRegion(region){
		var regionPath = getRegionPath(region);
		regionPath.setState(CLICKED_STATE);
	}

	function attackMoveFromTo(startRegion, endRegion){
		
		mapControls.attack();
		moveFromTo(startRegion, endRegion, "attack");
		
	}

	function troopshiftMoveFromTo(startRegion, endRegion){
		
		mapControls.troopShift();
		moveFromTo(startRegion, endRegion, "troopshift");
		
	}

	function moveFromTo(startRegion, endRegion, type){
		
		pointer = new ActionPointer(startRegion, endRegion, type);
		actionLayer.addPointer(pointer);
		
		previousStartRegion = startRegion;
		previousEndRegion = endRegion;
		
		var startRegionPath = regionPaths["id=" + startRegion.id];
		var endRegionPath = regionPaths["id=" + endRegion.id];
		actionLayer.addRegionPath(startRegionPath);
		actionLayer.addRegionPath(endRegionPath);
		
		var duration = Config.view.map.fade.speed * 1000;
		var inverseOpacityValue = 1 - Config.view.map.fade.targetOpacity;
		
		var centering = Utils.Math2d.getCenteringWithin(
			{
				x: startRegion.centerx,
				y: startRegion.centery
			},
			{
				x: endRegion.centerx,
				y: endRegion.centery
			},
			config.width,
			config.height,
			100
		);
		var targetScale = centering.scale;
		var targetOffset = centering.offset;
		
		var animation = new Kinetic.Animation(function(frame){
			
			if(frame.time > duration){
				this.stop();
				pointer.scale(1);
				mapLayer.update();
				actionLayer.update();
			}
			
			var process = Math.min(frame.time/duration, 1);

			var opacity = 1 - inverseOpacityValue * process;
			
			var scaleScalar = 1 + (targetScale - 1) * process;
			var scale = {
				x: scaleScalar,
				y: scaleScalar
			};
			// Best result with .../2, reason unknown
			var offset = {
				x: targetOffset.x * process /2,
				y: targetOffset.y * process /2
			};
			mapLayer.setOpacity(opacity);
			mapLayer.setScale(scale);
			mapLayer.setOffset(offset);
			actionLayer.setScale(scale);
			actionLayer.setOffset(offset);
			pointer.scale(process);
			
			mapLayer.update();
			actionLayer.update();
		});
		animation.start();
		pointerConfig = null;
	}

	function endMove(){
		
		mapControls.none();
		
		actionLayer.clearPointers();
		pointer = null;
		
		var duration = Config.view.map.fade.speed * 1000;
		
		var startOpacityValue = Config.view.map.fade.targetOpacity;
		var inverseOpacityValue = 1 - Config.view.map.fade.targetOpacity;
		
		var centering = Utils.Math2d.getCenteringWithin(
			{
				x: previousStartRegion.centerx,
				y: previousStartRegion.centery
			},
			{
				x: previousEndRegion.centerx,
				y: previousEndRegion.centery
			},
			config.width,
			config.height,
			100
		);

		var targetScale = centering.scale;
		var targetOffset = centering.offset;
		
		var animation = new Kinetic.Animation(function(frame){
			
			if(frame.time > duration){
				this.stop();
				var startRegionPath = regionPaths["id=" + previousStartRegion.id];
				var endRegionPath = regionPaths["id=" + previousEndRegion.id];
				mapLayer.addRegionPath(startRegionPath);
				mapLayer.addRegionPath(endRegionPath);
				mapLayer.setOpacity(1);
				mapLayer.setScale({x:1,y:1});
				mapLayer.setOffset({x:0, y:0});
				actionLayer.clear();
				mapLayer.update();
				actionLayer.update();
			}
			
			var process = Math.min(frame.time/duration, 1);

			var opacity = startOpacityValue + inverseOpacityValue * process;
			
			var scaleScalar = targetScale + (1 - targetScale) * process;
			var scale = {
				x: scaleScalar,
				y: scaleScalar
			};
			// Best result with .../2, reason unknown
			var offset = {
				x: targetOffset.x * (1 - process) /2,
				y: targetOffset.y * (1 - process) /2
			};
			mapLayer.setOpacity(opacity);
			mapLayer.setScale(scale);
			mapLayer.setOffset(offset);
			actionLayer.setScale(scale);
			actionLayer.setOffset(offset);
			
			mapLayer.update();
			actionLayer.update();
		});
		animation.start();
	}
	
	
	
	mapLayer = new MapLayer(kineticStage);
	actionLayer = new MapLayer(kineticStage);
	
	for(var key in model.regions){
		var regionPath = new RegionPath(model.regions[key]);
		addRegionPath(regionPath);
	}
	
	mapLayer.update();
	actionLayer.update();
	
	return self;
	
}


function MapLayer(kineticStage){
	
	var geoLayer = new Kinetic.Layer({});
	var animationLayer = new Kinetic.Layer({});
	var labelLayer = new Kinetic.Layer({});
	var troopLayer = new Kinetic.Layer({});
	
	if(kineticStage){
		kineticStage.add(geoLayer);
		kineticStage.add(labelLayer);
		kineticStage.add(troopLayer);
		kineticStage.add(animationLayer);
	}
	
	return {
		
		addRegionPath : function(regionPath){
			geoLayer.add(regionPath.getKineticPath());
			labelLayer.add(regionPath.getKineticNameLabel());
			troopLayer.add(regionPath.getKineticTroopLabel());
		},
		
		addPointer : function(pointer){
			animationLayer.add(pointer.getKinetic());
		},
		
		clearPointers : function(){
			animationLayer.removeChildren();
			this.update();
		},
	
		update : function(){
			geoLayer.draw();
			animationLayer.draw();
			labelLayer.draw();
			troopLayer.draw();
		},

		clear : function(){
			geoLayer.removeChildren();
			animationLayer.removeChildren();
			labelLayer.removeChildren();
			troopLayer.removeChildren();
			this.update();
		},
		
		setOpacity : function(opacity){
			geoLayer.opacity(opacity);
			animationLayer.opacity(opacity);
			labelLayer.opacity(opacity);
			troopLayer.opacity(opacity);
		},
		
		setScale : function(scale){
			geoLayer.scale(scale);
			animationLayer.scale(scale);
			labelLayer.scale(scale);
			troopLayer.scale(scale);
		},
		
		setOffset : function(offset){
			geoLayer.offset(offset);
			animationLayer.offset(offset);
			labelLayer.offset(offset);
			troopLayer.offset(offset);
		}
		
	};
	
}

function RegionPath(model){
	
	var parent;
	
	var state = 0;
	
	var defaultMode = Config.view.map.defaultMode;
	
	var colorKey = model[defaultMode].matchcolor;
	
	var theme = Config.view.themes[colorKey];
	
	function fire(eventName){
		var data = {
			model: model
		};
		var event = new Event(eventName, data, parent);
		parent.fire(event);
	}
	
	function getMode(){
		return parent.getMode();
	}
	
	var kineticPath = new Kinetic.Path({
		data: model.svgdata,
		fill: theme.fill[state],
		stroke: theme.stroke[state],
		strokeWidth: theme.strokeWidth[state]
	});
	kineticPath.on('mouseover', function() {
		fire("region.mouse.over");
	});
	kineticPath.on('mouseout', function() {
		fire("region.mouse.out");
	});
	kineticPath.on('click', function() {
		fire("region.mouse.click");
	});

	var nameLabelConfig = Config.view.map.regions.nameLabels;
	nameLabelConfig.text = Lang.get("region." + model.name);
	nameLabelConfig.offsetX = model.label.length + nameLabelConfig.offsetX;
	nameLabelConfig.rotation = model.angle;
	nameLabelConfig.data = model.pathdata;
	nameLabelConfig.x = model.centerx;
	nameLabelConfig.y = model.centery;
	nameLabelConfig.fill = theme.text[state];
	
	var nameLabel = new Kinetic.Text(nameLabelConfig);
	nameLabel.on('mouseover', function() {
		fire("region.mouse.over");
	});
	nameLabel.on('mouseout', function() {
		fire("region.mouse.out");
	});
	nameLabel.on('click', function() {
		fire("region.mouse.click");
	});
	
	var troopLabelConfig = Config.view.map.regions.troopLabels;
	var troopLabel = new Kinetic.Label({
		x: model.centerx - troopLabelConfig.padding - troopLabelConfig.fontSize/2,
		y: model.centery - troopLabelConfig.padding - troopLabelConfig.fontSize/2
	});
	troopLabelConfig.text = model.troops;
	troopLabelConfig.fill = theme.troops.color[state];
	var troopLabelText = new Kinetic.Text(troopLabelConfig);
	var troopLabelTag = new Kinetic.Rect({
		width: troopLabelConfig.width,
		height: troopLabelConfig.width,
		cornerRadius: troopLabelConfig.cornerRadius,

		fill: theme.troops.fill[state],
		stroke: theme.troops.stroke[state],
		strokeWidth: theme.troops.strokeWidth[state]
	});
	troopLabel.add(troopLabelTag);
	troopLabel.add(troopLabelText);
	troopLabel.on('mouseover', function() {
		fire("region.mouse.over");
	});
	troopLabel.on('mouseout', function() {
		fire("region.mouse.out");
	});
	troopLabel.on('click', function() {
		fire("region.mouse.click");
	});
	
	return {
		
		setState : function(newState){
			state = newState;
		},
		
		setParent : function(newParent){
			parent = newParent;
		},
		
		getKineticPath : function(){
			return kineticPath;
		},
		
		getKineticNameLabel : function(){
			return nameLabel;
		},
		
		getKineticTroopLabel : function(){
			return troopLabel;
		},
		
		update : function(){
			var mode = getMode();
			var colorKey = model[mode].matchcolor;
			var theme = Config.view.themes[colorKey];

			var fill = theme.fill[state];
			var stroke = theme.stroke[state];
			var strokeWidth = theme.strokeWidth[state];
			var textFill = theme.text[state];
			var troopsColor = theme.troops.color[state];
			var troopsFill = theme.troops.fill[state];
			var troopsStroke = theme.troops.stroke[state];
			var troopsStrokeWidth = theme.troops.strokeWidth[state];

			kineticPath.setFill(fill);
			kineticPath.setStroke(stroke);
			kineticPath.setStrokeWidth(strokeWidth);

			nameLabel.setFill(textFill);

			troopLabelText.setText(model.troops);
			troopLabelText.setFill(troopsColor);
			troopLabelTag.setFill(troopsFill);
			troopLabelTag.setStroke(troopsStroke);
			troopLabelTag.setStrokeWidth(troopsStrokeWidth);

			if(state > 0){
				kineticPath.moveToTop();
			} else {
				kineticPath.moveToBottom();
			}
		},
		
		getIdentifier : function(){
			return "id=" + model.id;
		}
		
	};
	
}


function Pointer(start, end, customConfig){
	
	var parent;
	
	var vec = Utils.Math2d.fromTo(start, end);
	vec = Utils.Math2d.normalize(vec);
	vec = Utils.Math2d.orthogonalize(vec);
	
	for(var key in Config.view.map.pointers){
		if(!customConfig[key]){
			customConfig[key] = Config.view.map.pointers[key];
		}
	}
	if(customConfig.fillLinearGradientColorStops){
		customConfig.fillLinearGradientStartPoint = {
			x: start.x,
			y: start.y
		};
		customConfig.fillLinearGradientEndPoint = {
			x: end.x,
			y: end.y
		};
	}
	customConfig.points = [
		start.x - 10 * vec[1].x,
		start.y - 10 * vec[1].y,
		end.x,
		end.y,
		start.x + 10 * vec[1].x,
		start.y + 10 * vec[1].y
	];
	customConfig.closed = true;
			   
	var kinetic = new Kinetic.Line(customConfig);
	
	return {
		
		setParent : function(newParent){
			parent = newParent;
		},
		
		getParent : function(){
			return parent;
		},
		
		getKinetic : function(){
			return kinetic;
		},
		
		setCustomConfig : function(newCustomConfig){
			for(var key in newCustomConfig){
				kinetic[key](newCustomConfig[key]);
			}
		},
		
		scale : function(scale){
			kinetic.setPoints([
				start.x - 10 * vec[1].x,
				start.y - 10 * vec[1].y,
				start.x + (end.x - start.x) * scale,
				start.y + (end.y - start.y) * scale,
				start.x + 10 * vec[1].x,
				start.y + 10 * vec[1].y
			]);
		}
		
	};
	
}


function ActionPointer(startRegion, endRegion, type){
	var start = {
		x: startRegion.centerx,
		y: startRegion.centery
	};
	var end = {
		x: endRegion.centerx,
		y: endRegion.centery
	};
	var colorStops = Config.view.map.pointers.fillLinearGradientColorStops[type];
	var customConfig = {
		fillLinearGradientColorStops: colorStops
	};
	
	var pointer = new Pointer(start, end, customConfig);
	pointer.isType = function(testType){
		return testType === type;
	};
	pointer.setType = function(newType){
		type = newType;
		var colorStops = Config.view.map.pointers.fillLinearGradientColorStops[type];
		var customConfig = {
			fillLinearGradientColorStops: colorStops
		};
		this.setCustomConfig(customConfig);
	};
	return pointer;
}


function MapButton(id, content, classes){
	var element = HTML.make("button", "btn btn-primary " + classes, id);
	if(content.indexOf(".png") !== -1 || content.indexOf(".jpg") !== -1){
		content = HTML.make("img").attr("src", "/img/" + content);
		element.append(content);
	} else {
		element.html(content);
	}
	element.click(function(){
		var event = new Event(id + ".clicked");
		Controller.listen(event);
		element.blur();
	});
	element.disable = function(){
		element.prop("disabled", true);
	};
	element.enable = function(){
		element.prop("disabled", false);
	};
	return element;
}



function MapControls(elementId){
	
	var base = $("#" + elementId);
	base.addClass("mapControlPanel");
	base.draggable({ snap: "#game-map" });
	
	var mode = null;
	
	var title = HTML.make("div");
	title.html(Lang.get("controls"));
	base.append(title);
	
	var activePlayerLabel = HTML.make("div");
	base.append(activePlayerLabel);
	
	var currentPhase = HTML.make("div");
	base.append(currentPhase);
	var currentPhaseLabel = HTML.make("div");
	var currentPhaseSymbols = HTML.make("div");
	var phaseSymbols = {
		"troopgain" : null,
		"troopdeployment" : null,
		"attack" : null,
		"troopshift" : null
	};
	for(var key in phaseSymbols){
		phaseSymbols[key] = HTML.make("img", "state-symbol");
		phaseSymbols[key].attr("src", "/img/" + key + ".png");
		phaseSymbols[key].attr("title", Lang.get("phase." + key));
		currentPhaseSymbols.append(phaseSymbols[key]);
	}
	currentPhase.append(currentPhaseSymbols);
	currentPhase.append(currentPhaseLabel);
	base.append(currentPhase);
	
	var newTroopsLabel = HTML.make("div");
	base.append(newTroopsLabel);
	
	var buttonPanel = HTML.make("div");
	
	var attackButtons = HTML.make("div", "control-group");
	var attackConfirmButton = new MapButton("button.attack.confirm", "confirm.png", "attack");
	var attackCancelButton = new MapButton("button.attack.cancel", "cancel.png", "cancel");
	attackButtons.append(attackConfirmButton);
	attackButtons.append(attackCancelButton);
	
	var troopShiftButtons = HTML.make("div", "control-group");
	var shiftPlusButton = new MapButton("button.troopshift.plus", "plus.png", "plus");
	var shiftConfirmButton = new MapButton("button.troopshift.confirm", "confirm.png", "confirm");
	var shiftMinusButton = new MapButton("button.troopshift.minus", "minus.png", "minus");
	troopShiftButtons.append(shiftPlusButton);
	troopShiftButtons.append(shiftConfirmButton);
	troopShiftButtons.append(shiftMinusButton);
	
	buttonPanel.append(attackButtons);
	buttonPanel.append(troopShiftButtons);
	base.append(buttonPanel);
	
	var dicerPanel = HTML.make("div", "control-group", "dicer");
	base.append(dicerPanel);
	var dicer = new Dicer(100, 100, dicerPanel);
	
	var width = base.width();
	var height = base.height();
	
	var self = {
		
		position : function (x, y){
			base.css("margin", x + "px 0 0 " + y + "px");
		},
		
		active : function(isActive){
			if(isActive){
				buttonPanel.show();
			} else {
				buttonPanel.hide();
			}
		},
		
		phase : function(newPhase){
			var activePhaseSymbol = phaseSymbols[newPhase];
			if(activePhaseSymbol){
				for(var key in phaseSymbols){
					phaseSymbols[key].removeClass("active");
				}
				activePhaseSymbol.addClass("active");
				currentPhaseLabel.html(Lang.get("phase." + newPhase));
			}
		},
		
		activePlayer : function(newActivePlayer){
			var content = Lang.get("active.player") + ": ";
			content = content + newActivePlayer.name;
			activePlayerLabel.html(content);
		},
		
		newTroops : function(newTroops){
			if(newTroops > 0){
				newTroopsLabel.show();
				newTroopsLabel.html(Lang.get("available.troops") + ": " + newTroops);
			} else {
				newTroopsLabel.hide();
			}
		},
		
		mode : function(newMode){
			var style = base.attr("style");
			if(style){
				style = style.replace(/height:\s*([0-9])*px;/, '').replace(/width:\s*([0-9])*px;/, '');
				base.attr("style", style);
			}
			mode = newMode;
			if(mode === "attack"){
				attackButtons.show();
				troopShiftButtons.hide();
				dicerPanel.show();
			} else if(mode === "troopshift"){
				attackButtons.hide();
				troopShiftButtons.show();
			} else {
				troopShiftButtons.hide();
				attackButtons.hide();
				dicerPanel.hide();
				dicer.clear();
			}
		},
		
		attack : function(){
			this.mode("attack");
		},
		
		troopShift : function(){
			this.mode("troopshift");
		},
		
		none : function(){
			this.mode(null);
		},
		
		diceRolling : function(){
			return dicer.isRolling();
		},
		
		diceStart : function(diceNumAttackor, diceNumDefender){
			dicer.start(diceNumAttackor, diceNumDefender);
		},
		
		diceEndWith : function(result, callback){
			dicer.endWith(result, callback);
		}
		
	};
	
	self.none();
	
	return self;
}

function Dicer(width, height, element){
	
	var private;
	
	var result = [];
	var resultCallback;
	
	var rolling = false;
	
	var stage = new Kinetic.Stage({
		container: element.attr("id"),
		width: width,
		height: height
	});
	
	var dices = {
		attackor: new Array(),
		defender: new Array(),
		pointers: new Array(),
		
		clear : function(){
			delete this.attackor;
			delete this.defender;
			delete this.pointers;
			this.attackor = new Array();
			this.defender = new Array();
			this.pointers = new Array();
		},
		
		addDie : function(to){
			var xPos = 0;
			if (to === "defender"){
				xPos = 60;
			}
			var die = new Die({
				x : xPos,
				y : this[to].length * 35
			}, images, diceLayer);
			this[to].push(die);
		},
		
		nextFrame : function(time){
			var atLeastOneRunning = false;
			for(var i in this.attackor){
				atLeastOneRunning |= this.attackor[i].nextFrame(time);
			}
			for(var i in this.defender){
				atLeastOneRunning |= this.defender[i].nextFrame(time);
			}
			return atLeastOneRunning;
		},
		
		stop : function(result){
			for(var i in this.attackor){
				this.attackor[i].stop(result[i][1]);
			}
			for(var i in this.defender){
				this.defender[i].stop(result[i][2]);
			}
		},
		
		animateResult : function(){
			for(var i in result){
				var success = result[i][0];
				if(success === "win" && this.attackor[i] && this.defender[i]){
					var dicePointer = new DicePointer(
						this.attackor[i],
						this.defender[i],
						true
					);
					diceLayer.add(dicePointer.getKinetic());
					this.pointers.push(dicePointer);
				} else if(success === "lose" && this.attackor[i] && this.defender[i]){
					var dicePointer = new DicePointer(
						this.defender[i],
						this.attackor[i],
						false
					);
					diceLayer.add(dicePointer.getKinetic());
					this.pointers.push(dicePointer);
				}
			}
			diceLayer.draw();
			var duration = 250;
			var pointers = this.pointers;
			var animation = new Kinetic.Animation(function(frame){
			
				if(frame.time > duration){
					this.stop();
					for(var i in pointers){
						pointers[i].scale(1);
					}
					if(resultCallback){
						resultCallback();
						View.update();
					}
				}

				var process = Math.min(frame.time/duration, 1);
				for(var i in pointers){
					pointers[i].scale(process);
				}
				diceLayer.draw();

			});
			animation.start();
		}
	};
	
	var diceLayer = new Kinetic.Layer({});
	stage.add(diceLayer);
	
	var images = {
		lying : [
			"/img/dice/die-1.png",
			"/img/dice/die-2.png",
			"/img/dice/die-3.png",
			"/img/dice/die-4.png",
			"/img/dice/die-5.png",
			"/img/dice/die-6.png"
		],
		horizontal : [
			"/img/dice/dices-1.png",
			"/img/dice/dices-2.png",
			"/img/dice/dices-3.png",
			"/img/dice/dices-4.png",
			"/img/dice/dices-5.png",
			"/img/dice/dices-6.png"
		],
		vertical : [
			"/img/dice/dicet-1.png",
			"/img/dice/dicet-2.png",
			"/img/dice/dicet-3.png",
			"/img/dice/dicet-4.png",
			"/img/dice/dicet-5.png",
			"/img/dice/dicet-6.png"
		]
	};
	
	for(var mode in images){
		for(var index in images[mode]){
			var src = images[mode][index];
			var img = new Image();
			img.src = src;
			images[mode][index] = img;
		}
	}
	
	function clear(){
		dices.clear();
		diceLayer.removeChildren();
		diceLayer.draw();
		resultCallback = null;
	}
	
	function extractDiceNumber(result){
		var numbers = [0,0];
		for(var i in result){
			var resultPart = result[i];
			if(resultPart[1]){
				++numbers[0];
			}
			if(resultPart[2]){
				++numbers[1];
			}
		}
		return numbers;
	}
	
	private = {
		
		start : function(diceNumAttackor, diceNumDefender){
			clear();
			for(var i = 0; i < diceNumAttackor; i++){
				dices.addDie("attackor");
			}
			for(var i = 0; i < diceNumDefender; i++){
				dices.addDie("defender");
			}
			var animation = new Kinetic.Animation(function(frame){
				var running = dices.nextFrame(frame.time);
				diceLayer.draw();
				if(!running){
					this.stop();
					dices.animateResult();
				}
			});
			animation.start();
			rolling = true;
		},
		
		endWith : function(endResult, callback){
			if(!this.isRolling()){
				var diceNumbers = extractDiceNumber(endResult);
				this.start(diceNumbers[0], diceNumbers[1]);
			}
			result = endResult;
			dices.stop(result);
			rolling = false;
			resultCallback = callback;
		},
		
		isRolling : function(){
			return rolling;
		},
		
		hide : function(){
			element.hide();
			clear();
		},
		
		show : function(){
			clear();
			element.show();
		},
		
		clear : function(){
			clear();
		}
		
	};
	
	return private;
}

function Die(position, images, layer){
	
	var modes = Object.keys(images);
	var mode = 0;

	function random(range){
		return Math.ceil(range * Math.random());
	}
	
	var finishFrames = 2 * random(10);
	var terminate = false;
	var lastFrameTime;
	var result = -1;
	
	function getNextMode(){
		if(mode === 0){
			mode = random(2);
		} else {
			mode = 0;
		}
		if(finishFrames === 1){
			mode = 0;
		}
		return modes[mode];
	}
	
	function getNextNumber(){
		var num = random(6);
		if(finishFrames === 1){
			num = result;
		}
		return num;
	}
	
	var image = new Kinetic.Image({
		x: position.x,
		y: position.y,
		image : images["lying"][0]
	});
	layer.add(image);
	
	return {
		
		getPosition : function(){
			return position;
		},
		
		x : function(){
			return position.x;
		},
		
		y : function(){
			return position.y;
		},
		
		width : function(){
			return image.width();
		},
		
		height : function(){
			return image.height();
		},
		
		nextFrame : function(time){
			if(finishFrames <= 0){
				return false;
			}
			if(time - lastFrameTime < 50){
				return true;
			}
			lastFrameTime = time;
			var nextMode = getNextMode();
			var nextNumber = getNextNumber();
			var nextImage = images[nextMode][nextNumber - 1];
			image.image(nextImage);
			if(terminate){
				--finishFrames;
			}
			return true;
		},
		
		stop : function(target){
			result = target;
			terminate = true;
			mode = 0;
		}
		
	};
	
}
	
function DicePointer(startDie, endDie, success){
	var color = (success ? "#0f0" : "#f00");
	var transparentColor = (success ? "rgba(0,255,0,0)" : "rgba(255,0,0,0)");
	var startCenter = {
		x : startDie.x() + startDie.width()/2,
		y : startDie.y() + startDie.height()/2
	};
	var endCenter = {
		x : endDie.x() + endDie.width()/2,
		y : endDie.y() + endDie.height()/2
	};
	var start = {
		x : startCenter.x + (startCenter.x > endCenter.x ? -1 : 1 ) * startDie.width()/2,
		y : startCenter.y
	};
	var end = {
		x : endCenter.x + (startCenter.x < endCenter.x ? -1 : 1 ) * endDie.width()/2,
		y : endCenter.y
	};
	var config = {
		fillLinearGradientColorStops: [0, transparentColor, 0.5, color, 1, color]
	};
	var pointer = new Pointer(start, end, config);
	pointer.scale(0);
	return pointer;
}



function Chat(elementId, parent) {
	
	var container = $("#" + elementId),
		content = $("#" + elementId + "content"),
		input = $("#" + elementId + "input");

	var lastMessageSender,
		lastContentContainer;
	
	input.keypress(function(event){
		if(event.keyCode === 13 && input.val()){
			var evt = new Event(
				"message.send",
				{text : input.val()},
				parent
			);
			parent.fire(evt);
			input.val("");
		}
	});
	
	container.mouseover(function(){
		$("*[toggle-for=" + elementId + "]").removeClass("changed");
	});
		
	return {
		
		addMessage : function(message, user){
			var msgContainer = HTML.make("div", "chatMessage message-" + user.matchcolor);
			
			var nameLabelContainer = HTML.make("div", "chatMessageUserName").html(user.name + ":");
			var text = HTML.make("div", "chatMessageText").html(message);
			var textContainer = HTML.make("div", "chatMessageTexts");
			var contentContainer = HTML.make("div", "chatMessageContent");
			contentContainer
					.append(nameLabelContainer)
					.append(textContainer);
			
			var userContainer = HTML.make("div", "chatMessageUser");
			var avatar = HTML.make("img", "user-avatar")
					.attr("src", "/img/avatars/" + user.avatarfile);
			userContainer.append(avatar);
			
			if(lastMessageSender === user){
				lastContentContainer.append(text);
			} else {
				textContainer.append(text);
				if(user === Model.me){
					msgContainer.append(contentContainer);
					msgContainer.append(userContainer);
				} else {
					msgContainer.append(userContainer);
					msgContainer.append(contentContainer);
				}
				content.append(msgContainer);
				lastContentContainer = textContainer;
			}
			
			content.animate({ scrollTop: content.prop("scrollHeight") }, "slow");
			lastMessageSender = user;
			
			if(user !== Model.me){
				$("*[toggle-for=" + elementId + "]").addClass("changed");
			}
		}
		
	};
	
}

function Log(elementId){
	
	var TYPE_NORMAL = "normal";
	
	var container = $("#" + elementId + "content"),
		entries = [];

	function log(message, type){
		if(!type){
			type = TYPE_NORMAL;
		}
		var entry = {
			message: message,
			type: type,
			time: new Date()
		};
		entries.push(entry);
		addLogMessage(entry);
	}
	
	function addLogMessage(entry){
		var entryContainer = HTML.make("div", "log-entry " + entry.type);
		var message = entry.time.getDate()
				+ "-" + entry.time. getMonth()
				+ "-" + entry.time.getFullYear()
				+ ", " + entry.time.getHours()
				+ ":" + entry.time.getMinutes()
				+ ":" + entry.time.getSeconds();
		message += ": " + entry.message;
		entryContainer.html(message);
		container.append(entryContainer);
		container.animate({ scrollTop: container.prop("scrollHeight") }, "slow");
	}
	
	return {
		
		log : function(message){
			log(message, TYPE_NORMAL);
		}
		
	}
	
}

function PlayerList(elementId){
	
	var container = $("#" + elementId + "content"),
		playerContainers = [];
	
	for(var i in Model.players){
		var player = Model.players[i];
		
		var element = {
			playerData : HTML.make("div", "data player"),
			img : HTML.make("img", "user-avatar").attr("src", "/img/avatars/" + player.avatarfile),
			state : HTML.make("div", "state"),
			nameLabel : HTML.make("span").html(player.name)
		};
		element.playerData
				.append(element.img)
				.append(element.state)
				.append(element.nameLabel);
		container.append(element.playerData);
		playerContainers["id=" + player.id] = element;
	}
	
	var self = {
		
		online : function(player){
			var playerIdentifier = "id=" + player.id;
			var container = playerContainers[playerIdentifier];
			container.state.removeClass("offline").addClass("online");
			container.state.attr("title", "Online");
		},
		
		offline : function(player){
			var playerIdentifier = "id=" + player.id;
			var container = playerContainers[playerIdentifier];
			container.state.removeClass("online").addClass("offline");
			container.state.attr("title", "Offline");
		},
		
		unknown : function(player){
			var playerIdentifier = "id=" + player.id;
			var container = playerContainers[playerIdentifier];
			container.state.removeClass("online").removeClass("offline");
			container.state.attr("title", "Unkown");
		},
		
		activePlayer : function(player){
			$(".data.player").removeClass("active");
			playerContainers["id=" + player.id].playerData.addClass("active");
		},
		
		update : function(){
			for(var i in Model.players){
				var player = Model.players[i];
				if(player.isonline){
					self.online(player);
				} else {
					self.offline(player);
				}
			}
		}
		
	};
	
	self.update();
	self.activePlayer(Model.activePlayer);
	
	return self;
	
}

function SideBar(model, config, context) {
	
	var parent;
	
	var self = {
		setParent : function(newParent){
			parent = newParent;
		},
		
		getParent : function(){
			return parent;
		},
		
		render : function(){
		},
		
		update : function(){
			if(context.newChatMessage){
				var msg = context.newChatMessage;
				chat.addMessage(msg.text, msg.user);
				delete context.newChatMessage;
			}
			playerList.update();
		},

		fire : function(event){
			parent.fire(event);
		}
	
	};
	
	var chat = new Chat("chat", self);
	
	var log = new Log("log", self);
	window.log = log.log;
	
	var playerList = new PlayerList("players");
	window.online = playerList.online;
	window.offline = playerList.offline;
	
	return self;
}