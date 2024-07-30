<?php
	if (!isset($load)) exit;
	
	$subContent = "";
	
	if (!!$settings['enableCreditPurchase'])
	{
		//echo "addess: ".$player['address'];
		$subContent = "<div class=\"ui-widget-header widget-header\">Purchase</div>
		<div class=\"ui-widget-content widget-content\">
			<br />
			<table height=800 border=10>
				<tr><td>";
					if (!!$settings['enablePaypalPayment'] && $settings['paypalAddress'])
						/*$subContent .= "<td class=\"percent33 middle\">
			<form action=\"https://nftlineage2.ddns.net/AccountControlPanel/blockchain/index.php\" id=\"blockchain\" method=\"post\">
			<img src=\"images/logo_btc.png\" alt=\"Pay with Bitcoin\" class=\"payment\" value=\"bitcoin\" />
				<div id=\"cantidad_btc\" class=\"input\"><input type=\"number\" name=\"cantidad_btc\" step=\"any\"/></div><br/>
				<input type=\"hidden\" name=\"user\" value=\"".$player['account']."\"/>
				<input type=\"submit\" value=\"Buy\" class=\"button_grande_azul\"/>  
	
			</form><br/><br/><br/>
		</td>";*/
		//$subContent .= "<center><img src=\"images/logo_btc.png\" alt=\"Pay with Bitcoin\" class=\"payment\" value=\"bitcoin\" /><center><br><br>";
		
			//<input type=\"submit\" value=\"Buy\" class=\"button_grande_azul\"/>  
	//$subContent .="<a href=\"buy_btc\" value=\"".$player['character']['id']."\" class=\"button_grande_azul\">Buy</a><br><br><br></td></tr><tr>";

					if (!!$settings['enablePayseraPayment'] && $settings['payseraProjectId'] && $settings['payseraProjectPassword'])
						$subContent .= "<td class=\"percent33 middle\">
							<img src=\"images/paysera.png\" alt=\"Pay with Paysera\" class=\"payment\" value=\"paysera\" /><br />
							Bank transfer, WebMoney, SMS
						</td>";
					if (!!$settings['enablePaysafecardPayment'])
						$subContent .= "<td class=\"percent33 middle\">
							<img src=\"images/paysafecard.png\" alt=\"Pay with PaySafeCard\" class=\"payment\" value=\"paysafecard\" /><br />
							PaySafeCards, Cash
						</tr>";
				$subContent .= "</tr>";
				
				
												$subContent .= "<tr><td class=\"percent33 middle\">
					
							<img src=\"images/paypal1.png\" />&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href=\"https://nftlineage2.ddns.net/paypal/\"><img src=\"images/paypal.gif\" /><br><br>
						
						</td></tr>";
			
				
															/*	$subContent .= "<tr><td class=\"percent33 middle\">
					
							<img src=\"images/busd.png\" /><br>
						
						</td></tr>";*/
						
						
								
																$subContent .= "<tr><td class=\"percent33 middle\"><br>
					
					<span class=\"button_grande_azul\">You can buy balance whit usdt in the Market</span><br><br><br><br>
					
					
								
								<a href=\"https://nftlineage2.ddns.net/AccountControlPanel/?page=itemShop/\" value=\"\" class=\"button_grande_azul\">Buy</a><br><br>
						</td>";
					//$subContent .="<a href=\"localhost/AccountControlPanel/?page=itemShop/\" value=\"\" class=\"button_grande_azul\">Buy</a><br><br>";
				
				$subContent .= "</tr>
			</table>
		</div>";
		

	}

	$templateContent->replace("purchase",$subContent);
	
?>
