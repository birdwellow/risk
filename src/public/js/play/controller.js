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
	
	getGlobalStateEvent : function(event){
		
		var eventName = event.name;
		var hasUsesArray = this.state.uses && Utils.Type.isArray(this.state.uses);
		if(hasUsesArray){
			for(var i in this.state.uses){
				var globalEventName = this.state.uses[i];
				if(this.globalStateEvents[globalEventName] && typeof this.globalStateEvents[eventName] == 'function'){
					return this.globalStateEvents[globalEventName];
				}
			}
		}
		
	},

	listen : function(event){
		
		var eventClosure = this.getEventClosure(event);
		if(typeof eventClosure === 'function'){
			var resultingState = eventClosure(event, this.context);
		}
		View.update();
		if(resultingState){
			this.switchToState(resultingState);
		}

	},
	
	getEventClosure : function(event){
		
		var eventName = event.name;
		
		var globalStateEvent = this.getGlobalStateEvent(event);
		
		if(this.state[eventName] && typeof this.state[eventName] === 'function'){
			return this.state[eventName];
		} else if (globalStateEvent){
			return globalStateEvent;
		} else if (this.globalStateEvents[eventName]){
			return this.globalStateEvents[eventName];
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