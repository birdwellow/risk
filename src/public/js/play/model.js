
var Model = {
	
	digest : function(data) {
		for(var index in data){
			var property = data[index];
			this[index] = property;
		}
		
		this.setupRelationsRecursively();
		
	},

	setupRelationsRecursively : function(object){
		
		if(!object){
			object = this;
		}
		
		for(var propertyName in object){
			var propertyValue = object[propertyName];
			if(Utils.Type.of(propertyValue) === "String"){
				var reference = this.get(propertyValue);
				if(! (reference === undefined) ){
					object[propertyName] = reference;
				}
			}
			else if(Utils.Type.of(propertyValue) === "Array"){
				for(var key in propertyValue){
					var arrayElement = propertyValue[key];
					if(Utils.Type.of(arrayElement) === "String"){
						var ref = this.get(arrayElement);
						if(! (ref === undefined) ){
							propertyValue[key] = ref;
						}
					} else if(Utils.Type.of(arrayElement) === "Object"){
						this.setupRelationsRecursively(arrayElement);
					}
				}
			}
		}
	},
	
	
	get : function(descriptor){
		descriptor = descriptor.replace("[", '').replace("]", '');

		var parts = descriptor.split(":");
		var variable = parts[0];
		var property = parts[1];

		if(!property){
			return undefined;
		}

		var propertyParts = property.split("=");
		var propertyName = propertyParts[0];
		var propertyValue = propertyParts[1];

		var searchTarget = this[variable];
		if(!searchTarget){
			return searchTarget;
		}

		if(Utils.Type.of(searchTarget) === "Array"){
			for(var key in searchTarget){
				var candidate = searchTarget[key];
				var candidatePropertyValue = candidate[propertyName];
				if(candidatePropertyValue && candidatePropertyValue == propertyValue){
					return candidate;
				}
			}
			if(propertyValue){
			}
			return null;
		}
		//else if(typeString == "[object Object]"){
		//	return searchTarget;
		//}

		return undefined;
	},
	
	update : function(modelDelta){
		
	}
	
};