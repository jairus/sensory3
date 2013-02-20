jQuery(function(){
        
    jQuery('#attr_to_test_tr').hide();

    jQuery('#preferred_date').datepicker({
        inline: true
    }).change(function(){
        var freq = jQuery('#frequency');

        if(freq.val() != '' && freq.val() != 'other' && freq.val() != '2m') {
            freq.trigger('change');
        }
    });

    jQuery('#analytical_wrapper').hide();
    jQuery('#affective_wrapper').hide();

    jQuery('#specifics_wrapper').css('height', '26px');

    jQuery('#sbu_other').bind('keydown keyup paste', function(){

        var item = -1;

        if(SBU.length) {

            var str = jQuery.trim(jQuery(this).val().toLowerCase());

            for(var x=0; x<SBU.length; x++) {

                var tmp = SBU[x].toLowerCase();
                if(tmp.indexOf(str) > -1) {

                    item = x;
                    break;
                }
            }

            /* Must select the last option item ("Others") when no result. */
            if(item == -1 || str == '') {item = (jQuery('#sbu option').length - 1);}
            jQuery('#sbu')[0].selectedIndex = item;
        }
    });

    jQuery('#spec1_other, #spec2_other').bind('keydown keyup paste', function(){

        var item = -1;
        var tot = jQuery('#type_of_test').val();
        var subj = [];
        var this_obj_id = jQuery(this).attr('id');
        var spec_num = (this_obj_id == 'spec1_other') ? 1 : 2;

        if(tot == 'micro' || tot == 'physico_chem') return; /* Not applicable for MICRO and PHYSICO_CHEM. */

        if(tot == 'affective') {

            if(this_obj_id == 'spec1_other') subj = SPEC1_AFFECTIVE;
            else if(this_obj_id == 'spec2_other') subj = SPEC2_AFFECTIVE;
        }
        else if(tot == 'analytical') {

            if(this_obj_id == 'spec1_other') subj = SPEC1_ANALYTICAL;
            else if(this_obj_id == 'spec2_other') {

                return;
            }
        }

        if(subj.length) {

            var str = jQuery.trim(jQuery(this).val().toLowerCase());

            for(var x=0; x<subj.length; x++) {

                var tmp = subj[x].toLowerCase();
                if(tmp.indexOf(str) > -1) {

                    item = x;
                    break;
                }
            }                
        }

        /* Must select the last option item ("Others") when no result. */
        if(item == -1 || str == '') {item = (jQuery('#spec'+ spec_num +'_'+ tot +' option').length - 1);}

        jQuery('#spec'+ spec_num +'_'+ tot)[0].selectedIndex = item;            
    });

    jQuery('#other_schedule_wrapper').hide();
    jQuery('#calculated_schedule').hide();

    jQuery('#frequency').change(function(){

        var frequency = jQuery(this).val();

        if(frequency != '' && frequency != 'other' && frequency != '2m') {
            jQuery('#other_schedule_wrapper').hide();

            jQuery.post(
                DOCROOT +'rta/async_calculate_schedule',
                {   
                    pdate : jQuery('#preferred_date').val(),
                    nof_testing_dates : jQuery('#nof_testing_dates').val(),
                    frequency : jQuery('#frequency').val(),
                    other_schedule : jQuery('#other_schedule').val(),
                    t : (new Date).getTime()
                },
                function(r){

                    RTA_FORM.calculated_schedule = r.schedule;
                    jQuery('#calculated_schedule').html('<b>Schedule:</b> '+ r.schedule +'.');
                    jQuery('#calculated_schedule').show();
                }, 'json'
            );
        } else {

            if(frequency != '') {

                jQuery('#calculated_schedule').hide();
                jQuery('#other_schedule_wrapper').show();
                jQuery('#other_schedule').focus();

            } else {

                jQuery('#calculated_schedule').hide();
                jQuery('#other_schedule_wrapper').hide();
            }
        }
    });

    jQuery('#step2').hide();

    jQuery('#nof_testing_dates').keyup(function(){
        
        if(jQuery('#preferred_date').val() == '') {
            
            Popup.dialog({
                title : 'ERROR',
                message : 'Preferred date is required in this field.',
                buttons: ['Okay', 'Cancel'],
                buttonClick : function() { jQuery('#preferred_date').focus(); },
                width: '420px'
            });
            
            return;
        }
        
        var nof_testing_dates = jQuery(this).val();
        var field = jQuery('#frequency_wrapper');
        
        if(nof_testing_dates > 1) {
            
            field.show();
            
            var freq = jQuery('#frequency');

            if(freq.val() != '' && freq.val() != 'other' && freq.val() != '2m') {
                freq.trigger('change');
            }
            
        } else {

            field.hide();
            jQuery('#other_schedule_wrapper').hide();
            jQuery('#frequency')[0].selectedIndex = 0;            
            jQuery('#calculated_schedule').html('').hide();
            RTA_FORM.calculated_schedule = '';
        }
    });

    RTA_FORM.toggle_type_of_test(jQuery('#type_of_test'));

    if(jQuery('#nof_testing_dates').val() > 1) {

        jQuery('#nof_testing_dates').trigger('keyup');
    }

    /* START: Put initial value of the product data table. */
    if(RTA_FORM.product_data) {

        var html = '';
        for(x=1; x<=RTA_FORM.product_data.length; x++) {

            html += '<tr id="product_data_tr'+ x +'"><td><div>&nbsp;&nbsp;&nbsp;<b>'+ x +'</b>&nbsp;&nbsp;&nbsp;<input id="product_data_attr'+ x +'" type="text" value="'+ RTA_FORM.product_data[x - 1].variables +'" /></div></td>';
            html += '<td><div><input id="product_data_code'+ x +'" type="text" style="width: 70px" value="'+ RTA_FORM.product_data[x - 1].code +'" /></div></td>';
            html += '<td><div><input id="product_data_pd'+ x +'" type="text" style="width: 80px" readonly="readonly" value="'+ RTA_FORM.product_data[x - 1].pd +'" /></div></td>';
            html += '<td><div><input id="product_data_cu'+ x +'" type="text" style="width: 80px" readonly="readonly" value="'+ RTA_FORM.product_data[x - 1].cu +'" /></div></td>';
            html += '<td><div><input id="product_data_supplier'+ x +'" type="text" value="'+ RTA_FORM.product_data[x - 1].supplier +'" /></div></td>';
            html += '<td><div><input id="product_data_batch_weight'+ x +'" type="text" style="width: 70px" value="'+ RTA_FORM.product_data[x - 1].batch_weight +'" /></div></td>';
            html += '<td><div><input id="product_data_qty'+ x +'" type="text" style="width: 70px" value="'+ RTA_FORM.product_data[x - 1].quantity +'" /></div></td>';
            html += '<td><div><input id="product_data_others'+ x +'" type="text" style="width: 70px" value="'+ RTA_FORM.product_data[x - 1].others +'" /></div></td>';
            html += '</tr>';
        }

        if(html != '') {

            jQuery('#table_data').append(html);
            for(x=1; x<=RTA_FORM.product_data.length; x++) {

                jQuery('#product_data_pd'+ x +', #product_data_cu'+ x).datepicker({
                    inline: true
                });
            }
        }
    }
    /* END: Put initial value of the product data table. */
    
    jQuery('#no_of_samples').trigger('keyup');

});

var RTA_FORM = new function() {
    
    this.calculated_schedule = null;
    this.product_data = null;
    this.target = null;
    
    this.spec1 = '';
    this.spec2 = '';
    this.spec1_other = '';
    this.spec2_other = '';
    
    this.toggle_type_of_test = function(obj) {
        
        obj = jQuery(obj);
        
        jQuery("div[id^='spec1_']").hide();
        jQuery("div[id^='spec2_']").hide();
        
        if(obj.val() != '') {
            
            jQuery('#spec1_'+ obj.val() +'_wrapper').show();
            jQuery('#spec2_'+ obj.val() +'_wrapper').show();
        }

        if(obj.val() == 'micro' || obj.val() == 'physico_chem') {
            
            jQuery('#spec1_other_wrapper').show();
            jQuery('#spec1_other_label').show();
            jQuery('#spec1_other_tip').show();            
            jQuery('#spec1_other_tip').attr('alt', 'You can enter multiple items from this Field. Just separate them with comma (<b>,</b>).<br/><br/>i.e. <b>Item 1, Item 2</b>.');
            
            if(jQuery('#spec2_'+ obj.val() +'_wrapper').html() != 'Not Applicable.') {
                
                jQuery('#spec2_other_wrapper').show();
                jQuery('#spec2_other_label').show();
                jQuery('#spec2_other_tip').show();            
                jQuery('#spec2_other_tip').attr('alt', 'You can enter multiple items from this Field. Just separate them with comma (<b>,</b>).<br/><br/>i.e. <b>Item 1, Item 2</b>.');
            }
        } else {
            
            jQuery('#spec1_other_label').hide();
            jQuery('#spec1_other_tip').hide();
            
            jQuery('#spec2_other_label').hide();
            jQuery('#spec2_other_tip').hide();
        }
        
        if(obj.val() == 'affective') jQuery('#attr_to_test_tr').show();
        else jQuery('#attr_to_test_tr').hide();
    }
    
    this.toggle_tpurpose_all = function(obj) {
        
        obj = jQuery(obj);
        jQuery('input[name=tpurpose]').each(function(){
            jQuery(this).attr('checked', obj.is(':checked'));
        });
    }
    
    this.specifics_checker = function(tot) {
        
        var tmp = '', spec1_flag = false, spec2_flag = false;
        
        /* START: Reset fields. */
        this.spec1 = '';
        this.spec2 = '';
        this.spec1_other = '';
        this.spec2_other = '';
        /* END: Reset fields. */
        
        if(tot == 'micro' || tot == 'physico_chem') {

            tmp = [];
            jQuery('input[name=spec1_'+ tot +']:checked').each(function(){
                tmp.push(jQuery(this).val());
            });
            
            if(tmp.length > 0) this.spec1 = tmp.toString();            
            spec1_flag = (this.spec1 == '' && GBL.blank(jQuery('#spec1_other')));
            
        } else {
            
            this.spec1 = jQuery('#spec1_'+ tot).val().trim();
            spec1_flag = (this.spec1 == '');
        }

        if(spec1_flag) {
            
            Popup.dialog({
                title : 'OOOPS !!! BLANK',
                message : '"Specifics <b>#1</b>" is a <b>required</b> field.',
                buttons: ['Okay', 'Cancel'],
                width: '420px'
            });
            
            return false;
        }

        if(this.spec1 == 'other' && GBL.blank(jQuery('#spec1_other'))) {
            
            Popup.dialog({
                title : 'OOOPS !!! BLANK',
                message : 'The "<b>other</b>" field is <b>required</b> for "Specifics <b>#1</b>".',
                buttons: ['Okay', 'Cancel'],
                buttonClick : function() { jQuery('#spec1_other').focus(); },
                width: '420px'
            });
            
            return false;

        } else this.spec1_other = jQuery('#spec1_other').val().trim();
        
        var wrapper = jQuery('#spec2_'+ tot +'_wrapper').html();        
        if(wrapper != 'Not Applicable.') {
            
            if(tot == 'micro' || tot == 'physico_chem') {

                tmp = [];
                jQuery('input[name=spec2_'+ tot +']:checked').each(function(){
                    tmp.push(jQuery(this).val());
                });
                
                if(tmp.length > 0) this.spec2 = tmp.toString();
                spec2_flag = (this.spec2 == '' && GBL.blank(jQuery('#spec2_other')));
                
            } else {
                
                this.spec2 = jQuery('#spec2_'+ tot).val();
                spec2_flag = (this.spec2 == '');
            }
            
            if(spec2_flag) {
                
                Popup.dialog({
                    title : 'OOOPS !!! BLANK',
                    message : '"Specifics <b>#2</b>" is a <b>required</b> field.',
                    buttons: ['Okay', 'Cancel'],
                    width: '420px'
                });

                return false;
            }

            if(this.spec2 == 'other' && GBL.blank(jQuery('#spec2_other'))) {
                
                Popup.dialog({
                    title : 'OOOPS !!! BLANK',
                    message : 'The "<b>other</b>" field is <b>required</b> for "Specifics <b>#2</b>".',
                    buttons: ['Okay', 'Cancel'],
                    buttonClick : function() { jQuery('#spec2_other').focus(); },
                    width: '420px'
                });

                return false;

            } else this.spec2_other = jQuery('#spec2_other').val().trim();
        }

        return true;
    }
    
    this.submit = function(obj) {
        
        var pdate = jQuery('#preferred_date');
        var requested_by = jQuery('#requested_by');
        var approved_by_other = jQuery('#approved_by_other');
        var approved_by = '';
        
        if(jQuery('#approved_by') && jQuery('#approved_by').length) {
            approved_by = jQuery('#approved_by').val().trim();
        }
        
        var sbu = jQuery('#sbu');
        var sbu_other = jQuery('#sbu_other');
        var type_of_test = jQuery('#type_of_test');        
        var nof_testing_dates = jQuery('#nof_testing_dates');
        var frequency = jQuery('#frequency');
        var calculated_schedule = this.calculated_schedule;
        var other_schedule = jQuery('#other_schedule');        
        var tpurpose_arr = [];
        var tpurpose_other = jQuery('#tpurpose_other');
        var decision_criteria = jQuery('#decision_criteria');
        var next_step = jQuery('#next_step');
        var attr_to_test = '';
        
        if(jQuery('#attr_to_test') && jQuery('#attr_to_test').length) {
            attr_to_test = jQuery('#attr_to_test').val().trim();
        }
        
        var special_requirements = jQuery('#special_requirements');
        
        var project_desc = jQuery('#project_desc');
        var nof_samples = jQuery('#no_of_samples');
        var samples_name = jQuery('#samples_name');
        var samples_desc = jQuery('#samples_desc');        
        var product_data = '';
        var tmp;
        
        this.step(1);
        if(GBL.blank(pdate)) {
            
            Popup.dialog({
                title : 'OOOPS !!! BLANK',
                message : 'Please fill-in the "<b>preferred date</b>".',
                buttons: ['Okay', 'Cancel'],
                buttonClick : function() { pdate.focus(); },
                width: '420px'
            });

            return;
        }
        
        if(GBL.blank(requested_by)) {
            
            Popup.dialog({
                title : 'OOOPS !!! BLANK',
                message : 'Please fill-in the "<b>requested by</b>" field.',
                buttons: ['Okay', 'Cancel'],
                buttonClick : function() { requested_by.focus(); },
                width: '420px'
            });
            
            return;
        }

        if(approved_by_other.val().trim() != '') {
            
            tmp = approved_by_other.val().split(' ').length;
            if(tmp != 3) {
                
                Popup.dialog({
                    title : 'OOOPS !!! INVALID',
                    message : 'Please make sure that the "<b>approved by</b>" field is in correct format.',
                    buttons: ['Okay', 'Cancel'],
                    buttonClick : function() { approved_by_other.focus(); },
                    width: '420px'
                });

                return;
            }            
        }
        
        if(GBL.blank(sbu)) {
            
            Popup.dialog({
                title : 'OOOPS !!! BLANK',
                message : '"<b>SBU</b>" is a <b>required</b> field.',
                buttons: ['Okay', 'Cancel'],
                buttonClick : function() { sbu.focus(); },
                width: '420px'
            });

            return;
        }
        
        if(sbu.val() == 'other' && GBL.blank(sbu_other)) {
            
            Popup.dialog({
                title : 'OOOPS !!! BLANK',
                message : '"Other <b>SBU</b>" is a <b>required</b> field.',
                buttons: ['Okay', 'Cancel'],
                buttonClick : function() { sbu_other.focus(); },
                width: '420px'
            });
            
            return;
        }
        
        if(GBL.blank(type_of_test)) {
            
            Popup.dialog({
                title : 'OOOPS !!! BLANK',
                message : 'The "<b>type of test</b>" is a <b>required</b> field.',
                buttons: ['Okay', 'Cancel'],
                buttonClick : function() { type_of_test.focus(); },
                width: '420px'
            });
            
            return;
            
        } else {
            
            var spec_check = this.specifics_checker(type_of_test.val());
            if(! spec_check) return;
        }
        
        if(nof_testing_dates.val() > 1) {
            
            if(GBL.blank(frequency)) {
                
                Popup.dialog({
                    title : 'OOOPS !!! BLANK',
                    message : 'The <b>frequency</b> is a <b>required</b> field.',
                    buttons: ['Okay', 'Cancel'],
                    buttonClick : function() { frequency.focus(); },
                    width: '420px'
                });

                return;
            }
            else {
                
                if((frequency.val() == 'other' || frequency.val() == '2m') && GBL.blank(other_schedule)) {
                    
                    Popup.dialog({
                        title : 'OOOPS !!! BLANK',
                        message : 'Please enter the preferred date schedule(s).',
                        buttons: ['Okay', 'Cancel'],
                        buttonClick : function() { other_schedule.focus(); },
                        width: '420px'
                    });

                    return;
                }
            }
        }
        
        jQuery('input[name=tpurpose]:checked').each(function(){
            tpurpose_arr.push(jQuery(this).val());
        });
        
        if(tpurpose_arr.length == 0 && tpurpose_other.val() == '') {
            
            Popup.dialog({
                title : 'OOOPS !!! BLANK',
                message : 'Please select at least one (1) "<b>purpose</b>" of this Form.',
                buttons: ['Okay', 'Cancel'],
                width: '420px'
            });

            return;
        }
        
        if(GBL.blank(decision_criteria)) {
            
            Popup.dialog({
                title : 'OOOPS !!! BLANK',
                message : 'The "<b>decision criteria</b>" is a <b>required</b> field.',
                buttons: ['Okay', 'Cancel'],
                buttonClick : function() { decision_criteria.focus(); },
                width: '420px'
            });

            return;
        }
        
        if(type_of_test.val() == 'affective') {
            
            if(GBL.blank(jQuery('#attr_to_test'))) {
            
                Popup.dialog({
                    title : 'OOOPS !!! BLANK',
                    message : 'The "<b>attributes</b> to test" is a <b>required</b> field.',
                    buttons: ['Okay', 'Cancel'],
                    buttonClick : function() { jQuery('#attr_to_test').focus(); },
                    width: '420px'
                });

                return;
            }
        }
        
        /* STEP 2:*/        
        this.step(2);
        
        if(GBL.blank(project_desc)) {
            
            Popup.dialog({
                title : 'OOOPS !!! BLANK',
                message : 'The "<b>project description</b>" is a <b>required</b> field.',
                buttons: ['Okay', 'Cancel'],
                buttonClick : function() { project_desc.focus(); },
                width: '420px'
            });
            
            return;
        }
        
        if(GBL.blank(samples_name)) {
            
            Popup.dialog({
                title : 'OOOPS !!! BLANK',
                message : 'The "<b>sample(s) name</b>" is a <b>required</b> field.',
                buttons: ['Okay', 'Cancel'],
                buttonClick : function() { samples_name.focus(); },
                width: '420px'
            });
            
            return;
        }
        
        if(GBL.blank(samples_desc)) {
            
            Popup.dialog({
                title : 'OOOPS !!! BLANK',
                message : 'The "<b>sample(s) description</b>" is a <b>required</b> field.',
                buttons: ['Okay', 'Cancel'],
                buttonClick : function() { samples_desc.focus(); },
                width: '420px'
            });
            
            return;
        }
        
        if(GBL.blank(nof_samples)) {
            
            Popup.dialog({
                title : 'OOOPS !!! BLANK',
                message : 'The "<b>number of samples</b> to be tested" is a <b>required</b> field.',
                buttons: ['Okay', 'Cancel'],
                buttonClick : function() { nof_samples.focus(); },
                width: '420px'
            });
            
            return;
            
        } else {
            
            /* START: Number of Samples Check. */
            var nof_samples_accepted = [];
            var nof_samples_int = parseInt(nof_samples.val(), 10);
            var subject = '';
            
            if(type_of_test.val() == 'affective') {
                
                subject = (this.spec2 == 'other') ? this.spec2_other : this.spec2;
                
                nof_samples_accepted[7]     = 1; /* Single Monadic. */
                nof_samples_accepted[8]     = 2; /* Sequential Monadic. */
                nof_samples_accepted[9]     = 2; /* Sequential Monadic w/ Paired Preference. */
                
                nof_samples_accepted[10]    = 3; /* Sequential Monadic w/ Ranking for Preference. */
                nof_samples_accepted[12]    = 3; /* Ranking. */
                
            }
            else
            if(type_of_test.val() == 'analytical') {
                
                subject = (this.spec1 == 'other') ? this.spec1_other : this.spec1;
                
                nof_samples_accepted[3]     = 1; /* Descriptive. */
                nof_samples_accepted[1]     = 2; /* Triangle. */
                nof_samples_accepted[46]    = 2; /* 2-afc. */
            }
            
            subject = parseInt(subject, 10);
            var must_be_equal = [1, 46];
            
            if(nof_samples_accepted.length) {
                
                for(var xIndex in nof_samples_accepted) {
                    
                    if(subject == xIndex) {
                        
                        if(jQuery.inArray(subject, must_be_equal) > -1) {
                            
                            if(nof_samples_int != nof_samples_accepted[xIndex]) {

                                Popup.dialog({
                                    title : 'OOOPS !!! NOT EQUAL',
                                    message : 'The "<b>number of samples</b>" must be <b>'+ nof_samples_accepted[xIndex] +'</b>'+ ((nof_samples_int > nof_samples_accepted[xIndex]) ? ' only' : '') +'.',
                                    buttons: ['Okay', 'Cancel'],
                                    buttonClick : function() { nof_samples.focus(); },
                                    width: '420px'
                                });

                                return;
                            }
                        }
                        else {
                            
                            if(nof_samples_int < nof_samples_accepted[xIndex]) {

                                Popup.dialog({
                                    title : 'OOOPS !!! LESS',
                                    message : 'The "<b>number of samples</b>" must be at least <b>'+ nof_samples_accepted[xIndex] +'</b> or above.',
                                    buttons: ['Okay', 'Cancel'],
                                    buttonClick : function() { nof_samples.focus(); },
                                    width: '420px'
                                });

                                return;
                            }
                        }
                    }                    
                }
            }
            /* END: Number of Samples Check. */
        }
        
        var x = 0;
        
        for(x=1; x<=nof_samples.val(); x++) {
            
            if(GBL.blank(jQuery('#product_data_attr'+ x))) {
                
                Popup.dialog({
                    title : 'OOOPS !!! BLANK',
                    message : 'The "<b>attribute</b>" is a <b>required</b> field for item <b>#'+ x +'</b>.',
                    buttons: ['Okay', 'Cancel'],
                    buttonClick : function() { jQuery('#product_data_attr'+ x).focus(); },
                    width: '420px'
                });
                
                return;
            }
            
            if(GBL.blank(jQuery('#product_data_code'+ x))) {
                
                Popup.dialog({
                    title : 'OOOPS !!! BLANK',
                    message : 'The "<b>code</b>" is a <b>required</b> field for item <b>#'+ x +'</b>.',
                    buttons: ['Okay', 'Cancel'],
                    buttonClick : function() { jQuery('#product_data_code'+ x).focus(); },
                    width: '420px'
                });
                
                return;
            }
            
            if(GBL.blank(jQuery('#product_data_pd'+ x)) && GBL.blank(jQuery('#product_data_cu'+ x))) {
                
                Popup.dialog({
                    title : 'OOOPS !!! BLANK',
                    message : 'Please fill-in at least a "<b>PD</b> or <b>CU</b>" for item <b>#'+ x +'</b>.',
                    buttons: ['Okay', 'Cancel'],
                    buttonClick : function() { jQuery('#product_data_pd'+ x).focus(); },
                    width: '420px'
                });
                
                return;
            }
            
            if(GBL.blank(jQuery('#product_data_supplier'+ x))) {
                
                Popup.dialog({
                    title : 'OOOPS !!! BLANK',
                    message : 'The "<b>supplier</b>" is a <b>required</b> field for item <b>#'+ x +'</b>.',
                    buttons: ['Okay', 'Cancel'],
                    buttonClick : function() { jQuery('#product_data_supplier'+ x).focus(); },
                    width: '420px'
                });
                
                return;
            }
            
            if(GBL.blank(jQuery('#product_data_batch_weight'+ x))) {
                
                Popup.dialog({
                    title : 'OOOPS !!! BLANK',
                    message : 'The "<b>batch weight</b>" is a <b>required</b> field for item <b>#'+ x +'</b>.',
                    buttons: ['Okay', 'Cancel'],
                    buttonClick : function() { jQuery('#product_data_batch_weight'+ x).focus(); },
                    width: '420px'
                });
                
                return;
            }
            
            if(GBL.blank(jQuery('#product_data_qty'+ x))) {
                
                Popup.dialog({
                    title : 'OOOPS !!! BLANK',
                    message : 'The "<b>quantity</b>" is a <b>required</b> field for item <b>#'+ x +'</b>.',
                    buttons: ['Okay', 'Cancel'],
                    buttonClick : function() { jQuery('#product_data_qty'+ x).focus(); },
                    width: '420px'
                });
                
                return;
            }            
        }
        
        for(x=1; x<=nof_samples.val(); x++) {
            
            product_data += jQuery('#product_data_attr'+ x).val();
            product_data += '[=AXL_D=]'+ jQuery('#product_data_code'+ x).val();
            product_data += '[=AXL_D=]'+ jQuery('#product_data_pd'+ x).val();
            product_data += '[=AXL_D=]'+ jQuery('#product_data_cu'+ x).val();
            product_data += '[=AXL_D=]'+ jQuery('#product_data_supplier'+ x).val();
            product_data += '[=AXL_D=]'+ jQuery('#product_data_batch_weight'+ x).val();
            product_data += '[=AXL_D=]'+ jQuery('#product_data_qty'+ x).val();
            product_data += '[=AXL_D=]'+ jQuery('#product_data_others'+ x).val() +'[=AXL_R=]';
        }
        
        GBL.loader();
        
        var args = {
            pdate               : pdate.val(),
            requested_by        : requested_by.val(),
            approved_by         : approved_by,
            approved_by_other   : approved_by_other.val().trim(),
            sbu                 : sbu.val(),
            sbu_other           : sbu_other.val(),
            tot                 : type_of_test.val(),
            spec1               : this.spec1,
            spec2               : this.spec2,
            spec1_other         : this.spec1_other,
            spec2_other         : this.spec2_other,
            nof_testing_dates   : nof_testing_dates.val(),
            frequency           : frequency.val(),
            calculated_schedule : calculated_schedule,
            other_schedule      : other_schedule.val(),
            tpurpose            : tpurpose_arr.toString(),
            tpurpose_other      : tpurpose_other.val(),
            project_desc        : project_desc.val(),
            nof_samples         : nof_samples.val(),
            samples_name        : samples_name.val(),
            samples_desc        : samples_desc.val(),
            product_data        : product_data,
            decision_criteria   : decision_criteria.val(),
            next_step           : next_step.val(),
            attr_to_test        : attr_to_test,
            special_req         : special_requirements.val(),
            t                   : (new Date).getTime()
        };
        
        if(SECTION == 'edit_by_owner') {
            jQuery.extend(args, { id : this.id });
        }
        
        jQuery.post(
            DOCROOT +'rta/async_create',
            args,
            function(r){
                
                GBL.loader(false);
                
                if(r) {
                    
                    Popup.dialog({
                        title : r.title,
                        message : r.msg,
                        buttons: ['Okay', 'Cancel'],
                        buttonClick : function() { window.location.href = r.go; },
                        width: '420px'
                    });
                    
                }

            },
            'json'
        );
    }
    
    this.step = function(step) {
        
        jQuery('#step_text').html(step);
        
        if(step == 1) {
            
            jQuery('#step2').hide();
            jQuery('#step1').show();
            
        } else {
            
            jQuery('#step1').hide();
            jQuery('#step2').show();
        }
    }
    
    this.pad_row = function(obj) {
        
        var t = jQuery(obj).val();
        
        /* Force a maximum value of 10. */
        if(t > 10) {
            
            jQuery(obj).val(10);
            this.pad_row(obj);
            
            return;
        }
        
        var html = '';
        var x = 0;
        var total_rows = jQuery('#table_data tr').length - 1;
        
        var start = total_rows;
        
        if(t > total_rows) {
            
            for(x=start; x<t; x++) {
                
                var lbl = (x + 1);
                
                html += '<tr id="product_data_tr'+ lbl +'"><td><div>&nbsp;&nbsp;&nbsp;<b>'+ lbl +'</b>&nbsp;&nbsp;&nbsp;<input id="product_data_attr'+ lbl +'" type="text" /></div></td>';
                html += '<td><div><input id="product_data_code'+ lbl +'" type="text" style="width: 70px" /></div></td>';
                html += '<td><div><input id="product_data_pd'+ lbl +'" type="text" style="width: 80px" readonly="readonly" /></div></td>';
                html += '<td><div><input id="product_data_cu'+ lbl +'" type="text" style="width: 80px" readonly="readonly" /></div></td>';
                html += '<td><div><input id="product_data_supplier'+ lbl +'" type="text" /></div></td>';
                html += '<td><div><input id="product_data_batch_weight'+ lbl +'" type="text" style="width: 70px" /></div></td>';
                html += '<td><div><input id="product_data_qty'+ lbl +'" type="text" style="width: 70px" /></div></td>';
                html += '<td><div><input id="product_data_others'+ lbl +'" type="text" style="width: 70px" /></div></td>';
                html += '</tr>';
            }
            
            if(html != '') {
                
                jQuery('#table_data').append(html);
                for(x=start; x<t; x++) {

                    jQuery('#product_data_pd'+ (x + 1) +', #product_data_cu'+ (x + 1)).datepicker({
                        inline: true
                    });
                }
            }
        } else {
            
            for(x=total_rows; x>t; x--) {
                jQuery('#product_data_tr'+ x).remove();
            }
        }
    }
}