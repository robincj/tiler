
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
		?>
		</div>
<?php
		return $this;
	}
	
	/**
	 * Parameters: the path to the directory that contains images, the path to the directory
	 * in which resized image files will be placed.
	 * We are assuming that the path will be a relative path working
	 * both in the filesystem, and through the web for links
	 *
	 * @param unknown $pathToImages        	
	 * @param unknown $pathToThumbs        	
	 * @param unknown $thumbWidth        	
	 */
	function createCacheFromDir($overwrite = FALSE) {
		// open the directory
		$dir = opendir ( $this->originalsdir () );
		$cachedFiles = array ();
		// loop through it, looking for any/all JPG files:
		while ( false !== ($fname = readdir ( $dir )) ) {
			$fpath = "$pathToImages/$fname";
			$cacheFile = cacheImage ( $fpath, $this->$thumbs_bigside (), $overwrite );
			if ($cacheFile)
				$cacheFiles [] = $cacheFile;
		}
		// close the directory
		closedir ( $dir );
		return $cacheFiles [];
	}
	/**
	 * Creates cached image file, returns cached file's filename with path.
	 *
	 * @param unknown $fpath        	
	 * @param unknown $bigside        	
	 * @param string $overwrite        	
	 * @return string|boolean
	 */
	public function cacheImage($fpath, $bigside, $overwrite = FALSE) {
		$fname = basename ( $fpath );
		preg_match ( "/^(\w):/", $fpath, $matches );
		$cleanpath = preg_replace ( "/^\w:/", $matches [1], $fpath );
		$cleanpath = preg_replace ( "/\\/", "/", $cleanpath );
		$cachefile = $this->cachedir () . "{$bigside}px/$fpath/$fname";
		
		if (file_exists ( $cachefile ) && ! $overwrite)
			return $cachefile;
			
			// parse path for the extension
		$info = pathinfo ( $fpath );
		$valid_formats = array (
				'jpg',
				'jpeg',
				'png' 
		);
		$ext = strtolower ( $info ['extension'] );
		if (array_key_exists ( 'extension', $info ) && in_array ( $ext, $valid_formats )) {
			// continue only if this is a JPEG/PNG image
			
			$format = NULL;
			
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
				if ($width > $height) {
					// Landscape
					$new_width = $bigside;
					$new_height = floor ( $height * ($bigside / $width) );
				} else {
					// Portrait
					$new_height = $bigside;
					$new_width = floor ( $width * ($bigside / $height) );
				}
				
				// create a new temporary image
				$tmp_img = imagecreatetruecolor ( $new_width, $new_height );
				
				// copy and resize old image into new image
				imagecopyresized ( $tmp_img, $img, 0, 0, 0, 0, $new_width, $new_height, $width, $height );
				
				// save thumbnail into a file
				call_user_func ( "image$format", $tmp_img, $cachefile );
			}
		} else
			return FALSE;
		return $cachefile;
	}
}
?>
