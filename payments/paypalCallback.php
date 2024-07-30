<?php

	


	error_reporting(E_STRICT);

	$req = "cmd=_notify-validate";

	foreach ($_POST as $key => $value) {  
		$value = urlencode(stripslashes($value));  
		$req .= "&$key=$value";
	}
	

	

	$header = "POST /cgi-bin/webscr HTTP/1.0\r\n";  
	$header .= "Content-Type: application/x-www-form-urlencoded\r\n";  
	$header .= "Content-Length: " . strlen($req) . "\r\n\r\n";  

	$fp = fsockopen("www.paypal.com", 80, $errno, $errstr, 30);
	
  	if (!$fp) exit();

	fputs ($fp, $header.$req);
  
	while (!feof($fp)) { 
		$res = fgets ($fp, 1024);

		if (strcmp($res, "VERIFIED") == 0) {
			if ($_POST['payment_status'] == "Completed" && $_POST['mc_currency'] == "EUR") {
  				$order = $_POST['item_number'];
				$amount = $_POST['mc_gross'];
				
				require_once("../core.php");
				
				if (!!!$settings['enablePaypalPayment'])
					exit("Paypal payment option is disabled.");
				
				if (!$validate->blank(@$order))
					exit("Missing some details.");
				else if (!$validate->numeric($order))
					exit("Some of the fields contain fobidden symbols.");
				
				$paymentData = $mysqlClientLoginServer->select("SELECT * FROM `acp_payments` WHERE `id` = ? AND `status` = 0 ORDER BY `id` DESC LIMIT 1;",array($order));
				
				if (!$paymentData)
					exit("There's no outstanding order with such ID.");
					
				if ($paymentData['amount'] != $amount)
					exit("The money sent does not match the order amount.");
										
				$player['account'] = $paymentData['account'];
				
				$mysqlClientLoginServer->execute("UPDATE `acp_payments` SET `status` = 2 WHERE `id` = ?;",array($order));
				$credits->increase($paymentData['reward']);
				$log->add("Purchased ".sprintf('%0.2f',$paymentData['reward'])."&#162; for ".sprintf('%0.2f',$paymentData['amount'])." EUR through PayPal, thank you.");
			}
			else if ($_POST['payment_status'] == "Reversed")
			{
				$order = $_POST['item_number'];
				$amount = $_POST['mc_gross'];

				require_once("../core.php");
				
				if (!$validate->blank(@$order))
					exit("Missing some details.");
				else if (!$validate->numeric($order))
					exit("Some of the fields contain fobidden symbols.");
				
				$paymentData = $mysqlClientLoginServer->select("SELECT * FROM `acp_payments` WHERE `id` = ? ORDER BY `id` DESC LIMIT 1;",array($order));
				
				if (!$paymentData)
					exit("There's no such order.");
				
				$mysqlClientLoginServer->execute($queryLogin['disableAccount'],array($paymentData['account']));
				
				$log->add("Account has been banned for reversing the payment.");
			}
		}
	} 

	fclose ($fp);
?>