
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
        self.receive(msg, username);
    };
    
    this.receive = function(msg, username){
        var chatContentHtml = self._output.html();
        self._output.html(chatContentHtml + "<div class='message'>" + username + ": " + msg + "</div>");
        self._output.animate({ scrollTop: self._output[0].scrollHeight }, "slow");
    };
};

function GameSocket(url){
    
    var self = this;
    
    this._webSocket = new WebSocket(url);
    this._events = new Array();
    
    this._webSocket.addEventListener("open", function (e) {
    });

    this._webSocket.addEventListener("close", function (e) {
        console.log("Closing");
        console.log(arguments);
    });

    this._webSocket.addEventListener("error", function (e) {
    });
    
    this._webSocket.addEventListener("message", function (e) {
        var message = JSON.parse(e.data);
        if(typeof self._events[message.type] == "function"){
            self._events[message.type](message);
        }
    });
    
    this.send = function(type, message){
        var msg = JSON.stringify({
            "type" : type,
            "data" : message
        });
        self._webSocket.send(msg);
    };
    
    this.on = function(eventName, callback){
        self._events[eventName] = callback;
    };

}



var socket = new GameSocket("ws://dev.app.risk:7778/?joinid=" + joinId);
socket.on("chat.message", function(message){
    chat.receive(message.data, message.username);
});

var chat = new Chat(socket);