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
				
				var move = context.getMove();
				if(!pointer && move){
					var startRegion = move.start;
					var endRegion = move.end;
					if(move.type === "attack"){
						attackMoveFromTo(startRegion, endRegion);
					} else if(move.type === "shift"){
						troopshiftMoveFromTo(startRegion, endRegion);
					}
				} else if(pointer && !move){
					endMove();
				}
				if(move && context.attackResult == "waiting"){
					attackControls.inactive();
				} else {
					attackControls.active();
				}
				
				// Prüfung ändern
				if(Utils.Type.is(context.attackResult, "Array")){
					console.log(context.attackResult);
					context.attackResult = null;
				}
			}
			
		},
		
		update : function(){
			
			this.render();

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
	
	var attackControls = new AttackControls(config.containerId);
	
	var kineticStage = new Kinetic.Stage({
		container: config.containerId,
		width: config.width,
		height: config.height
	});
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

	function waiting(){
		attackControls.inactive();
	}

	function continuing(){
		attackControls.active();
	}

	function attackMoveFromTo(startRegion, endRegion){
		pointer = new ActionPointer(startRegion, endRegion, "attack");
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
				attackControls.show(config.height/2, config.width/2, "top-right");
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

	function troopshiftMoveFromTo(startRegion, endRegion){
		pointer = new Pointer(
			{
				x: startRegion.centerx,
				y: startRegion.centery
			},
			{
				x: endRegion.centerx,
				y: endRegion.centery
			},
			{
				fillLinearGradientColorStops: config.pointers.fillLinearGradientColorStops.troopshift
			}
		);
		pointer.animate(mapLayer);
		pointerConfig = null;
	}

	function endMove(){
		
		attackControls.hide();
		
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
	
	var attackControls = new AttackControls(config.containerId);
	
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
	nameLabelConfig.text = model.name;
	nameLabelConfig.offsetX = model.name.length + nameLabelConfig.offsetX;
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
	return new Pointer(start, end, customConfig);
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
	});
	element.disable = function(){
		element.prop("disabled", true);
	};
	element.enable = function(){
		element.prop("disabled", false);
	};
	return element;
}

function AttackControls(parentElementId){
	
	var attackConfirmButton = new MapButton("attackConfirmButton", "confirm.png", "attack");
	var attackCancelButton = new MapButton("attackCancelButton", "cancel.png", "cancel");
	
	var buttonPanel = HTML.make("div", "mapControlPanel", "attackControlPanel");
	buttonPanel.append(attackConfirmButton);
	buttonPanel.append(attackCancelButton);
	buttonPanel.hide();
	
	$("#" + parentElementId).prepend(buttonPanel);
	
	var width = buttonPanel.width();
	var height = buttonPanel.height();
	buttonPanel.draggable();
	
	return {
		
		position : function (x, y, fromEdge){
			if(x && y){
				var marginString = "";
				if(fromEdge === "top-right"){
					marginString = x + "px 0 0 " + (y - width) + "px";
				} else if(fromEdge === "top-left"){
					marginString = x + "px 0 0 " + y + "px";
				} else if(fromEdge === "bottom-right"){
					marginString = (x - height) + "px 0 0 " + (y - width) + "px";
				} else if(fromEdge === "bottom-left"){
					marginString = (x - height) + "px 0 0 " + y + "px";
				} else {
					marginString = (x - height/2) + "px 0 0 " + (y - width/2) + "px";
				}
				buttonPanel.css("margin", marginString);
			}
		},
		
		show : function (x, y, fromEdge){
			this.position(x, y, fromEdge);
			buttonPanel.show();
		},
		
		hide : function (){
			buttonPanel.hide();
		},
		
		active : function(){
			buttonPanel.removeClass("inactive");
			attackConfirmButton.enable();
			attackCancelButton.enable();
		},
		
		inactive : function(){
			buttonPanel.addClass("inactive");
			attackConfirmButton.disable();
			attackCancelButton.disable();
		}
		
	};
}