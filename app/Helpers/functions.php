<?php

use \Illuminate\Support\Facades\Auth;

if(! function_exists('toUserTimezone')){
    function toUserTimezone($date){
        if(! Auth::check()){
            return now(new DateTimeZone('America/Sao_Paulo'));
        }
        $user = Auth::user();
        $timeZone = 'America/Sao_Paulo';

        if(is_string($date)){
            $date = DateTime::createFromFormat('Y-m-d H:i:s', $date);
        }

        if(! empty($user->timezone)){
            $timeZone = $user->timezone;
        }

        return $date->setTimezone(new DateTimeZone($timeZone));
    }
}

if(! function_exists('getUserTimezone')){
    function getUserTimezone(){
        if(! Auth::check()){
            return 'America/Sao_Paulo';
        }

        $user = Auth::user();
        $timeZone = 'America/Sao_Paulo';
        if(! empty($user->timezone)){
            $timeZone = $user->timezone;
        }

        return $timeZone;
    }
}

if(! function_exists('toShortName')){
    function toShortName(string $name){
        $aux = explode(' ', $name);
        $shortName = array_shift($aux) . ( count($aux) > 0 ? ' '. array_pop($aux) : ''  );
        return $shortName;
    }
}

if(! function_exists('databaseErrorCodeToMessage')){
    function databaseErrorCodeToMessage($code){
        $message = '';


        return true;
    }
}
