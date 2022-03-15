<?php 
	class Database {
			// Connection Parameters
			private $conn;
			private $db_name;
			private $username;
			private $password;

			public function __construct(){


				$this->hostname=getenv('HOSTNAME'); 
				$this->db_name=getenv('DB_NAME');
				$this->username=getenv('USERNAME');
				$this->password=getenv('PASSWORD');
				#private $url = getenv('JAWSDB_URL');

				
				#private $hostname = $dbparts['host'];
				#private $username = $dbparts['user'];
				#private $password = $dbparts['pass'];
				#private $db_name = ltrim($dbparts['path'],'/');

			}
			
			public function connect() {
				$this->conn = null;
				
				try {
					$this->conn= new PDO('mysql:host=' . $this->hostname . ';dbname=' . $this->db_name, 
						$this->username, $this->password);
					$this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
				} catch (PDOException $e) {
					echo "Connection error: " . $e->getMessage();
				}
				
				return $this->conn;
			}
		
	}
	
	
	?>