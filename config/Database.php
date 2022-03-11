<?php 
	class Database {
			// Connection Parameters
			private $host='localhost:3307';
			private $db_name='quotesdb';
			private $username='root';
			private $password='';
			private $conn;
			
			public function connect() {
				$this->conn = null;
				
				try {
					$this->conn= new PDO('mysql:host=' . $this->host . ';dbname=' . $this->db_name, 
						$this->username, $this->password);
					$this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
				} catch (PDOException $e) {
					echo "Connection error: " . $e->getMessage();
				}
				
				return $this->conn;
			}
		
	}
	
	
	?>