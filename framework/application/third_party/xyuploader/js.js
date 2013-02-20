jQuery(function(){
    
    var version = deconcept.SWFObjectUtil.getPlayerVersion();
    var so = null;
    var browse_path = PLUGIN_PATH['xyuploader'] +'swf';
    
    if(jQuery.trim(jQuery('#xy_uploader_statistics').html()) == '') {
        jQuery('#xy_uploader_statistics').hide();
    }
    
    if(document.getElementById && version['major'] < 10) {

        /* Allow option to upgrade the client's FLASH Player. */
        so = new SWFObject(browse_path +".swf", "", "600", "300", "10", "#FFFFFF");
        so.useExpressInstall('plugin/expressinstall.swf');

    } else {

        so = new SWFObject(browse_path +".swf", "browse", 58, 21, 10, '#8A8A8A');
        so.addParam("wmode", "opaque"); /* Make it NOT to overlap any element. */        
        so.addVariable("file_size_max", 5);
        
        /* The script URL that will execute the uploading process. Relative to the SWF file. */
        //so.addVariable("file_upload_script", 'upload.php?userid='+ USERID +'&docroot'+ DOC_ROOT);
        
        //DOCROOT +'group/async_upload_photo/?id='+ USERID +'|'+ GROUP_ID +'|'+ (new Date).getTime()
        so.addVariable("file_upload_script", PLUGIN_PATH['xyuploader_target_url']);
    }

    so.write("xy_uploader_browse_button");
    xyUPLOADER.docroot = DOCROOT;
    /*
    var img = '<div style="background: url('+ xyUPLOADER.docroot +'profile/loadProfilePicture/?scale=30&t='+ (new Date).getTime() +') top left no-repeat; width: 30px; height: 30px"></div>';//img = '<img src="'+ PLUGIN_PATH['xyuploader'] +'scifly.jpg" />';
    jQuery('#xy_uploader_preview').html(img);*/
    
});

var xyUPLOADER = new function() {
    
    this.item = 0;
    this.message = null;
    this.docroot = null;
    this.enqueue = function(filename, size, accept) {
        
        filename = unescape(filename); /* Unescape since it was escaped by ActionScript. */

        if(accept == false) {
            
            alert('File is too large. Please resize or select another image.');
            return;
        }
        
        var wlimit = 25;
        var tmp = filename;
        if(filename.length > wlimit) {

            tmp = tmp.substr(0, (wlimit - 3)) +"...";
        }
        
        this.item = 1;
        jQuery('#xy_uploader_loader').css('width', '0px');
        jQuery('#xy_uploader_statistics').html(tmp +' <b><span>0</span>/'+ size +'</b> <sup>0% OK</sup>');
        jQuery('#xy_uploader_statistics').show();
    }
    
    this.enqueing = function(state) {
        
        jQuery('#xy_uploader_statistics').html(((state == 'open') ? 'Loading your photo ...' : ''));
        if(state == 'open') jQuery('#xy_uploader_statistics').show();
        else {
            
            this.item = 0;
            jQuery('#xy_uploader_statistics').html('Browse a Photo from your Computer.');
        }
    }
    
    this.progress = function(p, loaded) {

        jQuery('#xy_uploader_statistics span').html(loaded);
        jQuery('#xy_uploader_statistics sup').html(p +"% OK");
        jQuery('#xy_uploader_loader').css('width', parseInt(p, 10) +'px');
    }

    this.upload = function() {
        
        if(this.item <= 0) return;
        
        /* Send cancelled file to Flash. */
        this.getFlashMovie("browse").upload();
    }
    
    this.finished = function(response) {
        
        response = unescape(response);
        this.item = 0;
        
        var tmp = response.split('[=AXL=]');
        
        if(tmp[2] == 'group') {
            
            /* 0=> User Id, 1=> Group Id, 2 => Filename */
            window.location.href = DOCROOT +'group/ae/?id='+ tmp[0] +'&step=3&p='+ tmp[1];
        }
        else
        if(tmp[2] == 'wall') {
            
            jQuery.post(
                DOCROOT +'helper/async_photo_content',
                {
                    user_id : tmp[0],
                    wall_post_id : tmp[1],
                    content : jQuery('#xyWALLPOST').html(),
                    t : (new Date).getTime()
                },
                function(r) {
                    jQuery('#activity_wrapper').prepend(r).show();
                }
            );
        }        
    }
    
    this.getFlashMovie = function(movieName) {
        var isIE = navigator.appName.indexOf("Microsoft") != -1;

        return (isIE) ? window[movieName] : document[movieName];
    }

    this.debug = function(message) {
        
        var debugger_message = jQuery('#xy_debugger');
        if(! debugger_message || ! debugger_message.length) { jQuery('body').append('<div id="xy_debugger"></div>'); }        
        debugger_message.attr('style', ' background: #000; color: #FFF');
        jQuery('#xy_debugger').html(message);
    }
}