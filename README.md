# phpBlendmodes
A simple PHP class to perform blend modes on images like photoshop/etc does.

## source
This blend mode function is taken from an other project of mine:

https://github.com/blacksunshineCoding/whirl

## usage

	include 'Blendmodes.class.php';

	$blendmodes = new Blendmodes();
	$blendedImage = $blendmodes->blend($blendmmode);

see demo.php

## blend modes
 - dissolve
 - darkerColor
 - darken
 - multiply
 - colorBurn
 - linearBurn
 - lighterColor
 - lighten
 - screen
 - colorDodge
 - linearDodge
 - overlay
 - softLight
 - hardLight
 - vividLight
 - linearLight
 - pinLight
 - hardMix
 - difference
 - exclusion
 - subtract
 - divide
 - hue
 - saturation
 - color
 - luminosity

 ## accuracy
 most of the blend modes work exact like photoshop, but some of the more complex blend modes show more or less differences