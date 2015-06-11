
<?php
class Tiler {
	public $cachedir;
	public $originalsdir;
	public $cacheurl;
	public $originalsurl;
	public $thumbs_bigside = 300;
	public $photos_bigside = 600;
	
	private $nextimage;
	/**
	 */
	public function __construct() {
	}
	
	// GETTERS AND SETTERS
	/**
	 * simple general getter/setter
	 */
	private function getset() {
		$callers = debug_backtrace ();
		$fname = $callers [1] ['function'];
		$args = $callers [1] ['args'];
		$name = lcfirst ( preg_replace ( "/^[gs]et_?/", '', $fname ) );
		
		if (isset ( $args [0] ))
			$this->$name = $args [0];
		
		return $this->$name;
	}
	public function cachedir() {
		return $this->getset ();
	}
	public function originalsdir() {
		return $this->getset ();
	}
	public function cacheurl() {
		return $this->getset ();
	}
	public function originalsurl() {
		return $this->getset ();
	}
	public function thumbs_bigside() {
		return $this->getset ();
	}
	public function photos_bigside() {
		return $this->getset ();
	}
	private function nextimage() {
		return $this->getset ();
	}
	/**
	 */
	public function showDir() {
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
		foreach ( scandir ( $this->originalsdir() ) as $file ) {
			$this->show($file, $lib);
		}
		?>
		</div>
		<?php
		return $this;
	}
	
	public function show($file, $lib=0){
		$thumbsize = $this->thumbs_bigside();
		$photosize = $this->photos_bigside();
		echo <<<EOH
		<span class="tiler" >
		<a href="img.php?lib=$lib&imgsrc=$file&size=$photosize" data-toggle="lightbox" data-gallery="multiimages" data-title="Aorangi Undulator" >
			<img src="img.php?lib=$lib&imgsrc=$file&size=$thumbsize" />
		</a>
		</span>
EOH;
	}
	/**
	 *
	 * @param unknown $file        	
	 */
	function streamImageFromFile($file) {
		$format = getFormatFromSuffix ( $file );
		$src_img = call_user_func ( "imagecreatefrom$format", $file );
		call_user_func ( "image$format", $src_img );
		imagedestroy ( $src_img );
	}
	
	/**
	 * Creates resized photo and thumbnail cached files from files found in the originalsdir.
	 *
	 * @param string $overwrite        	
	 * @return array of cache filenames
	 */
	function createCacheFromDir( $overwrite = FALSE) {
		// open the directory
		$dir = opendir ( $this->originalsdir () );
		$cachedFiles = array ();
		// loop through it, looking for any/all JPG files:
		while ( false !== ($fname = readdir ( $dir )) ) {
			$original_fpath = $this->originalsdir () . "/$fname";
			$cacheFiles ['thumb'][] = cacheImage ( $original_fpath, TRUE, FALSE, $overwrite );
			$cacheFiles ['photo'][] = cacheImage ( $original_fpath, FALSE, FALSE, $overwrite );			
		}
		// close the directory
		closedir ( $dir );
		return $cacheFiles;
	}
	/**
	 * Creates cached image file if it doesn't already exist.
	 * If $stream == TRUE then streams the resized image data, else returns cached file's filename with path.
	 *
	 * @param unknown $fname        	
	 * @param unknown $bigside        	
	 * @param boolean $stream        	
	 * @param boolean $overwrite        	
	 * @return string|boolean
	 */
	public function cacheImage($fname, $thumb=FALSE, $stream = FALSE, $overwrite = FALSE) {
		$bigside = $thumb ? $this->thumbs_bigside() : $this->photos_bigside ;
		$fpath = $this->originalsdir () . "/$fname";
		preg_match ( "/^(\w):/", $fpath, $matches );
		$cleanpath = preg_replace ( "/^\w:/", $matches [1], $fpath );
		$cleanpath = preg_replace ( "/\\/", "/", $cleanpath );
		$cachefile = $this->cachedir () . "{$bigside}px/$fpath/$fname";
		
		if (file_exists ( $cachefile ) && ! $overwrite)
			return $cachefile;
		
		$format = getFormatFromSuffix ( $fpath );
		if (in_array ( $ext, array (
				'png',
				'jpeg' 
		) )) {
			// continue only if this is a JPEG/PNG image
			if ($format) {
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
				// Stream the image data to output if $stream is true.
				if ($stream)
					call_user_func ( "image$format", $tmp_img );
			}
		} else
			return FALSE;
		return $cachefile;
	}
	/**
	 * Returns jpeg or png depending on file format indicated by suffix.
	 *
	 * @param unknown $filename        	
	 * @return Ambigous <NULL, string>
	 */
	static function getFormatFromSuffix($filename) {
		// parse path for the extension
		$info = pathinfo ( $filename );
		$ext = strtolower ( $info ['extension'] );
		$format = NULL;
		
		if (array_key_exists ( 'extension', $info )) {
			if ($ext == 'jpg' || $ext == 'jpeg')
				$format = "jpeg";
			elseif ($ext == 'png')
				$format = "png";
		}
		return $format;
	}
}

?>
