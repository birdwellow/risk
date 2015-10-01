
function CommunicationProxy(url) {

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

var proxy = new CommunicationProxy("ws://dev.app.risk:7778/?joinid=" + joinId);

proxy.on("open", function () {
	proxy.send("get.all");
});
proxy.on("get.all", function (data) {
	Model.digest(data);
	
	View = new ViewInstance(Controller);
	
	var map = new Map(Model, Config.view.map, Controller.getContext());
	View.addComponent(map).as("Map");
	
	/*
	View.getComponent("Map").attackFrom(Model.regions[2]).to(Model.regions[3]);
	View.getComponent("Map").mouseOverRegion(Model.regions[1]);
	View.getComponent("Map").clickRegion(Model.regions[3]);
	*/
	
	
	Controller.switchToState("active:selecting.attack.start");
});
proxy.on("select.region", function (data) {
	var region = Model.get(data);
	var event = new Event("clickRegionPath", {model: region}, this);
	Controller.fire(event);
});
proxy.on("chat.message", function (message) {
	//chat.receive(message.data, message.user);
});
