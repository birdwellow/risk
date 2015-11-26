var c = document.getElementById("mapraster");
var context = c.getContext("2d");
var imageObj = new Image();

imageObj.onload = function() {
	context.drawImage(imageObj, 0, 0);
	var imgWidth = this.width;
	var imgHeight = this.height;
	var imgData = context.getImageData(0, 0, this.width, this.height);
	var pointsArray = new Array();
	var lineArray = new Array();
	var suggestedPoints = new Array();
	for (var i = 0; i < imgData.data.length; i += 4){
		var sum = imgData.data[i+0] + imgData.data[i+1] + imgData.data[i+2];
		var decision = ((sum > 600 || imgData.data[i+3] < 155) ? 255 : 0);
		imgData.data[i+0] = decision;
		imgData.data[i+1] = decision;
		imgData.data[i+2] = decision;
		imgData.data[i+3] = 255;
		
		if(decision < 200){
			var suggestedPoint = new SuggestedPoint(0, 0);
			suggestedPoints.push(suggestedPoint);
		}

		lineArray.push(decision);
		
		if(lineArray.length >= imgWidth){
			pointsArray.push(lineArray);
			lineArray = new Array();
		}
	}
	context.putImageData(imgData, 0, 0);
	var ratio = suggestedPoints.length/(pointsArray[0].length*pointsArray.length) * 10000;
	ratio = Math.round(ratio);
	ratio = ratio / 100;
	alert("Points: " + suggestedPoints.length + " (" + ratio + "%)");
};
imageObj.src = "map.png";

function SuggestedPoint(x, y){
	this.x = x;
	this.y = y;
}