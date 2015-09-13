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
		
		this.setupRelations();
	},

	setupRelations : function(){
		this.regions = [];
		for(var i in this.continents){
			var continent = this.continents[i];
			for(var j in continent.regions){
				var region = continent.regions[j];
				this.regions.push(region);
			}
		}
		this.replaceRelations(this.regions, "owner", "id", this.joinedUsers, "id");
		console.log(this.regions);
	},
	
	
	/*
	 * Iterates through @subjects and replaces the values/relations of @subjectFieldName
	 * with the object in @objects, whose value of @objectFieldName matches the value of
	 * subjectField's criterumField
	 * Example:
	 *	The Model.regions[].owner objects must be replaced with the corresponding
	 *	instance of Model.joinedUsers. This replacement is done by
	 *		Model.regions[].owner.id === Model.joinedUsers.id
	 */

	replaceRelations : function (subjects, subjectFieldName, criteriumField, objects, objectField){
		for(var index in subjects){
			var subject = subjects[index];
			console.log(subject);
			var criterium = subject[subjectFieldName][criteriumField];
			subject[subjectFieldName] = this.findObjectWith(objects, objectField, criterium);
		}
	},
	
	findObjectWith : function (objects, fieldName, value){
		for(var index in objects){
			var object = objects[index];
			if(object[fieldName] === value){
				return object;
			}
		}
		return null;
	}
};

