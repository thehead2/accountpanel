<?php
	if (@fclose(@fopen("gameIcons/".$_GET['item'].".png", "r")))
		$iconLink = "gameIcons/".$_GET['item'].".png";
	else
		$iconLink = "gameIcons/noimage.png";
				
	$icon = ImageCreateFromPng($iconLink);
	$blue = ImageColorAllocate($icon,255,255,255);
	$black = ImageColorAllocate($icon,0,0,0);
	
	if (isset($_GET['enchantLevel']) && is_numeric($_GET['enchantLevel']))
	{
		imagestring($icon, 2, imagesx($icon)-(strlen("+".$_GET['enchantLevel'])*6.5)-1, 18, "+".$_GET['enchantLevel'], $black);
		imagestring($icon, 2, imagesx($icon)-(strlen("+".$_GET['enchantLevel'])*6.5), 19, "+".$_GET['enchantLevel'], $blue);
	}
			
	header("Content-Type: image/png");
			
	imagepng($icon);
	imagedestroy($icon);
?>