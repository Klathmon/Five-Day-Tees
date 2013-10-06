<?php
/**
 * Created by: Gregory Benner.
 * Date: 9/30/13
 */

class Filter
{
    /* Sensitization */
    const SANITIZE_STRING             = 513; //Strips HTML Tags from the string if they are there
    const SANITIZE_EMAIL              = 517; //Strips all characters except alpha-numeric and !#$%&'*+-/=?^_`{|}~@.[]
    const SANITIZE_ENCODED            = 514; //URL-Encodes the string before returning it
    const SANITIZE_FLOAT              = 520; //Strips all non-digits and returns a float
    const SANITIZE_INT                = 519; //Strips all non-digits and returns an int
    const SANITIZE_SPECIAL_CHARS      = 515; //Equivalent to calling htmlspecialchars()
    const SANITIZE_URL                = 518; //Remove all characters except letters, digits and $-_.+!*'(),{}|\\^~[]`<>#%";/?:@&=
    const SANITIZE_RAW                = 516; //Strip/Remove nothing

    /* Validation */
    const VALIDATE_INT     = 257;
    const VALIDATE_BOOLEAN = 258;
    const VALIDATE_FLOAT   = 259;
    const VALIDATE_URL     = 273;
    const VALIDATE_EMAIL   = 274;
    const VALIDATE_IP      = 275;
    const VALIDATE_MAC     = 276;

    public function get($name, $type = self::SANITIZE_STRING)
    {
        return filter_input(INPUT_GET, $name, $type);
    }

    public function post($name, $type = self::SANITIZE_STRING)
    {
        return filter_input(INPUT_POST, $name, $type);
    }

    public function filter($data, $type = self::SANITIZE_STRING)
    {
        return filter_var($data, $type);
    }
}