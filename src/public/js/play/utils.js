var Lang = {

	get : function(key, replaceArray){
		var translation = (Model.translations && Model.translations[key]) ? Model.translations[key] : key;
		if(replaceArray){
			for(var key in replaceArray){
				var replaceTranslation = Lang.get(replaceArray[key]);
				translation = translation.replace(":" + key, replaceTranslation);
			}
		}
		return translation;
	}
};


var Utils = {
	
	Type : {
		
		of : function (variable){
			var typeString = Object.prototype.toString.call(variable);
			var type = typeString
				.replace("[object ", '')
				.replace("]", '');
			return type;
		},
		
		is : function (object, typeName){
			return typeName == this.of(object);
		},
		
		isArray : function (obj){
			return this.is(obj, "Array");
		},
		
		isString : function (obj){
			return this.is(obj, "String");
		},
		
		isObject : function (obj){
			return this.is(obj, "Object");
		},
		
		isFunction : function (obj){
			return typeof obj === 'function';
		}
		
	},

	Math2d : {

		distance : function(point1, point2){
			return Math.sqrt(
				Math.pow(point1.x - point2.x, 2)
				+ Math.pow(point1.y - point2.y, 2)
			);
		},
		
		getCenteringWithin : function(point1, point2, borderWidth, borderHeight, margin){
			if(!margin){
				margin = 0;
			}
			var spreadX = Math.abs(point1.x - point2.x) + 2 * margin;
			var spreadY = Math.abs(point1.y - point2.y) + 2 * margin;
			var scaleX = borderWidth / spreadX;
			var scaleY = borderHeight / spreadY;
			// Best result with .../1.55, reason unknown
			var scale = Math.min(scaleX, scaleY) / 1.55;
			scale = Math.max(1, scale);
			
			var borderCenter = {
				x: borderWidth / 2,
				y: borderHeight / 2
			};
			var pointsMiddle = this.middleOf(point1, point2);
			var scaledPointsCenter = {
				// Best result with .../1.1, reason unknown
				x: scale / 1.1 * pointsMiddle.x,
				y: scale * pointsMiddle.y
			};
			var offset = this.fromToVector(borderCenter, scaledPointsCenter);
			
			return {
				scale: scale,
				offset: offset
			};
		},
		
		fromToVector : function(point1, point2){
			var fromTo = this.fromTo(point1, point2);
			var end = fromTo[1];
			return {
				x: -end.x,
				y: -end.y
			};
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
					y: vector[0].y/length
				},
				{
					x: vector[1].x/length,
					y: vector[1].y/length
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
	}

};