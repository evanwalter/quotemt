
<?php 

class Category {
	// DB Conn
	private $conn;

	// Post Properties
	public $id;
    public $category;
	public $message;

	public function __construct($db){
		$this->conn = $db;
		$this->random = "false";
		$this->message = "";
	}

	public function read(){

		$query = 'SELECT id,category
				  FROM categories';

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
		$query = 'SELECT id,category
				  FROM categories
				  WHERE id= ? LIMIT 1';
		
		// Prepare statement
		$stmt = $this->conn->prepare($query);

		// Bind parameters
		$stmt->bindParam(1, $this->id);

		// Execute query
		$stmt->execute();

		$row = $stmt->fetch(PDO::FETCH_ASSOC);

		if (isset($row['category'])){
			$this->category = $row['category'];
		} else {
			$this->id ="-1";
			$this->category = "Category not found";
		}
	}

	// Create a Category
	public function create() {
		//Create query
		$query = 'INSERT INTO categories(category)
				  VALUES(:category)';

		// Prepare statement
		$stmt = $this->conn->prepare($query);

		// Clean data
		$this->category = htmlspecialchars(strip_tags($this->category));
		// Bind the data
		$stmt->bindParam(':category', $this->category);

		//Execute query
		if ($stmt->execute()){
			$new_id = $this->conn->lastInsertId();
			return $new_id;
		}

		// Print error if something goes wrong
		//printf("Error creating the category: %s.\n", $stmt->error);

		return "-1";
	}

	// Update a Category
	public function update() {
		//Create query
		$query = 'UPDATE categories SET category = :category
		WHERE id = :id';

		// Prepare statement
		$stmt = $this->conn->prepare($query);

		// Clean data
		$this->id = htmlspecialchars(strip_tags($this->id));
		$this->category = htmlspecialchars(strip_tags($this->category));

		// Bind the data (using named params)
		$stmt->bindParam(':id', $this->id);
		$stmt->bindParam(':category', $this->category);

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
		$query = "DELETE FROM categories WHERE id = :id";

		// Pepare statement
		$stmt = $this->conn->prepare($query);

		// Clean data
		$this->id = htmlspecialchars(strip_tags($this->id));

		// Bind the data
		$stmt->bindParam(':id', $this->id);

		try { 
			$stmt->execute();
			return true;
		} catch(Exception $e){
			$this->message=$e->getMessage();
			return false;
		}
		
		// Print error if something goes wrong
		printf("Error: %s.\n", $stmt->error);

		return false;

	}

}
?>