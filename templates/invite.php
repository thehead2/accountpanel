<?php




	if (!isset($load)) exit;
	
	
	
	
	
	if (!$settings['enableInvitationSystem']){
		exit(header("Location: ".$settings['projectAddress']));
		}
	if ($validate->blank($player['account'])){
	
		exit(header("Location: ".$settings['projectAddress']));
		}
	else if (!$validate->blank(@$_GET['ref'])){

		exit(header("Location: ".$settings['projectAddress']));
		}
	/*if (getenv('HTTP_X_FORWARDED_FOR')){
		
		echo 1;
		//exit(header("Location: ".$settings['projectAddress']));
		}
	else if ($validate->blank($player['account'])){
	
	echo 2;
		//exit(header("Location: ".$settings['projectAddress']));
		}
	else if (!$validate->blank(@$_GET['account'])){
		
		echo 3;
		//exit(header("Location: ".$settings['projectAddress']));
		}
	else if (!$validate->account($_GET['account'])){
		echo 4;
		//exit(header("Location: ".$settings['projectAddress']));
		}*/
	
	/*if (isset($_SERVER['HTTP_REFERER'])){
		
		echo 5;
		preg_match('@^[^/]+://[^/]+@', $_SERVER['HTTP_REFERER'], $match);
		}
	else{
		echo 6;
		//exit(header("Location: ".$settings['projectAddress']));
		}*/

	/*if ($mysqlClientLoginServer->execute("INSERT INTO `acp_invitations` (`account`,`refererIp`,`refererUrl`,`time`) VALUES (?,?,?,?);",array($_GET['account'],$_SERVER['REMOTE_ADDR'],isset($match[0])?$match[0]:"url",time())))
	{
		echo 7;
		$player['account'] = $_GET['account'];
		//$credits->increase($settings['invitationReward']);
	}*/
	//exit(header("Location: ".$settings['projectAddress']));
?>