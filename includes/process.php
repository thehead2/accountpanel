<?php
	require_once("../core.php");
	
	
	
	
	if ($session->set("request") && $session->get("request")+1 > time())
		exit("RESPONSE^ERROR^Flood protector has been activated. Please try again...");
		
		
	$session->add("request",time());
	
	if (!isset($_GET['action']))
		exit;

	
	//mail("asdm@hotmail.com","nada",$_GET['action'],"l2l2mediavida.com");
	if ($_GET['action'] == "termsAndConditions")
	{
		if ($player['termsAndConditions'] == 1)
			exit("REFRESH");
		
		if (!$validate->blank(@$_GET['response']))
			exit("REFRESH");
		
		if ($_GET['response'] == "agree")
		{
			$player['termsAndConditions'] = 1;
			updateSession();
		}

		exit("REDIRECT^?page=account");
	}
	
	else if ($_GET['action'] == "register")
	{
		if (!!!$settings['enableRegistration'])
			exit("REFRESH");
			
		if ($player['termsAndConditions'] != 1 || $validate->blank($player['account']))
			exit("REFRESH");
		
		if (!$validate->blank(@$_GET['account']) || !$validate->blank(@$_GET['email']) || !$validate->blank(@$_GET['password']) || !$validate->blank(@$_GET['repeatPassword']))
			exit("RESPONSE^ERROR^Please fill in all the fields.");
		else if (!$validate->account($_GET['account']) || !$validate->email($_GET['email']) || !$validate->password($_GET['password']) || !$validate->password($_GET['repeatPassword']))
			exit("RESPONSE^ERROR^Some of the fields contain fobidden symbols. Only latin letters/numbers are acceptable.");
		else if (!$validate->match($_GET['password'],$_GET['repeatPassword']))
			exit("RESPONSE^ERROR^Both passwords do not match.");
		if(strlen(@$_GET['account'])>=15)
		{
			exit("RESPONSE^ERROR^Please choose account name less for 15 characteres.");
		}
			
		$accountData = $mysqlClientLoginServer2->select($queryLogin['checkAccount'],array($_GET['account'],$_GET['email']));
		
		if ($accountData)
		{
			if ($accountData[0] == $_GET['account'])
				exit("RESPONSE^ERROR^Such Account name already exists.");
			else if ($accountData[1] == $_GET['email'])
				exit("RESPONSE^ERROR^Such E-Mail address is already in use.");
		}
		$characterNameData = $mysqlClientLoginServer2->select($queryLogin['checkReferido'],array($_GET['referido']));
		if($_GET['referido']!="" && !$characterNameData)
		{
			exit("RESPONSE^ERROR^Such Character name not exist.");
		}
		//$mysqlClientLoginServer2->execute($queryLogin['createAccount'],array($_GET['account'],base64_encode(pack("H*", sha1(utf8_encode($_GET['password'])))),$_GET['email'],0,$_SERVER['REMOTE_ADDR']));
		$mysqlClientLoginServer2->execute($queryLogin['createAccount'],array($_GET['account'],$_GET['password'],$_GET['email'],0,$_SERVER['REMOTE_ADDR']));
		
		if($_GET['referido']!="")
			$mysqlClientLoginServer2->execute($queryLogin['referido'],array($_GET['referido'],$_GET['account']));

		exit("RESPONSE^SUCCESS^Account has been created. You may now connect to the server.");
	}
	else if ($_GET['action'] == "login")
	{
		
		if ($player['termsAndConditions'] != 1 || $validate->blank($player['account']))
			exit("REFRESH");
		
		if (!$validate->blank(@$_GET['account']) || !$validate->blank(@$_GET['password']))
				exit("RESPONSE^ERROR^Please fill in all the fields.");
		else if (!$validate->account($_GET['account']) || !$validate->password($_GET['password']))
			exit("RESPONSE^ERROR^Some of the fields contain fobidden symbols. Only latin letters/numbers are acceptable.");

		//$accountData = $mysqlClientLoginServer2->select($queryLogin['loginToAccount'],array($_GET['account'],base64_encode(pack("H*", sha1(utf8_encode($_GET['password']))))));
		$accountData = $mysqlClientLoginServer2->select($queryLogin['loginToAccount'],array($_GET['account'],$_GET['password']));
		if (!$accountData)
		{
			$accountData = $mysqlClientLoginServer2->select($queryLogin['loginToAccount'],array($_GET['account'],base64_encode(pack("H*", sha1(utf8_encode($_GET['password']))))));
			if (!$accountData)
				exit("RESPONSE^ERROR^Account or Password is incorrect.");
		}
		elseif ($accountData[0] == -1)
			exit("RESPONSE^ERROR^Account is not activated yet. Please check your email to verify your account...");
		
		$player['account'] = $accountData[0];
		
		
		
		$mysqlClientLoginServer->execute("UPDATE `acp_sessions` SET `account` = NULL, `characterId` = NULL, `server` = NULL WHERE `account` = ?;",array($player['account']));
		
		updateSession();
		
		$log->add("Logged in from: ".$_SERVER['REMOTE_ADDR'].".");
		
		exit("REDIRECT^?page=server");
	}
	else if ($_GET['action'] == "changeName")
	{
			
		if ($player['termsAndConditions'] != 1 || !$validate->blank($player['account']) || !$validate->blank($player['server']))
			exit("REFRESH");
		if (!$validate->blank($player['character']['id']))
			exit("RESPONSE^ERROR^Please choose a character first.");
		if (!$validate->blank(@$_GET['name']))
			exit("RESPONSE^ERROR^Please fill in all the fields.");
		$accountData = $mysqlClientLoginServer2->select("SELECT char_name from characters WHERE char_name  =?",array($_GET['name']));
		if ($accountData)
			exit("RESPONSE^ERROR^The name already exist.");
		exit("changeName^".$_GET['name']);
		//$mysqlClientLoginServer2->execute("UPDATE characters SET char_name=? where obj_Id=? ",array($_GET['name'],$player['character']['id']));
		//$log->add("Changed your name.");
		
		//exit("RESPONSE^SUCCESS^Your name has been successfully changed.");
	}
	else if ($_GET['action'] == "changePassword")
	{
		
		
		if (!!!$settings['enablePasswordChanging'])
			exit("REFRESH");
			
		if ($player['termsAndConditions'] != 1 || !$validate->blank($player['account']) || !$validate->blank($player['server']))
			exit("REFRESH");
		
		if (!$validate->blank(@$_GET['oldPassword']) || !$validate->blank(@$_GET['password']) || !$validate->blank(@$_GET['repeatPassword']))
			exit("RESPONSE^ERROR^Please fill in all the fields.");
		else if (!$validate->password($_GET['oldPassword']) || !$validate->password($_GET['password']) || !$validate->password($_GET['repeatPassword']))
			exit("RESPONSE^ERROR^Some of the fields contain fobidden symbols. Only latin letters/numbers are acceptable.");
		else if (!$validate->match($_GET['password'],$_GET['repeatPassword']))
			exit("RESPONSE^ERROR^Both passwords do not match.");
		else if ($validate->match($_GET['oldPassword'],$_GET['password']))
			exit("RESPONSE^ERROR^New password cannot be the same as the old one.");
		//exit("RESPONSE^ERROR^.".$_GET['oldPassword']);
		//$accountData = $mysqlClientLoginServer2->select($queryLogin['loginToAccount'],array($player['account'],base64_encode(pack("H*", sha1(utf8_encode($_GET['oldPassword']))))));
		$accountData = $mysqlClientLoginServer2->select($queryLogin['loginToAccount'],array($player['account'],$_GET['oldPassword']));
		if (!$accountData)
			exit("RESPONSE^ERROR^Old password is incorrect.");
		//$log->add("try change pass.");

		//$mysqlClientLoginServer2->execute($queryLogin['changePassword'],array(base64_encode(pack("H*", sha1(utf8_encode($_GET['password'])))),$player['account']));
		$mysqlClientLoginServer2->execute($queryLogin['changePassword'],array($_GET['password'],$player['account']));
		$log->add("Changed your password.");
		
		exit("RESPONSE^SUCCESS^Your password has been successfully changed.");
	}
	else if ($_GET['action'] == "recoverPassword")
	{
		if (!!!$settings['enablePasswordRecovery'])
			exit("REFRESH");
			
		if ($player['termsAndConditions'] != 1 || $validate->blank($player['account']))
			exit("REFRESH");
		
		if (!$validate->blank(@$_GET['account']) || !$validate->blank(@$_GET['email']))
			exit("RESPONSE^ERROR^Please fill in all the fields.");
		else if (!$validate->account($_GET['account']) || !$validate->email($_GET['email']))
			exit("RESPONSE^ERROR^Some of the fields contain fobidden symbols. Only latin letters/numbers are acceptable.");
			
		$accountData = $mysqlClientLoginServer2->select($queryLogin['checkAccount'],array($_GET['account'],$_GET['email']));
		
		if (!$accountData)
			exit("RESPONSE^ERROR^There's no such Account or E-Mail address.");
		else if ($accountData[0] != $_GET['account'] || $accountData[1] != $_GET['email'])
			exit("RESPONSE^ERROR^Account or E-Mail address is incorrect.");
		
		$mysqlClientLoginServer->execute($queryLogin['createAccount'],array($_GET['account'],base64_encode(pack("H*", sha1(utf8_encode($_GET['password'])))),$_GET['email'],0,$_SERVER['REMOTE_ADDR']));
		//$mysqlClientLoginServer->execute($queryLogin['createAccount'],array($_GET['account'],base64_encode(pack("H*", sha1(utf8_encode($_GET['password'])))),$_GET['email'],0,$_SERVER['REMOTE_ADDR']));

		$log->add("Recovered your password.");
		
		exit("RESPONSE^SUCCESS^An email with instructions on how to reset your password has been sent to your E-Mail address.");
	}
	else if ($_GET['action'] == "updateEmail")
	{
		if ($player['termsAndConditions'] != 1 || !$validate->blank($player['account']) || $validate->blank($player['email']))
			exit("REFRESH");
		
		if (!$validate->blank(@$_GET['email']))
			exit("RESPONSE^ERROR^Please fill in all the fields.");
		else if (!$validate->email($_GET['email']))
			exit("RESPONSE^ERROR^Some of the fields contain fobidden symbols. Only latin letters/numbers are acceptable.");
			
		$emailData = $mysqlClientLoginServer2->select($queryLogin['checkEmail'],array($_GET['email']));
		
		if ($emailData)
			exit("RESPONSE^ERROR^Such E-Mail address is already in use.");
		
		$mysqlClientLoginServer2->execute($queryLogin['updateEmail'],array($_GET['email'],$player['account']));
		exit("REDIRECT^?page=server");
	}
	else if ($_GET['action'] == "server")
	{
		if ($player['termsAndConditions'] != 1 || !$validate->blank($player['account']) || $validate->blank($player['server']))
			exit("REFRESH");
			
		if (!$validate->blank(@$_GET['id']))
			exit("RESPONSE^ERROR^Please fill in all the fields.");
		else if (!$validate->gameserver($_GET['id']))
			exit("RESPONSE^ERROR^Some of the fields contain fobidden symbols.");
		else if (!$mysqlClientLoginServer->select("SELECT `id` FROM `acp_servers` WHERE `id` = ?;",array($_GET['id'])))
			exit("REFRESH");
			
		$player['server'] = $_GET['id'];
		updateSession();
			
		exit("REDIRECT^?page=character");
	}
	else if ($_GET['action'] == "character")
	{
		if ($player['termsAndConditions'] != 1 || !$validate->blank($player['account']) || !$validate->blank($player['server']) || $validate->blank($player['character']['id']))
			exit("REFRESH");
			
		if (!$validate->blank(@$_GET['id']))
			exit("RESPONSE^ERROR^Please fill in all the fields.");
		else if (!$validate->character($_GET['id']))
			exit("RESPONSE^ERROR^Some of the fields contain fobidden symbols.");
		else if (!$mysqlClientGameServer->select($queryGame['selectCharacterData'],array($player['account'],$_GET['id'])))
			exit("REFRESH");
			
		$player['character']['id'] = $_GET['id'];
		updateSession();
			
		exit("REDIRECT^?page=player");
	}
	else if ($_GET['action'] == "changeServer")
	{
		if($validate->blank($player['server']))
		{
			$player['server'] = NULL;
			$player['character']['id'] = NULL;
			updateSession();
		}
			
		exit("REDIRECT^?page=server");
	}
	else if ($_GET['action'] == "logout")
	{
		if ($validate->blank($player['account']))
			clearSession();
			
		exit("REDIRECT^?page=account");
	}
	else if ($_GET['action'] == "changeCharacter")
	{
		if($validate->blank($player['character']['id']))
		{
			$player['character']['id'] = NULL;
			updateSession();
		}
			
		exit("REDIRECT^?page=character");
	}
	else if ($_GET['action'] == "purchaseItem")
	{
		if (!!!$config['enableItemShop'])
			exit("REFRESH");
			
		if ($player['termsAndConditions'] != 1 || !$validate->blank($player['account']) || !$validate->blank($player['server']))
			exit("REFRESH");
		if (!$validate->blank(@$_GET['id']))
			exit("RESPONSE^ERROR^Please fill in all the fields.");		
		else if (!$validate->numeric($_GET['id']))
			exit("RESPONSE^ERROR^Some of the fields contain fobidden symbols.");
		if(!$validate->blank($player['character']['id']))
			exit("RESPONSE^ERROR^Please <a href=\"?page=character\">select your character</a> first.");


			
		$itemData = $mysqlClientLoginServer->select("SELECT * FROM `acp_shop_items` WHERE `serverId` = ? AND `id` = ?;",array($player['server'],$_GET['id']));
		
		if (!$itemData)
			exit("REFRESH");
			
		if ($itemData['setId'] > 0)
			exit("REFRESH");

		$mysqlClientLoginServer->execute("INSERT INTO acp_auctions_secundario (id,serverId,itemId,itemCount,itemEnchant,startingTime,startingBid,character_id, account) VALUES ((SELECT MAX(id)+1 FROM acp_auctions_secundario AS id),1,?,?,?,?,?,?,?)",array($itemData['itemId'],$itemData['itemCount'],$itemData['itemEnchant'],time(),$itemData['price'],$player['character']['id'],$player['account']));
		exit($itemData["price"].",".$itemData["id"]);
	}
		else if ($_GET['action'] == "purchaseItem1")
	{
		if (!!!$config['enableItemShop'])
			exit("REFRESH");
			
		if ($player['termsAndConditions'] != 1 || !$validate->blank($player['account']) || !$validate->blank($player['server']))
			exit("REFRESH");
			
		if (!$validate->blank($player['character']['id']))
			exit("RESPONSE^ERROR^Please <a href=\"?page=character\">select your character</a> first.");
			
		//$logeando = $mysqlClientLoginServer->select("SELECT * FROM logeando WHERE id = ?",array($player['character']['id']));
		
		/*if (!!!$config['enableTelnet'] && ($player['character']['online'] || $logeando))
			exit("RESPONSE^ERROR^Please logout from the game in order to use this function.");*/
			
		if (!$validate->blank(@$_GET['id']))
			exit("RESPONSE^ERROR^Please fill in all the fields.");
		else if (!$validate->numeric($_GET['id']))
			exit("RESPONSE^ERROR^Some of the fields contain fobidden symbols.");
			
			
		
		$auctionData = $mysqlClientLoginServer->select("SELECT *,coalesce(`highest_bidder`.`bidAmount`,`acp_auctions`.`startingBid`) AS `highestBid` FROM `acp_auctions` LEFT JOIN (SELECT `auctionId`,`account` AS `highestBidder` ,`bidAmount` FROM `acp_auction_bids` ORDER BY `bidAmount` DESC) `highest_bidder` ON `acp_auctions`.`id` = `highest_bidder`.`auctionId` WHERE `serverId` = ? AND `id` = ? GROUP BY `acp_auctions`.`id`;",array($player['server'],$_GET['id']));
		//$auctionData = $mysqlClientLoginServer->select("SELECT * FROM `acp_auctions` WHERE `serverId` = ? AND `id` = ?;",array($player['server'],$_GET['id']));
		
		
		if (!$auctionData)
			exit("REFRESH");
		
		if ($auctionData['account'] == $player['account'])
			exit("RESPONSE^ERROR^You're already the seller.");
		
		if ($auctionData['endingTime'] < time() || $auctionData['claimed'] == 1)
			exit("RESPONSE^ERROR^The sell for this item has already ended. Please refresh the page.");
		
		//$mysqlClientLoginServer->execute("REPLACE INTO login_bloqueado (id) VALUES (?)",array($player['character']['id']));
		
		$mysqlClientLoginServer->execute("INSERT INTO acp_auctions_secundario (id,serverId,itemId,itemCount,itemEnchant,startingTime,startingBid,character_id, account, type1, value1, type2, value2, type3, value3,id_original) VALUES ((SELECT MAX(id)+1 FROM acp_auctions_secundario AS id),1,?,?,?,?,?,?,?,?,?,?,?,?,?,?)",array($auctionData['itemId'],$auctionData['itemCount'],$auctionData['itemEnchant'],time(),$auctionData['startingBid'],$player['character']['id'],$auctionData['account'],$auctionData['type1'], $auctionData['value1'], $auctionData['type2'], $auctionData['value2'], $auctionData['type3'], $auctionData['value3'],$_GET['id']));

		
		exit($auctionData["account"].",".$auctionData["startingBid"].",".$auctionData["id"]);
		//exit();
		/*if ($auctionData['highestBidder'] == $player['account'])
			exit("RESPONSE^ERROR^You're already a highest bidder for this auction.");*/
			

			
		/*if ($auctionData['startingBid']+0.00001 > $_GET['bid'])
			exit("RESPONSE^ERROR^Minimum bid for this auction is ".number_format($auctionData['highestBid']+0.00001,2).".");
		
		if ($player['balance'] < $_GET['bid'])
			exit("RESPONSE^ERROR^You have insufficient funds to place such bid. <a href=\"?page=credits\">Get more tokens</a> and try again.");*/
	}
	else if ($_GET['action'] == "claimTokens")
	{
		
				/*$logeando = $mysqlClientLoginServer->select("SELECT * FROM logeando WHERE id = ?",array($player['character']['id']));
		
		if (!!!$config['enableTelnet'] && ($player['character']['online'] || $logeando))
			exit("RESPONSE^ERROR^Please logout from the game in order to use this function.");*/
		
		$tokensData = $mysqlClientLoginServer->select("SELECT * from envios_wallet where account=?;",array($player['account']));
		if (!$tokensData)
			exit("REFRESH");
		$wallet = $tokensData["wallet"];
		$count = $tokensData["count"];
		if($count==0)
		{
			exit("RESPONSE^ERROR^You dont have enough tokens to claim.");
		}
		//$mysqlClientLoginServer->execute("REPLACE INTO envios_wallet (account,wallet,count) VALUES(?,?,?);",array($player['account'],$wallet,0));
		




		
		
		
		exit($wallet.",".$count);
		
	}
	else if ($_GET['action'] == "claimBusd")
	{
		
		
				/*$logeando = $mysqlClientLoginServer->select("SELECT * FROM logeando WHERE id = ?",array($player['character']['id']));
		
		if (!!!$config['enableTelnet'] && ($player['character']['online'] || $logeando))
			exit("RESPONSE^ERROR^Please logout from the game in order to use this function.");*/
		
		$tokensData = $mysqlClientLoginServer->select("SELECT * from envios_wallet_tickets where account=?;",array($player['account']));
		if (!$tokensData)
			exit("REFRESH");
		$wallet = $tokensData["wallet"];
		$count = $tokensData["count"];
		if($count==0)
		{
			exit("RESPONSE^ERROR^You dont have enough tokens to claim.");
		}
		//$mysqlClientLoginServer->execute("REPLACE INTO envios_wallet (account,wallet,count) VALUES(?,?,?);",array($player['account'],$wallet,0));
		




		
		
		
		exit($wallet.",".$count);
		
	}
	else if ($_GET['action'] == "claim_game")
	{
				$tokensData = $mysqlClientLoginServer->select("SELECT * from envios_wallet where account=?;",array($player['account']));
		if (!$tokensData)
			exit("REFRESH");
		$count = $tokensData["count"];
		if($count==0)
		{
			exit("RESPONSE^ERROR^You dont have enough tokens to claim.");
		}
		if (!$validate->blank($player['character']['id']))
			exit("RESPONSE^ERROR^Please <a href=\"?page=character\">select your character</a> first.");
		$mysqlClientLoginServer->execute("INSERT INTO market_items_buy VALUES (?,?,?,?)",array($player['character']['id'],57364,$count,0));
		$mysqlClientLoginServer->execute("UPDATE envios_wallet SET count=0 WHERE account=?;",array($player['account']));
		$log->add("Succesfully claimed tokens game ".$count);
		
		exit("RESPONSE^SUCCESS^Succesfully claimed tokens game ".$count);
	}
	
	else if ($_GET['action'] == "purchaseSet")
	{
		if (!!!$config['enableItemShop'])
			exit("REFRESH");
			
		if ($player['termsAndConditions'] != 1 || !$validate->blank($player['account']) || !$validate->blank($player['server']))
			exit("REFRESH");
			
		if (!$validate->blank($player['character']['id']))
			exit("RESPONSE^ERROR^Please <a href=\"?page=character\">select your character</a> first.");
			
		/*if (!!!$config['enableTelnet'] && $player['character']['online'])
			exit("RESPONSE^ERROR^Please logout from the game in order to use this function.");*/
			
		if (!$validate->blank(@$_GET['id']))
			exit("RESPONSE^ERROR^Please fill in all the fields.");
		else if (!$validate->numeric($_GET['id']))
			exit("RESPONSE^ERROR^Some of the fields contain fobidden symbols.");
	
		/*$setData = $mysqlClientLoginServer->select("SELECT * FROM `acp_shop_sets` WHERE `serverId` = ? AND `id` = ?;",array($player['server'],$_GET['id']));
			$logeando = $mysqlClientLoginServer->select("SELECT * FROM logeando WHERE id = ?",array($player['character']['id']));*/
		
		/*if (!!!$config['enableTelnet'] && ($player['character']['online'] || $logeando))
			exit("RESPONSE^ERROR^Please logout from the game in order to use this function.");	*/
		if (!$setData)
			exit("REFRESH");
		
		if ($player['balance'] < $setData['price'])
			exit("RESPONSE^ERROR^You have insufficient funds for this transaction. <a href=\"?page=credits\">Get more tokens</a> and try again.");
		
		if ($credits->reduce($setData['price']))
		{
			$itemsData = $mysqlClientLoginServer->select("SELECT * FROM `acp_shop_items` WHERE `serverId` = ? AND `setId` = ?;",array($player['server'],$_GET['id']));

			if (@$itemsData['id'])
				$setItems[0] = $itemsData;
			else
				$setItems = $itemsData;
				
			$enchantedItems = false;
			
			foreach ($setItems as $item)
			{
				if ($item['itemEnchant'] > 0)
				{
					$enchantedItems = true;
					break;
				}
			}
				
			foreach ($setItems as $item)
			{
				$itemXML = $items->findItemInXML($item['itemId']);
				$items->add($itemXML,$item['itemId'],$item['itemCount'],$item['itemEnchant'],$enchantedItems);
			}
			
			$log->add("Purchased ".$setData['name']." for ".sprintf('%0.2f',$setData['price']).".");

			exit("PURCHASED^".round($player['balance']-$setData['price'],2)."^Succesfully purchased ".$setData['name']." for ".sprintf('%0.2f',$setData['price']).".");
		}
	}
	else if ($_GET['action'] == "enchantItem")
	{
		if (!!!$config['enableItemEnchanter'])
			exit("REFRESH");
			
		if ($player['termsAndConditions'] != 1 || !$validate->blank($player['account']) || !$validate->blank($player['server']) || !$validate->blank($player['character']['id']))
			exit("REFRESH");
			
		if (!$validate->blank(@$_GET['id']))
			exit("RESPONSE^ERROR^Please fill in all the fields.");
		else if (!$validate->numeric($_GET['id']))
			exit("RESPONSE^ERROR^Some of the fields contain fobidden symbols.");
		else if ($player['character']['online'])
			exit("RESPONSE^ERROR^Please logout from the game in order to use this function.");
		$itemData = $mysqlClientGameServer->select3($queryGame['selectPlayerItemEnchantData'],array($_GET['id'],$player['character']['id']));


		if (!$itemData)
			exit("REFRESH");
		
		
		if ($itemData[0]['loc']!="INVENTORY"){
			
			if ($itemData[0]['loc']!="PAPERDOLL"){
				
				exit("RESPONSE^ERROR^The object must be in the inventory.");
				
			}
			
		} 
			
				$logeando = $mysqlClientLoginServer->select("SELECT * FROM logeando WHERE id = ?",array($player['character']['id']));
		
		if (!!!$config['enableTelnet'] && ($player['character']['online'] || $logeando))
			exit("RESPONSE^ERROR^Please logout from the game in order to use this function.");
			
		if ($itemData[0]['enchant_level'] >= $config['enchanterLimit'])
			exit("RESPONSE^ERROR^This item has reached the enchant limit of ".$config['enchanterLimit'].".");
			
		$itemXML = $items->findItemInXML($itemData[0]['item_id']);



		
		
		if ($items->getType($itemXML) == "Weapon")
			$enchantPrice = $config['enchanterWeaponPrice'];
		else if ($items->getType($itemXML) == "Armor")
			$enchantPrice = $config['enchanterArmorPrice'];
		else
			exit("REFRESH");
		

		
		if ($player['balance'] < $enchantPrice)
			exit("RESPONSE^ERROR^You have insufficient funds for this transaction. <a href=\"?page=credits\">Get more tokens</a> and try again.");
		
		if ($credits->reduce($enchantPrice))
		{


			
				$mysqlClientGameServer->execute2($queryGame['updateEnchantOnItem3'],array($itemData[0]['object_id']));

			
			
			
			
			$log->add("Purchased enchant on ".$items->getName($itemXML)." (from +".$itemData[0]['enchant_level']." to +".($itemData[0]['enchant_level']+1).") for ".sprintf('%0.2f',$enchantPrice).".");

			
			
			exit("ENCHANTED^".$_GET['id']."^".round($player['balance']-$enchantPrice,2)."^Succesfully enchanted ".$items->getName($itemXML)." (from +".$itemData[0]['enchant_level']." to +".($itemData[0]['enchant_level']+1).") for ".sprintf('%0.2f',$enchantPrice).".");
		
		}
	
	}

	
	
	
	else if ($_GET['action'] == "purchaseService")
	{
		if (!!!$config['enableCharacterServices'])
			exit("REFRESH");
			
		if ($player['termsAndConditions'] != 1 || !$validate->blank($player['account']) || !$validate->blank($player['server']))
			exit("REFRESH");
			
		if (!$validate->blank($player['character']['id']))
			exit("RESPONSE^ERROR^Please <a href=\"?page=character\">select your character</a> first.");
			
		if (!$validate->blank(@$_GET['id']))
			exit("RESPONSE^ERROR^Please fill in all the fields.");
		else if (!$validate->letters($_GET['id']))
			exit("RESPONSE^ERROR^Some of the fields contain fobidden symbols.");
		else if ($player['character']['online'])
			exit("RESPONSE^ERROR^Please logout from the game in order to use this function.");
				$logeando = $mysqlClientLoginServer->select("SELECT * FROM logeando WHERE id = ?",array($player['character']['id']));
		
		if (!!!$config['enableTelnet'] && ($player['character']['online'] || $logeando))
			exit("RESPONSE^ERROR^Please logout from the game in order to use this function.");
		if ($_GET['id'] == "unstuck")
		{
			if (!!!$config['enableServiceUnstuck'])
				exit("REFRESH");
			
			$price = $config['serviceUnstuckPrice'];
			
			$location = explode("^",$config['serviceUnstuckLocation']);
			$x = $location[0];
			$y = $location[1];
			$z = $location[2];
			
			if ($player['balance'] < $price)
				exit("RESPONSE^ERROR^You have insufficient funds for this transaction. <a href=\"?page=credits\">Get more tokens</a> and try again.");

			if ($credits->reduce($price))
			{
				$mysqlClientGameServer->execute($queryGame['updatePlayerLocation'],array($x,$y,$z,$player['character']['id']));
				
				$log->add("Purchased service \"Return character to town\" for ".sprintf('%0.2f',$price).".");
				
				exit("PURCHASED^".round($player['balance']-$price,2)."^Succesfully returned the character to town for ".sprintf('%0.2f',$price).".");
			}
		}
		else if ($_GET['id'] == "unjail")
		{
			if (!!!$config['enableServiceUnjail'])
				exit("REFRESH");
			
			$price = $config['serviceUnjailPrice'];
			
			if ($player['balance'] < $price)
				exit("RESPONSE^ERROR^You have insufficient funds for this transaction. <a href=\"?page=credits\">Get more tokens</a> and try again.");

			if ($credits->reduce($price))
			{
				$mysqlClientGameServer->execute($queryGame['updatePlayerJail'],array($player['character']['id']));
				
				$location = explode("^",$config['serviceUnstuckLocation']);
				$x = $location[0];
				$y = $location[1];
				$z = $location[2];

				$mysqlClientGameServer->execute($queryGame['updatePlayerLocation'],array($x,$y,$z,$player['character']['id']));
				
				$log->add("Purchased service \"Unjail\" for ".sprintf('%0.2f',$price).".");
				
				exit("PURCHASED^".round($player['balance']-$price,2)."^Succesfully unjailed for ".sprintf('%0.2f',$price).".");
			}
		}
		else if ($_GET['id'] == "unban")
		{
			if (!!!$config['enableServiceUnban'])
				exit("REFRESH");
			
			$price = $config['serviceUnbanPrice'];
			
			if ($player['balance'] < $price)
				exit("RESPONSE^ERROR^You have insufficient funds for this transaction. <a href=\"?page=credits\">Get more tokens</a> and try again.");

			if ($credits->reduce($price))
			{
				$mysqlClientLoginServer->execute($queryLogin['updatePlayerBan'],array($player['account']));
				
				$log->add("Purchased service \"Unban\" for ".sprintf('%0.2f',$price).".");
				
				exit("PURCHASED^".round($player['balance']-$price,2)."^Succesfully unbanned for ".sprintf('%0.2f',$price).".");
			}
		}
		else if ($_GET['id'] == "noblesse")
		{
			if (!!!$config['enableServiceNoblesse'])
				exit("REFRESH");
			
			$price = $config['serviceNoblessePrice'];
			
			if ($player['balance'] < $price)
				exit("RESPONSE^ERROR^You have insufficient funds for this transaction. <a href=\"?page=credits\">Get more tokens</a> and try again.");

			if ($credits->reduce($price))
			{
				$itemXML = $items->findItemInXML(7694);
				$items->add($itemXML,7694,1,0,false);
				
				$mysqlClientGameServer->execute($queryGame['updatePlayerNoblesse'],array($player['character']['id']));
				
				$log->add("Purchased service \"Noblesse\" for ".sprintf('%0.2f',$price).".");
				
				exit("PURCHASED^".round($player['balance']-$price,2)."^Succesfully purchased noblesse for ".sprintf('%0.2f',$price).".");
			}
		}
		else if ($_GET['id'] == "cleanseKarma")
		{
			//exit("REFRESH");
			if (!!!$config['enableServiceCleanseKarma'])
				exit("REFRESH");
			
			$karmaData = $mysqlClientLoginServer->select("SELECT karma FROM characters WHERE obj_Id=?;",array($player['character']['id']));

		
		
			if (!$karmaData)
				exit("REFRESH");
			
			$karma = $karmaData['karma'];
				

			
			$karma = $karmaData['karma'];
			if ($karma == 0)
				exit("RESPONSE^ERROR^You dont have karma.");
			$price = intval($karma/1000)+1;
				
			$mysqlClientLoginServer->execute("REPLACE INTO login_bloqueado (id) VALUES (?)",array($player['character']['id']));

			exit($price.",1");
			
			/*if ($player['balance'] < $price)
				exit("RESPONSE^ERROR^You have insufficient funds for this transaction. <a href=\"?page=credits\">Get more tokens</a> and try again.");

			if ($credits->reduce($price))
			{
				$mysqlClientGameServer->execute($queryGame['removePlayerKarma'],array($player['character']['id']));
				
				$log->add("Purchased service \"Cleanse Karma\" for ".sprintf('%0.2f',$price).".");
				
				exit("PURCHASED^".round($player['balance']-$price,2)."^Succesfully removed karma for ".sprintf('%0.2f',$price).".");
			}*/
		}
		else if ($_GET['id'] == "removePK")
		{
			if (!!!$config['enableServiceRemovePk'])
				exit("REFRESH");
			
			$price = $config['serviceRemovePkPrice'];
			
			if ($player['balance'] < $price)
				exit("RESPONSE^ERROR^You have insufficient funds for this transaction. <a href=\"?page=credits\">Get more tokens</a> and try again.");

			if ($credits->reduce($price))
			{
				$mysqlClientGameServer->execute($queryGame['removePlayerPK'],array($player['character']['id']));
				
				$log->add("Purchased service \"Remove PK\" for ".sprintf('%0.2f',$price).".");
				
				exit("PURCHASED^".round($player['balance']-$price,2)."^Succesfully removed PK for ".sprintf('%0.2f',$price).".");
			}
		}
		else if ($_GET['id'] == "changeSex")
		{
			//exit("REFRESH");
			if (!!!$config['enableServiceChangeSex'])
				exit("REFRESH");
			
			$price = $config['serviceChangeSexPrice'];
			
			/*if ($player['balance'] < $price)
				exit("RESPONSE^ERROR^You have insufficient funds for this transaction. <a href=\"?page=credits\">Get more tokens</a> and try again.");

			if ($credits->reduce($price))
			{
				$mysqlClientGameServer->execute($queryGame['updatePlayerSex'],array($player['character']['id']));
				
				$log->add("Purchased service \"Change Sex\" for ".sprintf('%0.2f',$price).".");
				
				exit("PURCHASED^".round($player['balance']-$price,2)."^Succesfully purchased sex change for ".sprintf('%0.2f',$price).".");
			}*/
			$mysqlClientLoginServer->execute("REPLACE INTO login_bloqueado (id) VALUES (?)",array($player['character']['id']));

			exit($price.",2");
		}
		else if ($_GET['id'] == "changeName")
		{
			$price = 3.00;
			
			/*if ($player['balance'] < $price)
				exit("RESPONSE^ERROR^You have insufficient funds for this transaction. <a href=\"?page=credits\">Get more tokens</a> and try again.");

			if ($credits->reduce($price))
			{
				$mysqlClientGameServer->execute($queryGame['updatePlayerSex'],array($player['character']['id']));
				
				$log->add("Purchased service \"Change Sex\" for ".sprintf('%0.2f',$price).".");
				
				exit("PURCHASED^".round($player['balance']-$price,2)."^Succesfully purchased sex change for ".sprintf('%0.2f',$price).".");
			}*/
			$mysqlClientLoginServer->execute("REPLACE INTO login_bloqueado (id) VALUES (?)",array($player['character']['id']));

			exit($price.",3");
		}		
	}
	else if ($_GET['action'] == "placeBid")
	{
		if (!!!$config['enableItemAuction'])
			exit("REFRESH");
			
		if ($player['termsAndConditions'] != 1 || !$validate->blank($player['account']) || !$validate->blank($player['server']))
			exit("REFRESH");
			
		if (!$validate->blank(@$_GET['id']) || !$validate->blank(@$_GET['bid']))
			exit("RESPONSE^ERROR^Please fill in all the fields.");
		else if (!$validate->numeric($_GET['id']) || !$validate->money($_GET['bid']))
			exit("RESPONSE^ERROR^Some of the fields contain fobidden symbols.");
			
		$auctionData = $mysqlClientLoginServer->select("SELECT *,coalesce(`highest_bidder`.`bidAmount`,`acp_auctions`.`startingBid`) AS `highestBid` FROM `acp_auctions` LEFT JOIN (SELECT `auctionId`,`account` AS `highestBidder` ,`bidAmount` FROM `acp_auction_bids` ORDER BY `bidAmount` DESC) `highest_bidder` ON `acp_auctions`.`id` = `highest_bidder`.`auctionId` WHERE `serverId` = ? AND `id` = ? GROUP BY `acp_auctions`.`id`;",array($player['server'],$_GET['id']));

		
		
		if (!$auctionData)
			exit("REFRESH");
			
		if ($auctionData['highestBidder'] == $player['account'])
			exit("RESPONSE^ERROR^You're already a highest bidder for this auction.");
			
		if ($auctionData['endingTime'] < time() || $auctionData['claimed'] == 1)
			exit("RESPONSE^ERROR^The auction for this item has already ended. Please refresh the page.");
			
		if ($auctionData['highestBid']+0.00001 > $_GET['bid'])
			exit("RESPONSE^ERROR^Minimum bid for this auction is ".number_format($auctionData['highestBid']+0.00001,2).".");
		
		if ($player['balance'] < $_GET['bid'])
			exit("RESPONSE^ERROR^You have insufficient funds to place such bid. <a href=\"?page=credits\">Get more tokens</a> and try again.");
		
		if ($credits->reduce($_GET['bid']))
		{
			$mysqlClientLoginServer->execute("UPDATE `acp_players` LEFT JOIN `acp_auction_bids` ON `acp_auction_bids`.`account` = `acp_players`.`account` SET `acp_players`.`balance` = `acp_players`.`balance`+`acp_auction_bids`.`bidAmount` WHERE `acp_auction_bids`.`auctionId` = ? AND `acp_auction_bids`.`refunded` = 0;",array($_GET['id']));
			$mysqlClientLoginServer->execute("DELETE FROM `acp_auction_bids` WHERE `auctionId` = ?;",array($_GET['id']));
			$mysqlClientLoginServer->execute("INSERT INTO `acp_auction_bids` (`auctionId`,`account`,`bidAmount`,`bidTime`) VALUES (?,?,?,?);",array($_GET['id'],$player['account'],$_GET['bid'],time()));
		
			

			
			
			
			
			$log->add("Bid ".sprintf('%0.2f',$_GET['bid'])." in the auction.");
			
			exit("REFRESH");
		}
	}
	
	else if ($_GET['action'] == "sellprice")
	{
		
		if (!$validate->blank(@$_GET['id']) || !$validate->blank(@$_GET['price']))
			exit("RESPONSE^ERROR^Please fill in all the fields.");
		
		
		
		else if (!$validate->numeric($_GET['id']) || !$validate->money($_GET['price']))
			exit("RESPONSE^ERROR^Some of the fields contain fobidden symbols.");
		
		if ($player['termsAndConditions'] != 1 || !$validate->blank($player['account']) || !$validate->blank($player['server']))
			exit("REFRESH");

		
		if ($player['character']['online'])
			exit("RESPONSE^ERROR^Please logout from the game in order to use this function.");	
			
				$logeando = $mysqlClientLoginServer->select("SELECT * FROM logeando WHERE id = ?",array($player['character']['id']));
		
		if (!!!$config['enableTelnet'] && ($player['character']['online'] || $logeando))
			exit("RESPONSE^ERROR^Please logout from the game in order to use this function.");

		
		
	
		
		
		$itemData = $mysqlClientGameServer->select3($queryGame['selectPlayerItemEnchantData'],array($_GET['id'],$player['character']['id']));

		
		if($items->getAugmentation($itemData[0]))
		

			exit("REFRESH");
		

		
		
		
		if (!$itemData)
			exit("REFRESH");
//exit("RESPONSE^ERROR^The object must be in the inventory.");
		
		if ($itemData[0]['loc']!="INVENTORY"){
			
			if ($itemData[0]['loc']!="PAPERDOLL"){
				
				exit("RESPONSE^ERROR^The object must be in the inventory.");
				
			}
			
		} 

		$itemXML = $items->findItemInXML($itemData[0]['item_id']-200000);
		if($itemXML==null)
		{
			exit("RESPONSE^ERROR^The object dont exist.");
		}







		

			
		$mysqlClientGameServer->execute($queryGame['updateEnchantOnItem'],array($itemData[0]['object_id']));
			
		
		
		
	
			
		$elementData = $mysqlClientGameServer->select2($queryGame['element'],array($itemData[0]['object_id']));
		
		//$file = fopen("archivo.txt", "w");
		
		//fwrite($file, $elementData[0] . PHP_EOL);
		
		
		if(count($elementData)==1)
			$mysqlClientGameServer->execute2("INSERT INTO `acp_auctions` (`id`,`serverId`,`itemId`,`itemCount`,`itemEnchant`,`endingTime`,`startingBid`,`character_id`,`type1`,`value1`, `account`) VALUES ((SELECT MAX(id)+1 FROM acp_auctions AS id),1,?,?,?,?,?,?,?,?,?)",array($itemData[0]['item_id'],$itemData[0]['count'],$itemData[0]['enchant_level'],time()+(86400),$_GET['price'],$player['character']['id'],$elementData[0]['elemType'],$elementData[0]['elemValue'],$player['account']));
		else if(count($elementData)==2)
			$mysqlClientGameServer->execute2("INSERT INTO `acp_auctions` (`id`,`serverId`,`itemId`,`itemCount`,`itemEnchant`,`endingTime`,`startingBid`,`character_id`,`type1`,`value1`,`type2`,`value2`, `account`) VALUES ((SELECT MAX(id)+1 FROM acp_auctions AS id),1,?,?,?,?,?,?,?,?,?,?,?)",array($itemData[0]['item_id'],$itemData[0]['count'],$itemData[0]['enchant_level'],time()+(86400),$_GET['price'],$player['character']['id'],$elementData[0]['elemType'],$elementData[0]['elemValue'],$elementData[1]['elemType'],$elementData[1]['elemValue'],$player['account']));
		else if(count($elementData)==3)
			$mysqlClientGameServer->execute2("INSERT INTO `acp_auctions` (`id`,`serverId`,`itemId`,`itemCount`,`itemEnchant`,`endingTime`,`startingBid`,`character_id`,`type1`,`value1`,`type2`,`value2`,`type3`,`value3`, `account`) VALUES ((SELECT MAX(id)+1 FROM acp_auctions AS id),1,?,?,?,?,?,?,?,?,?,?,?,?,?)",array($itemData[0]['item_id'],$itemData[0]['count'],$itemData[0]['enchant_level'],time()+(86400),$_GET['price'],$player['character']['id'],$elementData[0]['elemType'],$elementData[0]['elemValue'],$elementData[1]['elemType'],$elementData[1]['elemValue'],$elementData[2]['elemType'],$elementData[2]['elemValue'],$player['account']));
		else if(count($elementData)==0)
			$mysqlClientGameServer->execute2("INSERT INTO `acp_auctions` (`id`,`serverId`,`itemId`,`itemCount`,`itemEnchant`,`endingTime`,`startingBid`,`character_id`, `account`) VALUES ((SELECT MAX(id)+1 FROM acp_auctions AS id),1,?,?,?,?,?,?,?)",array($itemData[0]['item_id'],$itemData[0]['count'],$itemData[0]['enchant_level'],time()+(86400),$_GET['price'],$player['character']['id'],$player['account']));




		//fclose($file);
		
	
		//$mysqlClientGameServer->execute($queryGame['element2'],array($itemData[0]['object_id']));
			
			
			$log->add("Han puesto a la venta  ".$items->getName($itemXML));

			
			//echo("llegamos al final");
		
					exit("RESPONSE^SUCCESS^The Shop item has been created.");


	
	}

	else if ($_GET['action'] == "claimItem")
	{
		
		

		
		if (!!!$config['enableItemAuction'])
			exit("REFRESH");
			
		/*if ($player['character']['online'])
			exit("RESPONSE^ERROR^Please logout from the game in order to use this function.");	*/
	
			
		if ($player['termsAndConditions'] != 1 || !$validate->blank($player['account']) || !$validate->blank($player['server']))
			exit("REFRESH");
			
	
			
		if (!$validate->blank($player['character']['id']))
			exit("RESPONSE^ERROR^Please <a href=\"?page=character\">select your character</a> first.");
			
	
			
		if (!$validate->blank(@$_GET['id']))
			exit("RESPONSE^ERROR^Please fill in all the fields.");
		else if (!$validate->numeric($_GET['id']))
			exit("RESPONSE^ERROR^Some of the fields contain fobidden symbols.");
			
	
			
		$auctionData = $mysqlClientLoginServer->select("SELECT *,coalesce(`highest_bidder`.`bidAmount`,`acp_auctions`.`startingBid`) AS `highestBid` FROM `acp_auctions` LEFT JOIN (SELECT `auctionId`,`account` AS `highestBidder` ,`bidAmount` FROM `acp_auction_bids` ORDER BY `bidAmount` DESC) `highest_bidder` ON `acp_auctions`.`id` = `highest_bidder`.`auctionId` WHERE `serverId` = ? AND `id` = ? GROUP BY `acp_auctions`.`id`;",array($player['server'],$_GET['id']));
		$auctionData1 = $mysqlClientLoginServer->select("SELECT * from acp_auctions_secundario WHERE id_original=?",array($_GET['id']));
		if($auctionData1)
		{
			exit("RESPONSE^ERROR^This item is in transact now.");
		}

			//$logeando = $mysqlClientLoginServer->select("SELECT * FROM logeando WHERE id = ?",array($player['character']['id']));
		
		/*if (!!!$config['enableTelnet'] && ($player['character']['online'] || $logeando))
			exit("RESPONSE^ERROR^Please logout from the game in order to use this function.");*/



	
	
			
	

		
		if (!$auctionData)
			exit("REFRESH");
			
			
		if 	($auctionData['character_id'] != $player['character']['id']){
			
		
			
		if ($auctionData['highestBidder'] != $player['account'])
			exit("RESPONSE^ERROR^You did not win this auction. Security breach alert?");
		
		if ($auctionData['endingTime'] > time())
			exit("RESPONSE^ERROR^Auction for this item is not over yet. Security breach alert?");
		
		}
		
		
		if ($auctionData['claimed'] == 1)
			exit("REFRESH");
		
		$mysqlClientLoginServer->execute("UPDATE `acp_auctions` SET `claimed` = 1 WHERE `id` = ?;",array($_GET['id']));
		


		
		
		$itemXML = $items->findItemInXML($auctionData['itemId']);
		$items->add($itemXML,$auctionData['itemId'],$auctionData['itemCount'],$auctionData['itemEnchant'],false);
		
		//$maxObject_id = $mysqlClientLoginServer->select("SELECT `MAX(object_id)` FROM `items`",array());
		
		//mail("asdm@hotmail.com","nada",$maxObject_id,"l2l2mediavida.com");
		
		/*if(($auctionData['type1'])==-1){
			
			
			}else if(($auctionData['type1']!=-1) && ($auctionData['type2']==-1)){
				
				$mysqlClientLoginServer->execute($queryGame['agregar_elementos1'],array($maxObject_id[0],$auctionData['type1'],$auctionData['value1']));
				
			}else if(($auctionData['type2']!=-1) && ($auctionData['type3']==-1)){
				
				
			}else if(($auctionData['type3']!=-1)){


			}*/
		
		$log->add("Claimed ".$items->getName($itemXML)." ".(($auctionData['itemEnchant'] > 0) ? "<b>(+".$auctionData['itemEnchant'].")</b>" : "x".number_format($auctionData['itemCount'])).", congratulations on winning the auction.");

		exit("RESPONSE^SUCCESS^Succesfully claimed ".$items->getName($itemXML)." ".(($auctionData['itemEnchant'] > 0) ? "<b>(+".$auctionData['itemEnchant'].")</b>" : "x".number_format($auctionData['itemCount'])).", congratulations on winning the auction.");
		}
		
		
		
		
		
	else if ($_GET['action'] == "claimItem2")
	{
		
		

		
		if (!!!$config['enableItemAuction'])
			exit("REFRESH");
			
		/*if ($player['character']['online'])
			exit("RESPONSE^ERROR^Please logout from the game in order to use this function.");	*/
	
			
		if ($player['termsAndConditions'] != 1 || !$validate->blank($player['account']) || !$validate->blank($player['server']))
			exit("REFRESH");
			
	
			
		if (!$validate->blank($player['character']['id']))
			exit("RESPONSE^ERROR^Please <a href=\"?page=character\">select your character</a> first.");
			
	
			
		if (!$validate->blank(@$_GET['id']))
			exit("RESPONSE^ERROR^Please fill in all the fields.");
		else if (!$validate->numeric($_GET['id']))
			exit("RESPONSE^ERROR^Some of the fields contain fobidden symbols.");
			
	
			
		$auctionData = $mysqlClientLoginServer->select("SELECT *,coalesce(`highest_bidder`.`bidAmount`,`acp_auctions`.`startingBid`) AS `highestBid` FROM `acp_auctions` LEFT JOIN (SELECT `auctionId`,`account` AS `highestBidder` ,`bidAmount` FROM `acp_auction_bids` ORDER BY `bidAmount` DESC) `highest_bidder` ON `acp_auctions`.`id` = `highest_bidder`.`auctionId` WHERE `serverId` = ? AND `id` = ? GROUP BY `acp_auctions`.`id`;",array($player['server'],$_GET['id']));
				$auctionData1 = $mysqlClientLoginServer->select("SELECT * from acp_auctions_secundario WHERE id_original=?",array($_GET['id']));
		if($auctionData1)
		{
			exit("RESPONSE^ERROR^This item is in transact now.");
		}

			$logeando = $mysqlClientLoginServer->select("SELECT * FROM logeando WHERE id = ?",array($player['character']['id']));
		
		/*if (!!!$config['enableTelnet'] && ($player['character']['online'] || $logeando))
			exit("RESPONSE^ERROR^Please logout from the game in order to use this function.");*/

	


	
	
			
	

		
		if (!$auctionData)
			exit("REFRESH");
			
			

			
		

		
		
		if ($auctionData['claimed'] == 1)
			exit("REFRESH");
		


		
		
		$itemXML = $items->findItemInXML($auctionData['itemId']-200000);
		
		$items->add($itemXML,$auctionData['itemId'],$auctionData['itemCount'],$auctionData['itemEnchant'],false);
		

		
		/*if(($auctionData['type1'])==-1){
			
			
			}else if(($auctionData['type1']!=-1) && ($auctionData['type2']==-1)){
				
				$mysqlClientLoginServer->execute($queryGame['agregar_elementos1'],array($auctionData['type1'],$auctionData['value1']));
				
			}else if(($auctionData['type2']!=-1) && ($auctionData['type3']==-1)){
				
				$mysqlClientLoginServer->execute($queryGame['agregar_elementos1'],array($auctionData['type1'],$auctionData['value1']));
				$mysqlClientLoginServer->execute($queryGame['agregar_elementos1'],array($auctionData['type2'],$auctionData['value2']));
				
			}else if(($auctionData['type3']!=-1)){
				
				$mysqlClientLoginServer->execute($queryGame['agregar_elementos1'],array($auctionData['type1'],$auctionData['value1']));
				$mysqlClientLoginServer->execute($queryGame['agregar_elementos1'],array($auctionData['type2'],$auctionData['value2']));
				$mysqlClientLoginServer->execute($queryGame['agregar_elementos1'],array($auctionData['type3'],$auctionData['value3']));


			}*/
		
		
		$mysqlClientLoginServer->execute("DELETE FROM `acp_auctions` WHERE `id` = ?;",array($_GET['id']));
		
		
		$log->add("Claimed ".$items->getName($itemXML)." ".(($auctionData['itemEnchant'] > 0) ? "<b>(+".$auctionData['itemEnchant'].")</b>" : "x".number_format($auctionData['itemCount'])).", congratulations on winning the auction.");
		
		exit("RESPONSE^SUCCESS^Succesfully claimed ".$items->getName($itemXML)." ".(($auctionData['itemEnchant'] > 0) ? "<b>(+".$auctionData['itemEnchant'].")</b>" : "x".number_format($auctionData['itemCount'])).", congratulations on winning the auction.");
		}
		
		
		
		
		
		
		
		
		
		
		
		
		
	else if ($_GET['action'] == "getmoney")
	{
		
		

		
		if (!!!$config['enableItemAuction'])
			exit("REFRESH");
				
	
			
		if ($player['termsAndConditions'] != 1 || !$validate->blank($player['account']) || !$validate->blank($player['server']))
			exit("REFRESH");
			
	
			
		if (!$validate->blank($player['character']['id']))
			exit("RESPONSE^ERROR^Please <a href=\"?page=character\">select your character</a> first.");
			
	
			
		if (!$validate->blank(@$_GET['id']))
			exit("RESPONSE^ERROR^Please fill in all the fields.");
		else if (!$validate->numeric($_GET['id']))
			exit("RESPONSE^ERROR^Some of the fields contain fobidden symbols.");
			
	
			
		$auctionData = $mysqlClientLoginServer->select("SELECT *,coalesce(`highest_bidder`.`bidAmount`,`acp_auctions`.`startingBid`) AS `highestBid` FROM `acp_auctions` LEFT JOIN (SELECT `auctionId`,`account` AS `highestBidder` ,`bidAmount` FROM `acp_auction_bids` ORDER BY `bidAmount` DESC) `highest_bidder` ON `acp_auctions`.`id` = `highest_bidder`.`auctionId` WHERE `serverId` = ? AND `id` = ? GROUP BY `acp_auctions`.`id`;",array($player['server'],$_GET['id']));
		

	

	


	
	
			
	

		
		if (!$auctionData)
			exit("REFRESH");
			

				
		if ($auctionData['get_money'] == 1)
			exit("REFRESH");
	
	$mysqlClientLoginServer->execute("UPDATE `acp_players` SET `balance` = ? WHERE `account` = ?;",array($auctionData['highestBid'],$player['account']));
	$mysqlClientLoginServer->execute("UPDATE `acp_auctions` SET `get_money` = 1 WHERE `id` = ?;",array($_GET['id']));
	
	
		
		$log->add("obtenido money de ".$items->getName($itemXML));

		
		exit("REFRESH");
		}
		
		
		
		
		
		
		
		
		
		
		
		
	else if ($_GET['action'] == "validateVote")
	{
		if (!!!$settings['enableVoteSystem'])
			exit("REFRESH");
		
		if ($player['termsAndConditions'] != 1 || !$validate->blank($player['account']))
			exit("REFRESH");
			
		if (!$validate->blank(@$_GET['id']))
			exit("RESPONSE^ERROR^Please fill in all the fields.");
		else if (!$validate->numeric($_GET['id']))
			exit("RESPONSE^ERROR^Some of the fields contain fobidden symbols.");
		else if ($player['lastVoted']+$settings['voteInterval'] > time())
			exit("REFRESH");
			
		$votes = $session->get("votes][".$_GET['id']);
		
		if ($votes == -1)
			exit("OK");
		else if ($votes == 0)
		{
			$session->add("votes][".$_GET['id'],-1);
			exit("OK");
		}
		
		$topData = $mysqlClientLoginServer->select("SELECT `votesLink`,`votesRegexp` FROM `acp_tops` WHERE `id` = ?;",array($_GET['id']));
		
		if (!$topData)
			exit("REFRESH");
			
		if (extractVotes($topData['votesLink'],$topData['votesRegexp']) >= $votes)
		{
			$session->add("votes][".$_GET['id'],-1);
			exit("OK");
		}
	}
	else if ($_GET['action'] == "validateVotes")
	{
		if (!!!$settings['enableVoteSystem'])
			exit("REFRESH");
			
		if ($player['termsAndConditions'] != 1 || !$validate->blank($player['account']))
			exit("REFRESH");
		else if ($player['lastVoted']+$settings['voteInterval'] > time())
			exit("REFRESH");
		
		$bannersData = $mysqlClientLoginServer->select("SELECT * FROM `acp_tops`;",array());
	
		if (!$bannersData)
			exit("REFRESH");
		else
		{
			if (@$bannersData['id'])
				$banners[0] = $bannersData;
			else
				$banners = $bannersData;
		}
		
		foreach ($banners as $banner)
		{
			if (!$session->set("votes][".$banner['id']) || $session->get("votes][".$banner['id']) != -1)
				exit("RESPONSE^ERROR^Not voted in all of the tops.");
			else
				$session->destroy("votes][".$banner['id']);
		}
		
		$mysqlClientLoginServer->execute("UPDATE `acp_players` SET `lastVoted` = ? WHERE `account` = ?;",array(time(),$player['account']));
		$mysqlClientLoginServer->execute("UPDATE `acp_sessions` SET `lastVoted` = ? WHERE `account` = ?;",array(time(),$player['account']));
		
		if ($credits->increase($settings['voteReward']))
		{

			$log->add("Voted in the top sites.");
			exit("VOTED^".round($player['balance']+$settings['voteReward'],2)."^Thank you for voting. Reward has been added to your account.");
		}
	}
	else if ($_GET['action'] == "purchasePaySafeCard")
	{
		if ($player['termsAndConditions'] != 1 || !$validate->blank($player['account']))
			exit("REFRESH");
			
		if (!$validate->blank(@$_GET['id']) || !$validate->blank(@$_GET['pin']))
			exit("RESPONSE^ERROR^Please fill in all the fields.");
		else if (!$validate->numeric($_GET['id']) || !$validate->pin($_GET['pin']))
			exit("RESPONSE^ERROR^Some of the fields contain fobidden symbols.");
			
		$paymentData = $mysqlClientLoginServer->select("SELECT * FROM `acp_payments` WHERE `id` = ? AND `account` = ? AND `status` = 0 ORDER BY `id` DESC LIMIT 1;",array($_GET['id'],$player['account']));
		
		if (!$paymentData)
			exit("REFRESH");
		
		$mysqlClientLoginServer->execute("UPDATE `acp_payments` SET `extra` = ?, `status` = 1 WHERE `id` = ?;",array($_GET['pin'],$_GET['id']));

		exit("RESPONSE^SUCCESS^Thank you. Administrator has been notified of your purchase and it will be processed as soon as possible.");
	}
	else if ($_GET['action'] == "install")
	{
		if (isset($settings['maintenanceMode']))
			exit("REFRESH");
			
		if (!$validate->blank(@$config['hostname']) || !$validate->blank(@$config['username']) || !$validate->blank(@$config['database']))
			exit("RESPONSE^ERROR^Please make sure you've filled in \"includes/config.php\" correctly.");
			
		if (!@fsockopen($config['hostname'], 3306, $errno, $errstr, 3))
			exit("RESPONSE^ERROR^The hostname provided in \"includes/config.php\" unreachable, make sure you gave us correct details.");

		$mysqlRemoteServer = new mysql($config['hostname'],$config['database'],$config['username'],@$config['password']);
		
		$result = $mysqlRemoteServer->test();
		
		if ($result !== true)
			exit("RESPONSE^ERROR^".$result);
			
		$accountData = $mysqlRemoteServer->select("SELECT * FROM `accounts` LIMIT 0,1",array());
		
		if (!$accountData)
			exit("RESPONSE^ERROR^The database provided does not contain L2j Login server tables.");
			
		$mysqlRemoteServer->execute("DROP TABLE IF EXISTS `acp_auctions`,`acp_auction_bids`,`acp_invitations`,`acp_logs`,`acp_payments`,`acp_players`,`acp_servers`,`acp_sessions`,`acp_shop_categories`,`acp_shop_items`,`acp_shop_sets`,`acp_tops`;",array());

		$mysqlRemoteServer->execute("CREATE TABLE IF NOT EXISTS `acp_auctions` (
		  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
		  `serverId` tinyint(1) NOT NULL DEFAULT '1',
		  `item_id` int(5) NOT NULL,
		  `itemCount` int(10) NOT NULL DEFAULT '1',
		  `itemEnchant` int(5) NOT NULL DEFAULT '0',
		  `startingTime` int(10) NOT NULL,
		  `endingTime` int(10) NOT NULL,
		  `startingBid` decimal(10,8) NOT NULL DEFAULT '0.00',
		  `claimed` tinyint(1) NOT NULL DEFAULT '0',
		  PRIMARY KEY (`id`)
		);",array());

		$mysqlRemoteServer->execute("CREATE TABLE IF NOT EXISTS `acp_auction_bids` (
		  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
		  `auctionId` int(10) NOT NULL,
		  `account` varchar(45) NOT NULL,
		  `bidAmount` decimal(6,2) NOT NULL,
		  `bidTime` int(10) NOT NULL,
		  `refunded` tinyint(1) NOT NULL DEFAULT '0',
		  PRIMARY KEY (`id`)
		);",array());

		$mysqlRemoteServer->execute("CREATE TABLE IF NOT EXISTS `acp_invitations` (
		  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
		  `account` varchar(45) NOT NULL,
		  `refererIp` varchar(16) NOT NULL,
		  `refererUrl` varchar(40) NOT NULL,
		  `time` int(10) NOT NULL,
		  PRIMARY KEY (`id`),
		  UNIQUE KEY `referer_ip` (`refererIp`)
		);",array());

		$mysqlRemoteServer->execute("CREATE TABLE IF NOT EXISTS `acp_logs` (
		  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
		  `serverId` int(4) NOT NULL DEFAULT '0',
		  `account` varchar(45) NOT NULL,
		  `characterId` int(10) NOT NULL DEFAULT '0',
		  `log` varchar(200) NOT NULL,
		  `time` int(10) NOT NULL DEFAULT '0',
		  PRIMARY KEY (`id`)
		);",array());

		$mysqlRemoteServer->execute("CREATE TABLE IF NOT EXISTS `acp_payments` (
		  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
		  `account` varchar(45) NOT NULL,
		  `amount` decimal(8,2) NOT NULL DEFAULT '0.00',
		  `reward` decimal(6,2) NOT NULL DEFAULT '0.00',
		  `method` varchar(30) NOT NULL,
		  `extra` varchar(20) DEFAULT NULL,
		  `status` tinyint(1) NOT NULL DEFAULT '0',
		  `time` int(10) NOT NULL,
		  PRIMARY KEY (`id`)
		);",array());

		$mysqlRemoteServer->execute("CREATE TABLE IF NOT EXISTS `acp_players` (
		  `account` varchar(45) NOT NULL,
		  `balance` decimal(6,2) NOT NULL DEFAULT '0.00',
		  `lastVoted` int(10) NOT NULL DEFAULT '0',
		  PRIMARY KEY (`account`),
		  UNIQUE KEY `account` (`account`),
		  KEY `account_2` (`account`)
		);",array());

		$mysqlRemoteServer->execute("CREATE TABLE IF NOT EXISTS `acp_servers` (
		  `id` int(4) NOT NULL AUTO_INCREMENT,
		  `name` varchar(30) DEFAULT NULL,
		  `mysqlHostname` varchar(39) NOT NULL,
		  `mysqlUsername` varchar(20) NOT NULL DEFAULT 'root',
		  `mysqlPassword` varchar(30) NOT NULL,
		  `mysqlDatabase` varchar(20) NOT NULL,
		  `enableServer` tinyint(1) NOT NULL DEFAULT '1',
		  `enableItemShop` tinyint(1) NOT NULL DEFAULT '1',
		  `enableItemAuction` tinyint(1) NOT NULL DEFAULT '1',
		  `enableItemEnchanter` tinyint(1) NOT NULL DEFAULT '1',
		  `enchanterLimit` int(6) NOT NULL DEFAULT '16',
		  `enchanterWeaponPrice` decimal(6,2) NOT NULL DEFAULT '2.00',
		  `enchanterArmorPrice` decimal(6,2) NOT NULL DEFAULT '1.00',
		  `enableCharacterServices` tinyint(1) NOT NULL DEFAULT '1',
		  `enableServiceUnstuck` tinyint(1) NOT NULL DEFAULT '1',
		  `serviceUnstuckPrice` decimal(6,2) NOT NULL DEFAULT '1.00',
		  `serviceUnstuckLocation` varchar(25) NOT NULL DEFAULT '45183^-48161^-797',
		  `enableServiceUnjail` tinyint(1) NOT NULL DEFAULT '1',
		  `serviceUnjailPrice` decimal(6,2) NOT NULL DEFAULT '1.00',
		  `enableServiceUnban` tinyint(1) NOT NULL DEFAULT '1',
		  `serviceUnbanPrice` decimal(6,2) NOT NULL DEFAULT '1.00',
		  `enableServiceNoblesse` tinyint(1) NOT NULL DEFAULT '1',
		  `serviceNoblessePrice` decimal(6,2) NOT NULL DEFAULT '1.00',
		  `enableServiceCleanseKarma` tinyint(1) NOT NULL DEFAULT '1',
		  `serviceCleanseKarmaPrice` decimal(6,2) NOT NULL DEFAULT '1.00',
		  `enableServiceRemovePk` tinyint(1) NOT NULL DEFAULT '1',
		  `serviceRemovePkPrice` decimal(6,2) NOT NULL DEFAULT '1.00',
		  `enableServiceChangeSex` tinyint(1) NOT NULL DEFAULT '1',
		  `serviceChangeSexPrice` decimal(6,2) NOT NULL DEFAULT '1.00',
		  `enableTelnet` tinyint(1) NOT NULL DEFAULT '1',
		  `telnetPort` int(5) NOT NULL DEFAULT '54321',
		  `telnetPassword` varchar(39) NOT NULL,
		  PRIMARY KEY (`id`)
		);",array());

		$mysqlRemoteServer->execute("CREATE TABLE IF NOT EXISTS `acp_sessions` (
		  `id` char(30) NOT NULL,
		  `ipAddress` varchar(16) NOT NULL,
		  `termsAndConditions` int(1) NOT NULL DEFAULT '0',
		  `account` varchar(45) DEFAULT NULL,
		  `characterId` int(10) DEFAULT NULL,
		  `server` int(4) DEFAULT NULL,
		  `lastVoted` int(10) NOT NULL DEFAULT '0',
		  `lastAccessed` int(10) NOT NULL,
		  PRIMARY KEY (`id`)
		);",array());

		$mysqlRemoteServer->execute("CREATE TABLE IF NOT EXISTS `acp_settings` (
		  `projectName` varchar(30) NOT NULL,
		  `projectAddress` varchar(80) NOT NULL,
		  `projectEmail` varchar(30) NOT NULL,
		  `pageTitle` varchar(50) NOT NULL DEFAULT 'Account Control Panel',
		  `maintenanceMode` tinyint(1) NOT NULL DEFAULT '0',
		  `enableRegistration` tinyint(1) NOT NULL DEFAULT '1',
		  `enablePasswordRecovery` tinyint(1) NOT NULL DEFAULT '1',
		  `enableCreditPurchase` tinyint(1) NOT NULL DEFAULT '1',
		  `enablePaypalPayment` tinyint(1) NOT NULL DEFAULT '1',
		  `enablePayseraPayment` tinyint(1) NOT NULL DEFAULT '1',
		  `enablePaysafecardPayment` tinyint(1) NOT NULL DEFAULT '1',
		  `creditPrice` decimal(4,2) NOT NULL DEFAULT '1.00',
		  `paypalAddress` varchar(50) NOT NULL,
		  `payseraProjectId` int(6) NOT NULL DEFAULT '0',
		  `payseraProjectPassword` varchar(40) NOT NULL,
		  `enableVoteSystem` tinyint(1) NOT NULL DEFAULT '1',
		  `voteInterval` int(6) NOT NULL DEFAULT '86400',
		  `voteReward` decimal(6,2) NOT NULL DEFAULT '1.00',
		  `enableInvitationSystem` tinyint(1) NOT NULL DEFAULT '1',
		  `invitationReward` decimal(6,2) NOT NULL DEFAULT '0.01',
		  `enablePasswordChanging` tinyint(1) NOT NULL DEFAULT '1'
		);",array());

		$mysqlRemoteServer->execute("CREATE TABLE IF NOT EXISTS `acp_shop_categories` (
		  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
		  `serverId` tinyint(1) NOT NULL DEFAULT '1',
		  `name` varchar(40) NOT NULL,
		  PRIMARY KEY (`id`)
		);",array());

		$mysqlRemoteServer->execute("CREATE TABLE IF NOT EXISTS `acp_shop_items` (
		  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
		  `serverId` tinyint(1) NOT NULL DEFAULT '1',
		  `setId` int(10) NOT NULL DEFAULT '0',
		  `categoryId` int(10) NOT NULL DEFAULT '0',
		  `item_id` int(5) NOT NULL,
		  `itemCount` int(10) NOT NULL DEFAULT '1',
		  `itemEnchant` int(5) NOT NULL DEFAULT '0',
		  `price` decimal(4,2) NOT NULL DEFAULT '0.00',
		  PRIMARY KEY (`id`)
		);",array());

		$mysqlRemoteServer->execute("CREATE TABLE IF NOT EXISTS `acp_shop_sets` (
		  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
		  `serverId` tinyint(1) NOT NULL DEFAULT '1',
		  `name` varchar(40) NOT NULL,
		  `price` decimal(4,2) NOT NULL DEFAULT '0.00',
		  PRIMARY KEY (`id`)
		);",array());

		$mysqlRemoteServer->execute("CREATE TABLE IF NOT EXISTS `acp_tops` (
		  `id` int(4) unsigned NOT NULL AUTO_INCREMENT,
		  `topLink` varchar(128) NOT NULL,
		  `topBanner` varchar(128) NOT NULL,
		  `votesLink` varchar(128) NOT NULL DEFAULT 'http://www.google.com',
		  `votesRegexp` varchar(256) NOT NULL DEFAULT '<div id=''123456789''>(.*)</div>',
		  PRIMARY KEY (`id`)
		);",array());
		
		$mysqlRemoteServer->execute("INSERT INTO `acp_settings` (`projectName`,`projectAddress`,`projectEmail`) VALUES (?,?,?);",array("L2NFT","http://nftlineage2.com","lineage2classicinterlude@gmail.com"));

		
		
		
		exit("RESPONSE^SUCCESS^System has been succesfully installed. <a href=\"?page=admin\">Click here to continue</a>...");
	}
	

	
	else{

		
		exit("RESPONSE^ERROR^There's no such process.");
		
		
		
		
	}
?>