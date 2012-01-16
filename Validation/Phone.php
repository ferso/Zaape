<?php

class Zaape_Validation_Phone  extends Zend_Validate_Abstract{
    /**
    * Validates phone number.
    *
    * @param mixed $value
    * @return boolean
    */
    public function isValid($value)
    {
        // Strip all non-numeric characters
        $value = (string) $value;
        $value = preg_replace('/\D/', '', $value);
 
        // If not 10 characters or more, it cannot be a phone number.
        if(!isset($value{9}))
        {
            return false;
        }
 
        // If the first character is "1" and is not 11 characters or more,
        // cannot be a phone number.
        if($value{0} === '1' && !isset($value{10}))
        {
            return false;
        }
 
        return true;
    }
}



?>