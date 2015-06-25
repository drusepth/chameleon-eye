<?php
	//Expects a writeable dir ./saved_images/ to work!
	
	//Download image
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, "http://www.ipillion.com/captcha/captcha.php?height=60&width=200");
	curl_setopt($ch, CURLOPT_HEADER, 0);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	$binary_image = curl_exec($ch);
	
	//Create image and read some properties
	$im = imagecreatefromstring($binary_image);
	$x  = imagesx($im);
	$y  = imagesy($im);
	$mid = floor($y / 2);
	$imagename = mt_rand(10000000, 99999999);
	
	//Save the original image
	imagepng($im, "./saved_images/" . $imagename . "-ORI.png");
	
	//make image greyscale
	imagecopymergegray($im, $im, 0, 0, 0, 0, $x, $y, 0);
	
	//Walk through the middle of the image to find the darkest color
	$darkest_color = 255;
	for($i = 0; $i < $x; $i++)
	{
		$rgb = imagecolorat($im, $i, $mid);
		$r = ($rgb >> 16) & 0xFF;
		$g = ($rgb >> 8) & 0xFF;
		$b = $rgb & 0xFF;
		if($r < $darkest_color) $darkest_color = $r;
		if($g < $darkest_color) $darkest_color = $r;
		if($b < $darkest_color) $darkest_color = $r;
		
	}

	//FIRST PASS - remove clutter, make B&W
	$white = imagecolorallocate($im, 255, 255, 255);
	$black = imagecolorallocate($im, 0, 0, 0);
	for($i = 0; $i < $x; $i++)
	{
		for($j = 0; $j < $y; $j++)
		{
			$rgb = imagecolorat($im, $i, $j);
			$r = ($rgb >> 16) & 0xFF;
			$g = ($rgb >> 8) & 0xFF;
			$b = $rgb & 0xFF;
			if($r > ($darkest_color + ($_GET['slack'] + 0)))
			{
				imagesetpixel($im, $i, $j, $white);
			}
			else
			{
				imagesetpixel($im, $i, $j, $black);
			}
			
			if($i == 0 || $i == ($x - 1) || $j == 0 || $j == ($y - 1))
			{
				imagesetpixel($im, $i, $j, $white);
			}
			
		}
	}
	
	//SECOND PASS
	for($i = 1; $i < ($x - 1); $i++)
	{
		for($j = 1; $j < ($y - 1); $j++)
		{
			$pixels = 0;
			$rgb = imagecolorat($im, $i, $j);
			//Count howmany surrounding pixels are not black
			if(imagecolorat($im, ($i - 1), ($j - 1)))	$pixels++;
			if(imagecolorat($im, $i, ($j - 1)))			$pixels++;
			if(imagecolorat($im, ($i + 1), ($j - 1)))	$pixels++;
			
			if(imagecolorat($im, ($i - 1), $j))	$pixels++;
			if(imagecolorat($im, ($i + 1), $j))	$pixels++;

			if(imagecolorat($im, ($i - 1), ($j + 1)))	$pixels++;
			if(imagecolorat($im, $i, ($j - 1)))			$pixels++;
			if(imagecolorat($im, ($i + 1), ($j + 1)))	$pixels++;

			if($rgb == 0 && $pixels > 6)
			{
				imagesetpixel($im, $i, $j, $white);
			}
			elseif($rgb != 0 && $pixels < 3)
			{
				imagesetpixel($im, $i, $j, $black);
			}
		}
	}
	
	//Save final image
	imagepng($im, "./saved_images/" . $imagename . "-FIN.png");
	
	
	echo "X: $x<br />";
	echo "Y: $y<br />";
	echo "Darkest color: $darkest_color<br />";
	echo '<img src="saved_images/', $imagename, '-ORI.png" alt="ORIG" /><br />';
	echo '<img src="saved_images/', $imagename, '-FIN.png" alt="FINAL" /><br />';
	
?>