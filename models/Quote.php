
<?php 

class Quote {
	// DB Conn
	private $conn;

	// Post Properties
	public $id;
	public $category_id;
	public $author_id;
    public $quote;

	public function __construct($db){
		$this->conn = $db;	
	}

	public function read(){
		$query = 'SELECT    q.id,q.quote,
                            a.author,
                            c.category
				  FROM quotes q INNER JOIN authors a on q.authorId=a.id
                            INNER JOIN categories c	ON q.categoryId=c.id 
				  ORDER BY q.id;';
		
		// Prepare statement
		$stmt = $this->conn->prepare($query);
		// Execute query
		$stmt->execute();
		return $stmt;
	}

	// Getting all the quotas of a given author
	public function read_by_author(){
		$query = 'SELECT    q.id,q.quote,
                            a.author,
                            c.category
				  FROM quotes q INNER JOIN authors a on q.authorId=a.id
                            INNER JOIN categories c	ON q.categoryId=c.id 
				  WHERE q.authorId=:author_id
				  ORDER BY RAND() LIMIT 1;';
		
		// Prepare statement
		$stmt = $this->conn->prepare($query);

		$stmt->bindParam(':author_id', $this->author_id);

		// Execute query
		$stmt->execute();
		return $stmt;
	}

	// Get all quotes within a given category
	public function read_by_category(){
		$query = 'SELECT    q.id,q.quote,
                            a.author,
                            c.category
				  FROM quotes q INNER JOIN authors a on q.authorId=a.id
                            INNER JOIN categories c	ON q.categoryId=c.id 
				  WHERE q.authorId=:category_id
				  ORDER BY q.id;';
		
		// Prepare statement
		$stmt = $this->conn->prepare($query);

		$stmt->bindParam(':category_id', $this->category_id);

		// Execute query
		$stmt->execute();
		return $stmt;
	}

	// Get a Single Post
	public function read_single(){
		$query = 'SELECT    q.id,q.quote,
                            a.author,
                            c.category
				  FROM quotes q INNER JOIN authors a on q.authorId=a.id
                            INNER JOIN categories c	ON q.categoryId=c.id  
				  WHERE q.id= ? LIMIT 0,1';
		
		// Prepare statement
		$stmt = $this->conn->prepare($query);

		// Bind parameters
		$stmt->bindParam(1, $this->id);

		// Execute query
		$stmt->execute();

		$row = $stmt->fetch(PDO::FETCH_ASSOC);

		if (isset($row['quote'])){
			$this->quote = $row['quote'];
			$this->author = $row['author'];
			$this->category = $row['category'];
		} else {
			$this->id ="-1";
		}		

	}

	// Create a post
	public function create() {

		//Create query
		$query = 'INSERT INTO quotes(quote,authorId,categoryId)
            VALUES (:quote,:author_id,:category_id)';

		// Prepare statement
		$stmt = $this->conn->prepare($query);

		// Clean data
		$this->quote = htmlspecialchars(strip_tags($this->quote));
		$this->author_id = htmlspecialchars(strip_tags($this->author_id));
		$this->category_id = htmlspecialchars(strip_tags($this->category_id));
		
		// Bind the data (using named params)
		$stmt->bindParam(':quote', $this->quote);
		$stmt->bindParam(':author_id', $this->author_id);
		$stmt->bindParam(':category_id', $this->category_id);

		//Execute query
		if ($stmt->execute()){
			$new_id = $this->conn->lastInsertId();
			return $new_id;
		}
		// Print error if something goes wrong
		//printf("Error: %s.\n", $stmt->error);

		return "-1";
	}

	// Update a post
	public function update() {
		//Create query
		$query = 'UPDATE quotes SET
			quote = :quote
		WHERE
			id = :id';

		// Prepare statement
		$stmt = $this->conn->prepare($query);
		printf("teaet" . $this->category_id);
 
		// Clean data
		$this->id = htmlspecialchars(strip_tags($this->id));
		$this->quote = htmlspecialchars(strip_tags($this->quote));

		// Bind the data (using named params)
		$stmt->bindParam(':id', $this->id);
		$stmt->bindParam(':quote', $this->quote);

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
		$query = "DELETE FROM quotes WHERE id = :id";

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