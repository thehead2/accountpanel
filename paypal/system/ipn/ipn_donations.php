<?php
/*
 * This program is free software: you can redistribute it and/or modify it under
 * the terms of the GNU General Public License as published by the Free Software
 * Foundation, either version 3 of the License, or (at your option) any later
 * version.
 * 
 * This program is distributed in the hope that it will be useful, but WITHOUT
 * ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS
 * FOR A PARTICULAR PURPOSE. See the GNU General Public License for more
 * details.
 * 
 * You should have received a copy of the GNU General Public License along with
 * this program. If not, see <http://www.gnu.org/licenses/>.
 */

/*
 * PayPal System - IPN Coins
 * @author Dasoldier
 */

require "../class/paypal.php";
require "../config.php";
require "../connect.php";
include "l2j_telnet.php";


/*$charId=268667823;
$charname="misaka";
			$connection = new PDO("mysql:host=$db_host;dbname=$db_database;charset=utf8", $db_user, $db_pass);
			$connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);*/





$p = new paypal_class;
$p->paypal_url = $payPalURL;
if ($p->validate_ipn())
{
	if ($p->ipn_data['payment_status']=='Completed') 
	{
		
		// Telnet host, port, pass, timeout.
		$telnet = new telnet("".$telnet_host."", "".$telnet_port."", "".$telnet_pass."", 2);

		// Gets the donated amount.
		$amount = $p->ipn_data['mc_gross'];

		// Gets the donated amount minus paypals fee.
		$amountminfee = $p->ipn_data['mc_gross'] - $p->ipn_data['mc_fee'];

		// Get character name + donation option from paypal ipn data.
		$custom = $p->ipn_data['custom'];

		// Here we need to separate the data into separate values.
		$splitdata = explode('|', $custom);
		$charname = $splitdata[0];
		$donation_option = $splitdata[1];
		$donation_option_enc = $splitdata[2];

		$donation_option1 = 'Coins';
		$donation_option2 = 'Karma';
		$donation_option3 = 'Pkpoints';
		$donation_option4 = 'Enchitems';

		// Item enchant.
		$donation_enc_option1 = 'Shirt';
		$donation_enc_option2 = 'Helmet';
		$donation_enc_option3 = 'Necklace';
		$donation_enc_option4 = 'Weapon';
		$donation_enc_option5 = 'FullarmorBreastplate';
		$donation_enc_option6 = 'Shield';
		$donation_enc_option7 = 'Ring1';
		$donation_enc_option8 = 'Ring2';
		$donation_enc_option9 = 'Earring1';
		$donation_enc_option10 = 'Earring2';
		$donation_enc_option11 = 'Gloves';
		$donation_enc_option12 = 'Leggings';
		$donation_enc_option13 = 'Boots';
		$donation_enc_option14 = 'Belt';
		$donation_enc_option15 = 'All_Enc';

		// Get transaction_id from paypal ipn data.
		$transid = $p->ipn_data['txn_id'];
		
		// Query info.
		$invertory = 'INVENTORY';
		$enchlvl = 0;
		// TODO: need to check this data.
		$loc_data = 0;

		$custom_type1 = 0;
		$custom_type2 = 0;



	try {
			// Try to make connection.
			$connection = new PDO("mysql:host=$db_host;dbname=$db_database;charset=utf8", $db_user, $db_pass);
			$connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			

			
			// Here we will make a log of all the donations after the payment status is complete.
			if ($donation_option === $donation_option1)
				{
					$pay_text = 'Paypal, Coins';
				}
			if ($donation_option === $donation_option2)
				{
					$pay_text = 'Remove karma';
				}
			if ($donation_option === $donation_option3)
				{
					$pay_text = 'Remove PK points';
				}
			if ($donation_option === $donation_option4)
				{
					$pay_text = 'Enchant item';
				}

			$dc_log = $connection->prepare('INSERT INTO log_paypal_donations(transaction_id, donation, amount, amountminfee, character_name) VALUES(:transid, :pay_text, :amount, :amountminfee, :charname )');
			$dc_log->execute(array(
			':transid' => $transid,
			':pay_text' => $pay_text,
			':amount' => $amount,
			':amountminfee' => $amountminfee,
			':charname' => $charname
			));
			
			
										$charid_row = $connection->prepare('SELECT obj_Id,account_name FROM characters WHERE char_name = :charname LIMIT 1');
									$charid_row->execute(array(':charname' => $charname));
									$charid_row_fetch = $charid_row->fetchAll();
									$charId = $charid_row_fetch[0]['obj_Id'];
									$account_name = $charid_row_fetch[0]['account_name'];

			// Get the Charid given according to the char_name.
			/*$charid_row = $connection->prepare('SELECT login FROM accounts WHERE login = :charname LIMIT 1');
			$charid_row->execute(array(':charname' => $charname));
			$charid_row_fetch = $charid_row->fetchAll();
			$total = count($charid_row_fetch);
			$charId = $charid_row_fetch[0]['login'];*/


			// Check if character is online.
			/*$onlinechar_row = $connection->prepare('SELECT online FROM characters WHERE char_name = :charname LIMIT 1');
			$onlinechar_row->execute(array(':charname' => $charname));
			$character_row_fetch = $onlinechar_row->fetchAll();
			$onlinearray = $character_row_fetch[0]['online'];*/
		}
	catch(PDOException $e) 
		{
			// Local file reporting.
			// Logging: file location.
			$local_log_file = $log_location_ipn;

			// Logging: Timestamp.
			$local_log = '['.date('m/d/Y g:i A').'] - ';

			// Logging: response from the server.
			$local_log .= "IPN DONATIONS ERROR: ". $e->getMessage();
			$local_log .= '</td></tr><tr><td>';

			// Write to log.
			$fp=fopen($local_log_file,'a');
			fwrite($fp, $local_log . "");

			// Close file.
			fclose($fp);
		}
		
		
	
















	
		
		
		
		
		

// COINS DONATION OPTIONS.
if ($donation_option === $donation_option1)
{
	// Checks if coins is enabled in the config or else log this.
	if ($coins_enabled == true)
	{
		// Checks if charname exists.
		if ($charId>0)
		{
			// Donate Rewards Coins I.
			if ($amount == $donatecoinamount1)
			{
				// Checks if coins option 1 is enabled in the config or else make a log.
				 if ($coins1_enabled == true)
				{

					{

							try {

								
								
									/*$balance=0;
									$charid_row = $connection->prepare('SELECT balance FROM special_shop_balance WHERE characterId = :charname LIMIT 1');
									$charid_row->execute(array(':charname' => $charId));
									$charid_row_fetch = $charid_row->fetchAll();
									$balance = $charid_row_fetch[0]['balance'];
									$balance_final = $donatecoinreward1*100+$balance;*/
									
									
													$charid_row = $connection->prepare('SELECT obj_Id,account_name FROM characters WHERE char_name = :charname LIMIT 1');
									$charid_row->execute(array(':charname' => $charname));
									$charid_row_fetch = $charid_row->fetchAll();
									$charId = $charid_row_fetch[0]['obj_Id'];
									$account_name = $charid_row_fetch[0]['account_name'];
			

									$sql_inv_update = $connection->prepare('INSERT INTO special_shop_balance_donations (characterId,balance) VALUES(:account,:count)');
									$sql_inv_update->execute(array(
									':account' => $charId,
									':count' => $donatecoinreward1*100
									));
									
									$referido = $connection->prepare('SELECT characterName FROM referidos WHERE account = :account LIMIT 1');
									$referido->execute(array(':account' => $account_name));
									$referido_row_fetch = $referido->fetchAll();
									$referido_row_count = count($referido_row_fetch);
									if($referido_row_count==1)
									{

										$name_referido = $referido_row_fetch[0]['characterName'];
										
											$forma = $connection->prepare('SELECT obj_Id FROM characters WHERE char_name = :account LIMIT 1');
											$forma->execute(array(':account' => $name_referido));
											$forma_row_fetch = $forma->fetchAll();
											//print_r($forma_row_fetch);
											$ref_id = $forma_row_fetch[0]['obj_Id'];
										
											$sql_inv_update = $connection->prepare('INSERT INTO special_shop_balance_donations (characterId,balance) VALUES(:account,:count)');
											$sql_inv_update->execute(array(
											':account' => $ref_id,
											':count' => $donatecoinreward1
											));
											
											$forma = $connection->prepare('SELECT balance FROM special_shop_balance_referidos WHERE characterId = :account LIMIT 1');
											$forma->execute(array(':account' => $ref_id));
											$forma_row_fetch = $forma->fetchAll();
											$balance_row_count = count($forma_row_fetch);
											if($balance_row_count==1)
											{
												$balance_referido = $forma_row_fetch[0]['balance'];
												$balance_final = $balance_referido+$donatecoinreward1;
												
												$sql_inv_update = $connection->prepare('REPLACE INTO special_shop_balance_referidos (characterId,balance) VALUES(:account,:count)');
												$sql_inv_update->execute(array(
												':account' => $ref_id,
												':count' => $balance_final
												));
												
											}
																						else
											{
												$sql_inv_update = $connection->prepare('REPLACE INTO special_shop_balance_referidos (characterId,balance) VALUES(:account,:count)');
												$sql_inv_update->execute(array(
												':account' => $ref_id,
												':count' => $donatecoinreward1
												));
												
												
											}
											
											
										
									}
									
									/*$data2 = $mysqlClientLoginServer->select("SELECT balance FROM special_shop_balance_referidos WHERE characterId = ? LIMIT 1",array($ref_id));
										if($data2)
										{
											$balance = $data2["balance"];
											$balance_final = $balance+$amount;
											$mysqlClientLoginServer->execute("REPLACE INTO special_shop_balance_referidos (characterId,balance) VALUES(?,?)",array($ref_id,$balance_final));
										}*/
										

										
									
									$sql_inv_update0 = $connection->prepare('update recaudado AS r set amount=r.amount+'.$donatecoinreward1);
									$sql_inv_update0->execute(array());

								} 
							catch(PDOException $e)
								{
									// Local file reporting.
									// Logging: file location.
									$local_log_file = $log_location_ipn;

									// Logging: Timestamp
									$local_log = '['.date('m/d/Y g:i A').'] - ';

									// Logging: response from the server.
									$local_log .= "IPN COINS I ERROR: ". $e->getMessage();
									$local_log .= '</td></tr><tr><td>';

									// Write to log.
									$fp=fopen($local_log_file,'a');
									fwrite($fp, $local_log . "\n");

									// Close file.
									fclose($fp);
						
						}

					}
				}
				else
				{
					// Local file reporting.
					// Logging: file location.
					$local_log_file = $log_location_ipn;

					// Logging: Timestamp
					$local_log = '['.date('m/d/Y g:i A').'] - ';

					// Logging: response from the server.
					$local_log .= "IPN COINS I ERROR: Someone tried to enter coins option 1 while disabled in config. Exploit attack ? Charname: ". @$charname ." amount:". @$amount ." donation option:". @$donation_option ."Transaction ID:". @$transid;
					$local_log .= '</td></tr><tr><td>';

					// Write to log.
					$fp=fopen($local_log_file,'a');
					fwrite($fp, $local_log . "\n");

					// Close file.
					fclose($fp);
				}
			}

		// Donate Rewards Coins II.
		if ($amount == $donatecoinamount2)
		{
			// Checks if coins option 2 is enabled in the config or else make a log.
			if ($coins2_enabled == true)
			{
				// Checks if the character is online and telnet is enabled.
				if (($onlinearray == 1) && ($use_telnet == true))
				{
					// If character is online lets send some telnet commands.
					$telnet->init();
					echo $telnet->write("give ".$charname." ".$item_id." ".$donatecoinreward2."");
					echo $telnet->disconnect(); // TODO: Need to check if this is closing the connection.
				}
				// Else player is offline we will add the items trough a mysql query.
				else
				{

						try {
								/*// We will update the invertory.
								$sql_inv_update = $connection->prepare('UPDATE acp_players SET balance = balance+:donatecoinreward2 WHERE account = :charId');
								$sql_inv_update->execute(array(
								':donatecoinreward2' => $donatecoinreward2,
								':charId' => $charId
								));*/
								
									/*$balance=0;
									$charid_row = $connection->prepare('SELECT balance FROM special_shop_balance WHERE characterId = :charname LIMIT 1');
									$charid_row->execute(array(':charname' => $charId));
									$charid_row_fetch = $charid_row->fetchAll();
									$balance = $charid_row_fetch[0]['balance'];
									$balance_final = $donatecoinreward2*100+$balance;*/
									
													$charid_row = $connection->prepare('SELECT obj_Id,account_name FROM characters WHERE char_name = :charname LIMIT 1');
									$charid_row->execute(array(':charname' => $charname));
									$charid_row_fetch = $charid_row->fetchAll();
									$charId = $charid_row_fetch[0]['obj_Id'];
									$account_name = $charid_row_fetch[0]['account_name'];
			

									$sql_inv_update = $connection->prepare('INSERT INTO special_shop_balance_donations (characterId,balance) VALUES(:account,:count)');
									$sql_inv_update->execute(array(
									':account' => $charId,
									':count' => $donatecoinreward2*100
									));
									
									$referido = $connection->prepare('SELECT characterName FROM referidos WHERE account = :account LIMIT 1');
									$referido->execute(array(':account' => $account_name));
									$referido_row_fetch = $referido->fetchAll();
									$referido_row_count = count($referido_row_fetch);
									if($referido_row_count==1)
									{

										$name_referido = $referido_row_fetch[0]['characterName'];
										
											$forma = $connection->prepare('SELECT obj_Id FROM characters WHERE char_name = :account LIMIT 1');
											$forma->execute(array(':account' => $name_referido));
											$forma_row_fetch = $forma->fetchAll();
											//print_r($forma_row_fetch);
											$ref_id = $forma_row_fetch[0]['obj_Id'];
										
											$sql_inv_update = $connection->prepare('INSERT INTO special_shop_balance_donations (characterId,balance) VALUES(:account,:count)');
											$sql_inv_update->execute(array(
											':account' => $ref_id,
											':count' => $donatecoinreward2
											));
											
											
											$forma = $connection->prepare('SELECT balance FROM special_shop_balance_referidos WHERE characterId = :account LIMIT 1');
											$forma->execute(array(':account' => $ref_id));
											$forma_row_fetch = $forma->fetchAll();
											$balance_row_count = count($forma_row_fetch);
											if($balance_row_count==1)
											{
												$balance_referido = $forma_row_fetch[0]['balance'];
												$balance_final = $balance_referido+$donatecoinreward2;
												
												$sql_inv_update = $connection->prepare('REPLACE INTO special_shop_balance_referidos (characterId,balance) VALUES(:account,:count)');
												$sql_inv_update->execute(array(
												':account' => $ref_id,
												':count' => $balance_final
												));
												
											}
																						else
											{
												$sql_inv_update = $connection->prepare('REPLACE INTO special_shop_balance_referidos (characterId,balance) VALUES(:account,:count)');
												$sql_inv_update->execute(array(
												':account' => $ref_id,
												':count' => $donatecoinreward2
												));
												
												
											}
										
									}
									
									$sql_inv_update0 = $connection->prepare('update recaudado AS r set amount=r.amount+'.$donatecoinreward2);
									$sql_inv_update0->execute(array());
								
							} 
						catch(PDOException $e)
							{
								// Local file reporting.
								// Logging: file location.
								$local_log_file = $log_location_ipn;

								// Logging: Timestamp.
								$local_log = '['.date('m/d/Y g:i A').'] - ';

								// Logging: response from the server.
								$local_log .= "IPN COINS II ERROR: ". $e->getMessage();
								$local_log .= '</td></tr><tr><td>';

								// Write to log.
								$fp=fopen($local_log_file,'a');
								fwrite($fp, $local_log . "\n");

								// Close file.
								fclose($fp);  
							}

				}
			}
			else
			{
				// Local file reporting.
				// Logging: file location.
				$local_log_file = $log_location_ipn;

				// Logging: Timestamp.
				$local_log = '['.date('m/d/Y g:i A').'] - ';

				// Logging: response from the server.
				$local_log .= "IPN COINS II ERROR: Someone tried to enter coins option 2 while disabled in config. Exploit attack ? Charname: ". @$charname ." amount:". @$amount ." donation option:". @$donation_option ."Transaction ID:". @$transid;
				$local_log .= '</td></tr><tr><td>';

				// Write to log.
				$fp=fopen($local_log_file,'a');
				fwrite($fp, $local_log . "\n");

				// Close file.
				fclose($fp);
			}
		}
		
		// Donate Rewards Coins III.
		if ($amount == $donatecoinamount3)
		{
			// Checks if coins option 3 is enabled in the config or else make a log.
			if ($coins3_enabled == true)
			{

				{


						try {
								/*// We will update the invertory.
								$sql_inv_update = $connection->prepare('UPDATE acp_players SET balance = balance+:donatecoinreward3 WHERE account = :charId');
								$sql_inv_update->execute(array(
								':donatecoinreward3' => $donatecoinreward3,
								':charId' => $charId
								));*/
								
									/*$balance=0;
									$charid_row = $connection->prepare('SELECT balance FROM special_shop_balance WHERE characterId = :charname LIMIT 1');
									$charid_row->execute(array(':charname' => $charId));
									$charid_row_fetch = $charid_row->fetchAll();
									$balance = $charid_row_fetch[0]['balance'];
									$balance_final = $donatecoinreward3*100+$balance;*/
									
									
													$charid_row = $connection->prepare('SELECT obj_Id,account_name FROM characters WHERE char_name = :charname LIMIT 1');
									$charid_row->execute(array(':charname' => $charname));
									$charid_row_fetch = $charid_row->fetchAll();
									$charId = $charid_row_fetch[0]['obj_Id'];
									$account_name = $charid_row_fetch[0]['account_name'];
			

									$sql_inv_update = $connection->prepare('INSERT INTO special_shop_balance_donations (characterId,balance) VALUES(:account,:count)');
									$sql_inv_update->execute(array(
									':account' => $charId,
									':count' => $donatecoinreward3*100
									));
									
									$referido = $connection->prepare('SELECT characterName FROM referidos WHERE account = :account LIMIT 1');
									$referido->execute(array(':account' => $account_name));
									$referido_row_fetch = $referido->fetchAll();
									$referido_row_count = count($referido_row_fetch);
									if($referido_row_count==1)
									{

										$name_referido = $referido_row_fetch[0]['characterName'];
										
											$forma = $connection->prepare('SELECT obj_Id FROM characters WHERE char_name = :account LIMIT 1');
											$forma->execute(array(':account' => $name_referido));
											$forma_row_fetch = $forma->fetchAll();
											//print_r($forma_row_fetch);
											$ref_id = $forma_row_fetch[0]['obj_Id'];
										
											$sql_inv_update = $connection->prepare('INSERT INTO special_shop_balance_donations (characterId,balance) VALUES(:account,:count)');
											$sql_inv_update->execute(array(
											':account' => $ref_id,
											':count' => $donatecoinreward3
											));
											
																						
											$forma = $connection->prepare('SELECT balance FROM special_shop_balance_referidos WHERE characterId = :account LIMIT 1');
											$forma->execute(array(':account' => $ref_id));
											$forma_row_fetch = $forma->fetchAll();
											$balance_row_count = count($forma_row_fetch);
											if($balance_row_count==1)
											{
												$balance_referido = $forma_row_fetch[0]['balance'];
												$balance_final = $balance_referido+$donatecoinreward3;
												
												$sql_inv_update = $connection->prepare('REPLACE INTO special_shop_balance_referidos (characterId,balance) VALUES(:account,:count)');
												$sql_inv_update->execute(array(
												':account' => $ref_id,
												':count' => $balance_final
												));
												
											}
																						else
											{
												$sql_inv_update = $connection->prepare('REPLACE INTO special_shop_balance_referidos (characterId,balance) VALUES(:account,:count)');
												$sql_inv_update->execute(array(
												':account' => $ref_id,
												':count' => $donatecoinreward3
												));
												
												
											}
										
									}
									
									$sql_inv_update0 = $connection->prepare('update recaudado AS r set amount=r.amount+'.$donatecoinreward3);
									$sql_inv_update0->execute(array());
							} 
						catch(PDOException $e) 
							{
								// Local file reporting.
								// Logging: file location.
								$local_log_file = $log_location_ipn;

								// Logging: Timestamp
								$local_log = '['.date('m/d/Y g:i A').'] - ';

								// Logging: response from the server.
								$local_log .= "IPN COINS III ERROR: ". $e->getMessage();
								$local_log .= '</td></tr><tr><td>';

								// Write to log.
								$fp=fopen($local_log_file,'a');
								fwrite($fp, $local_log . "\n");

								// Close file.
								fclose($fp);
							}
					
				}
			}
			else
			{
				// Local file reporting.
				// Logging: file location.
				$local_log_file = $log_location_ipn;

				// Logging: Timestamp.
				$local_log = '['.date('m/d/Y g:i A').'] - ';

				// Logging: response from the server.
				$local_log .= "IPN COINS III ERROR: Someone tried to enter coins option 3 while disabled in config. Exploit attack ? Charname: ". @$charname ." amount:". @$amount ." donation option:". @$donation_option ."Transaction ID:". @$transid;
				$local_log .= '</td></tr><tr><td>';

				// Write to log.
				$fp=fopen($local_log_file,'a');
				fwrite($fp, $local_log . "\n");

				// Close file.
				fclose($fp);
			}
		}
		
		// Donate Rewards Coins IV.
		if ($amount == $donatecoinamount4)
		{
			// Checks if coins option 4 is enabled in the config or else make a log.
			if ($coins4_enabled == true)
			{

				{

						try {
								// We will update the invertory.
								/*$sql_inv_update = $connection->prepare('UPDATE acp_players SET balance = balance+:donatecoinreward4 WHERE account = :charId');
								$sql_inv_update->execute(array(
								':donatecoinreward4' => $donatecoinreward4,
								':charId' => $charId
								));*/
								
									/*$balance=0;
									$charid_row = $connection->prepare('SELECT balance FROM special_shop_balance WHERE characterId = :charname LIMIT 1');
									$charid_row->execute(array(':charname' => $charId));
									$charid_row_fetch = $charid_row->fetchAll();
									$balance = $charid_row_fetch[0]['balance'];
									$balance_final = $donatecoinreward4*100+$balance;*/
									
									
													$charid_row = $connection->prepare('SELECT obj_Id,account_name FROM characters WHERE char_name = :charname LIMIT 1');
									$charid_row->execute(array(':charname' => $charname));
									$charid_row_fetch = $charid_row->fetchAll();
									$charId = $charid_row_fetch[0]['obj_Id'];
									$account_name = $charid_row_fetch[0]['account_name'];
			

									$sql_inv_update = $connection->prepare('INSERT INTO special_shop_balance_donations (characterId,balance) VALUES(:account,:count)');
									$sql_inv_update->execute(array(
									':account' => $charId,
									':count' => $donatecoinreward4*100
									));
									
									$referido = $connection->prepare('SELECT characterName FROM referidos WHERE account = :account LIMIT 1');
									$referido->execute(array(':account' => $account_name));
									$referido_row_fetch = $referido->fetchAll();
									$referido_row_count = count($referido_row_fetch);
									if($referido_row_count==1)
									{

										$name_referido = $referido_row_fetch[0]['characterName'];
										
											$forma = $connection->prepare('SELECT obj_Id FROM characters WHERE char_name = :account LIMIT 1');
											$forma->execute(array(':account' => $name_referido));
											$forma_row_fetch = $forma->fetchAll();
											//print_r($forma_row_fetch);
											$ref_id = $forma_row_fetch[0]['obj_Id'];
										
											$sql_inv_update = $connection->prepare('INSERT INTO special_shop_balance_donations (characterId,balance) VALUES(:account,:count)');
											$sql_inv_update->execute(array(
											':account' => $ref_id,
											':count' => $donatecoinreward4
											));
											
																						
											$forma = $connection->prepare('SELECT balance FROM special_shop_balance_referidos WHERE characterId = :account LIMIT 1');
											$forma->execute(array(':account' => $ref_id));
											$forma_row_fetch = $forma->fetchAll();
											$balance_row_count = count($forma_row_fetch);
											if($balance_row_count==1)
											{
												$balance_referido = $forma_row_fetch[0]['balance'];
												$balance_final = $balance_referido+$donatecoinreward4;
												
												$sql_inv_update = $connection->prepare('REPLACE INTO special_shop_balance_referidos (characterId,balance) VALUES(:account,:count)');
												$sql_inv_update->execute(array(
												':account' => $ref_id,
												':count' => $balance_final
												));
												
											}
																						else
											{
												$sql_inv_update = $connection->prepare('REPLACE INTO special_shop_balance_referidos (characterId,balance) VALUES(:account,:count)');
												$sql_inv_update->execute(array(
												':account' => $ref_id,
												':count' => $donatecoinreward4
												));
												
												
											}
										
									}
									
									$sql_inv_update0 = $connection->prepare('update recaudado AS r set amount=r.amount+'.$donatecoinreward4);
									$sql_inv_update0->execute(array());
								
								
							} 
						catch(PDOException $e)
							{
								// Local file reporting.
								// Logging: file location.
								$local_log_file = $log_location_ipn;

								// Logging: Timestamp.
								$local_log = '['.date('m/d/Y g:i A').'] - ';

								// Logging: response from the server.
								$local_log .= "IPN COINS IV ERROR: ". $e->getMessage();
								$local_log .= '</td></tr><tr><td>';

								// Write to log.
								$fp=fopen($local_log_file,'a');
								fwrite($fp, $local_log . "\n");

								// Close file.
								fclose($fp);  
							}
					
					}
				}
				else
				{
					// Local file reporting.
					// Logging: file location.
					$local_log_file = $log_location_ipn;

					// Logging: Timestamp.
					$local_log = '['.date('m/d/Y g:i A').'] - ';

					// Logging: response from the server.
					$local_log .= "IPN COINS IV ERROR: Someone tried to enter coins option 4 while disabled in config. Exploit attack ? Charname: ". @$charname ." amount:". @$amount ." donation option:". @$donation_option ."Transaction ID:". @$transid;
					$local_log .= '</td></tr><tr><td>';

					// Write to log.
					$fp=fopen($local_log_file,'a');
					fwrite($fp, $local_log . "\n");

					// Close file.
					fclose($fp);
				}
			}
		if ($amount == $donatecoinamount5)
		{
			// Checks if coins option 4 is enabled in the config or else make a log.
			if ($coins5_enabled == true)
			{

				{

						try {
								// We will update the invertory.
								/*$sql_inv_update = $connection->prepare('UPDATE acp_players SET balance = balance+:donatecoinreward5 WHERE account = :charId');
								$sql_inv_update->execute(array(
								':donatecoinreward5' => $donatecoinreward5,
								':charId' => $charId
								));*/
								
									/*$balance=0;
									$charid_row = $connection->prepare('SELECT balance FROM special_shop_balance WHERE characterId = :charname LIMIT 1');
									$charid_row->execute(array(':charname' => $charId));
									$charid_row_fetch = $charid_row->fetchAll();
									$balance = $charid_row_fetch[0]['balance'];
									$balance_final = $donatecoinreward5*100+$balance;*/
									
									
													$charid_row = $connection->prepare('SELECT obj_Id,account_name FROM characters WHERE char_name = :charname LIMIT 1');
									$charid_row->execute(array(':charname' => $charname));
									$charid_row_fetch = $charid_row->fetchAll();
									$charId = $charid_row_fetch[0]['obj_Id'];
									$account_name = $charid_row_fetch[0]['account_name'];
			

									$sql_inv_update = $connection->prepare('INSERT INTO special_shop_balance_donations (characterId,balance) VALUES(:account,:count)');
									$sql_inv_update->execute(array(
									':account' => $charId,
									':count' => $donatecoinreward5*100
									));
									
									$referido = $connection->prepare('SELECT characterName FROM referidos WHERE account = :account LIMIT 1');
									$referido->execute(array(':account' => $account_name));
									$referido_row_fetch = $referido->fetchAll();
									$referido_row_count = count($referido_row_fetch);
									if($referido_row_count==1)
									{

										$name_referido = $referido_row_fetch[0]['characterName'];
										
											$forma = $connection->prepare('SELECT obj_Id FROM characters WHERE char_name = :account LIMIT 1');
											$forma->execute(array(':account' => $name_referido));
											$forma_row_fetch = $forma->fetchAll();
											//print_r($forma_row_fetch);
											$ref_id = $forma_row_fetch[0]['obj_Id'];
										
											$sql_inv_update = $connection->prepare('INSERT INTO special_shop_balance_donations (characterId,balance) VALUES(:account,:count)');
											$sql_inv_update->execute(array(
											':account' => $ref_id,
											':count' => $donatecoinreward5
											));
										
										
																					
											$forma = $connection->prepare('SELECT balance FROM special_shop_balance_referidos WHERE characterId = :account LIMIT 1');
											$forma->execute(array(':account' => $ref_id));
											$forma_row_fetch = $forma->fetchAll();
											$balance_row_count = count($forma_row_fetch);
											if($balance_row_count==1)
											{
												$balance_referido = $forma_row_fetch[0]['balance'];
												$balance_final = $balance_referido+$donatecoinreward5;
												
												$sql_inv_update = $connection->prepare('REPLACE INTO special_shop_balance_referidos (characterId,balance) VALUES(:account,:count)');
												$sql_inv_update->execute(array(
												':account' => $ref_id,
												':count' => $balance_final
												));
												
											}
																						else
											{
												$sql_inv_update = $connection->prepare('REPLACE INTO special_shop_balance_referidos (characterId,balance) VALUES(:account,:count)');
												$sql_inv_update->execute(array(
												':account' => $ref_id,
												':count' => $donatecoinreward5
												));
												
												
											}
											
									}
									
									$sql_inv_update0 = $connection->prepare('update recaudado AS r set amount=r.amount+'.$donatecoinreward5);
									$sql_inv_update0->execute(array());
							} 
						catch(PDOException $e)
							{
								// Local file reporting.
								// Logging: file location.
								$local_log_file = $log_location_ipn;

								// Logging: Timestamp.
								$local_log = '['.date('m/d/Y g:i A').'] - ';

								// Logging: response from the server.
								$local_log .= "IPN COINS IV ERROR: ". $e->getMessage();
								$local_log .= '</td></tr><tr><td>';

								// Write to log.
								$fp=fopen($local_log_file,'a');
								fwrite($fp, $local_log . "\n");

								// Close file.
								fclose($fp);  
							}
					
					}
				}
				else
				{
					// Local file reporting.
					// Logging: file location.
					$local_log_file = $log_location_ipn;

					// Logging: Timestamp.
					$local_log = '['.date('m/d/Y g:i A').'] - ';

					// Logging: response from the server.
					$local_log .= "IPN COINS IV ERROR: Someone tried to enter coins option 4 while disabled in config. Exploit attack ? Charname: ". @$charname ." amount:". @$amount ." donation option:". @$donation_option ."Transaction ID:". @$transid;
					$local_log .= '</td></tr><tr><td>';

					// Write to log.
					$fp=fopen($local_log_file,'a');
					fwrite($fp, $local_log . "\n");

					// Close file.
					fclose($fp);
				}
			}
					if ($amount == $donatecoinamount6)
		{
			// Checks if coins option 4 is enabled in the config or else make a log.
			if ($coins6_enabled == true)
			{

				{

						try {
								// We will update the invertory.
								/*$sql_inv_update = $connection->prepare('UPDATE acp_players SET balance = balance+:donatecoinreward5 WHERE account = :charId');
								$sql_inv_update->execute(array(
								':donatecoinreward5' => $donatecoinreward5,
								':charId' => $charId
								));*/
								
									/*$balance=0;
									$charid_row = $connection->prepare('SELECT balance FROM special_shop_balance WHERE characterId = :charname LIMIT 1');
									$charid_row->execute(array(':charname' => $charId));
									$charid_row_fetch = $charid_row->fetchAll();
									$balance = $charid_row_fetch[0]['balance'];
									$balance_final = $donatecoinreward5*100+$balance;*/
									
													$charid_row = $connection->prepare('SELECT obj_Id,account_name FROM characters WHERE char_name = :charname LIMIT 1');
									$charid_row->execute(array(':charname' => $charname));
									$charid_row_fetch = $charid_row->fetchAll();
									$charId = $charid_row_fetch[0]['obj_Id'];
									$account_name = $charid_row_fetch[0]['account_name'];
			

									$sql_inv_update = $connection->prepare('INSERT INTO special_shop_balance_donations (characterId,balance) VALUES(:account,:count)');
									$sql_inv_update->execute(array(
									':account' => $charId,
									':count' => $donatecoinreward6*100
									));
									
									$referido = $connection->prepare('SELECT characterName FROM referidos WHERE account = :account LIMIT 1');
									$referido->execute(array(':account' => $account_name));
									$referido_row_fetch = $referido->fetchAll();
									$referido_row_count = count($referido_row_fetch);
									if($referido_row_count==1)
									{

										$name_referido = $referido_row_fetch[0]['characterName'];
										
											$forma = $connection->prepare('SELECT obj_Id FROM characters WHERE char_name = :account LIMIT 1');
											$forma->execute(array(':account' => $name_referido));
											$forma_row_fetch = $forma->fetchAll();
											//print_r($forma_row_fetch);
											$ref_id = $forma_row_fetch[0]['obj_Id'];
										
											$sql_inv_update = $connection->prepare('INSERT INTO special_shop_balance_donations (characterId,balance) VALUES(:account,:count)');
											$sql_inv_update->execute(array(
											':account' => $ref_id,
											':count' => $donatecoinreward6
											));
											
																						
											$forma = $connection->prepare('SELECT balance FROM special_shop_balance_referidos WHERE characterId = :account LIMIT 1');
											$forma->execute(array(':account' => $ref_id));
											$forma_row_fetch = $forma->fetchAll();
											$balance_row_count = count($forma_row_fetch);
											if($balance_row_count==1)
											{
												$balance_referido = $forma_row_fetch[0]['balance'];
												$balance_final = $balance_referido+$donatecoinreward6;
												
												$sql_inv_update = $connection->prepare('REPLACE INTO special_shop_balance_referidos (characterId,balance) VALUES(:account,:count)');
												$sql_inv_update->execute(array(
												':account' => $ref_id,
												':count' => $balance_final
												));
												
											}
																						else
											{
												$sql_inv_update = $connection->prepare('REPLACE INTO special_shop_balance_referidos (characterId,balance) VALUES(:account,:count)');
												$sql_inv_update->execute(array(
												':account' => $ref_id,
												':count' => $donatecoinreward6
												));
												
												
											}
										
									}
									
									$sql_inv_update0 = $connection->prepare('update recaudado AS r set amount=r.amount+'.$donatecoinreward6);
									$sql_inv_update0->execute(array());
							} 
						catch(PDOException $e)
							{
								// Local file reporting.
								// Logging: file location.
								$local_log_file = $log_location_ipn;

								// Logging: Timestamp.
								$local_log = '['.date('m/d/Y g:i A').'] - ';

								// Logging: response from the server.
								$local_log .= "IPN COINS IV ERROR: ". $e->getMessage();
								$local_log .= '</td></tr><tr><td>';

								// Write to log.
								$fp=fopen($local_log_file,'a');
								fwrite($fp, $local_log . "\n");

								// Close file.
								fclose($fp);  
							}
					
					}
				}
				else
				{
					// Local file reporting.
					// Logging: file location.
					$local_log_file = $log_location_ipn;

					// Logging: Timestamp.
					$local_log = '['.date('m/d/Y g:i A').'] - ';

					// Logging: response from the server.
					$local_log .= "IPN COINS IV ERROR: Someone tried to enter coins option 4 while disabled in config. Exploit attack ? Charname: ". @$charname ." amount:". @$amount ." donation option:". @$donation_option ."Transaction ID:". @$transid;
					$local_log .= '</td></tr><tr><td>';

					// Write to log.
					$fp=fopen($local_log_file,'a');
					fwrite($fp, $local_log . "\n");

					// Close file.
					fclose($fp);
				}
			}
	if ($amount == $donatecoinamount7)
		{
			// Checks if coins option 4 is enabled in the config or else make a log.
			if ($coins7_enabled == true)
			{

				{

						try {
								// We will update the invertory.
								/*$sql_inv_update = $connection->prepare('UPDATE acp_players SET balance = balance+:donatecoinreward5 WHERE account = :charId');
								$sql_inv_update->execute(array(
								':donatecoinreward5' => $donatecoinreward5,
								':charId' => $charId
								));*/
								
									/*$balance=0;
									$charid_row = $connection->prepare('SELECT balance FROM special_shop_balance WHERE characterId = :charname LIMIT 1');
									$charid_row->execute(array(':charname' => $charId));
									$charid_row_fetch = $charid_row->fetchAll();
									$balance = $charid_row_fetch[0]['balance'];
									$balance_final = $donatecoinreward5*100+$balance;*/
									
									
													$charid_row = $connection->prepare('SELECT obj_Id,account_name FROM characters WHERE char_name = :charname LIMIT 1');
									$charid_row->execute(array(':charname' => $charname));
									$charid_row_fetch = $charid_row->fetchAll();
									$charId = $charid_row_fetch[0]['obj_Id'];
									$account_name = $charid_row_fetch[0]['account_name'];
			

									$sql_inv_update = $connection->prepare('INSERT INTO special_shop_balance_donations (characterId,balance) VALUES(:account,:count)');
									$sql_inv_update->execute(array(
									':account' => $charId,
									':count' => $donatecoinreward7*100
									));
									
									$referido = $connection->prepare('SELECT characterName FROM referidos WHERE account = :account LIMIT 1');
									$referido->execute(array(':account' => $account_name));
									$referido_row_fetch = $referido->fetchAll();
									$referido_row_count = count($referido_row_fetch);
									if($referido_row_count==1)
									{

										$name_referido = $referido_row_fetch[0]['characterName'];
										
											$forma = $connection->prepare('SELECT obj_Id FROM characters WHERE char_name = :account LIMIT 1');
											$forma->execute(array(':account' => $name_referido));
											$forma_row_fetch = $forma->fetchAll();
											//print_r($forma_row_fetch);
											$ref_id = $forma_row_fetch[0]['obj_Id'];
										
											$sql_inv_update = $connection->prepare('INSERT INTO special_shop_balance_donations (characterId,balance) VALUES(:account,:count)');
											$sql_inv_update->execute(array(
											':account' => $ref_id,
											':count' => $donatecoinreward7
											));
											
																						
											$forma = $connection->prepare('SELECT balance FROM special_shop_balance_referidos WHERE characterId = :account LIMIT 1');
											$forma->execute(array(':account' => $ref_id));
											$forma_row_fetch = $forma->fetchAll();
											$balance_row_count = count($forma_row_fetch);
											if($balance_row_count>=1)
											{
												$balance_referido = $forma_row_fetch[0]['balance'];
												$balance_final = $balance_referido+$donatecoinreward7;
												
												$sql_inv_update = $connection->prepare('REPLACE INTO special_shop_balance_referidos (characterId,balance) VALUES(:account,:count)');
												$sql_inv_update->execute(array(
												':account' => $ref_id,
												':count' => $balance_final
												));
												
											}
											else
											{
												$sql_inv_update = $connection->prepare('REPLACE INTO special_shop_balance_referidos (characterId,balance) VALUES(:account,:count)');
												$sql_inv_update->execute(array(
												':account' => $ref_id,
												':count' => $donatecoinreward7
												));
												
												
											}
										
									}
									
									$sql_inv_update0 = $connection->prepare('update recaudado AS r set amount=r.amount+'.$donatecoinreward6);
									$sql_inv_update0->execute(array());
							} 
						catch(PDOException $e)
							{
								// Local file reporting.
								// Logging: file location.
								$local_log_file = $log_location_ipn;

								// Logging: Timestamp.
								$local_log = '['.date('m/d/Y g:i A').'] - ';

								// Logging: response from the server.
								$local_log .= "IPN COINS IV ERROR: ". $e->getMessage();
								$local_log .= '</td></tr><tr><td>';

								// Write to log.
								$fp=fopen($local_log_file,'a');
								fwrite($fp, $local_log . "\n");

								// Close file.
								fclose($fp);  
							}
					
					}
				}
				else
				{
					// Local file reporting.
					// Logging: file location.
					$local_log_file = $log_location_ipn;

					// Logging: Timestamp.
					$local_log = '['.date('m/d/Y g:i A').'] - ';

					// Logging: response from the server.
					$local_log .= "IPN COINS IV ERROR: Someone tried to enter coins option 4 while disabled in config. Exploit attack ? Charname: ". @$charname ." amount:". @$amount ." donation option:". @$donation_option ."Transaction ID:". @$transid;
					$local_log .= '</td></tr><tr><td>';

					// Write to log.
					$fp=fopen($local_log_file,'a');
					fwrite($fp, $local_log . "\n");

					// Close file.
					fclose($fp);
				}
			}
		if ($amount == $donatecoinamount8)
		{
			// Checks if coins option 4 is enabled in the config or else make a log.
			if ($coins8_enabled == true)
			{

				{

						try {
								// We will update the invertory.
								/*$sql_inv_update = $connection->prepare('UPDATE acp_players SET balance = balance+:donatecoinreward5 WHERE account = :charId');
								$sql_inv_update->execute(array(
								':donatecoinreward5' => $donatecoinreward5,
								':charId' => $charId
								));*/
								
									/*$balance=0;
									$charid_row = $connection->prepare('SELECT balance FROM special_shop_balance WHERE characterId = :charname LIMIT 1');
									$charid_row->execute(array(':charname' => $charId));
									$charid_row_fetch = $charid_row->fetchAll();
									$balance = $charid_row_fetch[0]['balance'];
									$balance_final = $donatecoinreward5*100+$balance;*/
									
									
													$charid_row = $connection->prepare('SELECT obj_Id,account_name FROM characters WHERE char_name = :charname LIMIT 1');
									$charid_row->execute(array(':charname' => $charname));
									$charid_row_fetch = $charid_row->fetchAll();
									$charId = $charid_row_fetch[0]['obj_Id'];
									$account_name = $charid_row_fetch[0]['account_name'];
			

									$sql_inv_update = $connection->prepare('INSERT INTO special_shop_balance_donations (characterId,balance) VALUES(:account,:count)');
									$sql_inv_update->execute(array(
									':account' => $charId,
									':count' => $donatecoinreward8*100
									));
									
									$referido = $connection->prepare('SELECT characterName FROM referidos WHERE account = :account LIMIT 1');
									$referido->execute(array(':account' => $account_name));
									$referido_row_fetch = $referido->fetchAll();
									$referido_row_count = count($referido_row_fetch);
									if($referido_row_count==1)
									{

										$name_referido = $referido_row_fetch[0]['characterName'];
										
											$forma = $connection->prepare('SELECT obj_Id FROM characters WHERE char_name = :account LIMIT 1');
											$forma->execute(array(':account' => $name_referido));
											$forma_row_fetch = $forma->fetchAll();
											//print_r($forma_row_fetch);
											$ref_id = $forma_row_fetch[0]['obj_Id'];
										
											$sql_inv_update = $connection->prepare('INSERT INTO special_shop_balance_donations (characterId,balance) VALUES(:account,:count)');
											$sql_inv_update->execute(array(
											':account' => $ref_id,
											':count' => $donatecoinreward8
											));
											
																						
											$forma = $connection->prepare('SELECT balance FROM special_shop_balance_referidos WHERE characterId = :account LIMIT 1');
											$forma->execute(array(':account' => $ref_id));
											$forma_row_fetch = $forma->fetchAll();
											$balance_row_count = count($forma_row_fetch);
											if($balance_row_count>=1)
											{
												$balance_referido = $forma_row_fetch[0]['balance'];
												$balance_final = $balance_referido+$donatecoinreward8;
												
												$sql_inv_update = $connection->prepare('REPLACE INTO special_shop_balance_referidos (characterId,balance) VALUES(:account,:count)');
												$sql_inv_update->execute(array(
												':account' => $ref_id,
												':count' => $balance_final
												));
												
											}
											else
											{
												$sql_inv_update = $connection->prepare('REPLACE INTO special_shop_balance_referidos (characterId,balance) VALUES(:account,:count)');
												$sql_inv_update->execute(array(
												':account' => $ref_id,
												':count' => $donatecoinreward8
												));
												
												
											}
										
									}
									
									$sql_inv_update0 = $connection->prepare('update recaudado AS r set amount=r.amount+'.$donatecoinreward6);
									$sql_inv_update0->execute(array());
							} 
						catch(PDOException $e)
							{
								// Local file reporting.
								// Logging: file location.
								$local_log_file = $log_location_ipn;

								// Logging: Timestamp.
								$local_log = '['.date('m/d/Y g:i A').'] - ';

								// Logging: response from the server.
								$local_log .= "IPN COINS IV ERROR: ". $e->getMessage();
								$local_log .= '</td></tr><tr><td>';

								// Write to log.
								$fp=fopen($local_log_file,'a');
								fwrite($fp, $local_log . "\n");

								// Close file.
								fclose($fp);  
							}
					
					}
				}
				else
				{
					// Local file reporting.
					// Logging: file location.
					$local_log_file = $log_location_ipn;

					// Logging: Timestamp.
					$local_log = '['.date('m/d/Y g:i A').'] - ';

					// Logging: response from the server.
					$local_log .= "IPN COINS IV ERROR: Someone tried to enter coins option 4 while disabled in config. Exploit attack ? Charname: ". @$charname ." amount:". @$amount ." donation option:". @$donation_option ."Transaction ID:". @$transid;
					$local_log .= '</td></tr><tr><td>';

					// Write to log.
					$fp=fopen($local_log_file,'a');
					fwrite($fp, $local_log . "\n");

					// Close file.
					fclose($fp);
				}
			}
		}
		// Else charname does not exists.
		else
			{
				// Local file reporting.
				// Logging: file location.
				$local_log_file = $log_location_ipn;

				// Logging: Timestamp.
				$local_log = '['.date('m/d/Y g:i A').'] - ';

				// Logging: response from the server.
				$local_log .= "IPN COINS ERROR: Charname does not exists ? Charname: ". @$charname ." amount:". @$amount ." donation option:". @$donation_option ."Transaction ID:". @$transid;
				$local_log .= '</td></tr><tr><td>';

				// Write to log.
				$fp=fopen($local_log_file,'a');
				fwrite($fp, $local_log . "\n");

				// Close file.
				fclose($fp);
			}
	}
	// Else Someone tried to enter coins while disabled.
	else
	{
		// Local file reporting.
		// Logging: file location.
		$local_log_file = $log_location_ipn;

		// Logging: Timestamp.
		$local_log = '['.date('m/d/Y g:i A').'] - ';

		// Logging: response from the server.
		$local_log .= "IPN COINS ERROR: Someone tried to enter coins while disabled in config. Exploit attack ? Charname: ". @$charname ." amount:". @$amount ." donation option:". @$donation_option ."Transaction ID:". @$transid;
		$local_log .= '</td></tr><tr><td>';

		// Write to log.
		$fp=fopen($local_log_file,'a');
		fwrite($fp, $local_log . "\n");

		// Close file.
		fclose($fp);
	}
}
// REMOVE KARMA DONATION OPTIONS.
if ($donation_option === $donation_option2)
	{
	// Checks if karma is enabled in the config or else log this maby a exploit attack.
	if ($karma_enabled == true)
	{
		// Checks if charname exists.
		if ($charId>0)
			{
				// Donate karma reward I.
				if ($amount == $donatekarmaamount1)
				{
					// Checks if karma option 1 is enabled in the config or else make a log.
					if ($karma1_enabled == true)
					{
					try {
							// How mutch karma on character.
							$karma_amount = $connection->prepare('SELECT karma FROM characters WHERE char_name = :charname');
							$karma_amount->execute(array(
							':charname' => $charname
							));
							$karma_amount_fetch = $karma_amount->fetchAll();
							
							// Karma minus $donateremovekarma1.
							$calc_karma = $karma_amount_fetch[0]['karma'] - $donateremovekarma1;
							
						// Check if karma is greater  or equal to $donateremovekarma1.
						if ($karma_amount_fetch[0]['karma'] >= $donateremovekarma1)
							{
								// We will update the new karma amount.
								$sql_stat_update = $connection->prepare('UPDATE characters SET karma = :calc_karma WHERE char_name = :charname');
								$sql_stat_update->execute(array(
								':calc_karma' => $calc_karma,
								':charname' => $charname
								));
							}
						// Player got less karma then hes trying to remove we set karma to 0.
						else
							{
								// We will set karma to 0.
								$karma_zero = 0;
								$sql_stat_update = $connection->prepare('UPDATE characters SET karma = :karma_zero WHERE char_name = :charname');
								$sql_stat_update->execute(array(
								':karma_zero' => $karma_zero,
								':charname' => $charname
								));
							}
						} 
					catch(PDOException $e) 
						{
							// Local file reporting.
							// Logging: file location.
							$local_log_file = $log_location_ipn;

							// Logging: Timestamp.
							$local_log = '['.date('m/d/Y g:i A').'] - ';

							// Logging: response from the server.
							$local_log .= "IPN KARMA I ERROR: ". $e->getMessage();
							$local_log .= '</td></tr><tr><td>';

							// Write to log.
							$fp=fopen($local_log_file,'a');
							fwrite($fp, $local_log . "\n");

							// Close file.
							fclose($fp);
						}
					}
					// Else Someone tried to enter karma 1 while disabled.
					else
					{
						// Local file reporting.
						// Logging: file location.
						$local_log_file = $log_location_ipn;

						// Logging: Timestamp.
						$local_log = '['.date('m/d/Y g:i A').'] - ';

						// Logging: response from the server.
						$local_log .= "IPN KARMA I ERROR: Someone tried to enter karma option 1 while disabled in config. Exploit attack ? Charname: ". @$charname ." amount:". @$amount ." donation option:". @$donation_option ."Transaction ID:". @$transid;
						$local_log .= '</td></tr><tr><td>';

						// Write to log.
						$fp=fopen($local_log_file,'a');
						fwrite($fp, $local_log . "\n");

						// Close file.
						fclose($fp);
					}
				}
				// Donate karma reward II.
				if ($amount == $donatekarmaamount2)
				{
					// Checks if karma option 2 is enabled in the config or else make a log.
					if ($karma2_enabled == true)
					{
					try {
							// How mutch karma on character.
							$karma_amount = $connection->prepare('SELECT karma FROM characters WHERE char_name = :charname');
							$karma_amount->execute(array(
							':charname' => $charname
							));
							$karma_amount_fetch = $karma_amount->fetchAll();

							// Karma minus $donateremovekarma2.
							$calc_karma = $karma_amount_fetch[0]['karma'] - $donateremovekarma2;

						// Check if karma is greater  or equal to $donateremovekarma2.
						if ($karma_amount_fetch[0]['karma'] >= $donateremovekarma2)
							{
								// We will update the new karma amount.
								$sql_stat_update = $connection->prepare('UPDATE characters SET karma = :calc_karma WHERE char_name = :charname');
								$sql_stat_update->execute(array(
								':calc_karma' => $calc_karma,
								':charname' => $charname
								));
							}
						// Player got less karma then hes trying to remove we set karma to 0.
						else
							{
								// We will set karma to 0
								$karma_zero = 0;
								$sql_stat_update = $connection->prepare('UPDATE characters SET karma = :karma_zero WHERE char_name = :charname');
								$sql_stat_update->execute(array(
								':karma_zero' => $karma_zero,
								':charname' => $charname
								));
							}
						} 
					catch(PDOException $e) 
						{
							// Local file reporting.
							// Logging: file location.
							$local_log_file = $log_location_ipn;

							// Logging: Timestamp.
							$local_log = '['.date('m/d/Y g:i A').'] - ';

							// Logging: response from the server.
							$local_log .= "IPN KARMA II ERROR: ". $e->getMessage();
							$local_log .= '</td></tr><tr><td>';

							// Write to log.
							$fp=fopen($local_log_file,'a');
							fwrite($fp, $local_log . "\n");

							// Close file.
							fclose($fp);
						}
					}
					// Else Someone tried to enter karma 2 while disabled.
					else
					{
						// Local file reporting.
						// Logging: file location.
						$local_log_file = $log_location_ipn;

						// Logging: Timestamp.
						$local_log = '['.date('m/d/Y g:i A').'] - ';

						// Logging: response from the server.
						$local_log .= "IPN KARMA II ERROR: Someone tried to enter karma option 2 while disabled in config. Exploit attack ? Charname: ". @$charname ." amount:". @$amount ." donation option:". @$donation_option ."Transaction ID:". @$transid;
						$local_log .= '</td></tr><tr><td>';

						// Write to log.
						$fp=fopen($local_log_file,'a');
						fwrite($fp, $local_log . "\n");

						// Close file.
						fclose($fp);
					}
				}
				// Donate karma reward III.
				if ($amount == $donatekarmaamount3)
				{
					// Checks if karma option 3 is enabled in the config or else make a log.
					if ($karma3_enabled == true)
					{
					try {
							// How mutch karma on character.
							$karma_amount = $connection->prepare('SELECT karma FROM characters WHERE char_name = :charname');
							$karma_amount->execute(array(
							':charname' => $charname
							));
							$karma_amount_fetch = $karma_amount->fetchAll();

							// Karma minus $donateremovekarma3.
							$calc_karma = $karma_amount_fetch[0]['karma'] - $donateremovekarma3;

						// Check if karma is greater  or equal to $donateremovekarma3.
						if ($karma_amount_fetch[0]['karma'] >= $donateremovekarma3)
							{
								// We will update the new karma amount.
								$sql_stat_update = $connection->prepare('UPDATE characters SET karma = :calc_karma WHERE char_name = :charname');
								$sql_stat_update->execute(array(
								':calc_karma' => $calc_karma,
								':charname' => $charname
								));
							}
						// Player got less karma then hes trying to remove we set karma to 0.
						else
							{
								// We will set karma to 0.
								$karma_zero = 0;
								$sql_stat_update = $connection->prepare('UPDATE characters SET karma = :karma_zero WHERE char_name = :charname');
								$sql_stat_update->execute(array(
								':karma_zero' => $karma_zero,
								':charname' => $charname
								));
							}
						} 
					catch(PDOException $e) 
						{
							// Local file reporting.
							// Logging: file location.
							$local_log_file = $log_location_ipn;

							// Logging: Timestamp.
							$local_log = '['.date('m/d/Y g:i A').'] - ';

							// Logging: response from the server.
							$local_log .= "IPN KARMA III ERROR: ". $e->getMessage();
							$local_log .= '</td></tr><tr><td>';

							// Write to log.
							$fp=fopen($local_log_file,'a');
							fwrite($fp, $local_log . "\n");

							// Close file.
							fclose($fp);
						}
					}
					// Else Someone tried to enter karma 3 while disabled.
					else
					{
						// Local file reporting.
						// Logging: file location.
						$local_log_file = $log_location_ipn;

						// Logging: Timestamp.
						$local_log = '['.date('m/d/Y g:i A').'] - ';

						// Logging: response from the server.
						$local_log .= "IPN KARMA III ERROR: Someone tried to enter karma option 3 while disabled in config. Exploit attack ? Charname: ". @$charname ." amount:". @$amount ." donation option:". @$donation_option ."Transaction ID:". @$transid;
						$local_log .= '</td></tr><tr><td>';

						// Write to log.
						$fp=fopen($local_log_file,'a');
						fwrite($fp, $local_log . "\n");

						// Close file.
						fclose($fp);
					}
				}
				// Donate karma reward IV.
				if ($amount == $donatekarmaallamount)
				{
					// Checks if karma option 4 is enabled in the config or else make a log.
					if ($karma4_enabled == true)
					{
					try {
							// We will set karma to 0.
							$karma_zero = 0;
							$sql_stat_update = $connection->prepare('UPDATE characters SET karma = :karma_zero WHERE char_name = :charname');
							$sql_stat_update->execute(array(
							':karma_zero' => $karma_zero,
							':charname' => $charname
							));
						}
					catch(PDOException $e) 
						{
							// Local file reporting.
							// Logging: file location.
							$local_log_file = $log_location_ipn;

							// Logging: Timestamp.
							$local_log = '['.date('m/d/Y g:i A').'] - ';

							// Logging: response from the server.
							$local_log .= "IPN KARMA IV ERROR: ". $e->getMessage();
							$local_log .= '</td></tr><tr><td>';

							// Write to log
							$fp=fopen($local_log_file,'a');
							fwrite($fp, $local_log . "\n");

							// Close file
							fclose($fp);
						}
					}
					// Else Someone tried to enter karma 4 while disabled.
					else
					{
						// Local file reporting.
						// Logging: file location.
						$local_log_file = $log_location_ipn;

						// Logging: Timestamp
						$local_log = '['.date('m/d/Y g:i A').'] - ';

						// Logging: response from the server.
						$local_log .= "IPN KARMA IV ERROR: Someone tried to enter karma option 4 while disabled in config. Exploit attack ? Charname: ". @$charname ." amount:". @$amount ." donation option:". @$donation_option ."Transaction ID:". @$transid;
						$local_log .= '</td></tr><tr><td>';

						// Write to log.
						$fp=fopen($local_log_file,'a');
						fwrite($fp, $local_log . "\n");

						// Close file.
						fclose($fp);
					}
				}
			}
				// Else charname does not exists.
				else
				{
					// Local file reporting.
					// Logging: file location.
					$local_log_file = $log_location_ipn;

					// Logging: Timestamp.
					$local_log = '['.date('m/d/Y g:i A').'] - ';

					// Logging: response from the server.
					$local_log .= "IPN KARMA ERROR: Charname does not exists ? Charname: ". @$charname ." amount:". @$amount ." donation option:". @$donation_option ."Transaction ID:". @$transid;
					$local_log .= '</td></tr><tr><td>';

					// Write to log.
					$fp=fopen($local_log_file,'a');
					fwrite($fp, $local_log . "\n");

					// Close file.
					fclose($fp);
				}
		}
		// Else Someone tried to enter karma while disabled.
		else
		{
			// Local file reporting.
			// Logging: file location.
			$local_log_file = $log_location_ipn;

			// Logging: Timestamp.
			$local_log = '['.date('m/d/Y g:i A').'] - ';

			// Logging: response from the server.
			$local_log .= "IPN KARMA ERROR: Someone tried to enter karma while disabled in config. Exploit attack ? Charname: ". @$charname ." amount:". @$amount ." donation option:". @$donation_option ."Transaction ID:". @$transid;
			$local_log .= '</td></tr><tr><td>';

			// Write to log.
			$fp=fopen($local_log_file,'a');
			fwrite($fp, $local_log . "\n");

			// Close file.
			fclose($fp);
		}
	
	}

// REMOVE PK POINTS DONATION OPTIONS.
if ($donation_option === $donation_option3)
	{
	// Checks if PK Points is enabled in the config or else log this maby a exploit attack.
	if ($pkpoints_enabled == true)
	{
		// Checks if charname exists.
		if ($charId>0)
			{
				// Donate PK Points reward I.
				if ($amount == $donatepkamount1)
				{
					// Checks if option pk points 1 is enabled in the config or else make a log.
					if ($pkpoints1_enabled == true)
					{
					try {
							// How mutch PK Points on character.
							$pk_points_amount = $connection->prepare('SELECT pkkills FROM characters WHERE char_name = :charname');
							$pk_points_amount->execute(array(
							':charname' => $charname
							));
							$pk_amount_fetch = $pk_points_amount->fetchAll();

							// PK Points minus $donateremovepk1.
							$calc_pkkills = $pk_amount_fetch[0]['pkkills'] - $donateremovepk1;

						// Check if PK Points is greater  or equal to $donateremovepk1.
						if ($pk_amount_fetch[0]['pkkills'] >= $donateremovepk1)
							{
								// We will update the new PK Points amount.
								$sql_stat_update = $connection->prepare('UPDATE characters SET pkkills = :calc_pkkills WHERE char_name = :charname');
								$sql_stat_update->execute(array(
								':calc_pkkills' => $calc_pkkills,
								':charname' => $charname
								));
							}
						// Player got less PK Points then hes trying to remove we set PK Points to 0.
						else
							{
								// We will set pkkills to 0.
								$pkkills_zero = 0;
								$sql_stat_update = $connection->prepare('UPDATE characters SET pkkills = :pkkills_zero WHERE char_name = :charname');
								$sql_stat_update->execute(array(
								':pkkills_zero' => $pkkills_zero,
								':charname' => $charname
								));
							}
						} 
					catch(PDOException $e) 
						{
							// Local file reporting.
							// Logging: file location.
							$local_log_file = $log_location_ipn;

							// Logging: Timestamp.
							$local_log = '['.date('m/d/Y g:i A').'] - ';

							// Logging: response from the server.
							$local_log .= "IPN PK POINTS I ERROR: ". $e->getMessage();
							$local_log .= '</td></tr><tr><td>';

							// Write to log.
							$fp=fopen($local_log_file,'a');
							fwrite($fp, $local_log . "\n");

							// Close file.
							fclose($fp);
						}
					}
					// Else Someone tried to enter pk points 1 while disabled.
					else
					{
						// Local file reporting.
						// Logging: file location.
						$local_log_file = $log_location_ipn;

						// Logging: Timestamp.
						$local_log = '['.date('m/d/Y g:i A').'] - ';

						// Logging: response from the server.
						$local_log .= "IPN PK POINTS I ERROR: Someone tried to enter PK Points option 1 while disabled in config. Exploit attack ? Charname: ". @$charname ." amount:". @$amount ." donation option:". @$donation_option ."Transaction ID:". @$transid;
						$local_log .= '</td></tr><tr><td>';

						// Write to log.
						$fp=fopen($local_log_file,'a');
						fwrite($fp, $local_log . "\n");

						// Close file.
						fclose($fp);
					}
				}
				// Donate PK Points reward II.
				if ($amount == $donatepkamount2)
				{
					// Checks if option pk points 2 is enabled in the config or else make a log.
					if ($pkpoints2_enabled == true)
					{
					try {
							// How mutch PK Points on character.
							$pk_points_amount = $connection->prepare('SELECT pkkills FROM characters WHERE char_name = :charname');
							$pk_points_amount->execute(array(
							':charname' => $charname
							));
							$pk_amount_fetch = $pk_points_amount->fetchAll();

							// PK Points minus $donateremovepk2.
							$calc_pkkills = $pk_amount_fetch[0]['pkkills'] - $donateremovepk2;

						// Check if PK Points is greater  or equal to $donateremovepk2.
						if ($pk_amount_fetch[0]['pkkills'] >= $donateremovepk2)
							{
								// We will update the new PK Points amount.
								$sql_stat_update = $connection->prepare('UPDATE characters SET pkkills = :calc_pkkills WHERE char_name = :charname');
								$sql_stat_update->execute(array(
								':calc_pkkills' => $calc_pkkills,
								':charname' => $charname
								));
							}
						// Player got less PK Points then hes trying to remove we set PK Points to 0.
						else
							{
								// We will set pkkills to 0.
								$pkkills_zero = 0;
								$sql_stat_update = $connection->prepare('UPDATE characters SET pkkills = :pkkills_zero WHERE char_name = :charname');
								$sql_stat_update->execute(array(
								':pkkills_zero' => $pkkills_zero,
								':charname' => $charname
								));
							}
						}
					catch(PDOException $e) 
						{
							// Local file reporting.
							// Logging: file location.
							$local_log_file = $log_location_ipn;

							// Logging: Timestamp.
							$local_log = '['.date('m/d/Y g:i A').'] - ';

							// Logging: response from the server.
							$local_log .= "IPN PK POINTS II ERROR: ". $e->getMessage();
							$local_log .= '</td></tr><tr><td>';

							// Write to log.
							$fp=fopen($local_log_file,'a');
							fwrite($fp, $local_log . "\n");

							// Close file.
							fclose($fp);
						}
					}
					// Else Someone tried to enter pk points 2 while disabled.
					else
					{
						// Local file reporting.
						// Logging: file location.
						$local_log_file = $log_location_ipn;

						// Logging: Timestamp
						$local_log = '['.date('m/d/Y g:i A').'] - ';

						// Logging: response from the server.
						$local_log .= "IPN PK POINTS II ERROR: Someone tried to enter PK Points option 2 while disabled in config. Exploit attack ? Charname: ". @$charname ." amount:". @$amount ." donation option:". @$donation_option ."Transaction ID:". @$transid;
						$local_log .= '</td></tr><tr><td>';

						// Write to log.
						$fp=fopen($local_log_file,'a');
						fwrite($fp, $local_log . "\n");

						// Close file.
						fclose($fp);
					}
				}
				// Donate PK Points reward III.
				if ($amount == $donatepkamount3)
				{
					// Checks if option pk points 3 is enabled in the config or else make a log.
					if ($pkpoints3_enabled == true)
					{
					try {
							// How mutch PK Points on character.
							$pk_points_amount = $connection->prepare('SELECT pkkills FROM characters WHERE char_name = :charname');
							$pk_points_amount->execute(array(
							':charname' => $charname
							));
							$pk_amount_fetch = $pk_points_amount->fetchAll();

							// PK Points minus $donateremovepk3.
							$calc_pkkills = $pk_amount_fetch[0]['pkkills'] - $donateremovepk3;

						// Check if PK Points is greater  or equal to $donateremovepk3.
						if ($pk_amount_fetch[0]['pkkills'] >= $donateremovepk3)
							{
								// We will update the new PK Points amount.
								$sql_stat_update = $connection->prepare('UPDATE characters SET pkkills = :calc_pkkills WHERE char_name = :charname');
								$sql_stat_update->execute(array(
								':calc_pkkills' => $calc_pkkills,
								':charname' => $charname
								));
							}
						// Player got less PK Points then hes trying to remove we set PK Points to 0.
						else
							{
								// We will set pkkills to 0
								$pkkills_zero = 0;
								$sql_stat_update = $connection->prepare('UPDATE characters SET pkkills = :pkkills_zero WHERE char_name = :charname');
								$sql_stat_update->execute(array(
								':pkkills_zero' => $pkkills_zero,
								':charname' => $charname
								));
							}
						} 
					catch(PDOException $e) 
						{
							// Local file reporting.
							// Logging: file location.
							$local_log_file = $log_location_ipn;

							// Logging: Timestamp.
							$local_log = '['.date('m/d/Y g:i A').'] - ';

							// Logging: response from the server.
							$local_log .= "IPN PK POINTS III ERROR: ". $e->getMessage();
							$local_log .= '</td></tr><tr><td>';

							// Write to log.
							$fp=fopen($local_log_file,'a');
							fwrite($fp, $local_log . "\n");

							// Close file.
							fclose($fp);
						}
					}
					// Else Someone tried to enter pk points 3 while disabled.
					else
					{
						// Local file reporting.
						// Logging: file location.
						$local_log_file = $log_location_ipn;

						// Logging: Timestamp.
						$local_log = '['.date('m/d/Y g:i A').'] - ';

						// Logging: response from the server.
						$local_log .= "IPN PK POINTS III ERROR: Someone tried to enter PK Points option 3 while disabled in config. Exploit attack ? Charname: ". @$charname ." amount:". @$amount ." donation option:". @$donation_option ."Transaction ID:". @$transid;
						$local_log .= '</td></tr><tr><td>';

						// Write to log.
						$fp=fopen($local_log_file,'a');
						fwrite($fp, $local_log . "\n");

						// Close file.
						fclose($fp);
					}
				}
				// Donate PK Points reward IV.
				if ($amount == $donatekarmaallamount)
				{
					// Checks if option pk points 4 is enabled in the config or else make a log.
					if ($pkpoints4_enabled == true)
					{
					try {
								// We will set pkkills to 0.
								$pkkills_zero = 0;
								$sql_stat_update = $connection->prepare('UPDATE characters SET pkkills = :pkkills_zero WHERE char_name = :charname');
								$sql_stat_update->execute(array(
								':pkkills_zero' => $pkkills_zero,
								':charname' => $charname
								));
						}
					catch(PDOException $e)
						{
							// Local file reporting.
							// Logging: file location.
							$local_log_file = $log_location_ipn;

							// Logging: Timestamp.
							$local_log = '['.date('m/d/Y g:i A').'] - ';

							// Logging: response from the server.
							$local_log .= "IPN PK POINTS IV ERROR: ". $e->getMessage();
							$local_log .= '</td></tr><tr><td>';

							// Write to log.
							$fp=fopen($local_log_file,'a');
							fwrite($fp, $local_log . "\n");

							// Close file.
							fclose($fp);
						}
					}
					// Else Someone tried to enter pk points 4 while disabled.
					else
					{
						// Local file reporting.
						// Logging: file location.
						$local_log_file = $log_location_ipn;

						// Logging: Timestamp.
						$local_log = '['.date('m/d/Y g:i A').'] - ';

						// Logging: response from the server.
						$local_log .= "IPN PK POINTS IV ERROR: Someone tried to enter PK Points option 4 while disabled in config. Exploit attack ? Charname: ". @$charname ." amount:". @$amount ." donation option:". @$donation_option ."Transaction ID:". @$transid;
						$local_log .= '</td></tr><tr><td>';

						// Write to log.
						$fp=fopen($local_log_file,'a');
						fwrite($fp, $local_log . "\n");

						// Close file.
						fclose($fp);
					}
				}
			}
				// Else charname does not exists.
				else
				{
					// Local file reporting.
					// Logging: file location.
					$local_log_file = $log_location_ipn;

					// Logging: Timestamp.
					$local_log = '['.date('m/d/Y g:i A').'] - ';

					// Logging: response from the server.
					$local_log .= "IPN PK POINTS ERROR: Charname does not exists ? Charname: ". @$charname ." amount:". @$amount ." donation option:". @$donation_option ."Transaction ID:". @$transid;
					$local_log .= '</td></tr><tr><td>';

					// Write to log.
					$fp=fopen($local_log_file,'a');
					fwrite($fp, $local_log . "\n");

					// Close file.
					fclose($fp);
				}
			}
		// Else Someone tried to enter pk points 4 while disabled.
		else
		{
			// Local file reporting.
			// Logging: file location.
			$local_log_file = $log_location_ipn;

			// Logging: Timestamp.
			$local_log = '['.date('m/d/Y g:i A').'] - ';

			// Logging: response from the server.
			$local_log .= "IPN PK POINTS ERROR: Someone tried to enter PK Points option 4 while disabled in config. Exploit attack ? Charname: ". @$charname ." amount:". @$amount ." donation option:". @$donation_option ."Transaction ID:". @$transid;
			$local_log .= '</td></tr><tr><td>';

			// Write to log.
			$fp=fopen($local_log_file,'a');
			fwrite($fp, $local_log . "\n");

			// Close file.
			fclose($fp);
		}
	}

		// ENCHANT DONATION OPTIONS.
		if ($donation_option === $donation_option4)
		{
			// Checks if item enchant is enabled in the config or else log this.
			if ($enchant_item_enabled == true)
			{
			// Select item id FROM items WHERE owner id = char id AND loc = PAPERDOLL ( means its equipped )
					$loc_paper = 'PAPERDOLL';
					// Loc_data locations
					// Shirt
					$locdata0 = 0;
					// Helmet
					$locdata1 = 1;
					// Necklace
					$locdata4 = 4;
					// Weapon
					$locdata5 = 5;
					// Breastplate and full armor
					$locdata6 = 6;
					// Shield
					$locdata7 = 7;
					// Earring1
					$locdata8 = 8;
					// Earring2
					$locdata9 = 9;
					// Gloves
					$locdata10 = 10;
					// Leggings
					$locdata11 = 11;
					// Boots
					$locdata12 = 12;
					// Ring1
					$locdata13 = 13;
					// Ring2
					$locdata14 = 14;
					// Belt
					$locdata24 = 24;
				try {
						// Here we select the shirt item id.
						$char_shirt_select = $connection->prepare('SELECT item_id FROM items WHERE loc_data = ? AND owner_id = ? AND loc = ? LIMIT 1');
						$char_shirt_select->bindValue(1, $locdata0, PDO::PARAM_INT);
						$char_shirt_select->bindValue(2, $charId, PDO::PARAM_INT);
						$char_shirt_select->bindValue(3, $loc_paper, PDO::PARAM_STR);
						$char_shirt_select->execute();
						$char_shirt_fetch = $char_shirt_select->fetchAll();
						$char_shirt_id = @$char_shirt_fetch[0]['item_id'];

						// Here we select the current helmet item id.
						$char_helmet_select = $connection->prepare('SELECT item_id FROM items WHERE loc_data = ? AND owner_id = ? AND loc = ? LIMIT 1');
						$char_helmet_select->bindValue(1, $locdata1, PDO::PARAM_INT);
						$char_helmet_select->bindValue(2, $charId, PDO::PARAM_INT);
						$char_helmet_select->bindValue(3, $loc_paper, PDO::PARAM_STR);
						$char_helmet_select->execute();
						$char_helmet_fetch = $char_helmet_select->fetchAll();
						$char_helmet_id = @$char_helmet_fetch[0]['item_id'];

						// Here we select the necklace item id.
						$char_necklace_select = $connection->prepare('SELECT item_id FROM items WHERE loc_data = ? AND owner_id = ? AND loc = ? LIMIT 1');
						$char_necklace_select->bindValue(1, $locdata4, PDO::PARAM_INT);
						$char_necklace_select->bindValue(2, $charId, PDO::PARAM_INT);
						$char_necklace_select->bindValue(3, $loc_paper, PDO::PARAM_STR);
						$char_necklace_select->execute();
						$char_necklace_fetch = $char_necklace_select->fetchAll();
						$char_necklace_id = @$char_necklace_fetch[0]['item_id'];

						// Here we select the weapon item id.
						$char_weapon_select = $connection->prepare('SELECT item_id FROM items WHERE loc_data = ? AND owner_id = ? AND loc = ? LIMIT 1');
						$char_weapon_select->bindValue(1, $locdata5, PDO::PARAM_INT);
						$char_weapon_select->bindValue(2, $charId, PDO::PARAM_INT);
						$char_weapon_select->bindValue(3, $loc_paper, PDO::PARAM_STR);
						$char_weapon_select->execute();
						$char_weapon_fetch = $char_weapon_select->fetchAll();
						$char_weapon_id = @$char_weapon_fetch[0]['item_id'];

						// Here we select the breastplate and full armor item id.
						$char_breastplate_full_select = $connection->prepare('SELECT item_id FROM items WHERE loc_data = ? AND owner_id = ? AND loc = ? LIMIT 1');
						$char_breastplate_full_select->bindValue(1, $locdata6, PDO::PARAM_INT);
						$char_breastplate_full_select->bindValue(2, $charId, PDO::PARAM_INT);
						$char_breastplate_full_select->bindValue(3, $loc_paper, PDO::PARAM_STR);
						$char_breastplate_full_select->execute();
						$char_breastplate_full_fetch = $char_breastplate_full_select->fetchAll();
						$char_breastplate_full_id = @$char_breastplate_full_fetch[0]['item_id'];

						// Here we select the shield item id.
						$char_shield_select = $connection->prepare('SELECT item_id FROM items WHERE loc_data = ? AND owner_id = ? AND loc = ? LIMIT 1');
						$char_shield_select->bindValue(1, $locdata7, PDO::PARAM_INT);
						$char_shield_select->bindValue(2, $charId, PDO::PARAM_INT);
						$char_shield_select->bindValue(3, $loc_paper, PDO::PARAM_STR);
						$char_shield_select->execute();
						$char_shield_fetch = $char_shield_select->fetchAll();
						$char_shield_id = @$char_shield_fetch[0]['item_id'];

						// Here we select the earring1 item id.
						$char_lowearring_select = $connection->prepare('SELECT item_id FROM items WHERE loc_data = ? AND owner_id = ? AND loc = ? LIMIT 1');
						$char_lowearring_select->bindValue(1, $locdata8, PDO::PARAM_INT);
						$char_lowearring_select->bindValue(2, $charId, PDO::PARAM_INT);
						$char_lowearring_select->bindValue(3, $loc_paper, PDO::PARAM_STR);
						$char_lowearring_select->execute();
						$char_lowearring_fetch = $char_lowearring_select->fetchAll();
						$char_lowearring_id = @$char_lowearring_fetch[0]['item_id'];

						// Here we select the earring2 item id.
						$char_upearring_select = $connection->prepare('SELECT item_id FROM items WHERE loc_data = ? AND owner_id = ? AND loc = ? LIMIT 1');
						$char_upearring_select->bindValue(1, $locdata9, PDO::PARAM_INT);
						$char_upearring_select->bindValue(2, $charId, PDO::PARAM_INT);
						$char_upearring_select->bindValue(3, $loc_paper, PDO::PARAM_STR);
						$char_upearring_select->execute();
						$char_upearring_fetch = $char_upearring_select->fetchAll();
						$char_upearring_id = @$char_upearring_fetch[0]['item_id'];

						// Here we select the gloves item id.
						$char_gloves_select = $connection->prepare('SELECT item_id FROM items WHERE loc_data = ? AND owner_id = ? AND loc = ? LIMIT 1');
						$char_gloves_select->bindValue(1, $locdata10, PDO::PARAM_INT);
						$char_gloves_select->bindValue(2, $charId, PDO::PARAM_INT);
						$char_gloves_select->bindValue(3, $loc_paper, PDO::PARAM_STR);
						$char_gloves_select->execute();
						$char_gloves_fetch = $char_gloves_select->fetchAll();
						$char_gloves_id = @$char_gloves_fetch[0]['item_id'];

						// Here we select the leggings item id.
						$char_leggings_select = $connection->prepare('SELECT item_id FROM items WHERE loc_data = ? AND owner_id = ? AND loc = ? LIMIT 1');
						$char_leggings_select->bindValue(1, $locdata11, PDO::PARAM_INT);
						$char_leggings_select->bindValue(2, $charId, PDO::PARAM_INT);
						$char_leggings_select->bindValue(3, $loc_paper, PDO::PARAM_STR);
						$char_leggings_select->execute();
						$char_leggings_fetch = $char_leggings_select->fetchAll();
						$char_leggings_id = @$char_leggings_fetch[0]['item_id'];

						// Here we select the boots item id.
						$char_boots_select = $connection->prepare('SELECT item_id FROM items WHERE loc_data = ? AND owner_id = ? AND loc = ? LIMIT 1');
						$char_boots_select->bindValue(1, $locdata12, PDO::PARAM_INT);
						$char_boots_select->bindValue(2, $charId, PDO::PARAM_INT);
						$char_boots_select->bindValue(3, $loc_paper, PDO::PARAM_STR);
						$char_boots_select->execute();
						$char_boots_fetch = $char_boots_select->fetchAll();
						$char_boots_id = @$char_boots_fetch[0]['item_id'];

						// Here we select the ring1 item id.
						$char_lowring_select = $connection->prepare('SELECT item_id FROM items WHERE loc_data = ? AND owner_id = ? AND loc = ? LIMIT 1');
						$char_lowring_select->bindValue(1, $locdata13, PDO::PARAM_INT);
						$char_lowring_select->bindValue(2, $charId, PDO::PARAM_INT);
						$char_lowring_select->bindValue(3, $loc_paper, PDO::PARAM_STR);
						$char_lowring_select->execute();
						$char_lowring_fetch = $char_lowring_select->fetchAll();
						$char_lowring_id = @$char_lowring_fetch[0]['item_id'];

						// Here we select the ring2 item id.
						$char_upring_select = $connection->prepare('SELECT item_id FROM items WHERE loc_data = ? AND owner_id = ? AND loc = ? LIMIT 1');
						$char_upring_select->bindValue(1, $locdata14, PDO::PARAM_INT);
						$char_upring_select->bindValue(2, $charId, PDO::PARAM_INT);
						$char_upring_select->bindValue(3, $loc_paper, PDO::PARAM_STR);
						$char_upring_select->execute();
						$char_upring_fetch = $char_upring_select->fetchAll();
						$char_upring_id = @$char_upring_fetch[0]['item_id'];

						// Here we select the belt item id.
						$char_belt_select = $connection->prepare('SELECT item_id FROM items WHERE loc_data = ? AND owner_id = ? AND loc = ? LIMIT 1');
						$char_belt_select->bindValue(1, $locdata24, PDO::PARAM_INT);
						$char_belt_select->bindValue(2, $charId, PDO::PARAM_INT);
						$char_belt_select->bindValue(3, $loc_paper, PDO::PARAM_STR);
						$char_belt_select->execute();
						$char_belt_fetch = $char_belt_select->fetchAll();
						$char_belt_id = @$char_belt_fetch[0]['item_id'];
					} 
				catch(PDOException $e) 
					{
						// Local file reporting.
						// Logging: file location.
						$local_log_file = $log_location_ipn;

						// Logging: Timestamp.
						$local_log = '['.date('m/d/Y g:i A').'] - ';

						// Logging: response from the server.
						$local_log .= "IPN SHIRT ENCHANT ERROR: ". $e->getMessage();
						$local_log .= '</td></tr><tr><td>';

						// Write to log.
						$fp=fopen($local_log_file,'a');
						fwrite($fp, $local_log . "\n");

						// Close file.
						fclose($fp);
					}
				// Checks if charname exists.
				if ($charId>0)
				{
					// Donate enchant shirt.
					if ($donation_option_enc === $donation_enc_option1)
						{
						// Checks if player got a blocked item id equipped.
						if (in_array($char_shirt_id, $enc_item_blocked)) 
							{
								// Local file reporting.
								// Logging: file location.
								$local_log_file = $log_location_ipn;

								// Logging: Timestamp.
								$local_log = '['.date('m/d/Y g:i A').'] - ';

								// Logging: response from the server.
								$local_log .= "IPN SHIRT ENCHANT ERROR: Someone tried to enchant shirt while this item_id is disabled in config. Charname: ". @$charname ." amount:". @$amount ." donation option:". @$donation_option ."Transaction ID:". @$transid;
								$local_log .= '</td></tr><tr><td>';

								// Write to log.
								$fp=fopen($local_log_file,'a');
								fwrite($fp, $local_log . "\n");

								// Close file.
								fclose($fp);
							}
							else
							{
								// Checks if the correct amount is donated otherwise log this.
								if ($amount == $shirt_donate_amount)
									{
									// Checks if enchant shirt is enabled in the config or else make a log.
									if ($shirt_enchant_enabled == true)
										{
											try {
													// If player is offline we will add shirt enchant trough a mysql query.
													$sql_enchant_shirt = $connection->prepare('UPDATE items SET enchant_level= :shirt_enchant_amount WHERE loc_data= :locdata0 AND owner_id = :charId AND loc = :paperdoll ');
													$sql_enchant_shirt->execute(array(
													':shirt_enchant_amount' => $shirt_enchant_amount,
													':locdata0' => $locdata0,
													':charId' => $charId,
													':paperdoll' => $loc_paper
													));
												} 
											catch(PDOException $e) 
												{
													// Local file reporting.
													// Logging: file location.
													$local_log_file = $log_location_ipn;

													// Logging: Timestamp.
													$local_log = '['.date('m/d/Y g:i A').'] - ';

													// Logging: response from the server.
													$local_log .= "IPN SHIRT ENCHANT ERROR: ". $e->getMessage();
													$local_log .= '</td></tr><tr><td>';

													// Write to log.
													$fp=fopen($local_log_file,'a');
													fwrite($fp, $local_log . "\n");

													// Close file.
													fclose($fp);
												}
											}
											else
											{
												// Local file reporting.
												// Logging: file location.
												$local_log_file = $log_location_ipn;

												// Logging: Timestamp.
												$local_log = '['.date('m/d/Y g:i A').'] - ';

												// Logging: response from the server.
												$local_log .= "IPN SHIRT ENCHANT ERROR: Someone tried to enter shirt enchant while disabled in config. Exploit attack ? Charname: ". @$charname ." amount:". @$amount ." donation option:". @$donation_option ."Transaction ID:". @$transid;
												$local_log .= '</td></tr><tr><td>';

												// Write to log.
												$fp=fopen($local_log_file,'a');
												fwrite($fp, $local_log . "\n");

												// Close file.
												fclose($fp);
											}
										}
										else
										{
											// Local file reporting.
											// Logging: file location.
											$local_log_file = $log_location_ipn;

											// Logging: Timestamp.
											$local_log = '['.date('m/d/Y g:i A').'] - ';

											// Logging: response from the server.
											$local_log .= "IPN SHIRT EXPLOIT ENCHANT ATTACK: Someone tried to change the donation amount to get the donation for cheap Charname: ". @$charname ." amount:". @$amount ." donation option:". @$donation_option ."Transaction ID:". @$transid;
											$local_log .= '</td></tr><tr><td>';

											// Write to log.
											$fp=fopen($local_log_file,'a');
											fwrite($fp, $local_log . "\n");

											// Close file.
											fclose($fp);
										}
									}
								}
						// Donate enchant helmet.
						if ($donation_option_enc === $donation_enc_option2)
							{
							// Someone tried to enchant a blocked item id.
							if (in_array($char_helmet_id, $enc_item_blocked))
								{
									// Local file reporting.
									// Logging: file location.
									$local_log_file = $log_location_ipn;

									// Logging: Timestamp.
									$local_log = '['.date('m/d/Y g:i A').'] - ';

									// Logging: response from the server.
									$local_log .= "IPN HELMET ENCHANT ERROR: Someone tried to enchant helmet while this item_id is disabled in config. Charname: ". @$charname ." amount:". @$amount ." donation option:". @$donation_option ."Transaction ID:". @$transid;
									$local_log .= '</td></tr><tr><td>';

									// Write to log.
									$fp=fopen($local_log_file,'a');
									fwrite($fp, $local_log . "\n");

									// Close file.
									fclose($fp);
								}
								else
								{
								// Checks if the correct amount is donated otherwise log this.
								if ($amount == $helmet_donate_amount)
									{
									// Checks if enchant helmet is enabled in the config or else make a log.
									 if ($helmet_enchant_enabled == true)
										{
											try {
													// If player is offline we will add the helmet enchant trough a mysql query.
													$sql_enchant_helmet = $connection->prepare('UPDATE items SET enchant_level= :helmet_enchant_amount WHERE loc_data= :locdata1 AND owner_id = :charId AND loc = :paperdoll ');
													$sql_enchant_helmet->execute(array(
													':helmet_enchant_amount' => $helmet_enchant_amount,
													':locdata1' => $locdata1,
													':charId' => $charId,
													':paperdoll' => $loc_paper
													));
												} 
											catch(PDOException $e) 
												{
													// Local file reporting.
													// Logging: file location.
													$local_log_file = $log_location_ipn;

													// Logging: Timestamp.
													$local_log = '['.date('m/d/Y g:i A').'] - ';

													// Logging: response from the server.
													$local_log .= "IPN HELMET ENCHANT ERROR: ". $e->getMessage();
													$local_log .= '</td></tr><tr><td>';

													// Write to log.
													$fp=fopen($local_log_file,'a');
													fwrite($fp, $local_log . "\n");

													// Close file.
													fclose($fp);
												}
											}
											else
											{
												// Local file reporting.
												// Logging: file location.
												$local_log_file = $log_location_ipn;

												// Logging: Timestamp.
												$local_log = '['.date('m/d/Y g:i A').'] - ';

												// Logging: response from the server.
												$local_log .= "IPN HELMET ENCHANT ERROR: Someone tried to enter helmet enchant while disabled in config. Exploit attack ? Charname: ". @$charname ." amount:". @$amount ." donation option:". @$donation_option ."Transaction ID:". @$transid;
												$local_log .= '</td></tr><tr><td>';

												// Write to log.
												$fp=fopen($local_log_file,'a');
												fwrite($fp, $local_log . "\n");

												// Close file.
												fclose($fp);
											}
										}
										else
										{
											// Local file reporting.
											// Logging: file location.
											$local_log_file = $log_location_ipn;

											// Logging: Timestamp.
											$local_log = '['.date('m/d/Y g:i A').'] - ';

											// Logging: response from the server.
											$local_log .= "IPN HELMET EXPLOIT ENCHANT ATTACK: Someone tried to change the donation amount to get the donation for cheap Charname: ". @$charname ." amount:". @$amount ." donation option:". @$donation_option ."Transaction ID:". @$transid;
											$local_log .= '</td></tr><tr><td>';

											// Write to log.
											$fp=fopen($local_log_file,'a');
											fwrite($fp, $local_log . "\n");

											// Close file.
											fclose($fp);
										}
									}
								}
				// Donate enchant necklace.
				if ($donation_option_enc === $donation_enc_option3)
					{
					// Someone tried to enchant a blocked item id.
					if (in_array($char_necklace_id, $enc_item_blocked))
						{
							// Local file reporting.
							// Logging: file location.
							$local_log_file = $log_location_ipn;

							// Logging: Timestamp.
							$local_log = '['.date('m/d/Y g:i A').'] - ';

							// Logging: response from the server.
							$local_log .= "IPN NECKLACE ENCHANT ERROR: Someone tried to enchant necklace while this item_id is disabled in config. Charname: ". @$charname ." amount:". @$amount ." donation option:". @$donation_option ."Transaction ID:". @$transid;
							$local_log .= '</td></tr><tr><td>';

							// Write to log.
							$fp=fopen($local_log_file,'a');
							fwrite($fp, $local_log . "\n");

							// Close file.
							fclose($fp);
						}
						else
						{
						// Checks if the correct amount is donated otherwise log this.
						if ($amount == $necklace_donate_amount)
							{
							// Checks if enchant necklace is enabled in the config or else make a log.
							 if ($necklace_enchant_enabled == true)
								{
									try {
											// If player is offline we will add the necklace enchant trough a mysql query.
											$sql_enchant_necklace = $connection->prepare('UPDATE items SET enchant_level= :necklace_enchant_amount WHERE loc_data= :locdata4 AND owner_id = :charId AND loc = :paperdoll ');
											$sql_enchant_necklace->execute(array(
											':necklace_enchant_amount' => $necklace_enchant_amount,
											':locdata4' => $locdata4,
											':charId' => $charId,
											':paperdoll' => $loc_paper
											));
										} 
									catch(PDOException $e) 
										{
											// Local file reporting.
											// Logging: file location.
											$local_log_file = $log_location_ipn;

											// Logging: Timestamp.
											$local_log = '['.date('m/d/Y g:i A').'] - ';

											// Logging: response from the server.
											$local_log .= "IPN NECKLACE ENCHANT ERROR: ". $e->getMessage();
											$local_log .= '</td></tr><tr><td>';

											// Write to log.
											$fp=fopen($local_log_file,'a');
											fwrite($fp, $local_log . "\n");

											// Close file.
											fclose($fp);
										}
									}
									else
									{
										// Local file reporting.
										// Logging: file location.
										$local_log_file = $log_location_ipn;

										// Logging: Timestamp.
										$local_log = '['.date('m/d/Y g:i A').'] - ';

										// Logging: response from the server.
										$local_log .= "IPN NECKLACE ENCHANT ERROR: Someone tried to enter necklace enchant while disabled in config. Exploit attack ? Charname: ". @$charname ." amount:". @$amount ." donation option:". @$donation_option ."Transaction ID:". @$transid;
										$local_log .= '</td></tr><tr><td>';

										// Write to log.
										$fp=fopen($local_log_file,'a');
										fwrite($fp, $local_log . "\n");

										// Close file.
										fclose($fp);
									}
								}
								else
								{
									// Local file reporting.
									// Logging: file location.
									$local_log_file = $log_location_ipn;

									// Logging: Timestamp.
									$local_log = '['.date('m/d/Y g:i A').'] - ';

									// Logging: response from the server.
									$local_log .= "IPN NECKLACE EXPLOIT ENCHANT ATTACK: Someone tried to change the donation amount to get the donation for cheap Charname: ". @$charname ." amount:". @$amount ." donation option:". @$donation_option ."Transaction ID:". @$transid;
									$local_log .= '</td></tr><tr><td>';

									// Write to log.
									$fp=fopen($local_log_file,'a');
									fwrite($fp, $local_log . "\n");

									// Close file.
									fclose($fp);
								}
							}
						}
					// Donate enchant weapon.
					if ($donation_option_enc === $donation_enc_option4)
						{
						// Someone tried to enchant a blocked item id.
						if (in_array($char_weapon_id, $enc_item_blocked))
							{
								// Local file reporting.
								// Logging: file location.
								$local_log_file = $log_location_ipn;

								// Logging: Timestamp.
								$local_log = '['.date('m/d/Y g:i A').'] - ';

								// Logging: response from the server.
								$local_log .= "IPN WEAPON ENCHANT ERROR: Someone tried to enchant weapon while this item_id is disabled in config. Charname: ". @$charname ." amount:". @$amount ." donation option:". @$donation_option ."Transaction ID:". @$transid;
								$local_log .= '</td></tr><tr><td>';

								// Write to log.
								$fp=fopen($local_log_file,'a');
								fwrite($fp, $local_log . "\n");

								// Close file.
								fclose($fp);
							}
							else
							{
							// Checks if the correct amount is donated otherwise log this.
							if ($amount == $weapon_donate_amount)
								{
								// Checks if enchant weapon is enabled in the config or else make a log.
								if ($weapon_enchant_enabled == true)
									{
									try {
											// If player is offline we will add the weapon enchant trough a mysql query.
											$sql_enchant_weapon = $connection->prepare('UPDATE items SET enchant_level= :weapon_enchant_amount WHERE loc_data= :locdata5 AND owner_id = :charId AND loc = :paperdoll ');
											$sql_enchant_weapon->execute(array(
											':weapon_enchant_amount' => $weapon_enchant_amount,
											':locdata5' => $locdata5,
											':charId' => $charId,
											':paperdoll' => $loc_paper
											));
										} 
									catch(PDOException $e) 
										{
											// Local file reporting.
											// Logging: file location
											$local_log_file = $log_location_ipn;

											// Logging: Timestamp.
											$local_log = '['.date('m/d/Y g:i A').'] - ';

											// Logging: response from the server.
											$local_log .= "IPN WEAPON ENCHANT ERROR: ". $e->getMessage();
											$local_log .= '</td></tr><tr><td>';

											// Write to log.
											$fp=fopen($local_log_file,'a');
											fwrite($fp, $local_log . "\n");

											// Close file.
											fclose($fp);
										}
									}
									else
									{
										// Local file reporting.
										// Logging: file location.
										$local_log_file = $log_location_ipn;

										// Logging: Timestamp.
										$local_log = '['.date('m/d/Y g:i A').'] - ';

										// Logging: response from the server.
										$local_log .= "IPN WEAPON ENCHANT ERROR: Someone tried to enter weapon enchant while disabled in config. Exploit attack ? Charname: ". @$charname ." amount:". @$amount ." donation option:". @$donation_option ."Transaction ID:". @$transid;
										$local_log .= '</td></tr><tr><td>';

										// Write to log.
										$fp=fopen($local_log_file,'a');
										fwrite($fp, $local_log . "\n");

										// Close file.
										fclose($fp);
									}
								}
								else
								{
									// Local file reporting.
									// Logging: file location.
									$local_log_file = $log_location_ipn;

									// Logging: Timestamp.
									$local_log = '['.date('m/d/Y g:i A').'] - ';

									// Logging: response from the server.
									$local_log .= "IPN WEAPON EXPLOIT ENCHANT ATTACK: Someone tried to change the donation amount to get the donation for cheap Charname: ". @$charname ." amount:". @$amount ." donation option:". @$donation_option ."Transaction ID:". @$transid;
									$local_log .= '</td></tr><tr><td>';

									// Write to log.
									$fp=fopen($local_log_file,'a');
									fwrite($fp, $local_log . "\n");

									// Close file.
									fclose($fp);
								}
							}
						}
					// Donate enchant full armor/Breastplate.
					if ($donation_option_enc === $donation_enc_option5)
						{
						// Someone tried to enchant a blocked item id.
						if (in_array($char_breastplate_full_id, $enc_item_blocked))
							{
								// Local file reporting.
								// Logging: file location.
								$local_log_file = $log_location_ipn;

								// Logging: Timestamp.
								$local_log = '['.date('m/d/Y g:i A').'] - ';

								// Logging: response from the server.
								$local_log .= "IPN ARMOR/BREASTPLATE ENCHANT ERROR: Someone tried to enchant armor/breastplate while this item_id is disabled in config. Charname: ". @$charname ." amount:". @$amount ." donation option:". @$donation_option ."Transaction ID:". @$transid;
								$local_log .= '</td></tr><tr><td>';

								// Write to log.
								$fp=fopen($local_log_file,'a');
								fwrite($fp, $local_log . "\n");

								// Close file.
								fclose($fp);
							}
							else
							{
							// Checks if the correct amount is donated otherwise log this.
							if ($amount == $breastplate_full_donate_amount)
							{
								// Checks if enchant full armor/Breastplate is enabled in the config or else make a log.
								if ($breastplate_full_enchant_enabled == true)
								{
									try {
											// If player is offline we will add the full armor/Breastplate enchant trough a mysql query.
											$sql_enchant_fullarmor_breastplate = $connection->prepare('UPDATE items SET enchant_level= :breastplate_full_enchant_amount WHERE loc_data= :locdata6 AND owner_id = :charId AND loc = :paperdoll ');
											$sql_enchant_fullarmor_breastplate->execute(array(
											':breastplate_full_enchant_amount' => $breastplate_full_enchant_amount,
											':locdata6' => $locdata6,
											':charId' => $charId,
											':paperdoll' => $loc_paper
											));
										} 
									catch(PDOException $e) 
										{
											// Local file reporting.
											// Logging: file location.
											$local_log_file = $log_location_ipn;

											// Logging: Timestamp.
											$local_log = '['.date('m/d/Y g:i A').'] - ';

											// Logging: response from the server.
											$local_log .= "IPN FULL ARMOR/BREASTPLATE ENCHANT ERROR: ". $e->getMessage();
											$local_log .= '</td></tr><tr><td>';

											// Write to log.
											$fp=fopen($local_log_file,'a');
											fwrite($fp, $local_log . "\n");

											// Close file.
											fclose($fp);
										}
									}
									else
									{
										// Local file reporting.
										// Logging: file location.
										$local_log_file = $log_location_ipn;

										// Logging: Timestamp.
										$local_log = '['.date('m/d/Y g:i A').'] - ';

										// Logging: response from the server.
										$local_log .= "IPN FULL ARMOR/BREASTPLATE ENCHANT ERROR: Someone tried to enter full armor/breastplate enchant while disabled in config. Exploit attack ? Charname: ". @$charname ." amount:". @$amount ." donation option:". @$donation_option ."Transaction ID:". @$transid;
										$local_log .= '</td></tr><tr><td>';

										// Write to log.
										$fp=fopen($local_log_file,'a');
										fwrite($fp, $local_log . "\n");

										// Close file.
										fclose($fp);
									}
								}
								else
								{
									// Local file reporting.
									// Logging: file location.
									$local_log_file = $log_location_ipn;

									// Logging: Timestamp.
									$local_log = '['.date('m/d/Y g:i A').'] - ';

									// Logging: response from the server.
									$local_log .= "IPN FULL ARMOR/BREASTPLATE ENCHANT EXPLOIT ATTACK: Someone tried to change the donation amount to get the donation for cheap Charname: ". @$charname ." amount:". @$amount ." donation option:". @$donation_option ."Transaction ID:". @$transid;
									$local_log .= '</td></tr><tr><td>';

									// Write to log.
									$fp=fopen($local_log_file,'a');
									fwrite($fp, $local_log . "\n");

									// Close file.
									fclose($fp);
								}
							}
						}
					// Donate enchant shield.
					if ($donation_option_enc === $donation_enc_option6)
						{
						// Someone tried to enchant a blocked item id.
						if (in_array($char_shield_id, $enc_item_blocked))
							{
								// Local file reporting.
								// Logging: file location.
								$local_log_file = $log_location_ipn;

								// Logging: Timestamp.
								$local_log = '['.date('m/d/Y g:i A').'] - ';

								// Logging: response from the server.
								$local_log .= "IPN SHIELD ENCHANT ERROR: Someone tried to enchant shield while this item_id is disabled in config. Charname: ". @$charname ." amount:". @$amount ." donation option:". @$donation_option ."Transaction ID:". @$transid;
								$local_log .= '</td></tr><tr><td>';

								// Write to log.
								$fp=fopen($local_log_file,'a');
								fwrite($fp, $local_log . "\n");

								// Close file.
								fclose($fp);
							}
							else
							{
							// Checks if the correct amount is donated otherwise log this.
							if ($amount == $shield_donate_amount)
								{
								// Checks if enchant shield is enabled in the config or else make a log.
								if ($shield_enchant_enabled == true)
									{
									try {
											// If player is offline we will add the shield enchant trough a mysql query.
											$sql_enchant_shield = $connection->prepare('UPDATE items SET enchant_level= :shield_enchant_amount WHERE loc_data= :locdata7 AND owner_id = :charId AND loc = :paperdoll ');
											$sql_enchant_shield->execute(array(
											':shield_enchant_amount' => $shield_enchant_amount,
											':locdata7' => $locdata7,
											':charId' => $charId,
											':paperdoll' => $loc_paper
											));
										} 
									catch(PDOException $e) 
										{
											// Local file reporting.
											// Logging: file location.
											$local_log_file = $log_location_ipn;

											// Logging: Timestamp.
											$local_log = '['.date('m/d/Y g:i A').'] - ';

											// Logging: response from the server.
											$local_log .= "IPN SHIELD ENCHANT ERROR: ". $e->getMessage();
											$local_log .= '</td></tr><tr><td>';

											// Write to log.
											$fp=fopen($local_log_file,'a');
											fwrite($fp, $local_log . "\n");

											// Close file.
											fclose($fp);
										}
									}
									else
									{
										// Local file reporting.
										// Logging: file location.
										$local_log_file = $log_location_ipn;

										// Logging: Timestamp.
										$local_log = '['.date('m/d/Y g:i A').'] - ';

										// Logging: response from the server.
										$local_log .= "IPN FULL SHIELD ENCHANT ERROR: Someone tried to enter full armor/breastplate enchant while disabled in config. Exploit attack ? Charname: ". @$charname ." amount:". @$amount ." donation option:". @$donation_option ."Transaction ID:". @$transid;
										$local_log .= '</td></tr><tr><td>';

										// Write to log.
										$fp=fopen($local_log_file,'a');
										fwrite($fp, $local_log . "\n");

										// Close file.
										fclose($fp);
									}
								}
								else
								{
									// Local file reporting.
									// Logging: file location.
									$local_log_file = $log_location_ipn;

									// Logging: Timestamp.
									$local_log = '['.date('m/d/Y g:i A').'] - ';

									// Logging: response from the server.
									$local_log .= "IPN SHIELD ENCHANT EXPLOIT ATTACK: Someone tried to change the donation amount to get the donation for cheap Charname: ". @$charname ." amount:". @$amount ." donation option:". @$donation_option ."Transaction ID:". @$transid;
									$local_log .= '</td></tr><tr><td>';

									// Write to log.
									$fp=fopen($local_log_file,'a');
									fwrite($fp, $local_log . "\n");

									// Close file.
									fclose($fp);
								}
							}
						}
					// Donate enchant ring1.
					if ($donation_option_enc === $donation_enc_option7)
					{
						// Else Someone tried to enchant a blocked item id.
						if (in_array($char_lowring_id, $enc_item_blocked))
							{
								// Local file reporting.
								// Logging: file location.
								$local_log_file = $log_location_ipn;

								// Logging: Timestamp.
								$local_log = '['.date('m/d/Y g:i A').'] - ';

								// Logging: response from the server.
								$local_log .= "IPN RING ENCHANT ERROR: Someone tried to enchant ring while this item_id is disabled in config. Charname: ". @$charname ." amount:". @$amount ." donation option:". @$donation_option ."Transaction ID:". @$transid;
								$local_log .= '</td></tr><tr><td>';

								// Write to log.
								$fp=fopen($local_log_file,'a');
								fwrite($fp, $local_log . "\n");

								// Close file.
								fclose($fp);
							}
							else
							{
							// Checks if the correct amount is donated otherwise log this.
							if ($amount == $ring_donate_amount)
							{
								// Checks if enchant rings is enabled in the config or else make a log.
								if ($ring_enchant_enabled == true)
								{
									try {
											// If player is offline we will add the ring1 enchant trough a mysql query.
											$sql_enchant_ring1 = $connection->prepare('UPDATE items SET enchant_level= :ring_enchant_amount WHERE loc_data= :locdata13 AND owner_id = :charId AND loc = :paperdoll ');
											$sql_enchant_ring1->execute(array(
											':ring_enchant_amount' => $ring_enchant_amount,
											':locdata13' => $locdata13,
											':charId' => $charId,
											':paperdoll' => $loc_paper
											));
										} 
									catch(PDOException $e) 
										{
											// Local file reporting.
											// Logging: file location.
											$local_log_file = $log_location_ipn;

											// Logging: Timestamp.
											$local_log = '['.date('m/d/Y g:i A').'] - ';

											// Logging: response from the server.
											$local_log .= "IPN RING ENCHANT ERROR: ". $e->getMessage();
											$local_log .= '</td></tr><tr><td>';

											// Write to log.
											$fp=fopen($local_log_file,'a');
											fwrite($fp, $local_log . "\n");

											// Close file.
											fclose($fp);
										}
									}
									else
									{
										// Local file reporting.
										// Logging: file location.
										$local_log_file = $log_location_ipn;

										// Logging: Timestamp.
										$local_log = '['.date('m/d/Y g:i A').'] - ';

										// Logging: response from the server.
										$local_log .= "IPN RING ENCHANT ERROR: Someone tried to enter ring enchant while disabled in config. Exploit attack ? Charname: ". @$charname ." amount:". @$amount ." donation option:". @$donation_option ."Transaction ID:". @$transid;
										$local_log .= '</td></tr><tr><td>';

										// Write to log.
										$fp=fopen($local_log_file,'a');
										fwrite($fp, $local_log . "\n");

										// Close file.
										fclose($fp);
									}
								}
								else
								{
									// Local file reporting.
									// Logging: file location.
									$local_log_file = $log_location_ipn;

									// Logging: Timestamp
									$local_log = '['.date('m/d/Y g:i A').'] - ';

									// Logging: response from the server.
									$local_log .= "IPN RING ENCHANT EXPLOIT ATTACK: Someone tried to change the donation amount to get the donation for cheap Charname: ". @$charname ." amount:". @$amount ." donation option:". @$donation_option ."Transaction ID:". @$transid;
									$local_log .= '</td></tr><tr><td>';

									// Write to log.
									$fp=fopen($local_log_file,'a');
									fwrite($fp, $local_log . "\n");

									// Close file.
									fclose($fp);
								}
							}
						}
				// Donate enchant ring2.
				if ($donation_option_enc === $donation_enc_option8)
				{
					// Someone tried to enchant a blocked item id.
					if (in_array($char_upring_id, $enc_item_blocked))
						{
							// Local file reporting.
							// Logging: file location.
							$local_log_file = $log_location_ipn;

							// Logging: Timestamp.
							$local_log = '['.date('m/d/Y g:i A').'] - ';

							// Logging: response from the server.
							$local_log .= "IPN RING ENCHANT ERROR: Someone tried to enchant ring while this item_id is disabled in config. Charname: ". @$charname ." amount:". @$amount ." donation option:". @$donation_option ."Transaction ID:". @$transid;
							$local_log .= '</td></tr><tr><td>';

							// Write to log.
							$fp=fopen($local_log_file,'a');
							fwrite($fp, $local_log . "\n");

							// Close file.
							fclose($fp);
						}
						else
						{
						// Checks if the correct amount is donated otherwise log this.
						if ($amount == $ring_donate_amount)
						{
							// Checks if enchant rings is enabled in the config or else make a log.
							if ($ring_enchant_enabled == true)
							{
								try {
										// If player is offline we will add the ring2 enchant trough a mysql query.
										$sql_enchant_ring2 = $connection->prepare('UPDATE items SET enchant_level= :ring_enchant_amount WHERE loc_data= :locdata14 AND owner_id = :charId AND loc = :paperdoll ');
										$sql_enchant_ring2->execute(array(
										':ring_enchant_amount' => $ring_enchant_amount,
										':locdata14' => $locdata14,
										':charId' => $charId,
										':paperdoll' => $loc_paper
										));
									} 
								catch(PDOException $e) 
									{
										// Local file reporting.
										// Logging: file location.
										$local_log_file = $log_location_ipn;

										// Logging: Timestamp.
										$local_log = '['.date('m/d/Y g:i A').'] - ';

										// Logging: response from the server.
										$local_log .= "IPN RING ENCHANT ERROR: ". $e->getMessage();
										$local_log .= '</td></tr><tr><td>';

										// Write to log.
										$fp=fopen($local_log_file,'a');
										fwrite($fp, $local_log . "\n");

										// Close file.
										fclose($fp);
									}
								}
								else
								{
									// Local file reporting.
									// Logging: file location.
									$local_log_file = $log_location_ipn;

									// Logging: Timestamp.
									$local_log = '['.date('m/d/Y g:i A').'] - ';

									// Logging: response from the server.
									$local_log .= "IPN RING ENCHANT ERROR: Someone tried to enter ring enchant while disabled in config. Exploit attack ? Charname: ". @$charname ." amount:". @$amount ." donation option:". @$donation_option ."Transaction ID:". @$transid;
									$local_log .= '</td></tr><tr><td>';

									// Write to log
									$fp=fopen($local_log_file,'a');
									fwrite($fp, $local_log . "\n");

									// Close file
									fclose($fp);
								}
							}
							else
							{
								// Local file reporting.
								// Logging: file location.
								$local_log_file = $log_location_ipn;

								// Logging: Timestamp
								$local_log = '['.date('m/d/Y g:i A').'] - ';

								// Logging: response from the server.
								$local_log .= "IPN RING ENCHANT EXPLOIT ATTACK: Someone tried to change the donation amount to get the donation for cheap Charname: ". @$charname ." amount:". @$amount ." donation option:". @$donation_option ."Transaction ID:". @$transid;
								$local_log .= '</td></tr><tr><td>';

								// Write to log.
								$fp=fopen($local_log_file,'a');
								fwrite($fp, $local_log . "\n");

								// Close file.
								fclose($fp);
							}
						}
					}
					// Donate enchant earring1.
					if ($donation_option_enc === $donation_enc_option9)
					{
						// Someone tried to enchant a blocked item id.
						if (in_array($char_lowearring_id, $enc_item_blocked))
							{
								// Local file reporting.
								// Logging: file location.
								$local_log_file = $log_location_ipn;

								// Logging: Timestamp.
								$local_log = '['.date('m/d/Y g:i A').'] - ';

								// Logging: response from the server.
								$local_log .= "IPN EARRING ENCHANT ERROR: Someone tried to enchant earring while this item_id is disabled in config. Charname: ". @$charname ." amount:". @$amount ." donation option:". @$donation_option ."Transaction ID:". @$transid;
								$local_log .= '</td></tr><tr><td>';

								// Write to log.
								$fp=fopen($local_log_file,'a');
								fwrite($fp, $local_log . "\n");

								// Close file.
								fclose($fp);
							}
							else
							{
							// Checks if the correct amount is donated otherwise log this.
							if ($amount == $earring_donate_amount)
								{
								// Checks if enchant earring is enabled in the config or else make a log.
								if ($earring_enchant_enabled == true)
									{
									try {
											// If player is offline we will add the earring1 enchant trough a mysql query.
											$sql_enchant_earring1 = $connection->prepare('UPDATE items SET enchant_level= :earring_enchant_amount WHERE loc_data= :locdata8 AND owner_id = :charId AND loc = :paperdoll ');
											$sql_enchant_earring1->execute(array(
											':earring_enchant_amount' => $earring_enchant_amount,
											':locdata8' => $locdata8,
											':charId' => $charId,
											':paperdoll' => $loc_paper
											));
										} 
									catch(PDOException $e) 
										{
											// Local file reporting.
											// Logging: file location.
											$local_log_file = $log_location_ipn;

											// Logging: Timestamp.
											$local_log = '['.date('m/d/Y g:i A').'] - ';

											// Logging: response from the server.
											$local_log .= "IPN EARRING ENCHANT ERROR: ". $e->getMessage();
											$local_log .= '</td></tr><tr><td>';

											// Write to log.
											$fp=fopen($local_log_file,'a');
											fwrite($fp, $local_log . "\n");

											// Close file.
											fclose($fp);
										}
									}
									else
									{
										// Local file reporting.
										// Logging: file location.
										$local_log_file = $log_location_ipn;

										// Logging: Timestamp.
										$local_log = '['.date('m/d/Y g:i A').'] - ';

										// Logging: response from the server.
										$local_log .= "IPN EARRING ENCHANT ERROR: Someone tried to enter earring enchant while disabled in config. Exploit attack ? Charname: ". @$charname ." amount:". @$amount ." donation option:". @$donation_option ."Transaction ID:". @$transid;
										$local_log .= '</td></tr><tr><td>';

										// Write to log.
										$fp=fopen($local_log_file,'a');
										fwrite($fp, $local_log . "\n");

										// Close file
										fclose($fp);
									}
								}
								else
								{
									// Local file reporting.
									// Logging: file location.
									$local_log_file = $log_location_ipn;

									// Logging: Timestamp.
									$local_log = '['.date('m/d/Y g:i A').'] - ';

									// Logging: response from the server.
									$local_log .= "IPN EARRING ENCHANT EXPLOIT ATTACK: Someone tried to change the donation amount to get the donation for cheap Charname: ". @$charname ." amount:". @$amount ." donation option:". @$donation_option ."Transaction ID:". @$transid;
									$local_log .= '</td></tr><tr><td>';

									// Write to log.
									$fp=fopen($local_log_file,'a');
									fwrite($fp, $local_log . "\n");

									// Close file.
									fclose($fp);
								}
							}
						}
					// Donate enchant earring2.
					if ($donation_option_enc === $donation_enc_option10)
						{
						// Someone tried to enchant a blocked item id.
						if (in_array($char_upearring_id, $enc_item_blocked))
							{
								// Local file reporting.
								// Logging: file location.
								$local_log_file = $log_location_ipn;

								// Logging: Timestamp.
								$local_log = '['.date('m/d/Y g:i A').'] - ';

								// Logging: response from the server.
								$local_log .= "IPN EARRING ENCHANT ERROR: Someone tried to enchant earring while this item_id is disabled in config. Charname: ". @$charname ." amount:". @$amount ." donation option:". @$donation_option ."Transaction ID:". @$transid;
								$local_log .= '</td></tr><tr><td>';

								// Write to log.
								$fp=fopen($local_log_file,'a');
								fwrite($fp, $local_log . "\n");

								// Close file.
								fclose($fp);
							}
							else
							{
							// checks if the correct amount is donated otherwise log this.
							if ($amount == $earring_donate_amount)
								{
								// Checks if enchant earring is enabled in the config or else make a log.
								if ($earring_enchant_enabled == true)
									{
									try {
											// If player is offline we will add the earring2 enchant trough a mysql query.
											$sql_enchant_earring2 = $connection->prepare('UPDATE items SET enchant_level= :earring_enchant_amount WHERE loc_data= :locdata9 AND owner_id = :charId AND loc = :paperdoll ');
											$sql_enchant_earring2->execute(array(
											':earring_enchant_amount' => $earring_enchant_amount,
											':locdata9' => $locdata9,
											':charId' => $charId,
											':paperdoll' => $loc_paper
											));
										} 
									catch(PDOException $e) 
										{
											// Local file reporting.
											// Logging: file location.
											$local_log_file = $log_location_ipn;

											// Logging: Timestamp
											$local_log = '['.date('m/d/Y g:i A').'] - ';

											// Logging: response from the server.
											$local_log .= "IPN EARRING ENCHANT ERROR: ". $e->getMessage();
											$local_log .= '</td></tr><tr><td>';

											// Write to log.
											$fp=fopen($local_log_file,'a');
											fwrite($fp, $local_log . "\n");

											// Close file.
											fclose($fp);
										}
									}
									else
									{
										// Local file reporting.
										// Logging: file location.
										$local_log_file = $log_location_ipn;

										// Logging: Timestamp.
										$local_log = '['.date('m/d/Y g:i A').'] - ';

										// Logging: response from the server.
										$local_log .= "IPN EARRING ENCHANT ERROR: Someone tried to enter earring enchant while disabled in config. Exploit attack ? Charname: ". @$charname ." amount:". @$amount ." donation option:". @$donation_option ."Transaction ID:". @$transid;
										$local_log .= '</td></tr><tr><td>';

										// Write to log.
										$fp=fopen($local_log_file,'a');
										fwrite($fp, $local_log . "\n");

										// Close file.
										fclose($fp);
									}
							}
						else
						{
							// Local file reporting.
							// Logging: file location.
							$local_log_file = $log_location_ipn;

							// Logging: Timestamp.
							$local_log = '['.date('m/d/Y g:i A').'] - ';

							// Logging: response from the server.
							$local_log .= "IPN EARRING ENCHANT EXPLOIT ATTACK: Someone tried to change the donation amount to get the donation for cheap Charname: ". @$charname ." amount:". @$amount ." donation option:". @$donation_option ."Transaction ID:". @$transid;
							$local_log .= '</td></tr><tr><td>';

							// Write to log.
							$fp=fopen($local_log_file,'a');
							fwrite($fp, $local_log . "\n");

							// Close file.
							fclose($fp);
						}
					}
				}
					// Donate enchant gloves.
					if ($donation_option_enc === $donation_enc_option11)
						{
						// Checks if player got a blocked item id equipped.
						if (in_array($char_gloves_id, $enc_item_blocked))
							{
								// Local file reporting.
								// Logging: file location.
								$local_log_file = $log_location_ipn;

								// Logging: Timestamp.
								$local_log = '['.date('m/d/Y g:i A').'] - ';

								// Logging: response from the server.
								$local_log .= "IPN GLOVES ENCHANT ERROR: Someone tried to enchant gloves while this item_id is disabled in config. Charname: ". @$charname ." amount:". @$amount ." donation option:". @$donation_option ."Transaction ID:". @$transid;
								$local_log .= '</td></tr><tr><td>';

								// Write to log.
								$fp=fopen($local_log_file,'a');
								fwrite($fp, $local_log . "\n");

								// Close file.
								fclose($fp);
							}
							else
							{
							// Checks if the correct amount is donated otherwise log this.
							if ($amount == $gloves_donate_amount)
								{
								// Checks if enchant gloves is enabled in the config or else make a log.
								if ($gloves_enchant_enabled == true)
									{
									try {
											// If player is offline we will add the gloves enchant trough a mysql query.
											$sql_enchant_gloves = $connection->prepare('UPDATE items SET enchant_level= :gloves_enchant_amount WHERE loc_data= :locdata10 AND owner_id = :charId AND loc = :paperdoll ');
											$sql_enchant_gloves->execute(array(
											':gloves_enchant_amount' => $gloves_enchant_amount,
											':locdata10' => $locdata10,
											':charId' => $charId,
											':paperdoll' => $loc_paper
											));
										} 
									catch(PDOException $e) 
										{
											// Local file reporting.
											// Logging: file location.
											$local_log_file = $log_location_ipn;

											// Logging: Timestamp.
											$local_log = '['.date('m/d/Y g:i A').'] - ';

											// Logging: response from the server.
											$local_log .= "IPN GLOVES ENCHANT ERROR: ". $e->getMessage();
											$local_log .= '</td></tr><tr><td>';

											// Write to log.
											$fp=fopen($local_log_file,'a');
											fwrite($fp, $local_log . "\n");

											// Close file.
											fclose($fp);
										}
									}
									else
									{
										// Local file reporting.
										// Logging: file location.
										$local_log_file = $log_location_ipn;

										// Logging: Timestamp.
										$local_log = '['.date('m/d/Y g:i A').'] - ';

										// Logging: response from the server.
										$local_log .= "IPN GLOVES ENCHANT ERROR: Someone tried to enter earring enchant while disabled in config. Exploit attack ? Charname: ". @$charname ." amount:". @$amount ." donation option:". @$donation_option ."Transaction ID:". @$transid;
										$local_log .= '</td></tr><tr><td>';

										// Write to log.
										$fp=fopen($local_log_file,'a');
										fwrite($fp, $local_log . "\n");

										// Close file.
										fclose($fp);
									}
								}
								else
								{
									// Local file reporting.
									// Logging: file location.
									$local_log_file = $log_location_ipn;

									// Logging: Timestamp.
									$local_log = '['.date('m/d/Y g:i A').'] - ';

									// Logging: response from the server.
									$local_log .= "IPN GLOVES ENCHANT EXPLOIT ATTACK: Someone tried to change the donation amount to get the donation for cheap Charname: ". @$charname ." amount:". @$amount ." donation option:". @$donation_option ."Transaction ID:". @$transid;
									$local_log .= '</td></tr><tr><td>';

									// Write to log.
									$fp=fopen($local_log_file,'a');
									fwrite($fp, $local_log . "\n");

									// Close file.
									fclose($fp);
								}
							}
						}
					// Donate enchant leggings.
					if ($donation_option_enc === $donation_enc_option12)
						{
						// Someone tried to enchant a blocked item id.
						if (in_array($char_leggings_id, $enc_item_blocked))
							{
								// Local file reporting.
								// Logging: file location.
								$local_log_file = $log_location_ipn;

								// Logging: Timestamp.
								$local_log = '['.date('m/d/Y g:i A').'] - ';

								// Logging: response from the server.
								$local_log .= "IPN LEGGING ENCHANT ERROR: Someone tried to enchant legging while this item_id is disabled in config. Charname: ". @$charname ." amount:". @$amount ." donation option:". @$donation_option ."Transaction ID:". @$transid;
								$local_log .= '</td></tr><tr><td>';

								// Write to log.
								$fp=fopen($local_log_file,'a');
								fwrite($fp, $local_log . "\n");

								// Close file.
								fclose($fp);
							}
							else
							{
							// Checks if the correct amount is donated otherwise log this.
							if ($amount == $leggings_donate_amount)
								{
								// Checks if enchant leggings is enabled in the config or else make a log.
								if ($leggings_enchant_enabled == true)
									{
									try {
											// If player is offline we will add the leggings enchant trough a mysql query.
											$sql_enchant_leggings = $connection->prepare('UPDATE items SET enchant_level= :leggings_enchant_amount WHERE loc_data= :locdata11 AND owner_id = :charId AND loc = :paperdoll ');
											$sql_enchant_leggings->execute(array(
											':leggings_enchant_amount' => $leggings_enchant_amount,
											':locdata11' => $locdata11,
											':charId' => $charId,
											':paperdoll' => $loc_paper
											));
										} 
									catch(PDOException $e) 
										{
											// Local file reporting.
											// Logging: file location.
											$local_log_file = $log_location_ipn;

											// Logging: Timestamp.
											$local_log = '['.date('m/d/Y g:i A').'] - ';

											// Logging: response from the server.
											$local_log .= "IPN LEGGINGS ENCHANT ERROR: ". $e->getMessage();
											$local_log .= '</td></tr><tr><td>';

											// Write to log.
											$fp=fopen($local_log_file,'a');
											fwrite($fp, $local_log . "\n");

											// Close file.
											fclose($fp);
										}
									}
									else
									{
										// Local file reporting.
										// Logging: file location.
										$local_log_file = $log_location_ipn;

										// Logging: Timestamp.
										$local_log = '['.date('m/d/Y g:i A').'] - ';

										// Logging: response from the server.
										$local_log .= "IPN LEGGINGS ENCHANT ERROR: Someone tried to enter leggings enchant while disabled in config. Exploit attack ? Charname: ". @$charname ." amount:". @$amount ." donation option:". @$donation_option ."Transaction ID:". @$transid;
										$local_log .= '</td></tr><tr><td>';

										// Write to log.
										$fp=fopen($local_log_file,'a');
										fwrite($fp, $local_log . "\n");

										// Close file.
										fclose($fp);
									}
								}
								else
								{
									// Local file reporting.
									// Logging: file location.
									$local_log_file = $log_location_ipn;

									// Logging: Timestamp.
									$local_log = '['.date('m/d/Y g:i A').'] - ';

									// Logging: response from the server.
									$local_log .= "IPN LEGGING ENCHANT EXPLOIT ATTACK: Someone tried to change the donation amount to get the donation for cheap Charname: ". @$charname ." amount:". @$amount ." donation option:". @$donation_option ."Transaction ID:". @$transid;
									$local_log .= '</td></tr><tr><td>';

									// Write to log.
									$fp=fopen($local_log_file,'a');
									fwrite($fp, $local_log . "\n");

									// Close file.
									fclose($fp);
								}
							}
						}
					// Donate enchant boots.
					if ($donation_option_enc === $donation_enc_option13)
						{
						// Else Someone tried to enchant a blocked item id.
						if (in_array($char_boots_id, $enc_item_blocked)) 
							{
								// Local file reporting.
								// Logging: file location.
								$local_log_file = $log_location_ipn;

								// Logging: Timestamp.
								$local_log = '['.date('m/d/Y g:i A').'] - ';

								// Logging: response from the server.
								$local_log .= "IPN BOOTS ENCHANT ERROR: Someone tried to enchant boots while this item_id is disabled in config. Charname: ". @$charname ." amount:". @$amount ." donation option:". @$donation_option ."Transaction ID:". @$transid;
								$local_log .= '</td></tr><tr><td>';

								// Write to log.
								$fp=fopen($local_log_file,'a');
								fwrite($fp, $local_log . "\n");

								// Close file.
								fclose($fp);
							}
							else
							{
							// Checks if the correct amount is donated otherwise log this.
							if ($amount == $boots_donate_amount)
								{
								// Checks if enchant boots is enabled in the config or else make a log.
								if ($boots_enchant_enabled == true)
									{
									try {
											// If player is offline we will add the boots enchant trough a mysql query.
											$sql_enchant_boots = $connection->prepare('UPDATE items SET enchant_level= :boots_enchant_amount WHERE loc_data= :locdata12 AND owner_id = :charId AND loc = :paperdoll ');
											$sql_enchant_boots->execute(array(
											':boots_enchant_amount' => $boots_enchant_amount,
											':locdata12' => $locdata12,
											':charId' => $charId,
											':paperdoll' => $loc_paper
											));
										} 
									catch(PDOException $e) 
										{
											// Local file reporting.
											// Logging: file location.
											$local_log_file = $log_location_ipn;

											// Logging: Timestamp.
											$local_log = '['.date('m/d/Y g:i A').'] - ';

											// Logging: response from the server.
											$local_log .= "IPN BOOTS ENCHANT ERROR: ". $e->getMessage();
											$local_log .= '</td></tr><tr><td>';

											// Write to log.
											$fp=fopen($local_log_file,'a');
											fwrite($fp, $local_log . "\n");

											// Close file.
											fclose($fp);
										}
									}
									else
									{
										// Local file reporting.
										// Logging: file location.
										$local_log_file = $log_location_ipn;

										// Logging: Timestamp.
										$local_log = '['.date('m/d/Y g:i A').'] - ';

										// Logging: response from the server.
										$local_log .= "IPN BOOTS ENCHANT ERROR: Someone tried to enter boots enchant while disabled in config. Exploit attack ? Charname: ". @$charname ." amount:". @$amount ." donation option:". @$donation_option ."Transaction ID:". @$transid;
										$local_log .= '</td></tr><tr><td>';

										// Write to log.
										$fp=fopen($local_log_file,'a');
										fwrite($fp, $local_log . "\n");

										// Close file.
										fclose($fp);
									}
								}
								else
								{
									// Local file reporting.
									// Logging: file location.
									$local_log_file = $log_location_ipn;

									// Logging: Timestamp
									$local_log = '['.date('m/d/Y g:i A').'] - ';

									// Logging: response from the server.
									$local_log .= "IPN BOOTS ENCHANT EXPLOIT ATTACK: Someone tried to change the donation amount to get the donation for cheap Charname: ". @$charname ." amount:". @$amount ." donation option:". @$donation_option ."Transaction ID:". @$transid;
									$local_log .= '</td></tr><tr><td>';

									// Write to log.
									$fp=fopen($local_log_file,'a');
									fwrite($fp, $local_log . "\n");

									// Close file.
									fclose($fp);
								}
							}
						}
					// Donate enchant belt.
					if ($donation_option_enc === $donation_enc_option14)
						{
						// Else Someone tried to enchant a blocked item id.
						if (in_array($char_belt_id, $enc_item_blocked))
							{
								// Local file reporting.
								// Logging: file location.
								$local_log_file = $log_location_ipn;

								// Logging: Timestamp.
								$local_log = '['.date('m/d/Y g:i A').'] - ';

								// Logging: response from the server.
								$local_log .= "IPN BELT ENCHANT ERROR: Someone tried to enchant belt while this item_id is disabled in config. Charname: ". @$charname ." amount:". @$amount ." donation option:". @$donation_option ."Transaction ID:". @$transid;
								$local_log .= '</td></tr><tr><td>';

								// Write to log.
								$fp=fopen($local_log_file,'a');
								fwrite($fp, $local_log . "\n");

								// Close file.
								fclose($fp);
							}
							else
							{
							// Checks if the correct amount is donated otherwise log this.
							if ($amount == $belt_donate_amount)
								{
								// Checks if enchant belt is enabled in the config or else make a log.
								if ($belt_enchant_enabled == true)
									{
									try {
											// If player is offline we will add the belt enchant trough a mysql query.
											$sql_enchant_belt = $connection->prepare('UPDATE items SET enchant_level= :belt_enchant_amount WHERE loc_data= :locdata24 AND owner_id = :charId AND loc = :paperdoll ');
											$sql_enchant_belt->execute(array(
											':belt_enchant_amount' => $belt_enchant_amount,
											':locdata24' => $locdata24,
											':charId' => $charId,
											':paperdoll' => $loc_paper
											));
										} 
									catch(PDOException $e) 
										{
											// Local file reporting.
											// Logging: file location.
											$local_log_file = $log_location_ipn;

											// Logging: Timestamp.
											$local_log = '['.date('m/d/Y g:i A').'] - ';

											// Logging: response from the server.
											$local_log .= "IPN BELT ENCHANT ERROR: ". $e->getMessage();
											$local_log .= '</td></tr><tr><td>';

											// Write to log.
											$fp=fopen($local_log_file,'a');
											fwrite($fp, $local_log . "\n");

											// Close file.
											fclose($fp);
										}
									}
									else
									{
										// Local file reporting.
										// Logging: file location.
										$local_log_file = $log_location_ipn;

										// Logging: Timestamp.
										$local_log = '['.date('m/d/Y g:i A').'] - ';

										// Logging: response from the server.
										$local_log .= "IPN BELT ENCHANT ERROR: Someone tried to enter belt enchant while disabled in config. Exploit attack ? Charname: ". @$charname ." amount:". @$amount ." donation option:". @$donation_option ."Transaction ID:". @$transid;
										$local_log .= '</td></tr><tr><td>';

										// Write to log.
										$fp=fopen($local_log_file,'a');
										fwrite($fp, $local_log . "\n");

										// Close file.
										fclose($fp);
									}
								}
								else
								{
									// Local file reporting.
									// Logging: file location.
									$local_log_file = $log_location_ipn;

									// Logging: Timestamp.
									$local_log = '['.date('m/d/Y g:i A').'] - ';

									// Logging: response from the server.
									$local_log .= "IPN BELT ENCHANT EXPLOIT ATTACK: Someone tried to change the donation amount to get the donation for cheap Charname: ". @$charname ." amount:". @$amount ." donation option:". @$donation_option ."Transaction ID:". @$transid;
									$local_log .= '</td></tr><tr><td>';

									// Write to log.
									$fp=fopen($local_log_file,'a');
									fwrite($fp, $local_log . "\n");

									// Close file.
									fclose($fp);
								}
							}
						}
					// Donate enchant ALL.
					if ($donation_option_enc === $donation_enc_option15)
						{
						// Someone tried to enchant a blocked item id.
						if (in_array($char_shirt_id, $enc_item_blocked) || !in_array($char_helmet_id, $enc_item_blocked) || !in_array($char_necklace_id, $enc_item_blocked) || !in_array($char_weapon_id, $enc_item_blocked) || !in_array($char_breastplate_full_id, $enc_item_blocked) || !in_array($char_shield_id, $enc_item_blocked) || !in_array($char_lowring_id, $enc_item_blocked) || !in_array($char_upring_id, $enc_item_blocked) || !in_array($char_lowearring_id, $enc_item_blocked) || !in_array($char_upearring_id, $enc_item_blocked) || !in_array($char_gloves_id, $enc_item_blocked) || !in_array($char_leggings_id, $enc_item_blocked) || !in_array($char_boots_id, $enc_item_blocked) || !in_array($char_belt_id, $enc_item_blocked)) 
							{
								// Local file reporting.
								// Logging: file location.
								$local_log_file = $log_location_ipn;

								// Logging: Timestamp.
								$local_log = '['.date('m/d/Y g:i A').'] - ';

								// Logging: response from the server.
								$local_log .= "IPN ALL ENCHANT ERROR: Someone tried to enchant a item while this item_id is disabled in config. Charname: ". @$charname ." amount:". @$amount ." donation option:". @$donation_option ."Transaction ID:". @$transid;
								$local_log .= '</td></tr><tr><td>';

								// Write to log.
								$fp=fopen($local_log_file,'a');
								fwrite($fp, $local_log . "\n");

								// Close file.
								fclose($fp);
							}
							else
							{
							// Checks if the correct amount is donated otherwise log this.
							if ($amount == $all_donate_amount)
							{
								// Checks if enchant all is enabled in the config or else make a log.
								if ($all_enchant_enabled == true)
								{
									try {
											// If player is offline we will add shirt enchant trough a mysql query.
											$sql_enchant_shirt = $connection->prepare('UPDATE items SET enchant_level= :shirt_enchant_amount WHERE loc_data= :locdata0 AND owner_id = :charId AND loc = :paperdoll ');
											$sql_enchant_shirt->execute(array(
											':shirt_enchant_amount' => $shirt_enchant_amount,
											':locdata0' => $locdata0,
											':charId' => $charId,
											':paperdoll' => $loc_paper
											));

											// If player is offline we will add the helmet enchant trough a mysql query.
											$sql_enchant_helmet = $connection->prepare('UPDATE items SET enchant_level= :helmet_enchant_amount WHERE loc_data= :locdata1 AND owner_id = :charId AND loc = :paperdoll ');
											$sql_enchant_helmet->execute(array(
											':helmet_enchant_amount' => $helmet_enchant_amount,
											':locdata1' => $locdata1,
											':charId' => $charId,
											':paperdoll' => $loc_paper
											));

											// If player is offline we will add the necklace enchant trough a mysql query.
											$sql_enchant_necklace = $connection->prepare('UPDATE items SET enchant_level= :necklace_enchant_amount WHERE loc_data= :locdata4 AND owner_id = :charId AND loc = :paperdoll ');
											$sql_enchant_necklace->execute(array(
											':necklace_enchant_amount' => $necklace_enchant_amount,
											':locdata4' => $locdata4,
											':charId' => $charId,
											':paperdoll' => $loc_paper
											));

											// If player is offline we will add the weapon enchant trough a mysql query.
											$sql_enchant_weapon = $connection->prepare('UPDATE items SET enchant_level= :weapon_enchant_amount WHERE loc_data= :locdata5 AND owner_id = :charId AND loc = :paperdoll ');
											$sql_enchant_weapon->execute(array(
											':weapon_enchant_amount' => $weapon_enchant_amount,
											':locdata5' => $locdata5,
											':charId' => $charId,
											':paperdoll' => $loc_paper
											));

											// If player is offline we will add the full armor/Breastplate enchant trough a mysql query.
											$sql_enchant_fullarmor_breastplate = $connection->prepare('UPDATE items SET enchant_level= :breastplate_full_enchant_amount WHERE loc_data= :locdata6 AND owner_id = :charId AND loc = :paperdoll ');
											$sql_enchant_fullarmor_breastplate->execute(array(
											':breastplate_full_enchant_amount' => $breastplate_full_enchant_amount,
											':locdata6' => $locdata6,
											':charId' => $charId,
											':paperdoll' => $loc_paper
											));

											// If player is offline we will add the shield enchant trough a mysql query.
											$sql_enchant_shield = $connection->prepare('UPDATE items SET enchant_level= :shield_enchant_amount WHERE loc_data= :locdata7 AND owner_id = :charId AND loc = :paperdoll ');
											$sql_enchant_shield->execute(array(
											':shield_enchant_amount' => $shield_enchant_amount,
											':locdata7' => $locdata7,
											':charId' => $charId,
											':paperdoll' => $loc_paper
											));

											// If player is offline we will add the ring1 enchant trough a mysql query.
											$sql_enchant_ring1 = $connection->prepare('UPDATE items SET enchant_level= :ring_enchant_amount WHERE loc_data= :locdata13 AND owner_id = :charId AND loc = :paperdoll ');
											$sql_enchant_ring1->execute(array(
											':ring_enchant_amount' => $ring_enchant_amount,
											':locdata13' => $locdata13,
											':charId' => $charId,
											':paperdoll' => $loc_paper
											));

											// If player is offline we will add the ring2 enchant trough a mysql query.
											$sql_enchant_ring2 = $connection->prepare('UPDATE items SET enchant_level= :ring_enchant_amount WHERE loc_data= :locdata14 AND owner_id = :charId AND loc = :paperdoll ');
											$sql_enchant_ring2->execute(array(
											':ring_enchant_amount' => $ring_enchant_amount,
											':locdata14' => $locdata14,
											':charId' => $charId,
											':paperdoll' => $loc_paper
											));

											// If player is offline we will add the earring1 enchant trough a mysql query.
											$sql_enchant_earring1 = $connection->prepare('UPDATE items SET enchant_level= :earring_enchant_amount WHERE loc_data= :locdata8 AND owner_id = :charId AND loc = :paperdoll ');
											$sql_enchant_earring1->execute(array(
											':earring_enchant_amount' => $earring_enchant_amount,
											':locdata8' => $locdata8,
											':charId' => $charId,
											':paperdoll' => $loc_paper
											));

											// If player is offline we will add the earring2 enchant trough a mysql query.
											$sql_enchant_earring2 = $connection->prepare('UPDATE items SET enchant_level= :earring_enchant_amount WHERE loc_data= :locdata9 AND owner_id = :charId AND loc = :paperdoll ');
											$sql_enchant_earring2->execute(array(
											':earring_enchant_amount' => $earring_enchant_amount,
											':locdata9' => $locdata9,
											':charId' => $charId,
											':paperdoll' => $loc_paper
											));

											// If player is offline we will add the gloves enchant trough a mysql query.
											$sql_enchant_gloves = $connection->prepare('UPDATE items SET enchant_level= :gloves_enchant_amount WHERE loc_data= :locdata10 AND owner_id = :charId AND loc = :paperdoll ');
											$sql_enchant_gloves->execute(array(
											':gloves_enchant_amount' => $gloves_enchant_amount,
											':locdata10' => $locdata10,
											':charId' => $charId,
											':paperdoll' => $loc_paper
											));

											// If player is offline we will add the leggings enchant trough a mysql query.
											$sql_enchant_leggings = $connection->prepare('UPDATE items SET enchant_level= :leggings_enchant_amount WHERE loc_data= :locdata11 AND owner_id = :charId AND loc = :paperdoll ');
											$sql_enchant_leggings->execute(array(
											':leggings_enchant_amount' => $leggings_enchant_amount,
											':locdata11' => $locdata11,
											':charId' => $charId,
											':paperdoll' => $loc_paper
											));

											// If player is offline we will add the boots enchant trough a mysql query.
											$sql_enchant_boots = $connection->prepare('UPDATE items SET enchant_level= :boots_enchant_amount WHERE loc_data= :locdata12 AND owner_id = :charId AND loc = :paperdoll ');
											$sql_enchant_boots->execute(array(
											':boots_enchant_amount' => $boots_enchant_amount,
											':locdata12' => $locdata12,
											':charId' => $charId,
											':paperdoll' => $loc_paper
											));

											// If player is offline we will add the belt enchant trough a mysql query.
											$sql_enchant_belt = $connection->prepare('UPDATE items SET enchant_level= :belt_enchant_amount WHERE loc_data= :locdata24 AND owner_id = :charId AND loc = :paperdoll ');
											$sql_enchant_belt->execute(array(
											':belt_enchant_amount' => $belt_enchant_amount,
											':locdata24' => $locdata24,
											':charId' => $charId,
											':paperdoll' => $loc_paper
											));
										}
									catch(PDOException $e) 
										{
											// Local file reporting.
											// Logging: file location.
											$local_log_file = $log_location_ipn;

											// Logging: Timestamp.
											$local_log = '['.date('m/d/Y g:i A').'] - ';

											// Logging: response from the server.
											$local_log .= "IPN ALL ENCHANT ERROR: ". $e->getMessage();
											$local_log .= '</td></tr><tr><td>';

											// Write to log.
											$fp=fopen($local_log_file,'a');
											fwrite($fp, $local_log . "\n");

											// Close file.
											fclose($fp);
										}
							}
							else
							{
								// Local file reporting.
								// Logging: file location.
								$local_log_file = $log_location_ipn;

								// Logging: Timestamp.
								$local_log = '['.date('m/d/Y g:i A').'] - ';

								// Logging: response from the server.
								$local_log .= "IPN ALL ENCHANT ERROR: Someone tried to enter all enchant while disabled in config. Exploit attack ? Charname: ". @$charname ." amount:". @$amount ." donation option:". @$donation_option ."Transaction ID:". @$transid;
								$local_log .= '</td></tr><tr><td>';

								// Write to log.
								$fp=fopen($local_log_file,'a');
								fwrite($fp, $local_log . "\n");

								// Close file.
								fclose($fp);
							}
						}
						else
						{
							// Local file reporting.
							// Logging: file location.
							$local_log_file = $log_location_ipn;

							// Logging: Timestamp.
							$local_log = '['.date('m/d/Y g:i A').'] - ';

							// Logging: response from the server.
							$local_log .= "IPN ALL ENCHANT EXPLOIT ATTACK: Someone tried to change the donation amount to get the donation for cheap Charname: ". @$charname ." amount:". @$amount ." donation option:". @$donation_option ."Transaction ID:". @$transid;
							$local_log .= '</td></tr><tr><td>';

							// Write to log.
							$fp=fopen($local_log_file,'a');
							fwrite($fp, $local_log . "\n");

							// Close file
							fclose($fp);
						}
					}
				}
			}
			// Else charname does not exists.
			else
				{
					// Local file reporting.
					// Logging: file location.
					$local_log_file = $log_location_ipn;

					// Logging: Timestamp.
					$local_log = '['.date('m/d/Y g:i A').'] - ';

					// Logging: response from the server.
					$local_log .= "IPN ITEM ENCHANT ERROR: Charname does not exists ? Charname: ". @$charname ." amount:". @$amount ." donation option:". @$donation_option ."Transaction ID:". @$transid;
					$local_log .= '</td></tr><tr><td>';

					// Write to log.
					$fp=fopen($local_log_file,'a');
					fwrite($fp, $local_log . "\n");

					// Close file.
					fclose($fp);
				}
			}
			// Else Someone tried to enter enchant option while disabled.
			else
			{
				// Local file reporting.
				// Logging: file location.
				$local_log_file = $log_location_ipn;

				// Logging: Timestamp.
				$local_log = '['.date('m/d/Y g:i A').'] - ';

				// Logging: response from the server.
				$local_log .= "IPN ITEM ENCHANT ERROR: Someone tried to enter item enchant while disabled in config. Exploit attack ? Charname: ". @$charname ." amount:". @$amount ." donation option:". @$donation_option ."Transaction ID:". @$transid;
				$local_log .= '</td></tr><tr><td>';

				// Write to log.
				$fp=fopen($local_log_file,'a');
				fwrite($fp, $local_log . "\n");

				// Close file.
				fclose($fp);
			}
		}
// End validation
	}
}
?>