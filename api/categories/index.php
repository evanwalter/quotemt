
<?php

$request_method = $_SERVER["REQUEST_METHOD"];
$id = isset($_GET['id']) ? $_GET['id'] : NULL;

include_once '../../config/Database.php';
include_once '../../models/Category.php';
include_once '../../models/Validate.php';


// Instantiate Db and connect
$database = new Database();
$db = $database->connect();

// Instantiate blog post object
$category = new Category($db);

$validator = new Validator();

// If GET call return all rows 
if ($request_method=="GET"){
    header('Access-Control-Allow-Origin: *');
    header('Content-Type: application/json');

				
    // Get query result & row count
    if ($id != NULL){
            $category->id = $id;   
            $category->read_single();

            if ($category->id === "-1"){
                echo json_encode( array("message" => "categoryId Not Found"));
            } else {
                //Create array
                $category_arr = array(
                    'id' => $category->id,
                    'category' => $category->category
                );
                
                // Convert to JSON
                print_r(json_encode($category_arr));        
            }
        } else {
            $test = getenv("JAWSDB_URL", false);

            $result = $category->read();
            $num = $result->rowCount();
        
            // Get data from result
            if ($num > 0){
                $cat_arr = array();
                while( $row = $result->fetch(PDO::FETCH_ASSOC)){
                    extract($row);
                    $cat_item = array(
                        'id'=> $id,'category'=> $category . $test
                        );
                    // Push to "data"	
                    array_push($cat_arr, $cat_item); 
                    }
                    // convert the PHP arry to JSON
                    echo json_encode($cat_arr);
                } else {
                    echo json_encode(
                        array('message'=> ' No categories found')
                    );		
                }
        } 
    }  // End If GET Method

if($request_method=="POST"){
    header('Access-Control-Allow-Origin: *');
    header('Content-Type: application/json');
    header('Access-Control-Allow-Methods: POST');
    header('Access-Control-Allow-Headers: Access-Control-Allow-Origin,Content-Type, Access-Control-Allow-Methods,Authorization, X-Requested-With');

    // Get raw posted data   - decodes FROM JSON format
    $data = json_decode(file_get_contents("php://input"));

    $category->category = $data->category;

    //Create Category
    $newid=$category->create();
    
    if($newid != "-1"){
            echo json_encode( array('message' => 'Category created','id' => $newid));
        } else {
            echo json_encode( array('message' => 'Category Not created'));
        }
    }

if($request_method=="PUT"){
    header('Access-Control-Allow-Origin: *');
    header('Content-Type: application/json');
    header('Access-Control-Allow-Methods: PUT');
    header('Access-Control-Allow-Headers: Access-Control-Allow-Origin,Content-Type,Access-Control-Allow-Methods,Authorization, X-Requested-With');

    $data = json_decode(file_get_contents("php://input"));

    // Set ID to update
    $category->id = $data->id;

    $isvalid = $validator->isValid($category);
   
    if ($isvalid){
            //Update
            $category->category = $data->category;
            if($category->update()){
                echo json_encode( array('message' => 'Category updated'));
            } else {
                echo json_encode( array('message' => 'Category Not updated'));
            }
        } else {
            echo json_encode( array('message' => 'CategoryId not found'));
        }

    }

if($request_method=="DELETE"){
    header('Access-Control-Allow-Origin: *');
    header('Content-Type: application/json');
    header('Access-Control-Allow-Methods: DELETE');
    header('Access-Control-Allow-Headers: Access-Control-Allow-Origin,Content-Type,Access-Control-Allow-Methods,Authorization,X-Requested-With');
    
    // Get raw posted data   - decodes FROM JSON format
    $data = json_decode(file_get_contents("php://input"));

    if (!isset($data->id))
    {
        echo json_encode( array('message' => 'A category id is required'));
    } else {
        // Set ID to delete
        $category->id = $data->id;

        //Create post
        if($category->delete()){
                echo json_encode( array('id' => $category->id, 'message' => 'Category has been deleted'));
            } else {
                echo json_encode( array('id' => $category->id, 'message' => 'Unable to delete category'));
            }
        }
    }
   

?>