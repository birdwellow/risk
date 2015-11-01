function DataMapper() {
	
	function isIterable(object){
		return Utils.Type.isArray(object) || Utils.Type.isObject(object);
	}
	
	function identifier(object){
		var fieldName = getFieldNameOf(object);
		return "[" + fieldName + ":id=" + object.id + "]";
	}
	
	function getFieldNameOf(object){
		for(var fieldName in Model){
			var field = Model[fieldName];
			if(isIterable(field)){
				for(var objKey in field){
					if(field[objKey] === object){
						return fieldName;
					}
				}
			}
		}
	}
	
	function getModelForIdentifier(string){
		
		var identifierRegex = /\[(.*?):(.*?)=(.*?)\]/g;
		var regexMatch = identifierRegex.exec(string);
		if(!regexMatch){
			return string;
		}
		var fieldName = regexMatch[1];
		var objectProperty = regexMatch[2];
		var objectValue = regexMatch[3];
		var field = Model[fieldName];
		if(!isIterable(field)){
			return;
		}
		for(var key in field){
			var object = field[key];
			if(object[objectProperty] && object[objectProperty] == objectValue){
				return object;
			}
		}
		return string;
	}

	return {
		
		decode : function(data){
			
			if(!data){
				return;
			}
			
			for(var key in data){
				var obj = data[key];
				if(Utils.Type.isString(obj)){
					data[key] = getModelForIdentifier(obj);
				} else if(isIterable(obj)){
					this.decode(obj);
				}
			}
			
		},
	
		encode : function(data){
			
			if(!data){
				return;
			}
			
			var encodedData = {};
			
			for(var key in data){
				var obj = data[key];
				if(isIterable(obj)){
					if(obj.id){
						encodedData[key] = identifier(obj);
					} else {
						encodedData[key] = this.encode(obj);
					}
				} else if (!Utils.Type.isFunction(obj)) {
					encodedData[key] = obj;
				}
			}
			
			return encodedData;

		}
	};
	
};


function CommunicationProxy(url) {
	
	var dataMapper = new DataMapper();
	var instance;

	var webSocket = new WebSocket(url);
	var events = new Array();
	
	webSocket.addEventListener("open", function (e) {
		instance.send("get.all");
	});

	webSocket.addEventListener("close", function (e) {
	});

	webSocket.addEventListener("error", function (e) {
	});

	webSocket.addEventListener("message", function (e) {
		var message = JSON.parse(e.data);
		if (events[message.type] && typeof events[message.type].callback === "function") {
			if(!events[message.type].leaveEncoded){
				dataMapper.decode(message.data);
			}
			events[message.type].callback(message.data);
		} else {
			dataMapper.decode(message.data);
			Controller.listen(new Event(message.type, message.data, this));
		}
	});
	
	instance = {

		send : function (type, data) {
			
			var dataToEncode = ( data ? data : Controller.getContext() );
			var encodedData = dataMapper.encode(dataToEncode);
					
			var msg = JSON.stringify({
				"type": type,
				"data": encodedData
			});
			
			webSocket.send(msg);
		},

		on : function (eventName, callback, leaveEncoded) {
			events[eventName] = {
				callback: callback,
				leaveEncoded: leaveEncoded ? true : false
			};
		}
		
	};
	return instance;

}

var proxy = new CommunicationProxy("ws://dev.app.risk:7778/?joinid=" + joinId);

proxy.on("get.all", function (data) {
	Model.digest(data);
	
	View = new ViewInstance(Controller);
	
	var map = new Map(Model, Config.view.map, Controller.getContext());
	View.addComponent(map).as("Map");
	
	var sideBar = new SideBar(Model, Config.view.map, Controller.getContext());
	View.addComponent(sideBar).as("SideBar");
	
	Controller.switchToState("selecting.attack.start");
}, true);

proxy.on("chat.message", function (message) {
	//chat.receive(message.data, message.user);
});