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
				if (context.move.start === region || context.move.end === region){
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
						attackFromTo(startRegion, endRegion);
					} else if(move.type === "shift"){
						troopShiftFromTo(startRegion, endRegion);
					}
				} else if(pointer && !move){
					clearPointers();
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

	function attackFromTo(startRegion, endRegion){
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
				fillLinearGradientColorStops: [0, '#ff0', 0.05, '#ff0', 0.4, '#f00', 1, '#f00']
			}
		);
		pointer.animate(mapLayer);
		fadeDownMap(startRegion, endRegion);
		delete pointerConfig;
	}

	function troopShiftFromTo(startRegion, endRegion){
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
				fillLinearGradientColorStops: [0, 'rgba(0,0,0,0)', 0.05, 'rgba(0,0,0,0)', 0.4, '#222', 1, '#222']
			}
		);
		pointer.animate(mapLayer);
		delete pointerConfig;
	}

	function clearPointers(){
		mapLayer.clearPointers();
		pointer = null;
		fadeInMap();
	}
	
	function fadeDownMap(startRegion, endRegion){
		
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
				mapLayer.update();
				actionLayer.update();
			}
			
			var process = frame.time/duration;

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
			
			//var actionOpacity = (frame.time/duration);
			//actionLayer.setOpacity(actionOpacity);
			
			mapLayer.update();
			actionLayer.update();
		});
		animation.start();
	}
	
	function fadeInMap(){
		
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
				actionLayer.clear();
				mapLayer.update();
				actionLayer.update();
			}
			
			var process = frame.time/duration;

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

			/*var newOpacity = startOpacityValue + inverseOpacityValue * (frame.time/duration);
			mapLayer.setOpacity(newOpacity);
			
			var actionOpacity = Math.max(1 - (frame.time/duration), 0);
			actionLayer.setOpacity(actionOpacity);*/
			
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
	var labelLayer = new Kinetic.Layer({});
	var troopLayer = new Kinetic.Layer({});
	var animationLayer = new Kinetic.Layer({});
	
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
			labelLayer.draw();
			troopLayer.draw();
			animationLayer.draw();
		},

		clear : function(){
			geoLayer.removeChildren();
			labelLayer.removeChildren();
			troopLayer.removeChildren();
			animationLayer.removeChildren();
			this.update();
		},
		
		setOpacity : function(opacity){
			geoLayer.opacity(opacity);
			labelLayer.opacity(opacity);
			troopLayer.opacity(opacity);
			animationLayer.opacity(opacity);
		},
		
		setScale : function(scale){
			geoLayer.scale(scale);
			labelLayer.scale(scale);
			troopLayer.scale(scale);
			animationLayer.scale(scale);
		},
		
		setOffset : function(offset){
			geoLayer.offset(offset);
			labelLayer.offset(offset);
			troopLayer.offset(offset);
			animationLayer.offset(offset);
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
	
	var config = Config.view.map.pointers;
	if(customConfig.fill){
		config.fill = customConfig.fill;
	}
	if(customConfig.stroke){
		config.stroke = customConfig.stroke;
	}
	if(customConfig.strokeWidth){
		config.strokeWidth = customConfig.strokeWidth;
	}
	if(customConfig.fillLinearGradientColorStops){
		config.fillLinearGradientColorStops = customConfig.fillLinearGradientColorStops;
		config.fillLinearGradientStartPoint = {
			x: start.x,
			y: start.y
		};
		config.fillLinearGradientEndPoint = {
			x: end.x,
			y: end.y
		};
	}
	config.points = [
		start.x - 10 * vec[1].x,
		start.y - 10 * vec[1].y,
		end.x,
		end.y,
		start.x + 10 * vec[1].x,
		start.y + 10 * vec[1].y
	];
	config.closed = true;
			   
	var kinetic = new Kinetic.Line(config);
	
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
		
		animate : function(mapLayer){
			
			mapLayer.addPointer(this);
			
			var animation = new Kinetic.Animation(function(frame){
				var duration = config.speed * 1000;
				kinetic.setPoints([
					start.x - 10 * vec[1].x,
					start.y - 10 * vec[1].y,
					start.x + (end.x - start.x) * frame.time/duration,
					start.y + (end.y - start.y) * frame.time/duration,
					start.x + 10 * vec[1].x,
					start.y + 10 * vec[1].y
				]);
				mapLayer.update();
				
				if(frame.time > duration){
					this.stop();
				}
			});
			animation.start();
		}
		
	};
	
}