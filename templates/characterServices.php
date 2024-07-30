<?php
	if (!isset($load)) exit;
	
	if (!!!$config['enableCharacterServices'])
		exit(header("Location: ?page=player"));
		
	$subContent = "";
	
	if (!!$config['enableServiceUnstuck'])
		$subContent .= "<tr class=\"ui-state-default\">
			<td class=\"tableIcon\">
				<img src=\"images/gameIcons/skillraid.png\" alt=\"Item grade\" align=\"top\" />
			</td>
			<td>
				<b>Return character to town</b><br />
				Useful when stuck or unable to login to the server.
			</td>
			<td class=\"tablePrice ui-state-active\">
				".number_format($config['serviceUnstuckPrice'],2)."
			</td>
			<td class=\"tablePurchase ui-state-hover\">
				<a href=\"purchaseService\" value=\"unstuck\">Buy now</a>
			</td>
		</tr>";
	if (!!$config['enableServiceUnjail'])
		$subContent .= "<tr class=\"ui-state-default\">
			<td class=\"tableIcon\">
				<img src=\"images/gameIcons/skill4287.png\" alt=\"Item grade\" align=\"top\" />
			</td>
			<td>
				<b>Unjail</b><br />
				Got punished for braking the rules? Here's your chance to escape.
			</td>
			<td class=\"tablePrice ui-state-active\">
				".number_format($config['serviceUnjailPrice'],2)."
			</td>
			<td class=\"tablePurchase ui-state-hover\">
				<a href=\"purchaseService\" value=\"unjail\">Buy now</a>
			</td>
		</tr>";
	if (!!$config['enableServiceUnban'])
		$subContent .= "<tr class=\"ui-state-default\">
			<td class=\"tableIcon\">
				<img src=\"images/gameIcons/skill4274.png\" alt=\"Item grade\" align=\"top\" />
			</td>
			<td>
				<b>Unban</b><br />
				Got banned for serious misconduct? We'll allow you to play again.
			</td>
			<td class=\"tablePrice ui-state-active\">
				".number_format($config['serviceUnbanPrice'],2)."
			</td>
			<td class=\"tablePurchase ui-state-hover\">
				<a href=\"purchaseService\" value=\"unban\">Buy now</a>
			</td>
		</tr>";
	if (!!$config['enableServiceNoblesse'])
		$subContent .= "<tr class=\"ui-state-default\">
			<td class=\"tableIcon\">
				<img src=\"images/gameIcons/skill1323.png\" alt=\"Item grade\" align=\"top\" />
			</td>
			<td>
				<b>Noblesse</b><br />
				Don't want to level up your subclass or complete the long quest? Here's your chance, you get a nice tiara as well.
			</td>
			<td class=\"tablePrice ui-state-active\">
				".number_format($config['serviceNoblessePrice'],2)."
			</td>
			<td class=\"tablePurchase ui-state-hover\">
				<a href=\"purchaseService\" value=\"noblesse\">Buy now</a>
			</td>
		</tr>";
	if (!!$config['enableServiceCleanseKarma'])
		$subContent .= "<tr class=\"ui-state-default\">
			<td class=\"tableIcon\">
				<img src=\"images/gameIcons/skill5662.png\" alt=\"Item grade\" align=\"top\" />
			</td>
			<td>
				<b>Cleanse Karma</b><br />
				Useful if you killed a few innocent players and want to get rid of your karma.
			</td>
			<td class=\"tablePrice ui-state-active\">
				karma
			</td>
			<td class=\"tablePurchase ui-state-hover\">
				<a href=\"purchaseService\" value=\"cleanseKarma\">Buy now</a>
			</td>
		</tr>";
	if (!!$config['enableServiceRemovePk'])
		$subContent .= "<tr class=\"ui-state-default\">
			<td class=\"tableIcon\">
				<img src=\"images/gameIcons/etc_leash_i00.png\" alt=\"Item grade\" align=\"top\" />
			</td>
			<td>
				<b>Remove PK</b><br />
				Reaching the dangerous limit of 5 and don't want to lose your items? Remove your PK without sin eater.
			</td>
			<td class=\"tablePrice ui-state-active\">
				".number_format($config['serviceRemovePkPrice'],2)."
			</td>
			<td class=\"tablePurchase ui-state-hover\">
				<a href=\"purchaseService\" value=\"removePK\">Buy now</a>
			</td>
		</tr>";
	if (!!$config['enableServiceChangeSex'])
		$subContent .= "<tr class=\"ui-state-default\">
			<td class=\"tableIcon\">
				<img src=\"images/gameIcons/skill5739.png\" alt=\"Item grade\" align=\"top\" />
			</td>
			<td>
				<b>Change Sex</b><br />
				Want to change the way you look? Change your sex.
			</td>
			<td class=\"tablePrice ui-state-active\">
				".number_format($config['serviceChangeSexPrice'],2)."
			</td>
			<td class=\"tablePurchase ui-state-hover\">
				<a href=\"purchaseService\" value=\"changeSex\">Buy now</a>
			</td>
		</tr>";
	$subContent .= "<tr class=\"ui-state-default\">
		<td class=\"tableIcon\">
			<img src=\"images/gameIcons/item_normal315.png\" alt=\"Item grade\" align=\"top\" />
		</td>
		<td>
			<b>Change Name</b><br />
			Want to change your name? Change your name.
		</td>
		<td class=\"tablePrice ui-state-active\">
			3.00
		</td>
		<td class=\"tablePurchase ui-state-hover\">
			<button class=\"changeName\">Buy Now</button>
		</td>
	</tr>";		
	$templateContent->replace("services",$subContent);
?>
