<?php
	if (!isset($load)) exit;
	
	$subContent = "";
	
	if (!!!$config['enableItemAuction'])
		exit(header("Location: ?page=player"));
		
		
		
		
	
	$auctionData = $mysqlClientLoginServer->select("SELECT *,coalesce(`highest_bidder`.`bidAmount`,`acp_auctions`.`startingBid`) AS `highestBid` FROM `acp_auctions` LEFT JOIN (SELECT `auctionId`,`account` AS `highestBidder` ,`bidAmount` FROM `acp_auction_bids` ORDER BY `bidAmount` DESC) `highest_bidder` ON `acp_auctions`.`id` = `highest_bidder`.`auctionId` WHERE `serverId` = ? GROUP BY `acp_auctions`.`id`;",array($player['server']));
	//$auctionData = $mysqlClientLoginServer->select("SELECT * FROM `acp_auctions` WHERE `serverId` = ?;",array($player['server']));


	
	


	
	
	
	

	
	$activeAuctions = array();
	$finishedAuctions = array();

	if (!$auctionData)
		$subContent = "There's no items for sale at the moment. Please come back later.";

	
	
	else
	{
		
		

		
		
		if (isset($auctionData['itemId']))
			$auctions[0] = $auctionData;
		else
			$auctions = $auctionData;
			
		foreach ($auctions as $auction)
		{
			if ($auction['endingTime'] > time() && $auction['claimed'] == 0)
				$activeAuctions[] = $auction;
			else
				$finishedAuctions[] = $auction;
		}
		
		$subContent .= "<div class=\"ui-widget-header widget-header\">Active sales</div>
		<div class=\"ui-widget-content widget-content\">
			<table class=\"shopAuctions\">";


	
			
		if (!$activeAuctions)
			$subContent .= "There's currently no active sales taking place.";
		else
		{
			


		
		
		
			
			foreach ($activeAuctions as $auction)
			{
				
				if(($auction['type1'])==-1){
				
				
				$itemXML = $items->findItemInXML($auction['itemId']-200000);
				$subContent .= "<tr class=\"ui-state-default\">
					<td class=\"tableIcon\">
						<img src=\"images/itemIcon.php?item=".$items->getIcon($itemXML).(($items->getEnchantable($itemXML)) ? "&enchant_level=".$auction['itemEnchant'] : "")."\" alt=\"Item icon\" align=\"top\" />
					</td>
					<td>
						<img src=\"images/itemGradeIcons/grd_".$items->getGrade($itemXML).".png\" alt=\"Item grade\" align=\"top\" /> ".$items->getName($itemXML)." ".((($items->getEnchantable($itemXML))) ? "<b>(+".$auction['itemEnchant'].")</b>" : "")."
					</td>
					<td class=\"tableCount\">
						x".number_format($auction['itemCount'])." 
					</td>
					<td class=\"tableBid ui-state-active\">
						".(($auction['highestBidder'] == $player['account']) ? "<span class=\"green\">YOU'RE HIGHEST BIDDER<br /><b>".number_format($auction['highestBid'],2)."</b></span>" : "PRICE<br /><b>".number_format($auction['highestBid'],2)."&nbsp ")."</b><br />
					</td>
					<td class=\"tableEnd ui-state-highlight\">
						".date("d M\, Y H:i:s e",$auction['endingTime'])."<br /><b>TIME LEFT <span class=\"auctionTime\" value=\"".($auction['endingTime']-time()-2)."\">".formatTime($auction['endingTime']-time()-1)."</span></b>
					</td>
					<td class=\"tablePurchase ui-state-hover\">";

			if ($auction['highestBidder'] == $player['account'])
						$subContent .= "<a href=\"javascript:location.reload(true);\" value=\"".$auction['id']."\">Refresh</a>";
			else{

			


		if ($auction['character_id'] == $player['character']['id']){
			
			if (!isset($auction['highestBidder']))
				
			$subContent .= "<a href=\"claimItem2\" value=\"".$auction['id']."\">Claim</a>";
			
			
		}
			else
				
			//$subContent .= "<a href=\"placeBid\" value=\"".$auction['id']."\">Buy</a>";
				$subContent .=		"<a href=\"purchaseItem1\" value=\"".$auction['id']."\">Buy now</a>";

		
			
			}

				
					
					$subContent .= "</td>
				</tr>";
			}else if(($auction['type1']!=-1) && ($auction['type2']==-1)){
				
				
				if($auction['type1'] == 0) $type1 = " FIRE ";
				if($auction['type1'] == 1) $type1 = " WATER ";	
				if($auction['type1'] == 2) $type1 = " WIND ";				
				if($auction['type1'] == 3) $type1 = " EARTH ";				
				if($auction['type1'] == 4) $type1 = " HOLY ";				
				if($auction['type1'] == 5) $type1 = " DARK ";	
				
				
				$itemXML = $items->findItemInXML($auction['itemId']-200000);
				
				$subContent .= "<tr class=\"ui-state-default\">
					<td class=\"tableIcon\">
						<img src=\"images/itemIcon.php?item=".$items->getIcon($itemXML).(($items->getEnchantable($itemXML)) ? "&enchant_level=".$auction['itemEnchant'] : "")."\" alt=\"Item icon\" align=\"top\" />
					</td>
					<td>
						<img src=\"images/itemGradeIcons/grd_".$items->getGrade($itemXML).".png\" alt=\"Item grade\" align=\"top\" /> ".$items->getName($itemXML)." ".((($items->getEnchantable($itemXML))) ? "<b>(+".$auction['itemEnchant'].")</b>" : "").$type1.$auction['value1']."
					</td>
					<td class=\"tableCount\">
						x".number_format($auction['itemCount'])."
					</td>
					<td class=\"tableBid ui-state-active\">
						".(($auction['highestBidder'] == $player['account']) ? "<span class=\"green\">YOU'RE HIGHEST BIDDER<br /><b>".number_format($auction['highestBid'],2)."</b></span>" : "PRICE<br /><b>".number_format($auction['highestBid'],2)."")."</b><br />
					</td>
					<td class=\"tableEnd ui-state-highlight\">
						".date("d M\, Y H:i:s e",$auction['endingTime'])."<br /><b>TIME LEFT <span class=\"auctionTime\" value=\"".($auction['endingTime']-time()-2)."\">".formatTime($auction['endingTime']-time()-1)."</span></b>
					</td>
					<td class=\"tablePurchase ui-state-hover\">";

			if ($auction['highestBidder'] == $player['account'])
						$subContent .= "<a href=\"javascript:location.reload(true);\" value=\"".$auction['id']."\">Refresh</a>";
			else{

			


			if ($auction['character_id'] == $player['character']['id']){
			
			if (!isset($auction['highestBidder']))
				
			$subContent .= "<a href=\"claimItem2\" value=\"".$auction['id']."\">Claim</a>";
			
			
		}
			else
				
			//$subContent .= "<a href=\"placeBid\" value=\"".$auction['id']."\">Buy</a>";
				$subContent .=		"<a href=\"purchaseItem1\" value=\"".$auction['id']."\">Buy now</a>";		

		
			
			}

				
					
					$subContent .= "</td>
				</tr>";
				
				
				
				
				
				
				
				
				
				
				
			}else if(($auction['type2']!=-1) && ($auction['type3']==-1)){
				
				
				
				
				
				
				
				
				if($auction['type1'] == 0) $type1 = " FIRE ";
				if($auction['type1'] == 1) $type1 = " WATER ";	
				if($auction['type1'] == 2) $type1 = " WIND ";				
				if($auction['type1'] == 3) $type1 = " EARTH ";				
				if($auction['type1'] == 4) $type1 = " HOLY ";				
				if($auction['type1'] == 5) $type1 = " DARK ";	
				
				if($auction['type2'] == 0) $type2 = " FIRE ";
				if($auction['type2'] == 1) $type2 = " WATER ";	
				if($auction['type2'] == 2) $type2 = " WIND ";				
				if($auction['type2'] == 3) $type2 = " EARTH ";				
				if($auction['type2'] == 4) $type2 = " HOLY ";				
				if($auction['type2'] == 5) $type2 = " DARK ";	
				
				
				
				$itemXML = $items->findItemInXML($auction['itemId']-200000);
				
				$subContent .= "<tr class=\"ui-state-default\">
					<td class=\"tableIcon\">
						<img src=\"images/itemIcon.php?item=".$items->getIcon($itemXML).(($items->getEnchantable($itemXML)) ? "&enchant_level=".$auction['itemEnchant'] : "")."\" alt=\"Item icon\" align=\"top\" />
					</td>
					<td>
						<img src=\"images/itemGradeIcons/grd_".$items->getGrade($itemXML).".png\" alt=\"Item grade\" align=\"top\" /> ".$items->getName($itemXML)." ".((($items->getEnchantable($itemXML))) ? "<b>(+".$auction['itemEnchant'].")</b>" : "").$type1.$auction['value1'].$type2.$auction['value2']."
					</td>
					<td class=\"tableCount\">
						x".number_format($auction['itemCount'])."
					</td>
					<td class=\"tableBid ui-state-active\">
						".(($auction['highestBidder'] == $player['account']) ? "<span class=\"green\">YOU'RE HIGHEST BIDDER<br /><b>".number_format($auction['highestBid'],2)."</b></span>" : "PRICE<br /><b>".number_format($auction['highestBid'],2)."")."</b><br />
					</td>
					<td class=\"tableEnd ui-state-highlight\">
						".date("d M\, Y H:i:s e",$auction['endingTime'])."<br /><b>TIME LEFT <span class=\"auctionTime\" value=\"".($auction['endingTime']-time()-2)."\">".formatTime($auction['endingTime']-time()-1)."</span></b>
					</td>
					<td class=\"tablePurchase ui-state-hover\">";

			if ($auction['highestBidder'] == $player['account'])
						$subContent .= "<a href=\"javascript:location.reload(true);\" value=\"".$auction['id']."\">Refresh</a>";
			else{

			


		if ($auction['character_id'] == $player['character']['id']){
			
			if (!isset($auction['highestBidder']))
				
			$subContent .= "<a href=\"claimItem2\" value=\"".$auction['id']."\">Claim</a>";
			
			
		}
			else
				
			//$subContent .= "<a href=\"placeBid\" value=\"".$auction['id']."\">Buy</a>";
						$subContent .=		"<a href=\"purchaseItem1\" value=\"".$auction['id']."\">Buy now</a>";

		
			
			}

				
					
					$subContent .= "</td>
				</tr>";
				
				
				
				
				
				
				
				
				
			}else if(($auction['type3']!=-1)){
				
				//echo "TYPE 3 NO NULL";
				
				
				if($auction['type1'] == 0) $type1 = " FIRE ";
				if($auction['type1'] == 1) $type1 = " WATER ";	
				if($auction['type1'] == 2) $type1 = " WIND ";				
				if($auction['type1'] == 3) $type1 = " EARTH ";				
				if($auction['type1'] == 4) $type1 = " HOLY ";				
				if($auction['type1'] == 5) $type1 = " DARK ";	
				
				if($auction['type2'] == 0) $type2 = " FIRE ";
				if($auction['type2'] == 1) $type2 = " WATER ";	
				if($auction['type2'] == 2) $type2 = " WIND ";				
				if($auction['type2'] == 3) $type2 = " EARTH ";				
				if($auction['type2'] == 4) $type2 = " HOLY ";				
				if($auction['type2'] == 5) $type2 = " DARK ";	
				
				if($auction['type3'] == 0) $type3 = " FIRE ";
				if($auction['type3'] == 1) $type3 = " WATER ";	
				if($auction['type3'] == 2) $type3 = " WIND ";				
				if($auction['type3'] == 3) $type3 = " EARTH ";				
				if($auction['type3'] == 4) $type3 = " HOLY ";				
				if($auction['type3'] == 5) $type3 = " DARK ";
				
				
				
				$itemXML = $items->findItemInXML($auction['itemId']-200000);
				
				$subContent .= "<tr class=\"ui-state-default\">
					<td class=\"tableIcon\">
						<img src=\"images/itemIcon.php?item=".$items->getIcon($itemXML).(($items->getEnchantable($itemXML)) ? "&enchant_level=".$auction['itemEnchant'] : "")."\" alt=\"Item icon\" align=\"top\" />
					</td>
					<td>
						<img src=\"images/itemGradeIcons/grd_".$items->getGrade($itemXML).".png\" alt=\"Item grade\" align=\"top\" /> ".$items->getName($itemXML)." ".((($items->getEnchantable($itemXML))) ? "<b>(+".$auction['itemEnchant'].")</b>" : "").$type1.$auction['value1'].$type2.$auction['value2'].$type3.$auction['value3']."
					</td>
					<td class=\"tableCount\">
						x".number_format($auction['itemCount'])."
					</td>
					<td class=\"tableBid ui-state-active\">
						".(($auction['highestBidder'] == $player['account']) ? "<span class=\"green\">YOU'RE HIGHEST BIDDER<br /><b>".number_format($auction['highestBid'],2)."</b></span>" : "PRICE<br /><b>".number_format($auction['highestBid'],2)."")."</b><br />
					</td>
					<td class=\"tableEnd ui-state-highlight\">
						".date("d M\, Y H:i:s e",$auction['endingTime'])."<br /><b>TIME LEFT <span class=\"auctionTime\" value=\"".($auction['endingTime']-time()-2)."\">".formatTime($auction['endingTime']-time()-1)."</span></b>
					</td>
					<td class=\"tablePurchase ui-state-hover\">";

			if ($auction['highestBidder'] == $player['account'])
						$subContent .= "<a href=\"javascript:location.reload(true);\" value=\"".$auction['id']."\">Refresh</a>";
			else{

			


		if ($auction['character_id'] == $player['character']['id']){
			
			if (!isset($auction['highestBidder']))
				
			$subContent .= "<a href=\"claimItem2\" value=\"".$auction['id']."\">Claim</a>";
			
			
		}
			else
				
			//$subContent .= "<a href=\"placeBid\" value=\"".$auction['id']."\">Buy</a>";
						$subContent .=		"<a href=\"purchaseItem1\" value=\"".$auction['id']."\">Buy now</a>";

		
			
			}

				
					
					$subContent .= "</td>
				</tr>";
				
				
				
				
				
				
				
				
				
			}
			}
		}
			$subContent .= "</table>
		</div>
		
		<div class=\"ui-widget-header widget-header\">Finished sales</div>
		<div class=\"ui-widget-content widget-content\">
			<table class=\"shopAuctions\">";

		if (!$finishedAuctions)
			$subContent .= "There's currently no finished sales.";
		
		
		else
		{
			foreach ($finishedAuctions as $auction)
			{
				$itemXML = $items->findItemInXML($auction['itemId']-200000);
				
				
				$subContent .= "<tr class=\"ui-state-default\">
					<td class=\"tableIcon\">
						<img src=\"images/itemIcon.php?item=".$items->getIcon($itemXML).(($items->getEnchantable($itemXML)) ? "&enchant_level=".$auction['itemEnchant'] : "")."\" alt=\"Item icon\" align=\"top\" />
					</td>
					<td>
						<img src=\"images/itemGradeIcons/grd_".$items->getGrade($itemXML).".png\" alt=\"Item grade\" align=\"top\" /> ".$items->getName($itemXML)." ".((($items->getEnchantable($itemXML))) ? "<b>(+".$auction['itemEnchant'].")</b>" : "")."
					</td>
					<td class=\"tableCount\">
						x".number_format($auction['itemCount'])."
					</td>";

			
					
					if ($auction['highestBidder'] == $player['account'])
						$subContent .= "<td class=\"tableBid ui-state-active\">
							<span class=\"green\">YOUR WINNING BID<br /><b>".number_format($auction['highestBid'],8)."</b></span>
						</td>";
					else
						$subContent .= "<td class=\"tableBid ui-state-active\">
							WINNING BID<br /><b>".number_format($auction['highestBid'],8)."</b>
						</td>";
					$subContent .= "<td class=\"tableEnd ui-state-highlight\">
						".date("d M\, Y H:i:s e",$auction['endingTime'])."<br /><b>FINISHED</b>
					</td>";
					
					
				if ($auction['character_id'] != $player['character']['id']){
						
					
					
					
					
					if ($auction['highestBidder'] == $player['account'] && $auction['claimed'] == 0)
						$subContent .= "<td class=\"tablePurchase ui-state-hover\">
							<a href=\"claimItem\" value=\"".$auction['id']."\">Claim</a>
						</td>";
					else
						$subContent .= "<td class=\"tablePurchase ui-widget-header\">
							
						</td>";
						
					}
					
					else {
						
						if ($auction['get_money'] == 0 && isset($auction['highestBidder']))
						
						$subContent .= "<td class=\"tablePurchase ui-state-hover\">
						<a href=\"getmoney\" value=\"".$auction['id']."\">GET MONEY</a>
						</td>";
						else if (!isset($auction['highestBidder']))
						$subContent .= "<td class=\"tablePurchase ui-state-hover\">
							<a href=\"claimItem2\" value=\"".$auction['id']."\">Claim</a>
						</td>";
					}
						
					
				
				$subContent .= "</tr>";
			}
		}
		
		

		
		
			$subContent .= "</table>
		</div>";
	}
		
	$templateContent->replace("items",$subContent);
	
?>
