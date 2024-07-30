<?php
	if (!isset($load)) exit;
	if ($validate->blank($player['account']))
		exit(header("Location: ?page=server"));
	$ref = "";
	if($validate->blank(@$_GET['ref']))
	{
		$ref=@$_GET['ref'];
	}
		

		
	$subContent = "";
	
	if (!!$settings['enableRegistration'])
		$subContent .= "<td class=\"percent33\">
			<form id=\"register\" class=\"collapsed\">
				<div class=\"input\"><input type=\"text\" id=\"account\" value=\"\" /><span>Account ID</span></div>
				<div class=\"input\"><input type=\"text\" id=\"email\" value=\"\" /><span>E-Mail Address</span></div>
				<div class=\"input\"><input type=\"password\" id=\"password\" value=\"\" /><span>Password</span></div>
				<div class=\"input\"><input type=\"password\" id=\"repeatPassword\" value=\"\" /><span>Repeat Password</span></div>
				<div class=\"input\"><input type=\"text\" id=\"referido\" value=\"$ref\" /><span>Referer Name</span></div>
				<button>Create a new account</button>
			</form>
		</td>";
	
	$subContent .= "<td class=\"percent33 middle\">
		<form id=\"login\">
			<div class=\"input\"><input type=\"text\" id=\"account\" value=\"\" /><span>Account ID</span></div>
			<div class=\"input\"><input type=\"password\" id=\"password\" value=\"\" /><span>Password</span></div>
			<button>Login</button>
		</form>
	</td>";
	
	if (!!$settings['enablePasswordRecovery'])
		$subContent .= "<td class=\"percent33 right\">
			<form id=\"recoverPassword\" class=\"collapsed\">
				<div class=\"input\"><input type=\"text\" id=\"account\" value=\"\" /><span>Account ID</span></div>
				<div class=\"input\"><input type=\"text\" id=\"email\" value=\"\" /><span>E-Mail Address</span></div>
				<button>Recover Password</button>
			</form>
		</tr>";
	
	$templateContent->replace("forms",$subContent);
?>
