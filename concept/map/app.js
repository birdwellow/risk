

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

		stage : new Kinetic.Stage({
			container: Config.view.containerName,
			width: Config.view.width,
			height: Config.view.height
		}),

		mapLayer : new Kinetic.Layer({}),
		labelLayer : new Kinetic.Layer({}),
		highlightLayer : new Kinetic.Layer({}),
		mapTopLayer : new Kinetic.Layer({}),
		labelTopLayer : new Kinetic.Layer({}),
		symbolLayer : new Kinetic.Layer({}),

		elements : {

			regions : [],

			pointer : null,

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
				closed: true
			});
			this.symbolLayer.add(this.elements.pointer);
			this.symbolLayer.drawScene();
			this.highlightOn();
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

				var nameLabel = new Kinetic.Label({
					x: region.center.x,
					y: region.center.y
				});
				nameLabel.tag = new Kinetic.Tag(Config.view.scheme.regionTags);
				nameLabel.add(nameLabel.tag);
				
				var config = Config.view.scheme.regionTexts;
				config.text = region.name;
				config.offsetX = region.name.length + config.offsetX;
				config.rotation = region.angle;
				config.data = region.path;
				if(region.pathData){
					nameLabel.text = new Kinetic.TextPath(config);
				} else {
					nameLabel.text = new Kinetic.Text(config);
				}
				nameLabel.add(nameLabel.text);


				var troopLabel = new Kinetic.Label({
					x: region.center.x,
					y: region.center.y + 10
				});
				troopLabel.tag = new Kinetic.Tag(Config.view.scheme.troopTags);
				troopLabel.add(troopLabel.tag)
				
				var config = Config.view.scheme.troopTexts;
				config.text = region.troops;
				troopLabel.text = new Kinetic.Text(config);
				troopLabel.add(troopLabel.text);

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
					this.nameLabel.moveTo(Game.View.labelLayer);
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
					this.nameLabel.moveTo(Game.View.labelTopLayer);
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
				};

				path.scheme("owner");
				Game.View.mapLayer.add(path);

				path.nameLabel = nameLabel;
				path.troopLabel = troopLabel;
				troopLabel.path = path;
				nameLabel.path = path;



				path.on('mouseover', function() {
					Game.Controller.fire("mouseoverRegionPath", this);
				});
				nameLabel.on('mouseover', function() {
					Game.Controller.fire("mouseoverRegionPath", this.path);
				}); 
				troopLabel.on('mouseover', function() {
					Game.Controller.fire("mouseoverRegionPath", this.path);
				}); 
				path.on('click', function() {
					Game.Controller.fire("clickRegionPath", this);
				});
				nameLabel.on('mouseout', function() {
					Game.Controller.fire("mouseoutRegionPath", this.path);
				});
				troopLabel.on('mouseout', function() {
					Game.Controller.fire("mouseoutRegionPath", this.path);
				});
				path.on('mouseout', function() {
					Game.Controller.fire("mouseoutRegionPath", this);
				});
				nameLabel.on('click', function() {
					Game.Controller.fire("clickRegionPath", this.path);
				});
				troopLabel.on('click', function() {
					Game.Controller.fire("clickRegionPath", this.path);
				});
				Game.View.labelLayer.add(nameLabel);
				Game.View.labelLayer.add(troopLabel);
			}

			var highlightBackground = new Kinetic.Rect({
				fill: '#000',
				width: 5000,
				height: 5000,
				opacity: 0.5
			});
			Game.View.highlightLayer.highlightBackground = highlightBackground;

			this.stage.add(Game.View.mapLayer);
			this.stage.add(Game.View.labelLayer);
			this.stage.add(Game.View.highlightLayer);
			this.stage.add(Game.View.mapTopLayer);
			this.stage.add(Game.View.symbolLayer);
			this.stage.add(Game.View.labelTopLayer);
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


Game.init();


function toOwnerView(){
	Game.Controller.fire("changeScheme", "owner");
}

function toContinentView(){
	Game.Controller.fire("changeScheme", "continent");
}

function confirm(){
	Game.Controller.fire("confirmAction");
}