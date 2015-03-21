

var Math2d = {

	distance : function(point1, point2){
		return Math.sqrt(
			Math.pow(point1.x - point2.x, 2)
			+ Math.pow(point1.y - point2.y, 2)
		);
	},

	fromTo : function(point1, point2){
		return [
			{x: 0, y: 0},
			{x: point1.x - point2.x, y: point1.y - point2.y}
		];
	},

	orthogonalize : function(vector){
		return [
			{x: 0, y: 0},
			{
				x: vector[1].y,
				y: -vector[1].x
			}
		];
	},

	normalize : function(vector){
		var length = this.distance(vector[0], vector[1]);
		return [
			{
				x: vector[0].x/length,
				y: vector[0].y/length,
			},
			{
				x: vector[1].x/length,
				y: vector[1].y/length,
			}
		];
	},

	middleOf : function(){
		var count = 0;
		var sumX = 0;
		var sumY = 0;
		for(var i = 0; i < arguments.length; i++){
			count++;
			var arg = arguments[i];
			if(arg.x && arg.y){
				sumX += arg.x;
				sumY += arg.y;
			}
		}
		var x = -1;
		var y = -1;
		var result = {
			x : x,
			y : y
		};
		if(count > 0){
			result = {
				x : Math.round(sumX/count),
				y : Math.round(sumY/count)
			};
		}
		return result;
	}
};


var Game = {

	Model : {

		init : function(){

		},

		regions : regions,

		players : players,

		continents : continents,

	},

	View : {

		mapStage : new Kinetic.Stage({
			container: Config.view.map.containerName,
			width: Config.view.map.width,
			height: Config.view.map.height
		}),

		randomStage : new Kinetic.Stage({
			container: Config.view.random.containerName,
			width: Config.view.random.width,
			height: Config.view.random.height
		}),

		mapLayer : new Kinetic.Layer({}),
		labelLayer : new Kinetic.Layer({}),
		highlightLayer : new Kinetic.Layer({}),
		mapTopLayer : new Kinetic.Layer({}),
		labelTopLayer : new Kinetic.Layer({}),
		symbolLayer : new Kinetic.Layer({}),
		controlsLayer : new Kinetic.Layer({}),

		randomLayer : new Kinetic.Layer({}),

		elements : {

			regions : [],

			pointer : null,

		},

		troopShift : function(start, end){
			var vec = Math2d.fromTo(start.model.center, end.model.center);
			vec = Math2d.normalize(vec);

			var middle = Math2d.middleOf(start.model.center, end.model.center);
			var config = Config.view.scheme.troopShift.controls;
			this.elements.incButton = new Kinetic.Label({
				x : middle.x - config.width/2 - vec[1].x * 2 * 15,
				y : middle.y - config.width/2 - vec[1].y * 2 * 15
			});
			this.elements.decButton = new Kinetic.Label({
				x : middle.x - config.width/2 + vec[1].x * 2 * 15,
				y : middle.y - config.width/2 + vec[1].y * 2 * 15
			});
			config.text = "+";
			config.fill = config.color[0];
			config.align = 'center';
			config.offsetY = -5;
			config.stroke = "";
			this.elements.incButton.text = new Kinetic.Text(config);
			config.text = "-";
			config.offsetY = 0;
			this.elements.decButton.text = new Kinetic.Text(config);

			config.fill = config.background[0];
			config.height = config.width;
			config.stroke = config.borderColor[0];
			config.strokeWidth = config.borderWidth[0];
			this.elements.incButton.label = new Kinetic.Rect(config);
			this.elements.decButton.label = new Kinetic.Rect(config);
			this.elements.incButton.add(this.elements.incButton.label);
			this.elements.incButton.add(this.elements.incButton.text);
			this.elements.decButton.add(this.elements.decButton.label);
			this.elements.decButton.add(this.elements.decButton.text);
			this.controlsLayer.add(this.elements.incButton);
			this.controlsLayer.add(this.elements.decButton);
			this.controlsLayer.drawScene();

			this.elements.incButton.state = function(newState){
				this._state = newState;
				var config = Config.view.scheme.troopShift.controls;
				this.text.setFill(config[this._state]);
				this.label.setFill(config.background[this._state]);
				Game.View.controlsLayer.drawScene();
			};
			this.elements.decButton.state = function(newState){
				this._state = newState;
				var config = Config.view.scheme.troopShift.controls;
				this.text.setFill(config[this._state]);
				this.label.setFill(config.background[this._state]);
				Game.View.controlsLayer.drawScene();
			};

			this.elements.incButton.on("mouseover", function(){
				console.log("hover");
				this.state(1);
			});
			this.elements.decButton.on("mouseover", function(){
				this.state(1);
			});
			this.elements.incButton.on("mouseout", function(){
				this.state(0);
			});
			this.elements.decButton.on("mouseout", function(){
				this.state(0);
			});
			this.elements.incButton.on("mousedown", function(){
				this.state(2);
			});
			this.elements.decButton.on("mousedown", function(){
				this.state(2);
			});
			this.elements.incButton.on("mouseup", function(){
				this.state(1);
			});
			this.elements.decButton.on("mouseup", function(){
				this.state(1);
			});
			this.elements.incButton.on("click", function(){
				Game.Controller.fire("shiftUnit");
			});
			this.elements.incButton.on("click", function(){
				Game.Controller.fire("retractUnit");
			});

			this.elements.incButton.eventListeners.mouseover.name = "mouseover";
			this.elements.incButton.eventListeners.mouseover.handler = function(){
				console.log("hover");
			};
			console.log(this.elements.incButton);
		},

		troopShiftOff : function(){
			this.elements.incButton.remove();
			this.elements.incButton = null;
			this.elements.decButton.remove();
			this.elements.decButton = null;
			this.controlsLayer.draw();
		},

		pointer : function(start, end){
			var vec = Math2d.fromTo(start.model.center, end.model.center);
			vec = Math2d.normalize(vec);
			vec = Math2d.orthogonalize(vec);

			this.elements.pointer = new Kinetic.Line({
				fill : Config.view.scheme.pointer.fill,
				stroke : Config.view.scheme.pointer.stroke,
				strokeWidth : Config.view.scheme.pointer.strokeWidth,
				points : [
					start.model.center.x - 10 * vec[1].x,
					start.model.center.y - 10 * vec[1].y,
					end.model.center.x,
					end.model.center.y,
					start.model.center.x + 10 * vec[1].x,
					start.model.center.y + 10 * vec[1].y
				],
				closed: true,

				fillLinearGradientColorStops: Config.view.scheme.pointer.fillLinearGradientColorStops,
				fillLinearGradientStartPoint: {
					x: start.model.center.x,
					y: start.model.center.y
				},
				fillLinearGradientEndPoint: {
					x: end.model.center.x,
					y: end.model.center.y
				},
			});
			this.symbolLayer.add(this.elements.pointer);
			this.symbolLayer.drawScene();
			this.highlightOn();

			var duration = Config.view.scheme.pointer.speed * 1000;
			var animation = new Kinetic.Animation(function(frame){
				if(Game.View.elements.pointer){
					Game.View.elements.pointer.setPoints([
						start.model.center.x - 10 * vec[1].x,
						start.model.center.y - 10 * vec[1].y,
						start.model.center.x + (end.model.center.x - start.model.center.x) * frame.time/duration,
						start.model.center.y + (end.model.center.y - start.model.center.y) * frame.time/duration,
						start.model.center.x + 10 * vec[1].x,
						start.model.center.y + 10 * vec[1].y
					]);
					Game.View.symbolLayer.drawScene();
				} else {
					this.stop();
				}
				if(frame.time > duration){
					this.stop();
				}
			});
			animation.start();
		},

		pointerOff : function(){
			this.elements.pointer.remove();
			this.elements.pointer = null;
			this.symbolLayer.draw();
			this.highlightOff();
		},

		highlightOn : function(){
			Game.View.highlightLayer.add(Game.View.highlightLayer.highlightBackground);
			Game.View.highlightLayer.drawScene();
		},

		highlightOff : function(){
			Game.View.highlightLayer.highlightBackground.remove();
			Game.View.highlightLayer.drawScene();
		},

		scheme : function(scheme){
			for(var key in this.elements.regions){
				var regionView = this.elements.regions[key];
				regionView.scheme(scheme);
			}
			Game.View.mapLayer.draw();
			Game.View.mapTopLayer.draw();
		},

		init : function (){

			for(var i = 0; i < regions.length; i++) {

				var region = regions[i];



				/***********************
				*
				*	Kinetic Shapes
				*
				************************/

				var path = new Kinetic.Path({
					id: region.name,
					data: region.svgData,
					fill: region.owner.colorscheme.fill[0],
					stroke: region.owner.colorscheme.stroke[0],
					strokeWidth: region.owner.colorscheme.strokeWidth[0],
				});
				
				var nameLabelConfig = Config.view.scheme.regionTexts;
				nameLabelConfig.text = region.name;
				nameLabelConfig.offsetX = region.name.length + nameLabelConfig.offsetX;
				nameLabelConfig.rotation = region.angle;
				nameLabelConfig.data = region.path;
				nameLabelConfig.x = region.labelCenter.x;
				nameLabelConfig.y = region.labelCenter.y;
				nameLabelConfig.fill = region.owner.colorscheme.text[0];
				if(region.pathData){
					path.nameLabel = new Kinetic.TextPath(nameLabelConfig);
				} else {
					path.nameLabel = new Kinetic.Text(nameLabelConfig);
				}


				var troopLabelConfig = Config.view.scheme.troopTexts;
				troopLabelConfig.text = region.troops;
				troopLabelConfig.align = 'center';
				path.troopLabel = new Kinetic.Label({
					x: region.center.x - troopLabelConfig.padding - troopLabelConfig.fontSize/2,
					y: region.center.y - troopLabelConfig.padding - troopLabelConfig.fontSize/2
				});
				troopLabelConfig.fill = region.owner.colorscheme.troops.color[0];
				path.troopLabel.text = new Kinetic.Text(troopLabelConfig);
				path.troopLabel.tag = new Kinetic.Rect({
					width: troopLabelConfig.width,
					height: troopLabelConfig.width,
					cornerRadius: troopLabelConfig.cornerRadius,

					fill: region.owner.colorscheme.troops.fill[0],
					stroke: region.owner.colorscheme.troops.stroke[0],
					strokeWidth: region.owner.colorscheme.troops.strokeWidth[0]
				});
				path.troopLabel.add(path.troopLabel.tag);
				path.troopLabel.add(path.troopLabel.text);

				this.elements.regions[region.id] = path;
				path.model = region;




				/***********************
				*
				*	Shape Controls
				*
				************************/

				path.colorschemeName = "owner";
				path._state = 0;

				path.mouseout = function(){
					this._state = 0;
					this.update();
					this.moveTo(Game.View.mapLayer);
					this.nameLabel.moveTo(Game.View.mapLayer);
					this.troopLabel.moveTo(Game.View.labelLayer);
					Game.View.mapTopLayer.drawScene();
					Game.View.mapLayer.drawScene();
					Game.View.labelTopLayer.drawScene();
					Game.View.labelLayer.drawScene();
				};
				path.isMouseout = function(){
					return this._state == 0;
				};

				path.mouseover = function(){
					this._state = 1;
					this.update();
					this.moveTo(Game.View.mapTopLayer);
					this.nameLabel.moveTo(Game.View.mapTopLayer);
					this.troopLabel.moveTo(Game.View.labelTopLayer);
					Game.View.mapTopLayer.drawScene();
					Game.View.labelTopLayer.drawScene();
				};
				path.isMouseover = function(){
					return this._state == 1;
				};

				path.click = function(){
					this._state = 2;
					this.update();
					Game.View.mapTopLayer.drawScene();
					Game.View.labelTopLayer.drawScene();
				};
				path.isClicked = function(){
					return this._state == 2;
				};

				path.scheme = function(scheme){
					if(this.model[scheme] && this.model[scheme].colorscheme){
						this.colorschemeName = scheme;
						this.update();
					}
				};

				path.update = function(){
					if(this.model[this.colorschemeName] && this.model[this.colorschemeName].colorscheme){
						this.colorscheme = this.model[this.colorschemeName].colorscheme;
					}
					if(this.colorscheme.fill[this._state] != undefined){
						this.setFill(this.colorscheme.fill[this._state]);
					}
					if(this.colorscheme.stroke[this._state] != undefined){
						this.setStroke(this.colorscheme.stroke[this._state]);
					}
					if(this.colorscheme.strokeWidth[this._state] != undefined){
						this.setStrokeWidth(this.colorscheme.strokeWidth[this._state]);
					}
					if(this.colorscheme.text[this._state] != undefined){
						this.nameLabel.setFill(this.colorscheme.text[this._state]);
					}
					if(this.colorscheme.troops && this.colorscheme.troops.color[this._state] != undefined){
						this.troopLabel.text.setFill(this.colorscheme.troops.color[this._state]);
					}
					if(this.colorscheme.troops && this.colorscheme.troops.fill[this._state] != undefined){
						this.troopLabel.tag.setFill(this.colorscheme.troops.fill[this._state]);
					}
					if(this.colorscheme.troops && this.colorscheme.troops.stroke[this._state] != undefined){
						this.troopLabel.tag.setStroke(this.colorscheme.troops.stroke[this._state]);
					}
					if(this.colorscheme.troops && this.colorscheme.troops.strokeWidth[this._state] != undefined){
						this.troopLabel.tag.setStrokeWidth(this.colorscheme.troops.strokeWidth[this._state]);
					}
				};

				path.scheme("owner");
				Game.View.mapLayer.add(path);

				path.troopLabel.path = path;
				path.nameLabel.path = path;



				path.on('mouseover', function() {
					Game.Controller.fire("mouseoverRegionPath", this);
				});
				path.nameLabel.on('mouseover', function() {
					Game.Controller.fire("mouseoverRegionPath", this.path);
				}); 
				path.troopLabel.on('mouseover', function() {
					Game.Controller.fire("mouseoverRegionPath", this.path);
				}); 
				path.on('click', function() {
					Game.Controller.fire("clickRegionPath", this);
				});
				path.nameLabel.on('mouseout', function() {
					Game.Controller.fire("mouseoutRegionPath", this.path);
				});
				path.troopLabel.on('mouseout', function() {
					Game.Controller.fire("mouseoutRegionPath", this.path);
				});
				path.on('mouseout', function() {
					Game.Controller.fire("mouseoutRegionPath", this);
				});
				path.nameLabel.on('click', function() {
					Game.Controller.fire("clickRegionPath", this.path);
				});
				path.troopLabel.on('click', function() {
					Game.Controller.fire("clickRegionPath", this.path);
				});
				Game.View.mapLayer.add(path.nameLabel);
				Game.View.labelLayer.add(path.troopLabel);
			}

			var highlightBackground = new Kinetic.Rect({
				fill: '#000',
				width: 5000,
				height: 5000,
				opacity: 0.5
			});
			Game.View.highlightLayer.highlightBackground = highlightBackground;

			this.mapStage.add(Game.View.mapLayer);
			this.mapStage.add(Game.View.labelLayer);
			this.mapStage.add(Game.View.highlightLayer);
			this.mapStage.add(Game.View.mapTopLayer);
			this.mapStage.add(Game.View.labelTopLayer);
			this.mapStage.add(Game.View.symbolLayer);
			this.mapStage.add(Game.View.controlsLayer);
		},

	},

	Controller : {

		init : function(){
			this.behave("selectStart");
		},

		globalEvents : globalBehaviors,

		behavior : {},

		behaviors : behaviors,

		behave : function(behaviorKey){
			if(this.behaviors[behaviorKey]){
				this.behavior = this.behaviors[behaviorKey];
				if(this.behavior.init && typeof this.behavior.init == 'function'){
					this.behavior.init(arguments[1], arguments[2], arguments[3], arguments[4], arguments[5]);
				}
			}
		},

		fire : function(event, data){
			if(this.behavior[event] && typeof this.behavior[event] == 'function'){
				this.behavior[event](data);
			} else  if(this.globalEvents[event] && typeof this.globalEvents[event] == 'function'){
				this.globalEvents[event](data);
			}
		}
	},

	init : function(){
		this.Model.init();
		this.Controller.init();
		this.View.init();
	},
};


$( document ).ready(Game.init());


function toOwnerView(){
	Game.Controller.fire("changeScheme", "owner");
}

function toContinentView(){
	Game.Controller.fire("changeScheme", "continent");
}

function confirm(){
	Game.Controller.fire("confirmAction");
}