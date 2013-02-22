<?php defined('BASEPATH') or exit('No direct script access allowed')?>

<?php
/* 
 * Title : Calendar Controller Class
 * Author: Armande Bayanes (tuso@programmerspride.com)
 * Description: Handles Logging-in and Registration, etc ...
 **/

class Calendar extends XY_Controller {
    
    public $access_level = array(1, 2, 4, 6, 7);
    
    function __construct() {
        
        parent::__construct();
        
        $try = array('login', 'async_login');
        
        /* Check if NOT logged-in. */
        $this->load->helper('url');
        if(empty($this->session)) {
            
            if(! in_array($this->uri->segment(2), $try)) {
                redirect(base_url('admin/login'));
            }
        }
        
        if(! in_array($this->session->level, $this->access_level)) {
            
            redirect(base_url('home'));            
        }
        
        $this->load->model('calendar_model', '', true);
    }
    
    public function index() {
        
        $year = $this->uri->segment(3);
        $month = $this->uri->segment(4);
        
        if($year == '' || $month == '') {
            
            $date = (string) trim($this->configXY->URI['date']);
            if($date === '') { $date = $this->configXY->TODAY; }
            
            list($year_tmp, $month_tmp, $day_tmp) = explode('-', $date);
            
            $year = $year_tmp;
            $month = $month_tmp;
        }
        
        $this->configXY->JS_VARS['[var=false]CALENDAR.date_subject'] = "'" . $year . '-' . $month . "'";
        
        $data['year'] = $year;
        $data['month'] = $month;
        
        $target = (string) trim($this->configXY->URI['target']);
        if($target === '') $target = 'month';
        $data['target'] = $target;
        
        $groups = array();
        
        if($target == 'month') {
            
            $init = array (
                'start_day'    => 'monday',
                'month_type'   => 'long',
                'day_type'     => 'long', /* Day Headers/Captions. */
                'show_next_prev'  => TRUE,
                'next_prev_url'   => xy_doc_root() . 'calendar/index/'
            );

            $init['template'] = '
            {table_open}<table style="background: #FFF; border-spacing: 0; border-collapse: collapse; border: 1px solid #CCC" id="table_calendar">{/table_open}
            {heading_row_start}<tr>{/heading_row_start}
            {heading_previous_cell}<th><div style="padding-left: 5px"><a href="{previous_url}">&lt;&lt; Previous Month</a></div></th>{/heading_previous_cell}
            {heading_title_cell}<th colspan="{colspan}">' . $this->calendar_model->loadHeader() . '</th>{/heading_title_cell}
            {heading_next_cell}<th align="right"><div style="padding-right: 5px"><a href="{next_url}">Next Month &gt;&gt;</a></div></th>{/heading_next_cell}
            {heading_row_end}</tr>{/heading_row_end}
            {week_row_start}<tr>{/week_row_start}
            {week_day_cell}<td>' . $this->calendar_model->loadDayHeader() . '</td>{/week_day_cell}
            {week_row_end}</tr>{/week_row_end}
            {cal_row_start}<tr>{/cal_row_start}
            {cal_cell_start}<td>{/cal_cell_start}
            {cal_cell_content}' . $this->calendar_model->loadCellContent($year, $month) . '{/cal_cell_content}
            {cal_cell_content_today}' . $this->calendar_model->loadCellContentToday($year, $month) . '{/cal_cell_content_today}
            {cal_cell_no_content}' . $this->calendar_model->loadCellNoContent() . '{/cal_cell_no_content}
            {cal_cell_no_content_today}' . $this->calendar_model->loadCellNoContentToday() . '{/cal_cell_no_content_today}
            {cal_cell_blank}&nbsp;{/cal_cell_blank}
            {cal_cell_end}</td>{/cal_cell_end}
            {cal_row_end}</tr>{/cal_row_end}
            {table_close}</table>{/table_close}';

            /* Initialize. */
            $date_end = ($year . '-' . ((($month + 1) < 10) ? ('0' . ($month + 1)) : ($month + 1)) . '-1');

            $sql = "
                SELECT  *
                FROM    rta_forms
                WHERE   state=1
                AND     date_filed BETWEEN date_filed AND '" . $date_end . "'";

            $query = $this->db->query($sql);

            $cdata = array();        
            $dates = array();
            
            $x = 0;
            if($query->num_rows()) {

                foreach($query->result() as $row) {

                    $schedule = $row->schedule;
                    if(substr_count($schedule, ',')) {
                        $schedule = explode(',', $schedule);
                    } else $schedule = array($schedule);

                    foreach($schedule as $value) {

                        $value = trim($value);

                        list($mm, $dd, $yyyy) = explode('/', $value);
                        if($yyyy == $year && $mm == $month) {
                            
                            if(! in_array($dd, $groups)) $groups[] = $dd;

                            $dates[$x]['tot'] = $row->type_of_test;
                            $dates[$x]['date'] = $value;
                            $dates[$x]['id'] = $row->id;
                            $dates[$x]['group'] = $dd;
                            $x++;
                        }                    
                    }
                }
            }

            foreach($groups as $group) {

                for($x=0, $y=count($dates); $x<$y; $x++) {

                    $row = $dates[$x];

                    /*if($row['tot'] == 'affective' && $row['group'] == $group) { $cdata[$group]['affective'] = count($cdata[$group]['affective']) + 1; }                
                    if($row['tot'] == 'analytical' && $row['group'] == $group) { $cdata[$group]['analytical'] = count($cdata[$group]['analytical']) + 1; }                
                    if($row['tot'] == 'micro' && $row['group'] == $group) { $cdata[$group]['micro'] = count($cdata[$group]['micro']) + 1; }
                    */
                    
                    if($row['tot'] == 'affective' && $row['group'] == $group) { $cdata[$group][$row['tot']] += count($cdata[$group][$row['tot']]); }
                    if($row['tot'] == 'analytical' && $row['group'] == $group) { $cdata[$group][$row['tot']] += count($cdata[$group][$row['tot']]); }
                    if($row['tot'] == 'micro' && $row['group'] == $group) { $cdata[$group][$row['tot']] += count($cdata[$group][$row['tot']]); }
                    if($row['tot'] == 'physico_chem' && $row['group'] == $group) { $cdata[$group][$row['tot']] += count($cdata[$group][$row['tot']]); }
                }
            }

            $tmp = $cdata;
            unset($cdata);

            foreach($tmp as $key => $value) {

                $str = '';
                foreach($value as $type_of_test => $count) { 
                    $str .= '<b>' . ($count + 1) . '</b> ' . ucfirst($type_of_test) . '<br />';                     
                }
                $cdata[$key] = $str;
            }

            $this->load->library('calendar', $init);
            $data['calendar'] = $this->calendar->generate($year, $month, $cdata);
            
            $grouped_by_week_number = $this->calendar_model->getWeekNumbers($month, $year);
            
            $html = '<table style="border-spacing: 0; border-collapse: collapse">'; $ctr = 0;
            foreach($grouped_by_week_number as $week_no => $content) {
                
                $ctr++;
                
                if($ctr == 1) $margin_top = 'margin-top: 70px;';
                $html .= '<tr><td><div style="' . $margin_top . ' width: 84px; height: 30px "><a title="Week# ' . $week_no . '" href="' . xy_url('calendar/?target=week&date=' . $year . '-' . $month) . '&week_no=' . $week_no . '"><span style="font-size: 18px; font-weight: bold">' . $week_no . '</span></a></div></td></tr>';
            }
            
            $data['week_no'] = $html . '</table>';            
        }
        else
        if($target == 'week') {
            
            $week_no = trim($this->configXY->URI['week_no']);
            $this->configXY->JS_VARS['[var=false]CALENDAR.week_no'] = "'" . $week_no . "'";
            
            $date = (string) trim($this->configXY->URI['date']);
            if(substr_count($date, '-') < 1) return $this->_404_ ();
            list($year, $month) = explode('-', $date);
            
            /* Initialize. */
            $date_end = ($year . '-' . ((($month + 1) < 10) ? ('0' . ($month + 1)) : ($month + 1)) . '-1');
            
            $this->configXY->JS_VARS['[var=false]CALENDAR.date_end'] = "'" . $date_end . "'";
            
            $sql = "
                SELECT  rta.*,
                        CONCAT_WS(' ',firstname,middlename,lastname) AS requestor,
                        loc.name AS location
                FROM    rta_forms rta
                LEFT
                JOIN    users u
                ON      rta.requested_by_id=u.id
                LEFT
                JOIN    sbu_locations loc
                ON      rta.location=loc.id
                WHERE   rta.state=1
                AND     rta.date_filed BETWEEN rta.date_filed AND '" . $date_end . "'";

            $query = $this->db->query($sql);

            $data['data'] = array();        
            $x = 0;
            $test_date = $month . '/' . (($day < 10) ? ('0' . $day) : $day) . '/' . $year;
            if($query->num_rows()) {

                foreach($query->result() as $row) {

                    $schedule = $row->schedule;
                    if(substr_count($schedule, ',')) {
                        $schedule = explode(',', $schedule);
                    } else $schedule = array($schedule);

                    foreach($schedule as $value) {
                        
                        $value = trim($value);
                        
                        $tmp = date('D,m', strtotime($value));
                        list($day_of_the_week, $month_of_this_data) = explode(',', $tmp);
                        
                        if(! in_array($day_of_the_week, $groups)) {
                            $groups[] = $day_of_the_week;
                        }
                        
                        $data['data'][$x]['data'] = $row;
                        $data['data'][$x]['tot'] = $row->type_of_test;
                        $data['data'][$x]['display'] = (intval($month) == intval($month_of_this_data)) ? true : false;
                        $data['data'][$x]['group'] = $day_of_the_week;
                        $x++;                        
                    }
                }
            }
            
            $data['groups'] = $groups;
        }
        else
        if($target == 'day') {
            
            $date = (string) trim($this->configXY->URI['date']);
            if(substr_count($date, '-') != 2) return $this->_404_ ();
            list($year, $month, $day) = explode('-', $date);
            
            /* Initialize. */
            $date_end = ($year . '-' . ((($month + 1) < 10) ? ('0' . ($month + 1)) : ($month + 1)) . '-1');

            $sql = "
                SELECT  rta.*,
                        CONCAT_WS(' ',firstname,middlename,lastname) AS requestor,
                        loc.name AS location
                FROM    rta_forms rta
                LEFT
                JOIN    users u
                ON      rta.requested_by_id=u.id
                LEFT
                JOIN    sbu_locations loc
                ON      rta.location=loc.id
                WHERE   rta.state=1
                AND     rta.date_filed BETWEEN rta.date_filed AND '" . $date_end . "'";

            $query = $this->db->query($sql);

            $data['data'] = array();        
            
            $day = (int) $day;
            $test_date = $month . '/' . (($day < 10) ? ('0' . $day) : $day) . '/' . $year;
            
            if($query->num_rows()) {

                foreach($query->result() as $row) {

                    $schedule = $row->schedule;
                    if(substr_count($schedule, ',')) {
                        $schedule = explode(',', $schedule);
                    } else $schedule = array($schedule);

                    foreach($schedule as $value) {

                        $value = trim($value);
                        if($value == $test_date) {
                            $data['data'][] = $row;
                        }                        
                    }
                }
            }
        }
        
        $grouped_by_week_number = $this->calendar_model->getWeekNumbersAll($year);
        $this->configXY->JS_VARS['[var=false]CALENDAR.grouped_by_week_number'] = json_encode($grouped_by_week_number);
        
        $data['access_level'] = $this->access_level;
        $data['grouped_by_week_number'] = $grouped_by_week_number;
        $data['content'] = $this->getViewFile(__FUNCTION__, $data);        
        $this->load->view('main', $data);
    }
    
    public function async_get_rtas_by_week() {
        
        $this->securePage();
        if(empty($this->session) || empty($_POST)) exit();
        $input = $_POST;
        unset($input['t']);        
        extract($input);
        
        if($week_no == '' || $date_end == '' || $date_subject == '') exit();
        
        list($year, $month) = explode('-', $date_subject);
        
        $date_arr = $this->calendar_model->getWeekNumbersAll($year);
        $date_arr = $date_arr[$week_no];
        
        $date_end = date('Y-m-d', strtotime($date_end));

        $sql = "
            SELECT  rta.id,
                    rta.type_of_test,
                    rta.samples_name,
                    rta.schedule,                        
                    CONCAT_WS(' ',firstname,middlename,lastname) AS requestor,
                    loc.name AS location
            FROM    rta_forms rta
            LEFT
            JOIN    users u
            ON      rta.requested_by_id=u.id
            LEFT
            JOIN    sbu_locations loc
            ON      rta.location=loc.id
            WHERE   rta.state=1
            AND     rta.date_filed BETWEEN rta.date_filed AND '" . $date_end . "'";

        $query = $this->db->query($sql);       
        $groups = array();
        $data['data'] = array();
        if($query->num_rows()) {
            
            $x = 0;
            foreach($query->result() as $row) {

                $schedule = $row->schedule;
                
                if(substr_count($schedule, ',')) $schedule = explode(',', $schedule);
                else $schedule = array($schedule);
                
                foreach($schedule as $value) {

                    $value = trim($value);
                    $tmp = date('D,m', strtotime($value)); /* ex: Fri,03 */

                    /* If matches in the any Days of the current "Week Number". */
                    if(in_array($value, $date_arr)) {
                    
                        list($day_of_the_week, $month_of_this_data) = explode(',', $tmp);

                        if(! in_array($day_of_the_week, $groups)) {
                            $groups[] = $day_of_the_week;
                        }

                        $data['data'][$x]['data'] = $row;
                        $data['data'][$x]['tot'] = $row->type_of_test;
                        $data['data'][$x]['display'] = (intval($month) == intval($month_of_this_data) ? true : false);
                        $data['data'][$x]['group'] = $day_of_the_week;
                        $x++;
                    }
                }
            }
        }
        
        $data['groups'] = $groups;
        
        echo json_encode($data);
    }
}    

/* End of file calendar.php */
/* Location: framework/application/controllers/calendar.php */