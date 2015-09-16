/*
 function Chat(socket){
 var self = this;
 this._socket = socket;
 this._input = $("#chatinput");
 this._output = $("#chatcontent");
 this._input.keypress(function(e){
 var message = self._input.val().trim();
 if(e.which == 13 && message != ""){
 self.send(message);
 self._input.val("");
 }
 });
 
 this.send = function(msg){
 self._socket.send("chat.message", msg);
 self.receive(msg, user);
 };
 
 this.receive = function(msg, user){
 var chatContentHtml = self._output.html();
 self._output.html(chatContentHtml + "<div style='color: " + user.color + "'>" + user.name + ": " + msg + "</div>");
 self._output.animate({ scrollTop: self._output[0].scrollHeight }, "slow");
 };
 };
 */

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
		if (typeof self._events[message.type] == "function") {
			self._events[message.type](message.data);
		}
	});

	this.send = function (type, message) {
		var msg = JSON.stringify({
			"type": type,
			"data": message
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
});
socket.on("chat.message", function (message) {
	//chat.receive(message.data, message.user);
});


Model = {
	
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
		/* else if(typeString == "[object Object]"){
			return searchTarget;
		}*/

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

Config = {
	
	view : {
		map : {
			width : 800,
			height : 500,
			containerId : "game-map"
		}
	}
	
};

View = {
	
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
};




/*****************************
 *	CLASSES
 *****************************/


function Event(name, data, caller){

	this.name = name;
	this.data = data;
	this.caller = caller;

}


function Map(model, config){
	
	this.model = model;
	
	this.mapLayers = [];
	
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
	
	this.addLayer("regionsLayer", new MapLayer());
	
	for(var key in this.model.regions){
		var regionPath = new RegionPath(this.model.regions[key]);
		this.mapLayers["regionsLayer"].addRegionPath(regionPath);
	}
	this.mapLayers["regionsLayer"].refresh();
	
}

function MapLayer(){
	
	this.map = null;
	
	this.regionPaths = [];
	
	this.kineticLayer = new Kinetic.Layer({});
	
	this.addRegionPath = function(regionPath){
		this.kineticLayer.add(regionPath.path);
		this.regionPaths["id=" + regionPath.model.id] = regionPath;
		regionPath.layer = this;
	};
	
	this.refresh = function(){
		this.kineticLayer.draw();
	};
	
}

function RegionPath(model){
	
	var self = this;
	
	this.model = model;
	this.model.VIEWSTATE = 0;
	
	this.eventListeners = [];
	
	this.path = new Kinetic.Path({
		data: model.svgdata,
		fill: View.themes[self.model.owner.matchcolor].fill[self.model.VIEWSTATE],
		stroke: View.themes[self.model.owner.matchcolor].stroke[self.model.VIEWSTATE],
		strokeWidth: View.themes[self.model.owner.matchcolor].strokeWidth[self.model.VIEWSTATE]
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
	
	this.update = function(){
		var fill = View.themes[this.model.owner.matchcolor].fill[this.model.VIEWSTATE];
		var stroke = View.themes[this.model.owner.matchcolor].stroke[this.model.VIEWSTATE];
		var strokeWidth = View.themes[this.model.owner.matchcolor].strokeWidth[this.model.VIEWSTATE];
		
		this.path.setFill(fill);
		this.path.setStroke(stroke);
		this.path.setStrokeWidth(strokeWidth);
	};
	
	this.addEventListener = function(eventListener){
		if(eventListener.fire){
			this.eventListeners.push(eventListener);
		}
	};
	
	this.fire = function(eventName){
		var data = {
			model: self.model,
			view: self
		};
		var event = new Event(eventName, data, self);
		for(var index in this.eventListeners){
			var eventListener = this.eventListeners[index];
			eventListener.fire(event);
		}
	};
	
}