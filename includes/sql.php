<?php
	$queryLogin['checkAccount']					= "SELECT `login`,`email` FROM `accounts` WHERE `login` = ? OR `email` = ?;";
	$queryLogin['checkEmail']					= "SELECT `email` FROM `accounts` WHERE `email` = ?;";
	$queryLogin['updateEmail']					= "UPDATE `accounts` SET `email` = ? WHERE `login` = ?;";
	$queryLogin['createAccount']				= "INSERT INTO `accounts` (`login`,`password`,`email`,`access_level`,`lastIP`) VALUES (?,?,?,?,?);";
	$queryLogin['referido']				= "INSERT INTO `referidos` (`characterName`,`account`) VALUES (?,?);";
	$queryLogin['checkReferido']				= "SELECT `char_name` FROM `characters` WHERE `char_name` = ?;";

	$queryLogin['loginToAccount']				= "SELECT `login` FROM `accounts` WHERE `login` = ? AND `password` = ?;";
	$queryLogin['changePassword']				= "UPDATE `accounts` SET `password` = ? WHERE `login` = ?;";
	$queryLogin['selectAccountData']			= "SELECT `email`,`access_level`,`lastIP` FROM `accounts` WHERE `login` = ?;";
	$queryLogin['updatePlayerBan']				= "UPDATE `accounts` SET `access_level` = 0 WHERE `login` = ? AND `access_level` < -1;";
	
	$queryLogin['disableAccount']				= "UPDATE `accounts` SET `access_level` = -100 WHERE `login` = ?;";
	
	$queryGame['selectAllCharacters']			= "SELECT `obj_Id`,`char_name` FROM `characters` WHERE `account_name` = ?;";
	$queryGame['selectCharacterData']			= "SELECT `char_name`,`online` FROM `characters` WHERE `account_name` = ? AND `obj_Id` = ?;";
	$queryGame['characterLevel']			= "SELECT `level` FROM `character_subclasses` WHERE `char_obj_id` = ?;";

	$queryGame['selectPlayerItemData']			= "SELECT `object_id` FROM `items` WHERE `owner_id` = ? AND `item_id` = ?;";
	$queryGame['updatePlayerItemCount']			= "UPDATE `items` SET `count` = count+? WHERE `object_id` = ?;";
	$queryGame['addItemToPlayerInventory']		= "INSERT INTO `items` (`owner_id`,`object_id`,`item_id`,`count`,`enchant_level`,`loc`,`life_time`) VALUES (?,(SELECT MAX(object_id)+1 FROM items AS object_id),?,?,?,'INVENTORY',0);";
	$queryGame['selectAllPlayerItems']			= "SELECT `object_id`,`item_id`,`enchant_level` FROM `items` WHERE `owner_id` = ? AND `count` = 1 ORDER BY `item_id` DESC, `enchant_level` DESC;";
	
		$queryGame['selectAllPlayerItems2']			= "SELECT * FROM `items` WHERE `owner_id` = ? AND `count` = 1 ORDER BY `item_id` DESC, `enchant_level` DESC;";
		
				$queryGame['selectAllPlayerItems3']			= "SELECT * FROM `items` WHERE `owner_id` = ? and `loc` = 'INVENTORY' ORDER BY `item_id` DESC, `enchant_level` DESC;";

	$queryGame['selectPlayerItemEnchantData']			= "SELECT * FROM `items` WHERE `object_id` = ? AND `owner_id` = ?;";

	$queryGame['selectPlayerItemEnchantData8']			= "SELECT * FROM `items` WHERE `item_id` = ? AND `owner_id` = ?;";

	
	
	
		$queryGame['updateEnchantOnItem3']			= "UPDATE `items` SET `enchant_level` = enchant_level+1 WHERE `object_id` = ?;";

	
	$queryGame['updateEnchantOnItem']			= "DELETE FROM `items` WHERE `object_id` = ?;";
	
	$queryGame['updateEnchantOnItem2']			= "INSERT INTO `items` (`object_id`,`owner_id`,`item_id`,`count`,`enchant_level`,`loc`,`loc_data`,`life_time`,`custom_type1`,`custom_type2`) VALUES ((SELECT MAX(object_id)+1 FROM items AS object_id),?,?,?,?,?,?,?,?,?);";
	
		$queryGame['insertintoshop']			= "INSERT INTO `acp_auctions` (`id`,`serverId`,`itemId`,`itemCount`,`itemEnchant`,`endingTime`,`startingBid`) VALUES ((SELECT MAX(id)+1 FROM acp_auctions AS id),1,?,?,?,?,?);";

	
	$queryGame['updatePlayerSex']				= "UPDATE `characters` SET `sex` = 1-sex WHERE `obj_Id` = ?;";
	$queryGame['removePlayerPK']				= "UPDATE `characters` SET `pkkills` = 0 WHERE `obj_Id` = ?;";
	$queryGame['removePlayerKarma']				= "UPDATE `characters` SET `karma` = 0 WHERE `obj_Id` = ?;";
	$queryGame['updatePlayerNoblesse']			= "UPDATE `characters` SET `nobless` = 1 WHERE `obj_Id` = ?;";
	$queryGame['updatePlayerJail']				= "UPDATE `characters` SET `punish_level` = 0, `punish_timer` = 0 WHERE `obj_Id` = ?;";
	$queryGame['updatePlayerLocation']			= "UPDATE `characters` SET `x` = ?, `y` = ?, `z` = ? WHERE `obj_Id` = ?;";
	$queryGame['element']			= "SELECT * FROM `item_elementals` WHERE `itemId` = ?;";
		$queryGame['element1']			= "SELECT `object_id` FROM `item_attributes`;";
		$queryGame['element2']			= "DELETE FROM `item_elementals` WHERE `itemId` = ?;";
		$queryGame['agregar_elementos1']			= "INSERT INTO `item_elementals` (`itemId`,`elemType`,`elemValue`) VALUES ((SELECT MAX(object_id) FROM items AS itemId),?,?);";



?>