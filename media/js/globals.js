/* Author: Armande Bayanes (tuso@programmerspride.com)
 * Description: Define the globally accessible JS methods.
 **/

/* Add trim prototype for the String object. 
 * i.e. myString.trim()
 **/
if(typeof String.prototype.trim !== 'function') {
  String.prototype.trim = function() {
    return this.replace(/^\s\s*/, '').replace(/\s\s*$/, ''); //replace(/^\s+|\s+$/g, ''); 
  }
}

Object.size = function(obj) {
    
    var size = 0, key;
    for(key in obj) {
        if (obj.hasOwnProperty(key)) size++;
    }
    
    return size;
};

var GBL = new function() {

    this.numOnly = function(e) {
        var key;
        var keychar;
        
        if(window.event) {key = window.event.keyCode;}
        else
        if(e) {key = e.which;}
        else { return true; }
        
        var numpad = [], y = 0;        
        for(var x=96; x<=105; x++) { numpad[x] = y; y++; }
        
        if(key >= 96 && key <= 105) keychar = numpad[key].toString();
        else keychar = String.fromCharCode(key);
        
        /* Control keys. */
        if((key == null) ||
           (key == 0) ||
           (key == 8) ||
           (key == 9) ||
           (key == 13) ||
           (key == 27) ||
           (key == 46)) {
           return true;
        }
        else
        if((("0123456789").indexOf(keychar) > -1)) { return true; }
        else { return false; }
    }

    this.countChars = function(obj, disp, max) {

        if(obj.val().length > max){
            obj.val(obj.val().substr(0, max));
        }

        var xy = (max - obj.val().length);
        if(disp.is(':input') == false) disp.html(xy);
        else disp.val(xy);
    }
    
    this.toggle_process = function(obj) {
        
        var processor = jQuery('#processor');
        if(processor && processor.length) {
            
            processor.remove();
            
        } else {
            
            obj = jQuery(obj);
            var loader = '<div id="processor" style="position: absolute; background: #FFF; z-index: 99; height: 50px; width: '+ (obj.width() + 50) +'px; left: '+ obj.position().left +'px; top: '+ obj.position().top +'px"><div class="fltLf"><img src="'+ blankGIF +'" class="loader" /></div> <div class="fltLf" style="padding-left: 5px; cursor: default">processing ...</div><div class="clear"></div></div>';        
            jQuery('body').append(loader);
        }
    }
    
    /* Used at register.php */
    this.toggle_other_field = function(condition, wrapper, field) {
        
        /* condition = The Field Object where condition will be taken from.
         * wrapper = Wrapper ID of Field to show.
         * field = The ID of "other" Field itself.
         * */
        
        wrapper = jQuery('#'+ wrapper);
        
        if(jQuery(condition).val() == 'other') {
            
            wrapper.show();
            jQuery('#'+ field).focus();
            
        } else wrapper.hide();
    }
    
    this.blank = function(obj) {
        
        var blank = jQuery.trim(obj.val()).length;
        
        if(blank == 0) return true;
        else return false;
    }
    
    this.get_keypressed = function(e) {
        
        /* Cross-browser "event object" detection.
         * IE -> window.event
         * FF -> event
         * */
        
        var eventObj = (window.event ? event : e);
        var key = (eventObj.charCode ? eventObj.charCode : eventObj.keyCode);
        
        return key; /* String.fromCharCode(key). */
    }
    
    this.loader = function(flag) {
        
        if(flag == undefined) flag = true;
        
        if(flag) {
            
            var loader = '<div id="gbl_loader"><img src="'+ DOCROOT +'media/images/loader.gif" /> Loading ...</div>';
            
            jQuery('body').append(loader);

            jQuery('#gbl_loader').
                css('padding', '20px').
                css('background', '#FFF').
                css('border', '2px solid #333').
                css('position', 'absolute').
                css('font', 'bold 24px Verdana').
                css('color', '#333').
                css('z-index', '999999').
                centerElement();
            
        } else jQuery('#gbl_loader').remove();        
    }
    
    this.rank_label = function(digit) {
        
        var len = digit.toString().length, response = '';

        if(len > 1) {
            
            if(digit > 19) {
                
                var tmp = parseInt(digit.toString().charAt(len - 1), 10);
                if(tmp == 1 || tmp == 2 || tmp == 3) { /* 1, 2, & 3 only. */

                    response = digit.toString().substring(0, (len - 1)) + this.rank_label(tmp);

                } else response = digit +'th';
                
            } else response = digit +'th';

        } else {

            if(digit < 4) {

                if(digit == 1) response = digit +'st';
                else if(digit == 2) response = digit +'nd';
                else if(digit == 3) response = digit +'rd';

            } else response = digit +'th';
        }

        return response;
    }
    
    this.go = function(url) { window.location.href = DOCROOT + url; }
    this.makeid = function(str) { 
        
        if(str) { str = str.replace(/[^a-zA-Z 0-9\_]+/g, '').replace(/ /g, '_').toLowerCase(); }
        return str;
    }
    
    this.ucwords = function(str) { str = str.replace(/_/g, ' '); return (str + '').replace(/^([a-z\u00E0-\u00FC])|\s+([a-z\u00E0-\u00FC])/g, function ($1) { return $1.toUpperCase(); } ); }
}

/* Extend jQuery, add the function named "fixedCenter".
 * 
 * Sample usage:
 * 
 *      1.  $('#element_id').fixedCenter()
 *      2.  jQuery('#element_id').fixedCenter()
 **/
jQuery.fn.fixedCenter = function(){
    
    return this.each(function(){
        
        var element = jQuery(this), win = jQuery(window);
        centerElement();

        jQuery(window).bind('resize', function() {
            centerElement();
        });

        function centerElement(){
            
            var elementWidth, elementHeight, windowWidth, windowHeight, X2, Y2;
            elementWidth = element.outerWidth();
            elementHeight = element.outerHeight();
            windowWidth = win.width();
            windowHeight = win.height();
            
            X2 = (windowWidth/2 - elementWidth/2) + "px";
            Y2 = ((windowHeight/2) - (elementHeight/2))+ "px";
            
            jQuery(element).css({
                'left'      : X2,
                'top'       : Y2,
                'position'  : 'fixed'
            });
        }
    });
}

jQuery.fn.centerElement = function () {
    
    this.css("position", "absolute");
    this.css("top", ((jQuery(window).height() - this.outerHeight()) / 2) + jQuery(window).scrollTop() + "px");
    this.css("left", ((jQuery(window).width() - this.outerWidth()) / 2) + jQuery(window).scrollLeft() + 35 + "px");
    
    return this;
}

jQuery(function(){
    
    //jQuery(".tooltip").mouseout(function(){
    //    jQuery('#tooltip').hide();
    //});
    
    jQuery('label').disableSelection();    
    jQuery('a[title], img[title], input').each(function(){ if(jQuery(this).attr('title') && jQuery(this).attr('title').length) { jQuery(this).qtip({ style: { name: 'cream', tip: true } }); } });
    
    /* START: Make sub-menu's width follow the wrapper's width. */
    var parent = jQuery('#xyNAV').parents('div');
    jQuery('#xyNAV li ul').css('width', parent.width() - 15 +'px');
    /* END: Make sub-menu's width follow the wrapper's width. */
    
    jQuery('#login_wrapper, #register_wrapper').centerElement().css('top', '70px');
    
    /*jQuery('#login_wrapper input').bind('keyup keydown', function(){
        
        var url = window.location.href.split('/');
        var un = 'username';
        if(url[url.length - 2] == 'employee') un = 'employee #';
        
        jQuery('#login_icon_wrapper span').css('color', '#006600').html('Please sign-in first with your <b>'+ un +'</b> and <b>password</b>.');
        jQuery('#login_icon_wrapper').attr('class', 'login_icon_wrapper_green');
        jQuery('#login_icon').removeClass('login_icon_red').addClass('login_icon_green');
    });*/
});

/* Author: James Padolsey
 * http://james.padolsey.com/javascript/regex-selector-for-jquery/
 **/
jQuery.expr[':'].regex = function(elem, index, match) {
    var matchParams = match[3].split(','),
        validLabels = /^(data|css):/,
        attr = {
            method: matchParams[0].match(validLabels) ? 
                        matchParams[0].split(':')[0] : 'attr',
            property: matchParams.shift().replace(validLabels,'')
        },
        regexFlags = 'ig',
        regex = new RegExp(matchParams.join('').replace(/^\s+|\s+$/g,''), regexFlags);
    return regex.test(jQuery(elem)[attr.method](attr.property));
}