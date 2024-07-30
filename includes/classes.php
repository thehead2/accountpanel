<?php
	class template
	{
		function load($filepath)
		{
			$this->template = preg_replace("#\{(.*)\}#","<?php echo $1; ?>",file_get_contents($filepath));
		}

		function replace($var, $content)
		{
			$this->template = str_replace("%$var%",$content,$this->template);
		}

		function content()
		{
			return $this->template;
		}
	}

	class mysql
	{
		protected $hostname;
		protected $database;
		protected $username;
		protected $password;

		protected $_handle = null;

		public function __construct($hostname = null,$database = null,$username = null,$password = null)
		{
			$this->hostname	= $hostname;
			$this->database	= $database;
			$this->username	= $username;
			$this->password	= $password;
		}

		public function __call($method, $args)
		{
			if (!$this->_handle)
				$this->connect();

			return call_user_func_array(array($this, $method),$args);
		}

		function connect()
		{
			global $client;
			
			try
			{
				@$this->_handle = new PDO("mysql:host={$this->hostname};dbname={$this->database};charset=utf8",$this->username,$this->password);		
				$this->_handle->setAttribute(PDO::ATTR_EMULATE_PREPARES,false);
				$this->_handle->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
				
			}
			catch (PDOException $e)
			{
				$this->error($e->getMessage(),$this->hostname);
		
			}
		}

		function error($error,$hostname)
		{
			$file = fopen(PATH_ROOT."errorLog.txt","a");
				
			fwrite($file,date("H:i:s d/m/Y",time()).": ".$error." (".$_SERVER['REMOTE_ADDR'].")\n");
			fclose($file);
		}

		
		
		protected function select3($query,$array)
		{

			if (!$this->_handle) {
				echo 'estoy dentro del return false';
			return false;
			}
			try
			{

				
				
				$statement = $this->_handle->prepare($query);

				
				
				$statement->execute($array);
				$data = $statement->fetchAll();
				
				if (count($data) == 1){

					
					return array($data[0]);}
				return $data;
			}
			catch (PDOException $e)
			{
				
				$this->error($e->getMessage(),$this->hostname);
			
				return false;
			}
		}
		
		
		
		
		
		
		
		
		
		
		protected function select2($query,$array)
		{
			
			if (!$this->_handle) {
				
			return false;
			}
			try
			{
				
				
				$statement = $this->_handle->prepare($query);


				
				
				$statement->execute($array);
				$data = $statement->fetchAll();

				if (count($data) == 1){
					

					
					return array($data[0]);
					
					}
					
					
					
					return $data;
			}
			catch (PDOException $e)
			{
				
				$this->error($e->getMessage(),$this->hostname);
			
				return false;
			}
		}
		
		
		
		
		
		
				protected function select4($query)
		{
			

			try
			{
				
				
				$statement = $this->_handle->prepare($query);
				

				
				
				$statement->execute();
				$data = $statement->fetchAll();
				

						
				if (count($data) == 1){
					

					
					return array($data[0]);
					
					}
			
				
				
			
					
					return $data;
			}
			catch (PDOException $e)
			{
				
				$this->error($e->getMessage(),$this->hostname);
			
				return false;
			}
		}
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		protected function select($query,$array)
		{
			
			if (!$this->_handle) {
				
				echo 'estoy dentro del return false';
			return false;
			}
			try
			{
				
				$statement = $this->_handle->prepare($query);
				
				
			
				
				

				
				
				$statement->execute($array);
				$data = $statement->fetchAll();
				if (count($data) == 1){
					
					
					
					return $data[0];
					}
					
				
				return $data;
			}
			catch (PDOException $e)
			{
				
				$this->error($e->getMessage(),$this->hostname);
			
				return false;
			}
		}
		
		
		
		protected function execute2($query,$array)
		{

			
			
			
			if (!$this->_handle) return false;
			
			try
			{
				$statement = $this->_handle->prepare($query);
				$statement->execute($array);
				return $statement->rowCount();
			}
			catch (PDOException $e)
			{
				if ($e->getCode() != "23000")
					
				$this->error($e->getMessage(),$this->hostname);
				return false;
			}
		}
		
		
		
		
		
		
		

		protected function execute($query,$array)
		{
			if (!$this->_handle) return false;
			
			try
			{
				$statement = $this->_handle->prepare($query);
				$statement->execute($array);
				return $statement->rowCount();
			}
			catch (PDOException $e)
			{
				if ($e->getCode() != "23000")
					
				$this->error($e->getMessage(),$this->hostname);
				return false;
			}
		}
		
		protected function isConnected()
		{
			return $this->_handle != null;
		}
		
		protected function test()
		{
			try
			{
				@$this->_handle = new PDO("mysql:host={$this->hostname};dbname={$this->database}",$this->username,$this->password);		
				$this->_handle->setAttribute(PDO::ATTR_EMULATE_PREPARES,false);
				$this->_handle->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
				return true;
				
			}
			catch (PDOException $e)
			{
				return $e->getMessage();
	
			}
		}
	}

	class log
	{
		function format($log,$arguments)
		{
			return vsprintf($log,$arguments);
		}
		function add($log)
		{
			global $mysqlClientLoginServer;
			global $queryLogin;
			global $player;

			$mysqlClientLoginServer->execute("INSERT INTO `acp_logs` (`serverId`,`account`,`characterId`,`log`,`time`) VALUES (?,?,?,?,?);",array((($player['server']) ? $player['server'] : 0),$player['account'],(($player['character']['id']) ? $player['character']['id'] : 0),$log,time()));
		}
	}

	$log = new log();
	
	class mail
	{
		function send($to,$message)
		{
			global $settings;
			
			$headers = "From: \" { ".$settings['projectName']." } \" < { ".$settings['projectEmail']." } >" . "\n"; 
			$headers .= "MIME-Version: 1.0" . "\n"; 
			$headers .= "Content-type: text/html; charset=iso-8859-1" . "\r\n";
			
			@mail($to,$settings['projectName'],$message,$headers);
		}
	}
	
	class validate
	{
		function account($account)
		{
			return (preg_match("/^[A-Za-z0-9]{1,20}$/", $account));
		}

		function character($id)
		{
			return (preg_match("/^[0-9]{9,10}$/", $id));
		}

		function password($password)
		{
			return (preg_match("/^[A-Za-z0-9]{1,20}$/", $password));
		}

		function numeric($value)
		{
			return (preg_match("/^\d+$/", $value));
		}
		
		function money($value)
		{
			return (preg_match("/^[0-9]+(?:\.[0-9]{0,8})?$/", $value));
		}
		
		function pin($value)
		{
			return (preg_match("/^[a-z0-9 ]{19}$/", $value));
		}

		function email($email)
		{
			return (filter_var($email, FILTER_VALIDATE_EMAIL));
		}

		function blank($value)
		{
			return ($value != null && (is_array($value) || strlen($value) > 0));
		}

		function match($value1,$value2)
		{
			return ($value1 === $value2);
		}

		function gameserver($id)
		{
			return ($this->numeric($id));
		}
		
		function escape($string)
		{
			return (preg_match("/^[A-Za-z0-9\\.\\_]{0,30}$/", $string));
		}
		
		function sessionID($string)
		{
			return (preg_match("/^[a-f0-9]{30}$/", $string));
		}
		
		function letters($string)
		{
			return (preg_match("/^[a-zA-Z]+$/", $string));
		}
	}
	
	$validate = new validate();
	
	class cookie
	{
		function add($name, $value, $duration)
		{
			global $client;

			setCookie("acp[".$client['id']."][".$name."]",$value,$duration,"/");
		}

		function set($name)
		{
			global $client;

			return (isset($_COOKIE['acp'][$client['id']][$name]));
		}

		function get($name)
		{
			global $client;
		
			return @$_COOKIE['acp'][$client['id']][$name];
		}

		function destroy($name)
		{
			global $client;
			
			setCookie("acp[".$client['id']."][".$name."]","",time()-10,"/");
		}
	}

	$cookie = new cookie();

	class session
	{
		function add($name, $value)
		{
			global $client;
			
			$_SESSION['acp'][$client['id']][$name] = $value;
		}

		function set($name)
		{
			global $client;
			
			return (isset($_SESSION['acp'][$client['id']][$name]));
		}

		function get($name)
		{
			global $client;
			
			return @$_SESSION['acp'][$client['id']][$name];
		}

		function destroy($name)
		{
			global $client;
			
			unset($_SESSION['acp'][$client['id']][$name]);
		}
	}

	$session = new session();
	
	class items {
		function findItemXML($item)
		{

			$file = "";
			
			$XMLfiles = opendir(PATH_LIBRARIES."xml/items");
 
			while ($XMLfile = readdir($XMLfiles))
			{
				
				
				if ($XMLfile != "." && $XMLfile != ".." && $XMLfile != "index.html")
				{
					
					$XMLfile = str_replace(".xml", "", $XMLfile);
					
					$range = explode('-', $XMLfile);
					
					if ($item >= $range[0] && $item <= $range[1])
					{
						
						$file = $XMLfile.".xml";
						
						break;
					}
				}   
			}
			
			return $file;
			
		}

		function findItemInXML($item_id)
		{
			
			if($item_id>60000)
				return;
			

			$itemXML = "";
			
			$items = simplexml_load_file(PATH_LIBRARIES."xml/items/".$this->findItemXML($item_id));
			
			foreach ($items as $item)
			{
				
				if ($item_id == $item->attributes()->id)
				{
					
					$itemXML = $item;
					break;
				}
			}
			
			return $itemXML;
		}

		function getType($item)
		{
			return (string)$item->attributes()->type;
		}

		function getName($item)
		{
			return (string)$item->attributes()->name;
		}

		function getIcon($item)
		{
			//echo $item;
			//print_r($item)."\n";
			$item = $item->xpath('set[@name="icon"]');
			//print_r($item);
			return (string)str_replace("icon.","",$item[0]->attributes()[1]);
		}

		
		function getTradeable($item)
		{
			$item1 = $item->xpath('set[@name="tradeable"]');
	
			if (!$item1) return false;

			//echo (bool)($item[0]->attributes()[1] == 0);
			
			return (bool)($item[0]->attributes()[1] == 0);
		}
		
		function getAugmentation($item)
		
		{
						global $mysqlClientGameServer;
			
			
			$argumento = $mysqlClientGameServer->select4("SELECT object_id from item_attributes");

						foreach ($argumento as $registro_argumento){
							
							

							if($registro_argumento['object_id']==$item['object_id']) return true;
							
						

						}
					return false;
			
		}
		
		
		
		
		function getStackable($item)
		{
			$item = $item->xpath('set[@name="stackable"]');
			if (!$item) return false;
			return (bool)$item[0]->attributes()[1];
		}
		
		function getEnchantable($item)
		{
			$item = $item->xpath('set[@name="enchant_enabled"]');
			if (!$item) return false;
			return (bool)($item[0]->attributes()[1] == 1);
		}

		function getGrade($item)
		{
			
			$item = $item->xpath('set[@name="crystal_type"]');

			if (!$item)
			{			
				
				return "none";
			}
		
			return (string)$item[0]->attributes()[1];
		}
		
		function add($itemXML,$item_id,$count,$enchant_level,$enchanted_set)
		{
			global $mysqlClientGameServer;
			global $queryGame;
			global $player;
			global $telnet;
			global $config;
			if (!(!$enchanted_set && $enchant_level == 0 && !!$config['enableTelnet'] && $telnet->give($player['character']['name'],$item_id,$count)))
			{
				/*if ($player['character']['online'])
					exit("RESPONSE^ERROR^Please logout from the game in order to use this function.");*/
				
				if ($this->getStackable($itemXML))
				{
					usleep(50);
					$mysqlClientGameServer->execute("INSERT INTO market_items_buy VALUES (?,?,?,?)",array($player['character']['id'],$item_id,$count,$enchant_level));
					/*$itemData = $mysqlClientGameServer->select3($queryGame['selectPlayerItemEnchantData8'],array($item_id,$player['character']['id']));

					
					if ($itemData)
					{
						$mysqlClientGameServer->execute2($queryGame['updateEnchantOnItem2'],array($itemData[0]['owner_id'],$itemData[0]['item_id'],$itemData[0]['count']+$count,$itemData[0]['enchant_level'],$itemData[0]['loc'],$itemData[0]['loc_data'],$itemData[0]['life_time'],$itemData[0]['custom_type1'],$itemData[0]['custom_type2']));
						$mysqlClientGameServer->execute($queryGame['updateEnchantOnItem'],array($itemData[0]['object_id']));
	
					}
					else{

						$mysqlClientGameServer->execute($queryGame['addItemToPlayerInventory'],array($player['character']['id'],$item_id,$count,$enchant_level));
					}*/
				}
				else
				{
					for($i=1;$i<=$count;$i++)
					{
						usleep(50);
						$mysqlClientGameServer->execute("INSERT INTO market_items_buy VALUES (?,?,?,?)",array($player['character']['id'],$item_id,1,$enchant_level));
						//$mysqlClientGameServer->execute($queryGame['addItemToPlayerInventory'],array($player['character']['id'],$item_id,1,$enchant_level));	
					}
				}


			}
		}
	}

	$items = new items();
	
	class telnet {
		function give($character,$id,$count) {
			$socket = @fsockopen($config['mysqlHostname'], $config['telnetPort'], $errno, $errstr, 30);
			if($socket) {
				fputs($socket, $config['telnetPassword']);
				fputs($socket, "\r\n");
				fputs($socket, "give ".$character." ".$id." ".$count);
				fputs($socket, "\r\n");
				fputs($socket, "exit\r\n");
				
				while (!feof($socket)) {
					$line = fgets($socket, 2000);
					if (strstr($line,"Player not found")) return false;
				}
				
				fclose($socket);
				return true;
			}
			return false;
		}
	}
	
	$telnet = new telnet();
	
	class credits {
		function increase($credits) {
			global $mysqlClientLoginServer;
			global $queryLogin;
			global $player;

			if (!$mysqlClientLoginServer->execute("UPDATE `acp_players` SET `balance` = balance+? WHERE `account` = ?;",array($credits,$player['account'])))
				exit("REFRESH");
			return true;
		}

		function reduce($credits) {
			global $mysqlClientLoginServer;
			global $queryLogin;
			global $player;
			
			if ($credits == 0)
				return true;

			if (!$mysqlClientLoginServer->execute("UPDATE `acp_players` SET `balance` = balance+? WHERE `account` = ?;",array("-".$credits,$player['account'])))
				exit("REFRESH");
			return true;
		}
	}

	$credits = new credits();
?>