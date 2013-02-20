<?php
error_reporting(E_ALL ^ E_NOTICE);

require_once 'inc.php';

$subject_to_mail = $_COOKIE['subject_to_mail'];
$admin_arr = $_COOKIE['admin_arr_cookie'];

if($admin_arr == '') {

    $sql = "
        SELECT  id,
                email,
                firstname,
                lastname,
                username
        FROM    users
        WHERE   level='1'
        AND     email!=''";
    $db->query($sql);

    if($db->count()) {
        
        $rows = $db->loadObjectList();
        setcookie("admin_arr_cookie", base64_encode(serialize($rows)), time() + 3600, "/");
        $admin_arr = $rows;
    }
    
} else $admin_arr = unserialize(base64_decode($admin_arr));

if(empty($subject_to_mail)) {

    $sql = "
        SELECT  *
        FROM    cron_to_admin
        WHERE   delivered='0'
        AND     DATE_FORMAT(created, '%Y-%m-%d')='" . date('Y-m-d', time()) . "'";
    $db->query($sql);
    $rows = $db->loadObjectList();
    
    if(! empty($rows)) { 
        
        for($x=0; $x<$db->count(); $x++) {
            
            $data = unserialize(base64_decode($rows[$x]->data));
            $data_tmp = array();
            
            if($data['action'] == 'rta_add' || $data['action'] == 'rta_edit') {
                
                $subject = $subject_arr[$data['action']] . ' [' . $data['rta_id'] . ']';
                
                $data_tmp['user_name'] = $data['user_name'];
                $data_tmp['rta_name'] = $data['rta_name'];
                $data_tmp['date_time'] = strtotime(base64_decode($data['rta_datetime']));                
            }
            
            $data = base64_encode(serialize(array_merge(array(
                
                'type' => $data['action'],
                'subject' => $subject
                
            ), $data_tmp)));
            
            setcookie("subject_to_mail[" . $rows[$x]->id . "]", $data, time() + 31536000, "/");
            $subject_to_mail[$rows[$x]->id] = $data;
        }
    }
}

if(! empty($subject_to_mail)) {
    
    require_once('phpMailer/class.phpmailer.php');
    $mail = new PHPMailer();

    $mail->AddReplyTo('tuso@programmerspride.com', 'Sensory Software');
    $mail->SetFrom('tuso@programmerspride.com', 'Sensory Software');
    $mail->AltBody  = 'To view the message, please use an HTML compatible email viewer !';
    
    $body = @file_get_contents('templates/body.html');
    $admin_arr_count = count($admin_arr);
    
    foreach($subject_to_mail as $id => $data) {
        
        $data_tmp = $data;
        $row = unserialize(base64_decode($data));
        
        $mail->Subject = 'Sensory Software Notification' . (($row['subject'] == '') ? '' : (' : ' . $row['subject']));
        $content = @file_get_contents('templates/cron_to_admin_' . $row['type'] . '.html');
        
        for($x=0; $x<$admin_arr_count; $x++) {
            
            $admin_name = $admin_arr[$x]->firstname . ' ' . $admin_arr[$x]->lastname;
            
            if($row['type'] == 'rta_add' || $row['type'] == 'rta_add') {
                
                $content_tmp = str_replace(
                    array('[=ADMIN_NAME=]', '[=USERNAME=]', '[=RTA_NAME=]', '[=DATE_TIME=]'),
                    array($admin_name, $row['user_name'], $row['rta_name'], date('F d, Y h:iA', $row['date_time'])),
                    $content
                );                
            }
            
            $body_tmp = str_replace('[=CONTENT=]', $content_tmp, $body);
            $mail->MsgHTML($body_tmp);
            
            $mail->AddAddress($admin_arr[$x]->email, $admin_arr[$x]->firstname . ' ' . $admin_arr[$x]->lastname);
            
            if(! $mail->Send()) xy_log('exec', 'Mail failed (' . $data_tmp . ') : ' . $mail->ErrorInfo);
            else xy_log('exec', 'Mail sent');
            
            $mail->ClearAddresses();
            sleep(3);
        }
        
        $sql = "UPDATE cron_to_admin SET delivered='1' WHERE id=" . $id;
        $db->query($sql);
        setcookie("subject_to_mail[" . $id . "]", '', time() - 31536000, "/");
        
        sleep(2);
    }
}
?>