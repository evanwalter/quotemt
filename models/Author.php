
<?php 

class Author {
	// DB Conn
	private $conn;

	// Post Properties
	public $id;
    public $author;

	public function __construct($db){
		$this->conn = $db;	
		$this->random = "false";
	}

	public function read(){
		$query = 'SELECT id,author
				  FROM authors';
		if ($this->random=="true"){
			$query = $query . " ORDER BY rand() LIMIT 0,1";
		} else {
			$query = $query . " ORDER BY id";
		}
		
		// Prepare statement
		$stmt = $this->conn->prepare($query);
		// Execute query
		$stmt->execute();
		return $stmt;
	}

	// Get a Single Category
	public function read_single(){
		$query = 'SELECT id,author
				  FROM authors
				  WHERE id= ? LIMIT 1';
		
		// Prepare statement
		$stmt = $this->conn->prepare($query);

		// Bind parameters
		$stmt->bindParam(1, $this->id);

		// Execute query
		$stmt->execute();

		$row = $stmt->fetch(PDO::FETCH_ASSOC);

		if (isset($row['author'])){
			$this->author = $row['author'];
		} else {
			$this->id ="-1";
			$this->author = "Author not found";
		}
	}

	// Create a Category
	public function create() {
		//Create query
		$query = 'INSERT INTO authors(author) VALUES(:author)';

		// Prepare statement
		$stmt = $this->conn->prepare($query);

		// Clean data
		$this->author = htmlspecialchars(strip_tags($this->author));
		// Bind the data
		$stmt->bindParam(':author', $this->author);

		//Execute query
		if ($stmt->execute()){
			$new_id = $this->conn->lastInsertId();
			return $new_id;
		}

		return "-1";
	}

	// Update a Category
	public function update() {
		//Create query
		$query = 'UPDATE authors SET author = :author
		WHERE id = :id';

		// Prepare statement
		$stmt = $this->conn->prepare($query);

		// Clean data
		$this->id = htmlspecialchars(strip_tags($this->id));
		$this->author = htmlspecialchars(strip_tags($this->author));

		// Bind the data (using named params)
		$stmt->bindParam(':id', $this->id);
		$stmt->bindParam(':author', $this->author);

		//Execute query
		if ($stmt->execute()){
			return true;
		}

		// Print error if something goes wrong
		printf("Error: %s.\n", $stmt->error);

		return false;
	}

	public function delete() {
		//Query
		$query = "DELETE FROM authors WHERE id = :id";

		// Pepare statement
		$stmt = $this->conn->prepare($query);

		// Clean data
		$this->id = htmlspecialchars(strip_tags($this->id));

		// Bind the data
		$stmt->bindParam(':id', $this->id);

		//Execute query
		if ($stmt->execute()){
			return true;
		}

		// Print error if something goes wrong
		printf("Error: %s.\n", $stmt->error);

		return false;

	}

}
?>