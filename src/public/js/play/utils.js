


var Utils = {
	
	Type : {
		
		of : function (variable){
			var typeString = Object.prototype.toString.call(variable);
			var type = typeString
				.replace("[object ", '')
				.replace("]", '');
			return type;
		}
		
	},

	Math2d : {

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