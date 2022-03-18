
<?php
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');
header('Access-Control-Allow-Methods: OPTIONS,GET,POST,PUT,DELETE');
header('Access-Control-Allow-Headers: Access-Control-Allow-Origin,Content-Type, Access-Control-Allow-Methods,Authorization, X-Requested-With');


$request_method = $_SERVER["REQUEST_METHOD"];
$id = isset($_GET['id']) ? $_GET['id'] : NULL;
$author_id = isset($_GET['authorId']) ? $_GET['authorId'] : NULL;
$category_id = isset($_GET['categoryId']) ? $_GET['categoryId'] : NULL;
$random = isset($_GET['random']) ? $_GET['random'] : "false";

include_once '../../config/Database.php';
include_once '../../models/Quote.php';
include_once '../../models/Author.php';
include_once '../../models/Category.php';
include_once '../../models/Validate.php';


// Instantiate Db and connect
$database = new Database();
$db = $database->connect();

// Instantiate blog post object
$quote = new Quote($db);

$validator = new Validator();

// If GET call return all rows 
if ($request_method=="GET"){

    // If an Id was provided, read single
    if ($id != NULL){
            $quote->id = $id;   
            $quote->read_single();

            if ($quote->id === "-1"){
                echo json_encode( array("message" => "No Quotes Found"));
            } else {
                //Create array
                $quote_arr = array(
                    'id' => $quote->id,
                    'quote' => $quote->quote,
                    'author' => $quote->author,
                    'category' => $quote->category
                );
                // Convert to JSON
                print_r(json_encode($quote_arr));        
        }            
    } else if ($author_id != NULL && $category_id == NULL){
        $author = new Author($db);
        $author->id = $author_id;
        $isvalid = $validator->isValid($author);
    
        if($isvalid){
            $quote->author_id = $author_id;   
            $result = $quote->read_by_author();
            $num = $result->rowCount();
        
            // Get data from result
            if ($num > 0){
                $quote_arr = array();
                while( $row = $result->fetch(PDO::FETCH_ASSOC)){
                    extract($row);
                    $quote_item = array(
                        'id'=> $id,'quote'=> $quote,
                        'author'=> $author,'category'=> $category
                        );
                    array_push($quote_arr, $quote_item); 
                    }  // end while
                 echo json_encode($quote_arr);
                } else {
                    echo json_encode(array('message'=> ' No Quotas Found'));		
                }  // eNd if else
              }  else {
                echo json_encode( array('message' => 'authorId Not Found'));
            }
    } else if ($category_id != NULL && $author_id === NULL){
                $category = new Category($db);
                $category->id = $category_id;
                $isvalid = $validator->isValid($category);

                if($isvalid){
                    $quote->category_id = $category_id;   
                    $result = $quote->read_by_category();
                    $num = $result->rowCount();
                
                    // Get data from result
                    if ($num > 0){
                        $quote_arr = array();
                        while( $row = $result->fetch(PDO::FETCH_ASSOC)){
                            extract($row);
                            $quote_item = array(
                                'id'=> $id,'quote'=> $quote,
                                'author'=> $author,'category'=> $category
                                );
                            array_push($quote_arr, $quote_item); 
                            } // end while
                            echo json_encode($quote_arr);
                        } else {
                            echo json_encode(array('message'=> ' No quotas found') );		
                        } // end if else   
                } else {
                echo json_encode( array('message' => 'categoryId Not Found'));
            }       
    } else if ($category_id != NULL && $author_id != NULL){
        $author = new Author($db);
        $author->id = $author_id;
        $isvalid = $validator->isValid($author);

        if($isvalid){
            $category = new Category($db);
            $category->id = $category_id;
            $isvalid = $validator->isValid($category);
        }
    
        if($isvalid){
            $quote->author_id = $author_id;
            $quote->category_id = $category_id;
            $result = $quote->read_by_author_category();
            $num = $result->rowCount();
            // Get data from result
            if ($num > 0){
                $quote_arr = array();
                while( $row = $result->fetch(PDO::FETCH_ASSOC)){
                    extract($row);
                    $quote_item = array(
                        'id'=> $id,'quote'=> $quote,
                        'author'=> $author,'category'=> $category
                        );
                    array_push($quote_arr, $quote_item); 
                    }  // end while
                 echo json_encode($quote_arr);
                } else {
                    echo json_encode(array('message'=> ' No Quotas Found'));		
                }  // eNd if else
        }  else {  // else if not valie
                echo json_encode( array('message' => 'authorId or categoryId Not Found'));
        }        
    // Else if an ID was not provided, return all quotes            
    } else {   
            $result = $quote->read();
            $num = $result->rowCount();
        
            // Get data from result
            if ($num > 0){
                $quote_arr = array();
                while( $row = $result->fetch(PDO::FETCH_ASSOC)){
                    extract($row);
                    $quote_item = array(
                        'id'=> $id,'quote'=> $quote,
                        'author'=> $author,'category'=> $category
                        );
                    // Push to array
                    array_push($quote_arr, $quote_item); 
                    }
                    // convert the PHP arry to JSON
                    echo json_encode($quote_arr);
                } else {
                    echo json_encode(
                        array('message'=> ' No Quotas Found')
                    );		
                }
        } 
    }  // End If GET Method
else if($request_method=="POST"){
    header('Access-Control-Allow-Methods: POST');
    header('Access-Control-Allow-Headers: Access-Control-Allow-Origin,Content-Type, Access-Control-Allow-Methods,Authorization, X-Requested-With');

    // Get raw posted data   - decodes FROM JSON format
    $data = json_decode(file_get_contents("php://input"));

    $quote->quote = isset($data->quote) ? $data->quote : NULL;
    $quote->author_id = isset($data->authorId) ? $data->authorId : NULL;
    $quote->category_id = isset($data->categoryId) ? $data->categoryId : NULL;

    if (isset($data->quote)===false){
        echo json_encode( array('message' => 'Missing Required Parameters') );
        return false;
    }    
    if (isset($data->authorId)===false){
        echo json_encode( array('message' => 'Missing Required Parameters') );
        return false;
    }
    if (isset($data->categoryId)===false){
        echo json_encode( array('message' => 'Missing Required Parameters') );
        return false;
    }

    $author = new Author($db);
    $author->id = $quote->author_id;
    $isvalid = $validator->isValid($author);
    if($isvalid){
        $category = new Category($db);
        $category->id = $quote->category_id;
        $isvalid = $validator->isValid($category);
    } else {
        echo json_encode( array('message' => 'authorId Not Found') );
        return false;
    }

    if ($isvalid){
        //Create Quote
        $newid=$quote->create();
        
        if($newid != "-1"){
                echo json_encode( array('quote' => $quote->quote,
                                    'authorId' => $author->id,
                                    'categoryId' => $category->id,
                                    'id' => $newid));
            } else {
                echo json_encode( array('message' => 'Unable to create the new Quote'));
            }
        } else {
            echo json_encode( array('message' => 'categoryId Not Found') );
        }
 
    }  // END POST

else if($request_method=="PUT"){
    header('Access-Control-Allow-Origin: *');
    header('Content-Type: application/json');
    header('Access-Control-Allow-Methods: PUT');
    header('Access-Control-Allow-Headers: Access-Control-Allow-Origin,Content-Type, Access-Control-Allow-Methods,Authorization, X-Requested-With');
    
    $data = json_decode(file_get_contents("php://input"));

    // Set ID to update
    $quote->id = $data->id;
    $quote->quote = $data->quote;
    $quote->author_id = isset($data->authorId) ? $data->authorId : NULL;
    $quote->category_id = isset($data->categoryId) ? $data->categoryId : NULL;

    if (isset($data->quote)===false){
        echo json_encode( array('message' => 'Missing Required Parameters') );
        return false;
    }    
    if (isset($data->authorId)===false){
        echo json_encode( array('message' => 'Missing Required Parameters') );
        return false;
    }
    if (isset($data->categoryId)===false){
        echo json_encode( array('message' => 'Missing Required Parameters') );
        return false;
    }

    $isvalid = $validator->isValid($quote);
    if (!$isvalid){
        echo json_encode( array('message' => 'No Quotes Found') );
        return false;
    }

    $author = new Author($db);
    $author->id = $quote->author_id;
    $isvalid = $validator->isValid($author);
    if($isvalid){
        $category = new Category($db);
        $category->id = $quote->category_id;
        $isvalid = $validator->isValid($category);
    } else {
        echo json_encode( array('message' => 'authorId Not Found') );
        return false;
    }

    if ($isvalid){
            if($quote->update()){
                        echo json_encode( array('quote' => $quote->quote,
                        'authorId' => $quote->author_id,
                        'categoryId' => $quote->category_id,
                        'id' => $quote->id));
            } else {
                echo json_encode( array('message' => 'Quote has Not updated'));
            }
            } else {
                echo json_encode( array('message' => 'categoryId Not Found') );
                return false;
        }
    }

else if($request_method=="DELETE"){
    header('Access-Control-Allow-Origin: *');
    header('Content-Type: application/json');
    header('Access-Control-Allow-Methods: DELETE');
    header('Access-Control-Allow-Headers: Access-Control-Allow-Origin,Content-Type,Access-Control-Allow-Methods,Authorization,X-Requested-With');
    
    // Get raw posted data   - decodes FROM JSON format
    $data = json_decode(file_get_contents("php://input"));

    if (is_null($data->id))
    {
        echo json_encode( array('message' => 'Missing Required Parameters'));
    } else {
        // Set ID to delete
        $quote->id = $data->id;

        $isvalid = $validator->isValid($quote);

        if ($isvalid){
            if($quote->delete()){
                echo json_encode( array('id' => $quote->id));
            } else {
                echo json_encode( array('id' => $quote->id, 'message' => 'No Quotes Found'));
            }
        } else {
            echo json_encode( array( 'message' => 'No Quotes Found'));
        }

        }
    }
   

?>