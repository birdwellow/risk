var Controller = {

	state : {},
	
	stateName : null,

	globalEvents : Config.controller.globalEvents,

	init : Config.controller.initServerEvent,

	serverEvents : Config.controller.serverEvents,

	possibleStates : Config.controller.states,
	
	switchToState : function(newState){
		var out = this.stateName + " -> ";
		if(this.state.onExit){
			this.state.onExit(this.context);
		}
		if(this.possibleStates[newState]){
			this.state = this.possibleStates[newState];
			this.stateName = newState;
		}
		if(this.state.onEnter){
			this.state.onEnter(this.context);
		}
		out += this.stateName;
		console.log(out);
	},

	listen : function(event){
		var resultingState = this.executeEventClosure(event);
		if(resultingState){
			this.switchToState(resultingState);
		}
		View.update();

	},
	
	executeEventClosure : function(event){
		
		if(event.name === "init.data"){
			return this.init(event.data);
		}
		
		var eventClosure;
		
		var globalEventClosure = this.globalEvents[event.name];
		var serverEventClosure = this.serverEvents[event.name];
		var stateClosure = this.state[event.name];
		
		if (globalEventClosure){
			return globalEventClosure(this.context, event);
		} else if (serverEventClosure){
			this.mapEventDataToContext(event);
			return serverEventClosure(this.context, event);
		} else if(stateClosure && this.context.isClientActive()){
			return stateClosure(this.context, event);
		}
	},
	
	mapEventDataToContext : function(event){
		if(!event.data){
			return;
		}
		for(var key in event.data){
			if(key === "name" || key === "data" || key === "__proto__"){
				continue;
			}
			this.context[key] = event.data[key];
		}
	},
	
	getContext : function(){
		
		return this.context;
		
	},
	
	context : {
	
		mouseOverRegion : null,
		
		moveType : null,
		moveStart : null,
		moveEnd : null,
		
		shiftTroops : null,
		
		getMove : function(){
			if(this.moveType !== null && this.moveStart !== null && this.moveEnd !== null){
				return {
					type: this.moveType,
					start: this.moveStart,
					end: this.moveEnd
				};
			}
			return null;
		},
		
		isClientActive : function(){
			return (Model.activePlayer === Model.me);
		}

	}
	
};


function Event(name, data, caller){

	this.name = name;
	this.data = data;
	this.caller = caller;

}