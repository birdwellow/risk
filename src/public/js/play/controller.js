var Controller = {

	state : {},
	
	stateData : {},

	globalStateEvents : Config.controller.globalEvents,

	possibleStates : Config.controller.states,
	
	switchToState : function(newState){
		if(this.possibleStates[newState]){
			this.state = this.possibleStates[newState];
		}
	},

	listen : function(event){
		
		var eventClosure = this.getEventClosure(event);
		if(Utils.Type.isFunction(eventClosure)){
			var resultingState = eventClosure(this.context, event);
		}
		View.update();
		if(resultingState){
			this.switchToState(resultingState);
		}

	},
	
	getEventClosure : function(event){
		
		var stateClosure = this.state[event.name];
		var globalClosure = this.globalStateEvents[event.name];
		
		if(stateClosure){
			return stateClosure;
		} else if (globalClosure){
			this.mapEventDataToContext(event);
			return globalClosure;
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
		
		getMove : function(){
			if(this.moveType !== null && this.moveStart !== null && this.moveEnd !== null){
				return {
					type: this.moveType,
					start: this.moveStart,
					end: this.moveEnd
				};
			}
			return null;
		}

	}
	
};


function Event(name, data, caller){

	this.name = name;
	this.data = data;
	this.caller = caller;

}