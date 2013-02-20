jQuery(function(){    
    jQuery('textarea[name=bbcode]').bbcodeeditor({
        bold:jQuery('.bold'),
        italic:jQuery('.italic'),
        underline:jQuery('.underline'),
        link:jQuery('.link'),
        quote:jQuery('.quote'),
        code:jQuery('.code'),
        image:jQuery('.image'),
        usize:jQuery('.usize'),
        dsize:jQuery('.dsize'),
        nlist:jQuery('.nlist'),
        blist:jQuery('.blist'),
        litem:jQuery('.litem'),
        back:jQuery('.back'),
        forward:jQuery('.forward'),
        back_disable:'btn back_disable',
        forward_disable:'btn forward_disable',
        tcolor:jQuery('.tcolor'),
        exit_warning:false,
        preview:jQuery('.preview')
    });

    jQuery("#bbcode_bold").mouseover(function(){ btn_toggle(jQuery(this), 'over', 'bold'); });
    jQuery("#bbcode_bold").mouseout(function(){ btn_toggle(jQuery(this), 'out', 'bold'); });
    jQuery("#bbcode_italic").mouseover(function(){ btn_toggle(jQuery(this), 'over', 'italic'); });
    jQuery("#bbcode_italic").mouseout(function(){ btn_toggle(jQuery(this), 'out', 'italic'); });
    jQuery("#bbcode_underline").mouseover(function(){ btn_toggle(jQuery(this), 'over', 'underline'); });
    jQuery("#bbcode_underline").mouseout(function(){ btn_toggle(jQuery(this), 'out', 'underline'); });
    jQuery("#bbcode_link").mouseover(function(){ btn_toggle(jQuery(this), 'over', 'link'); });
    jQuery("#bbcode_link").mouseout(function(){ btn_toggle(jQuery(this), 'out', 'link'); });
    jQuery("#bbcode_quote").mouseover(function(){ btn_toggle(jQuery(this), 'over', 'quote'); });
    jQuery("#bbcode_quote").mouseout(function(){ btn_toggle(jQuery(this), 'out', 'quote'); });
    jQuery("#bbcode_code").mouseover(function(){ btn_toggle(jQuery(this), 'over', 'code'); });
    jQuery("#bbcode_code").mouseout(function(){ btn_toggle(jQuery(this), 'out', 'code'); });
    jQuery("#bbcode_image").mouseover(function(){ btn_toggle(jQuery(this), 'over', 'image'); });
    jQuery("#bbcode_image").mouseout(function(){ btn_toggle(jQuery(this), 'out', 'image'); });
    jQuery("#bbcode_usize").mouseover(function(){ btn_toggle(jQuery(this), 'over', 'usize'); });
    jQuery("#bbcode_usize").mouseout(function(){ btn_toggle(jQuery(this), 'out', 'usize'); });
    jQuery("#bbcode_dsize").mouseover(function(){ btn_toggle(jQuery(this), 'over', 'dsize'); });
    jQuery("#bbcode_dsize").mouseout(function(){ btn_toggle(jQuery(this), 'out', 'dsize'); });
    jQuery("#bbcode_nlist").mouseover(function(){ btn_toggle(jQuery(this), 'over', 'nlist'); });
    jQuery("#bbcode_nlist").mouseout(function(){ btn_toggle(jQuery(this), 'out', 'nlist'); });
    jQuery("#bbcode_blist").mouseover(function(){ btn_toggle(jQuery(this), 'over', 'blist'); });
    jQuery("#bbcode_blist").mouseout(function(){ btn_toggle(jQuery(this), 'out', 'blist'); });
    jQuery("#bbcode_litem").mouseover(function(){ btn_toggle(jQuery(this), 'over', 'litem'); });
    jQuery("#bbcode_litem").mouseout(function(){ btn_toggle(jQuery(this), 'out', 'litem'); });
    jQuery("#bbcode_back").mouseover(function(){ btn_toggle(jQuery(this), 'over', 'back'); });
    jQuery("#bbcode_back").mouseout(function(){ btn_toggle(jQuery(this), 'out', 'back'); });
    jQuery("#bbcode_forward").mouseover(function(){ btn_toggle(jQuery(this), 'over', 'forward'); });
    jQuery("#bbcode_forward").mouseout(function(){ btn_toggle(jQuery(this), 'out', 'forward'); });
    jQuery("#bbcode_tcolor").mouseover(function(){ btn_toggle(jQuery(this), 'over', 'tcolor'); });
    jQuery("#bbcode_tcolor").mouseout(function(){ btn_toggle(jQuery(this), 'out', 'tcolor'); });
});

function btn_toggle(obj, argToggle, argClass) {

    if(argToggle == "over") {
        obj.removeClass(argClass);
        obj.addClass(argClass +'_');
    }
    else
    if(argToggle == "out") {
        obj.removeClass(argClass +'_');
        obj.addClass(argClass);
    }
}