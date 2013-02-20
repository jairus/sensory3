<?php defined('BASEPATH') or exit('No direct script access allowed');

/* 
 * Title : RTA Model Class
 * Author: Armande Bayanes (tuso@programmerspride.com)
 * Description: Handles RTA concerns ...
 **/

class Rta_model extends CI_Model {
	
    public function __construct() {
        
        parent::__construct ();
    }
    
    public function doLoadList($data) { /* AXL */
        
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
                
                    if($field == 'state' && $data > 0) $sql_search .= " AND rta.`" . $field . "`=" . ($data - 1) . " ";
                    elseif($field == 'requested_by' || $field == 'approved_by' || $field == 'location') $sql_search .= " HAVING rta.`" . $field . "` LIKE '%" . $data . "%' ";
                    //elseif($field == 'location') $sql_search .= " AND `loc.name` LIKE '%" . $data . "%' ";
                    else $sql_search .= " AND rta.`" . $field . "` LIKE '%" . $data . "%' ";
                }
            }
            
            if($sql_search && strpos($sql_search, 'AND') == 1) $sql_search = substr($sql_search, 4);
            
        }
        /* END: Search. */
        
        $response = new stdClass();
        
        if($sql_search) {
                
            if(strpos($sql_search, 'HAVING') == 1) {} /* If 'HAVING' clause is at the beginning of the string. */
            else $sql_search = ' WHERE ' . $sql_search; /* Else. */
        }
        
        $sql = "
            SELECT  rta.id,
                    rta.state,
                    rta.type_of_test,
                    rta.samples_name,
                    rta.samples_desc,
                    rta.schedule,
                    rta.date_filed,
                    (SELECT username FROM users WHERE id=rta.requested_by_id) AS requested_by,                    
                    sbu.name AS sbu_name,
                    loc.name AS location,
                    (SELECT username FROM users WHERE id=rta.processed_by_id) AS approved_by,
                    (SELECT content FROM specifics WHERE number=1 AND id=rta.specific_1) AS spec1,
                    (SELECT content FROM specifics WHERE number=2 AND id=rta.specific_2) AS spec2
            FROM    rta_forms rta            
            LEFT
            JOIN    sbu
            ON      rta.sbu=sbu.id
            LEFT
            JOIN    sbu_locations loc
            ON      rta.location=loc.id " . $sql_search;
        
        if($ownlist) $sql .= (($sql_search) ? ' AND ' : ' WHERE ') . " rta.user_id=" . $this->session->id;
        
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
            
            $response->rows[$x]['id'] = $row->id;
            
            if($self) {
                $response->rows[$x]['cell'] = array(
                    $row->id,
                    $row->id,
                    date('m/d/Y h:i A', strtotime($row->date_filed)),
                    ucfirst($state[$row->state]),
                    strtoupper($row->type_of_test),
                    '<b>' . $row->spec1 . '; ' . $row->spec2 . '</b>',
                    $row->samples_name,
                    $row->samples_desc,
                    $row->schedule
                );
            }
            else
            if($adminlist) {
                
                $ss_creation = 3;
                
                if($row->type_of_test == 'affective' || $row->type_of_test == 'analytical') {
                    
                    if($row->state == 1) { /* Only when 'Approved'. */
                        $sql = "SELECT id FROM q WHERE rta_id=" . $row->id;
                        $query = $this->db->query($sql);

                        if($query->num_rows()) $ss_creation = 1;
                        else $ss_creation = 2;
                    }
                }
                
                $response->rows[$x]['cell'] = array(
                    ((($page - 1) * $limit) + ($x + 1)),
                    $row->id,
                    ucfirst($state[$row->state]),
                    strtoupper($row->type_of_test),
                    '<b>' . $row->spec1 . '; ' . $row->spec2 . '</b>',
                    $row->samples_name,
                    $row->samples_desc,
                    $row->requested_by,
                    $row->approved_by,
                    $row->schedule,
                    date('m/d/Y', strtotime($row->date_filed)),
                    $row->location,
                    $ss_creation, /* Action for creating a questionnaire. */
                );
            }
            else
            if($ownlist) {
                
                $response->rows[$x]['cell'] = array(
                    ((($page - 1) * $limit) + ($x + 1)),
                    $row->id,                    
                    ucfirst($state[$row->state]),
                    strtoupper($row->type_of_test),
                    '<b>' . $row->spec1 . '; ' . $row->spec2 . '</b>',
                    $row->samples_name,
                    $row->samples_desc,                    
                    'You',
                    $row->approved_by,
                    $row->schedule,
                    date('m/d/Y', strtotime($row->date_filed)),
                    $row->location
                );
            }
            
            $x++;
        }
        
        echo json_encode($response);
    }
    
    public function doLoadDetail($id, $access = '', $read_only = false) { /* AXL */
        
        $id = (double) $id;
        if(! $id)
            return $this->_404_();
        
        if($access == 'admin') {
            
            /* Get data from the RTA record directly when who's currently accessing is an ADMIN. */
            $sql = "SELECT * FROM rta_forms WHERE id=" . $id;
            $query = $this->db->query($sql);

            if($query->num_rows()) $rta = $query->row();
            
        } elseif($access == 'po') {
            
            /* Get data from the saved History when who's currently accessing is a PO. */
            $sql = "SELECT * FROM rta_po_history WHERE rta_form_id=" . $id . " AND user_id=" . $this->session->id;
            $query = $this->db->query($sql);

            if($query->num_rows()) {
                $tmp = $query->row();
                $rta = (object) unserialize(base64_decode($tmp->data));
            }
            
        } else {
            
            $sql = "SELECT * FROM rta_forms WHERE id=" . $id . " AND user_id=" . $this->session->id;
            $query = $this->db->query($sql);

            if($query->num_rows()) $rta = $query->row();
        }
        
        if(! empty($rta)) {

            $rta->date_preferred = date('m/d/Y', strtotime($rta->date_preferred));

            if($rta->rta_product_data_ids != '') {
                $sql = "SELECT * FROM rta_product_data WHERE id IN(" . $rta->rta_product_data_ids . ")";
                $query = $this->db->query($sql);
                if($query->num_rows()) {

                    $rta->product_data = $query->result_array();
                    for($x=0; $x<$query->num_rows(); $x++) {

                        $rta->product_data[$x]['pd'] = ($rta->product_data[$x]['pd'] != '0000-00-00') ? date('m/d/Y', strtotime($rta->product_data[$x]['pd'])) : '';
                        $rta->product_data[$x]['cu'] = ($rta->product_data[$x]['cu'] != '0000-00-00') ? date('m/d/Y', strtotime($rta->product_data[$x]['cu'])) : '';
                    }

                    if(! $read_only) $this->configXY->JS_VARS['[var=false]RTA_FORM.product_data'] = json_encode($rta->product_data);
                }
            }
        }
        
        return $rta;
    }
    
    public function doSpecificGet($type, $number) {
        
        $sql = "
            SELECT  *
            FROM    specifics
            WHERE   type='" . $type . "'
            AND     number='" . $number . "'";
        $query = $this->db->query($sql);
        
        $results = array();
        if($query->num_rows()) {
            
            foreach($query->result() as $row) {
                $results[] = $row;
            }
        }
        
        return $results;
    }
    
    public function doLoadFields($session, $rta = NULL) {
        
        if(! $rta) $rta = new stdClass;
        
        /* START: SBU. */
        $html = '';
        $query = $this->db->query("SELECT * FROM sbu");        
        if($query->num_rows() > 0) {
            
            $this->configXY->JS_VARS['SBU'] = 'new Array()';
            $x = 0;
            foreach($query->result() as $row) {

                /* Create JS variables. */
                $this->configXY->JS_VARS['[var=false]SBU[' . $x . ']'] = "'" . $row->name . "'";
                $x++;
                
                /* Create select options. */
                $html .= '<option value="' . $row->id . '"' . (($row->id == $rta->sbu) ? ' selected="selected"' : '') . '>' . $row->name . '</option>';                
            }
        } $data['sbu'] = $html;
        /* END: SBU. */
        
        /* START: Test Purpose. */
        $test_purpose_ids = explode(',', $rta->test_purpose_ids);
        $html = '<table>';
        $query = $this->db->query("SELECT * FROM test_purpose ORDER BY content");
        if($query->num_rows() > 0) {
            
            $x = 0;
            foreach($query->result() as $row) {
                
                if($x%2==0) { $html .= '</tr><tr>'; } $x++;
                
                /* Create select options. */
                $html .= '<td><input type="checkbox" name="tpurpose" id="tpurpose_' . $row->id . '" value="' . $row->id . '"' . (in_array($row->id, $test_purpose_ids) ? ' checked="checked"' : ''). '><label for="tpurpose_' . $row->id . '">' . $row->content . '</label></td>';
            }
        } $data['test_purpose'] = $html . '</table>';
        /* END: Test Purpose. */
        
        $spec1_analytical   = $this->doSpecificGet('analytical', 1);
        $spec1_affective    = $this->doSpecificGet('affective', 1);
        $spec1_micro        = $this->doSpecificGet('micro', 1);
        $spec1_physico_chem = $this->doSpecificGet('physico_chem', 1);
        
        $spec2_analytical   = $this->doSpecificGet('analytical', 2);
        $spec2_affective    = $this->doSpecificGet('affective', 2);
        $spec2_micro        = $this->doSpecificGet('micro', 2);
        $spec2_physico_chem = $this->doSpecificGet('physico_chem', 2);
        
        $spec1_analytical_html = '';
        $spec1_affective_html = '';
        $spec1_micro_html = '';
        $spec1_physico_chem_html = '';
        
        $spec2_affective_html = '';
        $spec2_analytical_html = '';
        $spec2_micro_html = '';
        $spec2_physico_chem_html = '';
        
        if(($t = count($spec1_analytical)) > 0) {
            
            /* Initialize JS variables. */
            $this->configXY->JS_VARS['SPEC1_ANALYTICAL'] = 'new Array()';
            
            for($x=0; $x<$t; $x++) {
                
                $row = $spec1_analytical[$x];
                
                /* Create JS variables. */
                $this->configXY->JS_VARS['[var=false]SPEC1_ANALYTICAL[' . $x . ']'] = "'" . $row->content . "'";
                
                $spec1_analytical_html .= '<option value="' . $row->id . '"' . (($rta->type_of_test == 'analytical' && $row->id == $rta->specific_1) ? ' selected="selected"' : '' ) . '>' . $row->content . '</option>';
            }
            
            if($spec1_analytical_html != '') {
                
                $spec1_analytical_html = '<select id="spec1_analytical" onchange="GBL.toggle_other_field(this,\'spec1_other_wrapper\',\'spec1_other\')">' .
                '<option value="">Select:</option>' . $spec1_analytical_html .
                '<option value="other">Others</option>' .
                '</select>';
            }
        }
        
        if(($t = count($spec1_affective)) > 0) {
            
            /* Initialize JS variables. */
            $this->configXY->JS_VARS['SPEC1_AFFECTIVE'] = 'new Array()';
                
            for($x=0; $x<$t; $x++) {
                
                $row = $spec1_affective[$x];
                
                /* Create JS variables. */
                $this->configXY->JS_VARS['[var=false]SPEC1_AFFECTIVE[' . $x . ']'] = "'" . $row->content . "'";
                
                $spec1_affective_html .= '<option value="' . $row->id . '"' . (($rta->type_of_test == 'affective' && $row->id == $rta->specific_1) ? ' selected="selected"' : '') . '>' . $row->content . '</option>';
            }
            
            if($spec1_affective_html != '') {
                
                $spec1_affective_html = '<select id="spec1_affective" onchange="GBL.toggle_other_field(this,\'spec1_other_wrapper\',\'spec1_other\')">' .
                '<option value="">Select:</option>' . $spec1_affective_html .
                '<option value="other">Others</option>' .
                '</select>';
            }
        }
        
        if(($t = count($spec1_micro)) > 0) {
            
            /* Initialize JS variables. */
            $this->configXY->JS_VARS['SPEC1_MICRO'] = 'new Array()';
            
            if(substr_count($rta->specific_1, ',')) $specific_1 = explode(',', $rta->specific_1);
            else $specific_1 = array($rta->specific_1);
                
            for($x=0; $x<$t; $x++) {
                
                $row = $spec1_micro[$x];
                
                /* Create JS variables. */
                $this->configXY->JS_VARS['[var=false]SPEC1_MICRO[' . $x . ']'] = "'" . $row->content . "'";
                
                if($x%4 == 0) $spec1_micro_html .= '</tr><tr>';
                
                $spec1_micro_html .= '<td><input type="checkbox" name="spec1_micro" value="' . $row->id . '"' . (($rta->type_of_test == 'micro' && in_array($row->id, $specific_1)) ? ' checked="checked"' : '' ) . ' /> ' . $row->content . '</td>';
            }
            
            if($spec1_micro_html != '') {
                
                $spec1_micro_html = '<table><tr>' . $spec1_micro_html . '</tr></table>';
            }
        }
        
        if(($t = count($spec1_physico_chem)) > 0) {
            
            /* Initialize JS variables. */
            $this->configXY->JS_VARS['SPEC1_PHYSICO_CHEM'] = 'new Array()';
            
            if(substr_count($rta->specific_1, ',')) $specific_1 = explode(',', $rta->specific_1);
            else $specific_1 = array($rta->specific_1);
            
            for($x=0; $x<$t; $x++) {
                
                $row = $spec1_physico_chem[$x];
                
                /* Create JS variables. */
                $this->configXY->JS_VARS['[var=false]SPEC1_PHYSICO_CHEM[' . $x . ']'] = "'" . $row->content . "'";
                
                if($x%4 == 0) $spec1_physico_chem_html .= '</tr><tr>';
                
                $spec1_physico_chem_html .= '<td><input type="checkbox" name="spec1_physico_chem" value="' . $row->id . '"' . (($rta->type_of_test == 'physico_chem' && in_array($row->id, $specific_1)) ? ' checked="checked"' : '' ) . ' /> ' . $row->content . '</td>'; //'<option value="' . $row->id . '"' . (($rta->type_of_test == 'physico_chem' && $row->id == $rta->specific_1) ? ' selected="selected"' : '') . '>' . $row->content . '</option>';
            }
            
            if($spec1_physico_chem_html != '') {
                
                $spec1_physico_chem_html = '<table><tr>' . $spec1_physico_chem_html . '</tr></table>';
                
                /*$spec1_physico_chem_html = '<select id="spec1_physico_chem" onchange="GBL.toggle_other_field(this,\'spec1_other_wrapper\',\'spec1_other\')">' .
                '<option value="">Select:</option>' . $spec1_physico_chem_html .
                '<option value="other">Others</option>' .
                '</select>';*/
            }
        }
        
        if(($t = count($spec2_affective)) > 0) {
            
            /* Initialize JS variables. */
            $this->configXY->JS_VARS['SPEC2_AFFECTIVE'] = 'new Array()';
            
            for($x=0; $x<$t; $x++) {
                
                $row = $spec2_affective[$x];
                
                /* Create JS variables. */
                $this->configXY->JS_VARS['[var=false]SPEC2_AFFECTIVE[' . $x . ']'] = "'" . $row->content . "'";
                
                $spec2_affective_html .= '<option value="' . $row->id . '"' . (($rta->type_of_test == 'affective' && $row->id == $rta->specific_2) ? ' selected="selected"' : '' ) . '>' . $row->content . '</option>';
            }
            
            if($spec2_affective_html != '') {
                
                $spec2_affective_html = '<select id="spec2_affective" onchange="GBL.toggle_other_field(this,\'spec2_other_wrapper\',\'spec2_other\')">' .
                '<option value="">Select:</option>' . $spec2_affective_html .
                '<option value="other">Others</option>'.
                '</select>';
            }
        }
        
        if(($t = count($spec2_analytical)) > 0) {
            
            /* Initialize JS variables. */
            $this->configXY->JS_VARS['SPEC2_ANALYTICAL'] = 'new Array()';
            
            for($x=0; $x<$t; $x++) {
                
                $row = $spec2_analytical[$x];
                
                /* Create JS variables. */
                $this->configXY->JS_VARS['[var=false]SPEC2_ANALYTICAL[' . $x . ']'] = "'" . $row->content . "'";
                
                $spec2_analytical_html .= '<option value="' . $row->id . '"' . (($rta->type_of_test == 'analytical' && $row->id == $rta->specific_2) ? ' selected="selected"' : '' ) . '>' . $row->content . '</option>';
            }
            
            if($spec2_analytical_html != '') {
                
                $spec2_analytical_html = '<select id="spec2_analytical" onchange="GBL.toggle_other_field(this,\'spec2_other_wrapper\',\'spec2_other\')">' .
                '<option value="">Select:</option>' . $spec2_analytical_html .
                '<option value="other">Others</option>' .
                '</select>';
            }
        }
        
        if(($t = count($spec2_micro)) > 0) {
            
            /* Initialize JS variables. */
            $this->configXY->JS_VARS['SPEC2_MICRO'] = 'new Array()';
            
            if(substr_count($rta->specific_2, ',')) $specific_2 = explode(',', $rta->specific_2);
            else $specific_2 = array($rta->specific_2);
                
            for($x=0; $x<$t; $x++) {
                
                $row = $spec2_micro[$x];
                
                /* Create JS variables. */
                $this->configXY->JS_VARS['[var=false]SPEC2_MICRO[' . $x . ']'] = "'" . $row->content . "'";
                
                if($x%4 == 0) $spec2_micro_html .= '</tr><tr>';
                
                $spec2_micro_html .= '<td><input type="checkbox" name="spec2_micro" value="' . $row->id . '"' . (($rta->type_of_test == 'micro' && in_array($row->id, $specific_2)) ? ' checked="checked"' : '' ) . ' /> ' . $row->content . '</td>';
            }
            
            if($spec2_micro_html != '') { $spec2_micro_html = '<table><tr>' . $spec2_micro_html . '</tr></table>'; }
        }
        
        if(($t = count($spec2_physico_chem)) > 0) {
            
            /* Initialize JS variables. */
            $this->configXY->JS_VARS['SPEC2_PHYSICO_CHEM'] = 'new Array()';
            
            if(substr_count($rta->specific_2, ',')) $specific_2 = explode(',', $rta->specific_2);
            else $specific_2 = array($rta->specific_2);
            
            for($x=0; $x<$t; $x++) {
                
                $row = $spec2_physico_chem[$x];
                
                /* Create JS variables. */
                $this->configXY->JS_VARS['[var=false]SPEC2_PHYSICO_CHEM[' . $x . ']'] = "'" . $row->content . "'";
                
                if($x%4 == 0) $spec2_physico_chem_html .= '</tr><tr>';
                
                $spec2_physico_chem_html .= '<td><input type="checkbox" name="spec2_physico_chem" value="' . $row->id . '"' . (($rta->type_of_test == 'physico_chem' && in_array($row->id, $specific_2)) ? ' checked="checked"' : '' ) . ' /> ' . $row->content . '</td>'; //'<option value="' . $row->id . '"' . (($rta->type_of_test == 'physico_chem' && $row->id == $rta->specific_2) ? ' selected="selected"' : '') . '>' . $row->content . '</option>';
            }
            
            if($spec2_physico_chem_html != '') {
                
                $spec2_physico_chem_html = '<table><tr>' . $spec2_physico_chem_html . '</tr></table>';
                /*'<select id="spec2_physico_chem" onchange="GBL.toggle_other_field(this,\'spec2_other_wrapper\',\'spec2_other\')">' .
                '<option value="">Select:</option>' . $spec2_physico_chem_html .
                '<option value="other">Others</option>' .
                '</select>';*/
            }
        }
        
        $sql = "
            SELECT  id,
                    CONCAT_WS(' ', firstname, lastname) AS name,
                    username AS uname
            FROM    users
            WHERE   level IN(2,4,6,7)
            AND     `locked`='0'
            ORDER
            BY      firstname";
        $query = $this->db->query($sql);
        if($query->num_rows()) {
            
            if(empty($rta->requested_by_id)) $rta->requested_by_id = $session->id;
            
            foreach($query->result() as $row) {
                
                $name = ($row->uname != '') ? $row->uname : $row->name;
                $req_by_html .= '<option value="' . $row->id . '"' . (($row->id == $rta->requested_by_id) ? ' selected="selected"' : '') . '>' . $name . '</option>';
            }
            
            if($req_by_html != '') {
                
                $req_by_html = '<select id="requested_by">' .
                '<option value="">Select:</option>' . $req_by_html;                
            }
        }
        
        $sql = "
            SELECT  id,
                    CONCAT_WS(' ', firstname, lastname) AS name,
                    username AS uname
            FROM    users
            WHERE   level IN(4,6)
            AND     `locked`='0'
            ORDER
            BY      firstname";
        $query = $this->db->query($sql);
        if($query->num_rows()) {
            foreach($query->result() as $row) {
                
                if($row->id == $rta->approved_by_id) $selected = ' selected="selected"';
                else $selected = '';
                
                $name = ($row->uname != '') ? $row->uname : $row->name;
                
                $approved_by_html .= '<option value="' . $row->id . '"' . $selected . '>' . $name . '</option>';
            }
            
            if($approved_by_html != '') {
                $approved_by_html = '<select id="approved_by" onchange="GBL.toggle_other_field(this,\'approved_by_other_wrapper\',\'approved_by_other\')">' .
                '<option value="">[none]</option>' . $approved_by_html .
                '<option value="other">Others</option></select>';
            }
        }
        
        $data['spec1_affective'] = $spec1_affective_html;
        $data['spec2_affective'] = $spec2_affective_html;
        
        $data['spec1_analytical'] = $spec1_analytical_html;
        $data['spec2_analytical'] = ($spec2_analytical_html != '') ? $spec2_analytical_html : 'Not Applicable.';
        
        $data['spec1_micro'] = $spec1_micro_html;
        $data['spec2_micro'] = ($spec2_micro_html != '') ? $spec2_micro_html : 'Not Applicable.';
        
        $data['spec1_physico_chem'] = $spec1_physico_chem_html;
        $data['spec2_physico_chem'] = ($spec2_physico_chem_html != '') ? $spec2_physico_chem_html : 'Not Applicable.';
        
        $data['approved_by'] = $approved_by_html;
        $data['requested_by'] = $req_by_html;
        
        return $data;
    }
    
    public function doSBUAdd($sbu_other, $uid) {
        
        if($sbu_other == '' || $uid == 0)
            return;
        
        $sql = "SELECT * FROM sbu WHERE `name`='" . $sbu_other . "'";
        $query = $this->db->query($sql);
        if(! $query->num_rows()) {

            $sql = "
                INSERT
                INTO    sbu
                SET     `name`='" . $sbu_other . "',
                        user_id='" . $uid . "',
                        created='" . $this->configXY->DATE . "'
                "; $this->db->query($sql);
            $sbu = $this->db->insert_id();
        }
        
        return (int) $sbu;
    }
    
    public function doSpecificAdd($tot, $spec_no, $spec_other, $uid) {
        
        if( $tot == '' ||
            $spec_no == '' ||
            $spec_other == '' ||
            $uid == 0) return;
        
        $has = $this->doSpecificCheck($tot, $spec_no, $spec_other);
        if(! $has) {

            $sql = "
                INSERT
                INTO    specifics
                SET     user_id=" . $uid . ",
                        type='" . $tot . "',
                        number=" . $spec_no . ",
                        content='" . xy_input_clean_up($spec_other) . "',
                        created='" . $this->configXY->DATE . "'";
            $this->db->query($sql);
            $spec = $this->db->insert_id();                
        }
        
        return (int) $spec;
    }
    
    public function doTestPurposeAdd($tpurpose_other, $uid, &$tpurpose) {
        
        if($tpurpose_other == '' || $uid == 0)
            return;
        
        $sql = "SELECT * FROM test_purpose WHERE content='" . $tpurpose_other . "'";
        $query = $this->db->query($sql);
        if(! $query->num_rows()) {
            $sql = "
                INSERT
                INTO    test_purpose
                SET     content='" . $tpurpose_other . "',
                        user_id='" . $uid . "',
                        created='" . $this->configXY->DATE . "'";
            $this->db->query($sql);
            $tpurpose .= ',' . $this->db->insert_id();
        }
        
    }
    
    public function doApprovedByAdd($approved_by_other, $uid) {
        
        if($approved_by_other == '' || $uid == 0)
            return;
        
        if(substr_count($approved_by_other, ' ') != 2) exit();
        
        $tmp = explode(' ', $approved_by_other);
        
        $sql = "SELECT id FROM users WHERE CONCAT_WS(' ',firstname,middlename,lastname)='" . $approved_by_other . "'";
        $query = $this->db->query($sql);
        if(! $query->num_rows()) {
            $sql = "
                INSERT
                INTO    users
                SET     level=4,
                        username='" . strtolower($tmp[0]) . rand(100, 900) . "',
                        firstname='" . $tmp[0] . "',
                        middlename='" . $tmp[1] . "',
                        lastname='" . $tmp[2] . "',
                        created='" . $this->config->configXY->DATE . "'";
            $this->db->query($sql);
            $id = $this->db->insert_id();
        }
        
        return (double) $id;
    }
    
    public function doSpecificCheck($type, $number, $content) {
        
        if(! $type || ! $number || ! $content)
            return;
        
        $sql = "
            SELECT  id
            FROM    specifics
            WHERE   number=" . (int) $number . "
            AND     type='" . $type . "'
            AND     content='" . $content . "'";
        $query = $this->db->query($sql);
        
        return $query->num_rows();
    }
    
    public function doSchedAgeCalculate($sched, $sched_tentative = '') { /* AXL */
        
        $schedule = ($sched == '') ? $sched_tentative : $sched;
        
        if(! $schedule)
            return array();
        
        if(substr_count($schedule, ',')) {
            
            $schedule = explode(',', $schedule);
            
        } else $schedule = array($schedule);
        
        $age = 0; $tmp = array();
        $total = count($schedule);
        
        for($x=0; $x<$total; $x++) {

            $date = trim($schedule[$x]);
            if($x > 0) {

                $start = $schedule[$x - 1];
                $end = $schedule[$x];

                $day = xy_date_diff($start, $end);
                $age += $day;

            } else $age = 0;
            
            $tmp[] = array('date' => $date, 'age' => $age . ' Day' . (($age > 1) ? 's' : ''));            
        }
        
        return $tmp;
    }
    
    public function doProductDataAdd($product_data) {
        
        if($product_data == '')
            return;
        
        $tmp = explode('[=AXL_R=]', $product_data);
        $rta_product_ids = array();
        $response = '';
        
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
                
                $rta_product_ids[] = $this->db->insert_id();
            }
        }

        if(count($rta_product_ids)) $response = implode(',', $rta_product_ids);
        
        return $response;
    }
    
    /* Get the Superior's data of the currently logged User. */
    public function getSuperiorData($user_id = 0) {
        
        if(! $user_id) $user_id = $this->session->id;
        $response = '';
        
        $sql = "SELECT superior_id FROM users WHERE id=" . $user_id;
        $query = $this->db->query($sql);
        $id = (double) $query->row()->superior_id;
        if($id > 0 && $id != $user_id) {
            
            $sql = "
                SELECT  email,
                        office_email,
                        firstname,
                        lastname,
                        username
                FROM    users
                WHERE   id=" . $id;
            
            $query = $this->db->query($sql);
            $response = $query->row();
        }
        
        return $response;
    }
    
    /* Save scheduled dates for a certain questionnaire. */
    public function doSchedSave($schedules, $rta_id) {
        
        $rta_id = (double) $rta_id;
        
        if(empty($schedules) || $rta_id == 0)
            return;
        
        $tsched_next = count($schedules);
        $rows_in_rta = array();
        
        $sql = "SELECT * FROM q_schedules WHERE rta_id=" . $rta_id;
        $query = $this->db->query($sql);
        if(($tsched_prev = $query->num_rows()) > 0) {
            
            /* If something huge changed in the dates.
             * Wherein the size of previously saved is larger than the new one.
             **/
            
            $rows_in_rta = $query->result();
            
            if($tsched_prev != $tsched_next) {  /* prev != next */
                
                $sql = "UPDATE q_schedules SET `date`='0000-00-00',rta_id=0 WHERE rta_id=" . $rta_id;
                $this->db->query($sql);
                
                $rows_in_rta = array();
            }
        }
        
        if(! empty($rows_in_rta)) {
            
            for($x=0; $x<$tsched_prev; $x++) {
                
                $row = $rows_in_rta[$x];
                $sql = "UPDATE q_schedules SET `date`='" . date('Y-m-d', strtotime($schedules[$x])) . "' WHERE rta_id=" . $rta_id . " AND id=" . $row->id;
                $this->db->query($sql);                
            }
            
        } else {
            
            foreach($schedules as $date) {
                
                $date = date('Y-m-d', strtotime($date));

                $sql_set = " q_schedules SET rta_id=" . $rta_id . ",`date`='" . $date . "' ";

                $sql = "SELECT id FROM q_schedules WHERE rta_id=0 AND `date`='0000-00-00'";
                $query = $this->db->query($sql);
                if($query->num_rows()) $sql = "UPDATE " . $sql_set . " WHERE id=" .$query->row()->id;
                else $sql = "INSERT INTO " . $sql_set;

                $this->db->query($sql);
            }
        }
    }
}

/* End of file rta_model.php */
/* Location: ./application/models/rta_model.php */