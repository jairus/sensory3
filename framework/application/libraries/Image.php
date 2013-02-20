<?php
/*
 * Title        : IMAGE Class
 * Author       : Armande Bayanes
 * Description  : 
 **/

class Image {

    # Get the new Width and Height with respect to the new scaling.
    public function getNewWH($argDimension, $argScale) {

        list($width, $height) = explode("x", $argDimension);
        $scale = intval($argScale);

        $w = $width;
        $h = $height;

        /*if($height > 525 && $scale == 500) { # So not to distort the PhotoViewer Plug-in's height.

            $h = 520;
            $w = ($width / $height) * $h;
        }
        else*/
        if($width > $scale) {

           $w = $scale;
           $h = ($height / $width) * $w;
        }

        return array($w, floor($h));
    }

    public function readImage($image, $scale, $filename = NULL) {

        if(! empty($image) && is_object($image)) {
           
            $new_scale = intval($scale);
            if($new_scale == 0) $new_scale = 700;

            list($width, $height) = explode('x', $image->dimension);
            list($w, $h) = $this->getNewWH($width . 'x' . $height, $new_scale);

            $image_p = @imagecreatetruecolor($w, $h);
            $image = @imagecreatefromstring($image->content);

            @imagecopyresampled($image_p, $image, 0, 0, 0, 0, $w, $h, $width, $height);
            
            if($filename) @imagejpeg($image_p, $filename, 100);
            
            else return @imagejpeg($image_p, NULL, 100);
        }
    }

    public function getImageCenter($image, $argW, $argH) {
        
        if(! empty($image) && is_object($image)) {

            $crop_width = $argW;
            $crop_height = $argH;

            # Create the image buffer
            $image_p = imagecreatetruecolor($crop_width, $crop_height);

            # Store image in the memory
            list($width, $height) = explode("x", $image->dimension);
            $image = imagecreatefromstring($image->content);
            

            # Calculate the new X and Y axis
            $x_diff = $width - $crop_width;
            $new_x = floor($x_diff / 2);

            $y_diff = $height - $crop_height;
            $new_y = floor($y_diff / 2);

            # Cropping process
            imagecopyresampled($image_p, $image, 0, 0, $new_x, $new_y, $crop_width, $crop_height, $crop_width, $crop_height);

            # Create the output
            return imagejpeg($image_p, NULL, 100);
        }
    }
}

/* End of file Image.php */
/* Location: ./application/libraries/Image.php */