<?php
	$templateSubContent = new template;
	$templateSubContent->load(PATH_TEMPLATES."playerDetails.html");
	
	$subContent = "";
	
	if (!!$config['enableItemShop'])
		$subContent .= "<button class=\"redirect\" value=\"?page=itemShop\">Market</button>";
	if (!!$config['enableItemAuction'])
		$subContent .= "<button class=\"redirect\" value=\"?page=marketplace\">MarketPlace</button>";
	if (!!$config['enableItemEnchanter'])
		$subContent .= "<button class=\"redirect\" value=\"?page=itemEnchanter\">Item Enchanter</button>";
	if (!!$config['enableCharacterServices'])
		$subContent .= "<button class=\"redirect\" value=\"?page=characterServices\">Character Services</button>";
	$subContent .= "<br />";
	if (!!$settings['enableCreditPurchase'])
		$subContent .= "<button class=\"redirect small\" value=\"?page=credits\">Donation</button>";
	if (!!$settings['enableVoteSystem'])
		$subContent .= "<button class=\"redirect small\" value=\"?page=marketSystem\">Sell</button>";
	if (!!$settings['enableInvitationSystem'])
		$subContent .= "<button class=\"redirect small\" value=\"?page=invitationSystem\">Invitation System</button>";
	if (!!$settings['enablePasswordChanging'])
		$subContent .= "<button class=\"changePassword small\">Change Password</button>";
	
	$templateSubContent->replace("navigation",$subContent);
	
	$templateSubContent->replace("serverName",$config['name']);
	$templateSubContent->replace("accountName",$player['account']);
	
	if (!$validate->blank($player['character']['id']))
		$templateSubContent->replace("character","<a href=\"?page=character\">Select your character</a>");
	else
		$templateSubContent->replace("character",(($player['character']['online']) ? "<img src=\"images/status/online.png\" alt=\"Online\" title=\"Character is Online\" />" : "<img src=\"images/status/offline.png\" alt=\"Offline\" title=\"Character is Offline\" />")." ".$player['character']['name']." (<a class=\"process\" href=\"changeCharacter\">change</a>)");
		
	/*$templateSubContent->replace("credits",$player['balance']);*/
	$tokens=0;
	$tokensData = $mysqlClientLoginServer->select("SELECT * from envios_wallet where account=?;",array($player['account']));
		if (!$tokensData)
			$templateSubContent->replace("tokens",'0');
		else{
				$count = $tokensData["count"];
				$templateSubContent->replace("tokens",$count);
		}
		
	$busd=0;
	$busdData = $mysqlClientLoginServer->select("SELECT * from envios_wallet_tickets where account=?;",array($player['account']));
		if (!$busdData)
			$templateSubContent->replace("busd",'0');
		else{
				$count = $busdData["count"];
				$templateSubContent->replace("busd",$count/100);
		}
