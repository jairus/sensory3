<?php defined('BASEPATH') or exit('No direct script access allowed');

/* 
 * Title : Screen Model Class
 * Author: Armande Bayanes (tuso@programmerspride.com)
 * Description: 
 **/

class Screen_model extends CI_Model {
    
    public function __construct() {
        
        parent::__construct ();
    }
    
    public function doScreenCheck($rta_id, $code, $number) { /* AXL */
        
        $sql = "
            SELECT  id
            FROM    q_screens WHERE rta_id=" . $rta_id . "
            AND     code='" . $code . "'
            AND     sort_no=" . $number;
        $query = $this->db->query($sql);
        if($query->num_rows()) $response = $query->row()->id;
        else $response = 0;
        
        return $response;
    }
    
    public function doScreenAE($rta_id = 0, $screen_code = 0) { /* AXL */
        
        $screens = $_SESSION['SCREEN'][$rta_id][$screen_code];
        
        if(empty($screens)) { $screens = array(); }
        
        $this->load->model('sensory_model');
        $screens_db = $this->sensory_model->getScreensFor($rta_id, $screen_code);
        
        $_SESSION['SCREEN'][$rta_id][$screen_code] = $screens;
        
        /* There was a deletion took place in the Session,
         * so update the DB.
         **/
        
        $total = count($screens);
        
        if(($x = count($screens_db)) > $total) {
            
            /* START: Remove screen and its item(s). */
            $remove = $x - $total;
            $total =  $remove;
                
            for($i = 1; $i<=$remove; $i++) {
                
                $sql = "
                    SELECT  id
                    FROM    q_screens
                    WHERE   code='" .$screen_code . "'
                    AND     rta_id=" . $rta_id . "
                    AND     sort_no=" . $x;
                $query = $this->db->query($sql);
                
                $x--;
                if($query->num_rows()) {
                    
                    $id = (double) $query->row()->id;
                    $sql = "SELECT detail FROM q_screen_items WHERE screen_id=" . $id . " AND rta_id=" . $rta_id . " AND `type`='pause_break'";
                    
                    $sql = "DELETE FROM q_screen_items WHERE screen_id=" . $id . " AND rta_id=" . $rta_id;
                    $this->db->query($sql);
                    
                    $sql = "DELETE FROM q_screens WHERE id=" . $id . " AND rta_id=" . $rta_id;
                    $this->db->query($sql);
                }
            }
            /* END: Remove screen ans its item(s). */
            
        }
        
        if($total > 0) {
            
            for($i=1; $i<=$total; $i++) {

                $sql = "
                    SELECT  id
                    FROM    q_screens
                    WHERE   code='" .$screen_code . "'
                    AND     rta_id=" . $rta_id . "
                    AND     sort_no=" . $i;
                $query = $this->db->query($sql);
                if($query->num_rows()) {

                    $id = (double) $query->row()->id;

                    $item = $screens[$i]['items'];
                    $item_db = $screens_db[$i]['items'];

                    if(($titem_db = count($item_db)) > ($titem = count($item))) {

                        $remove = $titem_db - $titem;
                        for($i2 = 1; $i2<=$remove; $i2++) {
                            
                            if($item_db[$i2 - 1]['type'] == 'pause_break') { 
                                
                                $this->doPauseBreakPhotoRemove($item_db[$i2 - 1]['photo']);                                
                            }

                            $sql = "DELETE FROM q_screen_items WHERE screen_id=" . $id . " AND rta_id=" . $rta_id . " AND sort_no=" . $titem_db;
                            $this->db->query($sql);
                            $titem_db--;
                        }
                    } else {

                        unset($item['type']);
                        unset($item['rta_id']);
                        
                        //array_walk($item, 'xy_screen_and_item_string_encode');
                        
                        $sql = "
                            UPDATE  q_screen_items
                            SET     detail='" . json_encode($item) . "'
                            WHERE   screen_id=" . $id . "
                            AND     rta_id=" . $rta_id . "
                            AND     sort_no=" . $i;
                        $this->db->query($sql);
                    }
                }
            }
        }
        
        if(! empty($screens)) {
            
            foreach($screens as $number => $data) {

                $items = $data['items'];
                $details = $data['details'];
                $label = $data['screen_title_or_label_value'];
                $label_visibility = $data['screen_title_or_label_visibility'];

                if(($screen_id = $this->doScreenCheck($rta_id, $screen_code, $number)) > 0) {
                    
                    $sql = "
                        UPDATE  q_screens
                        SET     title_label_value='" . $label . "',
                                title_label_visibility='" . $label_visibility . "',
                                sort_no=" . $number . "
                        WHERE   id=" . $screen_id;

                } else {

                    $sql = "
                        INSERT
                        INTO    q_screens
                        SET     rta_id=" . $rta_id . ",
                                code='" . $screen_code . "',
                                title_label_value='" . $label . "',
                                title_label_visibility='" . $label_visibility . "',
                                sort_no=" . $number . ",
                                created='" . $this->configXY->DATE . "'";
                }

                $query = $this->db->query($sql);
                if(! $screen_id) $screen_id = $this->db->insert_id();
                
                /* START: Do ITEMS. */
                if($screen_id && count($items)) {

                    foreach($items as $sort_no => $item) {

                        array_walk($item, 'xy_input_clean_up_byref');

                        $sql = '';
                        $tmp = $item;
                        unset($tmp['type']);
                        unset($tmp['rta_id']);
                        
                        //array_walk($tmp, 'xy_screen_and_item_string_encode');
                        
                        $sort_no++;
                        
                        /* START: Since escaped strings are being escaped again by JS_ENCODE. */
                        $detail = stripslashes(json_encode($tmp));

                        //xy_screen_and_item_string_encode($detail);
                        $detail = xy_input_clean_up($detail);

                        /* END: Since escaped strings are being escaped again by JS_ENCODE. */
                        
                        if($this->doScreenItemCheck($rta_id, $screen_id, $sort_no)) {
                            
                            /* See if Pause Break. */
                            $screens_db_tmp = $this->sensory_model->getScreensFor($rta_id, $screen_code);
                            $screen_db_item = $screens_db_tmp[$number]['items'][$sort_no - 1];
                            //print_r($screen_db_item);
                            /* When DB item in the same index is not the same with the Session item. */
                            if($screen_db_item['type'] != $item['type']) {
                                if($screen_db_item['type'] == 'pause_break') {
                                    
                                    /* Remove physical file. */
                                    //$this->doPauseBreakPhotoRemove($screen_db_item['photo']);
                                }
                            } else {
                                
                                /* If still pause_break but deleted photo on it. */
                                if($screen_db_item['photo'] != '' && empty($item['photo'])) {
                                    
                                    /* Remove physical file. */
                                    //$this->doPauseBreakPhotoRemove($screen_db_item['photo']);                                    
                                }
                            }
                            
                            $sql = "
                                UPDATE  q_screen_items
                                SET     `type`='" . $item['type'] . "',
                                        detail='" . $detail . "'
                                WHERE   sort_no=" . $sort_no . "
                                AND     screen_id=" . $screen_id . "
                                AND     rta_id=" . $rta_id;

                        } else {

                            $sql = "
                                INSERT
                                INTO    q_screen_items
                                SET     type='" . $item['type'] . "',
                                        detail='" . $detail . "',
                                        sort_no=" . $sort_no . ",
                                        screen_id=" . $screen_id . ",
                                        rta_id=" . $rta_id . ",
                                        created='" . $this->configXY->DATE . "'";                            
                        }

                        if($sql) {
                            
                            $this->db->query($sql);
                            
                            if($item['type'] == 'pause_break' && ! empty($item['photo'])) {
                                
                                $sql = "SELECT id FROM q_pause_break_inuse_photos WHERE rta_id=" . $rta_id . " AND screen_id=" . $screen_id . " AND photo='" . $item['photo'] . "'";
                                $query = $this->db->query($sql);
                                
                                if(! $query->num_rows()) {
                                    
                                    $sql = "
                                        INSERT
                                        INTO    q_pause_break_inuse_photos
                                        SET     rta_id=" . $rta_id . ",
                                                screen_id=" . $screen_id . ",
                                                sort_no=" . $sort_no . ",
                                                photo='" . $item['photo'] . "'";
                                    $this->db->query($sql);
                                }
                            }
                        }
                    }
                }
                /* END: Do ITEMS. */
            }
        }
        
        //$this->doPauseBreakPhotoAE();
        
        $screens_db = $this->sensory_model->getScreensFor($rta_id, $screen_code);
        
        /* Just re-update the session to be exactly the same as the data from DB. ($item['details']) */
        $screens_ss = $_SESSION['SCREEN'][$rta_id][$screen_code] = $screens_db;
        
        $this->load->model('sensory_model');
        $response = $this->sensory_model->doScreenData($rta_id, $screen_code, $screens_ss);
        $response['flag'] = false;
        
        echo json_encode($response);
    }
    
    public function doScreenItemCheck($rta_id, $screen_id, $sort_no) { /* AXL */
        
        $sql = "
            SELECT  type
            FROM    q_screen_items
            WHERE   sort_no=" . $sort_no . "
            AND     rta_id=" . $rta_id . "
            AND     screen_id=" . $screen_id;

        $query = $this->db->query($sql);
        
        return $query->num_rows();
    }
    
    /*public function doPhotoClear($name) {
        
        $files = glob('TEMP/' . $this->session->id . '_*.*');
        foreach($files as $file) {
            
            if(! preg_match("/" . $name . "/", basename($file))) {
            
                unlink($file);
            }
        }
    }
    */
    public function doPauseBreakPhotoRemove($photo) {
        
        /*$files = glob('TEMP/' . $this->session->id . '_*.*');
        
        foreach($files as $file) {
            
            if(preg_match("/" . $name . "/", basename($file))) {
            
                unlink($file);
            }
        }*/
        
        if($photo == '')
            return;

        $sql = "DELETE FROM q_pause_break_inuse_photos WHERE photo='" . $photo . "'";
        $this->db->query($sql);

        $r = 'TEMP/' . $photo . '_resized.jpg';
        $p = 'TEMP/' . $photo . '_preview.jpg';
        @unlink($r); @unlink($p);
    }
    
    /*public function doPauseBreakPhotoAE() {
        
        $sql = "SELECT photo FROM q_pause_break_inuse_photos";
        $query = $this->db->query($sql);
        $photos_in_use = array();
        if($query->num_rows()) {

            foreach($query->result() as $row) { $photos_in_use[] = $row->photo; }
        }

        $files = glob('TEMP/*.*');
        foreach($files as $file) {

            $ext = pathinfo($file, PATHINFO_EXTENSION);
            $photo = str_replace(array('_resized', '_preview', ('.' . $ext)), '', basename($file));

            if(! empty($photos_in_use)) {
                if(! in_array($photo, $photos_in_use)) {
                    
                    unlink($file);
                }
            }
        }
    }*/
}

/* End of file screen_model.php */
/* Location: ./application/models/screen_model.php */