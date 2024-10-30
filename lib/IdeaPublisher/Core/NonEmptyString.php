<?php

namespace IdeaPublisher\Core {

    use IdeaPublisher\Validators\StringValidator;

    class NonEmptyString
    {
        protected function __construct(string $value)
        {
            $this->_value = $value;
        }

        public function value() : string
        {
            return $this->_value;
        }
        
        public function __toString() : string
        {
            return $this->value();
        }

        public function concat(string $value)
        {
            return self::__($this->value() . self::__($value)->value());
        }

        public static function __(string $value) : NonEmptyString
        {
            StringValidator::assertNonEmptyString($value);

            return new NonEmptyString($value);
        }
    }
}

