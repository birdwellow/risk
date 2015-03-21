var behaviors = {

	selectStart : {

		mouseoverRegionPath : function(regionPath){
			if(!regionPath.isClicked()){
				regionPath.mouseover();
			}
		},

		mouseoutRegionPath : function(regionPath){
			if(!regionPath.isClicked()){
				regionPath.mouseout();
			}
		},

		clickRegionPath : function(startRegionPath){
			startRegionPath.click();
			Game.Controller.behave("selectTarget", startRegionPath);
		}

	},

	selectTarget : {

		startRegionPath : null,

		init : function(startRegionPath){
			this.startRegionPath = startRegionPath;
			//Game.View.troopShift({model:{center:{x:100, y:100}}}, {model:{center:{x:200, y:200}}});
		},

		mouseoverRegionPath : function(regionPath){
			if(!regionPath.isClicked()){
				regionPath.mouseover();
			}
		},

		mouseoutRegionPath : function(regionPath){
			if(!regionPath.isClicked()){
				regionPath.mouseout();
			}
		},

		clickRegionPath : function(targetRegionPath){
			if(targetRegionPath.isClicked()){
				targetRegionPath.mouseover();
				Game.Controller.behave("selectStart");
			} else {
				targetRegionPath.click();
				Game.View.pointer(this.startRegionPath, targetRegionPath);
				Game.Controller.behave("performAction", this.startRegionPath, targetRegionPath);
			}
		}

	},

	performAction : {

		startRegionPath : null,

		targetRegionPath : null,

		init : function(startRegionPath, targetRegionPath){
			this.startRegionPath = startRegionPath;
			this.targetRegionPath = targetRegionPath;
		},

		clickRegionPath : function(regionPath){
			if(regionPath.isClicked() && regionPath == this.targetRegionPath){
				regionPath.mouseover();
				Game.View.pointerOff();
				Game.Controller.behave("selectTarget", this.startRegionPath);
			}
		},

		confirmAction : function(){
			this.targetRegionPath.model.owner = this.startRegionPath.model.owner;
			this.targetRegionPath.click();
			this.startRegionPath.click();
			Game.View.troopShift(this.startRegionPath, this.targetRegionPath);
			/*
			Game.View.pointerOff();
			Game.Controller.behave("shiftTroops", this.startRegionPath, this.targetRegionPath);*/
		}

	},

	shiftTroops : {

		startRegionPath : null,

		targetRegionPath : null,

		init : function(startRegionPath, targetRegionPath){
			this.startRegionPath = startRegionPath;
			this.targetRegionPath = targetRegionPath;
		},

		shiftUnit : function(){

		},

		retractUnit : function(){

		},

		confirmAction : function(){
			Game.View.troopShiftOff();
			this.targetRegionPath.mouseout();
			this.startRegionPath.mouseout();
			Game.Controller.behave("selectStart");
		},

	}

};

var globalBehaviors = {

	changeScheme : function(mode){
		if(mode == "owner" || mode == "continent"){
			Game.View.scheme(mode);
		}
	}

};