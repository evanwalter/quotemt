
<?php
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Methods: PUT');
header('Access-Control-Allow-Methods: DELETE');
header('Access-Control-Allow-Headers: Access-Control-Allow-Origin,Content-Type, Access-Control-Allow-Methods,Authorization, X-Requested-With');

$request_method = $_SERVER["REQUEST_METHOD"];
$id = isset($_GET['id']) ? $_GET['id'] : NULL;
$random = isset($_GET['random']) ? $_GET['random'] : "false";

include_once '../../config/Database.php';
include_once '../../models/Author.php';
include_once '../../models/Validate.php';

// Instantiate Db and connect
$database = new Database();
$db = $database->connect();

// Instantiate blog post object
$author = new Author($db);

$validator = new Validator();

// If GET call return all rows 
if ($request_method=="GET"){
    header('Access-Control-Allow-Origin: *');
    header('Content-Type: application/json');

    // Get query result & row count
    if ($id != NULL){
            $author->id = $id;   
            $author->read_single();

            if ($author->id === "-1"){
                echo json_encode( array("message" => "authorId Not Found"));
            } else {
                $author_arr = array(
                    'id' => $author->id,
                    'author' => $author->author
                );
                // Convert to JSON
                print_r(json_encode($author_arr));        
            }
        } else {
            $author->random = $random;
            $result = $author->read();
            $num = $result->rowCount();
        
            // Get data from result
            if ($num > 0){
                $author_arr = array();
                while( $row = $result->fetch(PDO::FETCH_ASSOC)){
                    extract($row);
                    $author_item = array(
                        'id'=> $id,'author'=> $author
                        );
                    // Push to "data"	
                    array_push($author_arr, $author_item); 
                    }
                    // convert the PHP arry to JSON
                    echo json_encode($author_arr);
                } else {
                    echo json_encode(
                        array('message'=> ' No authors exist')
                    );		
                }
        } 
    }  // End If GET Method

if($request_method=="POST"){
    header('Access-Control-Allow-Methods: POST');
    // Get raw posted data   - decodes FROM JSON format
    $data = json_decode(file_get_contents("php://input"));

    if (isset($data->author)===false){
        echo json_encode( array('message' => 'Missing Required Parameters') );
        return false;
    } 

    $author->author = $data->author;

    //Create Category
    $newid = $author->create();
    if($newid != "-1"){
            echo json_encode( array('id' => $newid, 'author' => $author->author));
        } else {
            echo json_encode( array('message' => 'Unable to create Author'));
        }
    }

if($request_method=="PUT"){
    $data = json_decode(file_get_contents("php://input"));

    // Set ID to update
    $author->id = $data->id;

    $isvalid = $validator->isValid($author);

    if ($isvalid){
            //Update
            $author->author = $data->author;            
            if($author->update()){
                echo json_encode( array('id' => $author->id, 'author' => $author->author) );
            } else {
                echo json_encode( array('message' => 'Author not updated'));
            }
        } else {
            echo json_encode( array('message' => 'AuthorId not found'));
        }
    }

if($request_method=="DELETE"){
    // Get raw posted data   - decodes FROM JSON format
    $data = json_decode(file_get_contents("php://input"));

    if (!isset($data->id))
    {
        echo json_encode( array('message' => 'Missing Required Parameters'));
    } else {
        // Set ID to delete
        $author->id = $data->id;

        //Create post
        if($author->delete()){
                echo json_encode( array('id' => $author->id));
            } else {
                echo json_encode( array('id' => $author->id, 'message' => 'authorId Not Found'));
            }
        }
    }
   

?>