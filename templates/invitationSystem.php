<?php
	if (!isset($load)) exit;
	
	
											/*foreach($_SERVER as $clave=>$valor){
			
			echo "A $clave le corresponde $valor " . "<br><br>";}*/
	
	
	
	
	if (!!!$settings['enableInvitationSystem'])
		exit(header("Location: ?page=player"));
	
	/*$subContent = "";
		
	$invitationData = $mysqlClientLoginServer->select("SELECT `refererIp`,`refererUrl`,`time` FROM `acp_invitations` WHERE `account` = ?;",array($player['account']));
	
	if (!$invitationData)
		$subContent = "You have not invited any players yet.";
	else
	{
		if ($invitationData['refererIp'])
			$invitations[0] = $invitationData;
		else
			$invitations = $invitationData;
			
		$subContent = "<table>";
			
		foreach ($invitations as $invitation)
		{
			$subContent .= "<tr class=\"ui-state-default\">
				<td class=\"middle\">
					".substr_replace($invitation['refererIp'],"x",-1)."
				</td>
				<td>
					".$invitation['refererUrl']."
				</td>
				<td class=\"tableEnd ui-state-highlight\">
					".date("d M\, Y H:i:s e",$invitation['time'])."
				</td>
			</tr>";
		}

		$subContent .= "</table>";
	}

	$templateContent->replace("count",((!$invitationData) ? 0 : count($invitations)));*/
	$templateContent->replace("account",$player['account']);
	//$templateContent->replace("invitations",$subContent);
	
?>
