<?php defined('BASEPATH') or exit('No direct script access allowed');

/* 
 * Title : RTA Controller Class
 * Author: Armande Bayanes (tuso@programmerspride.com)
 * Description: Handles RTA concerns ...
 **/

class Rta extends XY_Controller {
    
    //public $access_level = array(1, 2, 4, 6, 7);
    
    public function __construct() {
        
        parent::__construct();
        
        $this->load->model('rta_model', '', true);
        
        /* Controller + Function URL . */
        $cf_url = $this->configXY->URI[0] . '/' . $this->configXY->URI[1];
        if($cf_url[strlen($cf_url) - 1] == '/') {

            $cf_url = substr ($cf_url, 0, strlen($cf_url) - 1);
        }
        
        if($this->session->level != 1 && $cf_url == 'rta/edit_by_admin') {
            
            redirect(base_url('home/error'));
            return;
        }
    }
    
    public function async_load_list() {
        
        $this->securePage();
        
        if(empty($this->configXY->URI) || empty($this->session)) exit('No data.');
        $input = $this->configXY->URI;
        unset($input['t']);
        
        $this->rta_model->doLoadList($input);
    }
    
    public function view($id) {
        
        if($id == 0)
            return $this->_404_ ();
        
        if($this->session->level == 1) $access = 'admin';
        
        $rta = $this->rta_model->doLoadRTA($id, $access);
        if(empty($rta)) return $this->_404_ ();

        $rta->date_filed = date('\<\b\>m/d/Y\<\/\b\> \a\t \<\b\>h:iA\<\/\b\>', strtotime($rta->date_filed));
        $rta->date_preferred = date('m/d/Y', strtotime($rta->date_preferred));
        
        $data['requested_by'] = $this->admin_model->getUserName($rta->requested_by_id);
        $data['approved_by'] = $this->admin_model->getUserName($rta->approved_by_id);
        
        $sql = "SELECT name FROM sbu WHERE id=" . $rta->sbu;
        $tmp = $this->db->query($sql)->row();
        if(! empty($tmp)) $data['sbu'] = $tmp->name;
        
        if($rta->type_of_test != 'micro') $rta->type_of_test = ucfirst($rta->type_of_test);
        else $rta->type_of_test = strtoupper($rta->type_of_test);
        
        list($data['specifics_1'], $data['specifics_2']) = $this->admin_model->getSpecifics($rta->specific_1, $rta->specific_2);
        
        $frequency = array(
            'd' => 'Daily',
            'w' => 'Weekly',
            '2m' => 'Twice a Month',
            'm' => 'Monthly',
            'other' => 'Other'
        );
        
        $rta->frequency = $frequency[$rta->frequency];
        if($rta->state == 1) {
            
            /* This field will contain a value only when approved. */
            $data['schedule'] = $rta->schedule; /* Final schedule. */
            
        } else {
            
            if($rta->nof_testing_dates > 1) $data['schedule'] = $rta->schedule_tentative;
            else $data['schedule'] = date('m/d/Y', strtotime($rta->date_preffered));
        }
        
        $sql = "SELECT content FROM test_purpose WHERE id IN(" . $rta->test_purpose_ids . ") ORDER BY content";
        $query = $this->db->query($sql);
        foreach($query->result() as $row) {
            $data['test_purpose'] .= $row->content . '<br />';
        }
        
        $data['rta'] = $rta;
        
        $data['content'] = $this->getViewFile(__FUNCTION__, $data);
        $this->load->view('main', $data);
    }
    
    public function edit_by_admin($id) {
        
        if($id == 0) { redirect(base_url('home/error')); }
        
        $this->load->model('rta_model', '', true);
        
        /* Get necessary data for a specified RTA. */
        $rta = $this->rta_model->doLoadDetail($id, 'admin');
        if(empty($rta)) { redirect(base_url('home/error')); }
        
        $this->document->loadPlugin('datepicker');
        $this->configXY->JS_VARS['[var=false]RTA_FORM.id'] = $id;
        $this->configXY->JS_VARS['[var=false]RTA_FORM.state'] = $rta->state;
        $this->configXY->JS_VARS['[var=false]RTA_FORM.orig_nof_testing_dates'] = $rta->nof_testing_dates;
        $this->configXY->JS_VARS['[var=false]RTA_FORM.orig_frequency'] = "'" . $rta->frequency . "'";
        
        $rta->date_filed = date('\<\b\>m/d/Y\<\/\b\> \a\t \<\b\>h:iA\<\/\b\>', strtotime($rta->date_filed));
        $rta->samples_desc = str_replace(array('<br>', '<br/>', '<br />'), "\n", strtolower($rta->samples_desc));
        $rta->project_desc = str_replace(array('<br>', '<br/>', '<br />'), "\n", strtolower($rta->project_desc));
        $rta->decision_criteria = str_replace(array('<br>', '<br/>', '<br />'), "\n", strtolower($rta->decision_criteria));
        
        $data = $this->rta_model->doLoadFields($this->session, $rta);
        $data['rta'] = $rta;
        
        $sql = "SELECT * FROM sbu_locations";
        $query = $this->db->query($sql);
        if($query->num_rows()) {
            
            $this->configXY->JS_VARS['SBU_LOC'] = 'new Array()';
            $x = 0;
            foreach($query->result() as $row) {

                /* Create JS variables. */
                $this->configXY->JS_VARS['[var=false]SBU_LOC[' . $x . ']'] = "'" . $row->name . "'";
                $x++;
                
                /* Create select options. */
                $html .= '<option value="' . $row->id . '"' . (($row->id == $rta->location) ? ' selected="selected"' : (($row->id == 3) ? ' selected="selected"' : '')) . '>' . $row->name . '</option>'; //' . (($row->id == $rta->sbu) ? ' selected="selected"' : '') . '
            }
            
            if($html != '') {
                $html = '<select id="location" onchange="GBL.toggle_other_field(this,\'loc_other_wrapper\',\'loc_other\')">' .
                '<option value="">Select:</option>' . $html .
                '<option value="other">Others</option>' .
                '</select>';
            }
            
            $data['sbu_loc'] = $html;
        }
        
        $schedule = $this->rta_model->doSchedAgeCalculate($rta->schedule, $rta->schedule_tentative);
        
        $this->configXY->JS_VARS['[var=false]RTA_FORM.schedule_tentative'] = json_encode(explode(', ', $rta->schedule_tentative));
        $this->configXY->JS_VARS['[var=false]RTA_FORM.schedule_approved'] = json_encode($schedule);
        
        $data['content'] = $this->getViewFile(__FUNCTION__, $data);
        $this->load->view('main', $data);
    }
    
    public function edit_by_owner($id) {
        
        if($id == 0) { redirect(base_url('home/error')); }
        
        $this->load->model('rta_model', '', true);
        
        /* Get necessary data for a specified RTA. */
        $rta = $this->rta_model->doLoadDetail($id);
        if(empty($rta)) { redirect(base_url('home/error')); }
        if($rta->state != 3) { redirect(base_url('home/error')); } /* Check if PENDING. */
        $rta->date_filed = date('\<\b\>m/d/Y\<\/\b\> \a\t \<\b\>h:iA\<\/\b\>', strtotime($rta->date_filed));
        $rta->samples_desc = str_replace(array('<br>', '<br/>', '<br />'), "\n", strtolower($rta->samples_desc));
        $rta->project_desc = str_replace(array('<br>', '<br/>', '<br />'), "\n", strtolower($rta->project_desc));
        $rta->decision_criteria = str_replace(array('<br>', '<br/>', '<br />'), "\n", strtolower($rta->decision_criteria));
        
        $this->document->loadPlugin('datepicker');
        $this->configXY->JS[] = APPPATH . 'views/rta/create.js';
        $this->configXY->JS_VARS['[var=false]RTA_FORM.id'] = $id;
        
        $data = $this->rta_model->doLoadFields($this->session, $rta);
        $data['rta'] = $rta;
        
        $this->configXY->JS_VARS['[var=false]RTA_FORM.schedule_tentative'] = json_encode(explode(', ', $rta->schedule_tentative));
        //$this->configXY->JS_VARS['[var=false]RTA_FORM.schedule_approved'] = json_encode($schedule);
        
        $data['content'] = $this->getViewFile(__FUNCTION__, $data);
        $this->load->view('main', $data);
    }
    
    public function create($id = 0) {
        
        $this->document->loadPlugin('datepicker');
        
        /* Get necessary data when chosed to re-use an RTA. */
        if($id > 0) { 
            
            if($this->session->level == 1) $access = 'admin';
            elseif(in_array($this->session->level, array(2, 6, 7))) $access = 'po';
            else $access = '';
            
            $rta = $this->rta_model->doLoadDetail($id, $access);            
        }

        $data = $this->rta_model->doLoadFields($this->session, $rta);
        $data['rta'] = $rta;
        $data['date_filed'] = date('m/d/Y', strtotime($this->configXY->DATE));
        $data['access_level'] = $this->access_level;
        
        $data['content'] = $this->getViewFile(__FUNCTION__, $data);
        $this->load->view('main', $data);
    }
    
    public function async_create() {
        
        $this->securePage();
        if(empty($_POST) || empty($this->session)) exit();
        
        $input = $_POST;
        unset($input['t']);
        
        array_walk($input, 'xy_input_clean_up_byref');
        extract($input);
        
        /* Add new SBU when chosed to enter a new item via the "Others" field. */
        if($sbu_other != '') {
            
            $sbu = $this->rta_model->doSBUAdd($sbu_other, $this->session->id);
        }
        
        if($spec1_other != '') {
            
            if($tot == 'micro' || $tot == 'physico_chem') {
                
                $spec1 = array();
                if(substr_count($spec1_other, ',')) $tmp = explode(',', $spec1_other);
                else $tmp = array($spec1_other);

                foreach($tmp as $value) { $spec1[] = $this->rta_model->doSpecificAdd($tot, 1, $value, $this->session->id); }                
                $spec1 = implode(',', $spec1);
                
            } else $spec1 = $this->rta_model->doSpecificAdd($tot, 1, $spec1_other, $this->session->id);            
        }
        
        if($spec2_other != '') {
            
            if($tot == 'micro' || $tot == 'physico_chem') {
                
                $spec2 = array();
                if(substr_count($spec2_other, ',')) $tmp = explode(',', $spec2_other);
                else $tmp = array($spec2_other);

                foreach($tmp as $value) { $spec2[] = $this->rta_model->doSpecificAdd($tot, 2, $value, $this->session->id); }
                $spec2 = implode(',', $spec2);
                
            } else $spec2 = $this->rta_model->doSpecificAdd($tot, 2, $spec2_other, $this->session->id);
        }
        
        if($product_data != '') { $rta_product_ids = $this->rta_model->doProductDataAdd($product_data); }
        
        /* Add purpose of test when "other field" was filled-in. */
        $this->rta_model->doTestPurposeAdd($tpurpose_other, $this->session->id, $tpurpose);
        
        /* Add User (Approved by) when "other field" was filled-in. */
        if($approved_by_other != '') { $approved_by = $this->rta_model->doApprovedByAdd($approved_by_other, $this->session->id); }
        
        if($nof_testing_dates > 1) {
            
            /* Note: Only the tentative schedule will be filled-in with this action. */
            if($frequency == '2m' || $frequency == 'other') $schedule_tentative = $other_schedule;
            else $schedule_tentative = $calculated_schedule;
            
        } else $schedule_tentative = date('m/d/Y', strtotime($pdate));
        
        $sql_fields = "
            user_id=" . $this->session->id . ",
            date_preferred='" . date('Y-m-d', strtotime($pdate)) . "',
            requested_by_id='" . $requested_by . "',
            approved_by_id='" . $approved_by . "',
            sbu=" . $sbu . ",
            type_of_test='" . $tot . "',
            nof_testing_dates=" . $nof_testing_dates . ",
            frequency='" . $frequency . "',
            schedule_tentative='" . $schedule_tentative . "',
            specific_1='" . $spec1 . "',
            specific_2='" . $spec2 . "',
            test_purpose_ids='" . $tpurpose . "',
            project_desc='" . $project_desc . "',
            no_of_samples=" . $nof_samples . ",
            samples_name='" . $samples_name . "',
            samples_desc='" . $samples_desc . "',
            rta_product_data_ids='" . $rta_product_ids . "',
            decision_criteria='" . $decision_criteria . "',
            next_step='" . $next_step . "',
            attributes='" . $attr_to_test . "',
            special_requirements='" . $special_req . "',
            state=3";
        
        if($id > 0) {
            
            $sql = "UPDATE rta_forms SET " . $sql_fields . " WHERE id=" . $id;
            $rta_form_id = $id;
            
        } else $sql = "INSERT INTO rta_forms SET date_filed='" . $this->configXY->DATE . "'," . $sql_fields;
        
        $this->db->query($sql);
        if(! $rta_form_id) $rta_form_id = $this->db->insert_id();
        
        /*
         * rta_po_history
         * 
         * 1 => Draft
         * 2 => Sent
         * 
         **/
        $data = array(
            "rta_form_id" => $rta_form_id,
            "date_filed" => $this->configXY->DATE,
            "date_preferred" => date('Y-m-d', strtotime($pdate)),
            "requested_by_id" => $requested_by,
            "approved_by_id" => $approved_by,
            "sbu" => $sbu,
            "type_of_test" => $tot,
            "nof_testing_dates"  => $nof_testing_dates,
            "frequency" => $frequency,
            "schedule" => $calculated_schedule,
            "schedule_other" => $other_schedule,
            "specific_1" => $spec1,
            "specific_2" => $spec2,
            "test_purpose_ids" => $tpurpose,
            "project_desc" => $project_desc,
            "no_of_samples" => $nof_samples,
            "samples_name" => $samples_name,
            "samples_desc" => $samples_desc,
            "rta_product_data_ids" => $rta_product_ids,
            "decision_criteria" => $decision_criteria,
            "next_step" => $next_step,
            "attributes" => $attr_to_test,
            "special_requirements" => $special_req);
        
        $sql = "
            INSERT
            INTO    rta_po_history
            SET     user_id=" . $this->session->id . ",
                    rta_form_id=" . $rta_form_id . ",
                    data='" . base64_encode(serialize($data)) . "',
                    created='" . $this->configXY->DATE . "'";
        $this->db->query($sql);
        
        $data['title'] = 'GREAT !!! SUBMITTED';
        $data['msg'] = 'Your RTA was ' . (($id > 0) ? 'updated' : 'filed') . ' successfully. Please wait for approval.';
        $data['go'] = xy_url('po/rta_by_owner');
        
        /* START:
         * Prepare and execute email notification.
         **/
        $superior = $this->rta_model->getSuperiorData();
        $mail_fields['notify']              = 'superior';
        $mail_fields['action']              = 'rta_' . (($id > 0) ? 'edit' : 'add');
        $mail_fields['user_id']             = $this->session->id;
        $mail_fields['user_name']           = $this->session->username;
        $mail_fields['rta_id']              = $rta_form_id;
        $mail_fields['rta_name']            = $rta_form_id . ' : ' . $samples_name;
        $mail_fields['rta_datetime']        = base64_encode($this->configXY->DATE);
        $mail_fields['superior_name']       = $superior->firstname . ' ' . $superior->lastname;
        $mail_fields['superior_username']   = $superior->username;
        $mail_fields['superior_email']      = (($superior->email != '') ? $superior->email : $superior->office_email);
        $mail_fields['notify_admin']        = 1;
        
        xy_email($mail_fields, 'exec.php');
        /* END:
         * Prepare and execute email notification.
         **/
        
        echo json_encode($data);
    }
    
    public function async_edit_by_admin() {
        
        $this->securePage();
        if(empty($_POST) || empty($this->session)) exit();
        
        $input = $_POST;
        unset($input['t']);
        
        array_walk($input, 'xy_input_clean_up_byref');
        extract($input);
        
        if($id == 0) exit();
        
        if($location == 'other' && $loc_other != '') {
            
            /* Passed "loc other" might already exists but failed to be validated by JavaScript. */
            $sql = "
                SELECT  *
                FROM    sbu_locations
                WHERE   name='" . $loc_other . "'";
            $query = $this->db->query($sql);
            
            $tmp = $query->row();
            if(empty($tmp)) {
                
                /* Insert SBU Location so it will be available on the next instance. */
                $sql = "
                    INSERT
                    INTO    sbu_locations
                    SET     user_id=" . $this->session->id . ",
                            name='" . $loc_other . "',
                            created='" . $this->config->config['XY']->DATE . "'";
                $this->db->query($sql);
                $location = $this->db->insert_id();

            } else $location = $tmp->id;
        }
        
        /* Add new SBU when chosed to enter a new item via the "Others" field. */
        if($sbu_other != '') {
            
            $sbu = $this->rta_model->doSBUAdd($sbu_other, $this->session->id);
        }
        
        if($spec1_other != '') {
            
            if($tot == 'micro' || $tot == 'physico_chem') {
                
                $spec1 = array();
                if(substr_count($spec1_other, ',')) $tmp = explode(',', $spec1_other);
                else $tmp = array($spec1_other);

                foreach($tmp as $value) { $spec1[] = $this->rta_model->doSpecificAdd($tot, 1, $value, $this->session->id); }                
                $spec1 = implode(',', $spec1);
                
            } else $spec1 = $this->rta_model->doSpecificAdd($tot, 1, $spec1_other, $this->session->id);            
        }
        
        if($spec2_other != '') {
            
            if($tot == 'micro' || $tot == 'physico_chem') {
                
                $spec2 = array();
                if(substr_count($spec2_other, ',')) $tmp = explode(',', $spec2_other);
                else $tmp = array($spec2_other);

                foreach($tmp as $value) { $spec2[] = $this->rta_model->doSpecificAdd($tot, 2, $value, $this->session->id); }
                $spec2 = implode(',', $spec2);
                
            } else $spec2 = $this->rta_model->doSpecificAdd($tot, 2, $spec2_other, $this->session->id);
        }
        
        if($product_data != '') { $rta_product_ids = $this->rta_model->doProductDataAdd($product_data); }
        
        /* Add purpose of test when "other field" was filled-in. */
        $this->rta_model->doTestPurposeAdd($tpurpose_other, $this->session->id, $tpurpose);
        
        /* Add User (Approved by) when "other field" was filled-in. */
        if($approved_by_other != '') { $approved_by = $this->rta_model->doApprovedByAdd($approved_by_other, $this->session->id); }
        
        $schedule = $calculated_schedule;
        
        $state = (int) $state;
        
        if($state > 0) { $sql_state = " state=" . ($state - 1) . ", "; }
        
        $state--;
        /* If chosed to "approve", then set the final schedule. */
        if($state == 1) {
            
            if($nof_testing_dates > 1) {
                
                if($frequency == '2m' || $frequency == 'other') $schedule = $other_schedule;
                else $schedule = $calculated_schedule;

            } else $schedule = date('m/d/Y', strtotime($pdate));
        }
        
        /* Admin edited the original tentative_schedule. */
        if($tentative_schedule != '') {
            
            $tmp = explode(',', $tentative_schedule);
            $tmp2 = array();
            if($tentative_schedule_del != "") $tentative_schedule_del = explode(',', $tentative_schedule_del);
            else $tentative_schedule_del = array();
            
            for($x=0, $y = count($tmp); $x<$y; $x++) {
                
                if(! in_array(($x + 1), $tentative_schedule_del)) {
                    $tmp2[] = $tmp[$x];
                }                
            }
            
            $schedule = implode(',', $tmp2);
        }
        
        $sql = "SELECT * FROM rta_forms WHERE id=" . $id;
        $data = $this->db->query($sql)->row_array();
        
        $rta_no_flag = '';
        /* START: RTA Number. */
        if(substr_count($schedule, ',')) {
            
            $schedule_arr = explode(',', $schedule);
            $rta_no_flag = 'M';
            
        } else $schedule_arr = array($schedule);
        
        $this->rta_model->doSchedSave($schedule_arr, $id);
        
        $rta_no_date = date('Y-m-d', strtotime($schedule_arr[0]));
        $sql = "SELECT MAX(rta_no_count) AS rta_no_count_max FROM rta_forms WHERE rta_no_date='" . $rta_no_date . "'";
        $query = $this->db->query($sql);
        if($query->num_rows()) $rta_no_count = (int) $query->row()->rta_no_count_max;
        else $rta_no_count = 0;
        $rta_no_count++;
        /* END: RTA Number. */
        
        $sql = "
            UPDATE  rta_forms
            SET     rta_no_date='" . $rta_no_date . "',
                    rta_no_count='" . $rta_no_count . "',
                    rta_no_flag='" . $rta_no_flag . "',
                    processed_by_id=" . $this->session->id . ",
                    date_processed='" . $this->configXY->DATE . "',
                    date_preferred='" . date('Y-m-d', strtotime($pdate)) . "',
                    requested_by_id=" . $requested_by . ",
                    approved_by_id=" . (double) $approved_by . ",
                    sbu=" . $sbu . ",
                    type_of_test='" . $tot . "',
                    nof_testing_dates=" . $nof_testing_dates . ",
                    frequency='" . $frequency . "',
                    schedule='" . $schedule . "',                    
                    specific_1='" . $spec1 . "',
                    specific_2='" . $spec2 . "',
                    test_purpose_ids='" . $tpurpose . "',
                    project_desc='" . $project_desc . "',
                    no_of_samples=" . $nof_samples . ",
                    samples_name='" . $samples_name . "',
                    samples_desc='" . $samples_desc . "',
                    rta_product_data_ids='" . $rta_product_ids . "',
                    decision_criteria='" . $decision_criteria . "',
                    next_step='" . $next_step . "',
                    attributes='" . $attr_to_test . "',
                    special_requirements='" . $special_req . "',
                    " . $sql_state . "
                    
                    location='" . $location . "',
                    cancel_reason='" . $cancel_reason . "'
            WHERE   id=" . $id; //time='" . $time . "',
        $this->db->query($sql);

        $sql = "
            INSERT
            INTO    rta_admin_history
            SET     user_id=" . $this->session->id . ",
                    rta_form_id=" . $id . ",
                    data='" . base64_encode(serialize($data)) . "',
                    created='" . $this->configXY->DATE . "'";
        $this->db->query($sql);
        
        $sql = "
            SELECT  *
            FROM    alerts
            WHERE   user_id=" . $data['requested_by_id'] . "
            AND     rta_form_id=" . $id . "
            AND     state=" . $state;
        $query = $this->db->query($sql);
        if(! $query->num_rows()) {
            
            $status = array(0 => 'Canceled', 1 => 'Approved');        
            $msg = 'Your RTA was already reviewed and was set to: <b>' . $status[$state] . '</b>';
            $sql = "
                INSERT
                INTO    alerts
                SET     user_id=" . $data['requested_by_id'] . ",
                        admin_id=" . $this->session->id . ",
                        content='" . $msg . "',
                        rta_form_id=" . $id . ",
                        state=" . $state . ",
                        `new`=1,
                        created='" . $this->configXY->DATE . "'";
            $this->db->query($sql);
        }
        
        if($state == 1) {
            
            $sql = "
                SELECT  firstname,
                        lastname,
                        username,
                        office_email,
                        email
                FROM    users
                WHERE   id=" . $data['user_id'];
            $query = $this->db->query($sql);
            $po = $query->row();
            
            /* START:
             * Prepare and execute email notification.
             **/
            $mail_fields['notify']          = 'po';
            $mail_fields['action']          = 'rta_approved';
            $mail_fields['rta_id']          = $id;
            $mail_fields['rta_name']        = $data['samples_name'];
            $mail_fields['date_time']       = base64_encode($this->configXY->DATE);
            $mail_fields['po_name']         = $po->firstname . ' ' . $po->lastname;
            $mail_fields['po_username']     = $po->username;
            $mail_fields['po_email']        = (($po->email != '') ? $po->email : $po->office_email);
            $mail_fields['notify_admin']    = 0;

            //xy_email($mail_fields, 'exec.php');
            /* END:
             * Prepare and execute email notification.
             **/
        }
        
        $data['title'] = 'SUCCESS !!! UPDATED';
        $data['msg'] = 'This RTA was updated successfully.';
        $status =  xy_rta_state(true);
        $data['go'] = xy_url('admin/rta'); //xy_doc_root() . 'admin/rta/?target=' . $status[$state];
        
        echo json_encode($data);
    }
    
    public function async_calculate_schedule() {
        
        $this->securePage();
        if(empty($this->session) || empty($_POST)) exit();
        $input = $_POST;
        unset($input['t']);        
        extract($input);
        
        /* Fequency:
         * 
         * d    -> Daily
         * w    -> Weekly
         * 2m   -> Twice a Month
         * m    -> Monthly
         * 30d  -> 30 Days
         */
        
        $schedule = Array($pdate);
        $tmp = $pdate;
        if($frequency != '' && $frequency != 'other' && $frequency != '2m') {
            
            //if($frequency == '30d') $nof_testing_dates = 30;
            
            for($x=1; $x<$nof_testing_dates; $x++) {
                
                if($frequency == 'd') {
                    
                    $tmp = date('m/d/Y', strtotime('+1 Day', strtotime($tmp)));
                    $schedule[] = $tmp;
                }
                else
                if($frequency == 'w') {
                    
                    $tmp = date('m/d/Y', strtotime('+1 Week', strtotime($tmp)));
                    $schedule[] = $tmp;
                }
                else
                if($frequency == 'm') {
                    
                    $tmp = date('m/d/Y', strtotime('+1 Month', strtotime($tmp)));
                    $schedule[] = $tmp;
                }
                else
                if($frequency == '30d') {
                    
                    $tmp = date('m/d/Y', strtotime('+30 Day', strtotime($tmp)));
                    $schedule[] = $tmp;
                }
            }
        } else {
            
            if($frequency == 'other' || $frequency == '2m') {
                
                if(substr_count(other_schedule, ',')) {
                    
                    $schedule = explode(',', other_schedule);
                }
            }            
        }
        
        if(! empty($schedule)) $schedule = implode(', ', $schedule);
        
        $response['schedule_with_age'] = $this->rta_model->doSchedAgeCalculate($schedule);
        $response['schedule'] = $schedule;
        
        echo json_encode($response);
    }
}

/* End of file rta.php */
/* Location: framework/application/controllers/rta.php */