<?php

namespace IdeaPublisher\WordPress {

    use IdeaPublisher\Core\NonEmptyString;

    class WordPressTranslator implements \IdeaPublisher\Core\Translator
    {
        public function __(NonEmptyString $text, NonEmptyString ...$parameters) : NonEmptyString
        {
            return NonEmptyString::__(sprintf(translate($text, 'ideapublisher'), ...$parameters));
        }
    }
}
