<?php
include_once 'tiler.php';

$params = array_merge ( $_GET, $_POST );

if (isset ( $params ['img'] ))
	$imgsrc = basename($params ['img']);
else
	exit ();

$imglib = isset ( $params ['lib'] ) ? $params ['lib'] : 0;
$size = isset ( $params ['size'] ) ? $params ['size'] : 200;

$originalsdirs = array (
		'/data/development/gitbox/undulator_website/images/photos/originals' 
);

$tiler = new Tiler ();
$tiler->originalsdir ( $originalsdirs [$imglib] );
$tiler->cachedir ( '/tmp/cache' );
$tiler->cacheImage ( $imgsrc, TRUE, TRUE, FALSE );
