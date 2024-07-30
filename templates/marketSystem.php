<?php
	if (!isset($load)) exit;
	
	$subContent = "";

	
	if (!$settings['enableVoteSystem']){
		exit(header("Location: ?page=player"));
		
	}
	if (!$player['character']['id']){
		$subContent = "Please <a href=\"?page=character\">select your character</a> first.";

		}
	else
	{

		
		
		
		$itemData = $mysqlClientGameServer->select2($queryGame['selectAllPlayerItems3'],array($player['character']['id']));
		
		if (!$itemData || $player['character']['level']<40){
			if($player['character']['level']<40)
			{
				$subContent = "You need level 40 or more to use this function.";
			}
			else{
			$subContent = "There's no items in your inventory that can be selled.";
			
			}
		}
			
		else
		{
			
		

			
			
			
			if (isset($itemData['item_id']))
				$playerItems[0] = $itemData;
			else{
				$playerItems = $itemData;	
			}
		

			
				$subContent .= "<div class=\"ui-widget-content widget-content\">
					
					<table id='tableEnchant'>
						<tr>
							<td valign=\"top\" class=\"percent50\">
								<table>
									<tr class=\"ui-widget-header widget-header percent50\">
										<td class=\"middle\" colspan=\"2\">
											Your items
										</td>
										<td class=\"tableEnchant ui-state-active\">
											Your price
										</td>
									</tr>";

		
									foreach ($playerItems as $item)
									{
										
										if($item['item_id']<200000 || $item['item_id']>=300000)
												continue;
											$item['item_id']=$item['item_id']-200000;
													$elementData = $mysqlClientGameServer->select2($queryGame['element'],array($item['object_id']));

													if($elementData){
								
												
																	
																	
													if(count($elementData) == 1){
														
													if($elementData[0]['elemType'] == 0) $type1 = " FIRE";
													if($elementData[0]['elemType'] == 1) $type1 = " WATER";	
													if($elementData[0]['elemType'] == 2) $type1 = " WIND";				
													if($elementData[0]['elemType'] == 3) $type1 = " EARTH";				
													if($elementData[0]['elemType'] == 4) $type1 = " HOLY";				
													if($elementData[0]['elemType'] == 5) $type1 = " DARK";	
													
													
			
																					
													$itemXML = $items->findItemInXML($item['item_id']);
													
													if ($items->getTradeable($itemXML) || $items->getAugmentation($item)) continue;
													

													$subContent .= "<tr class=\"ui-state-default\">
														<td class=\"tableIcon\">
															<img src=\"images/itemIcon.php?item=".$items->getIcon($itemXML)."&enchant_level=".$item['enchant_level']."\" alt=\"Item icon\" align=\"top\" />
														</td>
														<td>
															<img src=\"images/itemGradeIcons/grd_".$items->getGrade($itemXML).".png\" alt=\"Item grade\" align=\"top\" /> ".$items->getName($itemXML)." <b>(+<span class=\"enchantLevel\">".$item['enchant_level']."</span>)</b>
														<b>(<span class=\"enchantLevel\">".$item['count']."</span>)</b><b>(<span class=\"enchantLevel\">".$type1."</span>)</b>
														<b>(<span class=\"enchantLevel\">".$elementData[0]['elemValue']."</span>)</b></td>";

															$subContent .= "<td class=\"tableEnchant ui-state-hover\">
																	<a href=\"sell\" value=\"".$item['object_id']."\">SELL</a>
															</td>";

													$subContent .= "</tr>";
													
													
																				
													}else if(count($elementData) == 2){
														
													if($elementData[0]['elemType'] == 0) $type1 = " FIRE";
													if($elementData[0]['elemType'] == 1) $type1 = " WATER";	
													if($elementData[0]['elemType'] == 2) $type1 = " WIND";				
													if($elementData[0]['elemType'] == 3) $type1 = " EARTH";				
													if($elementData[0]['elemType'] == 4) $type1 = " HOLY";				
													if($elementData[0]['elemType'] == 5) $type1 = " DARK";		
														
														
													if($elementData[1]['elemType'] == 0) $type2 = " FIRE";
													if($elementData[1]['elemType'] == 1) $type2 = " WATER";	
													if($elementData[1]['elemType'] == 2) $type2 = " WIND";				
													if($elementData[1]['elemType'] == 3) $type2 = " EARTH";				
													if($elementData[1]['elemType'] == 4) $type2 = " HOLY";				
													if($elementData[1]['elemType'] == 5) $type2 = " DARK";
														
																					
																					
													$itemXML = $items->findItemInXML($item['item_id']);
													
													if ($items->getTradeable($itemXML) || $items->getAugmentation($item)) continue;

													
													$subContent .= "<tr class=\"ui-state-default\">
														<td class=\"tableIcon\">
															<img src=\"images/itemIcon.php?item=".$items->getIcon($itemXML)."&enchant_level=".$item['enchant_level']."\" alt=\"Item icon\" align=\"top\" />
														</td>
														<td>
															<img src=\"images/itemGradeIcons/grd_".$items->getGrade($itemXML).".png\" alt=\"Item grade\" align=\"top\" /> ".$items->getName($itemXML)." <b>(+<span class=\"enchantLevel\">".$item['enchant_level']."</span>)</b>
														<b>(<span class=\"enchantLevel\">".$item['count']."</span>)</b><b>(<span class=\"enchantLevel\">".$type1."</span>)</b>
														<b>(<span class=\"enchantLevel\">".$elementData[0]['elemValue']."</span>)</b>
														<b>(<span class=\"enchantLevel\">".$type2."</span>)</b>
														<b>(<span class=\"enchantLevel\">".$elementData[1]['elemValue']."</span>)</b></td>";

															$subContent .= "<td class=\"tableEnchant ui-state-hover\">
																	<a href=\"sell\" value=\"".$item['object_id']."\">SELL</a>
															</td>";

													$subContent .= "</tr>";
														
														
														
														
														
													}		else if(count($elementData) == 3){
														
														
														
													if($elementData[0]['elemType'] == 0) $type1 = " FIRE";
													if($elementData[0]['elemType'] == 1) $type1 = " WATER";	
													if($elementData[0]['elemType'] == 2) $type1 = " WIND";				
													if($elementData[0]['elemType'] == 3) $type1 = " EARTH";				
													if($elementData[0]['elemType'] == 4) $type1 = " HOLY";				
													if($elementData[0]['elemType'] == 5) $type1 = " DARK";		
														
														
													if($elementData[1]['elemType'] == 0) $type2 = " FIRE";
													if($elementData[1]['elemType'] == 1) $type2 = " WATER";	
													if($elementData[1]['elemType'] == 2) $type2 = " WIND";				
													if($elementData[1]['elemType'] == 3) $type2 = " EARTH";				
													if($elementData[1]['elemType'] == 4) $type2 = " HOLY";				
													if($elementData[1]['elemType'] == 5) $type2 = " DARK";	
														
														
													if($elementData[2]['elemType'] == 0) $type3 = " FIRE";
													if($elementData[2]['elemType'] == 1) $type3 = " WATER";	
													if($elementData[2]['elemType'] == 2) $type3 = " WIND";				
													if($elementData[2]['elemType'] == 3) $type3 = " EARTH";				
													if($elementData[2]['elemType'] == 4) $type3 = " HOLY";				
													if($elementData[2]['elemType'] == 5) $type3 = " DARK";	
														
																					
																					
													$itemXML = $items->findItemInXML($item['item_id']);
													
													if ($items->getTradeable($itemXML) || $items->getAugmentation($item)) continue;
													

													
													$subContent .= "<tr class=\"ui-state-default\">
														<td class=\"tableIcon\">
															<img src=\"images/itemIcon.php?item=".$items->getIcon($itemXML)."&enchant_level=".$item['enchant_level']."\" alt=\"Item icon\" align=\"top\" />
														</td>
														<td>
															<img src=\"images/itemGradeIcons/grd_".$items->getGrade($itemXML).".png\" alt=\"Item grade\" align=\"top\" /> ".$items->getName($itemXML)." <b>(+<span class=\"enchantLevel\">".$item['enchant_level']."</span>)</b>
														<b>(<span class=\"enchantLevel\">".$item['count']."</span>)</b><b>(<span class=\"enchantLevel\">".$type1."</span>)</b>
														<b>(<span class=\"enchantLevel\">".$elementData[0]['elemValue']."</span>)</b>
														<b>(<span class=\"enchantLevel\">".$type2."</span>)</b>
														<b>(<span class=\"enchantLevel\">".$elementData[1]['elemValue']."</span>)</b>
														<b>(<span class=\"enchantLevel\">".$type3."</span>)</b>
														<b>(<span class=\"enchantLevel\">".$elementData[2]['elemValue']."</span>)</b></td>";

															$subContent .= "<td class=\"tableEnchant ui-state-hover\">
																	<a href=\"sell\" value=\"".$item['object_id']."\">SELL</a>
															</td>";

													$subContent .= "</tr>";
													}
													}else{
										


										$itemXML = $items->findItemInXML($item['item_id']);
										//echo $items->getName($itemXML);

										//if ($items->getTradeable($itemXML) || $items->getAugmentation($item)) continue;

										$subContent .= "<tr class=\"ui-state-default\">
											<td class=\"tableIcon\">
												<img src=\"images/itemIcon.php?item=".$items->getIcon($itemXML)."&enchant_level=".$item['enchant_level']."\" alt=\"Item icon\" align=\"top\" />
											</td>
											<td>
												<img src=\"images/itemGradeIcons/grd_".$items->getGrade($itemXML).".png\" alt=\"Item grade\" align=\"top\" /> ".$items->getName($itemXML)." <b>(+<span class=\"enchantLevel\">".$item['enchant_level']."</span>)</b>
											<b>(<span class=\"enchantLevel\">".$item['count']."</span>)</b></td>";

												$subContent .= "<td class=\"tableEnchant ui-state-hover\">
														<a href=\"sell\" value=\"".$item['object_id']."\">SELL</a>
												</td>";

										$subContent .= "</tr>";
												}
									}
								$subContent .= "</table>
							</td>
							

						</tr>
					</table>
				</div>";
		}

	}


			
	
	//echo "$subContent";
	$templateContent->replace("items",$subContent);
	
?>
