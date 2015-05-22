
<?php
class Tiler {
	public $cachedir;
	public $originalsdir;
	public $cacheurl;
	public $originalsurl;
	public $thumbs_bigside = 300;
	public $photos_bigside = 600;
	/**
	 */
	public function __construct() {
	}
	
	// GETTERS AND SETTERS
	public function cachedir($name, $val = NULL) {
		return getset ( func_get_args () );
	}
	public function originalsdir($name, $val = NULL) {
		return getset ( func_get_args () );
	}
	public function cacheurl($name, $val = NULL) {
		return getset ( func_get_args () );
	}
	public function originalsurl($name, $val = NULL) {
		return getset ( func_get_args () );
	}
	/**
	 * simple general getter/setter
	 */
	private function getset($args) {
		$name = $args [0];
		if (count ( $args ) == 2) {
			$this->$name = $args [1];
		}
		return $this->$name;
	}
	
	/**
	 */
	public function show() {
		$bigside = $this->bigside;
		$photodir_url = $this->photodir_url;
		?>
<script type="text/javascript" src="/lightbox/dist/ekko-lightbox.min.js"></script>
<script>
		$(document).delegate('*[data-toggle="lightbox"]', 'click', function(event) {
    		event.preventDefault();
   			$(this).ekkoLightbox();
		});
	</script>
<style>
.tiler {
	margin: 3px;
}
</style>
<div id='tiler'>
		<?php
		foreach ( scandir ( $this->photodir ) as $file ) {
			if (! preg_match ( "/(jpg|png|jpeg)$/i", $file ))
				continue;
				
				// Syntax: getimagesize Array ( [0] => 1140 [1] => 1900 [2] => 2 [3] => width="1140" height="1900" [bits] => 8 [channels] => 3 [mime] => image/jpeg )
			
			$size = getimagesize ( $this->photodir . "/$file" );
			$width = $size [0];
			$height = $size [1];
			$ratio = $width / $height;
			if ($ratio >= 1) {
				$display_width = $bigside;
				$display_height = $height * ($bigside / $width);
			} else {
				$display_width = $width * ($bigside / $height);
				$display_height = $bigside;
			}
			echo <<<EOH
		<span class="tiler" >
		<a href="$photodir_url/$file" data-toggle="lightbox" data-gallery="multiimages" data-title="Aorangi Undulator" >                              
			<img src="$photodir_url/$file" width="$display_width" height="$display_height" />
		</a>								
		</span>
EOH;
		}
		echo "</div>";
		return $this;
	}
	
	/**
	 * Parameters: the path to the directory that contains images, the path to the directory
	 * in which thumbnails will be placed and the thumbnail's width.
	 * We are assuming that the path will be a relative path working
	 * both in the filesystem, and through the web for links
	 *
	 * @param unknown $pathToImages        	
	 * @param unknown $pathToThumbs        	
	 * @param unknown $thumbWidth        	
	 */
	function createThumbsFromDir() {
		$pathToImages = $this->originalsdir ();
		$pathToThumbs = $this->thumbsdir ();
		$thumbsBigside = $this->thumbsBigside ();
		
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
				$new_width = $thumbsBigside;
				$new_height = floor ( $height * ($thumbsBigside / $width) );
				
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
	public function cacheImage($fpath, $bigside) {
		$fname = basename ( $fpath );
		$cleanpath = preg_replace("/^\w:/", "", $fpath);
		$cleanpath = preg_replace("/\\/", "/", $cleanpath );
		$cachefile = $this->cachedir () . "$bigside/$fpath/$fname";
		
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
			$new_width = $thumbsBigside;
			$new_height = floor ( $height * ($thumbsBigside / $width) );
			
			// create a new temporary image
			$tmp_img = imagecreatetruecolor ( $new_width, $new_height );
			
			// copy and resize old image into new image
			imagecopyresized ( $tmp_img, $img, 0, 0, 0, 0, $new_width, $new_height, $width, $height );
			
			// save thumbnail into a file
			call_user_func ( "image$format", $tmp_img, $thpath );
		}
	}
}
?>
