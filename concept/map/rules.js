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
			this.targetRegionPath.mouseout();
			this.startRegionPath.mouseout();
			this.targetRegionPath.update;
			Game.View.pointerOff();
			Game.Controller.behave("selectStart");
		}

	},

};

var globalBehaviors = {

	changeScheme : function(mode){
		if(mode == "owner" || mode == "continent"){
			Game.View.scheme(mode);
		}
	}

};