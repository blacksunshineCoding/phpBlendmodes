<?php
class Blendmodes {
	public function blend($baseImage, $topImage, $mode) {
			$baseIsTrueColor = imageistruecolor($baseImage);
			$topIsTrueColor = imageistruecolor($topImage);
			
			$baseWidth  = imagesx($baseImage);
			$baseHeight = imagesy($baseImage);
			$topWidth   = imagesx($topImage);
			$topHeight  = imagesy($topImage);
			
			$destX = ($baseWidth - $topWidth) / 2;
			$destY = ($baseHeight - $topHeight) / 2;

			for ($x = 0; $x < $topWidth; ++$x) {
				for ($y = 0; $y < $topHeight; ++$y) {
					
					$color = imagecolorat($baseImage, $x + $destX, $y + $destY);
					
					if ($baseIsTrueColor) {
						$baseColor = array(
							'red' => ($color >> 16) & 0xFF,
							'green' => ($color >> 8) & 0xFF,
							'blue' => $color & 0xFF,
							'alpha' => ($color & 0x7F000000) >> 24,
						);
					} else {
						$baseColor = imagecolorsforindex($baseImage, $color);
					}
				
					$color = imagecolorat($topImage, $x, $y);
					
					if ($topIsTrueColor) {
						$topColor = array(
							'red' => ($color >> 16) & 0xFF,
							'green' => ($color >> 8) & 0xFF,
							'blue' => $color & 0xFF,
							'alpha' => ($color & 0x7F000000) >> 24,
						);
					} else {
						$topColor = imagecolorsforindex($topImage, $color);
					}
					
					switch ($mode) {
						case 'dissolve':
							$topOpacityPercent = round(($topColor['alpha'] / 127) * 100);
							$randomPercent = rand(1,100);
							
							if ($randomPercent <= $topOpacityPercent) {
								$destColorAlpha = 127.0;
							} else {
								$destColorAlpha = 0.0;
							}
							
							$destColor = array(
								'red' => intval($topColor['red']),
								'green' => intval($topColor['green']),
								'blue' => intval($topColor['blue']),
								'alpha' => intval($destColorAlpha)
							);
							break;
							
						case 'darkerColor':
							$baseColorHsl = $this->rgbToHsl($baseColor['red'], $baseColor['green'], $baseColor['green']);
							$topColorHsl = $this->rgbToHsl($topColor['red'], $topColor['green'], $topColor['blue']);
							
							if ($baseColorHsl['lightness'] < $topColorHsl['lightness']) {
								$destColor = array(
									'red' => intval($baseColor['red']),
									'green' => intval($baseColor['green']),
									'blue' => intval($baseColor['blue']),
									'alpha' => intval($baseColor['alpha'])
								);
							} else {
								$destColor = array(
									'red' => intval($topColor['red']),
									'green' => intval($topColor['green']),
									'blue' => intval($topColor['blue']),
									'alpha' => intval($topColor['alpha'])
								);
							}
							break;
							
							
						case 'darken':
							$destColor = array(
								'red' => intval(min($baseColor['red'], $topColor['red'])),
								'green' => intval(min($baseColor['green'], $topColor['green'])),
								'blue' => intval(min($baseColor['blue'], $topColor['blue'])),
								'alpha' => intval($topColor['alpha'])
							);
							break;
							
						default:
						case 'multiply':
							$destColor = array(
								'red' => intval($baseColor['red'] * ($topColor['red'] / 255.0)),
								'green' => intval($baseColor['green'] * ($topColor['green'] / 255.0)),
								'blue' => intval($baseColor['blue'] * ($topColor['blue'] / 255.0)),
								'alpha' => intval($topColor['alpha'])
							);
							break;
							
						case 'colorBurn':
							$destColor = array(
								'red' => intval($baseColor['red'] * ($topColor['red'] / 255.0)),
								'green' => intval($baseColor['green'] * ($topColor['green'] / 255.0)),
								'blue' => intval($baseColor['blue'] * ($topColor['blue'] / 255.0)),
								'alpha' => intval($topColor['alpha'])
							);
							break;
							
						case 'linearBurn':
							$destColor = array(
								'red' => intval($baseColor['red'] + $topColor['red'] - 255.0),
								'green' => intval($baseColor['green'] + $topColor['green'] - 255.0),
								'blue' => intval($baseColor['blue'] + $topColor['blue'] - 255.0),
								'alpha' => intval($topColor['alpha'])
							);
							break;
							
						case 'lighterColor':
							$baseColorHsl = $this->rgbToHsl($baseColor['red'], $baseColor['green'], $baseColor['green']);
							$topColorHsl = $this->rgbToHsl($topColor['red'], $topColor['green'], $topColor['blue']);
							
							if ($baseColorHsl['lightness'] > $topColorHsl['lightness']) {
								$destColor = array(
									'red' => intval($baseColor['red']),
									'green' => intval($baseColor['green']),
									'blue' => intval($baseColor['blue']),
									'alpha' => intval($baseColor['alpha'])
								);
							} else {
								$destColor = array(
									'red' => intval($topColor['red']),
									'green' => intval($topColor['green']),
									'blue' => intval($topColor['blue']),
									'alpha' => intval($topColor['alpha'])
								);
							}
							break;
							
						case 'lighten':
							$destColor = array(
								'red' => intval(max($baseColor['red'], $topColor['red'])),
								'green' => intval(max($baseColor['green'], $topColor['green'])),
								'blue' => intval(max($baseColor['blue'], $topColor['blue'])),
								'alpha' => intval($topColor['alpha'])
							);
							break;
							
						case 'screen':
							$destColor = array(
								'red' => intval(round($baseColor['red'] + ((255.0 - $baseColor['red']) / 100) * (($topColor['red'] / 255.0) * 100))),
								'green' => intval(round($baseColor['green'] + ((255.0 - $baseColor['green']) / 100) * (($topColor['green'] / 255.0) * 100))),
								'blue' => intval(round($baseColor['blue'] + ((255.0 - $baseColor['blue']) / 100) * (($topColor['blue'] / 255.0) * 100))),
								'alpha' => intval($topColor['alpha'])
							);
							break;
							
						case 'colorDodge':
							$destColor = array(
								'red' => intval(($baseColor['red'] + $topColor['red'] > 255.0 ? 255.0 : $baseColor['red'])),
								'green' => intval(($baseColor['green'] + $topColor['green'] > 255.0 ? 255.0 : $baseColor['green'])),
								'blue' => intval(($baseColor['blue'] + $topColor['blue'] > 255.0 ? 255.0 : $baseColor['blue'])),
								'alpha' => intval($topColor['alpha'])
							);
							break;
						
						case 'linearDodge':
							$destColor = array(
								'red' => intval(($baseColor['red'] + $topColor['red']) > 255.0 ? 255.0 : ($baseColor['red'] + $topColor['red'])),
								'green' => intval(($baseColor['green'] + $topColor['green']) > 255.0 ? 255.0 : ($baseColor['green'] + $topColor['red'])),
								'blue' => intval(($baseColor['blue'] + $topColor['blue']) > 255.0 ? 255.0 : ($baseColor['blue'] + $topColor['blue'])),
								'alpha' => intval($topColor['alpha'])
							);
							break;
							
						case 'overlay':
							$destColor = array(
								'red' => intval($baseColor['red'] > 127.5 ? (($topColor['red'] * ((255.0 - $baseColor['red']) / 127.5)) + ($baseColor['red'] - (255.0 - $baseColor['red']))) : ($topColor['red'] * ($baseColor['red'] / 127.5))),
								'green' => intval($baseColor['green'] > 127.5 ? (($topColor['green'] * ((255.0 - $baseColor['green']) / 127.5)) + ($baseColor['green'] - (255.0 - $baseColor['green']))) : ($topColor['green'] * ($baseColor['green'] / 127.5))),
								'blue' => intval($baseColor['blue'] > 127.5 ? (($topColor['blue'] * ((255.0 - $baseColor['blue']) / 127.5)) + ($baseColor['blue'] - (255.0 - $baseColor['blue']))) : ($topColor['blue'] * ($baseColor['blue'] / 127.5))),
								'alpha' => intval($topColor['alpha'])
							);
							break;
							
						case 'softLight':
							$destColor = array(
								'red' => intval(round((($topColor['red'] / 255.0) < 0.5) ? ((2 * ($baseColor['red'] / 255.0) * ($topColor['red'] / 255.0) + (($baseColor['red'] / 255.0) * ($baseColor['red'] / 255.0)) * (1 - (2 * ($topColor['red'] / 255.0)))) * 255.0) : (((2 * ($baseColor['red'] / 255.0)) * (1-($topColor['red'] / 255.0)) + sqrt(($baseColor['red'] / 255.0)) * ((2 * ($topColor['red'] / 255.0)) - 1)) * 255.0))),
								'green' => intval(round((($topColor['green'] / 255.0) < 0.5) ? ((2 * ($baseColor['green'] / 255.0) * ($topColor['green'] / 255.0) + (($baseColor['green'] / 255.0) * ($baseColor['green'] / 255.0)) * (1 - (2 * ($topColor['green'] / 255.0)))) * 255.0) : (((2 * ($baseColor['green'] / 255.0)) * (1-($topColor['green'] / 255.0)) + sqrt(($baseColor['green'] / 255.0)) * ((2 * ($topColor['green'] / 255.0)) - 1)) * 255.0))),
								'blue' => intval(round((($topColor['blue'] / 255.0) < 0.5) ? ((2 * ($baseColor['blue'] / 255.0) * ($topColor['blue'] / 255.0) + (($baseColor['blue'] / 255.0) * ($baseColor['blue'] / 255.0)) * (1 - (2 * ($topColor['blue'] / 255.0)))) * 255.0) : (((2 * ($baseColor['blue'] / 255.0)) * (1-($topColor['blue'] / 255.0)) + sqrt(($baseColor['blue'] / 255.0)) * ((2 * ($topColor['blue'] / 255.0)) - 1)) * 255.0))),
								'alpha' => intval($topColor['alpha'])
							);
							break;
							
						case 'hardLight':
							$destColor = array(
								'red' => intval(round(($topColor['red'] > 127.5) ? ($baseColor['red'] * ((255 - $topColor['red']) / 127.5)) + ($topColor['red'] - (255 - $topColor['red'])) : ($baseColor['red'] * ($topColor['red'] / 127.5)))),
								'green' => intval(round(($topColor['green'] > 127.5) ? ($baseColor['green'] * ((255 - $topColor['green']) / 127.5)) + ($topColor['green'] - (255 - $topColor['green'])) : ($baseColor['green'] * ($topColor['green'] / 127.5)))),
								'blue' => intval(round(($topColor['blue'] > 127.5) ? ($baseColor['blue'] * ((255 - $topColor['blue']) / 127.5)) + ($topColor['blue'] - (255 - $topColor['blue'])) : ($baseColor['blue'] * ($topColor['blue'] / 127.5)))),
								'alpha' => intval($topColor['alpha'])
							);
							break;
							
						case 'vividLight':
							
							// hotfix for division by zero bug
							foreach ($baseColor as $baseKey => $base) if ($baseKey != 'alpha') if ($base <= 0) {
								$baseColor[$baseKey] = 1.0;
							} elseif ($base >= 255.0) {
								$baseColor[$baseKey] = 254.0;
							}
							foreach ($topColor as $topKey => $top) if ($topKey != 'alpha') if ($top <= 0) {
								$topColor[$topKey] = 1.0;
							} elseif ($top >= 255.0) {
								$topColor[$topKey] = 254.0;
							}
							
							$destColor = array(
								'red' => intval(round((($topColor['red'] / 255) <= 0.5) ? ((1 - (1 - ($baseColor['red'] / 255.0)) / (2 * ($topColor['red'] / 255.0))) * 255.0) : ((($baseColor['red'] / 255.0) / (2 * (1 - ($topColor['red'] / 255.0)))) * 255.0))),
								'green' => intval(round((($topColor['green'] / 255) <= 0.5) ? ((1 - (1 - ($baseColor['green'] / 255.0)) / (2 * ($topColor['green'] / 255.0))) * 255.0) : ((($baseColor['green'] / 255.0) / (2 * (1 - ($topColor['green'] / 255.0)))) * 255.0))),
								'blue' => intval(round((($topColor['blue'] / 255) <= 0.5) ? ((1 - (1 - ($baseColor['blue'] / 255.0)) / (2 * ($topColor['blue'] / 255.0))) * 255.0) : ((($baseColor['blue'] / 255.0) / (2 * (1 - ($topColor['blue'] / 255.0)))) * 255.0))),
								'alpha' => intval($topColor['alpha'])
							);
							break;
							
						case 'linearLight':
							$destColor = array(
								'red' => intval(round(((($baseColor['red']/255) + (2 * ($topColor['red']/255)) - 1) * 255.0))),
								'green' => intval(round(((($baseColor['green']/255) + (2 * ($topColor['green']/255)) - 1) * 255.0))),
								'blue' => intval(round(((($baseColor['blue']/255) + (2 * ($topColor['blue']/255)) - 1) * 255.0))),
								'alpha' => intval($topColor['alpha'])
							);
							break;
							
						case 'pinLight':
							$destColor = array(
								'red' => intval(round((($baseColor['red'] / 255.0) < ((2 * ($topColor['red'] / 255.0)) - 1) ) ? (((2 * ($topColor['red'] / 255.0)) - 1) * 255.0) : ( (2 * ($topColor['red'] / 255.0) - 1 < ($baseColor['red'] / 255.0)) && (($baseColor['red'] / 255.0) < 2 * ($topColor['red'] / 255.0))) ? ($baseColor['red']) : ((2 * ($topColor['red'] / 255.0)) * 255.0))),
								'green' => intval(round((($baseColor['green'] / 255.0) < ((2 * ($topColor['green'] / 255.0)) - 1) ) ? (((2 * ($topColor['green'] / 255.0)) - 1) * 255.0) : ( (2 * ($topColor['green'] / 255.0) - 1 < ($baseColor['green'] / 255.0)) && (($baseColor['green'] / 255.0) < 2 * ($topColor['green'] / 255.0))) ? ($baseColor['green']) : ((2 * ($topColor['green'] / 255.0)) * 255.0))),
								'blue' => intval(round((($baseColor['blue'] / 255.0) < ((2 * ($topColor['blue'] / 255.0)) - 1) ) ? (((2 * ($topColor['blue'] / 255.0)) - 1) * 255.0) : ( (2 * ($topColor['blue'] / 255.0) - 1 < ($baseColor['blue'] / 255.0)) && (($baseColor['blue'] / 255.0) < 2 * ($topColor['blue'] / 255.0))) ? ($baseColor['blue']) : ((2 * ($topColor['blue'] / 255.0)) * 255.0))),
								'alpha' => intval($topColor['alpha'])
							);
							break;
							
						case 'hardMix':
							$destColor = array(
								'red' => intval(($baseColor['red'] + $topColor['red']) >= 255.0 ? 255.0 : 0.0),
								'green' => intval(($baseColor['green'] + $topColor['green']) >= 255.0 ? 255.0 : 0.0),
								'blue' => intval(($baseColor['blue'] + $topColor['blue']) >= 255.0 ? 255.0 : 0.0),
								'alpha' => intval($topColor['alpha'])
							);
							break;
							
						case 'difference':
							$destColor = array(
								'red' => intval(($topColor['red'] - $baseColor['red']) < 0 ? (($topColor['red'] - $baseColor['red']) * -1) : ($topColor['red'] - $baseColor['red'])),
								'green' => intval(($topColor['green'] - $baseColor['green']) < 0 ? (($topColor['green'] - $baseColor['green']) * -1) : ($topColor['green'] - $baseColor['green'])),
								'blue' => intval(($topColor['blue'] - $baseColor['blue']) < 0 ? (($topColor['blue'] - $baseColor['blue']) * -1) : ($topColor['blue'] - $baseColor['blue'])),
								'alpha' => intval($topColor['alpha'])
							);
							break;
						
						case 'exclusion':
							$destColor = array(
								'red' => intval(round(((0.5 - 2.0 * (($baseColor['red'] / 255.0) - 0.5) * (($topColor['red'] / 255.0) - 0.5)) * 255.0))),
								'green' => intval(round(((0.5 - 2.0 * (($baseColor['green'] / 255.0) - 0.5) * (($topColor['green'] / 255.0) - 0.5)) * 255.0))),
								'blue' => intval(round(((0.5 - 2.0 * (($baseColor['blue'] / 255.0) - 0.5) * (($topColor['blue'] / 255.0) - 0.5)) * 255.0))),
								'alpha' => intval($topColor['alpha'])
							);
							break;
							
						case 'subtract':
							$destColor = array(
								'red' => intval(($baseColor['red'] - $topColor['red']) < 0.0 ? 0.0 : ($baseColor['red'] - $topColor['red'])),
								'green' => intval(($baseColor['green'] - $topColor['green']) < 0.0 ? 0.0 : ($baseColor['green'] - $topColor['green'])),
								'blue' => intval(($baseColor['blue'] - $topColor['blue']) < 0.0 ? 0.0 : ($baseColor['blue'] - $topColor['blue'])),
								'alpha' => intval($topColor['alpha'])
							);
							break;
						
						case 'divide':
							// hotfix for division by zero bug
							foreach ($baseColor as $baseKey => $base) if ($baseKey != 'alpha') if ($base <= 0) $baseColor[$baseKey] = 1;
							foreach ($topColor as $topKey => $top) if ($topKey != 'alpha') if ($top <= 0) $topColor[$topKey] = 1;
							
							$destColor = array(
								'red' => intval((($baseColor['red'] / $topColor['red']) * 255.0) > 255.0 ? 255.0 : (($baseColor['red'] / $topColor['red']) * 255.0)),
								'green' => intval((($baseColor['green'] / $topColor['green']) * 255.0) > 255.0 ? 255.0 : (($baseColor['green'] / $topColor['green']) * 255.0)),
								'blue' => intval((($baseColor['blue'] / $topColor['blue']) * 255.0) > 255.0 ? 255.0 : (($baseColor['blue'] / $topColor['blue']) * 255.0)),
								'alpha' => intval($topColor['alpha'])
							);
							break;
							
						case 'hue':
							$baseColorHsl = $this->rgbToHsl($baseColor['red'], $baseColor['green'], $baseColor['green']);
							$topColorHsl = $this->rgbToHsl($topColor['red'], $topColor['green'], $topColor['blue']);
							$destColorRgb = $this->hslToRgb($topColorHsl['hue'], $baseColorHsl['saturation'], $baseColorHsl['lightness']);
							
							$destColor = array(
								'red' => $destColorRgb['red'],
								'green' => $destColorRgb['green'],
								'blue' => $destColorRgb['blue'],
								'alpha' => $topColor['alpha']
							);
							break;
						
						case 'saturation':
							$baseColorHsl = $this->rgbToHsl($baseColor['red'], $baseColor['green'], $baseColor['green']);
							$topColorHsl = $this->rgbToHsl($topColor['red'], $topColor['green'], $topColor['blue']);
							$destColorRgb = $this->hslToRgb($baseColorHsl['hue'], $topColorHsl['saturation'], $baseColorHsl['lightness']);
							
							$destColor = array(
								'red' => $destColorRgb['red'],
								'green' => $destColorRgb['green'],
								'blue' => $destColorRgb['blue'],
								'alpha' => $topColor['alpha']
							);
							break;
							
						case 'color':
							$baseColorHsl = $this->rgbToHsl($baseColor['red'], $baseColor['green'], $baseColor['green']);
							$topColorHsl = $this->rgbToHsl($topColor['red'], $topColor['green'], $topColor['blue']);
							$destColorRgb = $this->hslToRgb($topColorHsl['hue'], $topColorHsl['saturation'], $baseColorHsl['lightness']);
							
							$destColor = array(
								'red' => $destColorRgb['red'],
								'green' => $destColorRgb['green'],
								'blue' => $destColorRgb['blue'],
								'alpha' => $topColor['alpha']
							);
							break;
								
						case 'luminosity':
							$baseColorHsl = $this->rgbToHsl($baseColor['red'], $baseColor['green'], $baseColor['green']);
							$topColorHsl = $this->rgbToHsl($topColor['red'], $topColor['green'], $topColor['blue']);
							$destColorRgb = $this->hslToRgb($baseColorHsl['hue'], $baseColorHsl['saturation'], $topColorHsl['lightness']);
							
							$destColor = array(
								'red' => $destColorRgb['red'],
								'green' => $destColorRgb['green'],
								'blue' => $destColorRgb['blue'],
								'alpha' => $topColor['alpha']
							);
							break;
							
					}
				
					$colorIndex = imagecolorallocatealpha($baseImage, $destColor['red'], $destColor['green'], $destColor['blue'], $destColor['alpha']);
					
					if ($colorIndex === false) {
						$colorIndex = imagecolorclosestalpha($baseImage, $destColor['red'], $destColor['green'], $destColor['blue'], $destColor['alpha']);
					}
					
					imagesetpixel($baseImage, $x + $destX, $y + $destY, $colorIndex);
				}
			}
			
			return $baseImage;
		}
}