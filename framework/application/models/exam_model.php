<?php defined('BASEPATH') or exit('No direct script access allowed');

class Exam_model extends CI_Model {
    
    public $date = null;
    
    public function __construct() {
        
        parent::__construct ();
    }
    
    public function getDetail($date) {
        
        $response = array();
        
        $sql = "SELECT * FROM exams WHERE `date`='" . date('Y-m-d', strtotime($date)) . "'";
        $query = $this->db->query($sql);
        if($query->num_rows()) $response = $query->row();
        
        return $response;
    }
    
    /* Get RTAs and stations assigned on each. */
    public function getStationsWithQ() {
        
        if($this->date) $sql_append = " WHERE d.`date`='" . date('Y-m-d', strtotime($this->date, $this->configXY->DATE)) . "' ";

        $response = array();

        $sql =  "SELECT d.rta_id,d.code,d.station,d.batch " .
                "FROM q_distributions AS d " .
                "LEFT JOIN q_distribution_queue AS q " .
                "ON d.code=q.code " . $sql_append .
                "ORDER BY q.id,q.rta_id";  /* Sort according to what is first in the queue. */

        $query = $this->db->query($sql);
        $t = $query->num_rows();

        if($t) {
            
            $rows = $query->result();
            $stations = array();
            
            $this->load->model('sensory_model', '', true);
            
            for($x=0; $x<$t; $x++) {
                
                $row = $rows[$x];
                $station_tmp = array();
                
                if(($comma = substr_count($row->station, ',')) || ($dash = substr_count($row->station, '-'))) {
                    
                    if($comma) {
                        
                        $tmp = explode(',', $row->station);
                        
                        foreach($tmp as $station) {
                            
                            if(substr_count($station, '-')) $station_tmp = array_merge($station_tmp, $this->getStationsOnDash($station));
                            else $station_tmp[] = $station;
                        }
                        
                    } elseif($dash) $station_tmp = array_merge($station_tmp, $this->getStationsOnDash($row->station));

                } else $station_tmp = array($row->station);
                
                $rta = $this->sensory_model->getRTA($row->rta_id, 'type_of_test,specific_1,specific_2');
                $specific = $this->sensory_model->getSpecific($rta);
                $one_ss_only = $this->sensory_model->getSpecifics_withOneSS();
                
                /*if(! is_numeric($row->code)) { /* If string such as 'analytical_*'. */

                /*    if(in_array($specific, $one_ss_only)) { $response[] = array('rta' => $row->rta_id, 'code' => $row->code, 'batch' => $row->batch, 'station' => $station_tmp); }

                } else $response[] = array('rta' => $row->rta_id, 'code' => $row->code, 'batch' => $row->batch, 'station' => $station_tmp);
                */
                
                /* If (string such as 'analytical_*' AND Single SS only) OR strictly numeric only. */
                if( ((! is_numeric($row->code)) && in_array($specific, $one_ss_only)) || is_numeric($row->code) ) {
                    
                    $response[] = array('rta' => $row->rta_id, 'code' => $row->code, 'batch' => $row->batch, 'station' => $station_tmp);
                }
            }
        }
        
        return $response;
    }
    
    private function getStationsOnDash($stations) {
        
        $station_arr = array(0);

        $tmp = explode('-', $stations);
        if(count($tmp) != 2) return $station_arr;
        if($tmp[0] > $tmp[1]) return $station_arr;
        
        $station_arr = array();
        for($x=$tmp[0]; $x<=$tmp[1]; $x++) { $station_arr[] = $x; }
        
        return $station_arr;
    }
    
    public function getQueue() {
        
        $stations = $this->getStationsWithQ();
        $station_number = $this->getIfIPHooked();
        
        $response = $tmp1 = $tmp2 = array();
        
        for($x=0, $y=count($stations); $x<$y; $x++) {
            
            extract($stations[$x]);
            if(in_array($station_number, $station)) { 
                
                $tmp1[] = array('rta' => $rta, 'code' => $code, 'batch' => $batch);
                $tmp2[$rta][] = $code;
            }
        }
        
        $response = array(
            'data' => $tmp1,
            'code' => $tmp2,
        );
        
        return $response;
    }
    
    public function getStationNumberFromIP() {
        
        return $this->getIfIPHooked();
    }
    
    public function getIfIPHooked() {
        
        //if($this->session->id == 1) $this->client->setIP('192.168.0.2');
        
        $sql = "SELECT id,number FROM stations WHERE ip='" . $this->client->getIP() . "'";
        $query = $this->db->query($sql);
        
        return ($query->num_rows()) ? $query->row()->number : 0;
    }
    
    public function getIfStationInQ($rta_id, $screen_code, $station_number = 0, $stations = array()) {
        
        if(empty($stations)) { $stations = $this->getStationsWithQ(); }
        if(! $station_number) $station_number = $this->getIfIPHooked();
        
        $response = false;
        
        for($x=0, $y = count($stations); $x<$y; $x++) {
            
            $station = $stations[$x];
            
            if($station['rta'] == $rta_id && $station['code'] == $screen_code) {
                
                if(in_array($station_number, $station['station'])) {
                    
                    $response = true;
                    break;                    
                }
            }
        }
        
        return $response;
    }
    
    public function getStationRTAs($rta_ids = array()) {

        if(empty($rta_ids))
            return;
        
        $response = array();
        
        $sql = "SELECT id," .
            "type_of_test,specific_1,specific_2,samples_name AS name" .
            " FROM rta_forms" .
            " WHERE id IN(" . implode(',', $rta_ids) . ")";
        
        $query = $this->db->query($sql);
        
        if($query->num_rows()) {
            
            $response['total'] = $query->num_rows();
            $response['rows'] = $query->result();            
        }
        
        return $response;
    }
    
    public function getQ($rta_id = 0, $fields = '') {
        
        $rta_id = (double) $rta_id;
        if($rta_id == 0)
            return;
        
        $response = array();
        
        if(! $fields) $fields = '*';
        
        $sql = "SELECT " . $fields . " FROM q WHERE rta_id=" . $rta_id;
        $query = $this->db->query($sql);
        if($query->num_rows()) $response = $query->row();
        
        return $response;
    }
    
    public function getRTA($rta_id) {
        
        $rta_id = (double) $rta_id;
        if($rta_id == 0)
            return;
        
        $response = array();
        
        $sql = "SELECT type_of_test,specific_1,specific_2 FROM rta_forms WHERE id=" . $rta_id;
        
        $query = $this->db->query($sql);
        if($query->num_rows()) $response = $query->row();
        
        return $response;
    }
    
    public function doUpdateSession() {
        
        setcookie("EXAM", serialize($_SESSION['EXAM']), strtotime($this->configXY->DATE) + 31536000, "/");
    }
    
    public function doUpdateSessionDB($data) {
        
        if(empty($data))
            return;
        
        array_walk($data, 'xy_input_clean_up_byref');
        extract($data);
        
        $rta_id = (double) $rta_id;
        
        if(empty($_SESSION['EXAM'][$screen_code]) || $rta_id == 0)
            return;
        
        foreach($_SESSION['EXAM'][$screen_code] as $screen_count => $items) {
            
            $sql = "SELECT id FROM exam_sessions" .
                " WHERE rta_id=" . $rta_id .
                " AND user_id=" . $this->session->id .
                " AND screen_code='" . $screen_code . "'" .
                " AND screen_count=" . $screen_count;
            $query = $this->db->query($sql);
            
            $sql_set = " SET rta_id=" . $rta_id .
                ",user_id=" . $this->session->id .
                ",screen_code='" . $screen_code . "'" .
                ",screen_count=" . $screen_count .
                ",content='" . base64_encode(serialize($items)) . "'" .
                ",created='" . $this->configXY->DATE . "'";

            if($query->num_rows()) $sql = "UPDATE exam_sessions" . $sql_set . " WHERE id=" . $query->row()->id;
            else $sql = "INSERT INTO exam_sessions" . $sql_set;
            
            $this->db->query($sql);            
        }
        
        $this->doUpdateSession(); /* Update cookie also. */
    }
    
    /* Get the number of items with answer in the specified screen / step. */
    //public function getNumberOfItemsWithAnswer($screen_code = '', $screen_count = 0, $items = array()) {
    
    public function getNumberOfItemsWithAnswer($data) {    
        
        extract($data);
        
        if(empty($items)) $items = $_SESSION['EXAM'][$screen_code][$screen_count]['items'];
        if(empty($items)) return array(0, 0); /* If still empty. */
        
        //print_r($items);
        
        $item_total = count($items);
        $item_check = 0;

        foreach($items as $value) { if(is_object($value)) $item_check++; }
        
        return array($item_check, $item_total);
    }
    
    public function doMessage($message = '', $back = true) {
        
        $style = 'padding: 5px;' 
        . 'border: 2px solid #CCC;'
        . 'margin: 10px 0 50px 0;'
        . '-moz-border-radius: 5px;'
        . '-webkit-border-radius: 5px;'
        . 'border-radius: 5px;'
        . 'background: #FFF';
        
        if($back) $url = '<div>Go back to the list of <a title="go back" href="' . xy_url('exam') . '"><b>EXAM</b></a>.</div>';
        
        return '<div style="' . $style . '"><div style="min-height: 100px">' . $message . '</div>' . $url . '</div>';
    }
    
    /* Get percentage finished from the total screens per questionnaire.
     * Ex:  1 Questionnaire = n Screens
     *      1 Screen        = n Items
     **/
    public function getPercentage($rta_id, $screen_code) {
        
        $sql = "SELECT screen_count,content" .
            " FROM exam_sessions" .
            " WHERE user_id=" . $this->session->id .
            " AND rta_id=" . $rta_id . 
            " AND screen_code='" . $screen_code . "'";

        $query = $this->db->query($sql);
        if($query->num_rows()) {

            $screens = $query->result();
            $total = $query->num_rows();
            $percentage = 0;
            for($x=0; $x<$total; $x++) {

                $screen = $screens[$x];

                $content = unserialize(base64_decode($screen->content));
                list($item_check, $item_total) = $this->getNumberOfItemsWithAnswer($content);

                echo $item_total, ' ', $item_check, ' ', (($item_check / $item_total) * 100) . '%', '<br />';

                $percentage += round(($item_check / $item_total), 2);
                //$y = 0;
                //foreach($content['items'] as $item) {

                //    $this->exam_model->getNumberOfItemsWithAnswer($specific_as_code, $y);
                //    $y++;
                //}



                //echo '<pre style="text-align: left">';
                //print_r($content);
                //echo '</pre>';
            }

            $percentage = (round(($percentage / $total), 2) * 100);
            
        } else $percentage = 0;
        
        return $percentage;
    }
    
    public function getItemState($rta_id, $screen_code) {
        
        if(! $rta_id || ! $screen_code)
            return false;
        
        $status = false;
        
        $sql = "SELECT status FROM q_distributions WHERE rta_id=" . $rta_id . " AND code='" .  $screen_code . "'";
        $query = $this->db->query($sql);
        if($query->num_rows()) $status = ($query->row()->status == 1) ? true : false;
        /*else {
            
            $sql = "SELECT status FROM q_distributions WHERE rta_id=" . $rta_id . " AND code='all'";
            $query = $this->db->query($sql);
            if($query->num_rows()) $status = ($query->row()->status == 1) ? true : false;
        }*/
        
        return $status;
    }
    
    public function getSession($rta_id, $screen_code, $screen_count) {
        
        $response = array();
        
        $sql = "SELECT content FROM exam_sessions " .
                "WHERE user_id=" . $this->session->id . " " .
                "AND rta_id=" . $rta_id . " " .
                "AND screen_code='" . $screen_code . "' " .
                "AND screen_count='" . $screen_count . "'";
        $query = $this->db->query($sql);
        if($query->num_rows()) $response = unserialize(base64_decode($query->row()->content));
        
        return $response;
    }
}

/* End of file exam_model.php */
/* Location: ./application/models/exam_model.php */