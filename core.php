<?php
	error_reporting(E_ALL);
	
	session_start();
	
	define("VERSION", "3.05");

	define("PATH_ROOT", realpath(dirname(__FILE__))."/");
	define("PATH_INCLUDES", PATH_ROOT."includes/");
	define("PATH_LIBRARIES", PATH_ROOT."libraries/");
	define("PATH_PAYMENTS", PATH_ROOT."payments/");
	define("PATH_TEMPLATES", PATH_ROOT."templates/");
	
	date_default_timezone_set("GMT+0");
	
	$player = array();
	
	require_once PATH_INCLUDES."classes.php";
	require_once PATH_INCLUDES."functions.php";
	require_once PATH_INCLUDES."sql.php";
	require_once PATH_INCLUDES."config.php";
	
	$mysqlClientLoginServer = new mysql($config['hostname'],$config['database'],$config['username'],$config['password']);
	$mysqlClientLoginServer2 = new mysql($config['hostname'],$config['database2'],$config['username'],$config['password']);
	
	$settings = $mysqlClientLoginServer->select("SELECT * FROM `acp_settings`;",array());
	
	
										/*foreach($settings as $clave=>$valor){
			
			echo "A $clave le corresponde $valor " . "<br><br>";}*/
	
	
		
	if ($cookie->set("sessionID") || $validate->sessionID($cookie->get("sessionID")))
	{
		$player['sessionID'] = $cookie->get("sessionID");
		$sessionData = $mysqlClientLoginServer->select("SELECT * FROM `acp_sessions` WHERE `id` = ?;",array($player['sessionID']));
	}
	else
	{
		$cookie->destroy("sessionID");
		$sessionData = $mysqlClientLoginServer->select("SELECT * FROM `acp_sessions` WHERE `ipAddress` = ?;",array($_SERVER['REMOTE_ADDR']));
	}
	
	$player['termsAndConditions'] 	= 0;
	$player['account']				= null;
	$player['character']['id']		= null;
	$player['server']				= null;
	$player['lastVoted']			= 0;
		
	if (!$sessionData)
	{
		$player['sessionID'] = generateSessionID();
		$cookie->add("sessionID",$player['sessionID'],time()+60*60*24*365);
		
		$mysqlClientLoginServer->execute("DELETE FROM `acp_sessions` WHERE `lastAccessed` < ?;",array(time()-604800));
		$mysqlClientLoginServer->execute("INSERT INTO `acp_sessions` (`id`,`ipAddress`,`lastAccessed`) VALUES (?,?,?);",array($player['sessionID'],$_SERVER['REMOTE_ADDR'],time()));
	}
	else
	{
		$player['termsAndConditions']	= $sessionData['termsAndConditions'];
		$player['account']				= $sessionData['account'];
		$player['character']['id']		= $sessionData['characterId'];
		$player['server']				= $sessionData['server'];
			
		if (!isset($player['sessionID']))
		{
			$player['sessionID'] = $sessionData['id'];
			$cookie->add("sessionID",$sessionData['id'],time()+60*60*24*365);
		}
		
		$update = false;
			
		if ($sessionData['server'] != $_SERVER['REMOTE_ADDR'])
			$update = true;
			
		if ($sessionData['lastAccessed'] < time()-604800)
			$update = true;
			
		if ($update)
			updateSession();
	}
		
	if ($validate->blank($player['account']))
	{
		$playerData = $mysqlClientLoginServer2->select($queryLogin['selectAccountData'],array($player['account']));
			
		if (!$playerData){

			destroySession();
		}
			
				
		$playerAcpData = $mysqlClientLoginServer->select("SELECT * FROM `acp_players` WHERE `account` = ?;",array($player['account']));

		
		$player['balance']	= 0;
		$player['email']	= $playerData['email'];
		
			
		if (!$playerAcpData)
			$mysqlClientLoginServer->execute("INSERT INTO `acp_players` (`account`) VALUES (?);",array($player['account']));
		else
		{
			$player['balance'] = $playerAcpData['balance'];
			$player['address'] = $playerAcpData['address'];
			if($player['address']==null)
			{
				$player['address']="";
			}

				
			if ($player['lastVoted'] <= $playerAcpData['lastVoted'])
				$player['lastVoted'] = $playerAcpData['lastVoted'];
		}

		if ($validate->blank($player['server']))
		{
			$serverData = $mysqlClientLoginServer->select("SELECT * FROM `acp_servers` WHERE `id` = ? AND `enableServer` = 1;",array($player['server']));
			
			if (!$serverData){

			destroySession();
			}
					

			
			$config += $serverData;
			
			
									/*foreach($config as $clave=>$valor){
			
			echo "A $clave le corresponde $valor " . "<br><br>";}*/
			
		
			$mysqlClientGameServer = new mysql($config['mysqlHostname'],$config['mysqlDatabase'],$config['mysqlUsername'],$config['mysqlPassword']);
		}
		
		if ($validate->blank($player['character']['id']))
		{
			$characterData = $mysqlClientGameServer->select($queryGame['selectCharacterData'],array($player['account'],$player['character']['id']));
			$characterLevel = $mysqlClientGameServer->select($queryGame['characterLevel'],array($player['character']['id']));
			if (!$characterData)
			{
				$player['character']['id'] = NULL;
				updateSession();
			}
			else
			{
				$player['character']['name']	= $characterData[0];
				$player['character']['level']	= $characterLevel[0];
				$player['character']['online']	= (bool)$characterData[1];
				
			/*foreach($player as $clave=>$valor){
			
			echo "A $clave le corresponde $valor " . "<br><br>";}*/
			}
		}
	}
?>