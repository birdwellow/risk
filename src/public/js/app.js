function log(arg){
    console.log(arg);
}
    
var UI = {
    
    init : function() {

        $('#bug-reporter').each(function(index, value){
            var element = $(value);
            window.bugreporter = new BugReporter(element);
        });
		
		$('[data-toggle="tooltip"]').tooltip();
        
        $('input[type="userselector"]').each(function(index, value){
            var element = $(value);
            new UserSelector(element);
        });

        $('select').each(function(index, value){
            var element = $(value);
            new DropDown(element);
        });

        $('input[type="checkbox"]').each(function(index, value){
            var element = $(value);
            new CheckBox(element);
        });

        $('*[toggle-for]').each(function(index, value){
            var toggle = $(value);
            var targetId = toggle.attr("toggle-for");
            toggle.click(function(){
                var visible = UI.toggle("#"+targetId);
                if(visible){
                    toggle.addClass("active");
                } else {
                    toggle.removeClass("active");
                }
            });
        });

        $('*[toggle-function]').each(function(index, value){
            var toggle = $(value);
            var callback = toggle.attr("toggle-function");
            toggle.click(function(){
				var active = eval(callback + "(toggle)");
            });
        });
		
		$('.confirm-matchnotification').click(function(){
			UI.fade(".alert-matchnotification");
			$.get("/match/removematchnotification");
		});
		
		window.toggleFixSideBar = function(toggle){
			$("#sidebar").removeClass("initial");
			if($('#sidebar').hasClass("in")){
				$('#sidebar').removeClass("in");
				$('#sidebar').addClass("out");
                toggle.removeClass("active");
				toggle.children().attr("src","/img/in.png");
			} else if($('#sidebar').hasClass("out")){
				$('#sidebar').removeClass("out");
				$('#sidebar').addClass("in");
                toggle.addClass("active");
				toggle.children().attr("src","/img/out.png");
			}
		};

        /*setTimeout(function(){
            $(".alert-success").fadeOut(1000, function(){
                $(".alert-success").hide();
            });
        }, 3000);*/
        
    },
    
    toggle : function (selector){
        element = $(selector);
        if(element.is(":visible")){
            element.hide("blind", 250);
            return false;
        } else {
            element.show("blind", 250);
            return true;
        }
    },
    
    fade : function (selector){
        element = $(selector);
        if(element.is(":visible")){
            element.fadeOut(500);
            return false;
        } else {
            element.fadeIn(500);
            return true;
        }
    },
    
    error : function(message, title, closeLabel){
        
        var config = {
            type : "error",
            message : message,
            title : title || "Error",
            buttons : {
                close: {
                    label : closeLabel || "OK"
                }
            }
        };
        this.errorDialog = new Dialog(config);
        
    },
    
    warn : function(message, title, closeLabel){
        
        var config = {
            type : "warn",
            message : message,
            title : title || "Warning",
            buttons : {
                close: {
                    label : closeLabel || "OK"
                }
            }
        };
        this.warnDialog = new Dialog(config);
        
    },
    
    info : function(message, title, closeLabel){
        
        var config = {
            type : "info",
            message : message,
            title : title || "Information",
            buttons : {
                close: {
                    label : closeLabel || "OK"
                }
            }
        };
        this.infoDialog = new Dialog(config);
        
    },
    
    confirmRedirect : function(url, message, title, abortLabel, confirmLabel, type){
        
        var config = {
            allowCloseOnBackground : false,
            type : type || "info",
            message : message,
            title : title || "Confirm",
            buttons : {
                close: {
                    label : abortLabel || "Cancel"
                },
                action : {
                    label : confirmLabel || "OK",
                    callback : function(){
                        UI.goto(url);
                    }
                }
            }
        };
        this.warnDialog = new Dialog(config);
        
    },
    
    confirmAction : function(actionCallback, message, title, abortLabel, confirmLabel, type){
        
        var config = {
            allowCloseOnBackground : false,
            type : type || "info",
            message : message,
            title : title || "Confirm",
            buttons : {
                close: {
                    label : abortLabel || "Cancel"
                },
                action : {
                    label : confirmLabel || "OK",
                    callback : actionCallback
                }
            }
        };
        this.warnDialog = new Dialog(config);
        
    },
    
    setLoading : function(element, isLoading) {
        
        if(isLoading == false){
            element.find(".wait-modal").remove();
        } else {
            var waitPanel = HTML.make("div", "wait-modal");
            var waitImg = HTML.make("img", "wait-img").attr("src", "/img/loading_big.gif");
            waitPanel.append(waitImg);
            element.prepend(waitPanel);
        }
        
    },
    
    loadContentFromTo : function(url, selector) {
        
        var element = $(selector);
        this.setLoading(element, true);
        
        $.get(url, function (data) {
            element.html(data);
            UI.init();
        });
        
    },
    
    goto : function(url) {
        
        document.location.href = url;
        
    },
    
    
};

function loadThread (threadId, referrer) {
    
    $("a.selected").removeClass("selected");
    $(referrer).addClass("selected");
    $(referrer).find("img.icon").remove();
    $(referrer).find("div.unread").removeClass("unread");
    
    var url = "/thread/" + threadId + "/ajaxpart";
    UI.loadContentFromTo(url, "#thread");
    $("#alert-container").hide();
    
}

var HTML = {

    body : $(document.body),
    
    make : function(tagname, classes, id, type){
        var element = $(document.createElement(tagname));
        if(classes){
            element.attr("class", classes);
        }
        if(id){
            element.attr("id", id);
        }
        if(type){
            element.attr("type", type);
        }
        return element;
    }
    
};

$(document).ready(function(){

    UI.init();
    
});


function CheckBox(baseInput) {
    
    baseInput.hide();
    
    var selfpointer = this;
    
    this._checked;
    var iconCheckedSrc = "/img/checkbox-true.png";
    var iconUncheckedSrc = "/img/checkbox-false.png";
    
    this._checkIcon = HTML.make("img", "icon");
    
    var id = baseInput.attr("id");
    this._label = $("label[for='" + id + "']");
    
    this.set = function(check){
        if(check){
            this._checked = true;
            this._checkIcon.attr("src", iconCheckedSrc);
            baseInput.prop("checked", true);
        } else {
            this._checked = false;
            this._checkIcon.attr("src", iconUncheckedSrc);
            baseInput.prop("checked", false);
        }
    };
    this.set( (baseInput.prop("checked")) );
    
    baseInput.after(this._checkIcon);
    
    baseInput.change(function(e){
        selfpointer.set(baseInput.prop("checked"));
    });
    this._checkIcon.click(function(){
        selfpointer.set(!selfpointer._checked);
    });
}

function DropDown(baseInput){
    
    baseInput.hide();
    var dropDown = HTML.make("div", "dropdown", baseInput.attr("id"));
    baseInput.after(dropDown);
    
    this.selectedOption = null;
    var selfpointer = this;
    this.options = baseInput.find("option");
    
    this._button = HTML.make("button", "dropdown-toggle")
            .attr("aria-expanded", "true")
            .attr("data-toggle", "dropdown")
            .attr("aria-expanded", "true");
    this._buttonLabel = HTML.make("span", "dropdown-label");
    var buttonCaret = HTML.make("span", "caret");
    this._button
            .append(this._buttonLabel)
            .append(buttonCaret);
    
    var list = HTML.make("ul", "dropdown-menu")
            .attr("role", "menu");
    
    this._hidden = HTML.make("input")
            .attr("name", baseInput.attr("name"))
            .attr("type", "hidden");
    dropDown.append(this._button);
    dropDown.append(list);
    dropDown.append(this._hidden);
    
    this.select = function (option) {
        this.selectedOption = option;
        if(option.value){
            this._hidden.val(option.value);
        } else {
            this._hidden.val(option.label);
        }
        this._buttonLabel.html(option.label);
        var buttonLabelClass = "dropdown-toggle";
        this._button.attr("class", buttonLabelClass + " " + $(option).attr("class"));
    };
    
    var hasIcons = false;
    this.options.each(function(index, value){
        if($(value).attr("icon")){
            hasIcons = true;
        }
    });
    
    this.options.each(function(index, value){
        var option = value;
        if(option.selected){
            selfpointer.select(option);
        }
        var li = HTML.make("li")
                .attr("role", "presentation");
        if(hasIcons){
            var icon = HTML.make("img", "drop-down-icon").attr("src", $(option).attr("icon"));
            li.append(icon);
        }
        li.append(option.label);
        li.attr("class", $(option).attr("class"));
        list.append(li);
        li.click({option: option}, function(event){
            selfpointer.select(event.data.option);
        });
    });
    
    baseInput.remove();
    
}

function Dialog(config){
    
    /*
    Example Config:
    
        var config = {
            allowCloseOnBackground : false,
            type : "error",
            message : message,
            title : title || "Error",
            buttons : {
                close: {
                    label : closeLabel || "OK",
                    callback : function(){
                        alert("Clooose");
                    }
                },
                action : {
                    label : "Action",
                    callback : function(){
                        alert("Yay");
                    }
                }
            },
            textInput : {
                id : "myInput",
                textAfter : "After Input"
            },
            timeouts : {
                dialogTimeout : 3000,
                dialogFadeDuration : 300
            }
        };
    */
    
    if(!Dialog._initiated){
        Dialog._stack = HTML.make("div", "", "modal");
        Dialog._background = HTML.make("div", "modal-background");
        Dialog._stack.append(Dialog._background);
        HTML.body.prepend(Dialog._stack);
        Dialog._initiated = true;
    }
    this._header = HTML.make("div", "header");
    this._body = HTML.make("div", "body");
    this._footer = HTML.make("div", "footer");
    Dialog._dialog = HTML.make("div", "", "modal-dialog")
            .append(this._header)
            .append(this._body)
            .append(this._footer)
            .appendTo(Dialog._stack);
    this._closer = HTML.make("button", "close").html("&times;");
    this._title = HTML.make("span", "title");
    this._header.append(this._closer);
    this._header.append(this._title);
    
    
    this._close = HTML.make("button", "btn btn-primary");
    this._action = HTML.make("button", "btn btn-primary");
    this._footer.append(this._close);
    this._footer.append(this._action);
    
    
    var type = config.type;
    var message = config.message;
    var title = config.title;
    var textInput = config.textInput;
    var buttons = config.buttons;
    var timeouts = config.timeouts;
    
    var self = this;
    
    this.close = function(){
        Dialog._stack.hide();
		Dialog._stack.remove();
		Dialog._initiated = false;
    };
    if(config.allowCloseOnBackground){
        Dialog._background.click(function(){
            self.close();
        });
    }
    this._closer.click(function(){
        self.close();
    });
    
    Dialog._dialog.attr("class", type);
    //type must be "error", "warn", "info"
    
    this._title.html(title);
    this._body.html(message);
    
    if(textInput){
        this._textInput = HTML.make("input", "", textInput.id, "text");
        this._body.append(this._textInput);
        if(textInput.textAfter){
            this._body.append(textInput.textAfter);
        }
    }
    
    if(buttons){
        if(buttons.close){
            self._close.html(buttons.close.label);
            self._close.click(function(){
                if(buttons.close.callback){
                    buttons.close.callback();
                }
                self.close();
            });
            self._close.show();
        } else {
            self._close.hide();
        }
        
        if(buttons.action){
            self._action.html(buttons.action.label);
            self._action.click(function(){
                if(buttons.action.callback){
                    buttons.action.callback();
                }
                self.close();
            });
            self._action.show();
        } else {
            self._action.hide();
        }
    } else {
        self._close.hide();
        self._action.hide();
    }
    
    if(timeouts){
        var dialogTimeout = timeouts.dialogTimeout || 3000;
        var dialogFadeDuration = timeouts.dialogFadeDuration || 400;
        setTimeout(function(){
            Dialog._dialog.fadeOut(dialogFadeDuration, function(){
                Dialog._stack.hide();
            });
        }, dialogTimeout);
    }
    
    Dialog._stack.show();
}

function UserSelector(baseInput){
    
    var classes = baseInput.attr("class");
    baseInput.attr("class", "");
    
    baseInput.addClass("loading");
    
    var div = baseInput.wrap("<div class='" + classes + " userselector-wrapper'></div>");
	
    var marginTop = 7;
    
    this.sourceURL = "/json/users";
    this.usernames = new Array();
    var _selfpointer = this;
    
    var namesDisplay = HTML.make("div");
    var parameterName = baseInput.attr("name");
    baseInput.removeAttr("name");
    var hidden = HTML.make("input")
            .attr("name", parameterName)
            .attr("type", "hidden");
    baseInput.after(namesDisplay);
    baseInput.after(hidden);
    
	//baseInput.after("<div style='width:50%; display:inline-block; padding:10px; border:1px solid #fff;'></div>");
    
    var _split = function( val ) {
        return val.split( /,\s*/ );
    };
    var _extractLast = function( term ) {
        return _split( term ).pop();
    };
    
    this.removeUserName = function ( userName ){
        var tempNames = new Array();
        for(var i = 0; i < this.usernames.length; i++){
            var currentUserName = this.usernames[i];
            if(currentUserName !== userName){
                tempNames.push(currentUserName);
            }
        }
        this.usernames = tempNames;
        hidden.val(this.usernames.join(","));
        this.refreshUserNames();
    };
    
    this.addUserName = function ( userName ){
        if(this.usernames.indexOf(userName) == -1){
            this.usernames.push(userName);
        }
        this.refreshUserNames();
    };
    
    this.refreshUserNames = function (){
        
        namesDisplay.empty();
        for(var i = 0; i < this.usernames.length; i++){
            var userName = this.usernames[i];
            if(userName !== ""){
                var nameDisplay = HTML.make("div", "usernamedisplay")
                        .html(userName);
                var removeSymbol = HTML.make("span", "removesymbol")
                        .html("&times;");
                removeSymbol.click({userName: userName}, function(event){
                    _selfpointer.removeUserName(event.data.userName);
                });
                nameDisplay.append(removeSymbol);
                namesDisplay.append(nameDisplay);
            }
        }
        hidden.val(this.usernames.join(","));
        
    };
    
    $.get(this.sourceURL, function(usersJSON){
		
		var list = HTML.make("div", "userselector-wrapper-userlist");
		div.parent().after(list);
		
		var users = JSON.parse(usersJSON);
		
		var usernames = new Array();
		for(var i = 0; i < users.length; i++){
			usernames.push(users[i].name);
		}
		
		for(var i = 0; i < users.length; i++){
			var user = users[i];
			var link = HTML.make("a");
			var tag = HTML.make("div", "user-label").attr("style", "margin:5px;");
			link.append(tag);
			tag.html("+ <img class=\"user-avatar icon\" src=\"/img/avatars/" + user.avatarfile + "\"><span class=\"user-name\">" + user.name + "</span>");
			
			link.click({user: user},function(event){
				_selfpointer.addUserName(event.data.user.name);
			});
			list.append(link);
		}
        
        baseInput
            // don't navigate away from the field on tab when selecting an item
            .bind( "keydown", function( event ) {
                if ( event.keyCode === $.ui.keyCode.TAB && $( this ).autocomplete( "instance" ).menu.active ) {
                    event.preventDefault();
                }
                if ( event.keyCode === $.ui.keyCode.ENTER ) {
                    event.preventDefault();
                }
            })

            .autocomplete({
                
                position: {
                    my: "left top" + marginTop
                },

                source: usernames,

                search: function() {
                    var term = _extractLast( this.value );
                },

                focus: function() {
                    // prevent value inserted on focus
                    return false;
                },

                select: function( event, ui ) {
                    var terms = _split( this.value );

                    // remove the current input
                    terms.pop();
                    // add the selected item
                    terms.push( ui.item.value );
                    // add placeholder to get the comma-and-space at the end
                    terms.push( "" );
                    baseInput.val("");
                    _selfpointer.addUserName( ui.item.value );

                    return false;
                }
            });
        baseInput.removeClass("loading");
    });
    
    var preselectedUserNames = baseInput.val().split(",");
    for(var i = 0; i < preselectedUserNames.length; i++){
        var userName = preselectedUserNames[i];
        this.addUserName(userName);
    }
    baseInput.val("");
}


function BugReporter(element) {
    
    element.hide();
    
    var self = this;
	
	var descriptionInput = element.find("[name=description]");
	var enteredCharsField = element.find(".enteredchars");
	
	var cancelButton = element.find(".cancel");
	var sendButton = element.find(".send");
	sendButton.attr("disabled", "disabled");
    
	cancelButton.click(function(){
		element.hide();
	});
	
	descriptionInput.keyup(function(){
		var length = descriptionInput.val().length;
		if(length < 30){
			sendButton.attr("disabled", "disabled");
		} else {
			sendButton.removeAttr("disabled");
		}
		enteredCharsField.html(length);
	});
			
	this.show = function(){
		console.log("show");
		element.show();
	};
}

var dumpButton = $(".dump-data");
dumpButton.click(function(){
	if(window.Model){
		console.log("Model=");
		console.log(Model);
	} else {
		console.log("Model=undefined");
	}
	
	if(window.Controller){
		console.log("Controller.getContext()=");
		console.log(Controller.getContext());
	} else {
		console.log("Controller.getContext()=undefined");
	}
});