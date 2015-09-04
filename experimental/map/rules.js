var behaviors = {

	selectStart : {

		mouseoverRegionPath : function(region){
			if(!region.path.isClicked()){
				region.path.mouseover();
			}
		},

		mouseoutRegionPath : function(region){
			if(!region.path.isClicked()){
				region.path.mouseout();
			}
		},

		clickRegionPath : function(startRegion){
			startRegion.path.click();
			Game.Controller.behave("selectTarget", startRegion);
		}

	},

	selectTarget : {

		startRegion : null,

		init : function(startRegion){
			this.startRegion = startRegion;
		},

		mouseoverRegionPath : function(region){
			if(!region.path.isClicked()){
				region.path.mouseover();
			}
		},

		mouseoutRegionPath : function(region){
			if(!region.path.isClicked()){
				region.path.mouseout();
			}
		},

		clickRegionPath : function(targetRegion){
			if(targetRegion.path.isClicked()){
				targetRegion.path.mouseover();
				Game.Controller.behave("selectStart");
			} else {
				targetRegion.path.click();
				Game.View.pointer(this.startRegion.path, targetRegion.path);
				Game.Controller.behave("attack", this.startRegion, targetRegion);
			}
		}

	},

	attack : {

		startRegion : null,

		targetRegion : null,

		init : function(startRegion, targetRegion){
			this.startRegion = startRegion;
			this.targetRegion = targetRegion;
		},

		clickRegionPath : function(region){
			if(region.path.isClicked() && region.path == this.targetRegion.path){
				region.path.mouseover();
				Game.View.pointerOff();
				Game.Controller.behave("selectTarget", this.startRegion);
			}
		},

		confirmAction : function(){
			var startRegion = this.startRegion;
			var targetRegion = this.targetRegion;
			Dicer.throw(3,2, function(win){
				if(win && targetRegion.troops > 0){
					targetRegion.removeUnit();
					if(targetRegion.troops == 0){
						targetRegion.setOwner(startRegion.owner);
						targetRegion.addUnit();
						startRegion.removeUnit();
						Game.View.troopShift(startRegion.path, targetRegion.path);
						Game.Controller.behave("shiftTroops", startRegion, targetRegion);
					}
				} else if(!win && startRegion.troops > 0){
					startRegion.removeUnit();
					if(startRegion.troops == 0){
						Game.View.pointerOff();
						targetRegion.path.mouseout();
						startRegion.path.mouseout();
						Game.Controller.behave("selectStart");
					}
				}
			}, startRegion, targetRegion);
		}

	},

	shiftTroops : {

		startRegion : null,

		targetRegion : null,

		init : function(startRegion, targetRegion){
			this.startRegion = startRegion;
			this.targetRegion = targetRegion;
		},

		shiftUnit : function(){
			if(this.startRegion.troops > 1){
				this.startRegion.removeUnit();
				this.targetRegion.addUnit();
			}
		},

		retractUnit : function(){
			if(this.targetRegion.troops > 1){
				this.startRegion.addUnit();
				this.targetRegion.removeUnit();
			}
		},

		confirmAction : function(){
			Game.View.troopShiftOff();
			Game.View.pointerOff();
			this.targetRegion.path.mouseout();
			this.startRegion.path.mouseout();
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