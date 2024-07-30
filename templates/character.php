<?php
	if (!isset($load)) exit;
	
	if ($validate->blank($player['character']['id']))
		exit(header("Location: ?page=player"));
	
	$subContent = "";
	
	$characterData = $mysqlClientGameServer->select("SELECT obj_Id, char_name FROM characters WHERE account_name = ?",array($player['account']));
	
	
	
	if (!$characterData)
		$subContent = "There are no created characters on this account.";
	else
	{
		if (isset($characterData['char_name']))
		{
			
			$player['character']['id'] = $characterData[0];

			
			updateSession();
			exit(header("Location: ?page=player"));
		}
		
		foreach($characterData as $character)
		{
			$subContent .= "<button class=\"process\" title=\"character\" name=\"id\" value=\"".$character[0]."\">".$character[1]."</button>";
		}
	}
	
	$templateContent->replace("buttonset",$subContent);
?>
