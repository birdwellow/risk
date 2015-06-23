var Dicer = {

	images : {},
	imageHeight : 0,
	loadCount : 0,
	diceLayer : new Kinetic.Layer(),

	playerDice : new Array(),
	opponentDice : new Array(),

	load : function(imagesObject, callback){
		this.imagesToLoad = 0;
		for(var key in imagesObject){
			this.imagesToLoad += imagesObject[key].length;
		}
		for(var key in imagesObject){
			this.images[key] = new Array();
			for(var i = 0; i < imagesObject[key].length; i++){
				var img = new Image();
				img.src = imagesObject[key][i];
				img.onload = function(){
					++Dicer.loadCount;
					if(Dicer.loadCount == Dicer.imagesToLoad && typeof callback == "function"){
						callback();
					}
				}
				this.images[key][i] = img;
				this.imageHeight = img.height;
			}
		}
	},

	init : function(callback){
		this.diceLayer = Game.View.diceLayer;
		var imagesObject = {
			lying : [
				"res/img/dice/die-1.gif",
				"res/img/dice/die-2.gif",
				"res/img/dice/die-3.gif",
				"res/img/dice/die-4.gif",
				"res/img/dice/die-5.gif",
				"res/img/dice/die-6.gif",
			],
			horizontal : [
				"res/img/dice/dices-1.gif",
				"res/img/dice/dices-2.gif",
				"res/img/dice/dices-3.gif",
				"res/img/dice/dices-4.gif",
				"res/img/dice/dices-5.gif",
				"res/img/dice/dices-6.gif"
			],
			vertical : [
				"res/img/dice/dicet-1.gif",
				"res/img/dice/dicet-2.gif",
				"res/img/dice/dicet-3.gif",
				"res/img/dice/dicet-4.gif",
				"res/img/dice/dicet-5.gif",
				"res/img/dice/dicet-6.gif"
			]
		};
		this.load(imagesObject, callback);
	},

	random : function(range){
		return Math.ceil(range * Math.random());
	},

	createProgram : function(minSteps, maxSteps){
		var arr = ["horizontal","vertical"];
		var program = new Array();
		for(var j = 0; j < minSteps - 1 + this.random(maxSteps - minSteps); j++){
			var mode = this.random(2)-1;
			program.push("lying");
			program.push(arr[mode]);
			program.push("lying");
			program.push(arr[mode]);
		}
		return program;
	},

	on : function(playerDiceNum, opponentDiceNum, startRegion, endRegion){
		//console.log("From: " + startRegion.owner.colorscheme + " to: " + endRegion.owner.colorscheme);
		this.diceLayer.clear();
		if(this.arrows){
			for(var i = 0; i < this.arrows.length; i++){
				this.arrows[i].remove();
			}
		}
		var playerResults = new Array();
		var opponentResults = new Array();
		for(var i = 0; i < playerDiceNum; i++){
			playerResults[i] = this.random(6);
		}
		for(var i = 0; i < opponentDiceNum; i++){
			opponentResults[i] = this.random(6);
		}
		playerResults = playerResults.sort(function(a, b){return b-a});
		opponentResults = opponentResults.sort(function(a, b){return b-a});
		this.group = new Kinetic.Group({
			x: 100,
			y: 100
		});
		/*
		this.background = new Kinetic.Rect({
			x: 0,
			y: 0,
			width: Config.view.dicer.width,
			height: 2 * Config.view.dicer.padding + 2 * Config.view.dicer.spacing + 3 * this.imageHeight,
			//fill: "rgba(0,0,0,0.5)",
			fillLinearGradientColorStops: [
				0,
				startRegion.owner.colorscheme.troops.fill[2],
				0.45,
				startRegion.owner.colorscheme.troops.fill[2],
				0.55,
				endRegion.owner.colorscheme.troops.fill[2],
				1,
				endRegion.owner.colorscheme.troops.fill[2]
			],
			fillLinearGradientStartPoint: {
				x: 0,
				y: 0
			},
			fillLinearGradientEndPoint: {
				x: Config.view.dicer.width,
				y: 0
			},

		});
		this.group.add(this.background);
		*/
		this.diceLayer.add(this.group);
		for(var i = 0; i < playerDiceNum; i++){
			this.playerDice[i] = new Dicer.Die({
				mode : "lying",
				x : Config.view.dicer.padding,
				y : Config.view.dicer.padding + i*(Config.view.dicer.spacing + this.imageHeight),
				images : this.images,
				layer : this.group,
				result : playerResults[i]
			});
		}
		for(var i = 0; i < opponentDiceNum; i++){
			var program = new Array();
			for(var j = 0; j < 4 + this.random(10); j++){
				program.push(this.random(2));
			}
			this.opponentDice[i] = new Dicer.Die({
				mode : "lying",
				x : Config.view.dicer.width - Config.view.dicer.spacing - this.imageHeight,
				y : Config.view.dicer.padding + i * (Config.view.dicer.spacing + this.imageHeight),
				images : this.images,
				layer : this.group,
				result : opponentResults[i]
			});
		}
		Dicer.diceLayer.drawScene();
	},

	Die : function(config){
		this._running = true;
		this.mode = config.mode;
		this.y = config.y;
		this.x = config.x;
		this.images = config.images;
		this.image = new Kinetic.Image({
			x : config.x,
			y : config.y,
			image: this.images[this.mode][Dicer.random(6) - 1]
		});
		this.program = Dicer.createProgram(4, 10);
		this.layer = config.layer;
		this.layer.add(this.image);
		this.step = 0;
		this.result = config.result;
		this.nextFrame = function(){
			if(this.program[this.step] == undefined){
				if(this.running()){
					this.stop();
				}
			} else {
				this.mode = this.program[this.step];
				var random = Dicer.random(6);
				this.image.setImage(this.images[this.mode][random-1]);
				++this.step;
			}
		};
		this.stop = function(){
			this._running = false;
			//this.result = Dicer.random(6);
			this.mode = "lying";
			this.image.setImage(this.images[this.mode][this.result-1]);
		};
		this.running = function(){
			return this._running;
		};
	},

	running : function(){
		var running = false;
		for(var i = 0; i < Dicer.playerDice.length; i++){
			running = running || Dicer.playerDice[i].running();
		}
		for(var i = 0; i < Dicer.opponentDice.length; i++){
			running = running || Dicer.opponentDice[i].running();
		}
		return running;
	},

	throw : function(playerDiceNum, opponentDiceNum, callback, startRegion, endRegion){
		if(!this.running()){
			this.on(playerDiceNum, opponentDiceNum, startRegion, endRegion);
			this.nextTime = 0;
			this.frameStep = 0;
			var animation = new Kinetic.Animation(function(frame){
				if(Dicer.nextTime == 0){
					Dicer.nextTime = frame.time;
				}
				if(frame.time > Dicer.nextTime){
					if(Dicer.running()){
						Dicer.frame();
						Dicer.nextTime += 50;
					} else {
						this.stop();
						var result = Dicer.getResult();
						var wins = Dicer.evaluate(result);
						Dicer.animate(wins, callback);
					}
				}
			});
			animation.start();
		}
	},

	evaluate : function(resultObject){
		var length = Math.min(resultObject.player.length, resultObject.opponent.length);
		var wins = [];
		for(var i = 0; i < length; i++){
			var playerWins = (resultObject.player[i] > resultObject.opponent[i]);
			wins[i] = playerWins;
		}
		return wins;
	},

	animate : function(wins, callback){
		for(var i = 0; i < wins.length; i++){
			var playerDiePoint = {
				x: Dicer.playerDice[i].x,
				y: Dicer.playerDice[i].y
			};
			var opponentDiePoint = {
				x: Dicer.opponentDice[i].x,
				y: Dicer.opponentDice[i].y
			};
			if(wins[i]){
				this.pointer(this.playerDice[i], this.opponentDice[i], i, "win", callback);
			} else {
				this.pointer(this.opponentDice[i], this.playerDice[i], i, "loose", callback);
			}
		}
	},

	pointer : function(startDie, endDie, index, mode, callback){
		if(!this.arrows){
			this.arrows = [];
		}
		var distance = {
			x: 20,
			y: 0
		};
		var startPoint = {
			x: startDie.x + startDie.image.getImage().height/2,
			y: startDie.y + startDie.image.getImage().width/2
		};
		var endPoint = {
			x: endDie.x + endDie.image.getImage().height/2,
			y: endDie.y + endDie.image.getImage().width/2
		};
		if(startPoint.x < endPoint.x){
			startPoint.x += distance.x;
			endPoint.x -= distance.x;
		} else {
			startPoint.x -= distance.x;
			endPoint.x += distance.x;
		}
		var vec = Math2d.fromTo(startPoint, endPoint);
		vec = Math2d.normalize(vec);
		vec = Math2d.orthogonalize(vec);

		this.arrows[index] = new Kinetic.Line({
			fill : Config.view.scheme.dicePointer[mode].fill,
			stroke : Config.view.scheme.dicePointer[mode].stroke,
			strokeWidth : Config.view.scheme.dicePointer[mode].strokeWidth,
			points : [
				startPoint.x - 10 * vec[1].x,
				startPoint.y - 10 * vec[1].y,
				endPoint.x,
				endPoint.y,
				startPoint.x + 10 * vec[1].x,
				startPoint.y + 10 * vec[1].y
			],
			closed: true,

			fillLinearGradientColorStops: Config.view.scheme.dicePointer[mode].fillLinearGradientColorStops,
			fillLinearGradientStartPoint: {
				x: startPoint.x,
				y: startPoint.y
			},
			fillLinearGradientEndPoint: {
				x: endPoint.x,
				y: endPoint.y
			},
		});
		this.group.add(this.arrows[index]);
		this.diceLayer.drawScene();

		var duration = Config.view.scheme.dicePointer[mode].speed * 1000;
		var animation = new Kinetic.Animation(function(frame){
			if(frame.time > duration){
				this.stop();
				if(typeof callback == "function"){
					callback(mode == "win");
				}
			} else {
				Dicer.arrows[index].setPoints([
					startPoint.x - 10 * vec[1].x,
					startPoint.y - 10 * vec[1].y,
					startPoint.x + (endPoint.x - startPoint.x) * frame.time/duration,
					startPoint.y + (endPoint.y - startPoint.y) * frame.time/duration,
					startPoint.x + 10 * vec[1].x,
					startPoint.y + 10 * vec[1].y
				]);
				Dicer.diceLayer.drawScene();
			}
		});
		animation.start();
	},

	getResult : function(){
		var result = {
			player : [],
			opponent : []
		};
		for(var i = 0; i < this.playerDice.length; i++){
			result.player[i] = this.playerDice[i].result;
		}
		for(var i = 0; i < this.opponentDice.length; i++){
			result.opponent[i] = this.opponentDice[i].result;
		}
		return result;
	},

	frame : function(){
		for(var i = 0; i < this.playerDice.length; i++){
			this.playerDice[i].nextFrame();
		}
		for(var i = 0; i < this.opponentDice.length; i++){
			this.opponentDice[i].nextFrame();
		}
		this.frameStep++;
		this.diceLayer.drawScene();
	}

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
			this.regions = regions;
			this.players = players;
			this.continents = continents;
			for(var i = 0; i < this.regions.length; i++){
				var region = this.regions[i];
				region.setOwner = function(newOwner){
					this.owner = newOwner;
					this.path.update();
				}
				region.addUnit = function(){
					++this.troops;
					this.path.update();
				}
				region.removeUnit = function(){
					--this.troops;
					this.path.update();
				}
			}
		},

	},

	View : {

		mapStage : new Kinetic.Stage({
			container: Config.view.map.containerName,
			width: Config.view.map.width,
			height: Config.view.map.height
		}),

		mapLayer : new Kinetic.Layer({}),
		labelLayer : new Kinetic.Layer({}),
		highlightLayer : new Kinetic.Layer({}),
		mapTopLayer : new Kinetic.Layer({}),
		labelTopLayer : new Kinetic.Layer({}),
		symbolLayer : new Kinetic.Layer({}),
		controlsLayer : new Kinetic.Layer({}),

		diceLayer : new Kinetic.Layer({}),

		elements : {

			regions : [],

			pointer : null,

		},

		troopShift : function(start, end){
			this.troopShiftOff();
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
			this.elements.decButton.on("click", function(){
				Game.Controller.fire("retractUnit");
			});
			this.controlsLayer.draw();
			this.symbolLayer.draw();
			
		},

		troopShiftOff : function(){
			if(this.elements.incButton)
				this.elements.incButton.remove();
			if(this.elements.decButton)
				this.elements.decButton.remove();
			this.elements.incButton = null;
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
				region.path = path;




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
					this.troopLabel.text.setText(this.model.troops);
					if(this.getLayer()){
						this.getLayer().draw();
					}
					if(this.nameLabel.getLayer() && this.nameLabel.getLayer() != this.getLayer()){
						this.nameLabel.getLayer().draw();
					}
					if(this.troopLabel.getLayer() && this.troopLabel.getLayer() != this.getLayer() && this.troopLabel.getLayer() != this.nameLabel.getLayer()){
						this.troopLabel.getLayer().draw();
					}
				};

				path.scheme("owner");
				Game.View.mapLayer.add(path);

				path.troopLabel.path = path;
				path.nameLabel.path = path;



				path.on('mouseover', function() {
					Game.Controller.fire("mouseoverRegionPath", this.model);
				});
				path.nameLabel.on('mouseover', function() {
					Game.Controller.fire("mouseoverRegionPath", this.path.model);
				}); 
				path.troopLabel.on('mouseover', function() {
					Game.Controller.fire("mouseoverRegionPath", this.path.model);
				}); 
				path.on('click', function() {
					Game.Controller.fire("clickRegionPath", this.model);
				});
				path.nameLabel.on('mouseout', function() {
					Game.Controller.fire("mouseoutRegionPath", this.path.model);
				});
				path.troopLabel.on('mouseout', function() {
					Game.Controller.fire("mouseoutRegionPath", this.path.model);
				});
				path.on('mouseout', function() {
					Game.Controller.fire("mouseoutRegionPath", this.model);
				});
				path.nameLabel.on('click', function() {
					Game.Controller.fire("clickRegionPath", this.path.model);
				});
				path.troopLabel.on('click', function() {
					Game.Controller.fire("clickRegionPath", this.path.model);
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
			this.mapStage.add(Game.View.diceLayer);
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
		Dicer.init();
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