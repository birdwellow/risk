
$(document).ready(function(){
    if(typeof userDialog !== "undefined"){
        var dialog = new Dialog(userDialog);
    }
    $("#theme-select").change(function(e){
        $("#css-theme").attr("href", "/css/theme_" + $("#theme-select").val() + ".css");
    });
    setTimeout(function(){
        $(".alert-success").hide("blind", {}, 500);
        /*$(".alert-success").fadeOut(500, function(){
            $(".alert-success").hide();
        });*/
    }, 3000);
    
    
    function split( val ) {
        return val.split( /,\s*/ );
    }
    function extractLast( term ) {
        return split( term ).pop();
    }
    
    $( "#invitation_helper" )
        // don't navigate away from the field on tab when selecting an item
        .bind( "keydown", function( event ) {
            if ( event.keyCode === $.ui.keyCode.TAB && $( this ).autocomplete( "instance" ).menu.active ) {
                event.preventDefault();
            }
        })
        
        .autocomplete({
            source: function( request, response ) {
                $.getJSON( "/json/users/names", {
                    selectednames: $("#invited_players").val(),
                    term: extractLast( request.term )
                }, response );
            },
            
            search: function() {
                // custom minLength
                var term = extractLast( this.value );
                if ( term.length < 2 ) {
                    return false;
                }
            },
            
            focus: function() {
                // prevent value inserted on focus
                return false;
            },
            
            select: function( event, ui ) {
                var terms = split( this.value );
                
                // remove the current input
                terms.pop();
                // add the selected item
                terms.push( ui.item.value );
                // add placeholder to get the comma-and-space at the end
                terms.push( "" );
                var invitedPlayers = $( "#invited_players").val();
                invitedPlayers += terms.join( ", " );
                $( "#invited_players").val(invitedPlayers);
                $( "#invitation_helper").val("");
                return false;
            }
        });
});

function Dialog(config){
    var type = config.type;
    var message = config.message;
    var title = config.title;
    var buttons = config.buttons;
    var timeouts = config.timeouts;
    
    this._stack = $("#modal");
    this._background = $("#modal-background");
    this._closer = $("#modal-closer");
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
    this._closer.click(function(){
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