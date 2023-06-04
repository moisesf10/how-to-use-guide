<?php

namespace App\Helpers;

enum DatabaseErrorStates: string
{
    case C1062 = '';

    public static function toString(int $code){
        $parsedCode = 'C'. $code;
        if(isset(self::$parsedCode )){
            return self::$parsedCode;
        }

        return false;
    }
}
