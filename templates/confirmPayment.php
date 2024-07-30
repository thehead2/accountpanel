<?php
	if (!isset($load)) exit;
	
	if (!!!$settings['enableCreditPurchase'])
		exit(header("Location: ?page=player"));
	
	$subContent = "";
	
	if (!$validate->blank($_GET['system']) || !$validate->blank($_GET['credits']))
		exit(header("Location: ?page=credits"));
	else if (!$validate->letters($_GET['system']) || !$validate->money($_GET['credits']))
		exit(header("Location: ?page=credits"));
	else if ($_GET['system'] != "paypal" && $_GET['system'] != "paysera" && $_GET['system'] != "paysafecard")
		exit(header("Location: ?page=credits"));
	else if ($_GET['system'] == "paysafecard" && ($_GET['credits'] != 10.00 && $_GET['credits'] != 25.00 && $_GET['credits'] != 50.00 && $_GET['credits'] != 100.00))
		exit(header("Location: ?page=credits"));
	else if ($_GET['credits'] <= 0.00)
		exit(header("Location: ?page=credits"));
	else if (number_format($_GET['credits']*$settings['creditPrice'],8) >= 999.1)
		exit(header("Location: ?page=credits"));
	
	if (!(isset($_GET['confirm']) && $_GET['confirm'] == "true"))
		$mysqlClientLoginServer->execute("INSERT INTO `acp_payments` (`account`,`amount`,`reward`,`method`,`time`) VALUES (?,?,?,?,?);",array($player['account'],number_format($_GET['credits']*$settings['creditPrice'],8),$_GET['credits'],$_GET['system'],time()));

	$paymentData = $mysqlClientLoginServer->select("SELECT * FROM `acp_payments` WHERE `account` = ? AND `status` = 0 ORDER BY `id` DESC LIMIT 1;",array($player['account']));
	
	if (!$paymentData)
		exit(header("Location: ?page=player"));
		
	if ($_GET['system'] == "paypal" && !!$settings['enablePaypalPayment'] && $settings['paypalAddress'])
		$subContent = "<button class=\"redirect\" value=\"https://www.paypal.com/cgi-bin/webscr?cmd=_donations&business=".$settings['paypalAddress']."&lc=GB&item_name=Purchasing ".number_format($paymentData['reward'],8)." credits for ".number_format($paymentData['amount'],8)." EUR&item_number=".$paymentData['id']."-".$client['id']."&amount=".number_format($paymentData['amount'],8)."&currency_code=EUR&cancel_return=".getCurrentURL()."?page=credits&return=".getCurrentURL()."?page=credits&no_note=0&currency_code=EUR&bn=PP%2dDonationsBF%3abtn_donateCC_LG%2egif%3aNonHostedGuest\">Pay now</button>";
	else if ($_GET['system'] == "paysera" && !!$settings['enablePayseraPayment'] && $settings['payseraProjectId'] && $settings['payseraProjectPassword'])
	{
		if (!$validate->blank($settings['payseraProjectId']) || !$validate->blank($settings['payseraProjectPassword']))
			exit(header("Location: ?page=credits"));
			
		if (isset($_GET['confirm']) && $_GET['confirm'] == "true") {
			require_once(PATH_LIBRARIES."paysera.php");

			try {
				$request = WebToPay::redirectToPayment(array(
					'projectid'     => $settings['payseraProjectId'],
					'sign_password' => $settings['payseraProjectPassword'],
					'orderid'       => $paymentData['id']."-".$client['id'],
					'amount'        => ($paymentData['amount']*100),
					'paytext'		=> "Purchasing ".number_format($paymentData['reward'],8)." credits for ".number_format($paymentData['amount'],8)." EUR. Order ID [order_nr] on [site_name].",
					'currency'      => "EUR",
					'country'       => "EN",
					'accepturl'     => getCurrentURL()."?credits=",
					'cancelurl'     => getCurrentURL()."?credits=",
					'callbackurl'   => getCurrentURL()."/payments/payseraCallback.php",
					'test'          => 0,
				));
			} catch (WebToPayException $e) {
				echo $e;
			}
		}

		$subContent = "<button class=\"redirect\" value=\"?page=confirmPayment&system=paysera&credits=".$_GET['credits']."&confirm=true\">Pay now</button>";
	}
	else if ($_GET['system'] == "paysafecard" && !!$settings['enablePaysafecardPayment'])
		$subContent = "<form id=\"purchasePaySafeCard\">
			Please enter your pin
			<input type=\"hidden\" id=\"id\" value=\"".$paymentData['id']."\" />
			<div class=\"input\"><input type=\"text\" id=\"pin\" value=\"\" /><span>PIN</span></div>
			<button>Confirm</button>
		</form>";
	else
		exit(header("Location: ?page=credits"));

	$templateContent->replace("button",$subContent);
	
?>
