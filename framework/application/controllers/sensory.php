<?php defined('BASEPATH') or exit('No direct script access allowed');

/* Title : Sensory Controller Class
 * Author: Armande Bayanes (tuso@programmerspride.com)
 * Description: 
 **/

class Sensory extends XY_Controller {
    
    private $id = 0;
    private $step = 1;
    private $questionnaire = array();
    
    function __construct() {
        
        parent::__construct();

        /* If NOT logged-in. */
        if(empty($this->session)) { redirect(base_url('home/logout')); }        
        if($this->session->level != 1) { redirect(base_url('home/error')); }
        
        $this->load->model('sensory_model', '', true);
        $this->document->loadPlugin('screen');
        
        $this->step = $this->configXY->URI['step'];
        
        $this->id = (double) $this->uri->segment(3);
        if(! $this->id) $this->id = (double) $this->configXY->URI['rta_id'];
        if(! $this->id) $this->id = (double) $_POST['rta_id'];
        
        $this->questionnaire = $this->sensory_model->getQviaRTAID($this->id);
        if(empty($this->questionnaire) && $this->step > 1) { redirect(base_url('sensory/create_test/' . $this->id)); }
    }
    
    public function index() {
        
        /* Administrator; Project Owner; Multi Level (2); Multi Level (3); Employee */
        $data['access_level'] = array(1, 2, 6, 7);
        
        $data['view'] = $this->uri->segment(2);
        
        $data['content'] = $this->getViewFile(__FUNCTION__, $data);
        $this->load->view('main', $data);
    }
    
    public function create_test($rta_id = 0) { /* AXL */
        
        //exit($this->step);
        //if(! in_array($this->step, array(1, 2, 3, 4)))
        //    redirect(base_url('home/error'));
        
        /* START: Display RTA with questionnaires on Grid. */
        if($rta_id == 0) {
            
            $this->document->loadPlugin('jqgrid');
            $this->configXY->JS[] = APPPATH . 'views/sensory/create_test/main.js';
            
            $data['page'] = 'main';
            $data['content'] = $this->getViewFile(__FUNCTION__, $data);
            $this->load->view('main', $data);
            
            return;
        }
        /* END: Display RTA with questionnaires on Grid. */
        
        /* START: Provide steps where to go next. */
        if($rta_id > 0) {
            
            $sql = "
                SELECT  rta.*
                FROM    rta_forms rta
                WHERE   rta.id=" . $rta_id . "
                AND     rta.state=1
                AND     (rta.type_of_test='affective' OR rta.type_of_test='analytical')";

            $query = $this->db->query($sql);
            if(! $query->num_rows()) redirect(base_url('home/error'));
            $rta = $query->row();
            
            if($this->step == 0) {
                
                $sql = "SELECT samples_name AS `name` FROM rta_forms WHERE id=" . $rta_id;
                $query = $this->db->query($sql);
                if(! $query->num_rows()) { redirect(base_url('home/error')); }

                $data['rta'] = $query->row();
                $data['url'] = xy_url('sensory/create_test/' . $rta_id . '/?step=');

                $data['page'] = 'goto';
                $data['content'] = $this->getViewFile(__FUNCTION__, $data);
                $this->load->view('main', $data);

                return;
            }
        }
        /* END: Provide steps where to go next. */
        
        if($this->step == '4') {
            
            $sql = "SELECT codes,`batch` FROM q WHERE rta_id=" . $rta_id;
            $query = $this->db->query($sql);
            if(! $query->num_rows()) return $this->_404_();
            
            $q = $query->row();
            
            if($q->codes == '' || $q->batch == 0) {
                
                echo 'Enter the number of batch and generate codes for this RTA first. Go to <a href="' . xy_url('sensory/create_test/' . $rta_id . '/?step=2') . '">Step 2</a>.';
                return;
            }
        }
        
        if($rta->specific_1) { /* Only MICRO has multiple specifics. */

            $sql = "SELECT content FROM specifics WHERE id IN(" . str_replace(' ', '', $rta->specific_1) . ") AND number=1";
            $query = $this->db->query($sql);
            if($query->num_rows()) {

                $tmp = array();
                foreach($query->result() as $row) {
                    $tmp[] = $row->content;
                }

                $data['spec1'] = implode(', ', $tmp);
            }
        }

        if($rta->specific_2) {

            $sql = "SELECT content FROM specifics WHERE id=" . $rta->specific_2 . " AND number=2";
            $query = $this->db->query($sql);
            if($query->num_rows()) {

                $data['spec2'] = $query->row()->content;
            }
        }
        
        $data['rta_id'] = $rta_id;
        $data['rta'] = $rta;

        $data['q'] = $this->questionnaire;
        $data['step'] = $this->step;
        $data['step_headers'] = array(
            '<div><b>Welcome text</b></div><div>Set: "welcome text" / instruction.</div>',
            '<div><b>Requirements</b></div><div>Set: No. of Respondents, No. of Batch, Question flow, Panelist registration, Assign Codes.</div>',
            '<div><b>Screens</b></div><div>Actual questionnaire design.</div>',
            '<div><b>Final: Code distribution</b></div><div>Distribute code on seats.</div>'
        );
        
        $this->configXY->JS[] = APPPATH . 'views/sensory/create_test/step_' . $this->step . '.js';
        $this->configXY->JS_VARS['[var=false]Q.id'] = (double) $data['q']->id;
        $this->configXY->JS_VARS['[var=false]Q.rta_id'] = (double) $rta->id;
        
        if($rta->type_of_test == 'affective') {
            $this->configXY->JS[] = APPPATH . 'views/sensory/affective.js';
        }
        
        $specifics_w_2ndary_code = $this->sensory_model->getSpecifics_with2ndaryCode();
        $data['specifics_w_2ndary_code'] = $specifics_w_2ndary_code;
        
        if(in_array($rta->specific_1, $specifics_w_2ndary_code) || in_array($rta->specific_2, $specifics_w_2ndary_code)) {
            
            $data['with_2ndary_code'] = 1;
        }
        
        $data['one_ss_only'] = $this->sensory_model->getSpecifics_withOneSS();
            
        if($rta->type_of_test == 'affective') {

            $check = in_array($rta->specific_2, $data['one_ss_only']);
            $specific_as_code = 'affective_' . $rta->specific_2;

            $specific_for_code_distribution = $rta->specific_2;


        } elseif($rta->type_of_test == 'analytical') {

            $check = in_array($rta->specific_1, $data['one_ss_only']);
            $specific_as_code = 'analytical_' . $rta->specific_1;

            $specific_for_code_distribution = $rta->specific_1;
        }

        $code_1 = xy_code_get($this->questionnaire->codes, 'primary');
        $this->configXY->JS_VARS['[var=false]Q.codes_1'] = json_encode($code_1);

        if($data['with_2ndary_code']) $code_2 = xy_code_get($this->questionnaire->codes, 'secondary');
        else $code_2 = array();

        $this->configXY->JS_VARS['[var=false]Q.codes_2'] = json_encode($code_2);
        
        if($this->step == 2 || $this->step == '4') {
            
            if($data['q']->batch_content != '') $data['batch_content'] = explode(',', $data['q']->batch_content);
            else $data['batch_content'] = array();
            
            if($this->step == 2) { 
                
                $this->configXY->JS_VARS['[var=false]STEP_2.batch_content'] = json_encode($data['batch_content']);
                
                if($this->questionnaire->code_control != '') {
                                
                    parse_str($this->questionnaire->code_control, $control_codes);
                    $tmp = array_keys($control_codes);
                    $tmp = explode('_', $tmp[0]);
                    $this->configXY->JS_VARS['[var=false]Q.control_codes_index'] = $data['control_codes_index'] = $tmp[count($tmp) - 1];
                }
            }
        }
        
        if($this->step == 2 || $this->step == 3) {
            
            if($this->questionnaire->product_names != '') parse_str($this->questionnaire->product_names, $product_names);
            else $product_names = array();
            
            $data['product_names'] = $product_names;
        }
        
        if($this->step == 3) {
            
            $this->configXY->CSS[] = APPPATH . 'views/sensory/create_test/step_3.css';
            
            parse_str($data['q']->codes, $tmp);
            $data['codes'] = array_values($tmp);
            
            $this->configXY->JS_VARS['[var=false]Q.type_of_test'] = "'" . $rta->type_of_test . "'";
            
            $screens_db = $this->sensory_model->getScreensFor($rta->id);           
            $this->configXY->JS_VARS['[var=false]STEP_3.default_instructions'] = json_encode(xy_default_instruction());
            
            /* START: Initialize screen modification flags. */
            if(! $check) {
            
                if(! empty($code_1)) {

                    foreach($code_1 as $c) {

                        $screens_ss = $_SESSION['SCREEN'][$rta->id][$c];

                        if(! empty($screens_db[$c])) array_walk($screens_db[$c], 'xy_remove_id');
                        else $screens_db[$c] = '';
                        
                        if(! empty($screens_ss)) array_walk($screens_ss, 'xy_remove_id');
                        else $screens_ss = '';
                        
                        $this->configXY->JS_VARS['[var=false]SCREEN.save_flag[' . $c . ']'] = (json_encode($screens_db[$c]) == json_encode($screens_ss)) ? 'false' : 'true';
                        $this->configXY->JS_VARS['[var=false]STEP_3.ss_save_full_flag[' . $c . ']'] = 'false';
                    }
                    
                    $this->configXY->JS_VARS['[var=false]STEP_3.ss_code'] = json_encode($code_1);
                }
            } else {
                
                $screens_ss = $_SESSION['SCREEN'][$rta->id][$specific_as_code];
                
                if(! empty($screens_db[$specific_as_code])) array_walk($screens_db[$specific_as_code], 'xy_remove_id');
                else $screens_db[$specific_as_code] = '';
                
                if(! empty($screens_ss)) array_walk($screens_ss, 'xy_remove_id');
                else $screens_ss = '';
                
                /*echo '<pre>';
                echo 'DB<br/>',json_encode($screens_db[$specific_as_code]) ."<br/>Session<br/>". json_encode($screens_ss);
                echo '</pre>';*/
                
                $this->configXY->JS_VARS['[var=false]SCREEN.save_flag[\'' . $specific_as_code . '\']'] = (json_encode($screens_db[$specific_as_code]) == json_encode($screens_ss)) ? 'false' : 'true';
                $this->configXY->JS_VARS['[var=false]STEP_3.ss_save_full_flag[\'' . $specific_as_code . '\']'] = 'false';
                
                $this->configXY->JS_VARS['[var=false]STEP_3.ss_code'] = json_encode(array($specific_as_code));                
            }
            /* END: Initialize screen modification flags. */
            
            /* For Liking, Compatibility, and JAR. */
            $this->configXY->JS_VARS['[var=false]STEP_3.library'] = json_encode($this->sensory_model->getLibrary());
            
        }
        else
        if($this->step == '4') {
            
            if($data['q']->respondents <= 0) {
                
                echo 'You must indicate the number of panelists. Go to <a href="' . xy_url('sensory/create_test/' . $rta->id . '/?step=2') . '">Step 2</a>.';
                return;
            }
            
            $this->configXY->CSS[] = APPPATH . 'views/sensory/create_test/step_4.css';
            parse_str($data['q']->code_control, $controls);
            $this->configXY->JS_VARS['[var=false]STEP_4.code_controls'] = json_encode(array_values($controls));
            
            
            if(! $data['q']->code_combination) $data['q']->code_combination = $code_combination = $this->sensory_model->doCodeCombinationFill($data['q']->id, $specific_for_code_distribution, $code_1, $code_2, $data['q']->respondents, $data['q']->code_control);
            else $code_combination = json_decode($data['q']->code_combination);
            
            if($code_combination != 'permutate') { $code_combination_js = json_encode(array_values((array) $code_combination)); }
            else $code_combination_js = "'" . $code_combination . "'";
            
            $this->configXY->JS_VARS['[var=false]Q.code_combination'] = $code_combination_js;
            $this->configXY->JS_VARS['[var=false]Q.respondents'] = $data['q']->respondents;
            
            if(substr_count($rta->schedule, ',')) {
                
                $tmp = explode(',', $rta->schedule);
                $tmp = $tmp[0];
            } else $tmp = $rta->schedule;
            
            //echo $data['q']->batch_content;
            $batch_list = array();
            if(substr_count($data['q']->batch_content, ',')) $batches = explode(',', $data['q']->batch_content);
            else $batches = array($data['q']->batch_content);
            
            /* START: Initialize and sort. */
            foreach($batches as $batch) { $batch_list[] = strtotime($batch, strtotime($this->configXY->DATE)); } /* Actual time formatting. */
            sort($batch_list);
            /* END: Initialize and sort. */

            /* START: Restore format in Layman's. */
            $batches = array();
            foreach($batch_list as $batch) { $batches[] = date('h:i A', $batch); }
            /* END: Restore format in Layman's. */
            
            $data['batches'] = $batches;
            
            $data['distribution_date'] = date('Y-m-d', strtotime($tmp));            
            $data['code_combination'] = $code_combination;//json_decode($data['q']->code_combination);//$code_combination;
        }
        
        $data['controller'] = $this;
        $data['content'] = $this->getViewFile(__FUNCTION__, $data);
        $this->load->view('main', $data);
    }
    
    public function async_get_permutations() { /* AXL */
        
        $this->securePage();
        
        if(empty($_POST)) exit('No data.');
        $input = $_POST;
        unset($input['t']);
        extract($input);
        
        $code = json_decode(json_decode($code));
        if(empty($code)) exit();
        
        $controls = json_decode(json_decode($code_controls));
        $this->load->library('Permutation');
        $code_combination = $this->permutation->generate($code, 0);
        
        $html = '';
        $t = count($code_combination);
        for($x=0; $x<$t; $x++) {

            $code = implode(' ', $code_combination[$x]);
            $text = str_replace(array($controls[0], $controls[1]), array('<span style="color: #CC0000">' . $controls[0] . '</span>', '<span style="color: #CC0000">' . $controls[1] . '</span>'), $code);
            
            if($x%2 == 0) $html .= '</tr><tr>';
            $no = ($x + 1);
            $html .= '<td style="width: 35px; color: #777" align="right">' . $no  . '</td><td style="padding-left: 5px"><input id="code_' . $no . '" type="checkbox" value="' . $code . '" /> <label id="code_' . $no  . '_label" for="code_' . $no  . '">' . $text . '</label></td>';
        }

        if($html != '') $html = '<table cellpadding="0" cellspacing="0" width="100%">' . $html . '</table>';
        
        echo $html;
        //echo json_encode($code_combination);
    }
    
    /* 
     * For AFFECTIVE tests wherein codes are permutated.
     **/
    public function async_code_combination_save() {
        
        $this->securePage();
        
        if(empty($_POST) || empty($this->session)) exit();
        $input = $_POST;
        unset($input['t']);
        
        $this->sensory_model->doCodeCombinationSave($input);
    }
    
    public function async_q_list() { /* AXL */
        
        $this->securePage();
        
        if(empty($this->configXY->URI) || empty($this->session)) exit('No data.');
        $input = $this->configXY->URI;
        unset($input['t']);
        
        $this->sensory_model->doLoadQList($input);        
    }
    
    /* Reset the Session by re-fetching the value from DB.
     **/
    public function async_screen_reset() { /* AXL */
        
        $this->securePage();
        
        if(empty($_POST)) exit('No data.');
        $input = $_POST;
        unset($input['t']);
        
        array_walk($input, 'xy_input_clean_up_byref');
        extract($input);
        
        $response = $this->sensory_model->doScreenReset($rta_id, $code);        
        echo json_encode($response);
    }
    
    public function async_screen_copy() { /* AXL */
        
        $this->securePage();
        
        if(empty($_POST)) exit('No data.');
        $input = $_POST;
        unset($input['t']);
        
        array_walk($input, 'xy_input_clean_up_byref');
        extract($input);
        
        $screens = $_SESSION['SCREEN'][$rta_id][$code_to_copy];
        if(empty($screens)) exit();
        
        $_SESSION['SCREEN'][$rta_id][$screen_code] = $_SESSION['SCREEN'][$rta_id][$code_to_copy];
        
        $response = $this->sensory_model->doScreenData($rta_id, $screen_code, $screens);
        
        $screens_db = $_SESSION['SCREEN'][$rta_id][$code_to_copy];//$this->sensory_model->getScreensFor($rta_id, $code_to_copy);

        array_walk($screens_db, 'xy_remove_id');
        array_walk($screens, 'xy_remove_id');
        
        if(json_encode($screens_db) == json_encode($screens)) $response['flag'] = 'true';
        else $response['flag'] = 'false';
        
        echo json_encode($response);
    }
    
    public function async_itemlabel_fetch() { 
        
        $this->securePage();
        
        if(empty($_POST)) exit('No data.');
        $input = $_POST;
        unset($input['t']);
        
        array_walk($input, 'xy_input_clean_up_byref');
        extract($input);
        
        $header = $_SESSION['SCREEN'][$rta_id][$screen_code][$screen_count - 1]['items'][$id - 1]['header'];
        xy_screen_and_item_string_decode($header);
        $header = html_entity_decode($header, ENT_QUOTES, 'UTF-8');
        
        echo $header;
    }
    
    /*public function async_itemlabel_ae() { 
        
        $this->securePage();
        
        if(empty($_POST)) exit('No data.');
        $input = $_POST;
        unset($input['t']);
        
        array_walk($input, 'xy_input_clean_up_byref');
        extract($input);
        
        if($label == '') exit();
        if(empty($_SESSION['SCREEN'][$rta_id][$screen_code][$screen_count]['items'][$id - 1])) exit();
        
        $_SESSION['SCREEN'][$rta_id][$screen_code][$screen_count]['items'][$id - 1]['label'] = $label;
        
        $screens_db = $this->sensory_model->getScreensFor($rta_id, $screen_code);
        $screens = $_SESSION['SCREEN'][$rta_id][$screen_code];
        
        array_walk($screens_db, 'xy_remove_id');
        array_walk($screens, 'xy_remove_id');
        
        if(json_encode($screens_db) == json_encode($screens)) $response = 'true';
        else $response = 'false';
        
        echo $response;        
    }*/
    
    /* Location: STEP 1
     * Get RTA sequence for a selected date.
     **/
    public function async_dt_rta_sequence() {
        
        $this->securePage();
        
        if(empty($_POST)) exit('No data.');
        $input = $_POST;
        unset($input['t']);
        
        array_walk($input, 'xy_input_clean_up_byref');
        extract($input);
        
        if($date == '') exit();
        
        $content = $this->sensory_model->getExam_ByDate($date);
        $rta = $this->sensory_model->doDistributeTest__LoadRTASequence($content->sequence, $date);
        
        $tmp['menu_1'] = ($content->welcome_text) ? $content->welcome_text : '';
        $tmp['menu_2'] = ($content->instruction) ? $content->instruction : '';
        $tmp['menu_3'] = ($content->thankyou_text) ? $content->thankyou_text : '';

        $response['rta'] = $rta;
        $response['content'] = $tmp;
        
        echo json_encode($response);
    }
    
    public function async_dt_save_step_1() {
        
        $this->securePage();
        
        if(empty($_POST)) exit('No data.');
        $input = $_POST;
        unset($input['t']);
        
        array_walk($input, 'xy_input_clean_up_byref');
        extract($input);
        
        if($date == '') exit();
        
        $date = date('Y-m-d', strtotime($date));
        
        $sql = "SELECT * FROM exams WHERE `date`='" . $date . "'";
        $query = $this->db->query($sql);
        if($query->num_rows()) {
            
            $sql = "
                UPDATE  exams
                SET     welcome_text='" . $welcome_text . "',
                        instruction='" . $instruction . "',
                        thankyou_text='" . $ty_text . "',
                        sequence='" . $sequence . "'
                WHERE   `date`='" . $date . "'";
            
        } else {
            
            $sql = "
                INSERT
                INTO    exams
                SET     welcome_text='" . $welcome_text . "',
                        instruction='" . $instruction . "',
                        thankyou_text='" . $ty_text . "',
                        sequence='" . $sequence . "',
                        `date`='" . $date . "',
                        created='" . $this->configXY->DATE . "'";
            
        }
        
        $this->db->query($sql);
        
        echo 'Ok.';
    }
    
    public function async_dt_save_step_2() {
        
    }
    
    public function async_dt_station_assignment_fields() {
        
        /* Secure this page from unauthorized remote access. */
        $this->securePage();
        
        if(empty($_POST)) exit('No data.');
        $input = $_POST;
        unset($input['t']);
        
        array_walk($input, 'xy_input_clean_up_byref');
        extract($input);
        
        $response = $this->sensory_model->getRTA_StationAssignmentFields($rta_id, $date, $batch);
        echo json_encode($response);
    }
    
    public function distribute_test($step = 1, $date = NULL) { /* AXL */
        
        $this->configXY->JS[] = APPPATH . 'views/sensory/distribute_test/step_' . $step . '.js';
        if($date == '') $date = $this->configXY->TODAY;
        $this->configXY->JS_VARS['[var=false]DT.step'] = $data['step'] = $step;
        
        $content = $this->sensory_model->getExam_ByDate($date);
        
        $data['sequence'] = $this->sensory_model->doDistributeTest__LoadRTASequence($content->sequence, $date);
        
        if($step == 1) {
            
            $response['menu_1'] = ($content->welcome_text) ? $content->welcome_text : '';
            $response['menu_2'] = ($content->instruction) ? $content->instruction : '';
            $response['menu_3'] = ($content->thankyou_text) ? $content->thankyou_text : '';
            
            $this->configXY->JS_VARS['[var=false]STEP_1.content'] = json_encode($response);
        }
        else        
        if($step == 2) {
            
            if($content->sequence == '') { redirect(base_url('sensory/distribute_test')); }
            $data = array_merge($data, $this->sensory_model->getBatch($date, $content->sequence));                
        }
        
        $this->configXY->JS_VARS['[var=false]DT.date'] = "'" . $date . "'";
        
        $data['today'] =  date('m/d/Y', strtotime($date));
        $data['date'] =  $date;
        
        $data['controller'] = $this;
        $data['content'] = $this->getViewFile(__FUNCTION__, $data);
        $this->load->view('main', $data);
    }
    
    /* Get the "primary code/s" of a selected RTA on Test Distirbution. */
    public function async_code_getfor_dt() {
        
        $this->securePage();
        
        if(empty($_POST)) exit('No data.');
        $input = $_POST;
        unset($input['t']);
        
        array_walk($input, 'xy_input_clean_up_byref');
        extract($input);
        
        $q = $this->sensory_model->getQviaRTAID($rta_id);
        
        $codes = xy_code_get($q->codes, 'primary');
        
        echo json_encode($codes);
    }
    
    /* dt = Distribute Test
     **/
    /*public function async_dt_step1_load_content() { /* AXL */
        
        /*$this->securePage();
        
        if(empty($_POST)) exit('No data.');
        $input = $_POST;
        unset($input['t']);
        
        $this->sensory_model->doLoadDTStep1Content($input);        
    }*/
    
    public function async_photo_upload($id = 0) {
        
        if($id == 0)
            return;
        
        if(($filename = $_FILES['photo']['name']) != '' ) {
            
            $img_arr = array('gif', 'jpg', 'png');
            $extension = pathinfo($filename, PATHINFO_EXTENSION);
            
            if(in_array(strtolower($extension), $img_arr)) {
                
                $tmp = 'TEMP/' . time();
                
                $filename = $tmp . '.' . $extension;
                $filename_resized = $tmp . '_resized.' . $extension;
                
                $ok = @move_uploaded_file($_FILES['photo']['tmp_name'], $filename);
                if($ok) {
                    
                    $content = @file_get_contents($filename);
                    list($width, $height) = getimagesize($filename);
                    list($w, $h) = $this->image->getNewWH($width . 'x' . $height, 700);

                    $image_p = @imagecreatetruecolor($w, $h);
                    $image = @imagecreatefromstring($content);
                    
                    @imagecopyresampled($image_p, $image, 0, 0, 0, 0, $w, $h, $width, $height);
                    @imagejpeg($image_p, $filename_resized, 100);
                    
                    /*$fp = @fopen($filename_resized, 'rb');
                    if($fp) {
                        
                        $content = fread($fp, filesize($filename_resized));
                        fclose($fp);
                    }*/
                    
                    $content = @file_get_contents($filename_resized);
                    
                    unlink($filename);
                    unlink($filename_resized);
                    
                    if($content) {
                        
                        $sql = "
                            UPDATE  q_temporary_item_data
                            SET     pause_break_photo='" . mysql_real_escape_string($content). "',
                                    pause_break_photo_dimension='" . $w . 'x' . $h . "'
                            WHERE   q_item_id=" . $id;
                        $this->db->query($sql);                        
                    }
                }
            }
        }
        // myIFrame.contentWindow.document.body.innerHTML = content;
        //print_r($_FILES);
        
        //echo $id;
    }
    
    public function loadPauseBreakPhoto($id, $scale = 0) {
        
        if(! $id) return $this->_404_();
        if(! $scale) $scale = 700;
        
        $sql = "
            SELECT  pause_break_photo AS content,
                    pause_break_photo_dimension AS dimension
            FROM    q_temporary_item_data
            WHERE   q_item_id=" . $id;
        
        $query = $this->db->query($sql);
        if($query->num_rows()) {
            
            $image = $query->row();
            $this->image->readImage($image, $scale);
        }
    }
    
    /* Sorts screen, updates Session only.
     **/
    public function async_screen_sort() { /* AXL */
        
        $this->securePage();
        
        if(empty($_POST)) exit('No data.');
        $input = $_POST;
        unset($input['t']);
        
        array_walk($input, 'xy_input_clean_up_byref');
        extract($input);
        
        if($screens == '') exit();
        
        $this->sensory_model->doScreenSort($rta_id, $screen_code, $screens);
    }
    
    public function async_screen_clear() { /* AXL */
        
        $this->securePage();
        
        if(empty($_POST)) exit('No data.');
        $input = $_POST;
        unset($input['t']);
        
        array_walk($input, 'xy_input_clean_up_byref');
        extract($input);
        
        $screens_db = $this->sensory_model->getScreensFor($rta_id, $screen_code);
        unset($_SESSION['SCREEN'][$rta_id][$screen_code]);

        if(! empty($screens_db)) array_walk ($screens_db, 'xy_remove_id');
        
        $response['count'] = 0;
        $response['flag'] = ((json_encode($screens_db) == json_encode($screens)) ? false : true);
        
        echo json_encode($response);        
    }
    
    public function async_screenitem_sort() { /* AXL */
        
        $this->securePage();
        
        if(empty($_POST)) exit('No data.');
        $input = $_POST;
        unset($input['t']);
        
        array_walk($input, 'xy_input_clean_up_byref');
        extract($input);
        
        if($items == '') exit();
        
        $items = explode(',', $items);
        
        $items_ss = $_SESSION['SCREEN'][$rta_id][$screen_code][$screen_count]['items'];
        
        $index = 0;
        $tmp = array();
        foreach($items as $item_id) {
            
            foreach($items_ss as $item_ss_id => $data) {
                
                if($item_ss_id == ($item_id - 1)) {
                    
                    $tmp[] = $data;
                }
            }
            
            $index++;
        }
        
        $_SESSION['SCREEN'][$rta_id][$screen_code][$screen_count]['items'] = $tmp;
        
        foreach($tmp as $item_id => $data) {
            
            $count =  $screen_count + 1;
            $id = $item_id + 1;
            $html .= '
                <li id="screenitem_' . $screen_code . '_' . $count . '_' . $id . '" style="padding: 0; margin: 0">
                    <a title="delete" class="item_' . $screen_code . '_' . $count . '_del_trigger" href="javascript:SCREEN.item_del(' . $id . ',\'' . $screen_code . '\',' . $count . ')"><img src="/jollibee/media/images/16x16/delete.png"></a>
                    <a title="edit" class="item_' . $screen_code . '_' . $count . '_edit_trigger" href="javascript:SCREEN.item_ae_picked(\'' . $data['type'] . '\',' . $id . ',\'' . $screen_code . '\',' . $count . ')" style="font: 12px Verdana"><span>' . (($data['header']) ? $data['header'] : strtoupper($data['type'])) . '</span></a>
                </li>';
        }
        
        $response['html'] = $html;
        
        $screens_db = $this->sensory_model->getScreensFor($rta_id, $screen_code);
        $screens = $_SESSION['SCREEN'][$rta_id][$screen_code];
        
        if(! empty($screens_db[$screen_code])) array_walk ($screens_db[$screen_code], 'xy_remove_id');
        if(! empty($screens)) array_walk ($screens, 'xy_remove_id');
        
        $response['flag'] = ((json_encode($screens_db[$screen_code]) == json_encode($screens)) ? false : true);
        echo json_encode($response);
    }
    
    public function async_code_distribution() { /* AXL */
        
        /* Secure this page from unauthorized remote access. */
        $this->securePage();
        
        if(empty($_POST) || empty($this->session)) exit('No data.');
        $input = $_POST;
        unset($input['t']);
        
        $this->sensory_model->doCodeDistributionAE($input);
    }
    
    public function async_code_distribution_load() { /* AXL */
        
        /* Secure this page from unauthorized remote access. */
        $this->securePage();
        
        if(empty($_POST) || empty($this->session)) exit('No data.');
        $input = $_POST;
        unset($input['t']);
        
        $controls = explode(',', $input['controls']);
        $response = $this->sensory_model->doCodeDistributionLoadAll($input);
        
        echo json_encode($response);
    }
    
    public function async_code_generate() { /* AXL */
        
        /* Secure this page from unauthorized remote access. */
        $this->securePage();
        
        if(empty($_POST)) exit('No data.');
        $input = $_POST;
        unset($input['t']);
        
        extract($input);
        $queue = array();
        
        //$sql = "SELECT code FROM q_codes WHERE created='" . $this->configXY->TODAY . "'";
        //$query = $this->db->query($sql);
        //if($query->num_rows()) $codes = $query->result();
        //else $codes = array();
        
        $codes = array();
        
        /* Create Unique random numbers. */
        if($with_2ndary_code) $max = ($nof_samples * 2);
        else $max = $nof_samples;
        while(count($queue) != $max) {
            
            $code = rand(100, 999);
            if((! in_array($code, $queue)) && (! in_array($code, $codes))) {
                
                $queue[] = $code;
            }
        }
        
        echo implode(',', $queue);
    }
    
    public function async_code_get() {
        
        /* Secure this page from unauthorized remote access. */
        $this->securePage();
        
        if(empty($_POST)) exit('No data.');
        $input = $_POST;
        unset($input['t']);
        
        extract($input);
        $queue = array();
        
        $sql = "SELECT codes FROM q WHERE rta_id=" . $rta_id;
        $query = $this->db->query($sql);
        
        if($query->num_rows()) parse_str($query->row()->codes, $codes);
        
        $code_arr = array();
        
            
        foreach($codes as $key => $value) {

            list($tmp, $number, $index) = explode('_', $key);
            
            if($type == 'ranking_for_preference' || $type == 'paired_preference') {
                
                if($number == 1) $code_arr[$index] = $value;
            }
        }
        
        echo json_encode($code_arr);
    }
    
    /* ct = Create Test. */
    public function async_ct_step_1() {
        
        /* Secure this page from unauthorized remote access. */
        $this->securePage();
        
        if(empty($_POST) || ($this->session->level != 1)) exit('No data.');
        $input = $_POST;
        unset($input['t']);
        
        $this->sensory_model->doCreateTest__SaveStep_1($input);
        
        echo 'Ok.';
    }
    
    public function async_ct_step_2() {
        
        /* Secure this page from unauthorized remote access. */
        $this->securePage();
        
        if(empty($_POST) || ($this->session->level != 1)) exit('No data.');
        $input = $_POST;
        unset($input['t']);
        
        $this->sensory_model->doCreateTest__SaveStep_2($input);
        
        echo 'Ok.';
    }
    
    public function async_ct_step_4() {
        
        /* Secure this page from unauthorized remote access. */
        $this->securePage();
        
        if(empty($_POST) || ($this->session->level != 1)) exit('No data.');
        $input = $_POST;
        unset($input['t']);
        
        $this->sensory_model->doCreateTest__SaveStep_4($input);
        
        echo 'Ok.';
    }
    
    /*public function async_save_step_4() {
        
        /* Secure this page from unauthorized remote access. */
        /*$this->securePage();
        
        if(empty($_POST)) exit('No data.');
        $input = $_POST;
        unset($input['t']);
        
        $input['s1code_distribution'] = substr($input['s1code_distribution'], 1, strlen($input['s1code_distribution']) - 2);
        $input['s2code_distribution'] = substr($input['s2code_distribution'], 1, strlen($input['s2code_distribution']) - 2);
        
        array_walk($input, 'xy_input_clean_up_byref');
        extract($input);
        
        if($rta_id == 0 || $batch == 0) exit();
            
        $sql = "
            SELECT  q_id
            FROM    q_code_distributions
            WHERE   rta_id=" . $rta_id . "
            AND     batch=" . $batch . "
            AND     q_id=" . $id;
        $query = $this->db->query($sql);
        if($query->num_rows()) {
            
            $sql = "
                UPDATE  q_code_distributions
                SET     s1d='" . $s1code_distribution . "',
                        s2d='" . $s2code_distribution . "'
                WHERE   batch=" . $batch . "
                AND     rta_id=" . $rta_id . "
                AND     q_id=" . $id;

        } else {
            
            $sql = "
                INSERT
                INTO    q_code_distributions
                SET     s1d='" . $s1code_distribution . "',
                        s2d='" . $s2code_distribution . "',
                        batch=" . $batch . ",
                        rta_id=" . $rta_id . ",
                        q_id=" . $id . ",
                        created='" . $this->configXY->DATE . "'";
        }
        
        $this->db->query($sql);
        /*
        $sql = "
            UPDATE  q
            SET     code_distribution='" . $code_distribution . "'
            WHERE   rta_id=" . $rta_id;
        $this->db->query($sql);
        */
        
        /*$sql = "
            SELECT  batch,
                    s1d,
                    s2d
            FROM    q_code_distributions
            WHERE   q_id=" . $id . "
            AND     rta_id=" . $rta_id;
        $query = $this->db->query($sql);
        if($query->num_rows()) $code_distribution = $query->result();
        else $code_distribution = array();
        
        $sql = "SELECT * FROM q WHERE id=" . $id;
        $query = $this->db->query($sql);
        if($query->num_rows()) $q_copy = $query->row_array();
        else $q_copy = array();

        $sql = "SELECT * FROM rta_forms WHERE id=" . $rta_id;
        $query = $this->db->query($sql);
        if($query->num_rows()) $rta = $query->row_array();
        else $rta = array();
        
        unset($rta['id']);
        unset($rta['created']);

        $q_copy = (object) array_merge($q_copy, $rta);
        $q_copy->code_combination = (array) $code_distribution;
        $product_name = $q_copy->product_name;
        
        $q_copy = json_encode($q_copy);
        $sql = "SELECT id FROM q_copies WHERE q_id=" . $id . " AND rta_id=" . $rta_id;
        $query = $this->db->query($sql);
        if($query->num_rows()) {
            
            $sql = "
                UPDATE  q_copies
                SET     name='" . $product_name . "',
                        content='" . $q_copy . "'
                WHERE   q_id=" . $id . "
                AND     rta_id=" . $rta_id;
            
        } else {
            
            $sql = "
                INSERT
                INTO    q_copies
                SET     q_id=" . $id . ",
                        rta_id=" . $rta_id . ",
                        name='" . $product_name . "',
                        content='" . $q_copy . "',
                        created='" . $this->configXY->DATE . "'";
            
        }
        $this->db->query($sql);
        
        echo json_encode($code_distribution);
    }*/
    
    /* Triggered when adding or editing item from
     * the Library. Most specially for Liking, COmpatibility, and JAR.
     **/
    public function async_library_ae() { /* AXL */
        
        $this->securePage(); /* Secure this page from unauthorized remote access. */
        
        if(empty($_POST) || empty($this->session)) exit('No data.');
        $input = $_POST;
        unset($input['t']);
        array_walk($input, 'xy_input_clean_up_byref');
        extract($input);
        
        $label = $this->string->getAlphanumericOnly($label);
        $response = $this->sensory_model->doLibraryAE($id, $type, $label);
        
        echo json_encode($response);
    }
    
    /* Checks before deleting item in the Library.
     * Most specially for Liking, COmpatibility, and JAR.
     **/
    public function async_library_del_check() { /* AXL */
        
        $this->securePage(); /* Secure this page from unauthorized remote access. */
        
        if(empty($_POST) || empty($this->session)) exit('No data.');
        $input = $_POST;
        unset($input['t']);
        
        array_walk($input, 'xy_input_clean_up_byref');
        extract($input);
        
        if($id == 0) exit();
        
        echo json_encode($this->sensory_model->doLibraryDeleteCheck($id, $label, $type));
    }
    
    /* Actual deleting of item in the Library.
     * Most specially for Liking, COmpatibility, and JAR.
     **/
    public function async_library_del() { /* AXL */
        
        $this->securePage(); /* Secure this page from unauthorized remote access. */
        
        if(empty($_POST) || empty($this->session)) exit('No data.');
        $input = $_POST;
        unset($input['t']);
        array_walk($input, 'xy_input_clean_up_byref');
        extract($input);
        
        if(! $id) exit();
        $response = $this->sensory_model->doLibraryDelete($id, $type);
        
        echo json_encode($response);
    }
    
    public function async_exam_save() {
        
        /* Secure this page from unauthorized remote access. */
        $this->securePage();
        
        if(empty($_POST) || empty($this->session)) exit('No data.');
        $input = $_POST;
        unset($input['t']);
        array_walk($input, 'xy_input_clean_up_byref');
        extract($input);
        
        if($date == '' || $respondents == 0 || $batch == 0) exit();
        
        $date = date('Y-m-d', strtotime($date));
        
        $id = 0;
        $sql = "
            SELECT  id
            FROM    exams
            WHERE   date='" . date('Y-m-d', strtotime($date)) . "'";
        $query = $this->db->query($sql);
        if($query->num_rows()) {
            
            $id = $query->row()->id;
            $sql = "UPDATE ";
            $sql_append = " WHERE id=" . $id;
            
        } else {
            
            $sql = "INSERT INTO ";
            $sql_append = ",created='" . $this->configXY->DATE . "'";
        }
        
        $sql .= "exams
            SET date='" . $date . "',
                batch='" . $batch . "',
                respondents=" . $respondents . $sql_append; 
        $this->db->query($sql);
        
        if($id == 0) $id = $this->db->insert_id();
        /*
         * START :
         */
        
        if($seats != '') {
            
            if(substr_count($seats, ',')) $seats = explode(',', $seats);
            else $seats = array($seats);
            
            foreach($seats as $seat) {
                
                $sql = "
                    SELECT  id
                    FROM    exam_items
                    WHERE   exam_id=" . $id . "
                    AND     seat_no=" . $seat . "
                    AND     batch_no=" . $batch_number;
                $query = $this->db->query($sql);
                if(! $query->num_rows()) {
                    
                    $sql = "
                        INSERT
                        INTO    exam_items
                        SET     exam_id=" . $id . ",
                                seat_no=" . $seat . ",
                                batch_no=" . $batch_number . ",
                                created='" . $this->configXY->DATE . "'";
                    $this->db->query($sql);
                    $exam_item_id = $this->db->insert_id();
                    
                } else $exam_item_id = $query->row()->id;
                
                if($rta != '') {

                    if(substr_count($rta, ',')) $rtas = explode(',', $rta);
                    else $rtas = array($rta);

                    foreach($rtas as $rta_id) {

                        $sql = "
                            SELECT  id
                            FROM    exam_item_data
                            WHERE   exam_id=" . $id . "
                            AND     exam_item_id=" . $exam_item_id . "
                            AND     rta_id=" . $rta_id;
                        $query = $this->db->query($sql);
                        
                        if(! $query->num_rows()) {

                            $sql = "
                            INSERT
                            INTO    exam_item_data
                            SET     exam_id=" . $id . ",
                                    exam_item_id=" . $exam_item_id . ",
                                    rta_id=" . $rta_id . ",
                                    created='" . $this->configXY->DATE . "'";
                            $this->db->query($sql);
                        }
                    }
                }
            }
        }
        
        echo 'Ok.';
    }
    
    public function async_dt_station_assign() {
        
        /* Secure this page from unauthorized remote access. */
        $this->securePage();
        
        if(empty($_POST) || $this->session->level != 1) exit('No data.');
        $input = $_POST;
        unset($input['t']);
        
        $this->sensory_model->doDistributeTest__Assign($input);
        
        echo 'Ok.';
    }
    
    /*public function async_station_get() {
        
        /* Secure this page from unauthorized remote access. */
        /*$this->securePage();
        
        if(empty($_POST) || empty($this->session)) exit('No data.');
        $input = $_POST;
        unset($input['t']);
        array_walk($input, 'xy_input_clean_up_byref');
        extract($input);
        
        if($batch == '' || $rta_id == 0 || $date == '') exit();
        
        $sql = "
            SELECT  station
            FROM    q_distributions
            WHERE   `date`='" . $date . "'
            AND     rta_id=" . $rta_id . "
            AND     `batch`='" . $batch . "'";
        $query = $this->db->query($sql);
        if($query->num_rows()) $response = $query->row()->station;
        
        echo $response;
    }*/
    
    /* Get station assignments by batch. */
    public function async_dt_station_assignments_bybatch() {
        
        /* Secure this page from unauthorized remote access. */
        $this->securePage();
        
        if(empty($_POST) || empty($this->session)) exit('No data.');
        $input = $_POST;
        unset($input['t']);
        
        array_walk($input, 'xy_input_clean_up_byref');
        extract($input);
        
        $batch_choice = $batch;
        
        $exam = $this->sensory_model->getExam_ByDate($date, 'sequence');
        $data = $this->sensory_model->getBatch($date, $exam->sequence);
        
        $response = array();
        if(! empty($data['batch_content'])) {
            
            foreach($data['batch_content'][$batch_choice] as $rta_id => $content) {
                
                $response[] = $this->sensory_model->getRTA_StationAssignmentFields($rta_id, $date, $batch_choice);
            }
        }
        
        echo json_encode($response);
    }
    
    public function async_dt_station_state() {
        
        /* Secure this page from unauthorized remote access. */
        $this->securePage();
        
        if(empty($_POST) || $this->session->level != 1) exit('No data.');
        $input = $_POST;
        unset($input['t']);
        
        $this->sensory_model->doDistributeTest__State($input);

        echo 'Ok.';
    }
    
    /*public function async_station_get_seats() {
        
        /* Secure this page from unauthorized remote access. */
        /*$this->securePage();
        
        if(empty($_POST) || empty($this->session)) exit('No data.');
        $input = $_POST;
        unset($input['t']);
        array_walk($input, 'xy_input_clean_up_byref');
        extract($input);
        
        if($batch == '' || $date == '') exit();
        
        $tmp = $station = $seat = array();
        
        $sql = "SELECT * FROM q_distributions WHERE `batch`='" . $batch . "' AND `date`='" . $date . "'";
        
        $query = $this->db->query($sql);
        if($query->num_rows()) {

            $rows = $query->result();
            foreach($rows as $row) {
                
                $sql = "SELECT samples_name FROM rta_forms WHERE id=" . $row->rta_id;
                $query = $this->db->query($sql);
                if($query->num_rows()) $rta_name = $query->row()->samples_name;
                else $rta_name = '';
                
                $station[$row->rta_id] = (object) array(
                    
                    'rta_name' => $rta_name,
                    'batch' => $row->batch,
                    'seat' => $row->station
                    
                );
                $seat[$row->rta_id] = $row->station;

            }
        }
        
        $data['station'] = $station;
        $data['seat'] = $seat;
        
        echo json_encode($data);        
    }*/
    
    public function async_scoresheet_search() { /* AXL */
        
        /* Secure this page from unauthorized remote access. */
        $this->securePage();
        
        if(empty($_POST) || empty($this->session)) exit('No data.');
        $input = $_POST;
        unset($input['t']);
        array_walk($input, 'xy_input_clean_up_byref');
        extract($input);
        
        if($search == '') exit();
        
        list($total, $rows) = $this->sensory_model->doScoreSheetSearch($search);
        
        if($total > 0) { 
            
            for($x=0; $x<$total; $x++) {
                
                $row = $rows[$x];
                $label = $row->ss_name;
                
                $label_html = xy_search_higlight($search, $label);
                        
                $html .= '<div id="ss_result_item_' . $row->id . '" title="click to load" onmouseover="this.style.background=\'#EFEFEF\'" onmouseout="this.style.background=\'#FFFFFF\'" onclick="STEP_3.ss_load(' . $row->id . ',' . $row->rta_id . ')" style="padding: 2px 0 2px 0; cursor: default"><div id="' . $label . '" style="padding-left: 5px"><span style="color: #777">' . ($x + 1) . '.</span> ' . $label_html . '</div></div>';
            }
            
        } else $html = '<span style="color: #999">No result(s) found.</span>';
        
        echo '<div style="padding: 5px 5px 5px 10px">' . $html . '</div>';
    }


/**************************************************************************************************************************
 * START:
 * Score-sheet manipulations.
 **************************************************************************************************************************/
    
    public function async_scoresheet_save() { /* AXL */
        
        $this->securePage(); /* Secure this page from unauthorized remote access. */
        
        if(empty($_POST) || empty($this->session)) exit('No data.');
        $input = $_POST;
        unset($input['t']);
        array_walk($input, 'xy_input_clean_up_byref');
        extract($input);
        
        if(! $rta_id || ! $code || $name == '') exit(); // || $name == ''
        
        if($full == 'true') $screens = $_SESSION['SCREEN'][$rta_id][$code];
        else $screens = $this->sensory_model->getScreensFor($rta_id, $code);
        
        if(! empty($screens)) {
            
            array_walk($screens, 'xy_remove_id');
            
            $sql = "
                INSERT
                INTO    q_copies
                SET     user_id=" . $this->session->id . ",
                        rta_id=" . $rta_id . ",
                        name='" . $name . "',
                        content='" . base64_encode(serialize($screens)) . "',
                        created='" . $this->configXY->DATE . "'";
            $this->db->query($sql);
        }
        
        echo 'Ok.';        
    }
    
    public function async_scoresheet_check() { /* AXL */
        
        $this->securePage();
        
        if(empty($_POST) || empty($this->session)) exit('No data.');
        $input = $_POST;
        unset($input['t']);
        array_walk($input, 'xy_input_clean_up_byref');
        extract($input);
        
        if(! $rta_id || ! $code ) exit();
        
        $screens = $this->sensory_model->getScreensFor($rta_id, $code);
        $response['exists'] = false;
        if(! empty($screens)) $response['exists'] = true;
        
        echo json_encode($response);
    }
    
    public function async_scoresheet_load() { /* AXL */
        
        $this->securePage(); /* Secure this page from unauthorized remote access. */
        
        if(empty($_POST) || empty($this->session)) exit('No data.');
        $input = $_POST;
        unset($input['t']);
        array_walk($input, 'xy_input_clean_up_byref');
        extract($input);
        
        if(! $rta_id_src || ! $rta_id_dst || ! $id || ! $code) exit();
        
        $response = $this->sensory_model->doScoreSheetLoad($rta_id_src, $id, $code);        
        $response['same'] = false;
        
        /*
        $fp = fopen('TEMP/logs.txt', 'w');
        if($fp) {
            
            fwrite($fp, json_encode($_SESSION['SCREEN'][$rta_id_dst][$code]) ."\r\n\r\n". json_encode($response['screens']));
            fclose($fp);
        }
        */
        
        /* If same, then no need to load. */
        if(json_encode($_SESSION['SCREEN'][$rta_id_dst][$code]) == json_encode($response['screens'])) {
            
            $response['same'] = true;
            unset($response['html']);
            unset($response['count']);
            
        } else $_SESSION['SCREEN'][$rta_id_dst][$code] = $response['screens'];

        unset($response['screens']);
        
        echo json_encode($response);
    }    
}

/* End of file sensory.php */
/* Location: framework/application/controllers/sensory.php */
