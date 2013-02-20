<?php
require_once 'inc.php';

if(empty($_POST)) {
    
    xy_log('exec.php', '$_POST is empty');
    exit();
}

$input = $_POST;
array_walk($input, 'xy_urldecode');
extract($input);

/* ================================================
 *          MAJOR
 * ================================================
 * $notify
 * $action
 * $recipient_email
 * $receipient_name
 * ================================================
 *          SPECIFIC PER $notify AND $action
 * ================================================
 * $superior_name
 * $username
 * $rta_name
 * $date_time
 */

$body = @file_get_contents('templates/body.html');
$content = @file_get_contents('templates/to_' . $notify . '_on_' . $action . '.html');

if($notify == 'superior') {

    if($action == 'rta_add' || $action == 'rta_edit') {

        $date_time = strtotime(base64_decode($rta_datetime));

        $subject = $subject_arr[$action] . ' [' . $rta_id . ']';
        $content = str_replace(
            array('[=SUPERIOR_NAME=]', '[=USERNAME=]', '[=RTA_NAME=]', '[=DATE_TIME=]'),
            array($superior_name, $user_name, $rta_name, date('F d, Y h:iA', $date_time)),
            $content
        );
    }

    $recipient_email = $superior_email;
    $recipient_name = $superior_name;
}
else
if($notify == 'po') {
    
    if($action == 'rta_approved') {
        
        $date_time = strtotime(base64_decode($date_time));
        $subject = $subject_arr[$action] . ' [' . $rta_id . ']';
        
        $content = str_replace(
            array('[=PO_NAME=]', '[=RTA_ID=]', '[=RTA_NAME=]', '[=DATE_TIME=]'),
            array($po_name, $rta_id, $rta_name, date('F d, Y h:iA', $date_time)),
            $content
        );
    }
    
    $recipient_email = $po_email;
    $recipient_name = $po_name;
}

if(! $date_time) $date_time = time();

if($notify_admin) {

    $sql = "
        INSERT
        INTO    cron_to_admin
        SET     data='" . base64_encode(serialize($_POST)) . "',
                created='" . date('Y-m-d H:i:s', $date_time) . "',
                delivered='0'";
    $db->query($sql);
}

if($subject == '') $subject = '';
else $subject = ' : ' . $subject;

$body = str_replace('[=CONTENT=]', $content, $body);

if($recipient_email != '') {
    
    require_once('phpMailer/class.phpmailer.php');

    $mail = new PHPMailer();
    $mail->AddReplyTo('tuso@programmerspride.com', 'Sensory Software');
    $mail->SetFrom('tuso@programmerspride.com', 'Sensory Software');
    $mail->AddAddress($recipient_email, $receipient_name);

    $mail->Subject    = 'Sensory Software Notification' . $subject;
    $mail->AltBody    = 'To view the message, please use an HTML compatible email viewer !';

    $mail->MsgHTML($body);

    if(! $mail->Send()) xy_log('exec', 'Mail failed (' . base64_encode(serialize($_POST)) . ') : ' . $mail->ErrorInfo);
    else xy_log('exec', 'Mail sent');
    
} else xy_log('exec', 'Mail failed : No email');
?>