
$(document).ready(function(){
    if(typeof userDialog !== "undefined"){
        var dialog = new Dialog(userDialog);
    }
    $("#theme-select").change(function(e){
        $("#css-app").attr("href", "/css/" + $("#theme-select").val() + "/app.css");
        $("#css-additional").attr("href", "/css/" + $("#theme-select").val() + "/additional.css");
    });
    setTimeout(function(){
        $(".alert-success").fadeOut(500, function(){
            $(".alert-success").hide();
            });
        }, 3000);
});

function Dialog(config){
    var type = config.type;
    var message = config.message;
    var title = config.title;
    var buttons = config.buttons;
    var timeouts = config.timeouts;
    
    this._stack = $("#modal");
    this._background = $("#modal-background");
    this._close = $("#modal-dialog-close");
    this._action = $("#modal-dialog-action");
    this._dialog = $("#modal-dialog");
    
    var self = this;
    
    this.close = function(){
        this._stack.hide();
    };
    this._background.click(function(){
        self.close();
    });
    
    this._dialog.attr("class", type);
    //type must be "error", "warning", "info"
    
    $("#modal-dialog-title").html(title);
    $("#modal-dialog-body").html(message);
    
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
            self._dialog.fadeOut(dialogFadeDuration, function(){
                self._stack.hide();
            });
        }, dialogTimeout);
    }
    
    this._stack.show();
}