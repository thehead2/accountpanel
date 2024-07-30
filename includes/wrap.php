<?php
	require_once("../core.php");
	
	//phpinfo();
	
	/*class workerThread extends Thread {
	public function __construct($text){
	  $this->text=$text;
	}

	public function run(){
		sleep(30);
		$mysqlClientLoginServer->execute("DELETE FROM transactions_delay WHERE wallet=?",array(text));
	  }
	}*/
	

	
	/*if ($session->set("request") && $session->get("request")+1 > time())
		exit("RESPONSE^ERROR^Flood protector has been activated. Please try again...");*/
		
		
	$session->add("request",time());


	$text = $_POST['text'];
	$amount = $_POST['amount'];
	$pass = $_POST['pass'];
	$accion = $_POST['accion'];
	if($pass!="1234561#")
		exit("no tienes permisos");

	if($accion=="conectar_wallet")
		echo $mysqlClientLoginServer->execute("REPLACE INTO `acp_players` (account, address) VALUES (?,?);",array($player['account'],$text));
	/*else if($accion=="transactions_delay")
	{
		$data = $mysqlClientLoginServer->select("SELECT wallet from transactions_delay WHERE wallet = ?;",array($text));
		if($data)
		{
			exit("RESPONSE^ERROR^Please wait 30 seconds before making another transaction.");
		}
		else
		{
			
			$mysqlClientLoginServer->execute("INSERT INTO transactions_delay (wallet) VALUES (?)",array($text));
			$worker=new workerThread($text);
			$workers->start();
			

			
			exit("RESPONSE^SUCCESS^Succesfully");			
		}
		
	}*/
	else if($accion=="obtener_wallet")
	{
		$data = $mysqlClientLoginServer->select("SELECT address from acp_players WHERE account = ?;",array($text));
		echo $data["address"];
	}
	else if($accion=="purchased_marketplace")
	{
		$credits = $_POST['newCredits'];
		
		$auctionData = $mysqlClientLoginServer->select("SELECT * FROM `acp_auctions` WHERE `id` = ?;",array($text));

		if (!$auctionData)
			echo "REFRESH";
		else
		{
			$itemXML = $items->findItemInXML($auctionData['itemId']-200000);
			
			//$items->add($itemXML,$auctionData['itemId'],$auctionData['itemCount'],$auctionData['itemEnchant'],false);

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
			$mysqlClientGameServer->execute("INSERT INTO market_items_buy VALUES (?,?,?,?)",array($player['character']['id'],$auctionData['itemId'],$auctionData['itemCount'],$auctionData['itemEnchant']));
			$mysqlClientLoginServer->execute("DELETE FROM `acp_auctions` WHERE `id` = ?;",array($text));	
			
			$log->add("Purchased ".$items->getName($itemXML)." ".(($auctionData['itemEnchant'] > 0) ? "<b>(+".$auctionData['itemEnchant'].")</b>" : "x".number_format($auctionData['itemCount']))." for ".sprintf('%0.2f',$auctionData['startingBid']).".");
			
			exit("PURCHASED^".round($credits-$auctionData['startingBid'],2)."^Succesfully purchased ".$items->getName($itemXML)." ".(($auctionData['itemEnchant'] > 0) ? "<b>(+".$auctionData['itemEnchant'].")</b>" : "x".number_format($auctionData['itemCount']))." for ".sprintf('%0.2f',$auctionData['startingBid']).".");
	
		
		}	
		

	}
	else if($accion=="buy_tokens")
	{
		$itemData = $mysqlClientLoginServer->select("SELECT * FROM `acp_shop_items` WHERE `id` = ?;",array($text));
		$cantidad = $itemData['itemCount'];
		$mysqlClientLoginServer->execute("INSERT INTO special_shop_balance_donations (characterId,balance) VALUES(?,?)",array($player['character']['id'],$cantidad*100));
		$data = $mysqlClientLoginServer->select("SELECT characterName FROM referidos WHERE account = ? LIMIT 1",array($player['account']));
		if($data)
		{
			$referido_name = $data["characterName"];
			
			$data1 = $mysqlClientLoginServer->select("SELECT obj_Id FROM characters WHERE char_name = ? LIMIT 1",array($referido_name));
			$ref_id = $data1["obj_Id"];
			$mysqlClientLoginServer->execute("INSERT INTO special_shop_balance_donations (characterId,balance) VALUES(?,?)",array($ref_id,$cantidad));
			$data2 = $mysqlClientLoginServer->select("SELECT balance FROM special_shop_balance_referidos WHERE characterId = ? LIMIT 1",array($ref_id));
			if($data2)
			{
				$balance = $data2["balance"];
				$balance_final = $balance+$amount;
				$mysqlClientLoginServer->execute("REPLACE INTO special_shop_balance_referidos (characterId,balance) VALUES(?,?)",array($ref_id,$balance_final));
			}
			else
			{
				$mysqlClientLoginServer->execute("REPLACE INTO special_shop_balance_referidos (characterId,balance) VALUES(?,?)",array($ref_id,$cantidad));
				
			}
		}

		$itemXML = $items->findItemInXML($itemData['itemId']);
		$log->add("Purchased ".$items->getName($itemXML)." ".(($itemData['itemEnchant'] > 0) ? "<b>(+".$itemdata['itemEnchant'].")</b>" : "x".number_format($itemData['itemCount']))." for ".sprintf('%0.2f',$itemData['price']).".");
		exit("RESPONSE^SUCCESS^Succesfully purchased Balance");

	}
	else if($accion=="purchased_market")
	{
		$credits = $_POST['newCredits'];
		
		$itemData = $mysqlClientLoginServer->select("SELECT * FROM `acp_shop_items` WHERE `id` = ?;",array($text));

		if (!$itemData)
			echo "REFRESH";
		else
		{
			$itemXML = $items->findItemInXML($itemData['itemId']);
			//$items->add($itemXML,$itemData['itemId'],$itemData['itemCount'],$itemData['itemEnchant'],false);
			$mysqlClientGameServer->execute("INSERT INTO market_items_buy VALUES (?,?,?,?)",array($player['character']['id'],$itemData['itemId'],$itemData['itemCount'],$itemData['itemEnchant']));
			/*if($text<=5)
			{
				$data = $mysqlClientLoginServer->select("SELECT characterName FROM referidos WHERE account = ? LIMIT 1",array($player['account']));
					if($data)
					{
						$referido_name = $data["characterName"];
						$data1 = $mysqlClientLoginServer->select("SELECT obj_Id FROM characters WHERE char_name = ? LIMIT 1",array($referido_name));
						$ref_id = $data1["obj_Id"];
						$data2 = $mysqlClientLoginServer->select("SELECT ancient_adena FROM special_shop_balance_referidos WHERE characterId = ? LIMIT 1",array($ref_id));
						if($data2)
						{
							$ancient_adena = $data2["ancient_adena"];
							$ancient_adena_final = $ancient_adena+$itemData['itemCount']/100;
							$mysqlClientLoginServer->execute("REPLACE INTO special_shop_balance_referidos (characterId,ancient_adena) VALUES(?,?)",array($ref_id,$ancient_adena_final));
							
						}
						else
						{
							$mysqlClientLoginServer->execute("REPLACE INTO special_shop_balance_referidos (characterId,ancient_adena) VALUES(?,?)",array($ref_id,$itemData['itemCount']/100));
							
						}
						$mysqlClientLoginServer->execute("INSERT INTO ancient_adena_referido_reclamar (characterId,ancient_adena) VALUES(?,?)",array($ref_id,$itemData['itemCount']/100));

					}
			
			}*/
			
			$log->add("Purchased ".$items->getName($itemXML)." ".(($itemData['itemEnchant'] > 0) ? "<b>(+".$itemdata['itemEnchant'].")</b>" : "x".number_format($itemData['itemCount']))." for ".sprintf('%0.2f',$itemData['price']).".");
				
				//$mysqlClientGameServer->execute("DELETE FROM login_bloqueado WHERE id = ?",array($player['character']['id']));
				$mysqlClientGameServer->execute("DELETE FROM acp_auctions_secundario WHERE character_id = ?",array($player['character']['id']));
			
			exit("PURCHASED^".round($credits-$itemData['price'],2)."^Succesfully purchased ".$items->getName($itemXML)." ".(($itemData['itemEnchant'] > 0) ? "<b>(+".$itemData['itemEnchant'].")</b>" : "x".number_format($itemData['itemCount']))." for ".sprintf('%0.2f',$itemData['price']).".");
		}
		
	}
	else if($accion=="purchaseService")
	{
		$credits = $_POST['newCredits'];
	
		
		if($text==1)
		{
			$mysqlClientLoginServer->execute("UPDATE characters SET karma=0 WHERE obj_Id=?",array($player['character']['id']));
			$log->add("Purchased cleanse karma.");
			$mysqlClientLoginServer->execute("DELETE FROM login_bloqueado WHERE id = ?",array($player['character']['id']));
			exit("RESPONSE^SUCCESS^Succesfully purchased Cleanse Karma");			

		}
		else if($text==2)
		{
			$data = $mysqlClientLoginServer->select("SELECT sex FROM characters WHERE obj_Id = ? LIMIT 1",array($player['character']['id']));
			$sex = $data["sex"];
			$sex1=0;
			if($sex==0)
				$sex1=1;
			else
				$sex1=0;
			$mysqlClientLoginServer->execute("UPDATE characters SET sex=? WHERE obj_Id=?",array($sex1,$player['character']['id']));
			$log->add("Purchased change sex.");
			$mysqlClientLoginServer->execute("DELETE FROM login_bloqueado WHERE id = ?",array($player['character']['id']));
			exit("RESPONSE^SUCCESS^Succesfully purchased Change Sex");	
			
			
		}
		/*else if($text==3)
		{
			$data = $mysqlClientLoginServer->select("SELECT char_name FROM characters WHERE obj_Id = ? LIMIT 1",array($player['character']['id']));
			$name = $data["char_name"];
	
			$mysqlClientLoginServer->execute("UPDATE characters SET sex=? WHERE obj_Id=?",array($sex1,$player['character']['id']));
			$log->add("Purchased change name.");
			$mysqlClientLoginServer->execute("DELETE FROM login_bloqueado WHERE id = ?",array($player['character']['id']));
			exit("RESPONSE^SUCCESS^Succesfully purchased Change Sex");	
			
			
		}*/

		/*else
		{
			$itemXML = $items->findItemInXML($itemData['itemId']);
			$items->add($itemXML,$itemData['itemId'],$itemData['itemCount'],$itemData['itemEnchant'],false);
			if($text<=3)
			{
				$data = $mysqlClientLoginServer->select("SELECT characterName FROM referidos WHERE account = ? LIMIT 1",array($player['account']));
					if($data)
					{
						$referido_name = $data["characterName"];
						$data1 = $mysqlClientLoginServer->select("SELECT obj_Id FROM characters WHERE char_name = ? LIMIT 1",array($referido_name));
						$ref_id = $data1["obj_Id"];
						$data2 = $mysqlClientLoginServer->select("SELECT ancient_adena FROM special_shop_balance_referidos WHERE characterId = ? LIMIT 1",array($ref_id));
						if($data2)
						{
							$ancient_adena = $data2["ancient_adena"];
							$ancient_adena_final = $ancient_adena+$itemData['itemCount']/100;
							$mysqlClientLoginServer->execute("REPLACE INTO special_shop_balance_referidos (characterId,ancient_adena) VALUES(?,?)",array($ref_id,$ancient_adena_final));
							
						}
						else
						{
							$mysqlClientLoginServer->execute("REPLACE INTO special_shop_balance_referidos (characterId,ancient_adena) VALUES(?,?)",array($ref_id,$itemData['itemCount']/100));
							
						}
						$mysqlClientLoginServer->execute("INSERT INTO ancient_adena_referido_reclamar (characterId,ancient_adena) VALUES(?,?)",array($ref_id,$itemData['itemCount']/100));

					}
			
			}
			
			$log->add("Purchased ".$items->getName($itemXML)." ".(($itemData['itemEnchant'] > 0) ? "<b>(+".$itemdata['itemEnchant'].")</b>" : "x".number_format($itemData['itemCount']))." for ".sprintf('%0.2f',$itemData['price']).".");
			//$mysqlClientLoginServer->execute("DELETE FROM login_bloqueado WHERE id = ?",array($player['character']['id']));
			//exit("RESPONSE^SUCCESS^Succesfully purchased Balance");
			
			exit("PURCHASED^".round($credits-$itemData['price'],2)."^Succesfully purchased ".$items->getName($itemXML)." ".(($itemData['itemEnchant'] > 0) ? "<b>(+".$itemData['itemEnchant'].")</b>" : "x".number_format($itemData['itemCount']))." for ".sprintf('%0.2f',$itemData['price']).".");
		}*/
		
	}
	else if($accion=='changeName')
	{
		$mysqlClientLoginServer2->execute("UPDATE characters SET char_name=? where obj_Id=? ",array($text,$player['character']['id']));
		$log->add("Changed your name.");
		exit("RESPONSE^SUCCESS^Succesfully changed name");
	}
	else if($accion=='claim_tokens')
	{
		$tokensData = $mysqlClientLoginServer->select("SELECT * from envios_wallet where account=?;",array($player['account']));
		if (!$tokensData)
			exit("REFRESH");
		$wallet = $tokensData["wallet"];
		$tokens = $tokensData["count"];
		if($amount==0)
		{
			exit("RESPONSE^ERROR^You dont have enough tokens to claim.");
		}
		if($amount>$tokens)
		{
			exit("RESPONSE^ERROR^You dont have enough tokens to claim.");
		}
		$mysqlClientLoginServer->execute("REPLACE INTO envios_wallet (account,wallet,count) VALUES(?,?,?);",array($player['account'],$wallet,0));
		$log->add("claimed tokens: ".$amount." ");
		//exit("PURCHASED^".round($amount+$amount,2)."^Succesfully claimed ".$amount." tokens");
		exit("RESPONSE^SUCCESS^Succesfully claimed tokens");

	}
		else if($accion=='claim_busd')
	{
		$tokensData = $mysqlClientLoginServer->select("SELECT * from envios_wallet_tickets where account=?;",array($player['account']));
		if (!$tokensData)
			exit("REFRESH");
		$wallet = $tokensData["wallet"];
		$tokens = $tokensData["count"];
		if($amount==0)
		{
			exit("RESPONSE^ERROR^You dont have enough usdt to claim.");
		}
		if($amount>$tokens)
		{
			exit("RESPONSE^ERROR^You dont have enough usdt to claim now. ".$amount." y ".$tokens);
		}
		$mysqlClientLoginServer->execute("REPLACE INTO envios_wallet_tickets (account,wallet,count) VALUES(?,?,?);",array($player['account'],$wallet,0));
		$log->add("claimed usdt: ".$amount." ");
		//exit("PURCHASED^".round($amount+$amount,2)."^Succesfully claimed ".$amount." tokens");
		exit("RESPONSE^SUCCESS^Succesfully claimed usdt");

	}
	else if($accion=="desbloquear")	
	{
		$mysqlClientLoginServer->execute("DELETE FROM acp_auctions_secundario WHERE character_id = ?",array($player['character']['id']));
		echo $mysqlClientLoginServer->execute("DELETE FROM login_bloqueado WHERE id = ?",array($player['character']['id']));
	}
	
	
?>