var Config = {

	view : {

		container : {

			name : 'container',

			width : 500,

			height : 500,

		},

	},

};

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

		regions : regions,

		players : players,

		continents : continents,

	},

	View : {

		stage : new Kinetic.Stage({
			container: Config.view.container.name,
			width: Config.view.container.width,
			height: Config.view.container.height
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

			var myPoints = [
				end.model.center.x,
				end.model.center.y,
				start.model.center.x - 10 * vec[1].x,
				start.model.center.y - 10 * vec[1].y,
				start.model.center.x + 10 * vec[1].x,
				start.model.center.y + 10 * vec[1].y
			];

			this.elements.pointer = new Kinetic.Line({
				fill : '#111',
				points : myPoints,
				closed: true
			});
			this.symbolLayer.add(this.elements.pointer);
			this.symbolLayer.drawScene();
			this.highlightOn();
		},

		pointerOff : function(){
			this.elements.pointer.remove();
			this.symbolLayer.draw();
			this.highlightOff();
		},

		scheme : function(scheme){
			for(var key in this.elements.regions){
				var regionView = this.elements.regions[key];
				regionView.scheme(scheme);
			}
			Game.View.mapLayer.draw();
			Game.View.mapTopLayer.draw();
		},

		highlightOn : function(){
			Game.View.highlightLayer.add(Game.View.highlightLayer.highlightBackground);
			Game.View.highlightLayer.drawScene();
		},

		highlightOff : function(){
			Game.View.highlightLayer.highlightBackground.remove();
			Game.View.highlightLayer.drawScene();
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
				nameLabel.tag = new Kinetic.Tag({
					fill: '#000',
					pointerDirection: 'down',
					pointerWidth: 10,
					pointerHeight: 10,
					lineJoin: 'round',
					shadowColor: '#000',
					shadowBlur: 10,
					shadowOffset: {x:10, y:10},
					shadowOpacity: 0.5
				});
				/*
				nameLabel.text = new Kinetic.TextPath({
					text: region.name,
					fontFamily: 'Calibri',
					fontSize: 18,
					padding: 5,
					fill: '#fff',
					offsetX: region.name.length * 5,
					offsetY: 15,
					rotation: region.angle,
					data: 'M10,10 C0,0 10,150 100,100 S300,150 400,50'
				});
				*/
				nameLabel.text = new Kinetic.Text({
					text: region.name,
					fontFamily: 'Calibri',
					fontSize: 18,
					padding: 5,
					fill: '#fff',
					offsetX: region.name.length * 5,
					offsetY: 15,
					rotation: region.angle
				});
				nameLabel
					//.add(nameLabel.tag)
					.add(nameLabel.text);


				var troopLabel = new Kinetic.Label({
					x: region.center.x,
					y: region.center.y + 10
				});
				troopLabel.tag = new Kinetic.Tag({
					fill: '#ddd',
					stroke: '#000',
					pointerWidth: 10,
					pointerHeight: 10,
					lineJoin: 'round',
					shadowColor: '#000',
					shadowBlur: 10,
					shadowOffset: {x:10, y:10},
					shadowOpacity: 0.5,
					cornerRadius : 10,
					borderRadius : 10
				});
				troopLabel.text = new Kinetic.Text({
					text: region.troops,
					fontFamily: 'Calibri',
					fontSize: 18,
					padding: 5,
					fill: '#444'
				});
				troopLabel
					.add(troopLabel.tag)
					.add(troopLabel.text);

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
				width: 10000,
				height: 10000,
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
		this.View.init();
		this.Controller.behave("selectStart");
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