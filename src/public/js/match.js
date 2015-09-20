
var Config = {

	controller : {
		globalEvents : {

		},

		behaviors : {

			initial : {

				uses : [
					//"mouseoverRegionPath",
					//"mouseoutRegionPath",
					//"clickRegionPath"
				],
				
				"mouseoverRegionPath" : function(event){
					View.Map.receive(event);
				},

				"mouseoutRegionPath" : function(event){
					View.Map.receive(event);
				},

				"clickRegionPath" : function(event){
					View.Map.receive(event);
					Controller.behave("selectTarget", event.data);
					socket.send("select.region", event.data.model.id);
				}

			},
			
			selectTarget : {
				
				init : function(data){
					this.source = data.model;
				},
				
				"mouseoverRegionPath" : function(event){
					if(this.source !== event.data.model){
						View.Map.receive(event);
					}
				},

				"mouseoutRegionPath" : function(event){
					if(this.source !== event.data.model){
						View.Map.receive(event);
					}
				},

				"clickRegionPath" : function(event){
					if(this.source !== event.data.model){
						View.Map.receive(event);
					} else if(this.source == event.data.model){
						this.source = null;
						event.name = "mouseoverRegionPath";
						View.Map.receive(event);
						Controller.behave("initial");
					}
				}
			}

		}
	
	},
	
	view : {
		map : {
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

function GameSocket(url) {

	var self = this;

	this._webSocket = new WebSocket(url);
	this._events = new Array();

	this._webSocket.addEventListener("open", function (e) {
		if (typeof self._events["open"] == "function") {
			self._events["open"]();
		}

	});

	this._webSocket.addEventListener("close", function (e) {
	});

	this._webSocket.addEventListener("error", function (e) {
	});

	this._webSocket.addEventListener("message", function (e) {
		var message = JSON.parse(e.data);
		if (typeof self._events[message.type] === "function") {
			self._events[message.type](message.data);
		}
	});

	this.send = function (type, data) {
		var msg = JSON.stringify({
			"type": type,
			"data": data
		});
		self._webSocket.send(msg);
	};

	this.on = function (eventName, callback) {
		self._events[eventName] = callback;
	};

}

var socket = new GameSocket("ws://dev.app.risk:7778/?joinid=" + joinId);

socket.on("open", function () {
	socket.send("get.all");
});
socket.on("get.all", function (data) {
	Model.digest(data);
	View.Map = new Map(Model, Config.view.map);
	Controller.behave("initial");
});
socket.on("select.region", function (data) {
	var region = Model.get(data);
	var event = new Event("clickRegionPath", {model: region}, this);
	Controller.fire(event);
});
socket.on("chat.message", function (message) {
	//chat.receive(message.data, message.user);
});


var Model = {
	
	digest : function(data) {
		for(var index in data){
			var property = data[index];
			this[index] = property;
		}
		
		this.setupRelationsRecursively();
		
	},

	setupRelationsRecursively : function(object){
		
		if(!object){
			object = this;
		}
		
		for(var propertyName in object){
			var propertyValue = object[propertyName];
			if(this.typeOf(propertyValue) === "String"){
				var reference = this.get(propertyValue);
				if(! (reference === undefined) ){
					object[propertyName] = reference;
				}
			}
			else if(this.typeOf(propertyValue) === "Array"){
				for(var key in propertyValue){
					var arrayElement = propertyValue[key];
					if(this.typeOf(arrayElement) === "String"){
						var ref = this.get(arrayElement);
						if(! (ref === undefined) ){
							propertyValue[key] = ref;
						}
					} else if(this.typeOf(arrayElement) === "Object"){
						this.setupRelationsRecursively(arrayElement);
					}
				}
			}
		}
	},
	
	
	get : function(descriptor){
		descriptor = descriptor.replace("[", '').replace("]", '');

		var parts = descriptor.split(":");
		var variable = parts[0];
		var property = parts[1];

		if(!property){
			return undefined;
		}

		var propertyParts = property.split("=");
		var propertyName = propertyParts[0];
		var propertyValue = propertyParts[1];

		var searchTarget = this[variable];
		if(!searchTarget){
			return searchTarget;
		}

		if(this.typeOf(searchTarget) === "Array"){
			for(var key in searchTarget){
				var candidate = searchTarget[key];
				var candidatePropertyValue = candidate[propertyName];
				if(candidatePropertyValue && candidatePropertyValue == propertyValue){
					return candidate;
				}
			}
			if(propertyValue){
			}
			return null;
		}
		//else if(typeString == "[object Object]"){
		//	return searchTarget;
		//}

		return undefined;
	},
	
	typeOf : function (object){
		var typeString = Object.prototype.toString.call(object);
		var type = typeString
			.replace("[object ", '')
			.replace("]", '');
		return type;
	}
};

var View = {
	
	viewMode : 'owner'
	
};

var Controller = {

	listeners : [],

	globalEvents : Config.controller.globalEvents,

	behavior : {},

	behaviors : Config.controller.behaviors,

	behave : function(behaviorKey, data){
		if(this.behaviors[behaviorKey]){
			this.behavior = this.behaviors[behaviorKey];
			this.behavior.data = data;
			
			if(this.behavior.init && typeof this.behavior.init === 'function'){
				this.behavior.init(data);
			}
		}
	},
	
	getGlobalEvent : function(event){
		
		var eventName = event.name;
		var hasUsesArray = this.behavior.uses && Model.typeOf(this.behavior.uses) === "Array";
		if(hasUsesArray){
			for(var i in this.behavior.uses){
				var globalEventName = this.behavior.uses[i];
				if(this.globalEvents[globalEventName] && typeof this.globalEvents[eventName] == 'function'){
					return this.globalEvents[globalEventName];
				}
			}
		}
		
	},

	fire : function(event){

		var eventName = event.name;
		
		var globalEvent = this.getGlobalEvent(event);
		
		if(this.behavior[eventName] && typeof this.behavior[eventName] === 'function'){
			this.behavior[eventName](event);
		} else if (globalEvent){
			this.globalEvents[eventName](event);
		}

	},

	registerListener : function(listener){
		this.listeners.push(listener);
	}
};






//*****************************
//*	CLASSES
//*****************************


function Event(name, data, caller){

	this.name = name;
	this.data = data;
	this.caller = caller;

}


function Map(model, config){
	
	this.model = model;
	
	this.mapLayers = [];
	
	this.regionPaths = [];
	
	this.kineticStage = new Kinetic.Stage({
		container: config.containerId,
		width: config.width,
		height: config.height
	});
	
	this.addLayer = function(layerName, layer){
		this.kineticStage.add(layer.kineticLayer);
		this.mapLayers[layerName] = layer;
		layer.map = this;
	};
	
	this.receive = function(event){
		var eventName = event.name;
		var data = event.data;
		
		var region = data.model;
		var regionPath = this.getRegionPath(region);
		if(regionPath){
			if(eventName === "mouseoverRegionPath"){
				regionPath.mouseOver();
			} else if(eventName === "mouseoutRegionPath"){
				regionPath.mouseOut();
			} else if(eventName === "clickRegionPath"){
				regionPath.click();
			}
		}
		this.refresh();
	};
	
	this.refresh = function(){
		for(var mapLayerName in this.mapLayers){
			var mapLayer = this.mapLayers[mapLayerName];
			mapLayer.refresh();
		}
	};
	
	this.addRegionPath = function(regionPath){
		this.regionPaths["id=" + regionPath.model.id] = regionPath;
		regionPath.layer = this;
		this.mapLayers["regionsLayer"].addKineticShape(regionPath.path);
		this.mapLayers["regionsNameLayer"].addKineticShape(regionPath.nameLabel);
		this.mapLayers["troopLabelLayer"].addKineticShape(regionPath.troopLabel);
	};
	
	this.getRegionPath = function(region){
		var search = "id=" + region.id;
		return this.regionPaths[search];
	},
	
	this.addLayer("regionsLayer", new MapLayer());
	this.addLayer("regionsNameLayer", new MapLayer());
	this.addLayer("animationLayer", new MapLayer());
	this.addLayer("troopLabelLayer", new MapLayer());
	
	for(var key in this.model.regions){
		var regionPath = new RegionPath(this.model.regions[key]);
		this.addRegionPath(regionPath);
	}
	this.mapLayers["regionsLayer"].refresh();
	this.mapLayers["regionsNameLayer"].refresh();
	this.mapLayers["animationLayer"].refresh();
	this.mapLayers["troopLabelLayer"].refresh();
	
	this.attackFrom = function(region){
		this.pointerConfig = {
			start : region,
			fillLinearGradientColorStops: [0, '#ff0', 0.05, '#ff0', 0.4, '#f00', 1, '#f00']
		};
		return this;
	};
	
	this.troopShiftFrom = function(region){
		this.pointerConfig = {
			start : region,
			fillLinearGradientColorStops: [0, 'rgba(0,0,0,0)', 0.05, 'rgba(0,0,0,0)', 0.4, '#222', 1, '#222']
		};
		return this;
	};
	
	this.to = function(region){
		if(this.pointerConfig){
			this.pointerConfig.end = region;
			this.pointer = new Pointer(
				{x:this.pointerConfig.start.centerx,y:this.pointerConfig.start.centery},
				{x:this.pointerConfig.end.centerx,y:this.pointerConfig.end.centery},
				{fillLinearGradientColorStops: this.pointerConfig.fillLinearGradientColorStops}
			);
			this.mapLayers["animationLayer"].addKineticShape(this.pointer.kinetic);
			this.pointer.animate();
			delete this.pointerConfig;
		}
		
	};
	
	this.clearPointers = function(){
		this.mapLayers["animationLayer"].clear();
		delete this.pointer;
	};
	
	//this.attackFrom(Model.regions[2]).to(Model.regions[3]);
	
}

function MapLayer(){
	
	this.map = null;
	
	this.regionPaths = [];
	
	this.kineticLayer = new Kinetic.Layer({});
	
	this.addKineticShape = function(shape){
		this.kineticLayer.add(shape);
	};
	
	this.refresh = function(){
		this.kineticLayer.draw();
	};
	
	this.clear = function(){
		this.kineticLayer.removeChildren();
		this.kineticLayer.draw();
	};
	
}

function RegionPath(model){
	
	var self = this;
	
	this.model = model;
	this.state = 0;
	
	this.eventListeners = [];
	
	var colorKey = this.model[View.viewMode].matchcolor;
	var theme = Config.view.themes[colorKey];
	
	this.path = new Kinetic.Path({
		data: model.svgdata,
		fill: theme.fill[self.state],
		stroke: theme.stroke[self.state],
		strokeWidth: theme.strokeWidth[self.state]
	});
	this.path.on('mouseover', function() {
		self.fire("mouseoverRegionPath");
	});
	this.path.on('mouseout', function() {
		self.fire("mouseoutRegionPath");
	});
	this.path.on('click', function() {
		self.fire("clickRegionPath");
	});

	var nameLabelConfig = Config.view.map.regions.nameLabels;
	nameLabelConfig.text = model.name;
	nameLabelConfig.offsetX = model.name.length + nameLabelConfig.offsetX;
	nameLabelConfig.rotation = model.angle;
	nameLabelConfig.data = model.pathdata;
	nameLabelConfig.x = model.centerx;
	nameLabelConfig.y = model.centery;
	nameLabelConfig.fill = theme.text[self.state];
	
	this.nameLabel = new Kinetic.Text(nameLabelConfig);
	this.nameLabel.on('mouseover', function() {
		self.fire("mouseoverRegionPath");
	});
	this.nameLabel.on('mouseout', function() {
		self.fire("mouseoutRegionPath");
	});
	this.nameLabel.on('click', function() {
		self.fire("clickRegionPath");
	});
	
	var troopLabelConfig = Config.view.map.regions.troopLabels;
	this.troopLabel = new Kinetic.Label({
		x: model.centerx - troopLabelConfig.padding - troopLabelConfig.fontSize/2,
		y: model.centery - troopLabelConfig.padding - troopLabelConfig.fontSize/2
	});
	troopLabelConfig.text = model.troops;
	troopLabelConfig.fill = theme.troops.color[self.state];
	this.troopLabel.text = new Kinetic.Text(troopLabelConfig);
	this.troopLabel.tag = new Kinetic.Rect({
		width: troopLabelConfig.width,
		height: troopLabelConfig.width,
		cornerRadius: troopLabelConfig.cornerRadius,

		fill: theme.troops.fill[self.state],
		stroke: theme.troops.stroke[self.state],
		strokeWidth: theme.troops.strokeWidth[self.state]
	});
	this.troopLabel.add(this.troopLabel.tag);
	this.troopLabel.add(this.troopLabel.text);
	this.troopLabel.on('mouseover', function() {
		self.fire("mouseoverRegionPath");
	});
	this.troopLabel.on('mouseout', function() {
		self.fire("mouseoutRegionPath");
	});
	this.troopLabel.on('click', function() {
		self.fire("clickRegionPath");
	});
	
	this.mouseOut = function(){
		this.state = 0;
		this.update();
	};
	this.mouseOver = function(){
		this.state = 1;
		this.update();
	};
	this.click = function(){
		this.state = 2;
		this.update();
	};
	
	this.update = function(){
		var colorKey = this.model[View.viewMode].matchcolor;
		var theme = Config.view.themes[colorKey];
		
		var fill = theme.fill[this.state];
		var stroke = theme.stroke[this.state];
		var strokeWidth = theme.strokeWidth[this.state];
		var textFill = theme.text[self.state];
		var troopsColor = theme.troops.color[self.state];
		var troopsFill = theme.troops.fill[self.state];
		var troopsStroke = theme.troops.stroke[self.state];
		var troopsStrokeWidth = theme.troops.strokeWidth[self.state];
		
		this.path.setFill(fill);
		this.path.setStroke(stroke);
		this.path.setStrokeWidth(strokeWidth);
		
		this.nameLabel.setFill(textFill);
		
		this.troopLabel.text.setText(model.troops);
		this.troopLabel.text.setFill(troopsColor);
		this.troopLabel.tag.setFill(troopsFill);
		this.troopLabel.tag.setStroke(troopsStroke);
		this.troopLabel.tag.setStrokeWidth(troopsStrokeWidth);
		
		if(this.state > 0){
			this.path.moveToTop();
		} else {
			this.path.moveToBottom();
		}
	};
	
	this.addEventListener = function(eventListener){
		if(eventListener.fire){
			this.eventListeners.push(eventListener);
		}
	};
	this.addEventListener(Controller);
	
	this.fire = function(eventName){
		var data = {
			model: self.model,
			view: self
		};
		var event = new Event(eventName, data, View.Map);
		for(var index in this.eventListeners){
			var eventListener = this.eventListeners[index];
			eventListener.fire(event);
		}
	};
	
}


function Pointer(start, end, customConfig){
	
	var self = this;
	
	var vec = Math2d.fromTo(start, end);
	vec = Math2d.normalize(vec);
	vec = Math2d.orthogonalize(vec);
	
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
			   
	this.kinetic = new Kinetic.Line(config);

	this.animate = function(){
		var animation = new Kinetic.Animation(function(frame){
			var duration = config.speed * 1000;
			self.kinetic.setPoints([
				start.x - 10 * vec[1].x,
				start.y - 10 * vec[1].y,
				start.x + (end.x - start.x) * frame.time/duration,
				start.y + (end.y - start.y) * frame.time/duration,
				start.x + 10 * vec[1].x,
				start.y + 10 * vec[1].y
			]);
			self.kinetic.getLayer().drawScene();
			if(frame.time > duration){
				this.stop();
			}
		});
		animation.start();
	};
	
}



var Math2d = {

	distance : function(point1, point2){
		return Math.sqrt(
			Math.pow(point1.x - point2.x, 2)
			+ Math.pow(point1.y - point2.y, 2)
		);
	},

	fromTo : function(point1, point2){
		return [
			{x: 0, y: 0},
			{x: point1.x - point2.x, y: point1.y - point2.y}
		];
	},

	orthogonalize : function(vector){
		return [
			{x: 0, y: 0},
			{
				x: vector[1].y,
				y: -vector[1].x
			}
		];
	},

	normalize : function(vector){
		var length = this.distance(vector[0], vector[1]);
		return [
			{
				x: vector[0].x/length,
				y: vector[0].y/length
			},
			{
				x: vector[1].x/length,
				y: vector[1].y/length
			}
		];
	},

	middleOf : function(){
		var count = 0;
		var sumX = 0;
		var sumY = 0;
		for(var i = 0; i < arguments.length; i++){
			count++;
			var arg = arguments[i];
			if(arg.x && arg.y){
				sumX += arg.x;
				sumY += arg.y;
			}
		}
		var x = -1;
		var y = -1;
		var result = {
			x : x,
			y : y
		};
		if(count > 0){
			result = {
				x : Math.round(sumX/count),
				y : Math.round(sumY/count)
			};
		}
		return result;
	}
};