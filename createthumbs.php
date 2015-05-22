<?php
function createThumbs($pathToImages, $pathToThumbs, $thumbWidth) {
	// open the directory
	$dir = opendir ( $pathToImages );
	
	// loop through it, looking for any/all JPG files:
	while ( false !== ($fname = readdir ( $dir )) ) {
		$fpath = "$pathToImages/$fname";
		$thpath = "$pathToThumbs/$fname";
		if (file_exists ( $thpath ))
			continue;
			// parse path for the extension
		$info = pathinfo ( $fpath );
		if (! array_key_exists ( 'extension', $info ))
			continue;
			// continue only if this is a JPEG/PNG image
		$format = NULL;
		$ext = strtolower ( $info ['extension'] );
		if ($ext == 'jpg' || $ext == 'jpeg')
			$format = "jpeg";
		elseif ($ext == 'png')
			$format = "png";
		
		if ($format) {
			// echo "Creating thumbnail for {$fname} <br />";
			
			// load image and get image size
			$img = call_user_func ( "imagecreatefrom$format", $fpath );
			$width = imagesx ( $img );
			$height = imagesy ( $img );
			
			// calculate thumbnail size
			$new_width = $thumbWidth;
			$new_height = floor ( $height * ($thumbWidth / $width) );
			
			// create a new temporary image
			$tmp_img = imagecreatetruecolor ( $new_width, $new_height );
			
			// copy and resize old image into new image
			imagecopyresized ( $tmp_img, $img, 0, 0, 0, 0, $new_width, $new_height, $width, $height );
			
			// save thumbnail into a file
			call_user_func ( "image$format", $tmp_img, $thpath );
		}
	}
	// close the directory
	closedir ( $dir );
}
// call createThumb function and pass to it as parameters the path
// to the directory that contains images, the path to the directory
// in which thumbnails will be placed and the thumbnail's width.
// We are assuming that the path will be a relative path working
// both in the filesystem, and through the web for links
