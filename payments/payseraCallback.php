<?php
	require_once("../core.php");
	require_once(PATH_LIBRARIES."paysera.php");

	try {
    	$response = WebToPay::checkResponse($_GET, array(
			'projectid'     => $settings['payseraProjectId'],
			'sign_password' => $settings['payseraProjectPassword'],
		));

		if ($response['test'] !== '0') {
			throw new Exception("Testing, real payment was not made");
		}
						
		if (!!!$settings['enablePayseraPayment'])
			exit("Paysera payment option is disabled.");
		
		$order = $response['orderid'];
		$amount = $response['amount'];
		
		if (!$validate->blank(@$order))
			exit("Missing some details.");
		else if (!$validate->numeric($order))
			exit("Some of the fields contain fobidden symbols.");
		
		$paymentData = $mysqlClientLoginServer->select("SELECT * FROM `acp_payments` WHERE `id` = ? AND `status` = 0 ORDER BY `id` DESC LIMIT 1;",array($order));
		
		if (!$paymentData)
			exit("There's no outstanding order with such ID.");
			
		if ($paymentData['amount'] != number_format($amount/100,2))
			exit("The money sent does not match the order amount.");
								
		$player['account'] = $paymentData['account'];
		
		$mysqlClientLoginServer->execute("UPDATE `acp_payments` SET `status` = 2 WHERE `id` = ?;",array($order));
		$credits->increase($paymentData['reward']);
		
		$log->add("Purchased ".sprintf('%0.2f',$paymentData['reward'])."&#162; for ".sprintf('%0.2f',$paymentData['amount'])." EUR through Paysera(Mokejimai.lt), thank you.");
				
		exit("OK");

	} catch (Exception $e) {
		echo get_class($e) . ': ' . $e->getMessage();
	} 
?>