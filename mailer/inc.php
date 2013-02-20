<?php
date_default_timezone_set('Asia/Manila');

define('DB_HOST', 'localhost');

define('DB_USER', 'armande');
define('DB_PASSWORD', 'axl4117jbee');
define('DB_NAME', 'jollibee');

/*define('DB_USER', 'root');
define('DB_PASSWORD', 'axl4117');
define('DB_NAME', 'sensorium_fromjb');*/

require_once 'db.php';

$subject_arr = array(
    'rta_add'   => 'New RTA was created',
    'rta_edit'  => 'An RTA was modified',
    'rta_approved'  => 'RTA has been approved',
);

function xy_log($filename, $error_message) {
    
    $fp = @fopen('logs/' . $filename . '_' . date('Y-m-d') . '.txt', 'a');
    if($fp) {
        
        fwrite($fp, date('h:i:s A') . "\t" . $error_message . "\r\n");
        fclose($fp);
    }
}

function xy_get_user($db, $id) {
    
    $sql = "SELECT id FROM users WHERE id=" . $id;
    $db->query($sql);
    $row = $db->loadObject();
    
    return $row;
}

function xy_urldecode(&$str) {
    
    $str = urldecode($str);
}
?>