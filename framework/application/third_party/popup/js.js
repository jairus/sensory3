/*
 * Name : PopUp Box
 * Description : Specific JS codes for this plug-in.
 * Author : Armande Bayanes (tuso@programmerspride.com)
 **/

jQuery(function() {

    xyPOPUP.init('xyPOPUP');
    
    jQuery(window).resize(function () {

        if(! jQuery('#xyPOPUPbox').is(':hidden')) {
            
            xyPOPUP.positioning();
            jQuery('#xyPOPUPoverlay, #xyPOPUPbox').show();
        }
    });
    
    jQuery('#xyPOPUPoverlay').dblclick(function(){
        xyPOPUP.close();
    });
});

var xyPOPUP = new function() {

    this.init = function(argContainer) {

        if(! jQuery('#'+ argContainer).length) {

            jQuery('body').append('<div id="xyPOPUP"></div>');
        }
        
        /* Dynamically create the elements. */
        jQuery('#'+ argContainer).append(jQuery('<div id="xyPOPUPoverlay"><div>'));
        var html = '<div id="xyPOPUPbox"><div id="xyPOPUPcontent">haller</div></div>';
        jQuery('#'+ argContainer).append(jQuery(html));

        this.positioning();
    }

    this.positioning = function() {

       /*
        * Use window instead of document so we can toggle when
        * window is minimized or maximized
        */

        var winH = jQuery(window).height();
        var winHeight = jQuery(document).height();
        if(winH > winHeight) winHeight = winH;

        var winWidth = jQuery(window).width();

        /* Assign the values. */
        jQuery('#xyPOPUPoverlay').css({height : winHeight, width : winWidth});
        jQuery('#xyPOPUPbox').fixedCenter();//css({top : dialogTop, left : dialogLeft});
    }
    
    this.show = function(message) {
        
        jQuery('#xyPOPUPcontent').html(message);
        xyPOPUP.positioning();
        jQuery('#xyPOPUPoverlay, #xyPOPUPbox').show();
    }
    
    this.close = function() {
        
        jQuery('#xyPOPUPoverlay, #xyPOPUPbox').hide();        
    }
}