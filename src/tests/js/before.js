var joinId = 'abcd1234';

/*
 * Custom mock for WebSocket
 */
function WebSocket () {
	
	var eventListners = {};
	WebSocket.messages = [];
	
	this.addEventListener = function (eventName, handler){
		if(!eventListners[eventName]){
			eventListners[eventName] = [];
		}
		eventListners[eventName].push(handler);
	};
	
	this.send = function (messageString) {
		console.log('Sent with mock WebSocket: ' + messageString);
		WebSocket.messages.push(messageString);
	};
	
	WebSocket._receive = function (eventName, eventObject) {
		if(eventListners[eventName]){
			var handlers = eventListners[eventName];
			for (var i in handlers) {
				handlers[i](eventObject);
			}
		}
	};
	
	WebSocket._lastMessage = function () {
		return WebSocket.messages[WebSocket.messages.length - 1];
	};
}