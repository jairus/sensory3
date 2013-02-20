/*
 * Name : Dialog Box
 * Description : Specific JS codes for this plug-in.
 * Author : Armande Bayanes (tuso@programmerspride.com)
 **/

jQuery(function() {

    xyDIALOG.init('xyDIALOG');
    
    jQuery(window).resize(function () {

        if(! jQuery('#xyDIALOGbox').is(':hidden')) {
            
            xyDIALOG.positioning();
            jQuery('#xyDIALOGoverlay, #xyDIALOGbox').show();
        }
    });    
});

var xyDIALOG = new function() {

    this.eFocus = null;
    this.go = null;
    this.func = null;
    this.icon = 'warning';

    this.init = function(argContainer) {

        if(! jQuery('#'+ argContainer).length) {

            jQuery('body').append('<div id="xyDIALOG"></div>');
        }
        
        // Dynamically create the elements.
        jQuery('#'+ argContainer).append(jQuery('<div id="xyDIALOGoverlay"><div>'));
        var html = '<div id="xyDIALOGbox"><div id="xyDIALOGcontent">';
        html += '<div id="xyDIALOGtitle"></div>';
        html += '<div id="xyDIALOGmessage"></div>';
        html += '<div id="xyDIALOGbuttons"></div>';
        html += '</div></div>';
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
        jQuery('#xyDIALOGoverlay').css({height : winHeight, width : winWidth});
        jQuery('#xyDIALOGbox').fixedCenter();//css({top : dialogTop, left : dialogLeft});
    }

    this.message = function(argTitle, argMsg, argFocus, argGo, argIcon) {

        if(argFocus) this.eFocus = argFocus;
        if(argGo) this.go = argGo;
        if(argIcon) this.icon = argIcon;

        jQuery('#xyDIALOGtitle').html(argTitle);
        jQuery('#xyDIALOGmessage').html(this.content(argMsg));
        jQuery('#xyDIALOGbuttons').html('<a title="close" href="javascript:xyDIALOG.close()" class="xyDIALOGbutton">Close</a>');
        jQuery('#xyDIALOGoverlay, #xyDIALOGbox').show();
    }

    this.confirm = function(argMsg, argFunc, argIcon, argBtn1Caption, argBtn2Caption) {

        if(argFunc) this.func = argFunc;
        if(argIcon) this.icon = argIcon;

        if(! argBtn1Caption) argBtn1Caption = 'OK';
        if(! argBtn2Caption) argBtn2Caption = 'Cancel';

        jQuery('#xyDIALOGtitle').html('CONFIRM !!!');
        jQuery('#xyDIALOGmessage').html(this.content(argMsg));

        var btn = '<a title="ok" href="javascript:xyDIALOG.ok()" class="xyDIALOGbutton">'+ argBtn1Caption +'</a>&nbsp;';
        btn += '<a title="cancel" href="javascript:xyDIALOG.close()" class="xyDIALOGbutton">'+ argBtn2Caption +'</a>';
        
        jQuery('#xyDIALOGbuttons').html(btn);
        jQuery('#xyDIALOGoverlay, #xyDIALOGbox').show();
    }

    this.content = function(argMsg) {

        var html = '<div title="'+ this.icon +'"><table width="100%" cellpadding="0" cellspacing="0"><tr><td width="48"><div class="xyDIALOGicon-image xyDIALOGicon-'+ this.icon +'"></div></td>';
        html += '<td style="padding-left:15px" height="70"><div style="padding:5px">'+ argMsg +'</div></td></tr></table></div>';

        return html;
    }

    this.ok = function() {

        this.close();
        if(this.func) {

            try {

                eval(this.func +";");
                this.func = null;
                
            } catch(e) {alert('The function "'+ this.func +'" is '+ e.error +' !');}
        }
    }

    this.close = function() {
        jQuery('#xyDIALOGoverlay, #xyDIALOGbox').hide();
        
        if(this.eFocus != null) {
            
            if(this.eFocus != '[object Object]') {
                this.eFocus = jQuery(this.eFocus);
            }
            this.eFocus.focus();
            this.eFocus = null;
        }
        
        if(this.go != null) {
            window.location.href = this.go;
            this.go = null;
        }

        this.icon = 'warning';
    }
}