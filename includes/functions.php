<?php
	function testMysqlConnection($hostname,$username,$password = null,$database)
	{
		try
		{
			$handler = new PDO("mysql:host={$hostname};dbname={$database}",$username,$password);
			$handler = null;
			return true;
		}
		catch (PDOException $e)
		{
			return false;
		}
	}
	
	function generateSessionID()
	{
		return strrev(substr(md5(date("Ymdhis").$_SERVER['REMOTE_ADDR']),0,-2));
	}
	
	function updateSession()
	{
		global $mysqlClientLoginServer;
		global $player;
		global $client;

		$mysqlClientLoginServer->execute("UPDATE `acp_sessions` SET `id` = ?, `termsAndConditions` = ?, `account` = ?, `characterId` = ?, `server` = ?, `ipAddress` = ?, `lastAccessed` = ? WHERE `id` = ?;",array($player['sessionID'],$player['termsAndConditions'],$player['account'],$player['character']['id'],$player['server'],$_SERVER['REMOTE_ADDR'],time(),$player['sessionID']));
	}
	
	function clearSession()
	{
		global $mysqlClientLoginServer;
		global $player;
		global $client;

		$mysqlClientLoginServer->execute("UPDATE `acp_sessions` SET `account` = NULL, `characterId` = NULL, `server` = NULL WHERE `id` = ?;",array($player['sessionID']));
	}
	
	function destroySession()
	{
		global $mysqlClientLoginServer;
		global $player;
		global $client;
		
		$mysqlClientLoginServer->execute("DELETE FROM `acp_sessions` WHERE `id` = ?;",array($player['sessionID']));
		exit(header("Location: ?page=termsAndConditions"));
	}
	
	function in_array_r($needle, $haystack, $strict=true)
	{
		foreach ($haystack as $item)
		{
			if (($strict ? $item === $needle : $item == $needle) || (is_array($item) && in_array_r($needle, $item, $strict)))
				return true;
		}

		return false;
	}
	
	function formatTime($seconds)
	{
		if (!$seconds = (int)$seconds) return "ERROR";

		$units = array(
			'd'	=> 86400,
			'h'	=> 3600,
			'm'	=> 60
		);

		$strs = array();

		foreach($units as $name=>$int)
		{
			if($seconds < $int)
				continue;
			$num = (int) ($seconds / $int);
			$seconds = $seconds % $int;
			if (empty($strs))
				$strs[] = "$num$name";
			else
			{
				$strs[] = "$num$name";
				break;
			}
		}
		
		if (count($strs) == 1 && $strs[0] > 2)
			return "<span class=\"red\">".$strs[0]." ".$seconds."s</a>";
		else if (empty($strs))
			return "FINISHED";

		return implode(" ", $strs);
	}
	
	function getCurrentURL()
	{
		return "http://".$_SERVER['HTTP_HOST'].strtok($_SERVER['REQUEST_URI'],"?");
	}
	
	function extractVotes($link,$regexp)
	{
		$opts = array('http' =>
			array(
				'method'  => 'GET',
				'timeout' => 5 
			)
		);

		$context  = stream_context_create($opts);
		
		ini_set("default_socket_timeout", 5);
		$content = str_replace("'", '"', file_get_contents($link, false, $context));

		preg_match("#".trim($regexp)."#", $content, $votes);

		return ((isset($votes[1])) ? preg_replace("/[^0-9]*/", "", $votes[1]) : 0);
	}
?>