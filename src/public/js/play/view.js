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
	
	var mapLayers = [];
	
	var regionPaths = [];
	
	
	
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
			for(var mapLayerName in mapLayers){
				var mapLayer = mapLayers[mapLayerName];
				mapLayer.update();
			}
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
	
	function addLayer (layerName, layer){
		mapLayers[layerName] = layer;
		mapLayers[layerName].setParent(self);
		kineticStage.add(mapLayers[layerName].getKinetic());
	}
		
	function addRegionPath (regionPath){
		regionPaths[regionPath.getIdentifier()] = regionPath;
		regionPath.setParent(self);
		mapLayers["regionsLayer"].addKinetic(regionPath.getKineticPath());
		mapLayers["regionsNameLayer"].addKinetic(regionPath.getKineticNameLabel());
		mapLayers["troopLabelLayer"].addKinetic(regionPath.getKineticTroopLabel());
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
		pointer.animate(mapLayers["animationLayer"]);
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
		pointer.animate(mapLayers["animationLayer"]);
		delete pointerConfig;
	}

	function clearPointers(){
		mapLayers["animationLayer"].clear();
		pointer = null;
		fadeInMap();
	}
	
	function fadeDownMap(startRegion, endRegion){
		
		mapLayers["actionLayer"].clear();
		var startRegionPath = new RegionPath(startRegion);
		var endRegionPath = new RegionPath(endRegion);
		startRegionPath.setParent(self);
		startRegionPath.setState(CLICKED_STATE);
		endRegionPath.setState(CLICKED_STATE);
		endRegionPath.setParent(self);
		/*startRegionPath.getKineticPath().x(100-startRegion.centerx);
		startRegionPath.getKineticPath().y(100-startRegion.centery);
		endRegionPath.getKineticPath().x(400-endRegion.centerx);
		endRegionPath.getKineticPath().y(100-endRegion.centery);*/
		mapLayers["actionLayer"].addKinetic(startRegionPath.getKineticPath());
		//mapLayers["actionLayer"].addKinetic(startRegionPath.getKineticNameLabel());
		//mapLayers["actionLayer"].addKinetic(startRegionPath.getKineticTroopLabel());
		mapLayers["actionLayer"].addKinetic(endRegionPath.getKineticPath());
		//mapLayers["actionLayer"].addKinetic(endRegionPath.getKineticNameLabel());
		//mapLayers["actionLayer"].addKinetic(endRegionPath.getKineticTroopLabel());
		
		
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
		
		
		/*mapLayers["regionsLayer"].addKinetic(new Kinetic.Ellipse({
			radius: {x: 5, y:5},
			fill: "red",
			x: 400,
			y: 250
		}));
		mapLayers["actionLayer"].addKinetic(new Kinetic.Ellipse({
			radius: {x: 5, y:5},
			stroke: "yellow",
			strokeWidth: 2,
			x: startRegion.centerx + 0.5*(endRegion.centerx - startRegion.centerx),
			y: startRegion.centery + 0.5*(endRegion.centery - startRegion.centery)
		}));*/
		
		var animation = new Kinetic.Animation(function(frame){
			
			if(frame.time > duration){
				this.stop();
			}
			
			var process = frame.time/duration;

			var opacity = 1 - inverseOpacityValue * process;
			mapLayers["regionsLayer"].getKinetic().opacity(opacity);
			mapLayers["regionsNameLayer"].getKinetic().opacity(opacity);
			mapLayers["animationLayer"].getKinetic().opacity(opacity);
			mapLayers["troopLabelLayer"].getKinetic().opacity(opacity);
			
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
			mapLayers["actionLayer"].getKinetic().scale(scale);
			mapLayers["actionLayer"].getKinetic().offset(offset);
			
			var actionOpacity = (frame.time/duration);
			mapLayers["actionLayer"].getKinetic().opacity(actionOpacity);
			
			
			mapLayers["regionsLayer"].update();
			mapLayers["regionsNameLayer"].update();
			mapLayers["animationLayer"].update();
			mapLayers["troopLabelLayer"].update();
			mapLayers["actionLayer"].update();
		});
		animation.start();
	}
	
	function fadeInMap(){
		
		var duration = Config.view.map.fade.speed * 1000;
		
		var startOpacityValue = Config.view.map.fade.targetOpacity;
		var inverseOpacityValue = 1 - Config.view.map.fade.targetOpacity;
		
		var animation = new Kinetic.Animation(function(frame){

			var newOpacity = startOpacityValue + inverseOpacityValue * (frame.time/duration);
			mapLayers["regionsLayer"].getKinetic().opacity(newOpacity);
			mapLayers["regionsNameLayer"].getKinetic().opacity(newOpacity);
			mapLayers["animationLayer"].getKinetic().opacity(newOpacity);
			mapLayers["troopLabelLayer"].getKinetic().opacity(newOpacity);
			
			var actionOpacity = Math.max(1 - (frame.time/duration), 0);
			mapLayers["actionLayer"].getKinetic().opacity(actionOpacity);
			
			mapLayers["regionsLayer"].update();
			mapLayers["regionsNameLayer"].update();
			mapLayers["animationLayer"].update();
			mapLayers["troopLabelLayer"].update();
			mapLayers["actionLayer"].update();
			
			if(frame.time > duration){
				this.stop();
				mapLayers["regionsLayer"].getKinetic().opacity(1);
				mapLayers["regionsNameLayer"].getKinetic().opacity(1);
				mapLayers["animationLayer"].getKinetic().opacity(1);
				mapLayers["troopLabelLayer"].getKinetic().opacity(1);
				mapLayers["actionLayer"].clear();
			}
		});
		animation.start();
	}
	
	
	
	addLayer("regionsLayer", new MapLayer());
	addLayer("regionsNameLayer", new MapLayer());
	addLayer("animationLayer", new MapLayer());
	addLayer("troopLabelLayer", new MapLayer());
	addLayer("actionLayer", new MapLayer());
	
	for(var key in model.regions){
		var regionPath = new RegionPath(model.regions[key]);
		addRegionPath(regionPath);
	}
	
	mapLayers["regionsLayer"].update();
	mapLayers["regionsNameLayer"].update();
	mapLayers["animationLayer"].update();
	mapLayers["troopLabelLayer"].update();
	mapLayers["actionLayer"].update();
	
	
	
	return self;
	
}


function MapLayer(){
	
	var parent = null;
	
	var kineticLayer = new Kinetic.Layer({});
	
	return {
	
		addKinetic : function(kineticObject){
			kineticLayer.add(kineticObject);
		},

		getKinetic : function(){
			return kineticLayer;
		},
	
		setParent : function(newParent){
			parent = newParent;
		},
	
		getParent : function(){
			return parent;
		},
	
		update : function(){
			kineticLayer.draw();
		},

		clear : function(){
			kineticLayer.removeChildren();
			this.update();
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
		
		animate : function(layer){
			
			layer.addKinetic(kinetic);
			
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
				layer.getKinetic().draw();
				
				if(frame.time > duration){
					this.stop();
				}
			});
			animation.start();
		}
		
	};
	
}