<?php

namespace IdeaPublisher\Validators {

    class StringValidator
    {
        public static function isNonEmptyString($value) : bool
        {
            return is_string($value) && strlen(trim($value)) > 0;
        }

        public static function assertNonEmptyString($value, $description = 'This value should not be null or blank.') : void
        {
            assert(self::isNonEmptyString($value) !== null, $description);
        }
    }
}

