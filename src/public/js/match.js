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
	Model.init(data);
});
socket.on("chat.message", function (message) {
	//chat.receive(message.data, message.user);
});




Model = {
	
	init : function(data) {
		for(var index in data){
			var property = data[index];
			this[index] = property;
		}
		
		this.setupRelationsRecursively();
		
		console.log(this);
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
						//console.log("setupRelationsRecursively for: ");
						//console.log(arrayElement);
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
				console.log(candidate);
				var candidatePropertyValue = candidate[propertyName];
				if(candidatePropertyValue && candidatePropertyValue == propertyValue){
					return candidate;
				}
			}
			if(propertyValue){
				console.log("nothing found for " + descriptor);
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

