<?php
/**
	* coreImageResizer - Image manipulation class
	* @author Lukas Meier
	* @copyright Lukas Meier
*/

	/**
		* Manipulate images and save them easily
		*
		* This class can be used to rescale images. It validates the file-types and can also change the file-extension.
	*/
	class coreImageResizer{

		/** 
		* Resize an image and save the new image at a given path. 
		* This will automatically calcualte the width/height so 
		* the proportions are keept, except if the 
		* $force_proportions parameter is set. (Animated gif's will lose their animation)
		* @param string $src  The path to the source-image
		* @param string $output  The path/name the resized file should be saved
		* @param integer $n_width  The width the new image should at least have.
		* @param integer $n_height  The height the new image should at least have.
		* @param boolean $force_proportions  If set to true, the n_width and n_height will not be calculated and proportions will be lost.
		* @param string $force_image_type  Force output to be either a JPG, a PNG or a GIF, if set to false, the image_type will be kept from the original file.
		* @param boolean $scale_up  If set to true small images will be scaled up to get desired width/height. If false, only scaling down will take place
		* @return boolean
		*/
		public function resizeImage($src, $output, $n_width, $n_height, $force_proportions = false, $force_image_type = false, $scale_up = true){

			$acceptedFileTypes = array("jpg", "jpeg", "png", "gif");
			$allowedForceTypes = array("jpg", "png", "gif");
			
			$finfo = finfo_open(FILEINFO_MIME_TYPE);
			$src_type = finfo_file($finfo, $src);
			finfo_close($finfo);
			$tmp = explode("/", $src_type);
			$src_type = $tmp[1];
			$src_type = $src_type == "jpeg" ? "jpg" : $src_type;

			$tmp = explode(".", $output);
			$output_type = $tmp[count($tmp)-1];

			unset($tmp);

			if(!in_array($force_image_type, $allowedForceTypes) && $force_image_type !== false){
				throw new Exception("The given 'force_image_type' parameter '".$force_image_type."' is not supported.", 1);
			}

			if(!is_file($src)){
				throw new Exception("The given Source '".$src."' doesn't exist", 2);
			}
			
			if(!in_array($src_type, $acceptedFileTypes)){
				throw new Exception("The given Source-File-Extension '".$src_type."' is not allowed.", 3);
			}

			list($width, $height) = getimagesize($src);
			if($force_proportions == false){
				if($width < $n_width && $height < $n_height && $scale_up == false){
					$new_width = $width;
					$new_height = $height;
				}else{
					if($width<$height) {
						$new_width = $n_width;
						$new_height = $height/100*(100/$width*$new_width);
					} else {
						$new_height = $n_height;
						$new_width = $width/100*(100/$height*$new_height);
					}
				}
			}else{
				$new_height = $n_height;
				$new_width = $n_width;
			}
			
			$image_p = imagecreatetruecolor($new_width, $new_height);

			imagealphablending( $image_p, false );
			imagesavealpha( $image_p, true );

			if($src_type == "jpg" || $src_type === "jpeg") {
				$image = imagecreatefromjpeg($src);
			} else if($src_type == "png") {
				$image = imagecreatefrompng($src);
			} else if($src_type == "gif") {
				$image = imagecreatefromgif($src);
			}
			
			imagecopyresampled($image_p, $image, 0, 0, 0, 0, $new_width, $new_height, $width, $height);

			$force_image_type = $force_image_type == false ? $src_type : $force_image_type;
			
			$output = $this->changeFileExtension($output, $force_image_type);
			if($src_type == "jpg" || $src_type == "jpeg" || $force_image_type == "jpg") {				
				if(imagejpeg($image_p, $output, 80)){
					return true;
				}
			} elseif($src_type == "png" || $force_image_type == "png") {
				if(imagepng($image_p, $output, 8)){
					return true;
				}
			} elseif($src_type == "gif" || $force_image_type == "gif"){
				if(imagegif($image_p, $output)){
					return true;
				}
			}
		}

		/** 
		* Returns path of given file with wanted file-extension 
		* for example /sample/file.png will be returned as /sample/file.jpg
		* This is used to resize and convert images if the name is forced to another type.
		* @param string $src  The path to the source-image
		* @param string $file_extension  The new extension the file should have
		* @return string The filename with the new extension
		*/
		public function changeFileExtension($src, $file_extension){
			$tmp = explode(".", $src);

			if($tmp[count($tmp)-1] == $file_extension){
				return $src;
			}else{
				$tmp[count($tmp)-1] = $file_extension;

				$new_fn = "";
				foreach ($tmp as $v) {
					$new_fn .= $v.".";
				}
				$new_fn = substr($new_fn, 0, -1);
				return $new_fn;
			}
		}
	}
?>