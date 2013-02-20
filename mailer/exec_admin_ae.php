<?php
require_once 'inc.php';

if(empty($_POST)) {
    
    xy_log('exec_admin_ae.php', '$_POST is empty');
    exit();
}

$input = $_POST;
array_walk($input, 'xy_urldecode');
extract($input);

if($action_type == '' || $data == '') {
    
    xy_log('exec_admin_ae.php', '$action_type nor $data is empty');
    exit();
}

$data = unserialize(base64_decode($data));
extract($data);
$id = (double) $id;
if($id == 0) {
    
    xy_log('exec_admin_ae.php', '$id is empty');
    exit();
}

$sql_set =
    "level='" .         $level          . "'," .
    "superior_id='" .   $superior_id    . "'," .
    "employee_no='" .   $employee_no    . "'," .
    "birthdate='" .     $birthdate      . "'," .
    "firstname='" .     $firstname      . "'," .
    "middlename='" .    $middlename     . "'," .
    "lastname='" .      $lastname       . "'," .
    "email='" .         $email          . "'," .
    "username='" .      $username       . "'," .
    "password='" .      $password       . "'";

if($action_type == 'add') $sql = "INSERT INTO users SET " . $sql_set . ",created='" . date('Y-m-d H:i:s') . "',id=" . $id;
elseif($action_type == 'edit') {
    
    $row = xy_get_user($db, $id);
    
    if(empty($row)) $sql = "INSERT INTO users SET " . $sql_set . ",created='" . date('Y-m-d H:i:s') . "',id=" . $id;
    else $sql = "UPDATE users SET " . $sql_set . " WHERE id=" . $id;
    
} elseif($action_type == 'delete') {
    
    $row = xy_get_user($db, $id);
    if(! empty($row)) $sql = "DELETE FROM users WHERE id=" . $id;
}

if($sql != '') {
    
    $db->query($sql);
    xy_log('exec_admin_ae.php', $sql);    
}
?>