<?php defined('BASEPATH') or exit('No direct script access allowed');

class Exam extends XY_Controller {
    
    public function __construct() {
        
        parent::__construct();
        
        $this->load->model('exam_model', '', true);
        
        //$try = array('login', 'async_login');
        
        /* Check if NOT logged-in. */
        /*if(empty($this->session)) {
            
            if(! in_array($this->uri->segment(2), $try)) {
                redirect(base_url('admin/login'));                
            }
        }*/
    }
    
    public function index($date = NULL) {
        
        $stations = $this->exam_model->getStationsWithQ();
        $station_number = $this->exam_model->getIfIPHooked();
        
        $rta_id = 0;
        $batch = $screen_code = '';
        
        for($x=0, $y = count($stations); $x<$y; $x++) {
            
            $station = $stations[$x];
            
            if(in_array($station_number, $station['station'])) {
                
                $rta_id = $station['rta'];
                $screen_code = $station['code'];
                $batch = $station['batch'];
                
                break;                    
            }
        }
        
        if(! $rta_id) exit('Station is not assigned to any questionnaire yet.');
        else {
            
            if(! $date) $date = $this->configXY->TODAY;
            redirect(base_url('exam/welcome/' . $date));
            
            //redirect(base_url('exam/actual/' . $rta_id . '/' . $screen_code . '/?batch=' . str_replace(' ', '-', $batch)));
        }
        
        /*$station = $this->exam_model->getIfStationInQ($rta_id, $screen_code);
        
        echo '<pre style="text-align: left">';
        print_r($stations);
        echo '</pre>';*/
        
        $data['content'] = $this->getViewFile(__FUNCTION__, $data);
        $this->load->view('exam', $data);
    }
    
    public function welcome($date = null) {
        
        if(! $date) return;
        
        $this->exam_model->date = $date;
        
        $this->configXY->JS_VARS['[var=false]EXAM.queue'] = json_encode($this->exam_model->getQueue());
        $data['exam'] = $this->exam_model->getDetail($date);
        
        $data['content'] = $this->getViewFile(__FUNCTION__, $data);
        $this->load->view('exam', $data);
    }
    
    public function preview($rta_id = 0, $screen_code = 0, $screen_count = 0) { /* AXL */
        
        $sql = "SELECT * FROM q WHERE rta_id=" . $rta_id;
        $query = $this->db->query($sql);
        if(! $query->num_rows()) { redirect(base_url('home/error')); }
                
        $q = $query->row();
        
        $data['code_1'] = xy_code_get($q->codes, 'primary');
        $data['code_2'] = xy_code_get($q->codes, 'secondary');
        
        //parse_str($q->code_control, $controls);
        //print_r($controls);
        
        parse_str($q->code_control, $controls);
        $controls = array_values($controls);

        $tmp = array_merge($data['code_1'], $data['code_2']);
        $experiments = array();

        foreach($tmp as $c) { if(! in_array($c, $controls)) { $experiments[] = $c; } }
        
        $data['controls'] = $controls;
        $data['experiments'] = $experiments;
        
        $this->load->model('sensory_model');
        if($screen_count == 0) $screen_count = 1;
        
        $screens = $this->sensory_model->getScreensFor($rta_id, $screen_code); /* From DB. */
        $screen = $screens[$screen_count];
        
        foreach($screen['items'] as $item) {
            
            if(substr_count($item['type'], 'ranking')) {
                
                $this->configXY->CSS[] = APPPATH . 'views/exam/ranking.css';
                break;
            }
            else
            if(substr_count($item['type'], 'descriptive')) {
                
                $this->document->loadPlugin('slider');
                break;
            }
        }
        
        $one_ss_only = $this->sensory_model->getSpecifics_withOneSS();
        $sql = "SELECT type_of_test,specific_1,specific_2 FROM rta_forms WHERE id=" . $rta_id;
        $query = $this->db->query($sql);
        if(! $query->num_rows()) { redirect(base_url('home/error')); }
        $rta = $query->row();
        
        /* START: See if entitled to have more questionnaires. */
        if($rta->type_of_test == 'affective') $check = in_array($rta->specific_2, $one_ss_only);
        elseif($rta->type_of_test == 'analytical') $check = in_array($rta->specific_1, $one_ss_only);

        $page_to_go = 'EXAM.close()';
        
        if(! $check) {
            
            $this->configXY->JS_VARS['[var=false]EXAM.one_ss_only'] = 'false';
            
            $current_code_pos = array_search($screen_code, $data['code_1']) + 1;
            if(($current_code_pos) < count($data['code_1'])) {
                
                $page_to_go = 'EXAM.screen_code=' . $data['code_1'][$current_code_pos] . '; EXAM.go()';
                $this->configXY->JS_VARS['[var=false]EXAM.screen_code_next'] = $data['code_1'][$current_code_pos];                
            }
        }
        /* END: See if entitled to have more questionnaires. */
        
        $screen_total = count($screens);
        
        if($screen_count >=2 && $screen_count <= $screen_total) {
            
            $html_prev = '<input type="button" onclick="EXAM.go(' . ($screen_count - 1) . ')" value="Previous" />';
        }
        
        if($screen_count < $screen_total) $html_next = '<input id="btn_next" type="button" onclick="EXAM.go(' . ($screen_count + 1) . ')" value="Next" />';
        else $html_next = '<input id="btn_next" type="button" onclick="' . $page_to_go . '" value="Finish" />';
        
        $this->configXY->JS_VARS['[var=false]EXAM.rta_id'] = $rta_id;
        $this->configXY->JS_VARS['[var=false]EXAM.screen_code'] = "'" . $screen_code . "'";
        
        $data['rta_id'] = $rta_id;
        $data['screen_code'] = $screen_code;
        
        $data['screen'] = $screen;
        $data['screen_total'] = $screen_total;
        $data['button_prev'] = $html_prev;
        $data['button_next'] = $html_next;
        
        $data['content'] = $this->getViewFile(__FUNCTION__, $data);
        $this->load->view('preview', $data);
    }
    
    public function actual($rta_id = 0, $screen_code = 0, $screen_count = '') { /* AXL */
        
        //echo is_numeric('analytical_10');
        //echo is_numeric('10');
        
        $this->exam_model->date = $this->configXY->URI['date'];
        
        /* START: See if ip is hooked in a pc/station. */
        if(($station_number = $this->exam_model->getIfIPHooked()) == 0) {
            
            $data['content'] = $this->exam_model->doMessage('<b>This station number is not properly configured yet.</b><br /><br /><b style="color: #CC0000">You are advised to contact your Administrator.</b>', false);
            $this->load->view('exam', $data);
            return;
        }
        /* END: See if ip is hooked in a pc/station. */
        
        $stations = $this->exam_model->getStationsWithQ();
        /*echo '<pre style="text-align: left">';
        print_r($stations);
        echo '</pre>';*/
        
        /* START: See if "station number" included in the current Questionnaire. */
        $response = $this->exam_model->getIfStationInQ($rta_id, $screen_code, $station_number, $stations);
        if(! $response) {
            
            $data['content'] = $this->exam_model->doMessage('<b>This station number is not assigned to this Questionnaire yet.</b><br /><br /><b style="color: #CC0000">You are advised to contact your Administrator.</b>', false);
            $this->load->view('exam', $data);
            return;
        }
        /* END: See if "station number" included in the current Questionnaire. */
        
        /* START: See parameter 'finish'.
         * This is passed when all answers are provided.
         **/
        if($screen_count === 'finish') {
            
            if(empty($this->configXY->URI['t'])) redirect(base_url('exam'));
                    
            $t = base64_decode($this->configXY->URI['t']);
//echo strtotime($this->configXY->DATE) .'<'. $t;
//exit();
            if(strtotime($this->configXY->DATE) < $t) {
                
                $data['content'] = $this->exam_model->doMessage('<div style="margin-top: 20px; margin-left: auto; margin-right: auto; width: 70%; text-align: center"><b style="color: #009900">Thank you for taking the exam.</b></div>');
                $this->load->view('exam', $data);
                return;
                
            } else redirect(base_url('exam')); /* Expired time. */
        }
        /* END: See parameter 'finish'.
         * This is passed when all answers are provided.
         **/
        
        $rta_id = (double) $rta_id;
        if($rta_id == 0) redirect(base_url('home/error'));
        
        $q = $this->exam_model->getQ($rta_id);
        if(empty($q)) { redirect(base_url('home/error')); }
        
        $data['code_1'] = xy_code_get($q->codes, 'primary');
        $data['code_2'] = xy_code_get($q->codes, 'secondary');
        
        parse_str($q->code_control, $controls);
        $controls = array_values($controls);

        $tmp = array_merge($data['code_1'], $data['code_2']);
        $experiments = array();

        foreach($tmp as $c) { if(! in_array($c, $controls)) { $experiments[] = $c; } }
        
        $data['controls'] = $controls;
        $data['experiments'] = $experiments;
        
        $this->load->model('sensory_model');
        if($screen_count == 0) $screen_count = 1;
        
        //$screens = $this->sensory_model->getScreensFor($rta_id, $screen_code); /* From DB. */
        $screens = $this->sensory_model->getScreensFor($rta_id); /* From DB. */
        $screen = $screens[$screen_code][$screen_count];
        
        if(! $screen) {

            if($screen_count > 1) $message = '<b style="color: #CC0000">Forbidden access.</b>';
            else $message = '<b>Score-sheet was not built yet. No screens found for this questionnaire.</b><br /><br /><b style="color: #CC0000">You are advised to contact your Administrator.</b>';

            $data['content'] = $this->exam_model->doMessage($message);
            $this->load->view('exam', $data);
            return;
        }

        if(empty($screen['items'])) {

            $data['content'] = $this->exam_model->doMessage('<b>A screen with empty items.</b><br /><br /><b style="color: #CC0000">You are advised to contact your Administrator.</b>');
            $this->load->view('exam', $data);
            return;
        }

        if(! $this->exam_model->getItemState($rta_id, $screen_code)) {
            
            $this->configXY->JS_VARS['[var=false]EXAM.pause'] = 'true';
            $this->configXY->JS_VARS['[var=false]EXAM.rta_id'] = $rta_id;
            $this->configXY->JS_VARS['[var=false]EXAM.screen_code'] = $screen_code;
            
            $this->configXY->JS[] = APPPATH . 'views/exam/actual.js';
            $this->configXY->JS[] = 'media/js/purl.js';
            
            $data['content'] = $this->exam_model->doMessage('<b>This item was assigned to you but it\'s not yet activated or it was paused for a moment.</b><br /><br /><b style="color: #CC0000">You are advised to contact your Administrator.</b>');
            $this->load->view('exam', $data);
            return;
        }
        
        //$data['code_1']
        
        /*foreach($screen['items'] as $item) {
            
            if(substr_count($item['type'], 'ranking')) { 
                
                $this->configXY->CSS[] = APPPATH . 'views/exam/ranking.css'; break;
                
            }
            else
            if(substr_count($item['type'], 'descriptive')) {
                
                $this->document->loadPlugin('slider'); break;
            }
        }*/
        
        $this->func_cssjs_include($screen['items'], 'ranking', 'views/exam/ranking.css', 'css');
        $this->func_cssjs_include($screen['items'], 'descriptive', 'slider', 'plugin');
        $this->func_cssjs_include($screen['items'], 'sqs', 'views/exam/sqs.js', 'js');
        
        $one_ss_only = $this->sensory_model->getSpecifics_withOneSS();
        
        $rta = $this->exam_model->getRTA($rta_id);
        if(empty($rta)) { redirect(base_url('home/error')); }
        
        /* START: See if entitled to have more questionnaires. */
        if($rta->type_of_test == 'affective') $check = in_array($rta->specific_2, $one_ss_only);
        elseif($rta->type_of_test == 'analytical') $check = in_array($rta->specific_1, $one_ss_only);

        $page_to_go = 'EXAM.submit(true)';
        
        if(! $check) {
            
            $this->configXY->JS_VARS['[var=false]EXAM.one_ss_only'] = 'false';
            
            $current_code_pos = array_search($screen_code, $data['code_1']) + 1;
            if(($current_code_pos) < count($data['code_1'])) {
                
                $page_to_go = 'EXAM.screen_code=' . $data['code_1'][$current_code_pos] . '; EXAM.go()';
                $this->configXY->JS_VARS['[var=false]EXAM.screen_code_next'] = $data['code_1'][$current_code_pos];                
            }
        }
        /* END: See if entitled to have more questionnaires. */
        
        //unset($_SESSION['EXAM']);
        //setcookie("EXAM", '', strtotime($this->configXY->DATE) - 31536000, "/");
        
        $this->configXY->JS_VARS['[var=false]EXAM.queue'] = json_encode($this->exam_model->getQueue());
        $this->configXY->JS_VARS['[var=false]EXAM.screen_total'] = count($screens[$screen_code]); /* Screen total of the current rta + code. */
        $this->configXY->JS_VARS['[var=false]EXAM.redirect_on_pause'] = 'false';
        
        if(empty($_SESSION['EXAM'])) {
            
            if(empty($_COOKIE['EXAM'])) {
               
                foreach($screens as $code => $screens_tmp) {

                    $tmp = array();
                    foreach($screens_tmp as $screen_no => $screen_data) { $tmp[$screen_no] = array('id' => $screen_data['id'], 'items' => array_keys($screen_data['items'])); }
                    $_SESSION['EXAM'][$code] = $tmp;
                }
                
                /* START: Get session value from DB if there's any. */
                $exam_session = $this->exam_model->getSession($rta_id, $screen_code, $screen_count);
                if(! empty($exam_session)) { $_SESSION['EXAM'][$screen_code][$screen_count] = $exam_session; }
                /* END: Get session value from DB if there's any. */
                
                $this->exam_model->doUpdateSession();
                
            } else $_SESSION['EXAM'] = unserialize($_COOKIE['EXAM']);
        }
        
        //echo '<pre style="text-align: left">';
        //print_r($_SESSION['EXAM']);
        //echo '</pre>';
        
        //echo $station_number;
        
        
        
        
        
        
        $screen_total = count($screens[$screen_code]);
        
        /* $q->flow tells when "previous button" can only be visible or invisible. */
        if($screen_count >=2 && $screen_count <= $screen_total && $q->flow == 'both') {
            
            $html_prev = '<input type="button" onclick="EXAM.go(' . ($screen_count - 1) . ')" value="Previous" />';            
        }
        
        $data['viewable'] = array('type' => '', 'cmd' => true);
        
        /* Checks previous step (PV) basing from the current step.
         * See if PV was completed.
         **/
        
        $param['screen_code'] = $this->uri->segment(4);
        if(($previous = ($screen_count - 1)) > 0) {
            
            /* See if advancing w/o completing the current step. */
            $param['screen_count'] = $previous;
            list($item_check, $item_total) = $this->exam_model->getNumberOfItemsWithAnswer($param);
            $data['viewable'] = array('type' => 'advancing', 'cmd' => (($item_check == $item_total && ($item_total > 0 && $item_check > 0)) ? true : false));
        }
        
        //echo $item_check, ',', $item_total, '<br/>';
        
        if($q->flow != 'both' && $data['viewable']['cmd']) {
            
            /* See if backing when it's not allowed. */
            $param['screen_count'] = $screen_count;
            list($item_check, $item_total) = $this->exam_model->getNumberOfItemsWithAnswer($param);
            $data['viewable'] = array('type' => 'backing', 'cmd' => (($item_check == $item_total && ($item_total > 0 && $item_check > 0)) ? false : true));
        }
        
        //echo $item_check, ',', $item_total, '<br/>';
        
        if(! $data['viewable']['cmd']) $this->configXY->JS_VARS['[var=false]EXAM.update_flag'] = 'false';

        if($screen_count < $screen_total) $html_next = '<input id="btn_next" type="button" onclick="EXAM.submit()" value="Next" />';
        //else $html_next = '<input id="btn_next" type="button" onclick="' . $page_to_go . '" value="Finish" />';
        else $html_next = '<input id="btn_next" type="button" onclick="EXAM.submit()" value="Finish" />';
        
        $this->configXY->JS_VARS['[var=false]EXAM.rta_id'] = $rta_id;
        $this->configXY->JS_VARS['[var=false]EXAM.screen_code'] = "'" . $screen_code . "'";
        
        $data['rta_id'] = $rta_id;
        $data['screen_code'] = $screen_code;
        $data['screen_count'] = $screen_count;
        
        $data['screen'] = $screen;
        $data['screen_total'] = $screen_total;
        $data['button_prev'] = $html_prev;
        $data['button_next'] = $html_next;
        //unset($_SESSION['EXAM'][$screen_code][$screen_count]['items']);
        $data['content'] = $this->getViewFile(__FUNCTION__, $data);
        $this->load->view('exam', $data);
    }
    
    public function async_unix_timestamp() {
        
        /* Secure this page from unauthorized remote access. */
        $this->securePage();
        
        if(empty($_POST) || empty($this->session)) exit('No data.');
        
        echo base64_encode(strtotime('+5 Minute', strtotime($this->configXY->DATE)));
    }
    
    public function func_cssjs_include($items, $subject_check, $subject_inc, $type) {
        
        if($type == 'css' || $type == 'js') $subject_inc = APPPATH . $subject_inc;
        
        foreach($items as $item) {
            
            if(substr_count($item['type'], $subject_check)) { 
                
                if($type == 'css') $this->configXY->CSS[] = $subject_inc;
                elseif($type == 'js') $this->configXY->JS[] = $subject_inc;
                elseif($type == 'plugin') $this->document->loadPlugin($subject_inc);
                
                break;
            }            
        }        
    }
    
    public function async_update_answers() {
        
        /* Secure this page from unauthorized remote access. */
        $this->securePage();
        
        if(empty($_POST) || empty($this->session)) exit('No data.');
        $input = $_POST;
        unset($input['t']);
        
        extract($input);
        
        $answers = json_decode(json_decode($answers));
        $_SESSION['EXAM'][$screen_code][$screen_no]['items'] = $answers;
        
        $this->exam_model->doUpdateSession();
        $this->exam_model->doUpdateSessionDB($input);
        
        if($close == 'true') echo base64_encode(strtotime('+5 Minute', strtotime($this->configXY->DATE)));
    }
    
    /* Triggered on specific interval. */
    public function async_update_session() {
        
        /* Secure this page from unauthorized remote access. */
        $this->securePage();
        
        if(empty($_POST) || empty($this->session)) exit('No data.');
        $input = $_POST;
        unset($input['t']);
        
        $this->exam_model->doUpdateSessionDB($input);
    }
    
    public function async_update_session_peritem() {
        
        /* Secure this page from unauthorized remote access. */
        $this->securePage();
        
        if(empty($_POST) || ! $this->session->id) exit('No data.');
        $input = $_POST;
        unset($input['t']);
        
        extract($input);
        
        $answers = json_decode(json_decode($answers));
        
        $answer = $answers[$item - 1];
        if(! is_object($answer))
            return;
        
        $_SESSION['EXAM'][$screen_code][$screen_count]['items'][$item - 1] = $answer;
        
        $this->exam_model->doUpdateSessionDB($input);
    }
    
    /* Checks regularly if "current station" is still under "station assignments" . */
    public function async_puller__check_station_state() {
        
        /* Secure this page from unauthorized remote access. */
        $this->securePage();
        
        if(empty($_POST) || empty($this->session)) exit('3');
        $input = $_POST;
        unset($input['t']);
        
        extract($input);
        
        /* START: See if "station number" included in the current Questionnaire. */
        $state = '2';
        $response = $this->exam_model->getIfStationInQ($rta_id, $screen_code);
        if($response) $state = '1';
        /* END: See if "station number" included in the current Questionnaire. */
        
        echo $state;
    }
    
    /* Checks if RTA_ID + CODE + STATUS = 1/True */
    public function async_puller__check_item_state() {
        
        /* Secure this page from unauthorized remote access. */
        $this->securePage();
        
        if(empty($_POST) || empty($this->session)) exit('3');
        $input = $_POST;
        unset($input['t']);
        
        array_walk($input, 'xy_input_clean_up_byref');
        extract($input);
        
        echo (($this->exam_model->getItemState($rta_id, $screen_code)) ? 1 : 0);
    }

    public function async_puller__queue() {
        
        /* Secure this page from unauthorized remote access. */
        $this->securePage();
        
        if(empty($_POST) || ! $this->session->id) return;
        
        $date = trim($_POST['date']);
        if($date) $this->exam_model->date = date('Y-m-d', strtotime($date)); /* Format the date regardless of
         * it's current format just to ensure that it's correct. */
        
        $response = $this->exam_model->getQueue();
        echo json_encode($response);
    }
}

/* End of file exam.php */
/* Location: framework/application/controllers/exam.php */