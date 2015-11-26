var Config = {
	
	background: "img/data.jpg"
	
};

var outenabled = true;

function Point(x, y){
	var me = this;
	this.deactivated = false;
	this.isNew = true;
	this.x = Math.round(x);
	this.y = Math.round(y);
	this.symbol = new Kinetic.Circle({
		x : this.x,
		y : this.y,
		radius : 1,
		strokeWidth : 1,
		stroke : "red"
	});
	this.symbol.on("mouseover", function(){
		document.body.style.cursor = "pointer";
		if(!me.isNew){
			this.radius(5);
			App.view.refresh();
		}
	});
	this.symbol.on("click", function(){
		if((App.mode == "deleting" || App.rightKeyDown) && !me.deactivated){
			this.remove();
			App.removePoint(me);
			App.view.refresh();
		}
	});
	this.symbol.on("mouseout", function(){
		document.body.style.cursor = "default";
		me.isNew = false;
		this.draggable(true);
		this.radius(1);
		App.view.refresh();
	});
	this.symbol.on("dragmove", function(e){
		if(App.view.collidingShapes && App.view.collidingShapes.length)
		for (var i = 0; i < App.view.collidingShapes.length; ++i){
			App.view.collidingShapes[i].fire("mouseout");
		}
		var pos = App.view.stage.getPointerPosition();
		var collidingShapes = App.view.stage.getAllIntersections(pos);
		var collidingPoints = new Array();
		for (var i = 0; i < collidingShapes.length; ++i){
			if(collidingShapes[i] instanceof Kinetic.Circle){
				collidingShapes[i].fire("mouseover");
				collidingPoints.push(collidingShapes[i]);
			}
		}
		App.view.collidingShapes = collidingPoints;
		document.body.style.cursor = "pointer";
		if(App.mode == "editting"){
			me.x = Math.round(me.symbol.x());
			me.y = Math.round(me.symbol.y());
			App.currentBorder.refresh();
		}
	});
	this.symbol.on("dragend", function(e){
		var dropPointCorrdinates = null;
		if(App.view.collidingShapes && App.view.collidingShapes.length)
		for (var i = 0; i < App.view.collidingShapes.length; ++i){
			if(App.view.collidingShapes[i] instanceof Kinetic.Circle){
				if(dropPointCorrdinates == null){
					dropPointCorrdinates = {
						x : App.view.collidingShapes[i].x(),
						y : App.view.collidingShapes[i].y()
					};
				}
				App.view.collidingShapes[i].fire("mouseout");
			}
		}
		if(App.mode == "editting"){
			me.x = dropPointCorrdinates.x;
			me.y = dropPointCorrdinates.y;
			this.x(dropPointCorrdinates.x);
			this.y(dropPointCorrdinates.y);
			App.currentBorder.refresh();
			App.view.refresh();
		}
		App.view.collidingShapes = null;
		outCoordinates();
	});
	this.symbol.on("dragstart", function(){
		if(App.mode == "editting"){
			this.radius(5);
			App.view.refresh();
		}
	});
	this.toString = function(){
		return this.x + "," + this.y;
	};
	this.setX = function(x){
		this.x = Math.round(x);
	};
	this.setY = function(y){
		this.y = Math.round(y);
	};
	this.deactivate = function(){
		me.deactivated = true;
		me.symbol.stroke("rgba(0,0,0,0)");
		me.symbol.draggable(false);
		me.symbol.radius(1);
		me.symbol.strokeWidth(1);
		me.symbol.on("mouseover", function(){
			me.symbol.radius(3);
			me.symbol.stroke("rgba(0,155,0,0.5)");
			App.view.refresh();
		});
		me.symbol.on("mouseout", function(){
			me.symbol.radius(1);
			me.symbol.stroke("rgba(0,0,0,0)");
			App.view.refresh();
		});
		me.symbol.on("click", function(){
			if(App.mode == "editting"){
				var x = me.symbol.x();
				var y = me.symbol.y();
				var point = new Point(x, y);
				App.addPoint(point);
				App.view.refresh();
			}
		});
	};
}

function Border(){
	this.points = new Array();
	this.shape = new Kinetic.Line({
		points: [],
		stroke: "black",
		strokeWidth: 1,
		closed: true
	});
	this.lastPoint = null;
	this.firstPoint = null;
	this.isComplete = function(){
		return this.points.length >= 3;
	};
	this.addPoint = function(point){
		if(App.mode == "editting"){
			this.points.push(point);
			if(this.points.length == 2){
				var points = this.toArray();
				this.shape = new Kinetic.Line({
					points: points,
					stroke: "rgba(0,0,0,0.5)",
					strokeWidth: 1,
					closed: true,
					fill: App.view.color,
					lineJoin: "round"
				});
				this.shape.on("click", function(){
					if(App.mode == "editting"){
						out("clicked Border");
						var mousePos = App.view.stage.getPointerPosition();
						var scale = App.view.scale;
						mousePos.x = mousePos.x/scale;
						mousePos.y = mousePos.y/scale;
						var point = new Point(mousePos.x, mousePos.y);
						App.addPoint(point);
						App.view.refresh();
					}
				});
				App.view.selectionLayer.add(this.shape);
				this.shape.moveToBottom();
				App.view.map.moveToBottom();
				App.view.refresh();
			}
			else if(this.points.length > 2){
				this.shape.points(this.toArray());
				App.view.refresh();
			}
		}
	};
	this.removePoint = function(point){
		if((App.mode == "deleting" || App.rightKeyDown)){
			var index = this.points.indexOf(point);
			if (index > -1) {
				this.points.splice(index, 1);
				if(this.points.length > 1){
					var points = this.toArray();
					this.shape.remove();
					this.shape = new Kinetic.Line({
						points: points,
						stroke: "rgba(0,0,0,0.5)",
						strokeWidth: 1,
						closed: true,
						fill: App.view.color,
						lineJoin: "round"
					});
					this.shape.on("click", function(){
						if(App.mode == "editting"){
							out("clicked Border");
							var mousePos = App.view.stage.getPointerPosition();
							var scale = App.view.scale;
							mousePos.x = mousePos.x/scale;
							mousePos.y = mousePos.y/scale;
							var point = new Point(mousePos.x, mousePos.y);
							App.addPoint(point);
							App.view.refresh();
						}
					});
					App.view.selectionLayer.add(this.shape);
					this.shape.moveToBottom();
					App.view.map.moveToBottom();
					App.view.refresh();
				}
				else{
					this.shape.remove();
					App.view.refresh();
				}
			}
		}
	};
	this.removeAllPoints = function(){
		for(var i = 0; i < this.points.length; i++){
			this.points[i].deactivate();
		}
		App.view.refresh();
	};
	this.refresh = function(){
		this.shape.points(this.toArray());
		App.view.refresh();
	};
	this.toArray = function(){
		var array = new Array();
		for(var i = 0; i < this.points.length; i++){
			array.push(this.points[i].x);
			array.push(this.points[i].y);
		}
		return array;
	};
	this.toString = function(){
		var arrayString = "[" + this.points + "]";
		arrayString = arrayString.replace(/,/g, ", ");
		return arrayString;
	};
}

function Country(){
	this.borders = new Array();
	this.addBorder = function(border){
		this.borders.push(border);
	};
	this.toArray = function(){
		var array = new Array();
		for(var i = 0; i < this.borders.length; i++){
			array.push(this.borders[i]);
		}
		return array;
	};
	this.toString = function(){
		var tempArray = this.borders.slice(0);
		if(this == App.currentCountry){
			tempArray[tempArray.length] = App.currentBorder;
		}
		var arrayString = "[" + tempArray + "]";
		return arrayString;
	};
	this.toSvgPath = function(){
		var array = new Array();
		for(var i = 0; i < this.borders.length; i++){
			array.push(this.borders[i].toArray());
		}
		array.push(App.currentBorder.toArray());
		return Convert.pointsArrayToPath(array);
	};
	this.isComplete = function(){
		return this.borders.length >= 1;
	};
}

function CountryArray(){
	this.countries = new Array();
	this.addCountry = function(country){
		this.countries.push(country);
	};
	this.toArray = function(){
		var array = new Array();
		for(var i = 0; i < this.countries.length; i++){
			array.push(this.countries[i]);
		}
		return array;
	};
	this.toString = function(){
		var returnString = "";
		for(var i = 0; i < this.countries.length; i++){
			returnString += "var areaPoints" + (i+1) + " = " + this.countries[i].toString() + ";\n";
		}
		returnString += "var areaPoints" + (this.countries.length + 1) + " = " + App.currentCountry.toString() + ";\n";
		return returnString;
	};
	this.toSvgPathData = function(){
		var returnString = "";
		for(var i = 0; i < this.countries.length; i++){
			returnString += "var areaPath" + (i+1) + " = \"" + this.countries[i].toSvgPath() + "\";\n";
		}
		returnString += "var areaPath" + (this.countries.length + 1) + " = \"" + App.currentCountry.toSvgPath() + "\";\n";
		return returnString;
	};
}

var App = {
	
	mode : "editting",
	
	currentBorder : new Border(),
	currentCountry : new Country(),
	countryArray : new CountryArray(),
	
	rightKeyDown : false,
	
	initCoordinates : function(){
		this.finishCountry();
		
		var countrySplit = "&c";
		var areaSplit = "&a";
		var content = $("#coordinates").val();
		content = content.replace(/var .* = /g, "");
		content = content.replace(/;/g, countrySplit);
		content = content.replace(/\n/g, "");
		content = content.replace(/ /g, "");
		n = content.lastIndexOf(countrySplit);
		if (n >= 0 && n + countrySplit.length >= content.length) {
			content = content.substring(0, n);
		}
		var countries = content.split(countrySplit);
		for(var i = 0; i < countries.length; i++){
			var border = countries[i].replace(/\],\[/g, "]" + areaSplit + "[");
			border = border.replace(/\]\]/g, "]");
			border = border.replace(/\[\[/g, "[");
			var areas = border.split(areaSplit);
			for(var j = 0; j < areas.length; j++){
				areas[j] = areas[j].replace(/\]/g, "");
				areas[j] = areas[j].replace(/\[/g, "");
				var points = areas[j].split(",");
				for(var k = 0; k < points.length; k += 2){
					var point = new Point(points[k], points[k+1]);
					App.addPoint(point);
				}
				App.finishArea();
			}
			App.finishCountry();
			out("rendered country " + i);
		}
		App.view.refresh();
		outCoordinates();
	},
	
	init : function(){
		$("#coordinates").val("");
		$("#pathdata").val("");
		document.addEventListener('contextmenu', function(e) {
            e.preventDefault();
		}, false);
		$("#mainstage").bind('mousewheel DOMMouseScroll', function(event){
		    if (event.originalEvent.wheelDelta > 0 || event.originalEvent.detail < 0) {
		        zoomIn();
		    }
		    else {
		        zoomOut();
		    }
		});
		$(window).bind('mousedown', function(event){
			if(event.button == 2){
				document.body.style.cursor = "pointer";
				App.rightKeyDown = true;
			}
		});
		$(window).bind('mouseup', function(event){
			document.body.style.cursor = "default";
			App.rightKeyDown = false;
		});
		$("#coordinates").on('keydown', function(event){
			if(event.which == 13 && $("#coordinates").val().trim() != ""){
			}
		});
		this.view.init();
	},
	
	changeMode : function(){
		if(this.mode == "editting"){
			$("body").css("background", "rgba(255,0,0,0.5");
			this.mode = "deleting";
		}
		else if((this.mode == "deleting" || this.rightKeyDown)){
			$("body").css("background", "none");
			this.mode = "editting";
		}
		$("#mode").html("Mode: " + this.mode);
	},
	
	finishArea : function(){
		if(this.currentBorder.isComplete()){
			this.currentCountry.addBorder(this.currentBorder);
			this.currentBorder.removeAllPoints();
			this.currentBorder = new Border();
		}
	},
	
	finishCountry : function(){
		this.finishArea();
		if(this.currentCountry.isComplete()){
			App.view.setNewColor();
			this.countryArray.addCountry(this.currentCountry);
			this.currentCountry = new Country();
		}
	},
	
	addPoint : function(point){
		this.currentBorder.addPoint(point);
		this.view.addPoint(point);
		outCoordinates();
	},
	
	removePoint : function(point){
		this.currentBorder.removePoint(point);
		outCoordinates();
	},
	
	// ********* VIEW ************
	view : {

		scale : 1,
		
		width: 0,
		height: 0,
		
		stage : null,
		
		selectionLayer : new Kinetic.Layer(),

		backgroundImage : new Image(),
		map : null,
		
		suggestedPoints : new Array(),
		
		collidingShapes : null,

		colorIndex : 0,
		
		colors : new Array(
				"rgba(0,255,0,0.5)",
				"rgba(255,0,0,0.5)",
				"rgba(0,0,255,0.5)",

				"rgba(255,150,0,0.5)",
				"rgba(0,150,255,0.5)",
				"rgba(150,0,255,0.5)",
				
				"rgba(170,135,0,0.5)",
				"rgba(0,235,130,0.5)",
				"rgba(255,0,150,0.5)"
		),
		
		setNewColor : function(){
			var color = this.colors[this.colorIndex];
			this.colorIndex++;
			if(this.colorIndex >= this.colors.length){
				this.colorIndex = 0;
			}
			this.color = color;
		},
		
		color : "rgba(0,255,0,0.5)",
		
		init : function(){
			this.setNewColor();
			
			this.backgroundImage.src = Config.background;
			this.width = this.backgroundImage.width;
			this.height = this.backgroundImage.height;
			$("#mainstage").width(this.width);
			$("#mainstage").height(this.height);
			this.stage = new Kinetic.Stage({
				container: 'mainstage',
				width: this.width,
				height: this.height
			});
			this.stage.on("mouseover", function(){
				if(App.mode == "editting"){
//					document.body.style.cursor = "crosshair";
				}
				else if((App.mode == "deleting" || App.rightKeyDown)){
					document.body.style.cursor = "pointer";
				}
			});
			this.stage.on("mouseout", function(){
				document.body.style.cursor = "default";
			});
			this.stage.add(this.selectionLayer);
			this.map = new Kinetic.Rect({
				x: 0,
				y: 0,
				width: this.width,
				height: this.height,
				closed: true,
				fillPatternImage: this.backgroundImage,
				fillPatternRepeat: "no-repeat",
				opacity: 0.5
			});
			this.selectionLayer.add(this.map);
			this.map.on("click", function(e){
				if(App.mode == "editting"){
					var mousePos = App.view.stage.getPointerPosition();
					var scale = App.view.scale;
					mousePos.x = mousePos.x/scale;
					mousePos.y = mousePos.y/scale;
					var point = new Point(mousePos.x, mousePos.y);
					App.addPoint(point);
					App.view.refresh();
				}
			});
			this.selectionLayer.on("mousemove", function(e){
				var mousePos = App.view.stage.getPointerPosition();
				mousePos.x = mousePos.x/App.view.scale;
				mousePos.y = mousePos.y/App.view.scale;
				outPosition(mousePos.x, mousePos.y);
			});
			this.refresh();
		},
		
		refresh : function(){
			this.selectionLayer.draw();
		},
		
		addPoint : function(point){
			this.selectionLayer.add(point.symbol);
			this.refresh();
		},
		
		zoomIn : function(){
			App.view.scale += 0.1;
			App.view.applyScale();
		},
		
		zoomOut : function(){
			if(App.view.scale > 0.2){
				App.view.scale -= 0.1;
			}
			App.view.applyScale();
		},
		
		applyScale : function(){
			App.view.stage.scale({
				x: App.view.scale,
				y: App.view.scale
			});
			var width = App.view.map.width()*App.view.scale;
			var height = App.view.map.height()*App.view.scale; 
			$("#mainstage").width(width);
			$("#mainstage").height(height);
			App.view.stage.size({width : width, height : height});
			App.view.refresh();
		}
	},
	
	util : {
	},

	math : {
	}
};

function out(text){
	if(outenabled){
		var output = $("#miniconsole").html();
		$("#miniconsole").html(output + text + "<br/>");
	}
};

function outCoordinates(){
	$("#coordinates").val(App.countryArray);
	$("#pathdata").val(App.countryArray.toSvgPathData());
};

function outPosition(x, y){
	$("#mouseposition").html(Math.round(x) + " | " + Math.round(y));
};

function zoomOut(){
	App.view.zoomOut();
}

function zoomIn(){
	App.view.zoomIn();
}

function changeMode(){
	App.changeMode();
}

function finishArea(){
	App.finishArea();
}

function finishCountry(){
	App.finishCountry();
}

function initCoordinates(){
	App.initCoordinates();
}


var Convert = {
	
	pointsToPath : function (points){
		for(var i in points){
			points[i] = Math.round(points[i]);
		}
		var x0=points.shift(), y0=points.shift();
		var pathdata = 'M'+x0+','+y0+'L'+points.join(' ');
		pathdata+='z';
		return pathdata;
	},
	
	pointsArrayToPath : function(pointsArray){
		var pathData = "";
		for(var i in pointsArray){
			pathData += this.pointsToPath(pointsArray[i]);
		}
		return pathData;
	}
	
};