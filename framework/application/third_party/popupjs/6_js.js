Popup.BorderImage            = DOCROOT + PLUGIN_PATH['popupjs'] +'images/popup_border_background.png';
Popup.BorderTopLeftImage     = DOCROOT + PLUGIN_PATH['popupjs'] +'images/popup_border_top_left.png';
Popup.BorderTopRightImage    = DOCROOT + PLUGIN_PATH['popupjs'] +'images/popup_border_top_right.png';
Popup.BorderBottomLeftImage  = DOCROOT + PLUGIN_PATH['popupjs'] +'images/popup_border_bottom_left.png';
Popup.BorderBottomRightImage = DOCROOT + PLUGIN_PATH['popupjs'] +'images/popup_border_bottom_right.png';

// Every popup should be draggable by the tilebar
Popup.Draggable = true;

// Focus automatically on first control in popup
Popup.AutoFocus = true; // here to document feature; true by default

// Add trigger behavior for links with a class of "popup".
Event.addBehavior({
    'a.popup': Popup.TriggerBehavior({
        reload: true     // reload Ajax popups on second click
    })
});

var POPUPJS = new function() {
    
    this.obj = null;
    this.overlay_show = function() {
        
        var winH = jQuery(window).height();
        var winHeight = jQuery(document).height();
        if(winH > winHeight) winHeight = winH;

        var winWidth = jQuery(window).width();

        jQuery('body').append('<div id="popupJSOverlay"></div>');
        jQuery('#popupJSOverlay').attr('style', 'width: 100%; height: 100%; z-index: 9000; position: absolute; top: 0; left: 0; background-color: #999; filter: alpha(opacity=50); -moz-opacity: 0.5; -khtml-opacity: 0.5; opacity: 0.5');
        jQuery('#popupJSOverlay').css({ height : winHeight, width : winWidth }).show();
        
        jQuery('#popupjs_btn_ok').show();        
        jQuery('.popup .popup_content p').css('min-height', '50px');
    }
    
    this.overlay_hide = function() {
        
        jQuery('#popupJSOverlay').hide();        
        jQuery('#popupjs_btn_ok').removeAttr('onclick');
    }
}