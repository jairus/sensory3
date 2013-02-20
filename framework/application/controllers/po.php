<?php defined('BASEPATH') or exit('No direct script access allowed');

/* 
 * Title : PO (Project Owner) Controller Class
 * Author: Armande Bayanes (tuso@programmerspride.com)
 * Description: Handles Logging-in and Registration, etc ...
 **/

class Po extends XY_Controller {
    
    public $access_level = array(1, 2, 4, 6, 7);
    
    function __construct() {
        
        parent::__construct();
        
        $this->load->model('po_model', '', true);
        $try = array('login', 'async_login');
        
        /* Check if NOT logged-in. */
        if(empty($this->session)) {
            
            if(! in_array($this->uri->segment(2), $try)) {
                redirect(base_url('po/login'));
            }
        }
        
        /* If already logged-in. */
        if(! empty($this->session)) {
            
            if(in_array($this->uri->segment(2), $try) || ! in_array($this->session->level, $this->access_level)) {
                
                redirect(base_url('home/error'));
            }
        }
        
        /* Plugin for Dialogbox. */
        $this->document->loadPlugin('dialogbox');
        
        if($this->uri->segment(3) == 'create') {
            
            /* Plugin for Dialogbox. */
            $this->document->loadPlugin('datepicker');
        }
    }
    
    public function login() {
        
        $data['content'] = $this->getViewFile(__FUNCTION__);        
        $this->load->view('main', $data);
    }
    
    public function async_login() {
        
        /* Secure this page from unauthorized remote access. */
        $this->securePage();
        
        if(empty($_POST)) exit('No data.');
        $input = $_POST;
        unset($input['t']);
        
        $this->po_model->doLogIn($input);
    }
    
    /*public function rta($section) {
        
        $this->configXY->JS_VARS['SUB_SECTION'] = "'" . $section . "'";
        $uri = $this->configXY->URI;
        extract($uri);
            
        if($section == 'create') {
            
            /* Get necessary data when chosed to re-use an RTA. */
            /*if($id > 0) { $rta = $this->po_model->loadRTA($id); }
            
            $data = $this->po_model->loadRTAFields($this->session, $rta);
            $data['rta'] = $rta;
            $data['date_filed'] = date('m/d/Y', strtotime($this->configXY->DATE));
        }
        else
        if($section == 'history') {
            
            $state = xy_rta_state();
            
            if($target == '') $target = 'all';
            else $sql_state = " AND r.state=" . $state[$target] . " ";
            
            if($target == 'all') $sql_state = '';
            
            $data['target'] = $target;
            $this->configXY->JS_VARS['[var=false]RTA_FORM.target'] = "'" . $target . "'";
            
            if($search != '') {
                
                $sql_append = " AND (r.samples_name LIKE '%" . $search . "%' OR r.samples_desc LIKE '%" . $search . "%') ";
                $data['search'] = $search;
            }
            
            $sql = "
                SELECT  h.*,
                        r.state,
                        r.schedule,
                        r.samples_name,
                        r.samples_desc
                FROM    rta_po_history h
                LEFT
                JOIN    rta_forms r
                ON      h.rta_form_id=r.id
                WHERE   h.user_id=" . $this->session->id . "
                " . $sql_append . "
                " . $sql_state . "
                ORDER
                BY      h.created
                DESC";
            
            $query = $this->db->query($sql);
            
            if($query->num_rows()) {
                
                $rpp = 10;
                if(intval($page) <= 0) $page = 1;
                $start = ($page - 1) * $rpp;
                $maxpage = ceil($query->num_rows() / $rpp);

                $sql .= " LIMIT " . $start . "," . $rpp;
                $query = $this->db->query($sql);

                $uri = $_SERVER['REQUEST_URI'];
                $page_occurence = (int) strpos($uri, '&page');
                if($page_occurence == 0) {

                    $page_occurence = (int) strpos($uri, '?page');
                    if($page_occurence == 0) $page_occurence = strlen($uri);
                }

                $uri = substr($uri, 0, $page_occurence);
                if($uri[strlen($uri) - 1] != '/') $uri .= '/';

                for($x=1; $x<= $maxpage; $x++) {

                    if($x == $page) $pages .= ', <b style="font-size: 18px">' . $x . '</b>';
                    else $pages .= ', <a title="page ' . $x . '" href="' . $uri . ((substr_count($uri, '?') == 0) ? '?' : '&') . 'page=' . $x . '">' . $x . '</a>';
                }

                if(($data['thistory'] = $query->num_rows()) > 0) {

                    $data['paging'] = 'Page: ' . substr($pages, 2);
                    $data['history'] = $query->result();                
                }
            }
        }
        
        $data['access_level'] = $this->access_level;
        
        $data['section'] = $section;
        $data['content'] = $this->getViewFile(__FUNCTION__, $data);        
        $this->load->view('main', $data);
    }*/
    
    /*public function rta_view($id) {
        
        if($id == 0) return $this->_404_ ();
        
        $this->load->model('po_model', '', true);
        
        $rta = $this->po_model->loadRTA($id, true, true);
        if(empty($rta)) return $this->_404_ ();

        $rta->date_filed = date('\<\b\>m/d/Y\<\/\b\> \a\t \<\b\>h:iA\<\/\b\>', strtotime($rta->date_filed));
        $rta->date_preferred = date('m/d/Y', strtotime($rta->date_preferred));
        
        $this->load->model('admin_model', '', true);
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
            /*$data['schedule'] = $rta->schedule; /* Final schedule. */
            
        /*} else {
            
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
    }*/
    
    public function rta_by_owner() { /* AXL */
        
        $this->document->loadPlugin('jqgrid');
        $data['access_level'] = $this->access_level;
        
        $data['content'] = $this->getViewFile(__FUNCTION__, $data);
        $this->load->view('main', $data);
    }
    
    /*public function rta_create($id = 0) {
        
        $this->document->loadPlugin('datepicker');
        
        //$this->configXY->JS_VARS['SUB_SECTION'] = "'" . $section . "'";
        //$uri = $this->configXY->URI;
        //extract($uri);
        
        /* Get necessary data when chosed to re-use an RTA. */
        /*if($id > 0) { $rta = $this->po_model->loadRTA($id); }

        $data = $this->po_model->loadRTAFields($this->session, $rta);
        $data['rta'] = $rta;
        $data['date_filed'] = date('m/d/Y', strtotime($this->configXY->DATE));
        $data['access_level'] = $this->access_level;
        
        $data['content'] = $this->getViewFile(__FUNCTION__, $data);
        $this->load->view('main', $data);
    }
*/

    /*public function async_calculate_schedule() {
        
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
        
        /*$schedule = Array($pdate);
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
        
        echo $schedule;
    }
    
    public function async_create_rta() {
        
        $this->securePage();
        if(empty($_POST) || empty($this->session)) exit();
        
        $input = $_POST;
        unset($input['t']);
        
        array_walk($input, 'xy_input_clean_up_byref');
        extract($input);
        
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
        
        if($nof_testing_dates > 1) {
            
            /* Note: Only the tentative schedule will be filled-in with this action. */
            /*if($frequency == '2m' || $frequency == 'other') $schedule_tentative = $other_schedule;
            else $schedule_tentative = $calculated_schedule;
            
        } else $schedule_tentative = date('m/d/Y', strtotime($pdate));
        
        $sql = "
            INSERT
            INTO    rta_forms
            SET     user_id=" . $this->session->id . ",
                    date_filed='" . $this->config->config['XY']->DATE . "',
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
        $this->db->query($sql);
        $rta_form_id = $this->db->insert_id();
        
        /*
         * rta_po_history
         * 
         * 1 => Draft
         * 2 => Sent
         * 
         **/
        /*$data = array(
            "rta_form_id" => $rta_form_id,
            "date_filed" => $this->config->config['XY']->DATE,
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
                    created='" . $this->config->item('XY')->DATE . "'";
        $this->db->query($sql);
        
        //$sql = str_replace('rta_forms', 'rta_forms_copy', $sql);
        //$this->db->query($sql);
        
        $data['title'] = 'GREAT !!! SUBMITTED';
        $data['msg'] = 'Your RTA was filed successfully. Please wait for approval.';
        $data['go'] = xy_url('po/rta_by_owner');
        
        echo json_encode($data);
    }*/
}

/* End of file po.php */
/* Location: framework/application/controllers/po.php */