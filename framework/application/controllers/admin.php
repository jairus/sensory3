<?php defined('BASEPATH') or exit('No direct script access allowed');

class Admin extends XY_Controller {
    
    public $level = array(
        1 => 'Administrator',
        2 => 'Project Owner',
        3 => 'Employee',
        4 => 'Immediate Superior',
        5 => 'Non-employee',
        6 => 'Multi Level (2)',
        7 => 'Multi Level (3)');
    
    public function __construct() {
        
        parent::__construct();
        
        $this->load->model('admin_model', '', TRUE);
        
        /* START: AGB Edit, access restrictions. */        
        $try = array('login', 'async_login');
        
        /* Check if NOT logged-in. */
        if(empty($this->session)) {
            
            if(! in_array($this->uri->segment(2), $try)) {
                redirect(base_url('admin/login'));                
            }
        }

        /* Check if NOT Admin. */
        if(! empty($this->session)) {
            
            $user_level = array(1, 7);
            
            if(in_array($this->uri->segment(2), $try) || ! in_array($this->session->level, $user_level)) {
                
                redirect(base_url('home/error'));
                return;        
            }
        }
        /* END: AGB Edit, access restrictions. */
        
        $this->document->loadPlugin('dialogbox');        
    }
    
    /*public function test_mail() {
        
        $config = Array(
            'protocol' => 'smtp',
            'smtp_host' => 'ssl://smtp.googlemail.com',
            'smtp_port' => 465,
            'smtp_user' => 'gb.armande@googlemail.com', //gmail.login@googlemail.com
            'smtp_pass' => 'lgbayanes4ever',
        );
        
        $this->load->library('email', $config);
        $this->email->set_newline("\r\n");

        $this->email->from('gb.armande@gmail.com', 'Sensory Software');
        $this->email->to('tuso@programmerspride.com');

        $this->email->subject(' CodeIgniter Rocks Socks ');
        $this->email->message('Hello World');


        if (!$this->email->send())
            show_error($this->email->print_debugger());
        else
            echo 'Your e-mail has been sent!';  
    }*/
    
    public function login() {

        $this->load->vars($data);
        $data['content'] = $this->getViewFile(__FUNCTION__);
        $this->load->view('main', $data);        
    }
    
    public function async_login() {
        
        $this->securePage();
        
        $input = $_POST;
        unset($input['t']);
        
        $this->admin_model->doLogin($input);
    }
    
    public function field($section = null) {
        
        if($section == null) $section = 'sbu';
        
        if($section == 'spec1' || $section == 'spec2') {
            
            $target = (string) strtolower(trim($this->configXY->URI['target']));
            if($target == '') $target = 'affective';
            $data['target'] = $target;
            
            $number = $section[strlen($section) - 1];
            $sql = "SELECT id,content AS item FROM specifics WHERE number=" . $number . " AND type='" . $target . "' ORDER BY content";

        }
        else
        if($section == 'sbu') {
            
            $sql = "SELECT id,name AS item FROM sbu ORDER BY ordering";
        }
        else
        if($section == 'purpose-of-test') {
            
            $sql = "SELECT id,content AS item FROM test_purpose ORDER BY content";
        }
        else
        if($section == 'location') {
            
            $sql = "SELECT id,name AS item FROM sbu_locations ORDER BY ordering";
        }
        else
        if($section == 'department') {
            
            $sql = "SELECT id,name AS item FROM departments ORDER BY name";
        }
        
        $query = $this->db->query($sql);
        if($query->num_rows()) {
            
            $data['tdata'] = $query->num_rows();
            $data['data'] = $query->result();
        }
            
        $this->configXY->JS_VARS['SECTION'] = "'" . $section . "'";
        $this->configXY->JS_VARS['TARGET'] = "'" . $target . "'";
        
        $data['section'] = $section;
        
        $data['content'] = $this->getViewFile(__FUNCTION__, $data);
        $this->load->view('main', $data);
        
    }
    
    public function async_field() {
        
        $this->securePage();
        if(empty($_POST) || empty($this->session)) exit();
        
        $input = $_POST;
        unset($input['t']);
        
        array_walk($input, 'xy_input_clean_up_byref');
        extract($input);
        
        if($section == '') $section = 'sbu';
        
        if($to_delete != '') {
            
            if($section == 'sbu') $tbl = 'sbu';
            elseif($section == 'purpose-of-test') $tbl = 'test_purpose';
            else
            if($section == 'spec1' || $section == 'spec2') {
                
                $tbl = 'specifics';
                $sql_append = ' AND number=' . $section[strlen($section) - 1];
            }
            elseif($section == 'location') $tbl = 'sbu_locations';
            elseif($section == 'department') $tbl = 'departments';
            
            $sql = "DELETE FROM " . $tbl . " WHERE id IN(" . $to_delete . ")" . $sql_append;
            $this->db->query($sql);
        }
        
        if($section == 'sbu') {
            $tbl = 'sbu';
            $field = 'name';
        }
        else
        if($section == 'purpose-of-test') {
            $tbl = 'test_purpose';
            $field = 'content';
        }
        else
        if($section == 'spec1' || $section == 'spec2') {

            $tbl = 'specifics';
            $field = 'content';            
        }
        else
        if($section == 'location') {

            $tbl = 'sbu_locations';
            $field = 'name';
        }
        elseif($section == 'department') {
            
            $tbl = 'departments';
            $field = 'name';
        }
        
        if($to_edit != '') {
            
            if(substr_count($to_delete, ',')) $to_delete = explode(',', $to_delete);
            else $to_delete = array($to_delete);
            
            parse_str($to_edit, $output);            
            $to_edit_ids = array_keys($output);
            
            $to_edit_ids = array_diff($to_edit_ids, $to_delete);
            
            foreach($to_edit_ids as $id) {
                
                if($section == 'spec1' || $section == 'spec2') { $sql_append = ' AND number=' . $section[strlen($section) - 1]; }
                
                $sql = "UPDATE " . $tbl . " SET " . $field . "='" . xy_input_clean_up($output[$id]) . "' WHERE id=" . $id . $sql_append;
                $this->db->query($sql);
            }
        }
        
        if($to_add != '') {
            
            $to_add = explode(',', $to_add);
            $sql_insert = array();
            $sorting = 0;
            
            foreach($to_add as $value) {
                
                $value = trim($value);
                if($value != '') {
                    
                    $sql = "SELECT id FROM " . $tbl . " WHERE " . $field . "='" . $value . "'";
                    $query = $this->db->query($sql);
                    
                    if(! $query->num_rows()) {
                        
                        if($section == 'sbu' || $section =='location') {
                            
                            if($sorting == 0) {
                                
                                $sql = "SELECT MAX(`ordering`) AS `sorting` FROM " . $tbl;
                                $query = $this->db->query($sql);
                                if($query->num_rows()) $sorting = (double) $query->row()->sorting;
                                
                            }
                            
                            $sorting++;
                            $sql_sorting = "," . $sorting;
                        }
                        else
                        if($section == 'spec1' || $section == 'spec2') {
                            
                            $sql_type_and_num = ",'" . $target . "'," . $section[strlen($section) - 1];
                        }
                        
                        $sql_insert[] = "('" . $value . "'" . $sql_sorting . "," . $this->session->id . ",'" . $this->configXY->DATE . "'" . $sql_type_and_num . ")";                        
                    }
                }
            }
            
            if(count($sql_insert)) {
                
                $sql = "INSERT INTO " . $tbl . " (" . $field . (($sql_sorting != '') ? ',ordering' : '') . ",user_id,created" . (($sql_type_and_num != '') ? ',`type`,`number`' : '') . ") VALUES " . implode(',', $sql_insert);
                $this->db->query($sql);
            }
            
            $msg = "You have added some items in this area.";
        }
        
        $data['title'] = 'GREAT !!!';
        $data['msg'] = (($msg != '') ? $msg : "You have successfully updated some fields in this area.");
        $data['go'] = xy_url('admin/field/' . $section . (($target != '') ? ('/?target=' . $target) : ''));
        
        echo json_encode($data);
    }
    
    /*public function async_rta_edit() {
        
        $this->securePage();
        if(empty($_POST) || empty($this->session)) exit();
        
        $input = $_POST;
        unset($input['t']);
        
        array_walk($input, 'xy_input_clean_up_byref');
        extract($input);
        
        if($id == 0) exit();
        
        /* Load Model to use. */
        /*$this->load->model('po_model', '', true);
        
        if($location == 'other' && $loc_other != '') {
            
            /* Passed "loc other" might already exists but failed to be validated by JavaScript. */
            /*$sql = "
                SELECT  *
                FROM    sbu_locations
                WHERE   name='" . $loc_other . "'";
            $query = $this->db->query($sql);
            
            $tmp = $query->row();
            if(empty($tmp)) {
                
                /* Insert SBU Location so it will be available on the next instance. */
                /*$sql = "
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
        /*if($sbu_other != '') {
            
            $sbu = $this->po_model->add_sbu($sbu_other, $this->session->id);
        }
        
        if($spec1_other != '') {
            
            if($tot == 'micro') {
                
                $spec1 = array();
                if(substr_count($spec1_other, ',')) $tmp = explode(',', $spec1_other);
                else $tmp = array($spec1_other);

                foreach($tmp as $value) { $spec1[] = $this->po_model->add_specific($tot, 1, $value, $this->session->id); }                
                $spec1 = implode(',', $spec1);
                
            } else $spec1 = $this->po_model->add_specific($tot, 1, $spec1_other, $this->session->id);            
        }
        
        if($spec2_other != '') {
            
            if($tot == 'micro') {
                
                $spec2 = array();
                if(substr_count($spec2_other, ',')) $tmp = explode(',', $spec2_other);
                else $tmp = array($spec2_other);

                foreach($tmp as $value) { $spec2[] = $this->po_model->add_specific($tot, 2, $value, $this->session->id); }
                $spec2 = implode(',', $spec2);
                
            } else $spec2 = $this->po_model->add_specific($tot, 2, $spec2_other, $this->session->id);
        }
        
        if($product_data != '') {
            
            $sql_append_row = '';
            $tmp = explode('[=AXL_R=]', $product_data);
            $rta_product_ids = '';
            
            foreach($tmp as $value) {
                
                if(! empty($value)) {
                    
                    $sql_append_data = "";
                    $product = explode('[=AXL_D=]', $value);
                    for($x=0; $x<=7; $x++) {

                        if($x == 2 || $x == 3) {
                            
                            $product[$x] = ($product[$x] != '') ? date('Y-m-d', strtotime($product[$x])) : '0000-00-00';
                        }
                        
                        $sql_append_data .= ",'" . $product[$x] . "'";
                    }
                    
                    $sql = "
                        INSERT
                        INTO    rta_product_data(`variables`,`code`,`pd`,`cu`,`supplier`,`batch_weight`,`quantity`,`others`)
                        VALUES(" . substr($sql_append_data, 1) . ")";
                    $this->db->query($sql);
                    $rta_product_ids .= ',' . $this->db->insert_id();
                }
            }
            
            if($rta_product_ids != '') $rta_product_ids = substr($rta_product_ids, 1);
        }
        
        /* Add purpose of test when "other field" was filled-in. */
        /*$this->po_model->add_test_purpose($tpurpose_other, $this->session->id, $tpurpose);
        
        /* Add User (Approved by) when "other field" was filled-in. */
        /*if($approved_by_other != '') {
            
            $approved_by = $this->po_model->add_approved_by($approved_by_other, $this->session->id);
        }
        
        $schedule = $calculated_schedule;
        
        $state = (int) $state;
        
        if($state > 0) {
            $sql_state = " state=" . ($state - 1) . ", ";
        }
        
        $state--;
        /* If chosed to "approve", then set the final schedule. */
        /*if($state == 1) {
            
            if($nof_testing_dates > 1) {
                
                if($frequency == '2m' || $frequency == 'other') $schedule = $other_schedule;
                else $schedule = $calculated_schedule;

            } else $schedule = date('m/d/Y', strtotime($pdate));
        }
        
        /* Admin edited the original tentative_schedule. */
        /*if($tentative_schedule != '') {
            
            $tmp = explode(',', $tentative_schedule);
            $tmp2 = array();
            if($tentative_schedule_del != "") $tentative_schedule_del = explode(',', $tentative_schedule_del);
            else $tentative_schedule_del = array();
            
            for($x=0, $y = count($tmp); $x<$y; $x++) {
                
                if(! in_array(($x + 1), $tentative_schedule_del)) {
                    $tmp2[] = $tmp[$x];
                }                
            }
            
            //$schedule = $tentative_schedule;
            $schedule = implode(',', $tmp2);
        }
        
        $sql = "SELECT * FROM rta_forms WHERE id=" . $id;
        /*$data = $this->db->query($sql)->row_array();
        
        $rta_no_flag = '';
        /* START: RTA Number. */
        /*if(substr_count($schedule, ',')) {
            
            $schedule_arr = explode(',', $schedule);
            $rta_no_flag = 'M';
            
        } else $schedule_arr = array($schedule);
        
        $rta_no_date = date('Y-m-d', strtotime($schedule_arr[0]));
        $sql = "SELECT MAX(rta_no_count) AS rta_no_count_max FROM rta_forms WHERE rta_no_date='" . $rta_no_date . "'";
        $query = $this->db->query($sql);
        if($query->num_rows()) $rta_no_count = (int) $query->row()->rta_no_count_max;
        else $rta_no_count = 0;
        $rta_no_count++;
        /* END: RTA Number. */
        
        /*$sql = "
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
        
        $data['title'] = 'SUCCESS !!! UPDATED';
        $data['msg'] = 'This RTA was updated successfully.';
        $status =  xy_rta_state(true);
        $data['go'] = xy_url('admin/rta'); //xy_doc_root() . 'admin/rta/?target=' . $status[$state];
        
        echo json_encode($data);
    }*/
    
    /*public function rta_edit($id) {
        
        $this->document->loadPlugin('datepicker');
        
        $this->load->model('po_model', '', true);
        
        if($id == 0) return $this->_404_();
        
        $this->configXY->JS_VARS['[var=false]RTA_FORM.id'] = $id;
        
        /* Get necessary data for a specified RTA. */
        /*$rta = $this->po_model->loadRTA($id, true);
        
        if($rta->state === 0) return $this->_404_(); /* Don't edit when already approved. */
        
        /*$this->configXY->JS_VARS['[var=false]RTA_FORM.state'] = $rta->state;
        
        $rta->date_filed = date('\<\b\>m/d/Y\<\/\b\> \a\t \<\b\>h:iA\<\/\b\>', strtotime($rta->date_filed));
        
        $data = $this->po_model->loadRTAFields($this->session, $rta);
        $data['rta'] = $rta;
        
        $sql = "SELECT * FROM sbu_locations";
        $query = $this->db->query($sql);
        if($query->num_rows()) {
            
            $this->configXY->JS_VARS['SBU_LOC'] = 'new Array()';
            $x = 0;
            foreach($query->result() as $row) {

                /* Create JS variables. */
                /*$this->configXY->JS_VARS['[var=false]SBU_LOC[' . $x . ']'] = "'" . $row->name . "'";
                $x++;
                
                /* Create select options. */
                /*$html .= '<option value="' . $row->id . '"' . (($row->id == $rta->location) ? ' selected="selected"' : (($row->id == 3) ? ' selected="selected"' : '')) . '>' . $row->name . '</option>'; //' . (($row->id == $rta->sbu) ? ' selected="selected"' : '') . '
            }
            
            if($html != '') {
                $html = '<select id="location" onchange="GBL.toggle_other_field(this,\'loc_other_wrapper\',\'loc_other\')">' .
                '<option value="">Select:</option>' . $html .
                '<option value="other">Others</option>' .
                '</select>';
            }
            
            $data['sbu_loc'] = $html;
        }
        
        $schedule_tentative = ($rta->schedule == '') ? $rta->schedule_tentative : $rta->schedule;
        $this->configXY->JS_VARS['TENTATIVE_SCHED_ORIG'] = "'" . str_replace(' ', '', $schedule_tentative) . "'";
        
        $this->configXY->JS_VARS['[var=false]RTA_FORM.schedule_tentative'] = json_encode(explode(', ', $rta->schedule_tentative));
        
        if(substr_count($schedule_tentative, ',')) {
            
            $rta->schedule_tentative = explode(',', $schedule_tentative);
            
        } else $rta->schedule_tentative= array($schedule_tentative);
        
        $age = 0; $tmp2 = array();
        $total = count($rta->schedule_tentative);
        for($x=0; $x<$total; $x++) {

            $date = trim($rta->schedule_tentative[$x]);
            if($x > 0) {

                $start = $rta->schedule_tentative[$x -1];
                $end = $rta->schedule_tentative[$x];

                list($year, $month, $day) = xy_date_diff($start, $end);

                $age += $day;

            } else $age = 0;
            
            $tmp2[] = array('date' => $date, 'age' => $age . ' Day' . (($age > 1) ? 's' : ''));
            /*?>
            <tr><td><?php echo $tmp?> <?php echo (($tmp > 0) ? 'Day' : '')?><?php echo (($tmp > 1) ? 's' : '')?></td>
                <td><input id="tentative_sched_<?php echo ($x + 1)?>" type="text" value="<?php echo trim($date)?>" style="width: 90px; text-align: right" readonly="readonly" /></td>
                <td><span id="tentative_sched_tmp_<?php echo ($x + 1)?>"></span> </td>
                <td style="padding-left: 5px">
                    <input type="checkbox" name="delete_date" />
                    <img src="<?php echo xy_url('media/images/16x16/x.png')?>" />
                </td>
            </tr><?php*/
        /*}
        
        //$this->configXY->JS_VARS['[var=false]RTA_FORM.schedule_approved'] = json_encode(explode(', ', $rta->schedule));
        /*$this->configXY->JS_VARS['[var=false]RTA_FORM.schedule_approved'] = json_encode($tmp2);
        
        
        $this->configXY->JS_VARS['TENTATIVE_SCHED_COUNT'] = count($rta->schedule_tentative);
        
        $data['content'] = $this->getViewFile(__FUNCTION__, $data);
        $this->load->view('main', $data);
    }*/
    
    public function rta() {
        
        $this->document->loadPlugin('jqgrid');
        
        /* Administrator; Project Owner; Multi Level (2); Multi Level (3); Employee */
        $data['access_level'] = array(1, 2, 6, 7);
        
        $data['content'] = $this->getViewFile(__FUNCTION__, $data);
        $this->load->view('main', $data);
    }
    
    public function rta_view($id) {
        
        if($id == 0)
            return $this->_404_ ();
        
        $this->load->model('rta_model', '', true);
        
        $rta = $this->rta_model->doLoadDetail($id, 'admin', true);
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

    public function user(){
        
        $data['level'] = $this->level;
        $data['users'] = $this->admin_model->getUsers();
        $data['editPNG'] = xy_doc_root() . 'MISC/b_edit.png';
        $data['deletePNG'] = xy_doc_root() . 'MISC/b_drop.png';
        
        $this->document->loadPlugin('jqgrid');
        
        $data['content'] = $this->getViewFile(__FUNCTION__, $data);
        $this->load->view('main', $data);
    }
    
    public function async_user_ae_field(){
        
        $this->securePage();
        if(empty($this->session)) exit();
        
        $input = $_POST;
        unset($input['t']);
        
        array_walk($input, 'xy_input_clean_up_byref');
        extract($input);
        
        if($id) {
            
            $data['user'] = $this->admin_model->doUserLoad($id);
            if($data['user']->birthdate != '0000-00-00') $data['user']->birthdate = date('m/d/Y', strtotime($data['user']->birthdate));
            else $data['user']->birthdate = '';
        }
        
        $superior_choices = $this->admin_model->getUsers('id,level,username,firstname,lastname', ' AND `level`!=5 ');
        if($superior_choices['total']) {
            
            for($x=0; $x<$superior_choices['total']; $x++) {
                
                if($superior_choices['data'][$x]->username != '') $name = $superior_choices['data'][$x]->username;
                else $name = $superior_choices['data'][$x]->firstname . ' ' . $superior_choices['data'][$x]->lastname;
                
                $superior_choices_html .= '<option value="' . $superior_choices['data'][$x]->id . '"' . (($superior_choices['data'][$x]->id == $data['user']->superior_id) ? ' selected="selected"' : '') . '>' . $name . ' --- ' . $this->level[$superior_choices['data'][$x]->level] . '</option>';
                
            }
            if($superior_choices_html != '') { $superior_choices_html = '<select id="superior_choice"><option value="">Select:' . str_repeat('&nbsp;', 20) . '</option>' . $superior_choices_html . '</select>'; }
        }
        
        $data['superior_choices_html'] = $superior_choices_html;
        $data['level'] = $this->level;

        $this->load->view('admin/user_ae_field', $data);
    }
    
    public function async_user_delete(){ /* AXL */
        
        $this->securePage();
        if(empty($_POST) || empty($this->session)) exit();
        
        $input = $_POST;
        unset($input['t']);
        
        $this->admin_model->doUserDelete($input);
        
        echo 'Ok.';
    }
    
    public function async_user_ae() { /* AXL */
        
        $this->securePage();
        if(empty($_POST) || empty($this->session)) exit();
        
        $input = $_POST;
        unset($input['t']);
        
        $data = $this->admin_model->doUserAE($input);
        
        echo json_encode($data);
    }
    
    public function async_user_list() { /* AXL */
        
        if(empty($this->configXY->URI) || empty($this->session)) exit('No data.');
        $input = $this->configXY->URI;
        unset($input['t']);
        
        $this->admin_model->doUserList($input);        
    }
    
    public function hardware() { /* AXL */
        
        $sql = "
            SELECT  *
            FROM    stations
            WHERE   sensorium=1";
        $query = $this->db->query($sql);
        if($query->num_rows()) {
            
            $s1 = $query->result();
            foreach ($s1 as $value) {
                $sensorium1[$value->number] = $value->ip;
            }
            
            $data['sensorium1'] = $sensorium1;
            
        } else $data['sensorium1'] = array();
        
        $sql = "
            SELECT  *
            FROM    stations
            WHERE   sensorium=2";
        $query = $this->db->query($sql);
        if($query->num_rows()) {
            
            $s2 = $query->result();
            foreach ($s2 as $value) {
                $sensorium2[$value->number] = $value->ip;
            }
            
            $data['sensorium2'] = $sensorium2;
            
        } else $data['sensorium2'] = array();
        
        $data['content'] = $this->getViewFile(__FUNCTION__, $data);
        $this->load->view('main', $data);
    }
    
    public function async_hardware() { /* AXL */
        
        $this->securePage();
        if(empty($_POST) || empty($this->session) || $this->session->level != 1) exit();
        
        $input = $_POST;
        unset($input['t']);
        
        array_walk($input, 'xy_input_clean_up_byref');
        extract($input);

        parse_str($sensorium1, $tmp1);
        parse_str($sensorium2, $tmp2);
        
        foreach($tmp1 as $station_no => $ip) {
            
            $sn = str_replace('sn', '', $station_no);
            
            $sql = "
                SELECT  id
                FROM    stations
                WHERE   number=" . $sn . "
                AND sensorium=1";
            
            $query = $this->db->query($sql);
            if($query->num_rows()) $sql = "UPDATE stations SET ip='" . $ip . "',state='0' WHERE number=" . $sn . " AND sensorium=1";
            else $sql = "INSERT INTO stations SET ip='" . $ip . "',number=" . $sn . ",sensorium=1";
            
            $this->db->query($sql);
        }
        
        foreach($tmp2 as $station_no => $ip) {
            
            $sn = str_replace('sn', '', $station_no);
            
            $sql = "
                SELECT  id
                FROM    stations
                WHERE   number=" . $sn . "
                AND sensorium=2";
            
            $query = $this->db->query($sql);
            if($query->num_rows()) $sql = "UPDATE stations SET ip='" . $ip . "',state='0' WHERE number=" . $sn . " AND sensorium=2";
            else $sql = "INSERT INTO stations SET ip='" . $ip . "',number=" . $sn . ",sensorium=2";
            
            $this->db->query($sql);
        }
    }
    
    public function async_station_state_get() {
        
        $this->securePage();
        $station = $sensorium1 = $sensorium2 = array();
        
        $sql = "SELECT * FROM stations WHERE sensorium=1";
        $query = $this->db->query($sql);
        if($query->num_rows()) {
            
            $rows = $query->result();
            foreach($rows as $row) { $sensorium1[] = array('number' => $row->number, 'state' => $row->state); }
        }
        
        $sql = "SELECT * FROM stations WHERE sensorium=2";
        $query = $this->db->query($sql);
        if($query->num_rows()) {
            
            $rows = $query->result();
            foreach($rows as $row) { $sensorium2[] = array('number' => $row->number, 'state' => $row->state); }
        }
        
        $station['one'] = $sensorium1;
        $station['two'] = $sensorium2;
        
        echo json_encode($station);
    }
}

/* End of file admin.php */
/* Location: framework/application/controllers/admin.php */