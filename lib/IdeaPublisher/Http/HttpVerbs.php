<?php

namespace IdeaPublisher\Http {

    // enums only available from PHP 8.1, trying to keep support on on the latest 2 major versions of PHP (7,8 atm)
    abstract class HttpVerbs
    {

 
        const HTTP_GET = 'GET';
        const HTTP_POST = 'POST';

        public static function isValid(string $verb): bool
        {
            switch ($verb) {
            case self::HTTP_GET:
            case self::HTTP_POST:
                return true;

            default:
                return false;
            }
        }
    }
}
