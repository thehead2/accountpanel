<?php
	require_once "core.php";
	

	

	$templateContent = new template;
	
	
							/*foreach($_SERVER as $clave=>$valor){
			
			echo "A $clave le corresponde $valor " . "<br><br>";}*/
		
	$load = "error";

	if (!$validate->blank($player['account']) && @$_GET['page'] === "invite")
		$load = "invite";
	else if ($player['termsAndConditions'] != 1){
		
		
	
		$load = "termsAndConditions";
	}
		
	else if (!$player['account'])
		$load = "account";
	else if (!$player['email'])
		$load = "updateEmail";
	else if (!$player['server']){

		$load = "server";
	}
		
	else if ($validate->blank(@$_GET['page']) && $validate->letters($_GET['page']) && file_exists(PATH_TEMPLATES.$_GET['page'].".html"))
		$load = $_GET['page'];
	else
		exit(header("Location: ?page=player"));
		
	$error = false;
		
	if (!$mysqlClientLoginServer->isConnected() && !empty($config['username']))
	{
		$load = "errors/mysql";
		$error = true;
	}
	else if (!isset($settings) || $settings === false)
	{
		$load = "errors/install";
		$error = true;
	}
	
	
	
	
	else if ($settings['maintenanceMode'] == TRUE)
	{
		
		
		
		
		$load = "errors/maintenance";
		$error = true;	
	}

	if ($validate->blank($player['account']) && $validate->blank($player['server']) && !$error)
	{
		$templateContent->load(PATH_TEMPLATES."playerMain.html");
		require_once PATH_TEMPLATES."playerMain.php";
		
		$templateSubContent = new template;
		$templateSubContent->load(PATH_TEMPLATES.$load.".html");
		$templateContent->replace("content",$templateSubContent->content());
		if (is_file(PATH_TEMPLATES.$load.".php"))
			require_once PATH_TEMPLATES.$load.".php";		
	}
	else
	{
		$templateContent->load(PATH_TEMPLATES.$load.".html");
		if (is_file(PATH_TEMPLATES.$load.".php"))
			require_once PATH_TEMPLATES.$load.".php";
	}
	
	$templateMain = new template;
	$templateMain->load(PATH_TEMPLATES."main.html");
	
	$templateMain->replace("content",$templateContent->content());
	eval("?>".$templateMain->content());
?>