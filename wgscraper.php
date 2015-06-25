<?php

	require("image.class.php");
	
	// Create the image
	if (isset($_GET['src'])) {
		$image = new Image($_GET['src']);
	} else {
		$image = new Image();
	}
	
	$image->countRGB(10);
	$image->countPureGreytones($_GET['slack']);
	
	echo "Width: " . $image->getWidth() . "px<br />";
	echo "Height: " . $image->getHeight() . "px<br />";
	echo "[Mostly] red pixels: " . $image->getPixelCount("Red") . " (" . $image->getTotalCount("Red") . " combined value in all pixels)<br />";
	echo "[Mostly] green pixels: " . $image->getPixelCount("Green") . " (" . $image->getTotalCount("Green") . " combined value in all pixels)<br />";
	echo "[Mostly] blue pixels: " . $image->getPixelCount("Blue") . " (" . $image->getTotalCount("Blue") . " combined value in all pixels)<br />";
	echo "[Pure] black pixels: " . $image->getPixelCount("Black") . " (" . $image->getTotalCount("Black") . " combined value in all pixels)<br />";
	echo "[Pure] white pixels: " . $image->getPixelCount("White") . " (" . $image->getTotalCount("White") . " combined value in all pixels)<br />";
	echo "Dominant color: " . $image->getDominantColor() . "<br />";
	$image->showImage();
	
?>