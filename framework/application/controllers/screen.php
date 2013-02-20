<?php defined('BASEPATH') or exit('No direct script access allowed')?>

<?php
/* 
 * Title : Screen Controller Class
 * Author: Armande Bayanes (tuso@programmerspride.com)
 * Description: Handles questionnaire screens and their items manipulation
 **/

class Screen extends XY_Controller {
    
    private $ci = null;
    
    function __construct() {
        
        parent::__construct();
        
        $this->ci =& get_instance();
        $this->load->model('screen_model');
    }
    
    public function index() {
        
    }
    
    public function async_ae() {
        
        $this->securePage();
        
        if(empty($_POST) || empty($this->session)) exit('No data.');
        $input = $_POST;
        unset($input['t']);
        
        //array_walk($input, 'xy_input_clean_up_byref');
        //$data = $input;
        extract($input);
        
        $this->screen_model->doScreenAE($rta_id, $screen_code);
    }
    
    public function async_screen_copy($rta_id, $screen_code) {
        
        $this->securePage();
        
        if(empty($_POST) || empty($this->session)) exit('No data.');
        $input = $_POST;
        unset($input['t']);
        
        array_walk($input, 'xy_input_clean_up_byref');        
        extract($input);
        
        $this->screen_model->doScreenCopy($rta_id, $screen_code);
    }
    
    public function async_session_update() {
        
        $this->securePage();
        
        if(empty($_POST) || empty($this->session)) exit('No data.');
        $input = $_POST;
        unset($input['t']);
        
        $data = $input;
        array_walk($data, 'xy_screen_and_item_string_encode');
        
        $input = $data;
        array_walk($input, 'xy_input_clean_up_byref');        
        extract($input);
        
        $response = array();
        
        $screen_count--;
        
        if($type == 'screen') {
            
            if(! is_array($_SESSION['SCREEN'][$rta_id][$screen_code])) {
                
                $_SESSION['SCREEN'][$rta_id][$screen_code] = array();
            }
            
            if($command == 'add') {
                
                if(! isset($screen_title_or_label_value)) $screen_title_or_label_value = '';
                if($screen_title_or_label_value == 'Click to change label') $screen_title_or_label_value = '';
                
                if(! isset($_SESSION['SCREEN'][$rta_id][$screen_code][$screen_count])) {

                    $_SESSION['SCREEN'][$rta_id][$screen_code][$screen_count] =
                        array(
                            'details' => '',
                            'screen_title_or_label_value' => $screen_title_or_label_value,
                            'screen_title_or_label_visibility' => $screen_title_or_label_visibility                            
                        );

                } else {

                    $_SESSION['SCREEN'][$rta_id][$screen_code][$screen_count]['screen_title_or_label_value'] = $screen_title_or_label_value;
                    $_SESSION['SCREEN'][$rta_id][$screen_code][$screen_count]['screen_title_or_label_visibility'] = $screen_title_or_label_visibility;
                }
            }
            else
            if($command == 'delete') {
                
                unset($_SESSION['SCREEN'][$rta_id][$screen_code][$screen_count]);

                if(! empty($_SESSION['SCREEN'][$rta_id][$screen_code])) {
                    
                    /* START: Reset index. */
                    $ctr = 1;
                    $tmp = array();
                    foreach($_SESSION['SCREEN'][$rta_id][$screen_code] as $number => $data) {
                        
                        $tmp[$ctr] = $data;
                        $ctr++;
                    }                    
                    $_SESSION['SCREEN'][$rta_id][$screen_code] = $tmp;
                    /* END: Reset index. */                    
                }
            }
        }
        else
        if($type == 'item') {
            
            $data['type'] = $data['item'];
            
            unset($data['screen_code']);
            unset($data['screen_count']);
            unset($data['screen_title_or_label_value']);
            unset($data['screen_title_or_label_visibility']);
            unset($data['item_id']);
            unset($data['item']);
            
            if(! empty($data)) {
                
                if($item_id > 0) {

                    if($command == 'delete') {

                        unset($_SESSION['SCREEN'][$rta_id][$screen_code][$screen_count]['items'][$item_id - 1]);
                        
                        if(! empty($_SESSION['SCREEN'][$rta_id][$screen_code][$screen_count]['items'])) {

                            /* START: Reset index. */
                            $tmp = array();
                            foreach($_SESSION['SCREEN'][$rta_id][$screen_code][$screen_count]['items'] as $number => $data) {

                                $tmp[] = $data;
                            }                    
                            $_SESSION['SCREEN'][$rta_id][$screen_code][$screen_count]['items'] = $tmp;
                            /* END: Reset index. */
                            
                        } else unset($_SESSION['SCREEN'][$rta_id][$screen_code][$screen_count]['items']);

                    }
                    else
                    if($command == 'copy' || $command == 'move') {
                        
                        if(($screen_count == $copy_or_move_to) && $command == 'move') {
                            
                            exit(json_encode(array('error' => 'Moving item to the same screen cannot be performed.')));
                        }
                        
                        /* Get current item. */
                        $item_current = $_SESSION['SCREEN'][$rta_id][$screen_code][$screen_count]['items'][$item_id - 1];
                        $target_screen_item_total = count($_SESSION['SCREEN'][$rta_id][$screen_code][$copy_or_move_to]['items']);
                        
                        /* Copy the current item to it's destination. */
                        $_SESSION['SCREEN'][$rta_id][$screen_code][$copy_or_move_to]['items'][$target_screen_item_total] = $item_current;
                        
                        if($command == 'move') {
                            
                            unset($_SESSION['SCREEN'][$rta_id][$screen_code][$screen_count]['items'][$item_id - 1]);
                            
                            if(! empty($_SESSION['SCREEN'][$rta_id][$screen_code][$screen_count]['items'])) {
                                
                                /* START: Reset index. */
                                $tmp = array();
                                foreach($_SESSION['SCREEN'][$rta_id][$screen_code][$screen_count]['items'] as $number => $data) {

                                    $tmp[] = $data;
                                }                    
                                $_SESSION['SCREEN'][$rta_id][$screen_code][$screen_count]['items'] = $tmp;
                                /* END: Reset index. */

                            } else unset($_SESSION['SCREEN'][$rta_id][$screen_code][$screen_count]['items']); /* Unset totally when left an empty array. */
                        }
                        
                        $response['html'] = '<li style="margin: 0; padding: 0; width: 250px" id="screenitem_' . $screen_code . '_' . ($copy_or_move_to + 1) . '_' . ($target_screen_item_total + 1) . '">' .
                            '<a title="delete" class="item_' . $screen_code . '_' . ($copy_or_move_to + 1) . '_del_trigger" href="javascript:SCREEN.item_del(' . ($target_screen_item_total + 1) . ',\'' . $screen_code . '\',' . ($copy_or_move_to + 1) . ')">' .
                                '<img src="' . xy_url('media/images/16x16/delete.png') . '" />' .
                            '</a> ' .
                            '<a title="edit" class="item_' . $screen_code . '_' . ($copy_or_move_to + 1) . '_edit_trigger" href="javascript:SCREEN.item_ae_picked(\'' . $item_current['type'] . '\',' . ($target_screen_item_total + 1) . ',\'' . $screen_code . '\',' . ($copy_or_move_to + 1) . ')" style="font: 12px Verdana"><span>' . (($item_current['header'] == '') ? strtoupper($item_current['type']) : $item_current['header']) . '</span></a>' .
                        '</li>';
                        
                        $response['target_screen_item_total'] = $target_screen_item_total;
                    }
                    else {

                        /* On EDIT. */
                        $_SESSION['SCREEN'][$rta_id][$screen_code][$screen_count]['items'][$item_id - 1] = $data;                        
                    }   

                } else {

                    /* On ADD of new item. */

                    if(! isset($_SESSION['SCREEN'][$rta_id][$screen_code][$screen_count]['items'])) {

                        $_SESSION['SCREEN'][$rta_id][$screen_code][$screen_count]['items'] = array();
                    }

                    array_push($_SESSION['SCREEN'][$rta_id][$screen_code][$screen_count]['items'], $data);

                    /* START: New item to append. */
                    $total_items = count($_SESSION['SCREEN'][$rta_id][$screen_code][$screen_count]['items']);

                    $response['html'] = '<li style="margin: 0; padding: 0; width: 250px" id="screenitem_' . $screen_code . '_' . ($screen_count + 1) . '_' . $total_items . '">' .
                        '<a title="delete" class="item_' . $screen_code . '_' . ($screen_count + 1) . '_del_trigger" href="javascript:SCREEN.item_del(' . $total_items . ',\'' . $screen_code . '\',' . ($screen_count + 1) . ')">' .
                            '<img src="' . xy_url('media/images/16x16/delete.png') . '" />' .
                        '</a> ' .
                        '<a title="edit" class="item_' . $screen_code . '_' . ($screen_count + 1) . '_edit_trigger" href="javascript:SCREEN.item_ae_picked(\'' . $data['type'] . '\',' . $total_items . ',\'' . $screen_code . '\',' . ($screen_count + 1) . ')" style="font: 12px Verdana"><span>' . (($data['header'] == '') ? strtoupper($data['type']) : $data['header']) . '</span></a>' .
                    '</li>';
                    /* END: New item to append. */
                }
            }
        }
        
        $this->load->model('sensory_model');
        $screens_db = $this->sensory_model->getScreensFor($rta_id, $screen_code);
        $screens_ss = $_SESSION['SCREEN'][$rta_id][$screen_code];
        
        if(! empty($screens_db)) array_walk($screens_db, 'xy_remove_id');
        else $screens_db = '';
        
        if(! empty($screens_ss)) array_walk($screens_ss, 'xy_remove_id');
        else $screens_ss = '';
        
        $response['flag'] = ((json_encode($screens_db)) == (json_encode($screens_ss)) ? false : true);
        $response['command'] = $command;
        
        echo json_encode($response);
    }
    
    public function async_pausebreak_photo_upload($item_id = 0, $photo = '') {
        
        //if($id == 0)
          //  return;
        
        if(empty($this->session)) exit('Active session is required to access this area.');
        
        if(($filename = $_FILES['pause_break_photo']['name']) != '' ) {
            
            $img_arr = array('gif', 'jpg', 'png');
            $extension = pathinfo($filename, PATHINFO_EXTENSION);
            
            if(in_array(strtolower($extension), $img_arr)) {
                
                if($item_id > 0 && $photo != '') $filename_id_time = $photo;
                else $filename_id_time = $this->session->id . '_' . time();
                
                $tmp = 'TEMP/' . $filename_id_time;
                
                $filename = $tmp . '.' . $extension;
                $filename_resized = $tmp . '_resized.' . $extension;
                $filename_preview = $tmp . '_preview.' . $extension;
                
                $ok = @move_uploaded_file($_FILES['pause_break_photo']['tmp_name'], $filename);
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
                    
                    //$content = @file_get_contents($filename_resized);
                    
                    /*unlink($filename);
                    unlink($filename_resized);
                    
                    if($content) {
                        
                        $sql = "
                            UPDATE  q_temporary_item_data
                            SET     pause_break_photo='" . mysql_real_escape_string($content). "',
                                    pause_break_photo_dimension='" . $w . 'x' . $h . "'
                            WHERE   q_item_id=" . $id;
                        $this->db->query($sql);                        
                    }*/
                    
                    unlink($filename);
                    //echo base64_encode($filename_resized);
                    
                    list($w, $h) = getimagesize($filename_resized);
                    
                    $image = array();
                    $image['dimension'] = $w . 'x' . $h;
                    $image['content'] = file_get_contents($filename_resized);

                    $this->image->readImage((object) $image, 100, $filename_preview);
                    
                    echo $filename_id_time;
                }
            }
        }        
    }
    
    public function async_pausebreak_photo_delete() {
        
        $this->securePage();
        
        if(empty($_POST) || empty($this->session)) exit('No data.');
        $input = $_POST;
        unset($input['t']);
        
        array_walk($input, 'xy_input_clean_up_byref');
        extract($input);
        
        //if($item_id == 0) exit();
        
        $photo_r = 'TEMP/' . $photo . '_resized.jpg';
        if(file_exists($photo_r)) unlink($photo_r);
        
        $photo_p = 'TEMP/' . $photo . '_preview.jpg';
        if(file_exists($photo_p)) unlink($photo_p);
        
        if($item_id) $_SESSION['SCREEN'][$screen_code][$screen_count]['items'][$item_id - 1]['photo'] = '';
        
        echo 'Ok.';
    }
    
    /*public function load_pausebreak_photo($photo_path, $scale) {
        
        $photo_path = base64_decode($photo_path);
        
        //echo $photo_path;
        list($w, $h) = getimagesize($photo_path);
        
        $image['dimension'] = $w . 'x' . $h;
        
        $fp = fopen($photo_path, 'rb');
        if($fp) {
            
            $image['content'] = fread($fp, filesize($photo_path));
            fclose($fp);
        }
        
        //$image['content'] = file_get_contents($photo_path);
        
        
        $this->image->readImage((object) $image, $scale);
    }*/
}

/* End of file screen.php */
/* Location: framework/application/controllers/screen.php */