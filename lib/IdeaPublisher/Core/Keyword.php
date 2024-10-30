<?php

namespace IdeaPublisher\Core {

    use IdeaPublisher\Validators\StringValidator;

    class Keyword extends NonEmptyString
    {
        public static function __(string $value): Keyword
        {
            $value = self::optional($value);

            StringValidator::assertNonEmptyString($value);

            return $value;
        }

        public static function optional(string $value): ?Keyword
        {
            $keywordValue = trim(preg_replace('/[^a-zA-Z0-9_]+/', '_', $value), '_');

            if (StringValidator::isNonEmptyString($keywordValue)) {
                return new Keyword($keywordValue);
            }

            return null;
        }
    }
}
