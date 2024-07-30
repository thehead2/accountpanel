<?php
	if (!isset($load)) exit;
	
	$subContent = "";
	
	$logsData = $mysqlClientLoginServer->select("SELECT `log`,`time` FROM `acp_logs` WHERE `account` = ? ORDER BY `id` DESC LIMIT 0,30;",array($player['account']));
	
	if (!$logsData)
		$subContent = "There are no previous actions yet.";
	else
	{
		if (isset($logsData['time']))
			$subContent .= "<b>".date("d M\, Y H:i:s e",$logsData['time'])."</b><br />".$logsData['log']."<hr />";
		else
		{
			foreach($logsData as $log)
			{
				$subContent .= "<b>".date("d M\, Y H:i:s e",$log['time'])."</b><br />".$log['log']."<hr />";
			}
		}
	}
	
	$templateContent->replace("logs",$subContent);
	
?>
