<?php

	class Image {
		
		var $height;
		var $width;
		var $image;
		var $name;
		
		var $rgb_pixel_count =	array("Red" => 0, "Green" => 0, "Blue" => 0, "Black" => 0, "White" => 0);
		var $rgb_total_count =	array("Red" => 0, "Green" => 0, "Blue" => 0, "Black" => 0, "White" => 0);
		
		function Image($url = "http://www.ipillion.com/captcha/captcha.php?height=60&width=200"){
			
			// Download image
				$ch = curl_init();
				curl_setopt($ch, CURLOPT_URL, $url);
				curl_setopt($ch, CURLOPT_HEADER, 0);
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
				$binary_image = curl_exec($ch);
				
			// Create an image
				$this->image = @imagecreatefromstring($binary_image) or die("Invalid image data!");
				
			// Read in general data about the image
				$this->width = imagesx($this->image);
				$this->height = imagesy($this->image);
				$this->name = mt_rand(1000000000, 9999999999);
				
			// Save the image
				imagepng($this->image, "./saved_images/" . $this->name . "-ORI.png");
			
		}
		
		function countPureGreytones($slack = 0) {
			
			$white = imagecolorallocate($this->image, 255, 255, 255);
			$black = imagecolorallocate($this->image, 0, 0, 0);
			
			for ($x = 0; $x < $this->width; $x++) {
					
				for($y = 0; $y < $this->height; $y++) {
						
					// Calculate RGB values of this pixel
						$rgb = imagecolorat($this->image, $x, $y);
						$r = ($rgb >> 16) & 0xFF;
						$g = ($rgb >> 8) & 0xFF;
						$b = $rgb & 0xFF;
						
					// Count pure pixels
						// Black
							if ($r + $g + $b - $slack <= 0) {
								$this->rgb_total_count["White"] += 255;
								$this->rgb_pixel_count["White"]++;
							}
								
						// White
							if ($r + $g + b >= ( (255 - $slack) * 3)) {
								$this->rgb_total_count["Black"] += 255;
								$this->rgb_pixel_count["Black"]++;
							}
					
					}
				
			}
			
		}
		
		function countRGB($slack = 0) {
			
				/*
					Slack:
						The greater the slack, the more a color's R, G, or B value must 
						be larger than the other two values for it to be counted as a
						colored pixel.  
				*/
			
				for ($x = 0; $x < $this->width; $x++) {
					
					for($y = 0; $y < $this->height; $y++) {
						
						// Calculate RGB values of this pixel
							$rgb = imagecolorat($this->image, $x, $y);
							$r = ($rgb >> 16) & 0xFF;
							$g = ($rgb >> 8) & 0xFF;
							$b = $rgb & 0xFF;
							
						// Count colored pixels
							// Red
								$this->rgb_total_count["Red"] += $r;
								if ($r > $g + $slack && $r > $b + $slack)
									$this->rgb_pixel_count["Red"]++;
									
							// Green
								$this->rgb_total_count["Green"] += $g;
								if ($g > $r + $slack && $g > $b + $slack)
									$this->rgb_pixel_count["Green"]++;
									
							// Blue
								$this->rgb_total_count["Blue"] += $b;
								if ($b > $r + $slack && $b > $g + $slack)
									$this->rgb_pixel_count["Blue"]++;
						
					}
					
				}
			
		}
		
		function getDominantColor() {
			return $this->max_key($this->rgb_total_count);
		}
		
		function getHeight() {
			return $this->height;
		}
		
		function getPixelCount($color) {
			return $this->rgb_pixel_count[$color];
		}
		
		function getTotalCount($color) {
			return $this->rgb_total_count[$color];
		}
		
		function getWidth() {
			return $this->width;
		}
		
		function max_key($array) {
			# http://www.php.net/manual/en/function.max.php#81531
			foreach ($array as $key => $val) {
				if ($val == max($array)) return $key; 
			}
		}
		
		function showImage() {
			echo '<img src="saved_images/', $this->name, '-ORI.png" alt="ORIG" /><br />';
		}
		
	}
	
	
	/*
		Scratchpaper:
		
					// Colorless
			else if ( ($r + $g + $b) / 3 < ($r + $g + b) + $gradient_variance ) {
			#if ($r == 0 && $g == 0 && $b == 0)
			
				// Black
				if ( ($r + $g + $b) / 3 > 127)
					$rgb_counter[3]++;
				
				else 
					$rgb_counter[4]++;
			
			}
				
			// White
			#if ($r == 255 && $g == 255 && $b == 255)
			#	$rgb_counter[3]++;
			
			
			
			
		echo "Unaccounted for pixels: " . ( ($x * $y) - $rgb_counter[0] - $rgb_counter[1] - $rgb_counter[2] ) . "<br />";
			
	*/

?>