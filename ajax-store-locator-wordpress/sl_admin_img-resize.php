<?php

class SimpleImage {
 
   var $image;
   var $image_type;
   
   function load($filename) {
 
      $image_info = getimagesize($filename);
      $this->image_type = $image_info[2];
      if( $this->image_type == IMAGETYPE_JPEG ) {
 
         $this->image = imagecreatefromjpeg($filename);
      } elseif( $this->image_type == IMAGETYPE_GIF ) {
 
         $this->image = imagecreatefromgif($filename);
      } elseif( $this->image_type == IMAGETYPE_PNG ) {
 
         $this->image = imagecreatefrompng($filename);
      }
   }
   
   function save($filename, $image_type=IMAGETYPE_JPEG, $compression=75, $permissions=null) {	 
      if( $this->image_type == IMAGETYPE_JPEG ) {
         imagejpeg($this->image,$filename,$compression);
      } else if( $this->image_type == IMAGETYPE_GIF ) { 
         imagegif($this->image,$filename);
      } else if( $this->image_type == IMAGETYPE_PNG ) { 
         imagepng($this->image,$filename);
      }
      if( $permissions != null) {
 
         chmod($filename,$permissions);
      }
   }
   
   function output($image_type=IMAGETYPE_JPEG) {
 
      if( $image_type == IMAGETYPE_JPEG ) {
	  echo getcwd();
         imagejpeg($this->image);
      } elseif( $image_type == IMAGETYPE_GIF ) {
 
         imagegif($this->image);
      } elseif( $image_type == IMAGETYPE_PNG ) {
 
         imagepng($this->image);
      }
   }
   
   function getWidth() {
 
      return imagesx($this->image);
   }
   
   function getHeight() {
 
      return imagesy($this->image);
   }
   
   function resizeToHeight($height) {
 
      $ratio = $height / $this->getHeight();
      $width = $this->getWidth() * $ratio;
      $this->resize($width,$height);
   }
 
   function resizeToWidth($width) {
      $ratio = $width / $this->getWidth();
      $height = $this->getheight() * $ratio;
      $this->resize($width,$height);
   }
 
   function scale($scale) {
      $width = $this->getWidth() * $scale/100;
      $height = $this->getheight() * $scale/100;
      $this->resize($width,$height);
   }
 
   function resize($width,$height) {
		$scale = min(
            $width/ $this->getWidth(),
            $height / $this->getHeight()
        );
		$new_width = $this->getWidth() * $scale;
        $new_height = $this->getHeight() * $scale;
	    $new_image = imagecreatetruecolor($new_width, $new_height);		
	    imagecopyresampled($new_image, $this->image, 0, 0, 0, 0, $new_width, $new_height, $this->getWidth(), $this->getHeight());
	    $this->image = $new_image;
   }
   function resizeOr($width,$height) {
		$scale = min(
            $width/ $this->getWidth(),
            $height / $this->getHeight()
        );
		$new_width = $this->getWidth() * $scale;
        $new_height = $this->getHeight() * $scale;
		$new_image = imagecreatetruecolor($new_width, $new_height);
		if( $this->image_type == IMAGETYPE_GIF || $this->image_type == IMAGETYPE_PNG ) {		
			$current_transparent = imagecolortransparent($this->image);
			if($current_transparent != -1) {
				$transparent_color = imagecolorsforindex($this->image, $current_transparent);
				$current_transparent = imagecolorallocate($new_image, $transparent_color['red'], $transparent_color['green'], $transparent_color['blue']);
				imagefill($new_image, 0, 0, $current_transparent);
				imagecolortransparent($new_image, $current_transparent);
			} elseif( $this->image_type == IMAGETYPE_PNG) {
					imagealphablending($new_image, false);
					$color = imagecolorallocatealpha($new_image, 0, 0, 0, 127);
					imagefill($new_image, 0, 0, $color);
					imagesavealpha($new_image, true);
					imagealphablending($new_image, true);
					imagesavealpha($new_image,true);
					$transparent = imagecolorallocatealpha($new_image, 255, 255, 255, 127);
					imagefilledrectangle($new_image, 0, 0, $width, $height, $transparent);
			}
	   }
	   imagecopyresampled($new_image, $this->image, 0, 0, 0, 0, $new_width, $new_height, $this->getWidth(), $this->getHeight());	
	   $this->image = $new_image;
   }
  /* protected function create_scaled_image($width,$height,$file_name, $version, $options) {
        $file_path = $this->get_upload_path($file_name);    
        list($img_width, $img_height) = @getimagesize($this->image);
        if (!$img_width || !$img_height) {
            return false;
        }
        $scale = min(
            $width / $img_width,
            $height / $img_height
        );        
        $new_width = $img_width * $scale;
        $new_height = $img_height * $scale;
        $new_img = @imagecreatetruecolor($new_width, $new_height);
        switch (strtolower(substr(strrchr($file_name, '.'), 1))) {
            case 'jpg':
            case 'jpeg':
                $src_img = @imagecreatefromjpeg($file_path);
                $write_image = 'imagejpeg';
                $image_quality = isset($options['jpeg_quality']) ?
                    $options['jpeg_quality'] : 75;
                break;
            case 'gif':
                @imagecolortransparent($new_img, @imagecolorallocate($new_img, 0, 0, 0));
                $src_img = @imagecreatefromgif($file_path);
                $write_image = 'imagegif';
                $image_quality = null;
                break;
            case 'png':
                @imagecolortransparent($new_img, @imagecolorallocate($new_img, 0, 0, 0));
                @imagealphablending($new_img, false);
                @imagesavealpha($new_img, true);
                $src_img = @imagecreatefrompng($file_path);
                $write_image = 'imagepng';
                $image_quality = isset($options['png_quality']) ?
                    $options['png_quality'] : 9;
                break;
            default:
                $src_img = null;
        }
        $success = $src_img && @imagecopyresampled(
            $new_img,
            $src_img,
            0, 0, 0, 0,
            $new_width,
            $new_height,
            $img_width,
            $img_height
        ) && $write_image($new_img, $new_file_path, $image_quality);
        // Free up memory (imagedestroy does not delete files):
        @imagedestroy($src_img);
        @imagedestroy($new_img);
        return $success;
    }*/
 
}
?>