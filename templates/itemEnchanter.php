<?php
	if (!isset($load)) exit;
	

	
	
	
	$subContent = "";
	
	if (!!!$config['enableItemEnchanter']){
		exit(header("Location: ?page=player"));
	}
	if (!$player['character']['id']){
		$subContent = "Please <a href=\"?page=character\">select your character</a> first.";
	
		}
	else
	{
		

		$itemData = $mysqlClientGameServer->select2($queryGame['selectAllPlayerItems'],array($player['character']['id']));
		

		
		if (!$itemData){
			
			$subContent = "There's no items in your inventory that can be enchanted.";
			}
		else
		{
			$hasEnchantableWeapons = false;
			$hasEnchantableArmors = false;
			
			if (isset($itemData['item_id']))
				$playerItems[0] = $itemData;
			else{
				$playerItems = $itemData;
				
				

				
				
			}
				$subContent .= "<div class=\"ui-widget-header widget-header\">Your Items</div>
				<div class=\"ui-widget-content widget-content\">
					<br /><center><b>MAXIMUM LIMIT ON A SINGLE EQUIPMENT IS <span class=\"ui-state-default ui-corner-all credits\">".$config['enchanterLimit']."</span></b></center><br />
					<table id='tableEnchant'>
						<tr>
							<td valign=\"top\" class=\"percent50\">
								<table>
									<tr class=\"ui-widget-header widget-header percent50\">
										<td class=\"middle\" colspan=\"2\">
											Weapons/Shields
										</td>
										<td class=\"tableEnchant ui-state-active\">
											".$config['enchanterWeaponPrice']."
										</td>
									</tr>";

		
									foreach ($playerItems as $item)
									{
										if($item['item_id']>58000)
												continue;
	
	
										$itemXML = $items->findItemInXML($item['item_id']);
										
										if (!$items->getEnchantable($itemXML) || $items->getType($itemXML) != "Weapon") continue;
										
										$subContent .= "<tr class=\"ui-state-default\">
											<td class=\"tableIcon\">
												<img src=\"images/itemIcon.php?item=".$items->getIcon($itemXML)."&enchant_level=".$item['enchant_level']."\" alt=\"Item icon\" align=\"top\" />
											</td>
											<td>
												<img src=\"images/itemGradeIcons/grd_".$items->getGrade($itemXML).".png\" alt=\"Item grade\" align=\"top\" /> ".$items->getName($itemXML)." <b>(+<span class=\"enchantLevel\">".$item[2]."</span>)</b>
											</td>";
											if ($item[2] < $config['enchanterLimit'])
												$subContent .= "<td class=\"tableEnchant ui-state-hover\">
													<a href=\"enchantItem\" value=\"".$item['object_id']."\">Enchant it now</a>
												</td>";
											else
												$subContent .= "<td class=\"tableEnchant ui-widget-header\">
												
												</td>";	
										$subContent .= "</tr>";
										$hasEnchantableWeapons = true;
									}
									if (!$hasEnchantableWeapons)
										$subContent .= "<tr class=\"ui-state-default\">
											<td class=\"middle\" colspan=\"3\">
												There's no Weapons/Shields that can be enchanted.
											</td>
										</tr>";
								$subContent .= "</table>
							</td>
							<td valign=\"top\" class=\"percent50\">
								<table>
									<tr class=\"ui-widget-header widget-header percent50\">
										<td class=\"middle\" colspan=\"2\">
											Armors/Jewels
										</td>
										<td class=\"tableEnchant ui-state-active\">
											".$config['enchanterArmorPrice']."
										</td>
									</tr>";
									foreach ($playerItems as $item)
									{
										if($item['item_id']>58000)
												continue;
										$itemXML = $items->findItemInXML($item['item_id']);
										
										if (!$items->getEnchantable($itemXML) || $items->getType($itemXML) != "Armor") continue;
										
										$subContent .= "<tr class=\"ui-state-default\">
											<td class=\"tableIcon\">
												<img src=\"images/itemIcon.php?item=".$items->getIcon($itemXML)."&enchant_level=".$item['enchant_level']."\" alt=\"Item icon\" align=\"top\" />
											</td>
											<td>
												<img src=\"images/itemGradeIcons/grd_".$items->getGrade($itemXML).".png\" alt=\"Item grade\" align=\"top\" /> ".$items->getName($itemXML)." <b>(+<span class=\"enchantLevel\">".$item[2]."</span>)</b>
											</td>";
											if ($item[2] < $config['enchanterLimit'])
												$subContent .= "<td class=\"tableEnchant ui-state-hover\">
													<a href=\"enchantItem\" value=\"".$item['object_id']."\">Enchant it now</a>
												</td>";
											else
												$subContent .= "<td class=\"tableEnchant ui-widget-header\">
												
												</td>";	
										$subContent .= "</tr>";
										$hasEnchantableArmors = true;
									}
									if (!$hasEnchantableArmors)
										$subContent .= "<tr class=\"ui-state-default\">
											<td class=\"middle\" colspan=\"3\">
												There's no Armors/Jewels that can be enchanted.
											</td>
										</tr>";
								$subContent .= "</table>
							</td>
						</tr>
					</table>
				</div>";
		
		
		if (!$hasEnchantableWeapons && !$hasEnchantableArmors)
			$subContent = "There's no items in your inventory that can be enchanted.";
		}
	}
	//echo "$subContent";
	$templateContent->replace("items",$subContent);
	
?>
