<?php
	if (!isset($load)) exit;
	
	if (!!!$config['enableItemShop'])
		exit(header("Location: ?page=player"));
	
	$subContent = "";
	
	$itemData		= $mysqlClientLoginServer->select("SELECT * FROM `acp_shop_items` WHERE `serverId` = ?;",array($player['server']));
	$categoryData	= $mysqlClientLoginServer->select("SELECT * FROM `acp_shop_categories` WHERE `serverId` = ?;",array($player['server']));
	$setData		= $mysqlClientLoginServer->select("SELECT * FROM `acp_shop_sets` WHERE `serverId` = ?;",array($player['server']));
	
	$individualItems	= array();
	$categoryItems		= array();
	$setItems			= array();
	
	if (!$itemData)
		$subContent = "There's currently no items for sale.";
	else
	{
		if (isset($itemData['serverId']))
			$individualItems[0] = $itemData;
		else
		{
			foreach($itemData as $item)
			{
				if ($item['categoryId'] > 0)
					$categoryItems[] = $item;
				else if ($item['setId'] > 0)
					$setItems[] = $item;
				else
					$individualItems[] = $item;
			}
		}
			
		if (!empty($categoryData) && !empty($categoryItems))
		{
			if ($categoryData['id'])
				$categories[0] = $categoryData;
			else
				$categories = $categoryData;
				
			foreach ($categories as $category)
			{
				$subContent .= "<div class=\"ui-widget-header widget-header\">".$category['name']."</div>
				<div class=\"ui-widget-content widget-content\">
					<table class=\"shopCategories\">";
						foreach ($categoryItems as $item)
						{
							if ($item['categoryId'] != $category['id']) continue;
							
							$itemXML = $items->findItemInXML($item['itemId']);
							$subContent .= "<tr class=\"ui-state-default\">
								<td class=\"tableIcon\">
									<img src=\"images/itemIcon.php?item=".$items->getIcon($itemXML).(($items->getEnchantable($itemXML)) ? "&enchant_level=".$item['itemEnchant'] : "")."\" alt=\"Item icon\" align=\"top\" />
								</td>
								<td>
									<img src=\"images/itemGradeIcons/grd_".$items->getGrade($itemXML).".png\" alt=\"Item grade\" align=\"top\" /> ".$items->getName($itemXML)." ".((($items->getEnchantable($itemXML))) ? "<b>(+".$item['itemEnchant'].")</b>" : "")."
								</td>
								<td class=\"tableCount\">
									x".number_format($item['itemCount'])."
								</td>
								<td class=\"tablePrice ui-state-active\">
									".number_format($item['price'],2)."
								</td>
								<td class=\"tablePurchase ui-state-hover\">
									<a href=\"purchaseItem\" value=\"".$item['id']."\">Buy now</a>
								</td>
							</tr>";
						}
					$subContent .= "</table>
				</div>";
			}
		}
					
		if (!empty($individualItems))
		{
			$subContent .= "<div class=\"ui-widget-header widget-header\">Other items</div>
				<div class=\"ui-widget-content widget-content\">
					<table class=\"shopIndividuals\">";
						foreach ($individualItems as $item)
						{
							if ($item['setId'] != 0 || $item['categoryId'] != 0) continue;
							/*if ($item['itemId']==60000)
							{
								continue;
							}*/
							
							$itemXML = $items->findItemInXML($item['itemId']);
							//print_r($itemXML);
							$subContent .= "<tr class=\"ui-state-default\">
								<td class=\"tableIcon\">
									<img src=\"images/itemIcon.php?item=".$items->getIcon($itemXML).(($items->getEnchantable($itemXML)) ? "&enchant_level=".$item['itemEnchant'] : "")."\" alt=\"Item icon\" align=\"top\" />
								</td>
								<td>
									<img src=\"images/itemGradeIcons/grd_".$items->getGrade($itemXML).".png\" alt=\"Item grade\" align=\"top\" /> ".$items->getName($itemXML)." ".((($items->getEnchantable($itemXML))) ? "<b>(+".$item['itemEnchant'].")</b>" : "")."
								</td>
								<td class=\"tableCount\">
									x".number_format($item['itemCount'])."
								</td>
								<td class=\"tablePrice ui-state-active\">
									".number_format($item['price'],2)."
								</td>
								<td class=\"tablePurchase ui-state-hover\">
									<a href=\"purchaseItem\" value=\"".$item['id']."\">Buy now</a>
								</td>
							</tr>";
						}
					$subContent .= "</table>
				</div>";
		}
		
		if (!empty($setData) && !empty($setItems))
		{
			if ($setData['id'])
				$sets[0] = $setData;
			else
				$sets = $setData;
				
			foreach ($sets as $set)
			{
				$span = false;
				$subContent .= "<div class=\"ui-widget-header widget-header\">".$set['name']."</div>
					<div class=\"ui-widget-content widget-content\">
						<table class=\"shopSets\">
							<tr class=\"ui-state-default\">
								<td colspan=\"4\">";
									foreach ($setItems as $item)
									{
										if ($item['setId'] != $set['id']) continue;
										
										$itemXML = $items->findItemInXML($item['itemId']);
										$subContent .= "<img src=\"images/itemIcon.php?item=".$items->getIcon($itemXML).(($items->getEnchantable($itemXML)) ? "&enchant_level=".$item['itemEnchant'] : "")."\" alt=\"Item icon\" align=\"top\" /> ";
									}
						
								$subContent .= "</td>
								<td class=\"tablePreview ui-state-hover\">
									<a href=\"showMore\">More information</a>
								</td>
							</tr>";
							
							foreach ($setItems as $item)
							{
								if ($item['setId'] != $set['id']) continue;
								
								$itemXML = $items->findItemInXML($item['itemId']);
								$subContent .= "<tr class=\"tableCollapsed ui-state-default\">
									<td class=\"tableIcon\">
										<img src=\"images/itemIcon.php?item=".$items->getIcon($itemXML).(($items->getEnchantable($itemXML)) ? "&enchant_level=".$item['itemEnchant'] : "")."\" alt=\"Item icon\" align=\"top\" />
									</td>
									<td>
										<img src=\"images/itemGradeIcons/grd_".$items->getGrade($itemXML).".png\" alt=\"Item grade\" align=\"top\" /> ".$items->getName($itemXML)." ".((($items->getEnchantable($itemXML))) ? "<b>(+".$item['itemEnchant'].")</b>" : "")."
									</td>
									<td class=\"tableCount\">
										x".number_format($item['itemCount'])."
									</td>";
									if (!$span)
									{
										$subContent .= "<td class=\"tablePrice ui-state-active\" rowspan=\"100%\" rowspan=\"99\">
											".number_format($set['price'],2)."
										</td>
										<td class=\"tablePurchase ui-state-hover\" rowspan=\"100%\" rowspan=\"99\">
											<a href=\"purchaseSet\" value=\"".$set['id']."\">Buy now</a>
										</td>";
										$span = true;
									}
								$subContent .= "</tr>";
							}
						$subContent .= "</table>
					</div>";
			}
		}
	}
	
	$templateContent->replace("items",$subContent);
	
?>
