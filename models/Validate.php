<?php


class Validator {
    public function isValid($object){
        $object->read_single();
        if ($object->id==="-1"){
            return false;
        }
        return true;
    }
    
}


?>