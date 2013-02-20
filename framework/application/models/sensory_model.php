<?php defined('BASEPATH') or exit('No direct script access allowed');

class Sensory_model extends CI_Model {
    
    public function __construct() {
        
        parent::__construct ();        
    }
    
    /* Triggered when adding or editing item from
     * the Library. Most specially for Liking, COmpatibility, and JAR.
     * 
     * Trigger: async_library_ae()
     **/
    public function doLibraryAE($id, $type, $label) { /* AXL */
        
        /* START: Check if item already exists. */
        if($id > 0) { $sql_append = " AND id!=" . $id . " "; }        
        $sql = "SELECT id FROM q_library WHERE type='" . $type . "' AND label='" . $label . "'" . $sql_append;
        $query = $this->db->query($sql);
        /* END: Check if item already exists. */
        
        if(! $query->num_rows()) {

            if($id > 0) {

                $sql = "UPDATE ";
                $sql_end = " WHERE id=" . $id;            

            } else {

                $sql = "INSERT INTO ";
                $sql_end = ",created='" . $this->configXY->DATE . "'";
            }

            $sql = $sql . " q_library
                SET     type='" . $type . "',
                        user_id=" . $this->session->id . ",
                        label='" . $label . "'" . $sql_end;
            $this->db->query($sql);

            if(! $id) { $id = $this->db->insert_id(); }

            $response['library'] = $this->getLibrary($type);
            $response['item'] = $label . '_' . $id;
        }
        
        return $response;
    }
    
    /* Checks before deleting item in the Library.
     * Most specially for Liking, COmpatibility, and JAR.
     * 
     * Trigger: async_library_del_check()
     **/
    
    public $ljc_attr_queue = array();
    public function doLibraryDeleteCheck($id, $label, $type) { /* AXL */
        
        if(! $id || ! $label)
            return;
        
        $sql = "
            SELECT  detail
            FROM    q_screen_items
            WHERE   detail
            LIKE    '%" . $label . "%'
            AND     type='" . $type . "'";
        $query = $this->db->query($sql);

        $response = $query->num_rows();
        
        if(! $response) {
            
            $this->xy_ljc_attr_existence($_SESSION['SCREEN'], $type);
            if(! empty($this->ljc_attr_queue)) {
                
                foreach($this->ljc_attr_queue as $attr) {
                    
                    if(substr_count($attr, $label)) {
                        
                        $response++;
                    }
                }
            }            
        }
        
        return $response;
    }
    
    function xy_ljc_attr_existence($subject, $type) { /* AXL */
        
        foreach($subject as $key => $value) {
            
            if($key == 'items') {
                
                foreach($value as $k => $v) {
                    
                    if($v['type'] == $type && ! empty($v['attr'])) {
                        
                        $this->ljc_attr_queue[] = $v['attr'];
                    }
                }

            } else {

                if(is_array($value)) $this->xy_ljc_attr_existence($value, $type);
            }
        }
    }
    
    /* Actual deleting of item in the Library.
     * Most specially for Liking, COmpatibility, and JAR.
     * 
     * Trigger: async_library_del()
     **/
    public function doLibraryDelete($id, $type) { /* AXL */

        if(! $id || ! $type)
            return;
        
        $sql = "DELETE FROM q_library WHERE id=" . $id;
        $this->db->query($sql);
        
        $response['library'] = $this->getLibrary($type);
        
        return $response;
    }
    
    public function getSpecifics_with2ndaryCode() { /* AXL */
        
        $response = array();
        
        /*
         * OR      content LIKE '%duo-trio%'
            OR      content LIKE '%duo trio%'
         */
        $sql = "
            SELECT  id
            FROM    specifics
            WHERE   content LIKE '%triangle%'            
            OR      content LIKE '%2afc%'
            OR      content LIKE '%2-afc%'
            OR      content LIKE '%2 afc%'
            OR      content LIKE '%3afc%'
            OR      content LIKE '%3-afc%'
            OR      content LIKE '%3 afc%'
            OR      content LIKE '%same/different%'
            OR      content LIKE '%same / different%'";
        /*
         * OR      content LIKE '%same/different%'
            OR      content LIKE '%same / different%'
            OR      content LIKE '%same or different%'
         */
        $query = $this->db->query($sql);
        if($query->num_rows()) { foreach($query->result() as $row) { $response[] = $row->id; } }
        
        return $response;
    }
    
    /* Get specifics that should have one (1) scorescheet only.
     * Even though it has 2 or more codes.
     **/
    public function getSpecifics_withOneSS() { /* AXL */
        
        $response = array();
        
        $sql = "
            SELECT  id
            FROM    specifics
            WHERE   content LIKE '%triangle%'
            OR      content LIKE '%duo-trio%'
            OR      content LIKE '%duo trio%'
            OR      content LIKE '%2afc%'
            OR      content LIKE '%2-afc%'
            OR      content LIKE '%2 afc%'
            OR      content LIKE '%3afc%'
            OR      content LIKE '%3-afc%'
            OR      content LIKE '%3 afc%'
            OR      content LIKE 'ranking for preference only'
            OR      content LIKE '%paired comparison%'
            OR      content LIKE '%screening%'
            OR      content LIKE '%same/different%'
            OR      content LIKE '%same / different%'";
        //            OR      content LIKE '%paired preference%'
        

        $query = $this->db->query($sql);
        if($query->num_rows()) { foreach($query->result() as $row) { $response[] = $row->id; } }
        
        return $response;
    }
    
    public function getScreensFor($rta_id, $screen_code = 0, $reset = false) { /* AXL */
        
        if($screen_code > 0) { $sql = " AND code='" . $screen_code . "'"; }
        
        $sql = "SELECT * FROM q_screens WHERE rta_id=" . $rta_id . $sql . " ORDER BY sort_no";
        $query = $this->db->query($sql);
        if($query->num_rows()) {
            
            $screens_total = $query->num_rows();
            $screens = $query->result();
            
            $tmp_screens = array();            
            for($screens_x=0; $screens_x<$screens_total; $screens_x++) {
                
                $screen = $screens[$screens_x];
                
                $details = $this->doScreenItemDetails($rta_id, $screen->id);
                
                $tmp_screens[$rta_id][$screen->code][$screen->sort_no] = array(
                    'id' => $screen->id,
                    'details' => $details,
                    'screen_title_or_label_value' => (($screen->title_label_value == null) ? '' : $screen->title_label_value),
                    'screen_title_or_label_visibility' => $screen->title_label_visibility
                );
                
                $sql = "
                    SELECT  *
                    FROM    q_screen_items
                    WHERE   rta_id=" . $rta_id . "
                    AND     screen_id=" . $screen->id . "
                    ORDER
                    BY      sort_no";
                
                $query = $this->db->query($sql);
                if($query->num_rows()) {

                    $items_total = $query->num_rows();
                    $items = $query->result();
                    
                    for($items_x=0; $items_x<$items_total; $items_x++) {
                
                        $item = $items[$items_x];
                        $tmp_screens[$rta_id][$screen->code][$screen->sort_no]['items'][] = array_merge(array('type' => $item->type, 'rta_id' => $item->rta_id), (array) json_decode($item->detail));
                    }
                }
            }
        }
        
        
        /* Fill-in the "session" with the "screens" from the database. */
        if($reset) {
            
            if($screen_code != '') $_SESSION['SCREEN'][$rta_id][$screen_code] = $tmp_screens[$rta_id][$screen_code];
            else $_SESSION['SCREEN'][$rta_id] = $tmp_screens[$rta_id]; /* Full Reset. */
            
        } else {
              
            if(! empty($tmp_screens[$rta_id]) && empty($_SESSION['SCREEN'][$rta_id])) {

                $_SESSION['SCREEN'][$rta_id] = $tmp_screens[$rta_id];
            }
        }
        
        $reponse = (($screen_code != '') ? $tmp_screens[$rta_id][$screen_code] : $tmp_screens[$rta_id]);
        
        return $reponse;        
    }
    
    public function doScreenItemDetails($rta_id, $screen_id) { /* AXL */
        
        $sql = "SELECT * FROM q_screen_items WHERE rta_id=" . $rta_id . " AND screen_id=" . $screen_id;
        $query = $this->db->query($sql);
        
        $details = '';
        
        if($query->num_rows()) {
            
            foreach($query->result() as $row) {
                
                $detail = (array) json_decode($row->detail);
                unset($detail['header']);
                unset($detail['photo']);
                
                foreach($detail as $d) {
                    
                    if(is_object($d)) break;
                    //print_r($d);
                    //if(! empty($d)) {
                        $details .= xy_limit_string(str_replace(array(
                            '[nl]', '[=ROW=]', '[=LABEL=]',
                            '[=SETTING=]', '[=ITEM=]', '[and]',
                            '[quote]'
                        ), ' ', $d), 50);
                    //}
                }                
            }
        }
        
        return $details;
    }
    
    public function doScreenSort($rta_id, $screen_code, $screens) { /* AXL */
        
        $screens_ss = $_SESSION['SCREEN'][$rta_id][$screen_code];
        
        parse_str($screens, $screens);
        
        $index = 0;
        $tmp = array();
        
        foreach($screens as $key => $value) {
            
            list($session_id, $db_id) = explode('_', $key);
            
            $session_id = (int) $session_id;
            $db_id = (double) $db_id;
            $index++;
            
            foreach($screens_ss as $key_ss => $value_ss) {
                
                if($key_ss == $session_id) {
                    
                    /* Store in temporary handler with new index as sorting order. */
                    $tmp[$index] = $_SESSION['SCREEN'][$rta_id][$screen_code][$session_id];
                }
            }
        }
        
        $screens = $_SESSION['SCREEN'][$rta_id][$screen_code] = $tmp; /* Update SESSION with the new sorted values. */
        $response = $this->doScreenData($rta_id, $screen_code, $screens);
        
        $screens_db = $this->getScreensFor($rta_id, $screen_code);
        
        if(! empty($screens_db)) array_walk ($screens_db, 'xy_remove_id');
        if(! empty($screens)) array_walk ($screens, 'xy_remove_id');
        
        $response['flag'] = ((json_encode($screens_db) == json_encode($screens)) ? 'false' : 'true');
        echo json_encode($response);
    }
    
    public function doScreenReset($rta_id, $code) { /* AXL */
        
        $screens = $this->getScreensFor($rta_id, $code, true);
        $response = $this->doScreenData($rta_id, $code, $screens);
        
        return $response;
    }
    
    public function doScreenData($rta_id, $code, $screens) { /* AXL */
        
        $html = '';
        
        if(! empty($screens)) {
            
            foreach($screens as $number => $data) {
                
                $items = $data['items'];
                    
                $details = $data['details'];

                if($details != '') $details = wordwrap($details, 20, '<br />', true);

                xy_screen_and_item_string_decode($data['screen_title_or_label_value']);
                $label = $data['screen_title_or_label_value'];
                if($label == '') $label = ' <u>Click</u> to <b>change</b> label';

                $count = $number + 1;
                
                $option = '<div style="text-align: center; font-size: 20px"><b id="screennumber_' . $code . '_' . $count . '">' . $number . '</b></div>' .
                    '<div style="padding: 5px">' .
                        '<div><a title="add item" id="screenitemae_' . $code . '_' . $count . '_trigger" href="javascript:SCREEN.item_ae_pick(\'' . $code . '\',' . $count . ')"><img src="' . xy_url('media/images/16x16/item-add.png') . '" /> Add item</a></div>' .
                        '<div><a title="sort items" id="screenitemsort_' . $code . '_' . $count . '_trigger" href="javascript:SCREEN.item_sort_init(\'' . $code . '\',' . $count . ')"><img src="' . xy_url('media/images/16x16/sort.png') . '" /> Sort items</a></div>' .
                        '<div><a title="delete screen" id="screendel_' . $code . '_' . $count . '_trigger" href="javascript:SCREEN.screen_del(\'' . $code . '\',' . $count . ')"><img src="' . xy_url('media/images/16x16/delete.png') . '" /> Delete</a></div>' .
                        '<div><a title="preview screen" id="screenpreview_' . $code . '_' . $count . '_trigger" target="_blank" href="' . xy_url('exam/preview/' . $rta_id . '/' . $code . '/' . ($count - 1)) . '"><img src="' . xy_url('media/images/16x16/preview.png') . '" /> Preview</a></div>' .
                    '</div>';

                $label_html = '<div class="fntWrap" style="width: 153px; position: absolute; min-height: 16px; margin: 5px; padding: 2px; font: 12px Verdana" onkeypress="SCREEN.screenlabel_keypressed(event,\'' . $code . '\',' . $count . ')" onblur="SCREEN.screenlabel_toggle(\'' . $code . '\',' . $count . ',false); SCREEN.update(\'' . $code . '\',' . $count . ')" onclick="SCREEN.screenlabel_toggle(\'' . $code . '\',' . $count . ',true)" id="screenlabel_' . $code . '_' . $count . '" detail="' . $data['id'] . '">' . $label . '</div>';
                
                $visibility = (($data['screen_title_or_label_visibility'] == 'shown') ? ' checked="checked"' : '');
                
                $html .= '
                    <tr id="tr_' . $code . '_' . $count . '">
                        <td valign="top" nowrap="nowrap">' . $option . '</td>
                        <td style="padding: 5px"><ul style="padding: 0; list-style: none; margin: 0" id="ul_' . $code . '_' . $count . '">' . (count($items) ? $this->doScreenItemsGenerate($items, $code, $count) : '') . '</ul></td>
                        <td><div style="padding: 5px; color: #777">' . $details . '</div></td>
                        <td valign="top">' . $label_html . '</td>
                        <td align="center"><input id="screenlabel_' . $code . '_' . $count . '_visibility" type="checkbox"' . $visibility . ' onclick="SCREEN.update(\'' . $code . '\',' . $count . ')" /></td>
                    </tr>';
            }

        } else $screens = array();
        
        $response['count'] = count($screens);
        $response['html'] = $html;
        
        return $response;
    }
    
    public function doScreenItemsGenerate($items, $code, $count) { /* AXL */
        
        if(empty($items))
            return;
        
        $html = '';
        
        foreach($items as $sort_no => $item) {
            
            $sort_no++;
            
            $header = (($item['header'] == '') ? strtoupper($item['type']) : $item['header']);
            xy_screen_and_item_string_decode($header);
            
            $html .= '<li style="padding: 0; margin: 0; width: 250px" id="screenitem_' . $code . '_' . $count . '_' . $sort_no . '"><a title="delete" class="item_' . $code . '_' . $count . '_del_trigger" href="javascript:SCREEN.item_del(' . $sort_no . ',\'' . $code . '\',' . $count . ')"><img src="' . xy_url('media/images/16x16/delete.png') . '" /></a> <a title="edit" class="item_' . $code . '_' . $count . '_edit_trigger" href="javascript:SCREEN.item_ae_picked(\'' . $item['type'] . '\',' . $sort_no . ',\'' . $code . '\',' . $count . ')" style="font: 12px Verdana"><span>' . $header . '</span></a></li>';
        }
        
        return $html;
    }
    
    /* Get Libarary details.
     * i.e. For JS variable updates.
     */
    public function getLibrary($item_type = NULL) { /* AXL */
        
        $item_with_library_arr = array('liking', 'compatibility', 'jar');
        $response = array();
        
        if($item_type && in_array($item_type, $item_with_library_arr)) {
            
            $sql = "WHERE type='" . $item_type . "'";
        }
        
        $sql = "SELECT id,type,label FROM q_library " . $sql . " ORDER BY type";
        $query = $this->db->query($sql);
        if($query->num_rows()) {

            foreach($query->result() as $row) {

                if(! is_array($response[$row->type])) $response[$row->type] = array();
                $row_tmp = (array) $row;
                unset($row_tmp['type']);

                array_push($response[$row->type], $row_tmp);
            }
        }
        
        return $response;
    }

/***************************************************************************************************************************
 * START:
 * Score-sheet DB manipulations.
 ***************************************************************************************************************************/
    
    public function doScoreSheetSearch($search) { /* AXL */
        
        if($search == '')
            return;
        
        $sql = "
            SELECT  q.rta_id,
                    c.name AS ss_name,
                    c.content,
                    c.id
            FROM    q
            INNER
            JOIN    q_copies c
            ON      q.rta_id=c.rta_id            
            WHERE   c.name LIKE '%" . $search . "%'";
        
        $query = $this->db->query($sql);
        
        $response[0] = $query->num_rows();
        
        if($response[0]) { $response[1] = $query->result(); }
        
        return $response;
    }
    
    public function doScoreSheetLoad($rta_id, $id, $code) { /* AXL */
        
        if(! $rta_id || ! $id)
            return;
        
        $sql = "
            SELECT  content
            FROM    q_copies
            WHERE   rta_id=" . $rta_id . "
            AND     id=" . $id;
        
        $query = $this->db->query($sql);
        
        if($query->num_rows()) {
            
            $screens = unserialize(base64_decode($query->row()->content));
            $response = $this->doScreenData($rta_id, $code, $screens);
            $response['screens'] = $screens;
        }
        
        return $response;
    }
    
    public function getPatternTypes($spec) {
        
        $response = array();
        
        if($spec == 'triangle') $sql_content = "content LIKE '%triangle%'";
        elseif($spec == '2afc') $sql_content = "content LIKE '%2-afc%' OR content LIKE '%2 afc%' OR content LIKE '%2afc%'";
        elseif($spec == '3afc') $sql_content = "content LIKE '%3-afc%' OR content LIKE '%3 afc%' OR content LIKE '%3afc%'";
        elseif($spec == 'sd') $sql_content = "content LIKE '%same/different%' OR content LIKE '%same / different%' OR content LIKE '%same or different%'";
        
        $sql = "SELECT id FROM specifics WHERE " . $sql_content;
        $query = $this->db->query($sql);
        if($query->num_rows()) { foreach($query->result() as $row) { $response[] = $row->id; } }
        
        return $response;
    }
    
    public function doCodeCombinationFill($qid, $specific_for_code_distribution, $code_1, $code_2, $respondents, $code_control = null) { /* AXL */
        
        $code_combination = $_3afc = $_2afc = $triangle = array();
        
        $triangle = $this->getPatternTypes('triangle');
        $_2afc = $this->getPatternTypes('2afc');
        $_3afc = $this->getPatternTypes('3afc');
        $sd = $this->getPatternTypes('sd');
        
        if(in_array($specific_for_code_distribution, array_merge($_2afc, $_3afc, $triangle, $sd))) {
            
            if(in_array($specific_for_code_distribution, $triangle)) { /* Triangle */
                
                $this->load->library('Triangle');
                $code_combination = $this->triangle->generate(array($code_1, $code_2), $respondents);
            
            } else {
                
                parse_str($code_control, $controls);
                $controls = array_values($controls);
                
                $tmp = array_merge($code_1, $code_2);
                $experiments = array();
                
                foreach($tmp as $c) {

                    if(! in_array($c, $controls)) $experiments[] = $c;
                }
                
                if(in_array($specific_for_code_distribution, $_2afc)) {
                    
                    $this->load->library('_2Afc');
                    $code_combination = $this->_2afc->generate($respondents, $code_1, $code_2);
                }
                else
                if(in_array($specific_for_code_distribution, $_3afc)) {
                    
                    $this->load->library('Afc');
                    $code_combination = $this->afc->generate($controls, $experiments, $respondents);
                }
                else
                if(in_array($specific_for_code_distribution, $sd)) {
                    
                    $this->load->library('Sd');
                    $code_combination = $this->sd->generate($respondents, $code_1, $code_2);
                }
            }
            
        } else {
            
            /* When factorial is larger than the number of respondents,
             * then trigger to get all possible permutations. */
            $factorial = xy_factorial(count($code_1));
            
            if($factorial > $respondents) {
                
                return 'permutate';
            } else {
                
                $this->load->library('Permutation');
                $permutations = $this->permutation->generate($code_1, $respondents);

                $permutation_total = count($permutations);
                $permutation_ctr = 0;

                $seat = array();
                for($x=1; $x<=$respondents; $x++) {

                    if($permutation_ctr < $permutation_total) $permutation_ctr++;
                    else $permutation_ctr = 1;

                    $seat[$x] = implode(' ', $permutations[$permutation_ctr - 1]);
                }

                $code_combination = $seat;                
            }
        }
        
        
        /*if($specific == 1) { 
                    
            $this->load->library('Triangle');
            $code_combination = $this->triangle->generate(array($code_1, $code_2), $respondents);

        } else {

            $this->load->library('Permutation');
            $code_combination = $this->permutation->generate($code_1, $respondents);
        }
*/
        if(! empty($code_combination)) {

            $code_combination = json_encode($code_combination);

            $sql = "
                UPDATE  q
                SET     code_combination='" . $code_combination . "'
                WHERE   id=" . $qid;
            $this->db->query($sql);

            $code_combination = json_decode($code_combination);
        }
        
        return $code_combination;
    }
    
    public function doCodeCombinationSave($data) {
        
        array_walk($data, 'xy_input_clean_up_byref');
        extract($data);
        
        if($combination == '')
            return;
        
        $combination = explode(',', $combination);
        $c = array();
        $ctr = 1;
        foreach($combination as $code) { $c[$ctr] = $code; $ctr++; }
        
        if(! empty($c)) {
            
            $sql = "UPDATE q" .
            " SET code_combination='" . mysql_real_escape_string(json_encode($c)) . "'" .
            " WHERE id=" . $q_id .
            " AND rta_id=" . $rta_id;
            
            $this->db->query($sql);
        }
    }
    
    public function doCodeDistributionLoadAll($data) {
        
        extract($data);
        
        if($rta_id == 0 || $q_id == 0)
            return;
        
        $response = array();
        
        /* Ordering is important for batch matching in the JS.
         * views/create_test/step_4.js -> STEP_4.code_distribute_all()
         * */
        $sql = "SELECT s1d,s2d FROM q_code_distributions WHERE rta_id=" . $rta_id . " AND q_id=" . $q_id . " ORDER BY batch";
        $query = $this->db->query($sql);
        if($query->num_rows()) $response = $query->result();
        
        return $response;
    }
    
    public function getQviaRTAID($rta_id, $field = '') {
        
        $rta_id = (double) $rta_id;
        if(! $rta_id)
            return;
        
        $response = array();        
        if($field == '') $field = '*';
        
        $sql = "SELECT " . $field . " FROM q WHERE rta_id=" . $rta_id;
        $query = $this->db->query($sql);
        if($query->num_rows()) $response = $query->row();
        
        return $response;
    }
    
    public function doLoadQList($data) { /* AXL */
        
        array_walk($data, 'xy_input_clean_up_byref');
        extract($data);
        
        if(! $page) $page = 1;

        $limit = $rows;
        if(! $limit) $limit = 15;
        
        /* START: Search. */
        if(! empty($filters)) {
            
            $filters = json_decode(stripslashes($filters));
            
            /* Multiple parameter passed for search. */
            foreach($filters->rules as $rule) {
                
                $field = $rule->field;
                $data = $rule->data;
                
                if($field && $data) {
                
                    if($field == 'name') $field = 'samples_name';
                    
                    if($field == 'schedule') {
                      
                        $sql_search .= " AND rta.schedule LIKE '%" . $data . "%' ";
                        
                    } else $sql_search .= " AND `" . $field . "` LIKE '%" . $data . "%' ";
                }
            }
            
            if($sql_search && strpos($sql_search, 'AND') == 1) $sql_search = " WHERE " . substr($sql_search, 4);
            
        }
        /* END: Search. */
        
        $response = new stdClass();
        
        $sql = "
                SELECT  q.rta_id,
                        q.batch,
                        rta.samples_name AS `name`,
                        rta.type_of_test,
                        rta.schedule,
                        rta.state,
                        q.created,
                        (SELECT COUNT(q_code_distributions.id) FROM q_code_distributions WHERE q_code_distributions.q_id=q.id) AS cd,
                        (SELECT content FROM specifics WHERE id=rta.specific_1) AS specific1_name,
                        (SELECT content FROM specifics WHERE id=rta.specific_2) AS specific2_name
                FROM    q
                LEFT
                JOIN    rta_forms rta
                ON      q.rta_id=rta.id " . $sql_search;
        
        $query = $this->db->query($sql);
        $count = $query->num_rows();
        
        if(! $count) { /* Tell the GRID that there ano records to view. */
            
            $response->total = 0;
            exit(json_encode($response));
        }
        
        if($count > 0) { $total_pages = ceil($count / $limit); }
        else { $total_pages = 0; }

        if($page > $total_pages) $page = $total_pages;
        $start = $limit * $page - $limit;
        
        $sql .= " ORDER BY " . $sidx . " " . $sord . " LIMIT " . $start . " , " . $limit;
        $query = $this->db->query($sql);
        
        $response->page = $page;
        $response->total = $total_pages;
        $response->records = $count;
        
        $x = 0;
        $state = xy_rta_state(true);
        
        foreach($query->result() as $row) {
            
            $response->rows[$x]['id'] = $row->rta_id;
            
            if($row->state == 1) $status = (($row->batch == $row->cd && $row->cd > 0) ? 'DONE' : '---');
            else $status = ucfirst($state[$row->state]);
            
            $response->rows[$x]['cell'] = array(
                ((($page - 1) * $limit) + ($x + 1)),
                $row->rta_id,
                $row->name,
                ucfirst($row->type_of_test),
                $row->specific1_name . (($row->specific2_name != '') ? ('<br />' . $row->specific2_name) : ''),
                str_replace(',', ', ', $row->schedule),
                $status, /* Codes distributed in all batch determines 'DONE' status. */
                date('m/d/Y h:i A', strtotime($row->created))
                
            );
            
            $x++;
        }
        
        echo json_encode($response);
    }
    
    public function doCreateTest__SaveStep_1($data) {
        
        if(empty($data))
            return;
        
        array_walk($data, 'xy_input_clean_up_byref');
        extract($data);
        
        $rta_id = (double) $rta_id;
        if(! $rta_id) return;
        
        $q = $this->getQviaRTAID($rta_id);
        
        if(empty($q)) {
            
            $sql = "
                INSERT
                INTO    q
                SET     instruction='" . $i . "',
                        rta_id=" . $rta_id . ",
                        user_id=" . $this->session->id . ",
                        created='" . $this->configXY->DATE . "'";
        } else {
            
            $sql = "
                UPDATE  q
                SET     instruction='" . $i . "',
                        user_id=" . $this->session->id . "
                WHERE   rta_id=" . $rta_id;
        }
        
        $this->db->query($sql);        
    }
    
    public function doCreateTest__SaveStep_2($data) {
        
        if(empty($data))
            return;
        
        array_walk($data, 'xy_input_clean_up_byref');
        extract($data);
        
        $rta_id = (double) $rta_id;
        if(! $rta_id) return;
        
        $q = $this->getQviaRTAID($rta_id);
        if(empty($q) || $codes == '') return;
        
        /* panreg = Panel Registration. */
        if($panreg_e == 'true' && $panreg_ne == 'true') $reg = 'both';
        elseif($panreg_e == 'true') $reg = 'e';
        elseif($panreg_ne == 'true') $reg = 'ne';
        //else $reg = 'both';
        
        $code_changed = array();
        
        $code_1_orig = xy_code_get($q->codes, 'primary');
        $code_1_new = xy_code_get($codes, 'primary');
        
        /* START :
         * Only checks for changes in the Primary Code (PC) since
         * only in the PC that "screens" are created.
         **/
        $l = 0;        
        for($x=0, $y=count($code_1_orig); $x<$y; $x++) {
            
            if($code_1_orig[$x] != $code_1_new[$x]) {
                
                $code_changed[$l]['orig']   = $code_1_orig[$x];
                $code_changed[$l]['new']    = $code_1_new[$x];
                $l++;
            }
        }
        
        if(! empty($code_changed)) {
            
            foreach($code_changed as $key => $value) {
                
                $sql = "UPDATE q_screens SET code='" . $value['new'] . "' WHERE rta_id=" . $rta_id . " AND code='" . $value['orig'] . "'";
                $this->db->query($sql);
            }
        }
        
        /* Make sure code is numeric/digit and not 'analytical_*' and etc ... */
        $sql = "SELECT id FROM q_screens WHERE rta_id=" . $rta_id . " AND SUBSTRING(code, 1, 1) REGEXP '[[:digit:]]' AND code NOT IN(" . implode(',', $code_1_new) . ")";
        
        $query = $this->db->query($sql);
        $code_delete = array();
        if($query->num_rows()) {
            
            foreach($query->result() as $row) { $code_delete[] = $row->id; }
            
            $code_delete = implode(',', $code_delete);
            $sql = "DELETE FROM q_screen_items WHERE rta_id=" . $rta_id . " AND screen_id IN(" . $code_delete . ")";
            $this->db->query($sql);
            $sql = "DELETE FROM q_screens WHERE rta_id=" . $rta_id . " AND id IN(" . $code_delete . ")";
            $this->db->query($sql);
        }
        
        /* END :
         * Only checks for changes in the Primary Code (PC) since
         * only in the PC that "screens" are created.
         **/
        
        if($q->code_control == '' && $control_codes != '') {
            
            /* If modifies only the "code_control" then
             * no need to clear the "code_combination" field. */
            $sql_update = '';
            
        } else $sql_update = "code_combination='',";
        
        $sql = "
            UPDATE  q
            SET     flow='" . (($flow == 'true') ? 'both' : 'forward') . "',
                    respondents=" . $respondents . ",
                    `batch`=" . (int) $batch . ",
                    batch_content='" . $batch_content . "',
                    registration='" . $reg . "',
                    codes='" . $codes . "',
                    " . $sql_update . "
                    code_control='" . $control_codes . "',
                    product_names='" . $product_names . "'
            WHERE   rta_id=" . $rta_id;
        $this->db->query($sql);
    }
    
    public function doCreateTest__SaveStep_4($data) {
        
        if(empty($data))
            return;
        
        array_walk($data, 'xy_input_clean_up_byref');
        extract($data);
        
        $rta_id = (double) $rta_id;
        $q_id = (double) $q_id;
        
        if($rta_id == 0 || $q_id == 0 || $batch == 0) return;
        
        $q = $this->getQviaRTAID($rta_id);
        if(empty($q)) return;
        
        $sql = "
            SELECT  *
            FROM    q_code_distributions
            WHERE   rta_id=" . $rta_id . "
            AND     q_id=" . $q_id . "
            AND     batch=" . $batch;
        $query = $this->db->query($sql);
        if($query->num_rows()) {
            
            $sql = "
                UPDATE  q_code_distributions
                SET     s1d='" . $s1d . "',
                        s2d='" . $s2d . "'
                WHERE   id=" . $query->row()->id;
                
        } else {
            
            $sql = "
                INSERT
                INTO    q_code_distributions
                SET     s1d='" . $s1d . "',
                        s2d='" . $s2d . "',
                        rta_id=" . $rta_id . ",
                        q_id=" . $q_id . ",
                        `batch`=" . $batch . ",
                        created='" . $this->configXY->DATE . "'";
        }
        
        echo $sql;
        $this->db->query($sql);
    }
    
    public function doDistributeTest__Assign($data) {
        
        if(empty($data))
            return;
        
        array_walk($data, 'xy_input_clean_up_byref');
        extract($data);
        
        $response = $this->doDistributeTest__AssignSQL($date, $batch, $rta_id, $code, $station);
        if($response['sql']) $this->db->query($response['sql']);
        
        $code_arr = array();
        
        //if($code == 'all') {
        if(! is_numeric($code)) {
            
            $rta = $this->getRTA($rta_id, 'type_of_test,specific_1,specific_2');
            $one_ss_only = $this->getSpecifics_withOneSS();
            $specific = $this->getSpecific($rta);
            
            if(in_array($specific, $one_ss_only)) {
                
                $this->doDistributeTest__Enqueue($rta_id, $rta->type_of_test . '_' . $specific); /* Ex: analytical_1. */
                
            } else {
                
                $q = $this->getQviaRTAID($rta_id, 'codes');
                $code_arr = xy_code_get($q->codes, 'primary');

                foreach($code_arr as $code) {

                    $response = $this->doDistributeTest__AssignSQL($date, $batch, $rta_id, $code, $station);
                    if($response['sql']) $this->db->query($response['sql']);

                    $this->doDistributeTest__Enqueue($rta_id, $code);
                }
            }
            
        } else $this->doDistributeTest__Enqueue($rta_id, $code); /* Enqueue to determine which "q" to appear accordingly. */
    }
    
    public function doDistributeTest__AssignSQL($date, $batch, $rta_id, $code, $station) {
        
        $response = array();
        
        $response['id'] = 0;
        
        $sql = "SELECT id,station FROM" .
            " q_distributions WHERE" .
            " `date`='" . $date . "'" .
            " AND batch='" . $batch . "'" .
            " AND rta_id=" . $rta_id .
            " AND code='" . $code . "'";
        $query = $this->db->query($sql); $sql = '';
        if($query->num_rows()) {
            
            /* Ensure it's changeable before updating. */
            if($query->row()->station != $station) { $sql = "UPDATE q_distributions SET station='" . $station . "' WHERE id=" . $query->row()->id; }
            
            $response['id'] = $query->row()->id;
            
        } else $sql = "INSERT INTO q_distributions SET `date`='" . $date . "',batch='" . $batch . "',rta_id=" . $rta_id . ",code='" . $code . "',station='" . $station . "'";
        
        $response['sql'] = $sql;
        
        return $response;
    }
    
    public function doDistributeTest__Enqueue($rta_id, $code) {
        
        $sql = "SELECT id FROM q_distribution_queue WHERE rta_id=" . $rta_id . " AND code='" . $code . "'";
        $query = $this->db->query($sql);

        if($query->num_rows()) $sql = "UPDATE q_distribution_queue SET created='" . $this->configXY->DATE . "' WHERE id=" . $query->row()->id;
        else $sql = "INSERT INTO q_distribution_queue SET rta_id=" . $rta_id . ",code='" . $code . "',created='" . $this->configXY->DATE . "'";

        $this->db->query($sql);
    }
    
    public function doDistributeTest__LoadRTASequence($sequence, $date) {
        
        $sequence = trim($sequence);
        
        $sql = "
            SELECT  rta.id AS rta_id,
                    rta.type_of_test,
                    rta.specific_1 AS spec1,
                    rta.specific_2 AS spec2,
                    q.id AS q_id,
                    rta.samples_name AS rta_name,
                    q.batch,
                    q.batch_content
            FROM    q_schedules qs
            LEFT
            JOIN    q
            ON      qs.rta_id=q.rta_id
            LEFT
            JOIN    rta_forms rta
            ON      q.rta_id=rta.id
            WHERE   qs.`date`='" . date('Y-m-d', strtotime($date)) . "'";
        
        $query = $this->db->query($sql);
        $total = $query->num_rows(); /* Get the total distribution for this date. */
        
        if(! $total) return;
        
        if($sequence) {
            
            $sequence_arr = explode(',', $sequence);
            
            $rta_arr = array();
            foreach($query->result() as $row) { $rta_arr[] = $row->rta_id; }
            
            $tmp = array_merge($sequence_arr, $rta_arr);
            $tmp = array_unique($tmp);
            $tmp = array_merge($tmp);

            for($x=0, $y=count($tmp); $x<$y; $x++) {
                
                if(! in_array($tmp[$x], $sequence_arr)) {
                    
                    $tmp[$x] = trim($tmp[$x]);
                    if($tmp[$x]) $sequence_arr[] = $tmp[$x];
                }
            }
            
            $sequence_arr = array_filter($sequence_arr);
            
            $sequence = implode(',', $sequence_arr);
            $sql_order = " AND rta.id IN (" . $sequence . ") ORDER BY FIELD(rta.id," . $sequence . ") ";
        }
        
        $sql .= $sql_order;
        $query = $this->db->query($sql);
        $html = '';

        if($query->num_rows()) {
            
            if($sequence) {
                
                $sequence = explode(',', $sequence);
                $t = count($sequence);

            } else $t = $query->num_rows();

            $rows = $query->result();
            
            $y = 0;
            for($x=0; $x<$t; $x++) {
                
                if($sequence) {

                    if($sequence[$x] > 0) {

                        $row = $rows[$y];
                        //$spec = $this->getRTA_ListSpecForHTMLID($row);
                
                        $html .= '<li id="rta_' . $row->rta_id . '__item" class="ui-state-highlight">' . $row->rta_name . '</li>';
                        $y++;

                    } else $html .= '<li class="ui-state-highlight"><b>Pause/Break</b></li>';

                } else {
                    
                    $row = $rows[$x];
                    //$spec = $this->getRTA_ListSpecForHTMLID($row);
                    
                    $html .= '<li id="rta_' . $row->rta_id . '__item" class="ui-state-highlight">' . $row->rta_name . '</li>';

                }
            }
        }
        
        return $html;
    }
    
    public function doDistributeTest__LoadQDistribution($date, $sequence = '') {
        
        $response = array();
        
        if($sequence) $sql_order = " ORDER BY FIELD(rta_id," . $sequence . ") ";        
        $sql = "SELECT * FROM q_distributions WHERE `date`='" . $date . "'" . $sql_order;
        
        $query = $this->db->query($sql);
        if($query->num_rows()) {

            $response['total'] = $query->num_rows();
            $response['rows'] = $query->result();
        }
        
        return $response;
    }
    
    public function doDistributeTest__State($data) {
        
        if(empty($data))
            return;

        array_walk($data, 'xy_input_clean_up_byref');
        extract($data);
        
        $response = $this->doDistributeTest__AssignSQL($date, $batch, $rta_id, $code, $station);
        
        $id = $response['id'];
        if($response['sql'] != '') {
            
            $this->db->query($response['sql']);
            if($response['id'] == 0) $id = $this->db->insert_id();            
        }
        
        $sql = "UPDATE q_distributions SET status=" . (int) $state . " WHERE id=" . (int) $id;
        $this->db->query($sql);
        
        $code_arr = array();
        //if($code == 'all') {
        
        if(! is_numeric($code)) {
            
            $rta = $this->getRTA($rta_id, 'type_of_test,specific_1,specific_2');
            $one_ss_only = $this->getSpecifics_withOneSS();
            $specific = $this->getSpecific($rta);
            
            if(! in_array($specific, $one_ss_only)) {
                
                $q = $this->getQviaRTAID($rta_id, 'codes');
                $code_arr = xy_code_get($q->codes, 'primary');

                foreach($code_arr as $code) {

                    $response = $this->doDistributeTest__AssignSQL($date, $batch, $rta_id, $code, $station);

                    $id = $response['id'];
                    if($response['sql'] != '') {

                        $this->db->query($response['sql']);
                        if($response['id'] == 0) $id = $this->db->insert_id();
                    }

                    $sql = "UPDATE q_distributions SET status=" . (int) $state . " WHERE id=" . (int) $id;
                    $this->db->query($sql);
                }
            }
        }
    }
    
    /*(private function getRTA_ListSpecForHTMLID($row) { /* DELETE this. */
        
        /*$spec = '';
        if($row->type_of_test == 'affective' || $row->type_of_test == 'analytical') {

            if($row->type_of_test == 'affective') $spec = $row->spec2;
            elseif($row->type_of_test == 'analytical') $spec = $row->spec1;
            
            $sql = "SELECT content FROM specifics WHERE id=" . $spec;
            $query = $this->db->query($sql);
            if($query->num_rows()) $spec = $query->row()->content;
            
            $spec = '___' . $row->type_of_test . '__' . xy_make_id($spec);
        }
        
        return xy_make_id($spec);
    }*/
    
    public function getRTA($id, $field = '*') {
        
        $id = (double) $id;
        if(! $id)
            return;
        
        $response = array();
        $field = trim($field);
        if($field == '') $field = '*';
        
        $sql = "SELECT " . $field . " FROM rta_forms WHERE id=" . $id;
        $query = $this->db->query($sql);
        if($query->num_rows()) { $response = $query->row(); }
        
        return $response;
    }
    
    public function getRTA_StationAssignmentFields($id = 0, $date = '', $batch = '') {
        
        $id = (double) $id;
        if(! $id || $date == '')
            return;
        
        $rta = $this->getRTA($id);
        if(empty($rta)) return;
        
        $one_ss_only = $this->getSpecifics_withOneSS();
        
        $specific = $this->getSpecific($rta);
        $specific_name = strtolower($this->getSpecific($rta, true));
        
        $one_ss_only = in_array($specific, $one_ss_only);
        
        $q = $this->getQviaRTAID($id);
        
        $code = xy_code_get($q->codes, 'primary');
        $response = '';
        
        $content = $this->getBatch($date);
        $stations = $status = array();

        if(($total = count($content['batch_content'][$batch][$id])) > 0) {

            for($l=0; $l<$total; $l++) {

                $stations[$content['batch_content'][$batch][$id][$l]->code] = $content['batch_content'][$batch][$id][$l]->station;
                $status[$content['batch_content'][$batch][$id][$l]->code] = (($content['batch_content'][$batch][$id][$l]->status == 1) ? 'Pause' : 'Go');
            }
        }

        $assign_per_code_arr = array('sqs'); /* Default(s) for assigning per code. */
        $assign_per_code = in_array($specific_name, $assign_per_code_arr);

        $id_str = 'rta_' . $id;
        
        $code_all = $rta->type_of_test . '_' . $specific;
        //$codes = array($id_str . '__code-all');
        $codes = array($id_str . '__code-' . $code_all);
        
        $response .= '<tr id="' . $id_str . '__name_wrapper"><td><span id="' . $id_str . '__numbering"></span>. <span title="<b>RTA#' . $id . '</b> - ' . ucwords($rta->type_of_test) . '<br />Name: <b>' . $rta->samples_name . '</b><br/>Specific: <b>' . $specific_name . '</b><br />SS: ' . (($one_ss_only) ? 'Single' : 'Multi') . '"><b style="color: #777">' . $rta->samples_name . '</b></span></td></tr>';
        $response_code = '';
        
        $state = ($status[$code_all] == '' || $status[$code_all] == 'Go');
        $icon = ($state) ? 'pause' : 'ok';
        $class = ($state) ? 'pause' : 'go';
        $title = ($state) ? 'paused' : 'flowing';
        $caption = (($status[$code_all] == '') ? 'Go' : $status[$code_all]);
        
        $response_assigner = '
            <tr><td id="' . $id_str . '__icon-' . $code_all . '">
                    <img class="' . $class . '" title="' . $title . '" src="' . xy_url('media/images/16x16/' . $icon . '.png') . '" />
                </td>
                <td><input type="text" id="' . $id_str . '__code-' . $code_all . '" value="' . $stations[$code_all] . '" style="width: 50px; text-align: right" /></td>
                <td><input type="button" style="width: 35px" value="Ok" onclick="STEP_2.assign(this,\'' . $id_str . '\',\'' . $code_all . '\')" /></td>
                <td><input type="button" class="clear" style="background: url(' . xy_url('media/images/16x16/clear.png') . ') top left no-repeat; width: 16px; height: 16px; border: 0" onclick="STEP_2.clear(\'' . $id_str . '__code-' . $code_all . '\',this)" /></td>
                <td><input type="button" class="all" style="width: 55px" value="' . $caption . '" onclick="STEP_2.go(this,\'' . $id_str . '\',\'' . $code_all . '\')" /></td>
            </tr>';

        $response_assigner = '<tr id="' . $id_str . '__assign_wrapper"><td align="right"><table>' . $response_assigner . '</table></td></tr>';
        $response .= $response_assigner;

        if(! $one_ss_only) {
            
            $y = count($code);
            $flag = ($y <= 1);
            
            if($assign_per_code) {
                
                $label = 'Assign Once';
                $display = '';
                
            } else {
                
                $label = 'Assign per Code';
                $display = ' style="display: none"';
            }
            
            if(! $flag) {
                
                $response .= '<tr id="' . $id_str . '__codetrigger_wrapper"><td align="right">[<a href="javascript:;" onclick="STEP_2.assign_percode__field(this,\'' . $id_str . '\')"><b>' . $label . '</b></a>]</td></tr>';
            }
            
            for($x=0; $x<$y; $x++) {
                
                $state = ($status[$code[$x]] == '' || $status[$code[$x]] == 'Go');
                $icon = ($state) ? 'pause' : 'ok';
                $class = ($state) ? 'pause' : 'go';
                $title = ($state) ? 'paused' : 'flowing';
                $caption = ($status[$code[$x]] == '') ? 'Go' : $status[$code[$x]];
                
                $response_code .= '
                    <tr><td id="' . $id_str . '__icon-' . (int) $code[$x] . '">
                            <img class="' . $class . '" title="' . $title . '" src="' . xy_url('media/images/16x16/' . $icon . '.png') . '" />
                        </td>
                        ' . (($code[$x] > 0) ? ('<td><b>' . $code[$x] . '</b></td>') : '') . '
                        <td><input type="text" id="' . $id_str . '__code-' . $code[$x] . '" value="' . $stations[$code[$x]] . '" style="width: 50px; text-align: right" /></td>
                        <td><input type="button" style="width: 35px" value="Ok" onclick="STEP_2.assign(this,\'' . $id_str . '\',' . $code[$x] . ')" /></td>
                        <td><input type="button" id="' . $id_str . '__clear-' . (int) $code[$x] . '" style="background: url(' . xy_url('media/images/16x16/clear.png') . ') top left; width: 16px; height: 16px; border: 0" onclick="STEP_2.clear(\'' . $id_str . '__code-' . $code[$x] . '\',this)" /></td>
                        <td><input type="button" class="go" style="width: 55px" value="' . $caption . '" onclick="STEP_2.go(this,\'' . $id_str . '\',\'' . $code[$x] . '\')" /></td>
                    </tr>';
                
                $codes[] = $id_str . '__code-' . $code[$x];
            }
            
            if($response_code) $response .= '<tr id="' . $id_str . '__code_wrapper"' . $display . '><td align="right"><table>' . $response_code . '</table></td></tr>';
        }
        
        $tmp = $response;
        $response = array();
        $response['html'] = $tmp;
        $response['assign_per_code'] = $assign_per_code;
        $response['codes'] = $codes;
        
        return $response;
    }
    
    public function getRTA_ByDate($date, $field = '*', $sequence = '') {
        
        if(! $date) return;
        $date = date('Y-m-d', strtotime($date));
        
        $response = array();
        $field = trim($field);
        if($field == '') $field = '*';
        
        if($sequence) $sql_order = " ORDER BY FIELD(rta.id," . $sequence . ")";
        
        $sql = "
            SELECT  " . $field . "
            FROM    q_schedules qs
            RIGHT
            JOIN    q
            ON      qs.rta_id=q.rta_id
            LEFT
            JOIN    rta_forms rta
            ON      q.rta_id=rta.id
            WHERE   qs.`date`='" . $date . "'" . $sql_order;
        
        $query = $this->db->query($sql);
        if($query->num_rows()) { 
            
            $response['total'] = $query->num_rows();
            $response['rows'] = $query->result();            
        }
        
        return $response;
    }
    
    public function getSpecific($rta, $name = false) {
        
        $response = 0;
        
        if($rta->type_of_test == 'affective') $response = $rta->specific_2;
        elseif($rta->type_of_test == 'analytical') $response = $rta->specific_1;
        
        if($name) {
            
            $sql = "SELECT content FROM specifics WHERE id=" . $response;
            $query = $this->db->query($sql);
            if($query->num_rows()) $response = $query->row()->content;            
        }
        
        return $response;
    }
    
    public function getExam_ByDate($date, $field = '*') {
        
        if(! $date) return;
        $date = date('Y-m-d', strtotime($date));
        
        $field = trim($field);
        if($field == '') $field = '*';
        
        $response = array();
        $sql = "SELECT " . $field . " FROM exams WHERE `date`='" . $date . "'";
        $query = $this->db->query($sql);
        if($query->num_rows()) $response = $query->row();
        
        return $response;
    }
    
    public function getBatch($date, $sequence = '') {
        
        if(! $date)
            return;
        
        $response = array();
        
        if($sequence) $sql_order = " ORDER BY FIELD(rta.id," . $sequence . ")";
        
        $sql = "
            SELECT  rta.id AS rta_id,
                    q.id AS q_id,
                    rta.samples_name AS rta_name,
                    q.batch,
                    q.batch_content
            FROM    q_schedules qs
            RIGHT
            JOIN    q
            ON      qs.rta_id=q.rta_id
            LEFT
            JOIN    rta_forms rta
            ON      q.rta_id=rta.id
            WHERE   qs.`date`='" . date('Y-m-d', strtotime($date)) . "'" . $sql_order;

        $query = $this->db->query($sql);

        if(($total = $query->num_rows()) > 0) {
            
            $batches = array();
            $batch_list = array();
            
            /* Summarize batch from all RTAs. */
            $rtas = $query->result();
            for($x=0; $x<$total; $x++) {

                $rta = $rtas[$x];
                if($rta->batch_content != '') { $batch_list[] = $rta->batch_content; }
            }

            if(! empty($batch_list)) {

                $batch_list = implode(',', $batch_list);
                $batch_list = explode(',', $batch_list);

                $batches = array_unique($batch_list); /* So not to repeat batch. */
                $batch_list = array();

                /* START: Initialize and sort. */
                foreach($batches as $batch) { $batch_list[] = strtotime($batch, strtotime($this->configXY->DATE)); } /* Actual time formatting. */
                sort($batch_list);
                /* END: Initialize and sort. */

                /* START: Restore format in Layman's. */
                $batches = array();
                foreach($batch_list as $batch) { $batches[] = date('h:i A', $batch); }
                /* END: Restore format in Layman's. */
            }
            
            $response['batch'] = $batches;
            
            /* Get distribution. */
            $distribution = $this->doDistributeTest__LoadQDistribution($date, $sequence);
            
            $batch_list = array();
            if(! empty($distribution)) {

                foreach($batches as $batch) {

                    if(!is_array($batch_list[$batch])) $batch_list[$batch] = array();

                    for($x=0; $x<$distribution['total']; $x++) {

                        if($distribution['rows'][$x]->batch == $batch) {
                            
                            if(! is_array($batch_list[$batch][$distribution['rows'][$x]->rta_id])) {
                                
                                $batch_list[$batch][$distribution['rows'][$x]->rta_id] = array();
                            }
                            
                            $batch_list[$batch][$distribution['rows'][$x]->rta_id][] = $distribution['rows'][$x];
                        }
                    }
                }
                
            }
            
            $response['batch_content'] = $batch_list;
        }
        
        return $response;
    }
 }