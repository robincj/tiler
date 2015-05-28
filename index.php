<?php

include_once 'tiler.php';

$tiler = new Tiler();

$tiler->originalsdir('/data/development/gitbox/undulator_website/images/photos/originals');
$tiler->cachedir('/tmp/cache');


/*
$originals = "images/photos/originals";
$photodir = "images/photos";
$thumbdir = "images/photos/thumbs";
createThumbs ( $originals, $photodir, 600 );
createThumbs ( $originals, $thumbdir, 100 );
echo "<div>";
//include 'js/jssor_bootstrap/php/jssor_image-gallery.php';
include 'tiler/tiler.php';
$tiler = new Tiler();
$tiler->photodir = $photodir;
$tiler->photodir_url = "/images/photos";
$tiler->show();
echo "</div>";

?>
<br />
<div class="bordered-box text-center">

	<p>
		More photos can be viewed on <a
			href='https://www.flickr.com/photos/96525237@N08/sets/72157634894768852/'>Flickr</a>,
		the <a href='https://www.facebook.com/groups/1548916308660466/'>Aorangi
			Undulator 100 Facebook page</a> and <a
			href='https://www.facebook.com/pav.kotarba/media_set?set=a.10154875353490287.626285286&type=3'>Pavel's
			Facebook page</a>.
	</p>
</div>
*/