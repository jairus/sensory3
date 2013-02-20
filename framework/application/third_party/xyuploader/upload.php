<?php
exit('haller');
header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
header("Cache-Control: no-cache");
header("Pragma: no-cache");

$userid = (double) $_GET['userid'];
$docroot = trim($_GET['docroot']);
if($userid == 0) exit();

$photo = $_FILES['Filedata'];
$filename = $photo['name'];
$extension = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
$types = array("gif", "jpg", "png");
if(! in_array($extension, $types)) exit();

$path = "tmp/";
$filename = str_replace('.' . $extension, '', $filename);
$filename = preg_replace('/[^a-zA-Z0-9]/', '', $filename) . '.' . $extension;

$filename = $path . $userid . "_" . $filename;
$ok = move_uploaded_file($photo['tmp_name'], $filename);

if($ok) {

    # Do a special resizing when an images are uploaded.    
    $scale = 700; # Maximum width of Photo.

    # Get the actual dimension of the image
    list($width, $height) = getimagesize($filename);

    $w = $width;
    $h = $height;

    # Get the new height with respect to the aspect ratio
    if($width > $scale) {

       $w = $scale;
       $h = ($height / $width) * $w;
    }

    $dimension = $w . "x" . floor($h);
    $filename_resized =  $userid . '_' . rand(0, 999999) . '_' . $dimension . '_' . time() . '.jpg';

    $image_p = imagecreatetruecolor($w, $h);
    if($extension == "jpg") $image = imagecreatefromjpeg($filename);
    elseif($extension == "gif") $image = imagecreatefromgif($filename);
    elseif($extension == "png") $image = imagecreatefrompng($filename);

    imagecopyresampled($image_p, $image, 0, 0, 0, 0, $w, $h, $width, $height);
    
    # Output, convert all into JPEG
    imagejpeg($image_p, $path . $filename_resized, 100);
    
    unlink($filename);
    
    header('Content-type: text/html');
    echo $filename_resized;
    exit();
}
?>