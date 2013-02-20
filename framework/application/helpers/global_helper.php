<?php defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Returns array of Months
 *
 * @access	public
 * @return	array
 */
if(! function_exists('xy_month_array')) {
    
    function xy_month_array() {
        
        $month[] = 'January';
        $month[] = 'February';
        $month[] = 'March';
        $month[] = 'April';
        $month[] = 'May';
        $month[] = 'June';
        $month[] = 'July';
        $month[] = 'August';
        $month[] = 'September';
        $month[] = 'October';
        $month[] = 'November';
        $month[] = 'December';

        return $month;    
    }
}

/**
 * Returns escaped string
 *
 * @access	public
 * @return	string
 */
if(! function_exists('xy_input_clean_up')) {
    function xy_input_clean_up($str) {

        $str = trim($str);
        if($str == '') return;
        
        if(get_magic_quotes_gpc()) {

            $str = stripslashes($str);
        }

        //$str = htmlentities($str);
        
        //$str = htmlentities($str, ENT_QUOTES, 'UTF-8');
        
        return mysql_real_escape_string($str);
    }
}

/**
 * Returns escaped string
 *
 * @access	public
 * @return	string
 */
if(! function_exists('xy_input_clean_up_byref')) {
    function xy_input_clean_up_byref(&$str) {

        $str = xy_input_clean_up($str);
    }
}

/**
 * Fills-up user session
 *
 * @access	public
 * @return	string
 */
if(! function_exists('xy_current_user')) {
    function xy_current_user($fresh = false) {

        $ci =& get_instance();
        $config = $ci->config->item('XY');
        
        if(! isset($_SESSION['UserLOGID' . $config->SESSIONID]))
                return;
        
        $uid = (double) $_SESSION['UserLOGID' . $config->SESSIONID];
        
        if($fresh) {

            $query = $ci->db->query("SELECT * FROM `users` WHERE `id`=" . $uid);
            $tmp = $_SESSION['UserLoggedDetails' . $config->SESSIONID] = $query->row();

        } else {
            $tmp = $_SESSION['UserLoggedDetails' . $config->SESSIONID];
        }

        return $tmp;
    }
}

/**
 * Returns limited string
 *
 * @access	public
 * @return	string
 */
if(! function_exists('xy_limit_string')) {
    function xy_limit_string($str, $limit) {

        $str = trim($str);
        if($str == '')
            return;
        
        $len = strlen($str);
        if($len > $limit) {
            
            $str = substr($str, 0, $limit) . ' ...';
        }
        
        return $str;
    }
}

/**
 * Returns limited string
 *
 * @access	public
 * @return	string
 */
if(! function_exists('xy_doc_root')) {
    function xy_doc_root() {

        $ci =& get_instance();
        $config = $ci->config->item('XY');
        
        return $config->DOCROOT;
    }
}

/**
 * Returns RTA states
 *
 * @access	public
 * @return	array
 */
if(! function_exists('xy_rta_state')) {
    function xy_rta_state($interchange = false) {
        
        $state = array('canceled' => 0, 'approved' => 1, 'tba' => 2, 'pending' => 3);
        if($interchange) $state = xy_array_interchange($state);
        
        return $state;
    }
}

/**
 * Returns interchanged array
 *
 * @access	public
 * @return	array
 */
if(! function_exists('xy_array_interchange')) {
    function xy_array_interchange($array) {
        
        $state = array('canceled' => 0, 'approved' => 1, 'tba' => 2, 'pending' => 3);
        $new_arr = array();
        
        foreach($array as $key => $value) {
            $new_arr[$value] = $key;
        }
        
        return $new_arr;
    }
}

/**
 * Returns root and url
 *
 * @access	public
 * @return	string
 */
if(! function_exists('xy_url')) {
    
    function xy_url($url) { return xy_doc_root() . $url; }
}

if(! function_exists('xy_ucwords')) {
    function xy_ucwords($str) {
        
        return ucwords(str_replace('_', ' ', strtolower($str)));
    }
}

/**
 * Returns default instructions for screen items
 *
 * @access	public
 * @return	array
 */
if(! function_exists('xy_default_instruction')) {
    
    function xy_default_instruction($type = NULL) {
        
        $response = array(
            'liking'                    =>  'Evaluate the sample according to the ff attributes. Place a check on the box corresponding to your answer.' .
                                            "\n\n" . 'Choose only ONE answer',
            'paired_preference'         =>  'Taste the sample on the left first and the sample on the right second. After tasting the samples, select the code number of the sample you prefer.' .
                                            "\n\n" . 'Please choose one.',
            'ranking_for_preference'    =>  'Please rank the samples in the order of acceptability. Rank the most acceptable sample first and the least acceptable as third. Do not assign the same rank to two samples. Drag the code on box corresponding to your answer.' .
                                            "\n\n" . 'Evaluate the samples in the following order:',
            'ranking_for_intensity'     =>  'Please rank the samples in the order of acceptability. Rank the most acceptable sample first and the least acceptable as third. Do not assign the same rank to two samples. Drag the code on box corresponding to your answer.' .
                                            "\n\n" . 'Evaluate the samples in the following order:',
            'triangle'                  =>  'Taste samples from left to right. Two are identical; determine which is the odd sample. Select the code number of the odd sample.' .
                                            "\n\n" . 'You must make a choice.',
            'same_or_different'         => 'Here are two samples for you to evaluate. Taste each of the coded samples in the sequence presented, from left to right.' .
                                            "\n\n" . 'Are the samples the same or different?' .
                                            "\n\n" . 'Select the corresponding word.',
            'duo_trio'                  =>  'There are three samples for you to evaluate. One of the coded pairs is the same as the reference, R. Taste the reference first. Then taste each of the coded samples in the sequence presented from left to right. Select the code of the sample that is most similar to the reference.' .
                                            "\n\n" . 'Which of the coded sample is similar to the reference?',
            '2afc'                      =>  'Taste the sample on the left first and the sample on the right second. After tasting the samples, select the code number of the _____ sample.' .
                                            "\n\n" . 'Which sample is _____?',
            '3afc'                      =>  'In front of you are three samples. Two are the same, one is different. Taste the samples in the order indicated below and identify the _____ sample.',
            
            'screening_basic_taste_recognition' => 'You just received aqueous solutions of sucrose (sweet), NaCl (salty), citric acid (sour) and caffeine (bitter). Please evaluate the ' .
                                                   'samples in each set in the order indicated below. Your task is to recognize the basic taste of each solution. ' .
                                                   'Please write your response on the space provided. If the samples taste like water, mark with a zero (0).' .
                                                   "\n\n" . 'Retasting is allowed.',
            
            'screening_odor_recognition'        => 'Please evaluate the odors present in the following samples in the order indicated below by opening the containers only very slightly one at a time, ' .
                                                   'and taking a quick sniffs of the sample. Write all the descriptors that you think describe them on the space provided. Allow time to rest after each sample.'
            );
        
        if(! $type) return $response;
        else return $response[$type];
    }
}

/**
 * Returns code in array format from the source string
 *
 * @access	public
 * @return	array
 */
if(! function_exists('xy_code_get')) {
    
    function xy_code_get($data, $type = 'primary') {

        if($type == 'primary') $type = 1;
        else $type = 2;

        $code_arr = array();
        parse_str($data, $codes);
        foreach($codes as $key => $value) {

            $tmp = explode('_', $key);
            if($tmp[1] == $type) {

                $code_arr[] = $value;
            }
        }

        return $code_arr;
    }
}

if(! function_exists('xy_factorial')) {
    
    function xy_factorial($n) {
        
        if($n == 0) return 1;
        else { return $n * xy_factorial($n - 1); }
    }
}

if(! function_exists('xy_hexcolor')) {
    
    function xy_hexcolor() {
        
        return '#' . strtoupper(dechex(rand(0, 10000000)));
        /*
        mt_srand((double) microtime() * 1000000);
        
        $c = '';
        while(strlen($c) < 6) {
            
            $c .= sprintf("%02X", mt_rand(0, 255));
        }
        
        return '#' . $c;*/
    }
}

if(! function_exists('xy_date_diff')) {
    
    function xy_date_diff($start, $end) {
        
        $time_zone = new DateTimeZone('Asia/Manila');
	
        $start = new DateTime($start, $time_zone);
        $end = new DateTime($end, $time_zone);
        
        return $start->diff($end)->format("%a"); /* Returns total number of days. */
    }
}

if(! function_exists('xy_screen_and_item_string_encode')) { /* AXL */
    
    function xy_screen_and_item_string_encode(&$str) {
        
        $str = str_replace(array("'", '"', '&quot;', "\n"), array('[quote]', '[dquote]', '[dquote]', '[nl]'), $str);
        $str = htmlentities($str, ENT_QUOTES, 'UTF-8');
        
        $str = str_replace('&amp;', '&', $str); /* Restore ampersand, i.e. for SQS Main, etc ... */

    }
}

if(! function_exists('xy_screen_and_item_string_decode')) { /* AXL */
    
    function xy_screen_and_item_string_decode(&$str) {
        
        $str = str_replace(array('[quote]', '[dquote]', '[nl]'), array("'", '&quot;', "\n"), $str);        
    }
}

if(! function_exists('xy_screen_and_item_string_html')) { /* AXL */
    
    function xy_screen_and_item_string_html(&$str) {
        
        $str = str_replace(array('[quote]', '[dquote]', '[nl]', '[and]'), array("'", '&quot;', "<br />", '&'), $str);
    }
}

/**
 * Returns screen structure without the "id" index
 *
 * @access	public
 * @return	array
 */
if(! function_exists('xy_remove_id')) {
    
    function xy_remove_id(&$arr) { unset($arr['id']); }
}

/**
 * Returns highlighted subject inside the string on search
 *
 * @access	public
 * @return	string
 */
if(! function_exists('xy_search_higlight')) {
    function xy_search_higlight($search, $str) {

        preg_match_all("/" . $search . "/i", $str, $matches);
        $matches = $matches[0];

        if(! empty($matches)) {

            $replace = array();
            for($x=0, $y = count($matches); $x<$y; $x++) {

                $replace[] = '<b style="color: #FF0000">' . $matches[$x] . '</b>';        
            }

            $str = str_replace($matches, $replace, $str);
        }
        
        return $str;
    }
}

/**
 * Returns properly formatted string to use as an HTML element's id
 *
 * @access	public
 * @return	string
 */
if(! function_exists('xy_make_id')) {
    function xy_make_id($str) {
        
        $str = strip_tags($str);
        $str = html_entity_decode($str);
        
        $str = str_replace(' ', '_', $str);
        $str = preg_replace('/[^a-zA-Z0-9\_]/', '', $str);

        return strtolower($str);
    }
}

/**
 * Returns a formatted rank label based from a number
 *
 * @access	public
 * @return	string
 */
if(! function_exists('xy_rank_label')) {
    function xy_rank_label($digit) {
        
        $len = strlen($digit);
        
        if($len > 1) {
            
            if($digit > 19) {
                
                $tmp = strval($digit);
                $tmp = $tmp[$len - 1];
                
                if($tmp == 1 || $tmp == 2 || $tmp == 3) { /* 1, 2, & 3 only. */

                    $response = substr($digit, 0, ($len - 1)) . xy_rank_label($tmp);

                } else $response = $digit . 'th';
                
            } else $response = $digit . 'th';
            
        } else {
            
            if($digit < 4) {
                
                if($digit == 1) $response = $digit . 'st';
                elseif($digit == 2) $response = $digit . 'nd';
                elseif($digit == 3) $response = $digit . 'rd';
                
            } else $response = $digit . 'th';
        }
        
        return $response;        
    }
}

if(! function_exists('xy_email')) {
    function xy_email($fields, $url) {
        
        $url = 'http://jollibee.programmerspride.com/' . $url;
        
        foreach($fields as $key=>$value) { $fields_string .= $key . '=' . urlencode($value) . '&'; }
        rtrim($fields_string, '&');

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, count($fields));
        curl_setopt($ch, CURLOPT_POSTFIELDS, $fields_string);
        $result = curl_exec($ch);
        curl_close($ch);
    }
}
?>