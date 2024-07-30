<?php

	if (!isset($load)){
		
	
		exit;
	} 
		
	if ($validate->blank($player['server'])) exit(header("Location: ?page=character"));
	
	$subContent = "";
	
	$serverData = $mysqlClientLoginServer->select("SELECT `id`,`name` FROM `acp_servers`;",array());
	
	if (!$serverData)
		$subContent = "There's currently no servers for you to select.";
	else
	{
		if ($serverData['id'])
		{
			$player['server'] = $serverData['id'];
			updateSession();
				
			exit(header("Location: ?page=character"));
		}
		
		foreach($serverData as $server)
		{
			$subContent .= "<button class=\"process\" title=\"server\" name=\"id\" value=\"".$server['id']."\">".$server['name']."</button>";
		}
	}
	
	$templateContent->replace("buttonset",$subContent);
?>
